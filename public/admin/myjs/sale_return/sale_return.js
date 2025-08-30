"use strict";

$(document).ready(function() {

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
                data: 'invoice_number'
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
            }
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
        var str_url = getSaleIndexUrl +"/"+"saleReturnStore";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                var message = "Sale Create successfully";
                $('.alert-success').html(message).fadeIn().delay(4000).fadeOut('slow');
                showToastSuccess(message);
                setTimeout(function() {
                            window.location.href = getSIndexUrl;
                    }, 1500);
            } else {
                // console.log('error message'+ data.error);
                toastr.error(data.error);
                printErrorMsg(data.error);
            }
        });
    });
    
   
});
