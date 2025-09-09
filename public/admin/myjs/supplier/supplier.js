"use strict";

$(function () {
    $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: 'supplier/show',
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
                data: 'email'
            },
            {
                data: 'phone'
            },
            {
                data: 'city'
            },
            {
                data: 'opening_balance'
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
        $('.email').val('');
        $('.phone').val('');
        $('.address').val('');
        $('.country').val('');
        $('.city').val('');
        $('.tax_number').val('');
        $('.opening_balance').val('');
        $('#check_uncheck').prop('checked', false);
    }

    // Create supplier modal open
    $(document).on('click', '.create', function (e) {
        e.preventDefault();
        cleaner();
        $('#modalEdit').attr('id', 'modalAdd');
        $('#modalAdd').modal('show');
        $('.modal-title').text('Create Supplier');
        $('.error-msg').css('display', 'none');
    });

    // Edit supplier modal open + load data
    $(document).on('click', '.edit', function (e) {
    e.preventDefault();
        var id = $(this).attr('get_id');
        $('#modalEdit').attr('id', 'modalAdd');
        $('.error-msg').css('display', 'none');
        token();
        var str_url = 'supplier/' + id + '/edit';
        var str_method = "GET";
        var str_data_type = "json";
        var data = null;
        CustomAjax(str_url, str_method, data, str_data_type, function (result) {
            if (result.success) {
                let json = jQuery.parseJSON(result.data);
                // Fill form fields
                $('.id').val(json.id);
                $('.name').val(json.name);
                $('.email').val(json.email);
                $('.phone').val(json.phone);
                $('.address').val(json.address);
                $('.country').val(json.country);
                $('.city').val(json.city);
                $('.opening_balance').val(json.opening_balance);
                $('.tax_number').val(json.tax_number);

                // Assuming you have a function to toggle the status UI
                changeStatus(json.status);

                // Show the modal and update titles & id for update form
                $('#modalAdd').modal('show');
                $('.modal-title').text('Update Supplier');
                $('#modalAdd').attr('id', 'modalEdit');
            } else {
                printErrorMsg(result.error || 'Failed to load data');
            }
        });
    });


    // Create supplier submit
    $(document).on('submit', '#modalAdd', function (e) {
        e.preventDefault();
        var formData = $("form#supplier").serializeArray();
        token();
        var str_url = "supplier";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                refresh();
                cleaner();
                $('#modalAdd').modal('hide');
                $('.alert-success').html('Supplier created successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#modalEdit').attr('id', 'modalAdd');
            } else {
                printErrorMsg(data.error);
            }
        });
    });

    // Update supplier submit
    $(document).on('submit', '#modalEdit', function (e) {
        e.preventDefault();
        token();
        var formData = $("form#supplier").serializeArray();
        var str_url = "supplier/rec_update";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                refresh();
                cleaner();
                $('#modalEdit').modal('hide');
                $('.alert-success').html('Supplier updated successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#modalEdit').attr('id', 'modalAdd');
            } else {
                printErrorMsg(data.error);
            }
        });
    });
});
