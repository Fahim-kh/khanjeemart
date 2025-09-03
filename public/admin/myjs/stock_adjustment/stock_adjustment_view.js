"use strict";

$(function () {
    $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: 'stock_adjustment/show', // ✅ apni route ka URL yahan lagao
        buttons: ['csv', 'excel', 'pdf'],
        columns: [
            {
                data: 'DT_RowIndex',
                name: null, // indexing ke liye
                orderable: false,
                searchable: false
            },
            {
                data: 'adjustment_date',
                name: 'stock_adjustments.adjustment_date'
            },
            {
                data: 'reference',
                name: 'stock_adjustments.reference'
            },
            {
                data: 'product_count',   // ✅ total products
                name: 'product_count',
                orderable: false,
                searchable: false
            },
            {
                data: 'action',
                orderable: false,
                searchable: false
            }
        ],
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
            var str_url = "stock_adjustment/StockAdjustmentDelete" + "/" + id;
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
            var str_url = baseUrl+"/admin/stock_adjustment/StockAdjustmentTempDelete" + "/" + id;
            var str_method = "POST";
            var str_data_type = "json";
            var data = null;
            CustomAjax(str_url, str_method, data, str_data_type, function (data) {
                if (data) {
                   let url = baseUrl+'/admin/stock_adjustment/StockAdjustmentEdit/' + id;
                    window.location.href = url;
                } else {
                    printErrorMsg(data.error);
                }
            });
    });


    $(document).on('click', '.item-view', function (e) {
        e.preventDefault();

        var id = $(this).attr('get_id');
        token(); // csrf token inject

        var str_url = baseUrl + "/admin/stock_adjustment/view/detail/" + id;
        var str_method = "GET";
        var str_data_type = "json";
        var data = null;

        CustomAjax(str_url, str_method, data, str_data_type, function (res) {
        if (res.success) {
            // direct res.adjustment aur res.items use karna hai
            $("#adj_date").text(res.adjustment.adjustment_date);
            $("#adj_reference").text(res.adjustment.reference);

            let html = "";
            $.each(res.items, function (i, item) {
                html += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${item.product_name}</td>
                        <td>${item.product_code}</td>
                        <td>${item.quantity} ${item.unit_name ?? ''}</td>
                        <td>${item.adjustment_type}</td>
                    </tr>
                `;
            });

            $("#adj_items").html(html);
            $("#adjustmentDetailModal").modal("show");
        } else {
            printErrorMsg(res.message);
        }
    });
    });





});
