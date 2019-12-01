var table;
$(function() {
    table = $("#payrollMasterTable").DataTable({
        "info": false,
        "columnDefs": [{
            "targets": 2,
            "orderable": false
        }],
        "pageLength": 50,
        "dom": "<t<'float-left'l><'float-right'p>>",
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

function searchTable() {
    var term  =$("#searchBox").val();
    table.column(0).search(term);
    table.column(0).draw();
}


function filterDepartment() {
    var term  =$("#department").val();
    table.column(4).search(term);
    table.column(4).draw();
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
