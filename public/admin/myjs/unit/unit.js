"use strict";

$(function () {
    $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: 'unit/show',
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
        $('.modal-title').text('Create Unit');
        $('.error-msg').css('display', 'none');
    });

    //edit
    $(document).on('click', '.edit', function (e) {
        e.preventDefault();
        var id = $(this).attr('get_id');
        $('#modalEdit').attr('id', 'modalAdd');
        $('.error-msg').css('display', 'none');
        token();
        var str_url = 'unit/' + id + '/edit';
        var str_method = "GET";
        var str_data_type = "json";
        var data = null;
        CustomAjax(str_url, str_method, data, str_data_type, function (result) {
            if (result.success) {
                let json = jQuery.parseJSON(result.data);
                $('.id').val(json.id);
                $('.name').val(json.name);
                changeStatus(json.status);
                $('#modalAdd').modal('show');
                $('.modal-title').text('Update Unit');
                $('#modalAdd').attr('id', 'modalEdit');
            } else {
                printErrorMsg(data.error);
            }
        });
    });

    $(document).on('submit', '#modalAdd', function (e) {
        e.preventDefault();
        var formData = $("form#unit").serializeArray();
        console.log(formData);
        token();
        var str_url = "unit";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                refresh();
                cleaner();
                $('#modalAdd').modal('hide');
                $('.alert-success').html('Unit Create successfully').fadeIn().delay(4000).fadeOut('slow');
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

        var formData = $("form#unit").serializeArray();

        var str_url = "unit/rec_update";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                refresh();
                cleaner();
                $('#modalEdit').modal('hide');
                $('.alert-success').html('Unit Update Successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#modalEdit').attr('id', 'modalAdd');
            } else {
                printErrorMsg(data.error);
            }
        });
    });

});
