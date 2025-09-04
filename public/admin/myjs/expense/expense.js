"use strict";
$(function () {
    $('#example').DataTable({
        processing: true,
        serverSide: true,
        ajax: 'expense/show',
        buttons: ['csv', 'excel', 'pdf'],
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'date' },
            { data: 'expense_category' }, // ✅ use alias instead of expense_category_id
            { data: 'amount' },
            { data: 'description' },
            { data: 'action', orderable: false, searchable: false }
        ],
        error: function (xhr, error, code) {
            console.log(xhr);
            console.log(code);
        }
    });


    function cleaner() {
        $('.id').val('');
        $('.amount').val('');     
        $('.description').val('');     
        $('.expense_category').val('');     
        $('#check_uncheck').prop('checked', false);    
        //document.getElementById("statusText").innerText = "OFF";
    }

    //create
    $(document).on('click', '.create', function (e) {
        e.preventDefault();
        cleaner();
        $('#modalEdit').attr('id', 'modalAdd');
        $('#modalAdd').modal('show');
        $('.modal-title').text('Create Expense');
        $('.error-msg').css('display', 'none');
    });

    //edit
    $(document).on('click', '.edit', function (e) {
        e.preventDefault();
        var id = $(this).attr('get_id');
        $('#modalEdit').attr('id', 'modalAdd');
        $('.error-msg').css('display', 'none');
        token();
        var str_url = 'expense/' + id + '/edit';
        var str_method = "GET";
        var str_data_type = "json";
        var data = null;
        CustomAjax(str_url, str_method, data, str_data_type, function (result) {
            if (result.success) {
                let json = jQuery.parseJSON(result.data);
                $('.id').val(json.id);
                let expenseDate = json.date ? json.date.split(' ')[0] : ''; 
                $('.date').val(expenseDate);            
                loadExpenseCategory(json.expense_category_id);
                $('.amount').val(json.amount);
                $('.description').text(json.description);
                $('#modalAdd').modal('show');
                $('.modal-title').text('Update Expense');
                $('#modalAdd').attr('id', 'modalEdit');
            } else {
                printErrorMsg(data.error);
            }
        });
    });

    $(document).on('submit', '#modalAdd', function (e) {
        e.preventDefault();
        var formData = $("form#expense").serializeArray();
        console.log(formData);
        token();
        var str_url = "expense";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                refresh();
                cleaner();
                $('#modalAdd').modal('hide');
                $('.alert-success').html('Expense Create successfully').fadeIn().delay(4000).fadeOut('slow');
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

        var formData = $("form#expense").serializeArray();

        var str_url = "expense/rec_update";
        var str_method = "POST";
        var str_data_type = "json";
        CustomAjax(str_url, str_method, formData, str_data_type, function (data) {
            if (data.success) {
                refresh();
                cleaner();
                $('#modalEdit').modal('hide');
                $('.alert-success').html('Expense Update Successfully').fadeIn().delay(4000).fadeOut('slow');
                $('#modalEdit').attr('id', 'modalAdd');
            } else {
                printErrorMsg(data.error);
            }
        });
    });

});

function loadExpenseCategory(selectedId = null) {
    $.ajax({
        type: "GET",
        url: loadExpenseCategory_route,
        success: function (response) {
            let $select = $('#expense_category');
            $select.empty().append('<option disabled selected>Choose Expense Category</option>');
            response.forEach(function (item) {
                let selected = selectedId == item.id ? 'selected' : '';
                $select.append(`<option value="${item.id}" ${selected}>${item.name}</option>`);
            });
            $select.attr('data-url', expense_category_store).attr('data-callback', 'loadExpenseCategory');
            initSelect2('expense_category', 'Select Expense Category', expense_category_store, 'loadExpenseCategory');
            if (selectedId) $select.val(selectedId).trigger('change');
        }
    });
}
function initSelect2(attributeID, placeholder, storeUrl, reloadCallback) {
    $('#' + attributeID).select2({
        width: '100%',
        dropdownParent: $('.modalAdd'),
        placeholder: placeholder,
        language: {
            noResults: function () {
                return `<div class="text-center">
                    <em>No results found</em><br/>
                    <button 
                        type="button" 
                        class="btn btn-sm btn-primary mt-2 add-inline-btn" 
                        data-id="${attributeID}"
                        data-url="${storeUrl}"
                        data-callback="${reloadCallback}">
                        + Add "<span class="new-entry-text"></span>"
                    </button>
                </div>`;
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });
}
