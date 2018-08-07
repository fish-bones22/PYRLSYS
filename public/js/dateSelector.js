$(document).ready(function() {

    var calendar = jsCalendar.new("#calendar");
    //console.log(calendar);
    calendar.onDateClick(function(e, date) {
        selectDate(calendar, date);
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
    console.log($("#date").val());
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

    // if (hours < 0)
    //     return;

    hours = Math.abs(hours/3600000);
    hours = Math.round(hours * 100) / 100;

    $("#hour").val(hours);
}

function resetOutliers() {
    $('input[type=\'radio\']').prop('checked', false);
}
