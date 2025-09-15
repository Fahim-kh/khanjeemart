"use strict";
$(function () {
  let searchTimeout;
  showAllSale();
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
          response.forEach(function (product) {
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
    console.log(product);
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

          for (let i = 0; i < json.length; i++) {
            console.log(json[i]);
            if (json[0].customer_id != null) {
              window.loadCustomers(json[0].customer_id);
              $('#customer_id_hidden').val(json[0].customer_id);
              $('#customer_id').prop('disabled', true).trigger('change.select2');
            }
            const barcode = json[i].productBarcode 
              ? json[i].productBarcode.slice(-4) 
              : '';
            html += '<tr data-id="' + json[i].id + '">' +
            `<td class="text-start">${json[i].productName} - ${barcode}</td>
              <td class="text-center">
                  <input type="number" name="sale_price" class="form-control" value="${json[i].selling_unit_price}">
              </td>
              <td class="text-center">
                  <input type="number" value="${json[i].quantity}" min="1"
                      class="form-control form-control-sm text-center"
                      style="width: 70px; margin: auto;">
              </td>
              <td class="text-center">${json[i].subtotal}</td>
              <td class="text-center">Remove</td>
            </tr>`;

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

});

