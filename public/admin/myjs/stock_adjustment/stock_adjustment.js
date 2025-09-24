"use strict";

$(function () {
let searchTimeout;
showAllStockAdjustment();
bindStockAdjustmentEvents();
$('#btnStockAdjustment').show();
$('#btnStockAdjustmentUpdate').hide();
$('#btnStockAdjustment').click(function () {
        emptyError();
        var formData = $("form#stockAdjustmentForm").serializeArray();
        console.log(formData);
        token();
        var str_url = getStockAdjustmentIndexUrl +"/"+"StoreStockAdjustment";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                $('.alert-success').html('Stock Adjustment Create successfully').fadeIn().delay(4000).fadeOut('slow');
                showAllStockAdjustment();
            } else {
                // console.log('error message'+ data.error);
                toastr.error(data.error);
                printErrorMsg(data.error);
            }
        });
    });

    $('#btnFinalSave').click(function () {
        emptyError();
        var formData = $("form#stockAdjustmentForm").serializeArray();
        console.log(formData);
        token();
        var str_url = getStockAdjustmentIndexUrl+"/storeFinalStockAdjustment";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                $('.alert-success').html('Final StockAdjustment Create successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#stockAdjustmentForm')[0].reset(); 
                showAllStockAdjustment();
                setTimeout(function() {
                            window.location.href = getStockAdjustmentIndexUrl;
                    }, 1500);
            } else {
                toastr.error(data.error);
                //printErrorMsg(data.error);
            }
        });
    });

    $('#btnFinalEdit').click(function () {
        emptyError();
        var formData = $("form#stockAdjustmentForm").serializeArray();
        console.log(formData);
        token();
        var str_url = getStockAdjustmentIndexUrl +"/"+"storeFinalStockAdjustmentEdit";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                $('.alert-success').html('Final StockAdjustment Create successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#stockAdjustmentForm')[0].reset();
                showAllStockAdjustment();
                setTimeout(function() {
                            window.location.href = getStockAdjustmentIndexUrl;
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
        var formData = $("form#stockAdjustmentForm").serializeArray();
        $('#btnDelete').unbind().click(function () {
            var str_url = getStockAdjustmentIndexUrl +"/"+"deleteAll";
            var str_method = "post";
            var str_data_type = "json";
            var data = formData;
            CustomAjax(str_url, str_method, data, str_data_type, function (data) {
                if (data) {
                    var message = "Record Delete Successfully";
                    $('#deleteModal').modal('hide');
                    $('.alert-danger:first').html(message).fadeIn().delay(4000).fadeOut('slow');
                    showToastSuccess(message);
                    setTimeout(function() {
                            window.location.href = getStockAdjustmentIndexUrl;
                    }, 1500);
                } else {
                    printErrorMsg(data.error);
                }
            });
        });
    });
                                    

    function showAllStockAdjustment() {
        $('#ErrorMessages').html("");
        var stock_adjustment_id = $('#adjustment_id').val();
        $.ajax({
            type: 'ajax',
            method: 'get',
            url: getStockAdjustmentViewUrl+"/"+stock_adjustment_id,
            data: {},
            async: false,
            dataType: 'json',
            success: function (result) {
                if (result.success) {
                    var html = '';
                    let json = jQuery.parseJSON(result.data);
                    var totalAmount = 0;

                    for (let i = 0; i < json.length; i++) {
                        let additionSelected = (json[i].adjustment_type === "addition") ? "selected" : "";
                        let subtractionSelected = (json[i].adjustment_type === "subtraction") ? "selected" : "";

                        console.log(json[i]);
                        let productImg = (json[i].productImg && json[i].productImg.trim() !== "")
                                ? imageUrl + '/' + json[i].productImg
                                : imageUrl + '/default.png'; // fallback image
                        html += '<tr data-id="' + json[i].id + '">' +
                            '<td>' + (Number(i) + 1) + '</td>' +
                            '<td><img src="' +productImg+ '" width="120px" height="100px" class="product_image img-responsive" alt="' + json[i].productName + '"></td>' +
                            '<td>' + json[i].productName + '</td>' +
                            '<td>' + json[i].stock + '</td>' +
                            // ✅ Quantity with plus/minus
                            '<td>' +
                                '<div class="input-group" style="width:120px;">' +
                                    '<button class="btn btn-sm btn-outline-secondary qty-minus" type="button">-</button>' +
                                    '<input type="text" class="form-control form-control-sm text-center qty-input" value="' + json[i].quantity + '" data-id="' + json[i].id + '">' +
                                    '<button class="btn btn-sm btn-outline-secondary qty-plus" type="button">+</button>' +
                                '</div>' +
                            '</td>' +
                            '<td><div class="mb-3">' +
                               '<select class="form-select adjustment-type" data-id="' + json[i].id + '" required>' +
                                    '<option value="addition" ' + additionSelected + '>Addition</option>' +
                                    '<option value="subtraction" ' + subtractionSelected + '>Subtraction</option>' +
                                '</select>'+
                            '</div></td>' +

                            '<td>' +
                                '<a href="javascript:;" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center item-delete" title="Delete" data="' + json[i].id + '"><iconify-icon icon="mingcute:delete-2-line"></iconify-icon></a>' +
                            '</td>' +
                            '</tr>';

                        totalAmount += Number(json[i].subtotal);
                    }

                    $('#total_items').text(json.length);
                    $('#showdata').html(html);
                    calc();
                } else {
                    var html = '<div class="alert alert-danger alert-dismissible fade in" role="alert">'
                        + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>'
                        + '<strong>Error!</strong><br />' + result.message + '</div>';
                    $('#ErrorMessages').html(html);
                }
            },
            error: function () {
                alert('Data Problem Please Contact Admin');
            }
        });
    }


    function bindStockAdjustmentEvents() {
        // Quantity Plus
        $(document).off('click', '.qty-plus').on('click', '.qty-plus', function () {
            let input = $(this).siblings('.qty-input');
            let qty = parseInt(input.val()) || 0;
            let row = $(this).closest('tr');
            let stock = parseInt(row.find('td').eq(3).text()) || 0;

            //if (qty < stock) {
                qty++;
                input.val(qty).trigger("change");
            // } else {
            //     toastr.warning("⚠️ Quantity cannot exceed available stock!");
            // }
        });

        // Quantity Minus
        $(document).off('click', '.qty-minus').on('click', '.qty-minus', function () {
            let input = $(this).siblings('.qty-input');
            let qty = parseInt(input.val()) || 0;

            if (qty > 1) {
                qty--;
                input.val(qty).trigger("change");
            } else {
                toastr.warning("⚠️ Quantity must be at least 1!");
            }
        });

        // Quantity Change (auto save to backend)
        $(document).off('change', '.qty-input').on('change', '.qty-input', function () {
            let id = $(this).data("id");
            let qty = parseInt($(this).val()) || 0;
            let row = $(this).closest('tr');
            let stock = parseInt(row.find('td').eq(3).text()) || 0;

            // if (qty > stock) {
            //     toastr.error("⚠️ Entered quantity is greater than available stock!");
            //     qty = stock;
            //     $(this).val(stock);
            // }
            if (qty < 1) {
                toastr.error("⚠️ Quantity must be at least 1!");
                qty = 1;
                $(this).val(1);
            }

            token();
            CustomAjax(getStockAdjustmentIndexUrl + "/UpdateStockAdjustmentItem", "POST", [
                { name: "id", value: id },
                { name: "quantity", value: qty }
            ], "json", function (data) {
                if (data.success) {
                    showAllStockAdjustment();
                } else {
                    toastr.error(data.error);
                }
            });
        });

        $(document).off('change', '.adjustment-type').on('change', '.adjustment-type', function () {
            let id = $(this).data("id");
            let adjustmentType = $(this).val();

            token();
            CustomAjax(getStockAdjustmentIndexUrl + "/UpdateStockAdjustmentItem", "POST", [
                { name: "id", value: id },
                { name: "adjustment_type", value: adjustmentType }
            ], "json", function (data) {
                if (data.success) {
                    showAllStockAdjustment();
                } else {
                    toastr.error(data.error);
                }
            });
        });

    }

        $('#product_search').on('input', function() {
            clearTimeout(searchTimeout);
            let searchTerm = $(this).val().trim();
            const isBarcode = [8, 12, 13, 14].includes(searchTerm.length);
            if (isBarcode || searchTerm.length >= 2) {
                searchTimeout = setTimeout(() => {
                    performSearch(searchTerm);
                }, 100); 
            } else {
                $('#searchResults').hide();
            }
        });

        function performSearch(searchTerm) {
            $.ajax({
                url: product_search,
                method: "GET",
                data: { term: searchTerm },
                success: function(response) {
                    let $results = $('#searchResults');
                    $results.empty();
                    
                    if(response.length === 0) {
                            toastr.warning("No item found");
                            $results.hide();
                    } else if (response.length === 1 && response[0].barcode === searchTerm) {
                        let product = response[0];
                        getAverageCostAndSalePrice(product.id, function(prices) {
                            autoSaveTemp(product.id, prices);
                        });
                        $('#searchResults').hide();
                        $('.product_search').val('').focus();
            
                    } else{
                        response.forEach(function(product) {
                            let productImg = (product.product_image && product.product_image.trim() !== "")
                                        ? imageUrl + '/' + product.product_image
                                        : imageUrl + '/default.png'; // fallback image
                            $results.append(`
                                <a href="#" class="list-group-item list-group-item-action product-result" 
                                data-id="${product.id}" 
                                data-code="${product.barcode}"
                                data-product='${product.id}'>
                                    <div class="d-flex w-100 justify-content-between">
                                        <p class="mb-1"><img src="${productImg}" class="img-fluid" width="40px" height="25px" style="width:40px; height:25px;"> ${product.barcode}-${product.name}</p>
                                        <small></small>
                                    </div>
                                </a>
                            `);
                        });
                        $results.show();
                    } 
                },
                error: function() {
                    $('#searchResults').hide();
                    toastr.error("Failed to search products");
                }
            });
        }

        // ===============================
        // Product Click & Auto Save Flow
        // ===============================
        $(document).on('click', '.product-result', function(e) {
            e.preventDefault();
            alert('ddd');
            let product = JSON.parse($(this).attr('data-product'));
            console.log(product);
            // Fetch cost/sale price, then auto save
            getAverageCostAndSalePrice(product, function(prices) {
                autoSaveTemp(product, prices);
            });

            $('#searchResults').hide();
            $('#product_search').val('');
            $('#quantity').focus();
        });


        // ===============================
        // Get Average Cost & Sale Price
        // (With Callback Support)
        // ===============================
        function getAverageCostAndSalePrice(product_id, callback = null) {
            console.log("Fetching cost/sale price for product_id:", product_id);
            token();

            var str_url = baseUrl+'/admin/getAverageCostAndSalePrice/'+product_id;
            var str_method = "GET";
            var str_data_type = "json";
            var data = null;

            CustomAjax(str_url, str_method, data, str_data_type, function (result) {
                if (result.success) {
                    // Optional: update hidden form fields (agar UI me chahiye to)
                    $('.unit_cost').val(result.average_unit_cost);
                    $('.sell_price').val(result.last_sale_price);
                    $("#product_id").val(product_id);
                    $("#product_name").val(result.name);

                    // Callback fire karein
                    if (typeof callback === "function") {
                        callback({
                            cost_price: result.average_unit_cost,
                            sell_price: result.last_sale_price,
                            name: result.name
                        });
                    }
                } else {
                    printErrorMsg(result.error || 'Failed to load data');
                }
            });
        }


        // ===============================
        // Auto Save to Temp Table
        // ===============================
        function autoSaveTemp(product_id, prices) {
            var date = $('#date').val();
            var stock_adjustment_id = $('#adjustment_id').val();
            var formData = [
                { name: "adjustment_id", value: stock_adjustment_id },
                { name: "product_id", value: product_id },
                { name: "product_name", value: prices.name },
                { name: "unit_cost", value: prices.cost_price },
                { name: "adjustment_type", value: "addition" },
                { name: "quantity", value: 0 }, // default qty 0
                { name: "date", value : date}
            ];
            token();
            var str_url = getStockAdjustmentIndexUrl + "/StoreStockAdjustment";
            var str_method = "POST";
            var str_data_type = "json";

            CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
                if (data.success) {
                    $('.alert-success')
                        .html('Product added successfully')
                        .fadeIn().delay(2000).fadeOut('slow');

                    // Refresh table
                    showAllStockAdjustment();
                } else {
                    toastr.error(data.error);
                    printErrorMsg(data.error);
                }
            });
        }

        

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
                var str_url = getStockAdjustmentIndexUrl+"/"+id;
                var str_method = "DELETE";
                var str_data_type = "json";
                var data = null;
                CustomAjax(str_url, str_method, data, str_data_type, function (data) {
                    if (data) {
                        $('#deleteModal').modal('hide');
                        $('.alert-danger:first').html('Record Delete Successfully').fadeIn().delay(4000).fadeOut('slow');
                        showAllStockAdjustment();
                    } else {
                        printErrorMsg(data.error);
                    }
                });
            });
        });

    
        $('#btnStockAdjustmentUpdate').click(function () {
            emptyError();
            token();
            var formData = $("form#stockAdjustmentForm").serializeArray();
            var str_url = getStockAdjustmentIndexUrl+"/rec_update";
            var str_method = "post";
            var str_data_type = "json";
            CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
                if (data.success) {
                    showAllStockAdjustment();
                    $('#btnStockAdjustment').show();
                    $('#btnStockAdjustmentUpdate').hide();
                    $('.alert-success').html('Stock Adjustment updated successfully').fadeIn().delay(4000).fadeOut('slow');
                } else {
                    printErrorMsg(data.error);
                }
            });
        });
});
