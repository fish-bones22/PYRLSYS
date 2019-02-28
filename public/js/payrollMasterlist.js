var table;
$(function() {
    table = $("#payrollMasterTable").DataTable({
        "lengthChange": false,
        "info": false,
        "columnDefs": [{
            "targets": 2,
            "orderable": false
        }],
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

function searchTable() {
    var term  =$("#searchBox").val();
    table.column(0).search(term);
    table.column(0).draw();
}


function filterDepartment() {
    var term  =$("#department").val();
    table.column(1).search(term);
    table.column(1).draw();
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
