$(document).ready(function() {

    var calendar = jsCalendar.new("#calendar");
    //console.log(calendar);
    calendar.onDateClick(function(e, date) {
        selectDate(calendar, date);
        getRecord();
    })

    $(".timepicker").timepicki({ on_change: setHour });

});

function selectDate(calendar, date) {

    calendar.clearSelected();
    calendar.select(date);

    var _date = jsCalendar.tools.dateToString(date, "yyyy-MM-DD");
    var _disp = jsCalendar.tools.dateToString(date, "MMM DD, yyyy");
    $("#date").val(_date);
    $("#dateDisplay").val(_disp);
}

function setHour() {

    var timeInSt = $("#timeIn").val();
    var timeOutSt = $("#timeOut").val();

    if (timeInSt === '' || timeOutSt === '')
        return;

    $("#hour").val('');

    var timeIn = moment(timeInSt, "hh:mm tt");
    var timeOut = moment(timeOutSt, "hh:mm tt");

    var hours = timeOut - timeIn;
    hours = hours/3600000;

    if (hours < 0) {
        hours = 24 - Math.abs(hours);
    }

    hours = Math.round(hours * 100) / 100;

    $("#hour").val(hours);
}

function setTimeOutByHour() {
    var hoursSt = $("#hour").val();
    var timeInSt = $("#timeIn").val();

    if (hoursSt === '' || timeInSt === '') {
        return;
    }

    var timeIn = moment(timeInSt, "hh:mm tt");

    var timeout = timeIn.add(hoursSt*1, 'hours');
    var timeoutSt = timeout.format("HH:mm");

    $("#timeOut").val(timeoutSt);

}

function resetOutliers() {
    $('input[type=\'radio\']').prop('checked', false);
}


function getRecord() {
    var date = $("#date").val();
    var id = $("#employeeId").val();
    var url = "/manhour/getrecord/" + id + "/" + date;

    $("#idEdit").val(id);
    $.ajax({
        url: url,
        contentType: 'text/plain',
        dataType:"json",
        success: function(result) {
            clearDetails();
            mapDetails(result);
        },
        error: function(result) {
            clearDetails();
        }
    });
}

function clearDetails() {

    $("#timeIn").val('');
    $("#timeOut").val('');
    $("[name='outlier']").prop('checked', false);
    $("[name='authorized']").prop('checked', false);
    $("#remarks").text('');

}

function mapDetails(json) {

    $("#departmentNameDisplay").html(json.departmentName);
    $("#departmentIdDisplay").val(json.departmentId);
    $("[name='outlier'][value='" + json.outlier+ "']").prop('checked', true);

    if (json.timeIn == null)
        return;

    $("#timeIn").setTime(json.timeIn);
    $("#timeOut").setTime(json.timeOut);

    $("#timeIn").val(json.timeIn);
    $("#timeOut").val(json.timeOut);
    $("#timeCardDisplay").html(json.timeCard);

    if (json.authorized)
        $("[name='authorized']").prop('checked', true);
    $("#remarks").text(json.remarks);
    setHour();

}
