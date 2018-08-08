function updateTimeInTimeOut(self) {

    var id = $(self).val();
    if (id == 0) {
        $("#timeIn").val('');
        $("#timeOut").val('');
        return;
    }
    var url = "/category/getsubvalues/" + id;
    $.ajax({
        url: url,
        contentType: 'text/plain',
        dataType:"json",
        success: function(result) {
            mapSubvalues(result);
        }
    });

}

function mapSubvalues(json) {
    $("#timeIn").val(json.subvalue1);
    $("#timeOut").val(json.subvalue2);
}
