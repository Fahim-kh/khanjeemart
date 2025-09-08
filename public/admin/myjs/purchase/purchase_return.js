"use strict";

$(function () {
    

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
                            window.location.href = getPurchaseIndexUrl;
                    }, 1500);
            } else {
                // console.log('error message'+ data.error);
                toastr.error(data.error);
                printErrorMsg(data.error);
            }
        });
    });


    
   
});
