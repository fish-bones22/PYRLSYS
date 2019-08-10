function changeMiddleToMaiden() {

    var civStat = $("#civilStatus").val();
    var sex = $("#sex").val();

    if (sex === "f" && (civStat != "Single" && civStat != "Separated")){
        $("#middleNameLabel").text('Maiden Name:');
        return;
    }
    $("#middleNameLabel").text('Middle Name:');

}
