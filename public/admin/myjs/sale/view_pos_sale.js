"use strict";

$(function () {
    $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: 'getPosSale',
        buttons: ['csv', 'excel', 'pdf'],
        columns: [
            {
                data: 'DT_RowIndex',
                name: null, // indexing ke liye
                orderable: false,
                searchable: false
            },
            {
                data: 'sale_date',   // purchase_date -> sale_date
                name: 'sale_summary.sale_date'
            },
            {
                data: 'invoice_number',
                name: 'sale_summary.invoice_number'
            },
            {
                data: 'customer_name',   // supplier_name -> customer_name
                name: 'customers.name'
            },
            {
                data: 'status',
                name: 'sale_summary.status'
            },
            {
                data: 'grand_total',
                name: 'sale_summary.grand_total'
            },
            {
                data: 'action',
                orderable: false,
                searchable: false
            },
            {
                data: 'has_return',
                visible: false
            }
        ],
        createdRow: function (row, data, dataIndex) {
            // console.log('createdRow:', data.id, data.has_return);

            if (String(data.has_return) === '1' || data.has_return === 1) {
                // 1) add a class to row (optional)
                $(row).addClass('bg-light-danger');

                // 2) set background on all TDs of that row (this usually beats any CSS override)
                $('td', row).css('background-color', '#f8d7da');
            }
        },
        error: function (xhr, error, code) {
            console.log(xhr);
            console.log(code);
        }
        
    });


    $(document).on('click', '.sdelete', function (e) {
        e.preventDefault();
        var id = $(this).attr('get_id');
        token();
        $('#deleteModal').modal('show');
        //prevent previous handler - unbind()
        $('#btnDelete').unbind().click(function () {
            var str_url = "sale/saleDelete" + "/" + id;
            var str_method = "POST";
            var str_data_type = "json";
            var data = null;
            CustomAjax(str_url, str_method, data, str_data_type, function (data) {
                if (data) {
                    $('#deleteModal').modal('hide');
                    refresh();
                    $('.alert-danger:first').html('Record Delete Successfully').fadeIn().delay(4000).fadeOut('slow');
                } else {
                    printErrorMsg(data.error);
                }
            });
        });
    });

   $(document).on('click', '.sedit', function (e) {
        e.preventDefault();
        var id = $(this).attr('get_id');
        token();
            var str_url = baseUrl+"/admin/sale/saleTempDelete" + "/" + id;
            var str_method = "POST";
            var str_data_type = "json";
            var data = null;
            CustomAjax(str_url, str_method, data, str_data_type, function (data) {
                if (data) {
                   let url = baseUrl+'/admin/sale/saleEdit/' + id;
                    window.location.href = url;
                } else {
                    printErrorMsg(data.error);
                }
            });
    });
    let payable = 0;
    let balance = 0;
    let payingAmount = 0;
    let extraAmount = 0;
    let changeReturn = 0;
    let finalTotal = 0;
    $(document).on("click", ".viewPosSale", function (e) {
        e.preventDefault();
        var sale_id = $(this).attr('get_id');
        $.ajax({
            url: sale_print.replace(':id', sale_id),
            type: "GET",
            success: function (res) {
              let grandTotal = parseFloat(res.summary.grand_total) || 0;
                if (res.summary.extra_amount !== null) {
                    extraAmount = parseFloat(res.summary.extra_amount);
                } else {
                    extraAmount = 0;
                }
                finalTotal = parseFloat(grandTotal) + extraAmount;
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
                // $('.Pshipping').text(res.summary.shipping_charge);
                $('.Pgrand_total').text(finalTotal);
                $('.Ppaid').text(res.summary.grand_total);
                if(payingAmount ==0){
                  $('.Pamount_paid').text(finalTotal);
                } else{
                  $('.Pamount_paid').text(payingAmount);
                }
                if(res.summary.extra_amount !=  null){
                    $('.Pextra_amount').text(res.summary.extra_amount);
                  } else{
                    $('.Pextra_amount').text(extraAmount);
                  }
                $('.Preturn_amount').text(changeReturn);
                
                $(".barcode").text(res.summary.invoice_number);

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

                // Restore original page after printing
                document.body.innerHTML = originalContents;
                // location.reload();
            // });
            }
        });
    });

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
    $(document).on("click", ".editPosSale", function (e) {
        e.preventDefault();
        var id = $(this).attr('get_id');
        token();
        var str_url = baseUrl + "/admin/sale/saleTempDelete" + "/" + id;
        var str_method = "POST";
        var str_data_type = "json";
        var data = null;
        CustomAjax(str_url, str_method, data, str_data_type, function (data) {
            if (data) {
                let url = baseUrl + '/admin/sale/saleEdit/' + id;
                window.location.href = url;
            } else {
                printErrorMsg(data.error);
            }
        });
        
    });
    
    $(document).on("click", ".btnClose", function () {
        $("#printModal").modal("hide");   // hide the modal
        location.reload();                // reload the page
    });
    $(document).on("click", ".btn-close", function () {
        $("#printModal").modal("hide"); 
    });
});
