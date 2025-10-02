"use strict";

$(document).ready(function () {

    $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: 'sale_return/show',
        buttons: ['csv', 'excel', 'pdf'],
        columns: [{
            data: 'DT_RowIndex',
            name: null, // <-- Important!
            orderable: false,
            searchable: false
        },
        {
            data: 'sale_date'
        },
        {
            data: 'return_invoice_number',name : 'sr.invoice_number'
        },
        {
            data: 'customer_name',
            name: 'customers.name'
        },
        {
            data: 'status'
        },
        {
            data: 'grand_total'
        },
        {
            data: 'action',
            orderable: false,
            searchable: false
        },
        { data: 'sale_invoice_number', name: 'original.invoice_number', visible: false }
        ],
        error: function (xhr, error, code) {
            console.log(xhr);
            console.log(code);
        }
    });

    $('#btnSaleReturn').click(function () {
        emptyError();
        var formData = $("form#saleReturnForm").serializeArray();
        console.log(formData);
        token();
        var str_url = getSaleIndexUrl + "/" + "saleReturnStore";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                var message = "Sale Create successfully";
                $('.alert-success').html(message).fadeIn().delay(4000).fadeOut('slow');
                showToastSuccess(message);
                setTimeout(function () {
                    window.location.href = getSIndexUrl;
                }, 1500);
            } else {
                // console.log('error message'+ data.error);
                toastr.error(data.error);
                printErrorMsg(data.error);
            }
        });
    });


    $(document).on('click', '.item-view', function (e) {
        e.preventDefault();

        var id = $(this).attr('get_id');
        token(); // csrf token inject

        var str_url = baseUrl + "/admin/sale_return/view/detail/" + id;
        var str_method = "GET";
        var str_data_type = "json";
        var data = null;

        CustomAjax(str_url, str_method, data, str_data_type, function (res) {
            if (res.success) {
                // res.return aur res.items use karna hai
                $("#ret_date").text(res.return.sale_date);
                $("#ret_reference").text(res.return.org_sale_invoice);
                $("#ret_customer").text(res.return.customer_name);
                $("#ret_invoice").text(res.return.invoice_number);

                // Totals
                $("#ret_total_amount").text(res.return.total_amount);
                $("#ret_discount").text(res.return.discount);
                $("#ret_tax").text(res.return.tax);
                $("#ret_shipping").text(res.return.shipping_charge);
                $("#ret_grand_total").text(res.return.grand_total);
                let html = "";
                $.each(res.items, function (i, item) {
                    html += `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${item.product_name}</td>
                            <td>${item.product_code}</td>
                            <td>${item.quantity} ${item.unit_name ?? ''}</td>
                            <td>${item.selling_unit_price}</td>
                            <td>${item.subtotal}</td>
                        </tr>
                    `;
                });

                $("#ret_items").html(html);
                $("#saleReturnDetailModal").modal("show");
            } else {
                printErrorMsg(res.message);
            }
        });
    });



});
