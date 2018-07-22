(function() {

    $("#editModal").on("hidden.bs.modal", function () {
        resetViewModal();
    });

})();

function getDetails(self) {

    var id = $(self).data("id");
    var url = "category/getdetails/" + id;
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
    $("div.display-toggle").show();
    $(".btn-toggle").toggle();
}
