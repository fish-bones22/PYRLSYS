$(document).ready(function() {

    $("input[type='time']").not("[readonly]").timepicki();

});

function getEmployeesOnDepartment() {

    var dept = $("#department").val();

    if (dept === '') {
        $(".employee-list").html("");
        resetEmployees();
        return;
    }

    var url = "getemployees/" + dept;
    var token = $("input[name='_token']").val();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        type: 'post',
        url: url,
        contentType: 'text/plain',
        dataType:"json",
        success: function(result) {
            mapResults(result);
        },
        error: function() {
            console.log('error');
        }
    });
}

function mapResults(json) {

    resetEmployees();
    $(".employee-list").removeAttr("disabled");
    $(".employee-list").html("");

    $(".employee-list").append($("<option>"));
    for (var i = 0; i < json.length; i++) {
        var option = $("<option>")
        .attr("value", json[i].id)
        .text(json[i].name);

        $(".employee-list").append(option);
    }

}


function resetEmployees() {
    var index = $("#employee-index").val();

    $(".employee-0 input, .employee-0 select").val('');
    $(".employee-0 select").attr("disabled", "disabled");

    if (index == 1) {
        return;
    }

    for (var i = 1; i < index; i++) {
        $(".employee-" + i).remove();
    }
    var index = $("#employee-index").val(1);
}


function getEmployeeDetails(self) {

    var id = $(self).val();

    resetEmployeeDetails(self);
    if (id === '') {
        return;
    }

    var url = "/employee/getbasic/" + id;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        type: 'post',
        url: url,
        contentType: 'text/plain',
        dataType:"json",
        success: function(result) {
            mapResultsForEmployee(self, result);
            if ((result != undefined || result != null)) {
                checkEmployeeOtRequest(self, result.id);
            }

        },
        error: function() {
            console.log('error');
        }
    });
}

function checkEmployeeOtRequest(self, id) {
    var date = $("#date").val();

    if (date === '')
        return;

    url = "checkemployeerecord/" + id + "/" + date;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        type: 'post',
        url: url,
        contentType: 'text/plain',
        dataType:"json",
        success: function(result) {
            console.log(result);
            mapResultsForRecords(self, result);
        },
        error: function() {
            console.log('No OT Request record');
        }
    });
}

function mapResultsForEmployee(self, json) {

    var row = $(self).closest('.row');
    row.find(".name").val(json.fullName);
    row.find(".department").val(json.departmentValue);
    row.find(".employee-id").text(json.employeeId);
    row.find(".timecard").text(json.timecard);

}


function mapResultsForRecords(self, json) {

    var row = $(self).closest('.row');
    row.find(".allowed-hours").val(json.allowedHours);
    row.find(".start-time").setTime(json.startTime);
    row.find(".end-time").setTime(json.endTime);
    row.find(".reason").val(json.reason);

}

function resetEmployeeDetails(self) {

    var row = $(self).closest('.row');
    row.find(".id").val('');
    row.find(".department").val('');
    row.find(".employee-id").html("<i class='text-muted small'>ID</i>");
    row.find(".timecard").html("<i class='text-muted small'>Time card</i>");
    row.find(".allowed-hours").val('');
    row.find(".start-time").val('');
    row.find(".end-time").val('');
    row.find(".end-time").text('');
    row.find(".reason").text('');

}


function setEndTime(self) {

    var row = $(self).closest('.row');

    var allowedHours = row.find(".allowed-hours").val();
    if (allowedHours == '') return;

    var startTime = row.find('.start-time').val();
    if (startTime == '') return;

    var time = moment(startTime, "hh:mm tt");
    time.add(allowedHours, 'hours');

    var endTime = time.format("HH:mm")
    row.find('.end-time').setTime(endTime);

}


function updateEmployeeRecords() {
    $(document).find('.employee-list').each(function() {
        if ("createEvent" in document) {
            var evt = document.createEvent("HTMLEvents");
            evt.initEvent("change", false, true);
            $(this)[0].dispatchEvent(evt);
        }
        else
            $(this)[0].fireEvent("onchange");
    });
}
