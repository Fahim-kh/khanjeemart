"use strict";

$(function () {
    
showAllSale();
$('#btnSale').show();
$('#btnSaleUpdate').hide();
$('#btnSale').click(function () {
        emptyError();
        var formData = $("form#saleForm").serializeArray();
        console.log(formData);
        token();
        var str_url = getSaleIndexUrl +"/"+"StoreSale";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                $('.alert-success').html('Sale Create successfully').fadeIn().delay(4000).fadeOut('slow');
                //$('#purchaseForm')[0].reset();
                cleaner();
                showAllSale();
            } else {
                // console.log('error message'+ data.error);
                toastr.error(data.error);
                printErrorMsg(data.error);
            }
        });
    });

    $('#btnFinalSave').click(function () {
        emptyError();
        var formData = $("form#saleForm").serializeArray();
        console.log(formData);
        token();
        var str_url = "storeFinalSale";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                $('.alert-success').html('Final Sale Create successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#saleForm')[0].reset();
                $('select[name=customer_id]').val('').trigger('change');
                $('#order_tax').val(''); 
                $('#discount').val(''); 
                $('#shipping').val(''); 
                cleaner();
                showAllSale();
            } else {
                toastr.error(data.error);
                //printErrorMsg(data.error);
            }
        });
    });

     $('#btnFinalEdit').click(function () {
        emptyError();
        var formData = $("form#saleForm").serializeArray();
        console.log(formData);
        token();
        var str_url = getSaleIndexUrl +"/"+"storeFinalSaleEdit";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                $('.alert-success').html('Final Sale Create successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#saleForm')[0].reset();
                $('select[name=customer_id]').val('').trigger('change');
                $('#order_tax').val(''); 
                $('#discount').val(''); 
                $('#shipping').val(''); 
                cleaner();
                showAllSale();
                setTimeout(function() {
                            window.location.href = getSaleIndexUrl;
                    }, 1500);
            } else {
                toastr.error(data.error);
                //printErrorMsg(data.error);
            }
        });
    });

    $('#btnReset').click(function () {
        token();
        $('#deleteModal').modal('show');
        //prevent previous handler - unbind()
        $('#btnDelete').unbind().click(function () {
            var str_url = getSaleIndexUrl +"/"+"deleteAll";
            var str_method = "post";
            var str_data_type = "json";
            var data = null;
            CustomAjax(str_url, str_method, data, str_data_type, function (data) {
                if (data) {
                    var message = "Record Delete Successfully";
                    $('#deleteModal').modal('hide');
                    $('.alert-danger:first').html(message).fadeIn().delay(4000).fadeOut('slow');
                    showToastSuccess(message);
                    setTimeout(function() {
                            window.location.href = getSaleIndexUrl;
                    }, 1500);
                } else {
                    printErrorMsg(data.error);
                }
            });
        });
    });
                                    

    


    function showAllSale() {
            $('#ErrorMessages').html("");

            $.ajax({
                type: 'ajax',
                method: 'get',
                url: getSaleViewUrl,
                data: {},
                async: false,
                dataType: 'json',
                success: function (result) {               
                    if (result.success)
                    {
                        var html = '';
                        var i;
                        let json = jQuery.parseJSON(result.data);
                        console.log('data'+json);                    
                        var totalAmount = 0   
                        var totalItems = 0; 
                        for (i = 0; i < json.length; i++) {
                            // console.log(json[i]);
                            html += '<tr>' +
                                    '<td>' + (Number(i) + 1) + '</td>' +
                                    '<td> <img src="'+imageUrl+'/'+json[i].productImg+'" width="120px" class="product_image img-responsive" alt="'+ json[i].productName +'" ></td>' +
                                    '<td>' + json[i].productName + '</td>' +
                                    '<td>' + json[i].quantity + '</td>' +
                                    '<td>' + json[i].cost_unit_price + '</td>' +
                                    '<td>' + json[i].selling_unit_price + '</td>' +
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
        getAverageCostAndSalePrice(product);
        //$('#product_search').val('').focus();
        $('#searchResults').hide();
        //addProductToTable(product);
    });

        function getAverageCostAndSalePrice(product_id) {
            console.log(product_id);
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
                    $("#product_id").val(product_id);
                    $("#product_name").val(result.name);
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
                var str_url = getSaleIndexUrl+"/"+id;
                var str_method = "DELETE";
                var str_data_type = "json";
                var data = null;
                CustomAjax(str_url, str_method, data, str_data_type, function (data) {
                    if (data) {
                        $('#deleteModal').modal('hide');
                        $('.alert-danger:first').html('Record Delete Successfully').fadeIn().delay(4000).fadeOut('slow');
                        showAllSale();
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
            var str_url = getSaleIndexUrl+"/"+id + '/edit';
            var str_method = "GET";
            var str_data_type = "json";
            var data = null;
            CustomAjax(str_url, str_method, data, str_data_type, function (result) {
                if (result.success) {
                    let json = jQuery.parseJSON(result.data);
                    $('.quantity').val(json.quantity);
                    $('.product_id').val(json.product_id);
                    $('.unit_cost').val(json.cost_unit_price);
                    $('.sell_price').val(json.selling_unit_price);
                    $('.product_name').val(json.product_name);
                    $('.id').val(json.id);
                     $('#btnSale').hide();
                    $('#btnSaleUpdate').show();
                } else {
                    printErrorMsg(result.error || 'Failed to load data');
                }
            });
        });

    
        $('#btnSaleUpdate').click(function () {
            emptyError();
            token();
            var formData = $("form#saleForm").serializeArray();
            var str_url = getSaleIndexUrl+"/rec_update";
            var str_method = "post";
            var str_data_type = "json";
            CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
                if (data.success) {
                    showAllSale();
                    cleaner();
                    $('#btnSale').show();
                    $('#btnSaleUpdate').hide();
                    $('.alert-success').html('Sale updated successfully').fadeIn().delay(4000).fadeOut('slow');
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
