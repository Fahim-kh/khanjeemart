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
        var str_url = getPurchaseIndexUrl + "/" + "StorePurchase";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                $('.alert-success').html('Purchase Create successfully').fadeIn().delay(4000).fadeOut('slow');
                //$('#purchaseForm')[0].reset();
                $('.product_search').val('').focus();
                $('#product_name').prop('disabled', true).hide();
                $('#stock').prop('disabled', true).hide();
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
                $('#order_tax').val('');
                $('#discount').val('');
                $('#shipping').val('');
                cleaner();
                showAllPurchase();
                setTimeout(function () {
                    window.location.href = getPurchaseIndexUrl;
                }, 1500);
            } else {
                toastr.error(data.error);
                //printErrorMsg(data.error);
            }
        });
    });

    $('#btnFinalEdit').click(function () {
        emptyError();
        var formData = $("form#purchaseForm").serializeArray();
        console.log(formData);
        token();
        var str_url = getPurchaseIndexUrl + "/" + "storeFinalPurchaseEdit";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                $('.alert-success').html('Final Purchase Create successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#purchaseForm')[0].reset();
                $('select[name=supplier_id]').val('').trigger('change');
                $('#order_tax').val('');
                $('#discount').val('');
                $('#shipping').val('');
                cleaner();
                showAllPurchase();
                setTimeout(function () {
                    window.location.href = getPurchaseIndexUrl;
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
        var formData = $("form#purchaseForm").serializeArray();
        $('#btnDelete').unbind().click(function () {
            var str_url = getPurchaseIndexUrl + "/" + "deleteAll";
            var str_method = "post";
            var str_data_type = "json";
            var data = formData;
            CustomAjax(str_url, str_method, data, str_data_type, function (data) {
                if (data) {
                    var message = "Record Delete Successfully";
                    $('#deleteModal').modal('hide');
                    $('.alert-danger:first').html(message).fadeIn().delay(4000).fadeOut('slow');
                    showToastSuccess(message);
                    setTimeout(function () {
                        window.location.href = getPurchaseIndexUrl;
                    }, 1500);
                } else {
                    printErrorMsg(data.error);
                }
            });
        });
    });


    function showAllPurchase() {
        $('#ErrorMessages').html("");
        var purchase_id = $('#purchase_id').val();
        $.ajax({
            type: 'ajax',
            method: 'get',
            url: getPurchaseViewUrl + "/" + purchase_id,
            data: {},
            async: false,
            dataType: 'json',
            success: function (result) {
                if (result.success) {
                    var html = '';
                    var i;
                    let json = jQuery.parseJSON(result.data);
                    console.log('data' + json);
                    var totalAmount = 0
                    var totalItems = 0;
                    for (i = 0; i < json.length; i++) {
                        // console.log(json[i]);
                        html += '<tr>' +
                            '<td>' + (Number(i) + 1) + '</td>' +
                            '<td> <img src="' + imageUrl + '/' + json[i].productImg + '" width="120px" class="product_image img-responsive" alt="' + json[i].productName + '" style="width: 120px;height: 80px;"></td>' +
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

                } else {
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


    function showAllPurchase() {
        $('#ErrorMessages').html("");
        var purchase_id = $('#purchase_id').val();
        $.ajax({
            type: 'ajax',
            method: 'get',
            url: getPurchaseViewUrl + "/" + purchase_id,
            data: {},
            async: false,
            dataType: 'json',
            success: function (result) {
                if (result.success) {
                    var html = '';
                    var i;
                    let json = jQuery.parseJSON(result.data);
                    console.log('data' + json);
                    var totalAmount = 0
                    var totalItems = 0;

                    for (i = 0; i < json.length; i++) {
                        let productImg = (json[i].productImg && json[i].productImg.trim() !== null)
                            ? imageUrl + '/' + json[i].productImg
                            : imageUrl + '/default.png'; // fallback image
                        html += '<tr>' +
                            '<td>' + (Number(i) + 1) + '</td>' +
                            '<td> <img src="' + productImg + '" width="120px" height="120px" class="product_image img-responsive" alt="' + json[i].productName + '" " style="width: 120px;height: 80px;"></td>' +
                            '<td>' + json[i].productName + '<br> <span class="badge bg-success">' + json[i].bar_code + '</span></td>' +
                            '<td>' + json[i].quantity + '</td>' +
                            '<td>' + json[i].unit_cost + '</td>' +
                            '<td>' + json[i].sale_price + '</td>' +
                            '<td>' + json[i].subtotal + '</td>' +
                            '<td>' +
                            '<a href="javascript:;" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center item-delete" title="Delete" data="' + json[i].id + '"><iconify-icon icon="mingcute:delete-2-line"></iconify-icon></a>' +
                            '<a href="javascript:;" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center  item-edit" title="Edit" data="' + json[i].id + '"><iconify-icon icon="lucide:edit"></iconify-icon></a>' +
                            '<a href="javascript:;" class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center item-view" title="View Report" data-id="' + json[i].product_id + '">' +
                            '<iconify-icon icon="mdi:eye-outline"></iconify-icon>' +
                            '</a>' +
                            '</td>' +

                            '</tr>';
                        totalAmount += Number(json[i].subtotal);
                    }
                    $('#total_items').text(json.length);
                    $('#subTotal').text(totalAmount);

                    calc();
                    $('#showdata').html(html);

                } else {
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
    let searchTimeout;

    $('#product_search').on('input', function () {
        clearTimeout(searchTimeout);
        let searchTerm = $(this).val().trim();

        // If barcode is a standard length (EAN-8, UPC-A, EAN-13, etc.)
        const isBarcode = [8, 12, 13, 14].includes(searchTerm.length);

        if (isBarcode || searchTerm.length >= 2) {
            searchTimeout = setTimeout(() => {
                // $('#product_name').prop('disabled', false).show();
                // $('#stock').prop('disabled', false).show();
                performSearch(searchTerm);

            }, 100); // Small delay to allow fast scanner input
        } else {
            $('#searchResults').hide();
        }
    });

    function performSearch(searchTerm) {
        $.ajax({
            url: product_search,
            method: "GET",
            data: { term: searchTerm },
            success: function (response) {
                let $results = $('#searchResults');
                $results.empty();
                if (response.length === 0) {
                    // no products
                    toastr.warning("No item found");
                    $results.hide();

                } else if (response.length === 1 && response[0].barcode === searchTerm) {
                    // ✅ exact match → directly add to table
                    let product = response[0];
                    getAverageCostAndSalePrice(product.id);
                    $('#searchResults').hide();
                    $('.product_search').val('').focus();

                } else {
                    
                    // multiple matches OR single but not exact → show list
                    response.forEach(function (product) {
                        // ✅ check image
                        let productImg = (product.product_image && product.product_image.trim() !== "")
                            ? imageUrl + '/' + product.product_image
                            : imageUrl + '/default.png'; // fallback image

                        $results.append(`
                                <a href="#" class="list-group-item list-group-item-action product-result" 
                                   data-id="${product.id}" 
                                   data-code="${product.barcode}" 
                                   data-product='${JSON.stringify(product)}'>
                                    <div class="d-flex w-100 justify-content-between">
                                        <p class="mb-1">
                                            <img src="${productImg}" class="img-fluid" width="40px" height="25px" style="width:40px; height:25px;"> 
                                            ${product.barcode} - ${product.name}
                                        </p>
                                    </div>
                                </a>
                            `);
                    });
                    $results.show();
                }
            },
            error: function () {
                $('#searchResults').hide();
                toastr.error("Failed to search products");
            }
        });
    }
    // // Handle product selection
    // $(document).on('click', '.product-result', function(e) {
    //     e.preventDefault();
    //     let product = JSON.parse($(this).attr('data-product'));
    //     console.log(product);
    //     getAverageCostAndSalePrice(product);
    //     //$('#product_search').val('').focus();
    //     $('#searchResults').hide();
    //     //addProductToTable(product);
    // });
    $(document).on('click', '.product-result', function (e) {
        e.preventDefault();
        let product = JSON.parse($(this).attr('data-product')); // full product object

        getAverageCostAndSalePrice(product.id);
        $('#searchResults').hide();
        $('#product_name').prop('disabled', false).show();
        $('#stock').prop('disabled', false).show();
    });
    function getAverageCostAndSalePrice(product_id) {
        console.log(product_id);
        token();
        var str_url = baseUrl + '/admin/getAverageCostAndSalePrice/' + product_id;
        var str_method = "GET";
        var str_data_type = "json";
        var data = null;
        CustomAjax(str_url, str_method, data, str_data_type, function (result) {
            if (result.success) {
                //let json = jQuery.parseJSON(result);
                $('.unit_cost').val(result.average_unit_cost);
                $('.sell_price').val(result.last_sale_price);
                $("#product_id").val(product_id);
                $("#product_name").text('Product: ' + result.bar_code + '-' + result.name);
                $("#stock").text('stock Quantity: ' + result.stock);
                $('#quantity').focus();
                $('#product_search').val('');
                $('.result-info').removeClass('d-none');

            } else {
                printErrorMsg(result.error || 'Failed to load data');
            }
        });
    };

    function calc() {
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

    $('#order_tax').on('keyup', function () {
        //console.log('Key pressed, value: ' + $(this).val());
        calc();
    });
    $('#discount').on('keyup', function () {
        //console.log('Key pressed, value: ' + $(this).val());
        calc();
    });
    $('#shipping').on('keyup', function () {
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
            var str_url = getPurchaseIndexUrl + "/" + id;
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
        var str_url = getPurchaseIndexUrl + "/" + id + '/edit';
        var str_method = "GET";
        var str_data_type = "json";
        var data = null;
        CustomAjax(str_url, str_method, data, str_data_type, function (result) {
            if (result.success) {
                let json = jQuery.parseJSON(result.data);
                $('.quantity').val(json.quantity);
                $('.product_id').val(json.product_id);
                $('.unit_cost').val(json.unit_cost);
                $('.sell_price').val(json.sale_price);
                $('.product_name').val(json.product_name);
                $('.id').val(json.id);
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
        var str_url = getPurchaseIndexUrl + "/rec_update";
        var str_method = "post";
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
    $('#showdata').on('click', '.item-view', function () {
        let productId = $(this).data('id');

        // Ab function call yahan se karo
        showProductReport(productId);
    });
    function showProductReport(productId) {
        $("#purchaseData").html("");
        $("#saleData").html("");
        $.get(`${getPurchaseIndexUrl}/getLastPurchases/${productId}`, function (res) {
            if (res.success) {
                let rows = "";
                res.data.forEach(item => {
                    rows += `<tr>
                            <td>${item.purchase_id}</td>
                            <td>${item.product_name}</td>
                            <td>${item.quantity}</td>
                            <td>${item.unit_cost}</td>
                            <td>${item.purchase_date}</td>
                        </tr>`;
                });
                $("#purchaseData").html(rows);
            } else {
                $("#purchaseData").html("<tr><td colspan='4'>No purchase records found</td></tr>");
            }
        });

        var customerId = $('select[name=customer_id]').val();
        let url = `${getSaleIndexUrl}/lastSale/${productId}`;
        if (customerId) {
            url += `/${customerId}`;
        }

        $.get(url, function (res) {
            if (res.success) {
                let rows = "";
                res.data.forEach(item => {
                    rows += `<tr>
                            <td>${item.sale_id}</td>
                            <td>${item.product_name}</td>
                            <td>${item.quantity}</td>
                            <td>${item.sale_price}</td>
                            <td>${item.customer_name ?? '-'}</td>
                            <td>${item.sale_date}</td>
                        </tr>`;
                });
                $("#saleData").html(rows);
            } else {
                $("#saleData").html("<tr><td colspan='5'>No sale records found</td></tr>");
            }
        });

        // Show Modal
        $("#productReportModal").modal("show");
    }
});
