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

    table.column(3).search('^Active$', true, false, true).draw();
    table2.column(21).search('^Active$', true, false, true).draw();
    $('#toggleInactive').click(function() {
        $('#deleteInactive').toggle();
        $('#deleteAll').toggle();
        filterInactive();
    })
});

function filterDepartment() {
    var term = $("#department").val();
    table.column(2).search(term);
    table.column(2).draw();
    table2.column(16).search(term);
    table2.column(16).draw();
}

function filterEmployees() {
    var term = $("#searchBox").val();
    table.column(1).search(term);
    table.column(1).draw();
}

function filterInactive() {
    if ($('#toggleInactive').text() === 'Inactive Employees') {
        $('#toggleInactive').text('Active Employees')
        table.column(3).search('^Inactive$', true, false, true);
        table2.column(21).search('^Inactive$', true, false, true);
    } else {
        $('#toggleInactive').text('Inactive Employees')
        table.column(3).search('^Active$', true, false, true);
        table2.column(21).search('^Active$', true, false, true);
    }
    table.column(3).draw();
    table2.column(21).draw();
}


function filterTIN() {
    var option = $('#tinFilter').val();
    if (option === 'all') {
        table.column(4).search('');
        table2.column(23).search('');
    } else if (option === 'tin') {
        table.column(4).search('^[0-9 \-]+$', true, false, true);
        table2.column(23).search('^[0-9 \-]+$', true, false, true);
    } else if (option === 'notin') {
        table.column(4).search('^$', true, false, true);
        table2.column(23).search('^$', true, false, true);
    }
    table.column(4).draw();
    table2.column(23).draw();
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
