"use strict";
document.addEventListener("DOMContentLoaded", function () {
  const toggleBtn = document.getElementById("fullscreen-toggle");
  if (!toggleBtn) {
    console.warn("#fullscreen-toggle not found in DOM.");
    return;
  }

  const icon = toggleBtn.querySelector("i");
  // helper to set icon safely
  function setIcon(isFullscreen) {
    if (!icon) return;
    // use classes explicitly to avoid leftover classes
    icon.classList.remove("fa-expand", "fa-compress-arrows-alt", "fa-compress");
    if (isFullscreen) {
      // If compress-arrows-alt doesn't exist in your FA pack, use 'fa-compress' instead
      icon.classList.add("fa-compress-arrows-alt");
    } else {
      icon.classList.add("fa-expand");
    }
  }

  // Choose target element for fullscreen. To fullscreen only the POS container:
  // const targetElement = document.getElementById('pos-container') || document.documentElement;
  const targetElement = document.documentElement; // full page

  toggleBtn.addEventListener("click", async function () {
    try {
      if (!document.fullscreenElement) {
        if (targetElement.requestFullscreen) {
          await targetElement.requestFullscreen();
        } else if (targetElement.webkitRequestFullscreen) { // Safari
          await targetElement.webkitRequestFullscreen();
        } else {
          console.warn("Fullscreen API is not supported by this browser.");
          return;
        }
        setIcon(true);
      } else {
        if (document.exitFullscreen) {
          await document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
          await document.webkitExitFullscreen();
        }
        setIcon(false);
      }
    } catch (err) {
      console.error("Failed to toggle fullscreen:", err);
    }
  });

  // keep icon in sync if user presses ESC or otherwise exits fullscreen
  document.addEventListener("fullscreenchange", function () {
    setIcon(!!document.fullscreenElement);
  });

  // also cover webkit prefixed event
  document.addEventListener("webkitfullscreenchange", function () {
    setIcon(!!document.fullscreenElement);
  });
});
$(function () {
  let searchTimeout;
  showAllSale();
  bindSaleEvents();
  pdraft_summery();

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
      data: {
        term: searchTerm
      },
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
          response.forEach(function (product) {
            let productImg = (product.product_image && product.product_image.trim() !== "") ?
              imageUrl + '/' + product.product_image :
              imageUrl + '/default.png'; // fallback image
            $results.append(`
                        <a href="#" class="list-group-item list-group-item-action product-result" 
                        data-id="${product.id}" 
                        data-code="${product.barcode}"
                        data-product='${product.id}'>
                            <div class="d-flex w-100 justify-content-between">
                                <p class="mb-1"><img src="${productImg}" class="img-fluid" width="40px" height="25px" style="width:40px; height:25px;"> ${product.barcode} - ${product.name}</p>
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
    // Fetch cost/sale price, then auto save
    getAverageCostAndSalePrice(product, function (prices) {
      autoSaveTemp(product, prices);
    });

    $('#searchResults').hide();
    $('#product_search').val('');
    // $('#quantity').focus();
    $('.sell-price-input').first().focus();
  });




  // ===============================
  // Auto Save to Temp Table
  // ===============================
  function autoSaveTemp(product_id, prices) {
    var date = $('#date').val();
    var sale_id = $('#sale_id').val();
    var customer_id = $('#customer_id').val();
    var formData = [{
      name: "sale_id",
      value: sale_id
    },
    {
      name: "product_id",
      value: product_id
    },
    {
      name: "product_name",
      value: prices.name
    },
    {
      name: "unit_cost",
      value: prices.cost_price
    },
    {
      name: "sell_price",
      value: prices.sell_price
    },
    {
      name: "quantity",
      value: 0
    }, // default qty 0
    {
      name: "date",
      value: date
    },
    {
      name: "customer_id",
      value: customer_id
    }
    ];
    token();
    var str_url = posStoreSale;
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
          $('.sell-price-input').first().focus().select();

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
    extraAmount = Number($('#extra_amount').val());

    var discount = Number($('#discount').val());
    var shipping = Number($('#shipping').val());
    var totalSubTotal = Number($('.pos_total').val());
    // var grandTotal = (totalSubTotal+shipping+orderTax)-discount;
    var orderTaxAmount = (totalSubTotal * orderTax) / 100; // tax in %
    var grandTotal = (totalSubTotal + shipping + orderTaxAmount + extraAmount) - discount;
    $('#grand_total').text(grandTotal.toFixed(2));
    $('.modalbtnFinalSave').attr('data-payable', grandTotal.toFixed(2));
    //alert(grandTotal);
    // $('#order_tax_total').text(orderTax);
    // $('#discount_total').text(discount);
    // $('#shipping_total').text(shipping);
    // $('#grand_total').text(grandTotal);
  }

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
            sell_price: sellPrice, // 👈 adjusted sale price
            name: result.name
          });
        }
      } else {
        printErrorMsg(result.error || 'Failed to load data');
      }
    });
  }

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
      url: pos_getSaleView + "/" + sale_id,
      dataType: 'json',
      success: function (result) {
        if (result.success) {
          var html = '';
          let json = jQuery.parseJSON(result.data);
          var totalAmount = 0;

          if (json.length > 0) {
            if (json[0].customer_id != null) {
              // console.log(json[0].customer_id);
              window.loadCustomers(json[0].customer_id);
              $('#customer_id_hidden').val(json[0].customer_id);
              $('#customer_id').prop('disabled', false).trigger('change.select2');
            }
            for (let i = 0; i < json.length; i++) {
              const barcode = json[i].productBarcode ?
                json[i].productBarcode.slice(-4) :
                '';
              html += '<tr data-id="' + json[i].id + '">' +
                `<td class="text-start">${barcode} - ${json[i].productName}
                              <a href="javascript:;" style="text-decoration:none;" class="w-32-px h-32-px bg-info-focus text-info-main btn-info d-inline-flex align-items-center justify-content-center item-view" title="View Report" data-id="${json[i].product_id}"><i class="fas fa-eye"></i></a>
                          </td>
                          <td class="text-center">
                              <input type="number" 
                                  name="sale_price" 
                                  class="form-control sell-price-input no-spinner" 
                                  value="${parseInt(json[i].selling_unit_price)}" 
                                  min="0" 
                                  step="any" 
                                  data-id="${json[i].id}" 
                                  oninput="this.value = this.value.replace(/[^0-9.]/g, '')">
                          </td>
                          <td style="text-align: center;">${json[i].stock}</td>
                          <td class="text-center">
                              <input type="number" 
                                  value="${json[i].quantity}" 
                                  min="1"
                                  class="form-control form-control-sm text-center qty-input"
                                  style="width: 70px; margin: auto;"
                                  data-id="${json[i].id}" 
                                  >
                          </td>
                          <td class="text-center">${json[i].subtotal}</td>
                          <td>
                              <a href="javascript:;" style="text-decoration:none;" class="w-32-px h-32-px bg-danger-focus text-danger-main d-inline-flex align-items-center justify-content-center item-delete btn btn-danger" title="Delete" data="${json[i].id}"><i class="fas fa-trash"></i></a>
                          </td>
                      </tr>`;

              totalAmount += Number(json[i].subtotal);
            }
          } else {
            // ✅ Show "no items" row
            html = `<tr>
                      <td colspan="6" class="text-center text-muted">
                          No items are added yet!
                      </td>
                  </tr>`;
          }

          $('#total_items').text(json.length);
          $('.pos_total').val(totalAmount);
          $('#showdata').html(html);

          calc();

          if (json.length > 0) {
            // focus on first sale price field
            let $lastSale = $('.sell-price-input').first();
            if ($lastSale.length) {
              // setTimeout(() => {
              //   $lastSale.focus().select();
              // }, 50);
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

  // Sale →  Qty (same row)
  $(document).on('keydown', '.sell-price-input', function (e) {
    if (e.which === 9 && !e.shiftKey) {
      e.preventDefault();
      let saleInput = $(this).closest('tr').find('.qty-input');
      setTimeout(() => saleInput.focus().select(), 0);
    }
  });

  //  Qty  →  Sale(next row)
  $(document).on('keydown', '.qty-input', function (e) {
    if (e.which === 9 && !e.shiftKey) {
      e.preventDefault();
      let nextRowQty = $(this).closest('tr').next('tr').find('.sell-price-input');
      if (nextRowQty.length) {
        setTimeout(() => nextRowQty.focus().select(), 0);
      }
    }
  });

  // Shift+Tab Qty → Sale  (same row)
  $(document).on('keydown', '.qty-input', function (e) {
    if (e.which === 9 && e.shiftKey) {
      e.preventDefault();
      let qtyInput = $(this).closest('tr').find('.sell-price-input');
      setTimeout(() => qtyInput.focus().select(), 0);
    }
  });

  // Shift+Tab  Sale → Qty  (previous row)
  $(document).on('keydown', '.sell-price-input', function (e) {
    if (e.which === 9 && e.shiftKey) {
      e.preventDefault();
      let prevRowSale = $(this).closest('tr').prev('tr').find('.qty-input');
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

   
    $(document).off('change', '.qty-input').on('change', '.qty-input', function () {
      let id = $(this).data("id");
      let qty = parseInt($(this).val()) || 0;
      let row = $(this).closest('tr');
      let stock = parseInt(row.find('td').eq(2).text()) || 0;
      
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

      // get unit price from row input
      let unitPrice = parseFloat(row.find('.sell-price-input').val()) || 0;

      // recalc total price
      let totalPrice = unitPrice * qty;

      token();
      CustomAjax(posUpdateSaleItem, "POST", [
        { name: "id", value: id },
        { name: "quantity", value: qty },
        { name: "selling_unit_price", value: unitPrice },
        { name: "total_price", value: totalPrice }
      ], "json", function (data) {
        if (data.success) {
          showAllSale();
        } else {
          toastr.error(data.error);
        }
      });
    });

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
          // console.log(item);
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

  $('#btnReset').click(function () {
    token();
    clearSaleSession();
    $('#deleteModal').modal('show');
    //prevent previous handler - unbind()
    var formData = $("form#posForm").serializeArray();
    $('#btnDelete').unbind().click(function () {
      var str_url = getSaleIndexUrl + "/" + "posDeleteAll";
      var str_method = "post";
      var str_data_type = "json";
      CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
        if (data) {
          var message = "Record Rest Successfully";
          $('#deleteModal').modal('hide');
          $('.alert-danger:first').html(message).fadeIn().delay(4000).fadeOut('slow');
          showToastSuccess(message);
          setTimeout(function () {
            window.location.reload();
          }, 1500);
        } else {
          printErrorMsg(data.error);
        }
      });
    });
  });
  $('#order_tax').on('keyup', function () {
    //console.log('Key pressed, value: ' + $(this).val());
    calc();
  });
  $('#extra_amount').on('keyup', function () {
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
      var str_url = 'pos_destroy' + "/" + id;
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
  let payable = 0;
  let balance = 0;
  let payingAmount = 0;
  let extraAmount = 0;
  let changeReturn = 0;
  $(document).on("click", "#modalbtnFinalSave", function () {
    payable = parseFloat($(this).data('payable')) || 0;

    // store payable on modal (optional)
    $("#paymentModal").data("payable", payable);
    // set initial UI values
    $('.payAbleGrandtotal').text(payable.toFixed(2));
    $('.payingTotal').text(payable.toFixed(2));
    $('.balanceTotal').text("0.00");
    $('.changeReturn').text("0.00");
    $('.payingAmount').val(payable.toFixed(2));

    // show modal
    $("#paymentModal").modal("show");
  });

  // input change -> recalc
  $(document).on("input", ".payingAmount", updatePaymentSummary);

  // quick-amount buttons -> set value and recalc
  $(document).on("click", ".quick-amounts button", function () {
    let val = parseFloat($(this).data("amount")) || 0;
    $('.payingAmount').val(val.toFixed(2));
    updatePaymentSummary();
  });

  $('.btnFinalSave').click(function () {
    emptyError();
    // alert('payable: '+payable+'== balance:'+balance+'== paying:'+payingAmount+'== change Return:'+changeReturn);
    // payable    
    // balance
    // payingAmount
    // changeReturn      
    var formData = $("form#posForm").serializeArray();
    token();
    var str_url = storeFinalSale;
    var str_method = "POST";
    var str_data_type = "json";
    CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
      // console.log('final save Sale data '+ data.invoice_number);
      if (data.success) {
        $('.alert-success').html('Final Sale Create successfully').fadeIn().delay(4000).fadeOut('slow');
        $('select[name=customer_id]').val('').trigger('change');
        $('#order_tax').val('');
        $('#discount').val('');
        $('#shipping').val('');
        $('#grand_total').text('');
        showAllSale();
        // setTimeout(function () {
        //     window.location.reload();
        // }, 1500);
        var sale_id = data.sale_id;

        $.ajax({
          url: sale_print.replace(':id', sale_id),
          type: "GET",
          success: function (res) {
            let grandTotal = parseFloat(res.summary.grand_total) || 0;
            let finalTotal = grandTotal + extraAmount;
            // Summary fill
            $(".customerName").text(res.summary.customer_name);
            $(".order_tax").text(res.summary.tax);
            $(".discount").text(res.summary.discount);
            $(".shipping").text(res.summary.shipping_charge);
            $(".grand_total").text(res.summary.grand_total);
            $(".paid").text(res.summary.paid_amount);
            $(".due").text(res.summary.due_amount);
            $(".amount_paid").text(res.summary.paid_amount);
            $(".return_amount").text(res.summary.change_return);
            $('.Porder_tax').text(res.summary.tax);
            $('.Pdiscount').text(res.summary.discount);
            $('.Pshipping').text(res.summary.shipping_charge);
            $('.Pgrand_total').text(finalTotal);
            $('.Ppaid').text(res.summary.grand_total);
            if (payingAmount == 0) {
              $('.Pamount_paid').text(finalTotal);
            } else {
              $('.Pamount_paid').text(finalTotal);
            }
            $('.Preturn_amount').text(changeReturn);
            $('.Pextra_amount').text(extraAmount);
            $(".barcode").text(data.invoice_number);

            // Details rows
            var rows = "";
            $.each(res.details, function (i, item) {
              // console.log(item);
              rows += `
                            <tr>
                                <td colspan="3">
                                    ${item.product_name} - ${item.barcode_last4}<br>
                                    <span>${item.quantity} x ${item.unit_price}</span>
                                </td>
                                <td style="text-align: right;">${item.subtotal}</td>
                            </tr>
                        `;
            });

            // Clear old items & insert new rows
            $(".table_data tbody tr:first").before(rows);

            // Open modal
            $("#paymentModal").modal("hide");
            $("#printModal").modal("show");

            var printContents = document.querySelector("#printModal .modal-body").innerHTML;
            var styles = document.querySelector("#printStyles").innerHTML;

            var originalContents = document.body.innerHTML;

            // Replace body with printable content
            document.body.innerHTML = `
                            <html>
                                <head>
                                    <title>Invoice Print</title>
                                    <style>${styles}</style>
                                </head>
                                <body>${printContents}</body>
                            </html>
                        `;

            window.print();

            // Restore original page after printing
            document.body.innerHTML = originalContents;
            // location.reload();
            // });
          }
        });
      } else {
        toastr.error(data.error);
        //printErrorMsg(data.error);
      }
    });
  });
  $('.btnFinalDraft').click(function () {
    emptyError();
    var formData = $("form#posForm").serializeArray();
    // console.log(formData);
    token();
    var str_url = storeFinalSaleDraft;
    var str_method = "POST";
    var str_data_type = "json";
    CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
      if (data.success) {
        toastr.success("Sale successfully added to draft");
        $('select[name=customer_id]').val('').trigger('change');
        $('#order_tax').val('');
        $('#discount').val('');
        $('#shipping').val('');
        showAllSale();
        setTimeout(function () {
          window.location.reload();
        }, 1500);
      } else {
        toastr.error(data.error);
        //printErrorMsg(data.error);
      }
    });
  });

  function updatePaymentSummary() {
    // read current payable (use global or modal fallback)
    let pay = (typeof payable !== 'undefined' && !isNaN(payable)) ?
      parseFloat(payable) :
      (parseFloat($("#paymentModal").data("payable")) || 0);

    payingAmount = parseFloat($(".payingAmount").val()) || 0;
    balance = 0;
    changeReturn = 0;

    if (payingAmount < pay) {
      balance = pay - payingAmount;
    } else if (payingAmount > pay) {
      changeReturn = payingAmount - pay;
    }

    // update UI
    $('.payingTotal').text(payingAmount.toFixed(2));
    $('.balanceTotal').text(balance.toFixed(2));
    $('.changeReturn').text('-' + changeReturn.toFixed(2));
  }



  $(document).on("click", ".printNow", function () {
    var printContents = document.querySelector("#printModal .modal-body").innerHTML;
    var styles = document.querySelector("#printStyles").innerHTML;

    var printWindow = window.open("", "", "width=400,height=600");
    printWindow.document.write(`
          <html>
              <head>
                  <title>Invoice Print</title>
                  <style>${styles}</style>
              </head>
              <body>${printContents}</body>
          </html>
      `);
    printWindow.document.close();

    printWindow.onload = function () {
      printWindow.focus(); // ensure print dialog appears
      printWindow.print();

      // Close the window automatically after printing
      printWindow.onafterprint = function () {
        printWindow.close();
      };
    };
  });

  $(document).on("click", ".btnClose", function () {
    $("#printModal").modal("hide");   // hide the modal
    location.reload();                // reload the page
  });
  $(document).on("click", ".btn-close", function () {
    // location.reload();
    let customerId = $("#customer_id").val();
    let saleId = $("#sale_id").val();
    window.location.href = window.location.pathname + "?customer_id=" + customerId + "&sale_id=" + saleId;
  });

  $(document).on("click", ".recentDraft", function () {
    // Load data
    pdraft_summery();

    // Open modal manually
    var modal = new bootstrap.Modal(document.getElementById('posDraftModal'));
    modal.show();
  });

  function pdraft_summery() {
    $("#draftSummaryTable").html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');

    $.ajax({
      type: "get",
      url: pos_draft_summery, // your route url
      success: function (response) {
        let rows = "";

        if (response.posDraftSummery.length > 0) {
          response.posDraftSummery.forEach(function (item, index) {
            rows += `
                <tr>
                  <td>${index + 1}</td>
                  <td>${item.invoice_number}</td>
                  <td>${item.customer_name ?? '-'}</td>
                  <td>${item.grand_total ?? '-'}</td>
                  <td>${item.status ?? 'Draft'}</td>
                  <td>
                    <button class="btn btn-sm btn-success convert-btn" data-id="${item.invoice_number}">
                      Convert to Sale
                    </button>
                  </td>
                </tr>
              `;
          });
        } else {
          rows = `<tr><td colspan="5" class="text-center">No draft summaries found.</td></tr>`;
        }

        $("#draftSummaryTable").html(rows);
      }
    });
  }
  function loadPosProducts(page = 1) {
    $.ajax({
      url: latestPosProducts + '?' + "page=" + page,
      type: "GET",
      success: function (response) {
        let productsHtml = "";
        response.data.forEach(function (product) {
          const barcode = product.barcode || "";
          const lastFour = barcode.slice(-4);
          // console.log(product);
          let productImg = (product.product_image && product.product_image.trim() !== "") ?
            imageUrl + '/' + product.product_image :
            imageUrl + '/default.png'; // fallback image
          productsHtml += `
                    <div class="col-lg-4 col-md-4">
                      <a href="#" class="product-card d-block text-decoration-none product-result" data-id="${product.id}" data-code="${barcode}" data-product="${product.id}">
                          <span class="badge-stock">${product.stock} ${product.unitName}</span>
                          <img src="${productImg}" alt="${product.name}">
                          <h6>${product.name}</h6>
                          <p class="text-muted mb-1">${lastFour}</p>
                          <span class="product-price">${response.currency_symbol || '₤'} ${product.sale_price}</span>
                      </a>
                  </div>`;
        });
        $("#product-list").html(productsHtml);

        // Render pagination
        let paginationHtml = "";
        response.links.forEach(function (link) {
          paginationHtml += `
                    <li class="page-item ${link.active ? 'active' : ''} ${link.url ? '' : 'disabled'}">
                        <a class="page-link" href="#" data-page="${link.url ? new URL(link.url).searchParams.get('page') : ''}">
                            ${link.label}
                        </a>
                    </li>`;
        });
        $("#pagination-links").html(paginationHtml);
      }
    });
  }

  // Initial load
  loadPosProducts();

  // Handle pagination click
  $(document).on("click", "#pagination-links a", function (e) {
    e.preventDefault();
    let page = $(this).data("page");
    if (page) loadPosProducts(page);
  });
  function formatDate(date) {
    let d = new Date(date);
    let day = String(d.getDate()).padStart(2, '0');
    let month = String(d.getMonth() + 1).padStart(2, '0');
    let year = d.getFullYear();
    return `${day}-${month}-${year}`;
  }

  $('#todaySaleBtn').on('click', function () {
    $.ajax({
      url: posTodaySaleSummery,
      method: 'GET',
      success: function (response) {
        $('#todayDate').text(formatDate(response.date));
        $('#totalSalesAmount').text(response.currency_symbol + " " + parseFloat(response.today_sale).toFixed(2));
        let modal = new bootstrap.Modal(document.getElementById('todaySaleModal'));
        modal.show();
      }
    });
  });

  $(document).on('click', '.convert-btn', function () {
    let draft_saleInvoiceNumber = $(this).data('id');
    $.ajax({
      type: "get",
      url: posDraftSaleDetail.replace(':id', draft_saleInvoiceNumber),
      success: function (response) {

        if (response.success) {
          // ✅ set invoice number into #sale_id
          $('#sale_id').val(999);
          $('#customer_id_hidden').val(response.customer_id);
          // $('#reference').val(response.invoice_number);
          // localStorage.setItem("sale_id", response.invoice_number);
          localStorage.setItem("customer_id", response.customer_id);
          showAllSale();
          // Optional: show success message
          toastr.success(response.success);
        }
      },
      error: function (xhr) {
        toastr.error("Something went wrong while converting draft.");
      }
    });
  });
});