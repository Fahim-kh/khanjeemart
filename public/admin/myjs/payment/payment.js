"use strict";
$(function () {

    $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: 'payment/show',
        buttons: ['csv', 'excel', 'pdf'],
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'entry_date' },
            { data: 'transaction_type' },
            { data: 'trans_mode' },
            { 
                data: 'customer',
                name: 'customers.name'
            },   // ab customers.name ka alias hai
            { 
                data: 'supplier',
                name: 'suppliers.name'

            },   // ab suppliers.name ka alias hai
            { data: 'amount' },
            { data: 'comments' },
            { data: 'action', orderable: false, searchable: false }
        ],
        error: function (xhr, error, code) {
            console.log(xhr);
            console.log(code);
        }
    });

    // Toggle Customer / Supplier
    $(document).on('change', '#transaction_type', function () {
        if ($(this).val() === 'PaymentFromCustomer') {
            $('.customer_div').removeClass('d-none');
            $('.supplier_div').addClass('d-none');
        } else if ($(this).val() === 'PaymentToVendor') {
            $('.supplier_div').removeClass('d-none');
            $('.customer_div').addClass('d-none');
        } else {
            $('.customer_div, .supplier_div').addClass('d-none');
        }
    });

    // Toggle Cheque fields
    $(document).on('change', '#trans_mode', function () {
        if ($(this).val() === 'cheque') {
            $('.cheque_fields').removeClass('d-none');
        } else {
            $('.cheque_fields').addClass('d-none');
        }
    });


    function cleaner() {
        $('.id').val('');
        $('.transaction_type').val('');
        //$('.trans_mode').val('');
        $('.supplier_id').val('');
        $('.customer_id').val('');
        $('.amount').val('');
        $('.cheque_no').val('');
        $('.received_from').val('');
        $('.payee_from').val('');
        $('.comments').val('');
    }

    // create
    $(document).on('click', '.create', function (e) {
        e.preventDefault();
        cleaner();
        $('#modalEdit').attr('id', 'modalAdd');
        $('#modalAdd').modal('show');
        $('.modal-title').text('Create Payment');
        $('.error-msg').css('display', 'none');
    });

    // edit
    $(document).on('click', '.edit', function (e) {
        e.preventDefault();
        var id = $(this).attr('get_id');
        $('#modalEdit').attr('id', 'modalAdd');
        $('.error-msg').css('display', 'none');
        token();
        var str_url = 'payment/' + id + '/edit';
        var str_method = "GET";
        var str_data_type = "json";
        var data = null;
        CustomAjax(str_url, str_method, data, str_data_type, function (result) {
            if (result.success) {
                let json = result.data;

                $('.id').val(json.id);
                $('.transaction_type').val(json.transaction_type);
                $('.trans_mode').val(json.trans_mode);
                $('.supplier_id').val(json.supplier_id);
                $('.customer_id').val(json.customer_id);
                $('.amount').val(json.amount);
                $('.cheque_no').val(json.cheque_no);
                $('.cheque_date').val(json.cheque_date);
                $('.received_from').val(json.received_from);
                $('.payee_from').val(json.payee_from);
                $('.comments').text(json.comments);
                $('.entry_date').val(json.entry_date);

                $('#modalAdd').modal('show');
                $('.modal-title').text('Update Payment');
                $('#modalAdd').attr('id', 'modalEdit');
            } else {
                printErrorMsg(result.error);
            }
        });
    });

    // store
    $(document).on('submit', '#modalAdd', function (e) {
        e.preventDefault();
        var formData = $("form#payment").serializeArray();
        token();
        var str_url = "payment";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                refresh();
                cleaner();
                $('#modalAdd').modal('hide');
                $('.alert-success').html('Payment Created successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#modalEdit').attr('id', 'modalAdd');
            } else {
                printErrorMsg(data.error);
            }
        });
    });

    // update
    $(document).on('submit', '#modalEdit', function (e) {
        e.preventDefault();
        token();
        var formData = $("form#payment").serializeArray();
        var str_url = "payment/rec_update";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                refresh();
                cleaner();
                $('#modalEdit').modal('hide');
                $('.alert-success').html('Payment Updated Successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#modalEdit').attr('id', 'modalAdd');
            } else {
                printErrorMsg(data.error);
            }
        });
    });
});
