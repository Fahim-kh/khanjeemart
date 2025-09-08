"use strict";

$(function () {
    $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: 'product/show',
        buttons: ['csv', 'excel', 'pdf'],
        columns: [{
                data: 'DT_RowIndex',
                name: null, // <-- Important!
                orderable: false,
                searchable: false
            },
            {
                data: 'name'
            },
            {
                data: 'barcode'
            },
            {
                data: 'status'
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


    function cleaner() {
        $('.id').val('');
        $('.name').val('');     
        $('#check_uncheck').prop('checked', false);    
        //document.getElementById("statusText").innerText = "OFF";
    }

    //create
    $(document).on('click', '.create', function (e) {
        e.preventDefault();
        cleaner();
        $('#modalEdit').attr('id', 'modalAdd');
        $('#modalAdd').modal('show');
        $('.modal-title').text('Create Product');
        $('.error-msg').css('display', 'none');
    });

    //edit
    $(document).on('click', '.edit', function (e) {
        e.preventDefault();
        var id = $(this).attr('get_id');
        var str_url = 'product/' + id + '/edit';
        window.location.href = str_url;
    });
     //view
     $(document).on('click', '.view', function (e) {
        e.preventDefault();
        var id = $(this).attr('get_id');
        var str_url = 'product/view/' + id;
        window.location.href = str_url;
    });
    

    $(document).on('submit', '#modalAdd', function (e) {
        e.preventDefault();
        var formData = $("form#product").serializeArray();
        token();
        var str_url = "product";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                refresh();
                cleaner();
                $('#modalAdd').modal('hide');
                $('.alert-success').html('Product Create successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#modalEdit').attr('id', 'modalAdd');
            } else {
                printErrorMsg(data.error);
            }
        });
    });

    //update
    $(document).on('submit', '#modalEdit', function (e) {
        e.preventDefault();
        token();

        var formData = $("form#product").serializeArray();

        var str_url = "product/rec_update";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                refresh();
                cleaner();
                $('#modalEdit').modal('hide');
                $('.alert-success').html('Product Update Successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#modalEdit').attr('id', 'modalAdd');
            } else {
                printErrorMsg(data.error);
            }
        });
    });

});
