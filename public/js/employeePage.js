var table;
$(function() {
    table = $("#transferHistoryTable").DataTable({
        "lengthChange": false,
        "info": false,
        "order": false,//[[ 3, "asc" ], [1, "asc"]],
        "dom": "<t<'float-right'p>>",
        "paging": false,
        "buttons": [
            {
                filename: getFileName,
                extend: 'excelHtml5',
            },
            {
                filename: getFileName,
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'LETTER'
            }
        ]
    });
});

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

function updateTimeInTimeOutOnModal(self) {

    var id = $(self).val();
    if (id == 0) {
        $("#timeInModal").val('');
        $("#timeOutModal").val('');
        return;
    }
    var url = "/category/getsubvalues/" + id;
    $.ajax({
        url: url,
        contentType: 'text/plain',
        dataType:"json",
        success: function(result) {
            mapSubvaluesModal(result);
        }
    });

}

function mapSubvalues(json) {
    $("#timeIn").val(json.subvalue1);
    $("#timeOut").val(json.subvalue2);
}

function mapSubvaluesModal(json) {
    $("#timeInModal").val(json.subvalue1);
    $("#timeOutModal").val(json.subvalue2);
}

function saveAsExcel() {
    table.button(0).trigger();
}
function saveAsPDF() {
    table.button(1).trigger();
}

function getFileName() {
    return $("#title").text().replaceAll(" ", "-").replaceAll("---", "-").toLowerCase() + "-" + Date.now();
}
