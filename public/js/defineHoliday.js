var calendar;

$(document).ready(function() {

    calendar = jsCalendar.new("#calendar");
    //console.log(calendar);
    calendar.onDateClick(function(e, date) {
        console.log(date);
        selectDate(calendar, date);
        getDate();
    })

    $('#saveBtn').click(function() {
        saveHoliday();
    });
    $('#deleteBtn').click(function() {
        deleteHoliday();
    });

    $('[data-conditional]').click(function() {

    });

});


function onTableRowClicked(self) {
    var date = $(self).data('conditional');
    selectDate(calendar, new Date(date));
    getDate();
}


function selectDate(calendar, date) {
    calendar.clearSelected();
    $("#warningText").show();
    calendar.select(date);

    var _date = jsCalendar.tools.dateToString(date, "yyyy-MM-DD");
    var _disp = jsCalendar.tools.dateToString(date, "MMM DD, yyyy");
    $("#date").val(_date);
    $("#dateDisplay").val(_disp);
    $("#warningText").hide();

    $('#name').val('');
    $('#description').val('');
    $('[name="type"]').prop('checked', false);
    $("#deleteBtn").hide();
}


function getDate() {
    var date = $('#date').val();
    $.ajax({
        'url': '/manhour/getholiday/' + date,
        'method': 'GET',
        'contentType': 'plain/text',
        'dataType': 'json',
        'success': result => {
            console.log(result);
            mapDetails(result);
        }
    })
}

function saveHoliday() {
    var date = $('#date').val();
    var name = $('#name').val();
    var description = $('#description').val();
    var type = '';
    $('[name="type"]').each(function () {
        if ($(this).prop('checked')) {
            type = $(this).val();
            return;
        }
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        'url': '/manhour/saveholiday',
        'data': {
            'name': name,
            'description': description,
            'date': date,
            'type': type
        },
        'method': 'post',
        'dataType': 'json',
        'success': result => {
            mapResults(result);
            var dateFormatted = moment(date);
            if ($('[data-conditional="' + date + '"]').length > 0) {
                $('[data-conditional="' + date + '"]').remove();
            }
            $('#holidayList').append('<tr data-conditional="' + date + '" onclick="onTableRowClicked(this)"><td>' + dateFormatted.format('MMM DD, YYYY') + '</td><td>' + name + '</td><td>' + type + '</td><td>' + description + '</td></tr>');
            $("#deleteBtn").show();
        },
        'error': err => {
            console.error("Failed to save holiday", err);
        }

    })
}



function deleteHoliday() {
    var date = $('#date').val();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        'url': '/manhour/deleteholiday',
        'data': {
            'date': date
        },
        'method': 'post',
        'dataType': 'json',
        'success': result => {
            mapResults(result);
            $('#name').val('');
            $('#description').val('');
            $('[name="type"]').prop('checked', false);
            $("#deleteBtn").hide();
            $('tr[data-conditional="' + date + '"]').remove();
        },
        'error': err => {
            console.error("Failed to delete holiday", err);
        }

    })
}


function mapDetails(json) {

    $('#name').val(json.name);
    $('#description').val(json.description);
    $('[name="type"][value="' + json.type + '"]').prop('checked', true);
    $("#deleteBtn").show();

}


function mapResults(json) {

    if (json.result === false) {
        $('.alert-danger span').text(json.message);
        $('.alert-success').hide();
        $('.alert-danger').show();
        return;
    }

    $('.alert-success span').text(json.message);
    $('.alert-danger').hide();
    $('.alert-success').show();

}
