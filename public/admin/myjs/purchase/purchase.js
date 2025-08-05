"use strict";

$(function () {
    
showAllPurchase();
$('#btnPurchase').show();
$('#btnPurchaseUpdate').hide();
$('#btnPurchase').click(function () {
        emptyError();
        var formData = $("form#purchaseForm").serializeArray();
        console.log(formData);
        token();
        var str_url = "StorePurchase";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                $('.alert-success').html('Purchase Create successfully').fadeIn().delay(4000).fadeOut('slow');
                //$('#purchaseForm')[0].reset();
                cleaner();
                showAllPurchase();
            } else {
                printErrorMsg(data.error);
            }
        });
    });


    function showAllPurchase() {
            $('#ErrorMessages').html("");
            $.ajax({
                type: 'ajax',
                method: 'get',
                url: getPurchaseViewUrl,
                data: {},
                async: false,
                dataType: 'json',
                success: function (result) {               
                    if (result.success)
                    {
                        console.log(result);
                        var html = '';
                        var i;
                        let json = jQuery.parseJSON(result.data);
                        console.log(json);                        
                        for (i = 0; i < json.length; i++) {
                            
                            html += '<tr>' +
                                    '<td>' + (Number(i) + 1) + '</td>' +
                                    '<td>' + json[i].id + '</td>' +
                                    '<td>' + json[i].quantity + '</td>' +
                                    '<td>' + json[i].unit_cost + '</td>' +
                                    '<td>' + json[i].unit_cost + '</td>' +
                                    '<td>' + json[i].subtotal + '</td>' +
                                    '<td>' +
                                    '<a href="javascript:;" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center item-delete" title="Delete" data="' + json[i].id + '"><iconify-icon icon="mingcute:delete-2-line"></iconify-icon></a>' +
                                    '<a href="javascript:;" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center  item-edit" title="Edit" data="' + json[i].id + '"><iconify-icon icon="lucide:edit"></iconify-icon></a>' +
                                    '</td>' +
                                    '</tr>';
                        }
                        $('#showdata').html(html);
                    } else
                    {
                        var html = '<div class="alert alert-danger alert-dismissible fade in" role="alert">'
                                + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>'
                                + '<strong>Error!</strong><br />' + data.messages + '</div>';

                        /* place the message to msg-container */
                        $('#ErrorMessages').html(html);
                    }
                },
                error: function () {
                    alert('Data Problem Please Contact Admin');
                }
            });
        }

        $('#showdata').on('click', '.item-delete', function () {
            $('#ErrorMessages').html("");
            var id = $(this).attr('data');            
            token();
            $('#deleteModal').modal('show');
            //prevent previous handler - unbind()
            $('#btnDelete').unbind().click(function () {
                var str_url = id;
                var str_method = "DELETE";
                var str_data_type = "json";
                var data = null;
                CustomAjax(str_url, str_method, data, str_data_type, function (data) {
                    if (data) {
                        $('#deleteModal').modal('hide');
                        $('.alert-danger:first').html('Record Delete Successfully').fadeIn().delay(4000).fadeOut('slow');
                        showAllPurchase();
                    } else {
                        printErrorMsg(data.error);
                    }
                });
            });
        });



        $(document).on('click', '.item-edit', function (e) {
            e.preventDefault();
            var id = $(this).attr('data');
            $('.error-msg').css('display', 'none');
            token();
            var str_url = id + '/edit';
            var str_method = "GET";
            var str_data_type = "json";
            var data = null;
            CustomAjax(str_url, str_method, data, str_data_type, function (result) {
                if (result.success) {
                    let json = jQuery.parseJSON(result.data);
                    $('.quantity').val(json.quantity);
                    $('.product_id').val(json.product_id);
                    $('.unit_cost').val(json.unit_cost);
                    $('.sell_price').val(json.sell_price);
                    $('.id').val(json.id);
                     $('select[name=supplier_id]').val(json.supplier_id).trigger('change');
                     $('#btnPurchase').hide();
                    $('#btnPurchaseUpdate').show();
                } else {
                    printErrorMsg(result.error || 'Failed to load data');
                }
            });
        });

    
        $('#btnPurchaseUpdate').click(function () {
            emptyError();
            token();
            var formData = $("form#purchaseForm").serializeArray();
            var str_url = "rec_update";
            var str_method = "POST";
            var str_data_type = "json";
            CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
                if (data.success) {
                    showAllPurchase();
                    cleaner();
                    $('#btnPurchase').show();
                    $('#btnPurchaseUpdate').hide();
                    $('.alert-success').html('Purchase updated successfully').fadeIn().delay(4000).fadeOut('slow');
                } else {
                    printErrorMsg(data.error);
                }
            });
        });


        function cleaner() {
            $('.quantity').val('');
            $('.product_id').val('');
            $('.unit_cost').val('');
            $('.sell_price').val('');
            $('.id').val('');               
        }
});
