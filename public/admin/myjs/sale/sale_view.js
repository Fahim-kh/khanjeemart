"use strict";

$(function () {
    $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: 'sale/show',

        //export option
        dom: 'Bfrtip',
        buttons: [
            // {
            //     extend: 'excel',
            //     className: 'btn btn-success btn-sm',
            //     exportOptions: {
            //         columns: ':not(:last-child)'  // 👈 last column exclude
            //     }
            // },
            {
                extend: 'pdfHtml5',
                className: 'btn btn-danger btn-sm',
                orientation: 'portrait',
                pageSize: 'A4',
                exportOptions: { columns: ':not(:last-child)' },
                customize: function (doc) {
                    doc.defaultStyle.fontSize = 10;
                    doc.styles.tableHeader.fontSize = 12;

                    // ✅ Table ko full width
                    doc.content[1].table.widths =
                        Array(doc.content[1].table.body[0].length + 1).join('*').split('');

                    // ✅ Columns left align
                    doc.content[1].table.body.forEach(function (row, rowIndex) {
                        row.forEach(function (cell) {
                            cell.alignment = 'left';
                        });
                    });

                    // ✅ Custom Header
                    doc['header'] = function (currentPage, pageCount, pageSize) {
                        return {
                            columns: [
                                { text: 'Khanjee Beauty Mart', alignment: 'left', margin: [20, 10], fontSize: 14, bold: true },
                                { text: 'Sale Report', alignment: 'center', fontSize: 12, margin: [0, 10] },
                                { text: 'Page ' + currentPage.toString() + ' of ' + pageCount, alignment: 'right', margin: [0, 10], fontSize: 10 }
                            ],
                            margin: [20, 10]
                        };
                    };

                    // ✅ Custom Footer (optional)
                    doc['footer'] = function (currentPage, pageCount) {
                        return {
                            columns: [
                                { text: 'Generated on: ' + new Date().toLocaleString(), alignment: 'left', margin: [20, 0], fontSize: 8 },
                                { text: '© ' + new Date().getFullYear() + ' Khanjee Beauty Mart', alignment: 'right', margin: [0, 0, 20, 0], fontSize: 8 }
                            ]
                        };
                    };
                }
            },
            // {
            //     extend: 'csv',
            //     className: 'btn btn-primary btn-sm',
            //     exportOptions: {
            //         columns: ':not(:last-child)'
            //     }
            // },
            // {
            //     extend: 'print',
            //     className: 'btn btn-info btn-sm',
            //     exportOptions: {
            //         columns: ':not(:last-child)'
            //     }
            // }
        ],
        //export option close
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

});
