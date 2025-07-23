function printErrorMsg(msg) {
    $('.error-msg').find('div.error_list').html('');
    $('.error-msg').css('display', 'block');
    $.each(msg, function (key, value) {
        console.log(value);
        $(".error-msg").find("div.error_list").append('<p>' + value + '</p>');
    });
}

function printSingleErrorMsg(msg) {
    $('.error-msg').find('div.error_list').html('');
    $('.error-msg').css('display', 'block');    
    $(".error-msg").find("div.error_list").append('<p>' + msg + '</p>');    
}

function emptyError() {
    $('.error-msg').find('div.error_list').html('');
    $('.error-msg').css('display', 'none');    
}

function refresh() {
    var table = $('#example').DataTable();
    table.ajax.reload(null, false);
}

function refresh1() {
    var table = $('#example1').DataTable();
    table.ajax.reload(null, false);
}


function token() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
}

function CustomAjax(str_url, str_method, str_data, str_data_type, callback) {
    $("#global-loader").fadeIn("slow");
    var request = $.ajax({
        url: str_url,
        method: str_method,
        data: str_data,
        dataType: str_data_type
    });

    request.done(function (result) {
        console.log(result);
        if (result.success) {
            callback(result);
        } else {
            callback(result);
        }
    });

    request.fail(function (xhr, textStatus) {
        console.log("Request failed: " + textStatus);
        console.log("readyState: " + xhr.readyState + "\nstatus: " + xhr.status);
        console.log("responseText: " + xhr.responseText);
        alert("Oppss! Something went wrong");
    });

    request.always(function () {
        $("#global-loader").fadeOut("slow");
    });
}

function CustomAjaxWithImage(str_url, str_method, str_data, str_data_type, callback) {
    $("#global-loader").fadeIn("slow");
    var request = $.ajax({
        url: str_url,
        method: str_method,
        data: str_data,
        dataType: str_data_type,
        contentType: false,
        processData: false
    });

    request.done(function (result) {
        console.log(result);
        if (result.success) {
            callback(result);
        } else {
            callback(result);
        }
    });

    request.fail(function (xhr, textStatus) {
        console.log("Request failed: " + textStatus);
        console.log("readyState: " + xhr.readyState + "\nstatus: " + xhr.status);
        console.log("responseText: " + xhr.responseText);
        alert("Oppss! Something went wrong");
    });

    request.always(function () {
        $("#global-loader").fadeOut("slow");
    });
}

$(document).on('click', '.delete', function (e) {
    e.preventDefault();
    var id = $(this).attr('get_id');
    token();
    $('#deleteModal').modal('show');
    //prevent previous handler - unbind()
    $('#btnDelete').unbind().click(function () {
        var str_url = $("#delete_record").attr('url') + "/" + id;
        var str_method = "DELETE";
        var str_data_type = "json";
        var data = null;
        CustomAjax(str_url, str_method, data, str_data_type, function (data) {
            if (data) {
                $('#deleteModal').modal('hide');
                refresh();
                $('.alert-danger:first').html('Record Delete Successfully').fadeIn().delay(4000).fadeOut('slow');
            } else {
                printErrorMsg(data.error);
            }
        });
    });
});


$(document).on('click', '.delete_all', function (e) {
    e.preventDefault();
    token();
    $('#deleteModal').modal('show');
    $('#btnDelete').unbind().click(function () {
        var check_all = [];
        $("input[name='chk_del[]']:checked").each(function () {
            check_all.push($(this).val());
        });

        //console.log(check_all);

        var str_url = $("#delete_all_record").attr('url');
        var str_method = "POST";
        var str_data_type = "json";
        var data = {
            check_all: check_all
        }
        CustomAjax(str_url, str_method, data, str_data_type, function (data) {
            if (data) {
                $('#deleteModal').modal('hide');
                refresh();
                $('#chk_select_all').prop('checked', false);
                $('.alert-danger:first').html('Record Delete Successfully').fadeIn().delay(4000).fadeOut('slow');
            } else {
                printErrorMsg(data.error);
            }
        });
    });

});


$(document).on('click', '.status_change', function (e) {
    e.preventDefault();
    token();
    var check_all = [];
    $("input[name='chk_del[]']:checked").each(function () {
        check_all.push($(this).val());
    });

    console.log(check_all);

    var str_url = $("#status_change").attr('url');
    var str_method = "POST";
    var str_data_type = "json";
    var data = {
        check_all: check_all
    }
    CustomAjax(str_url, str_method, data, str_data_type, function (data) {
        if (data) {            
            refresh();
            $('#chk_select_all').prop('checked', false);
            $('.alert-success:first').html('Status Update Successfully').fadeIn().delay(4000).fadeOut('slow');
        } else {
            printErrorMsg(data.error);
        }
    });
});

$('#chk_select_all').change(function () {
    var id = this.id;
    $('.chk_del').prop('checked', this.checked);
});

$(document).on('change', '.chk_del', function () {
    if ($('.chk_del:checked').length == $('.chk_del').length) {
        $('#chk_select_all').prop('checked', true);
    } else {
        $('#chk_select_all').prop('checked', false);
    }
});

$('#toggleForm :checkbox').change(function () {
    if ($(this).is(':checked')) {
        var status_str = "ON";
    } else {
        var status_str = "OFF";
    }
    document.getElementById("statusText").innerText = status_str;
});

$('#StatusToggleForm :checkbox').change(function () {
    if ($(this).is(':checked')) {
        var status_str = "ON";
    } else {
        var status_str = "OFF";
    }
    document.getElementById("localText").innerText = status_str;
});

$('#toggleForm :checkbox').change(function () {
    if ($(this).is(':checked')) {
        var status_str = "ON";
    } else {
        var status_str = "OFF";
    }
    document.getElementById("statusText").innerText = status_str;
});

$('#toggleForm1 :checkbox').change(function () {
    if ($(this).is(':checked')) {
        var status_str = "ON";
    } else {
        var status_str = "OFF";
    }
    document.getElementById("statusText1").innerText = status_str;
});

function changeStatus(status) {
    // if (status == 1) {
    //     $('#check_uncheck').prop('checked', true);
    // } else {
    //     $('#check_uncheck').prop('checked', false);
    // }
    if (status == 1) {
        $('#switch1').prop('checked', true);
    } else {
        $('#switch1').prop('checked', false);
    }
}

function readOnly(status, field) {
    if (status == "on" || status == "1")
        $(field).attr("readonly", false);
    else
        $(field).attr('readonly', true);
}

function multiple_selected(field, array) {
    $(field + "> option").each(function () {
        if (array.includes($(this).val())) {
            $(field + " option[value='" + $(this).val() + "']").prop("selected", true);
        }
    });
}

function sleep(miliseconds) {
    var currentTime = new Date().getTime();
    while (currentTime + miliseconds >= new Date().getTime()) {
    }
}

function dateFormateChange(date)
{        
    var parts = date.split("-");
    var year = parts[0];
    var month = parts[1];
    var day = parts[2];        
    var newDate = day + "-" + month + "-" + year;
    return newDate;
}