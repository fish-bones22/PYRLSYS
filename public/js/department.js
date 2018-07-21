(function() {

    $("#editModal").on("hidden.bs.modal", function () {
        resetViewModal();
    });

})();

function getDetails(self) {

    var id = $(self).data("id");
    var url = "department/getdetails/" + id;
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
    $("#descriptionDisplay").text(data.description);
}


function toggleEdit() {
    $(".display-toggle").toggle();
    $(".btn-toggle").toggle();
}

function resetViewModal() {
    $(self).data("id", "");
    $("#nameEdit").attr("value", "");
    $("#nameDisplay").text("Retrieving information...");
    $("#descriptionEdit").text("");
    $("#descriptionDisplay").text("Retrieving information...");
    $(".display-toggle").not("div").hide();
}
