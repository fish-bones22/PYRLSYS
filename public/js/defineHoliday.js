$(document).ready(function() {

    var calendar = jsCalendar.new("#calendar");
    //console.log(calendar);
    calendar.onDateClick(function(e, date) {
        selectDate(calendar, date);
        getRecord();
    })

});

function selectDate(calendar, date) {

    calendar.clearSelected();
    $("#warningText").show();
    calendar.select(date);

    var _date = jsCalendar.tools.dateToString(date, "yyyy-MM-DD");
    var _disp = jsCalendar.tools.dateToString(date, "MMM DD, yyyy");
    $("#date").val(_date);
    $("#dateDisplay").val(_disp);
    $("#warningText").hide();
}
