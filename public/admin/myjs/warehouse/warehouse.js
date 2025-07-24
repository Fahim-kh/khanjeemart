"use strict";

$(function () {
    $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: 'warehouse/show',
        buttons: ['csv', 'excel', 'pdf'],
        columns: [
            {
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
    }

    // Create warehouse modal open
    $(document).on('click', '.create', function (e) {
        e.preventDefault();
        cleaner();
        $('#modalEdit').attr('id', 'modalAdd');
        $('#modalAdd').modal('show');
        $('.modal-title').text('Create Warehouse');
        $('.error-msg').css('display', 'none');
    });

    // Edit warehouse modal open + load data
    $(document).on('click', '.edit', function (e) {
        e.preventDefault();
        var id = $(this).attr('get_id');
        $('#modalEdit').attr('id', 'modalAdd');
        $('.error-msg').css('display', 'none');
        token();
        var str_url = 'warehouse/' + id + '/edit';
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
                $('.modal-title').text('Update Warehouse');
                $('#modalAdd').attr('id', 'modalEdit');
            } else {
                printErrorMsg(data.error);
            }
        });
    });

    // Create warehouse submit
    $(document).on('submit', '#modalAdd', function (e) {
        e.preventDefault();
        var formData = $("form#warehouse").serializeArray();
        token();
        var str_url = "warehouse";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                refresh();
                cleaner();
                $('#modalAdd').modal('hide');
                $('.alert-success').html('Warehouse created successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#modalEdit').attr('id', 'modalAdd');
            } else {
                printErrorMsg(data.error);
            }
        });
    });

    // Update warehouse submit
    $(document).on('submit', '#modalEdit', function (e) {
        e.preventDefault();
        token();
        var formData = $("form#warehouse").serializeArray();
        var str_url = "warehouse/rec_update";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                refresh();
                cleaner();
                $('#modalEdit').modal('hide');
                $('.alert-success').html('Warehouse updated successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#modalEdit').attr('id', 'modalAdd');
            } else {
                printErrorMsg(data.error);
            }
        });
    });

});
