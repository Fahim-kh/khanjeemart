"use strict";

$(function () {
    let searchTimeout;
    window.loadCustomers();
    showAllSale();
    bindSaleEvents();
    $('#btnSale').show();
    $('#btnSaleUpdate').hide();
    // $('#btnSale').click(function () {
    //         emptyError();
    //         var formData = $("form#saleForm").serializeArray();
    //         console.log(formData);
    //         token();
    //         var str_url = getSaleIndexUrl +"/"+"StoreSale";
    //         var str_method = "POST";
    //         var str_data_type = "json";
    //         CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
    //             if (data.success) {
    //                 $('.alert-success').html('Sale Create successfully').fadeIn().delay(4000).fadeOut('slow');
    //                 //$('#purchaseForm')[0].reset();
    //                 cleaner();
    //                 showAllSale();
    //             } else {
    //                 // console.log('error message'+ data.error);
    //                 toastr.error(data.error);
    //                 printErrorMsg(data.error);
    //             }
    //         });
    //     });

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
                setTimeout(function () {
                    window.location.href = getSaleIndexUrl;
                }, 1500);
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
        var str_url = getSaleIndexUrl + "/" + "storeFinalSaleEdit";
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
                setTimeout(function () {
                    window.location.href = getSaleIndexUrl;
                }, 1500);
            } else {
                toastr.error(data.error);
                //printErrorMsg(data.error);
            }
        });
    });

    $('#btnReset').click(function (e) {
        e.preventDefault();
        token();
        $('#deleteModal').modal('show');
        //prevent previous handler - unbind()
        var formData = $("form#saleForm").serializeArray();
        $('#btnDelete').unbind().click(function () {
            var str_url = getSaleIndexUrl + "/" + "deleteAll";
            var str_method = "post";
            var str_data_type = "json";
            CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
                if (data) {
                    var message = "Record Delete Successfully";
                    $('#deleteModal').modal('hide');
                    $('.alert-danger:first').html(message).fadeIn().delay(4000).fadeOut('slow');
                    showToastSuccess(message);
                    setTimeout(function () {
                        window.location.href = getSaleIndexUrl;
                    }, 1500);
                } else {
                    printErrorMsg(data.error);
                }
            });
        });
    });


    // function showAllSale() {
    //     $('#ErrorMessages').html("");
    //     var sale_id = $('#sale_id').val();
    //     $.ajax({
    //         type: 'ajax',
    //         method: 'get',
    //         url: getSaleViewUrl+"/"+sale_id,
    //         data: {},
    //         async: false,
    //         dataType: 'json',
    //         success: function (result) {
    //             if (result.success) {
    //                 var html = '';
    //                 let json = jQuery.parseJSON(result.data);
    //                 var totalAmount = 0;

    //                 for (let i = 0; i < json.length; i++) {
    //                     console.log(json[i]);
    //                     if(json[0].customer_id != null)
    //                     {
    //                         window.loadCustomers(json[0].customer_id);
    //                         $('#customer_id_hidden').val(json[0].customer_id);
    //                         $('#customer_id').prop('disabled', true).trigger('change.select2');
    //                     }
    //                     let productImg = (json[i].productImg && json[i].productImg.trim() !== "")
    //                             ? imageUrl + '/' + json[i].productImg
    //                             : imageUrl + '/default.png'; // fallback image
    //                     html += '<tr data-id="' + json[i].id + '">' +
    //                         '<td>' + (Number(i) + 1) + '</td>' +
    //                         '<td><img src="' +productImg+ '" width="120px" height="100px" class="product_image img-responsive" alt="' + json[i].productName + '"></td>' +
    //                         '<td>' + json[i].productName + '</td>' +
    //                         '<td>' + json[i].stock + '</td>' +
    //                         // ✅ Quantity with plus/minus
    //                         '<td>' +
    //                             '<div class="input-group" style="width:120px;">' +
    //                                 '<button class="btn btn-sm btn-outline-secondary qty-minus" type="button">-</button>' +
    //                                 '<input type="text" class="form-control form-control-sm text-center qty-input" value="' + json[i].quantity + '" data-id="' + json[i].id + '" >' +
    //                                 '<button class="btn btn-sm btn-outline-secondary qty-plus" type="button">+</button>' +
    //                             '</div>' +
    //                         '</td>' +

    //                         // ✅ Editable Sale Price
    //                         '<td><input type="text" class="form-control form-control-sm sell-price-input" value="' + json[i].selling_unit_price + '" data-id="' + json[i].id + '"></td>' +

    //                         '<td>' + json[i].subtotal + '</td>' +
    //                         '<td>' +
    //                             '<a href="javascript:;" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center item-delete" title="Delete" data="' + json[i].id + '"><iconify-icon icon="mingcute:delete-2-line"></iconify-icon></a>' +
    //                         '</td>' +
    //                         // HTML banate waqt
    //                         '<td>' +
    //                             '<a href="javascript:;" class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center item-view" title="View Report" data-id="' + json[i].product_id + '">' +
    //                                 '<iconify-icon icon="mdi:eye-outline"></iconify-icon>' +
    //                             '</a>' +
    //                         '</td>'+
    //                         '</tr>';

    //                     totalAmount += Number(json[i].subtotal);
    //                 }

    //                 $('#total_items').text(json.length);
    //                 $('#subTotal').text(totalAmount);

    //                 $('#showdata').html(html);
    //                 calc();
    //             } else {
    //                 var html = '<div class="alert alert-danger alert-dismissible fade in" role="alert">'
    //                     + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>'
    //                     + '<strong>Error!</strong><br />' + result.message + '</div>';
    //                 $('#ErrorMessages').html(html);
    //             }
    //         },
    //         error: function () {
    //             alert('Data Problem Please Contact Admin');
    //         }
    //     });


    // }

    function showAllSale() {
        $('#ErrorMessages').html("");
        var sale_id = $('#sale_id').val();

        // store currently focused field
        let $active = $(':focus');
        let activeId = $active.data('id');
        let activeClass = null;
        if ($active.hasClass('qty-input')) activeClass = 'qty-input';
        if ($active.hasClass('sell-price-input')) activeClass = 'sell-price-input';

        $.ajax({
            type: 'get',
            url: getSaleViewUrl + "/" + sale_id,
            dataType: 'json',
            success: function (result) {
                if (result.success) {
                    var html = '';
                    let json = jQuery.parseJSON(result.data);
                    var totalAmount = 0;

                    for (let i = 0; i < json.length; i++) {
                        if (json[0].customer_id != null) {
                            window.loadCustomers(json[0].customer_id);
                            $('#customer_id_hidden').val(json[0].customer_id);
                            $('#customer_id').prop('disabled', true).trigger('change.select2');
                        }
                        let productImg = (json[i].productImg && json[i].productImg.trim() !== "")
                            ? imageUrl + '/' + json[i].productImg
                            : imageUrl + '/default.png';
                        let barcode = json[i].productbarcode;
                        let barcodeLast4 = barcode ? barcode.slice(-4) : "";
                        html += '<tr data-id="' + json[i].id + '">' +
                            '<td>' + (Number(i) + 1) + '</td>' +
                            '<td><img src="' + productImg + '" width="120px" height="100px" class="product_image img-responsive" alt="' + json[i].productName + '"></td>' +
                            '<td>' + json[i].productName + ' - '+ barcodeLast4 + '</td>' +
                            '<td>' + json[i].stock + '</td>' +
                            '<td>' +
                            '<div class="input-group" style="width:120px;">' +
                            '<button class="btn btn-sm btn-outline-secondary qty-minus" type="button">-</button>' +
                            '<input type="text" class="form-control form-control-sm text-center qty-input" value="' + json[i].quantity + '" data-id="' + json[i].id + '" min="1">' +
                            '<button class="btn btn-sm btn-outline-secondary qty-plus" type="button">+</button>' +
                            '</div>' +
                            '</td>' +
                            '<td><input type="text" class="form-control form-control-sm sell-price-input" value="' + json[i].selling_unit_price + '" data-id="' + json[i].id + '"></td>' +
                            '<td>' + json[i].subtotal + '</td>' +
                            '<td><a href="javascript:;" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center item-delete" title="Delete" data="' + json[i].id + '"><iconify-icon icon="mingcute:delete-2-line"></iconify-icon></a></td>' +
                            '<td><a href="javascript:;" class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center item-view" title="View Report" data-id="' + json[i].product_id + '">' + '<iconify-icon icon="mdi:eye-outline"></iconify-icon>' + '</a></td>' +
                            '</tr>';

                        totalAmount += Number(json[i].subtotal);
                    }

                    $('#total_items').text(json.length);
                    $('#subTotal').text(totalAmount);
                    $('#showdata').html(html);

                    calc();

                    // restore focus after rebuild
                    if (activeId && activeClass) {
                        let $input = $('.' + activeClass + '[data-id="' + activeId + '"]');
                        if ($input.length) {
                            setTimeout(() => {
                                $input.focus().select();
                            }, 50);
                        }
                    }
                } else {
                    var html = '<div class="alert alert-danger">Error: ' + result.message + '</div>';
                    $('#ErrorMessages').html(html);
                }
            },
            error: function () {
                alert('Data Problem Please Contact Admin');
            }
        });
    }

    /* ------------------------------
       TAB NAVIGATION (attach once)
    ------------------------------ */

    // Qty → Sale (same row)
    $(document).on('keydown', '.qty-input', function (e) {
        if (e.which === 9 && !e.shiftKey) {
            e.preventDefault();
            let saleInput = $(this).closest('tr').find('.sell-price-input');
            setTimeout(() => saleInput.focus().select(), 0);
        }
    });

    // Sale → Qty (next row)
    $(document).on('keydown', '.sell-price-input', function (e) {
        if (e.which === 9 && !e.shiftKey) {
            e.preventDefault();
            let nextRowQty = $(this).closest('tr').next('tr').find('.qty-input');
            if (nextRowQty.length) {
                setTimeout(() => nextRowQty.focus().select(), 0);
            }
        }
    });

    // Shift+Tab Sale → Qty (same row)
    $(document).on('keydown', '.sell-price-input', function (e) {
        if (e.which === 9 && e.shiftKey) {
            e.preventDefault();
            let qtyInput = $(this).closest('tr').find('.qty-input');
            setTimeout(() => qtyInput.focus().select(), 0);
        }
    });

    // Shift+Tab Qty → Sale (previous row)
    $(document).on('keydown', '.qty-input', function (e) {
        if (e.which === 9 && e.shiftKey) {
            e.preventDefault();
            let prevRowSale = $(this).closest('tr').prev('tr').find('.sell-price-input');
            if (prevRowSale.length) {
                setTimeout(() => prevRowSale.focus().select(), 0);
            }
        }
    });



    function bindSaleEvents() {
        // Quantity Plus
        $(document).off('click', '.qty-plus').on('click', '.qty-plus', function () {
            let input = $(this).siblings('.qty-input');
            let qty = parseInt(input.val()) || 0;
            let row = $(this).closest('tr');
            let stock = parseInt(row.find('td').eq(3).text()) || 0;

            if (qty < stock) {
                qty++;
                input.val(qty).trigger("change");
            } else {
                toastr.warning("⚠️ Quantity cannot exceed available stock!");
            }
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

            if (qty > stock) {
                toastr.error("⚠️ Entered quantity is greater than available stock!");
                qty = stock;
                $(this).val(stock);
            }
            if (qty < 1) {
                toastr.error("⚠️ Quantity must be at least 1!");
                qty = 1;
                $(this).val(1);
            }

            token();
            CustomAjax(getSaleIndexUrl + "/UpdateSaleItem", "POST", [
                { name: "id", value: id },
                { name: "quantity", value: qty }
            ], "json", function (data) {
                if (data.success) {
                    showAllSale();
                } else {
                    toastr.error(data.error);
                }
            });
        });

        // Sale Price Change (auto save to backend)
        $(document).off('change', '.sell-price-input').on('change', '.sell-price-input', function () {
            let id = $(this).data("id");
            let price = parseFloat($(this).val()) || 0;

            if (price <= 0) {
                toastr.error("⚠️ Price must be greater than 0!");
                $(this).val(1);
                price = 1;
            }

            token();
            CustomAjax(getSaleIndexUrl + "/UpdateSaleItem", "POST", [
                { name: "id", value: id },
                { name: "selling_unit_price", value: price }
            ], "json", function (data) {
                if (data.success) {
                    showAllSale();
                } else {
                    toastr.error(data.error);
                }
            });
        });
    }

    $('#product_search').on('input', function () {
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
            success: function (response) {
                let $results = $('#searchResults');
                $results.empty();

                if (response.length === 0) {
                    toastr.warning("No item found");
                    $results.hide();
                } else if (response.length === 1 && response[0].barcode === searchTerm) {
                    let product = response[0];
                    getAverageCostAndSalePrice(product.id, function (prices) {
                        autoSaveTemp(product.id, prices);
                    });
                    $('#searchResults').hide();
                    $('.product_search').val('').focus();
                    // setTimeout(() => {
                    //     $('.qty-input').first().focus().select();
                    // }, 100);
                } else {
                    // response.forEach(function (product) {
                    //     let productImg = (product.product_image && product.product_image.trim() !== "")
                    //         ? imageUrl + '/' + product.product_image
                    //         : imageUrl + '/default.png'; // fallback image
                    //     $results.append(`
                    //             <a href="#" class="list-group-item list-group-item-action product-result" 
                    //             data-id="${product.id}" 
                    //             data-code="${product.barcode}"
                    //             data-product='${product.id}'>
                    //                 <div class="d-flex w-100 justify-content-between">
                    //                     <p class="mb-1"><img src="${productImg}" class="img-fluid" width="40px" height="25px" style="width:40px; height:25px;"> ${product.barcode}-${product.name}</p>
                    //                     <small></small>
                    //                 </div>
                    //             </a>
                    //         `);
                    // });
                    response.forEach(function (product) {
                        let productImg = (product.product_image && product.product_image.trim() !== "")
                            ? imageUrl + '/' + product.product_image
                            : imageUrl + '/default.png'; // fallback image
                        
                        // Get last 4 digits of barcode
                        let barcodeLast4 = product.barcode ? product.barcode.slice(-4) : "";
                    
                        $results.append(`
                            <a href="#" class="list-group-item list-group-item-action product-result" 
                                data-id="${product.id}" 
                                data-code="${product.barcode}"
                                data-product='${product.id}'>
                                <div class="d-flex w-100 justify-content-between">
                                    <p class="mb-1">
                                        <img src="${productImg}" class="img-fluid" width="40px" height="25px" style="width:40px; height:25px;">
                                        ${product.name} - ${barcodeLast4} 
                                    </p>
                                    <small></small>
                                </div>
                            </a>
                        `);
                    });
                    $results.show();
                    // $('.qty-input').focus();
                }
            },
            error: function () {
                $('#searchResults').hide();
                toastr.error("Failed to search products");
            }
        });
    }

    // ===============================
    // Product Click & Auto Save Flow
    // ===============================
    $(document).on('click', '.product-result', function (e) {
        e.preventDefault();
        let product = JSON.parse($(this).attr('data-product'));
        // console.log(product);
        // Fetch cost/sale price, then auto save
        getAverageCostAndSalePrice(product, function (prices) {
            autoSaveTemp(product, prices);
        });

        $('#searchResults').hide();
        $('#product_search').val('');
        $('#quantity').focus();
        $('.qty-input').first().focus();
    });


    // ===============================
    // Get Average Cost & Sale Price
    // (With Callback Support)
    // ===============================
    // function getAverageCostAndSalePrice(product_id, callback = null) {
    //     console.log("Fetching cost/sale price for product_id:", product_id);
    //     token();

    //     var str_url = '/admin/getAverageCostAndSalePrice/' + product_id;
    //     var str_method = "GET";
    //     var str_data_type = "json";
    //     var data = null;

    //     CustomAjax(str_url, str_method, data, str_data_type, function (result) {
    //         if (result.success) {
    //             // Optional: update hidden form fields (agar UI me chahiye to)
    //             $('.unit_cost').val(result.average_unit_cost);
    //             $('.sell_price').val(result.last_sale_price);
    //             $("#product_id").val(product_id);
    //             $("#product_name").val(result.name);

    //             // Callback fire karein
    //             if (typeof callback === "function") {
    //                 callback({
    //                     cost_price: result.average_unit_cost,
    //                     sell_price: result.last_sale_price,
    //                     name: result.name
    //                 });
    //             }
    //         } else {
    //             printErrorMsg(result.error || 'Failed to load data');
    //         }
    //     });
    // }
    function getAverageCostAndSalePrice(product_id, callback = null) {
        console.log("Fetching cost/sale price for product_id:", product_id);
        token();

        var str_url = baseUrl + '/admin/getAverageCostAndSalePrice/' + product_id;
        var str_method = "GET";
        var str_data_type = "json";
        var data = null;

        CustomAjax(str_url, str_method, data, str_data_type, function (result) {
            if (result.success) {
                // Optional: update hidden form fields
                $('.unit_cost').val(result.average_unit_cost);

                // 👇 apply owner logic
                let sellPrice = result.last_sale_price;
                if (window.isOwner == 1) {
                    sellPrice = result.average_unit_cost; // force sale = cost
                }

                $('.sell_price').val(sellPrice);
                $("#product_id").val(product_id);
                $("#product_name").val(result.name);

                // Fire callback
                if (typeof callback === "function") {
                    callback({
                        cost_price: result.average_unit_cost,
                        sell_price: sellPrice,   // 👈 adjusted sale price
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
        var sale_id = $('#sale_id').val();
        var customer_id = $('#customer_id').val();
        var formData = [
            { name: "sale_id", value: sale_id },
            { name: "product_id", value: product_id },
            { name: "product_name", value: prices.name },
            { name: "unit_cost", value: prices.cost_price },
            { name: "sell_price", value: prices.sell_price },
            { name: "quantity", value: 0 }, // default qty 0
            { name: "date", value: date },
            { name: "customer_id", value: customer_id }
        ];
        token();
        var str_url = getSaleIndexUrl + "/StoreSale";
        var str_method = "POST";
        var str_data_type = "json";

        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                $('.alert-success')
                    .html('Product added successfully')
                    .fadeIn().delay(2000).fadeOut('slow');

                // Refresh table
                showAllSale();
                setTimeout(() => {
                    // if your table is DESC, use .first()
                    $('.qty-input').first().focus().select();

                    // if table is ASC, use .last()
                    // $('.qty-input').last().focus().select();
                }, 300);
            } else {
                toastr.error(data.error);
                printErrorMsg(data.error);
            }
        });
    }



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
            var str_url = getSaleIndexUrl + "/" + id;
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
        var str_url = getSaleIndexUrl + "/" + id + '/edit';
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
        var str_url = getSaleIndexUrl + "/rec_update";
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
