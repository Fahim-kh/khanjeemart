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
                // console.log('error message'+ data.error);
                toastr.error(data.error);
                printErrorMsg(data.error);
            }
        });
    });

    $('#btnFinalSave').click(function () {
        emptyError();
        var formData = $("form#purchaseForm").serializeArray();
        console.log(formData);
        token();
        var str_url = "storeFinalPurchase";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                $('.alert-success').html('Final Purchase Create successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#purchaseForm')[0].reset();
                $('select[name=supplier_id]').val('').trigger('change');
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
                        var totalAmount = 0   
                        var totalItems = 0; 
                        for (i = 0; i < json.length; i++) {
                            html += '<tr>' +
                                    '<td>' + (Number(i) + 1) + '</td>' +
                                    '<td>' + json[i].productName + '</td>' +
                                    '<td>' + json[i].quantity + '</td>' +
                                    '<td>' + json[i].unit_cost + '</td>' +
                                    '<td>' + json[i].sale_price + '</td>' +
                                    '<td>' + json[i].subtotal + '</td>' +
                                    '<td>' +
                                    '<a href="javascript:;" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center item-delete" title="Delete" data="' + json[i].id + '"><iconify-icon icon="mingcute:delete-2-line"></iconify-icon></a>' +
                                    '<a href="javascript:;" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center  item-edit" title="Edit" data="' + json[i].id + '"><iconify-icon icon="lucide:edit"></iconify-icon></a>' +
                                    '</td>' +
                                    '</tr>';
                                   totalAmount += Number(json[i].subtotal);
                        }
                        $('#total_items').text(json.length);
                        $('#subTotal').text(totalAmount);
                        
                        calc();
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

        // // Handle product selection
    $(document).on('click', '.product-result', function(e) {
        e.preventDefault();
        let product = JSON.parse($(this).attr('data-product'));
        console.log(product);
        getAverageCostAndSalePrice(product.id);
        //$('#product_search').val('').focus();
        $('#searchResults').hide();
        //addProductToTable(product);
    });

        function getAverageCostAndSalePrice(product_id) {
            token();
            var str_url = '/admin/getAverageCostAndSalePrice/'+product_id;
            var str_method = "GET";
            var str_data_type = "json";
            var data = null;
            CustomAjax(str_url, str_method, data, str_data_type, function (result) {
                if (result.success) {
                    //let json = jQuery.parseJSON(result);
                    $('.unit_cost').val(result.average_unit_cost);
                    $('.sell_price').val(result.last_sale_price);
                } else {
                    printErrorMsg(result.error || 'Failed to load data');
                }
            });
        };

        function calc()
        {
            var orderTax = Number($('#order_tax').val());

            var discount = Number($('#discount').val());
            var shipping = Number($('#shipping').val());
            var totalSubTotal = Number($('#subTotal').text());
            // var grandTotal = (totalSubTotal+shipping+orderTax)-discount;
            var orderTaxAmount = (totalSubTotal * orderTax) / 100; // tax in %
            var grandTotal = (totalSubTotal + shipping + orderTaxAmount) - discount;
            $('#grand_total').text(grandTotal.toFixed(2));
            //alert(grandTotal);
            $('#order_tax_total').text(orderTax);
            $('#discount_total').text(discount);
            $('#shipping_total').text(shipping);
            $('#grand_total').text(grandTotal);
        }

        $('#order_tax').on('keyup', function() {
            //console.log('Key pressed, value: ' + $(this).val());
            calc();
        });
        $('#discount').on('keyup', function() {
            //console.log('Key pressed, value: ' + $(this).val());
            calc();
        });
        $('#shipping').on('keyup', function() {
            //console.log('Key pressed, value: ' + $(this).val());
            calc();
        });

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
                    $('.product_name').val(json.product_name);
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
            $('.product_name').val('');
            $('.unit_cost').val('');
            $('.sell_price').val('');
            $('.id').val('');               
        }
});
