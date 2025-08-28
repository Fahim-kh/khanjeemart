"use strict";

$(document).ready(function() {

    $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: 'purchase_return/show',
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

$('#btnPurchaseReturn').click(function () {
        emptyError();
        var formData = $("form#purchaseReturnForm").serializeArray();
        console.log(formData);
        token();
        var str_url = getPurchaseIndexUrl +"/"+"purchaseReturnStore";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                var message = "Purchase Create successfully";
                $('.alert-success').html(message).fadeIn().delay(4000).fadeOut('slow');
                //$('#purchaseForm')[0].reset();
                showToastSuccess(message);
                setTimeout(function() {
                            window.location.href = getPIndexUrl;
                    }, 1500);
            } else {
                // console.log('error message'+ data.error);
                toastr.error(data.error);
                printErrorMsg(data.error);
            }
        });
    });
    
   
});
