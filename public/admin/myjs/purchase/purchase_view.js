"use strict";

$(function () {
    $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: 'purchase/show',
        buttons: ['csv', 'excel', 'pdf'],
        columns: [{
                data: 'DT_RowIndex',
                name: null, // <-- Important!
                orderable: false,
                searchable: false
            },
            {
                data: 'purchase_date'
            },
            {
                data: 'invoice_number'
            },
            {
               data: 'supplier_name',
                name: 'suppliers.name'    
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
            }
        ],
        error: function (xhr, error, code) {
            console.log(xhr);
            console.log(code);
        }
    });


   

});
