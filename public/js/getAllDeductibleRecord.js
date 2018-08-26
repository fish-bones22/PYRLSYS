var table;
$(function() {
    table = $("#deductiblesTable").DataTable({
        "lengthChange": false,
        "info": false,
        "ordering": false,
        "dom": "<t<'float-right'p>>",
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


function saveAsExcel() {
    table.button(0).trigger();
}
function saveAsPDF() {
    table.button(1).trigger();
}

function getFileName() {
    return $("#title").text().replaceAll(" ", "-").replaceAll("---", "-").toLowerCase() + "-" + Date.now();
}


