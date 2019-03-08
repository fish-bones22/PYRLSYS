var table;
(function() {

    $("#editModal").on("hidden.bs.modal", function () {
        resetViewModal();
    });

    table = $("#departmentsTable").DataTable({
        "lengthChange": false,
        "info": false,
        "dom": "<t<'float-right'p>>"
    });

    // $("#subvalue1Add").timepicki();
    // $("#subvalue2Add").timepicki();

})();

function filterDepartment() {
    var term = $("#searchBox").val();
    table.column(0).search(term);
    table.column(0).draw();
}

function getDetails(self, key) {

    var id = $(self).data("id");
    var url = "/category/getdetails/" + key + "/" + id;
    $("#idEdit").val(id);
    $.ajax({
        url: url,
        contentType: 'text/plain',
        dataType:"json",
        success: function(result) {
            mapDetails(result);
        }
    });
}

function mapDetails(data) {
    $("#nameEdit").attr("value", data.name);
    $("#nameDisplay").text(data.name);
    $("#descriptionEdit").text(data.description);
    if (data.description === '') {
        console.log(data.description);
        $("#descriptionDisplay").html("<i class='text-muted'>No description</i>");
    }
    else {
        $("#descriptionDisplay").text(data.description);
    }

    $("#subValue5Edit").attr("value", data.subvalue5);
    $("#subValue4Edit").attr("value", data.subvalue4);
    $("#subValue3Edit").attr("value", data.subvalue3);
    $("#subValue2Edit").attr("value", data.subvalue2);
    $("#subValue1Edit").attr("value", data.subvalue1);

    if ($("#key").val() === 'department') {
        var sub1 = moment(data.subvalue1, 'HH:mm');
        var sub2 = moment(data.subvalue2, 'HH:mm');
        var sub4 = moment(data.subvalue4, 'YYYY-MM-DD');
        var sub5 = moment(data.subvalue5, 'YYYY-MM-DD');
        data.subvalue1 = sub1.format("hh:mm A");
        data.subvalue2 = sub2.format("hh:mm A");
        if (sub4.isValid()) {
            data.subvalue4 = sub4.format('MM/DD/YYYY');
        }
        if (sub5.isValid()) {
            data.subvalue5 = sub5.format('MM/DD/YYYY');
        }
    }

    $("#subValue1Display").text(data.subvalue1);
    $("#subValue2Display").text(data.subvalue2);
    $("#subValue3Display").text(data.subvalue3);
    $("#subValue4Display").text(data.subvalue4 != null ? data.subvalue4 : 'None');
    $("#subValue5Display").text(data.subvalue5 != null ? data.subvalue5 : 'None');
}


function toggleEdit() {
    $(".display-toggle").toggle();
    $(".view-toggle").show();
    $(".edit-toggle").hide();
}

function resetViewModal() {
    $(self).data("id", "");
    $("#nameEdit").attr("value", "");
    $("#nameDisplay").text("Retrieving information...");
    $("#descriptionEdit").text("");
    $("#descriptionDisplay").text("Retrieving information...");
    $("#subValue1Edit").attr("value", "");
    $("#subValue1Display").text("Retrieving information...");
    $("#subValue2Edit").attr("value", "");
    $("#subValue2Display").text("Retrieving information...");
    $("#subValue3Edit").attr("value", "");
    $("#subValue3Display").text("Retrieving information...");
    $("#subValue4Edit").attr("value", "");
    $("#subValue4Display").text("Retrieving information...");
    $("#subValue5Edit").attr("value", "");
    $("#subValue5Display").text("Retrieving information...");

    $(".display-toggle").not("div").hide();
    $("div.display-toggle").show();
    $(".view-toggle").hide();
    $(".edit-toggle").show();
}
