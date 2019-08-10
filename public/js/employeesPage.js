var table;
var table2;
$(function() {

    table = $("#employeesTable").DataTable({
        "info": false,
        "ordering": true,
        "pageLength": 50,
        "columnDefs":[{
            "targets":3,
            "orderable": false
        }],
        "dom": "<t<'float-left'l><'float-right'p>>"
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

function filterDepartment() {
    var term = $("#department").val();
    table.column(2).search(term);
    table.column(2).draw();
}

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
