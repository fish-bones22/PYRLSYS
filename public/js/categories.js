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

function getDetails(self) {

    var id = $(self).data("id");
    var url = "/category/getdetails/" + id;
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

    $("#subValue3Edit").attr("value", data.subvalue3);
    $("#subValue2Edit").attr("value", data.subvalue2);
    $("#subValue1Edit").attr("value", data.subvalue1);

    if ($("#key").val() === 'department') {
        var sub1 = moment(data.subvalue1, 'HH:mm');
        var sub2 = moment(data.subvalue2, 'HH:mm');
        data.subvalue1 = sub1.format("hh:mm A");
        data.subvalue2 = sub2.format("hh:mm A");
    }

    $("#subValue1Display").text(data.subvalue1);
    $("#subValue2Display").text(data.subvalue2);
    $("#subValue3Display").text(data.subvalue3);
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

    $(".display-toggle").not("div").hide();
    $("div.display-toggle").show();
    $(".view-toggle").hide();
    $(".edit-toggle").show();
}
