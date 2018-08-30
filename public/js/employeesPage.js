var table;
var table2;
$(function() {

    table = $("#employeesTable").DataTable({
        "lengthChange": false,
        "info": false,
        "dom": "<t<'float-right'p>>"
    });
    table2 = $("#employeesSummaryTable").DataTable({
        "lengthChange": false,
        "info": false,
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

function filterEmployees() {
    var term = $("#searchBox").val();
    table.column(1).search(term);
    table.column(1).draw();
}


function getFileName() {
    return $("#title").text().replaceAll(" ", "-").replaceAll("---", "-").toLowerCase() + "-" + Date.now();
}


function saveAsExcel() {
    table2.button(0).trigger();
}
function saveAsPDF() {
    table2.button(1).trigger();
}
