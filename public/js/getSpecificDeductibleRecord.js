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
    if ( $('#statusToggler').length > 0) {
        filterStatus();
        $('#statusToggler').change(filterStatus);
    }
});

function filterDepartment() {
    var term = $("#department").val();
    console.log(term);
    table.column(4).search(term);
    table.column(4).draw();
}

function filterEmployees() {
    var term = $("#searchBox").val();
    table.search(term);
    table.draw();
}

function filterStatus() {
    var colInd = table.columns().header().length - 1;
    if ($('#statusToggler').prop('checked')) {
        table.column(colInd).search('Inactive').draw();
    } else {
        table.column(colInd).search('Active').draw();
    }
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


