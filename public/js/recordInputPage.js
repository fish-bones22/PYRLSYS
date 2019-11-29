$(document).ready(function() {

    $("#timeIn").attr("disabled", "");
    $("#timeOut").attr("disabled", "");
    $("#hour").attr("disabled", "");

    if ($("#hasinvalidwarning").val() != undefined) {
        $("#warningText").hide();
        return
    }

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
    $("#warningText").show();
    calendar.select(date);

    var _date = jsCalendar.tools.dateToString(date, "yyyy-MM-DD");
    var _disp = jsCalendar.tools.dateToString(date, "MMM DD, yyyy");
    $("#date").val(_date);
    $("#dateDisplay").val(_disp);
    $("#timeIn").removeAttr("disabled");
    $("#timeOut").removeAttr("disabled");
    $("#hour").removeAttr("disabled");
    $("#warningText").hide();
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
    setCountedHours();
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
    setCountedHours();
}

function setCountedHours() {

    var timeInSt = $("#timeIn").val();
    var timeOutSt = $("#timeOut").val();
    var timeInSchedSt = $("#scheduledTimeInInput").val();
    var timeOutSchedSt = $("#scheduledTimeOutInput").val();

    if (timeInSt === '' || timeOutSt === '')
        return;

    $("#counted-hour").val('');

    var timeIn = moment(timeInSt, "hh:mm tt");
    var timeOut = moment(timeOutSt, "HH:mm");
    var timeInSched = moment(timeInSchedSt, "hh:mm A");
    var timeOutSched = moment(timeOutSchedSt, "hh:mm A");

    if ((timeIn - timeInSched)/3600000 <= 0.25 ) {
        timeIn = timeInSched;
    }

    if (timeOut > timeOutSched) {
        timeOut = timeOutSched;
    }

    var hours = timeOut - timeIn;
    hours = hours/3600000;


    if (hours < 0) {
        hours = 24 - Math.abs(hours);
    }

    hours = Math.round(hours * 100) / 100;

    // Subtract break
    var employeeBreak;
    if ($('#break-type option:selected').val() === 'straight') {
        employeeBreak = 0;
    }
    else {
        employeeBreak = $('#employee_break').val();
    }
    hours -= employeeBreak;

    $("#counted-hour").val(hours);

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
    $("#scheduledTimeInHidden").val('');
    $("#scheduledTimeOutHidden").val('');
    $("#scheduledTimeInInput").val('');
    $("#scheduledTimeOutInput").val('');
    $("[name='outlier']").prop('checked', false);
    $("[name='authorized']").prop('checked', false);
    $("#remarks").text('');
    $("#hour").val('');
    $("#counted-hour").val('');

}

function mapDetails(json) {
console.log(json);
    $("#departmentNameDisplay").html(json.departmentName);
    $("#departmentIdDisplay").val(json.departmentId);
    $("[name='outlier'][value='" + json.outlier+ "']").prop('checked', true);

    $("#scheduledTimeInHidden").val(json.scheduledTimeIn);
    $("#scheduledTimeOutHidden").val(json.scheduledTimeOut);
    $("#employee-break").val(json.empBreak);

    if (json.break === 0) {
        $("#break-type").val('straight');
    } else {
        $("#break-type").attr('break');
    }

    $("#scheduledTimeInInput").val(json.scheduledTimeIn);
    $("#scheduledTimeOutInput").val(json.scheduledTimeOut);

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
