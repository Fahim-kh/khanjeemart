"use strict";

$(function () {
    $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: 'customer/show',
        buttons: ['csv', 'excel', 'pdf'],
        columns: [
            {
                data: 'DT_RowIndex',
                name: null,
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
        $('.owner').val('');
        $('#check_uncheck').prop('checked', false);
    }

    // Create customer modal open
    $(document).on('click', '.create', function (e) {
        e.preventDefault();
        cleaner();
        $('#modalEdit').attr('id', 'modalAdd');
        $('#modalAdd').modal('show');
        $('.modal-title').text('Create Customer');
        $('.error-msg').css('display', 'none');
    });

    // Edit customer modal open + load data
    $(document).on('click', '.edit', function (e) {
        e.preventDefault();
        var id = $(this).attr('get_id');
        $('#modalEdit').attr('id', 'modalAdd');
        $('.error-msg').css('display', 'none');
        token();
        var str_url = 'customer/' + id + '/edit';
        var str_method = "GET";
        var str_data_type = "json";
        var data = null;
        CustomAjax(str_url, str_method, data, str_data_type, function (result) {
            if (result.success) {
                let json = jQuery.parseJSON(result.data);
                $('.id').val(json.id);
                $('.name').val(json.name);
                $('.email').val(json.email);
                $('.phone').val(json.phone);
                $('.address').val(json.address);
                $('.country').val(json.country);
                $('.city').val(json.city);
                $('.tax_number').val(json.tax_number);
                $('.opening_balance').val(json.opening_balance);
                $('.owner').val(json.owner);

                changeStatus(json.status);

                $('#modalAdd').modal('show');
                $('.modal-title').text('Update Customer');
                $('#modalAdd').attr('id', 'modalEdit');
            } else {
                printErrorMsg(result.error || 'Failed to load data');
            }
        });
    });

    // Create customer submit
    $(document).on('submit', '#modalAdd', function (e) {
        e.preventDefault();
        var formData = $("form#customer").serializeArray();
        token();
        var str_url = "customer";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                refresh();
                cleaner();
                $('#modalAdd').modal('hide');
                $('.alert-success').html('Customer created successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#modalEdit').attr('id', 'modalAdd');
            } else {
                printErrorMsg(data.error);
            }
        });
    });

    // Update customer submit
    $(document).on('submit', '#modalEdit', function (e) {
        e.preventDefault();
        token();
        var formData = $("form#customer").serializeArray();
        var str_url = "customer/rec_update";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                refresh();
                cleaner();
                $('#modalEdit').modal('hide');
                $('.alert-success').html('Customer updated successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#modalEdit').attr('id', 'modalAdd');
            } else {
                printErrorMsg(data.error);
            }
        });
    });
});
