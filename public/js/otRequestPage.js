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

    $(".employee-list").html("");
    resetEmployees();

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

    if (index == 1) {
        return;
    }

    for (var i = 1; i < index; i++) {
        $(".employee-" + i).remove();
    }
}
