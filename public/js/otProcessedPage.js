var table;
$(document).ready(function() {
    table = $("#otRequestTable").DataTable({
        "lengthChange": false,
        "info": false,
        "dom": "<t<'float-right'p>>",
        "order": [[ 7, "asc" ]],
        "columnDefs": [{
            "targets":8,
            "orderable": false
        }],
        "buttons": [
            {
                filename: getFileName,
                extend: 'excelHtml5',
                exportOptions: {
                    "columns": 'th:not(:last-child)'
                }
            },
            {
                filename: getFileName,
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'LETTER',
                exportOptions: {
                    "columns": 'th:not(:last-child)'
                }
            }
        ]
    });
});


function searchEmployee(term) {
    var term = $("#searchBox").val();
    table.column(0).search(term);
    table.column(0).draw();
}

function filterDepartment() {
    var deptId = $("#department").val();
    if (deptId == 0) {
        table.column(1).search('');
    }
    else {
        var text = $("#department option[value='"+deptId+"']").text();
        table.column(1).search(text);
    }
    table.column(1).draw();
}

function saveAsExcel() {
    table.button(0).trigger();
}
function saveAsPDF() {
    table.button(1).trigger();
}

function getFileName() {
    var text = $("#title").clone()    //clone the element
    .children() //select all the children
    .remove()   //remove all the children
    .end()  //again go back to selected element
    .text();
    return text.trim().replaceAll(" ", "-").replaceAll("---", "-").toLowerCase() + "-" + Date.now();
}
