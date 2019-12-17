var table;
$(function() {
    table = $("#payrollMasterTable").DataTable({
        "info": false,
        "ordering": true,
        "pageLength": 50,
        "columnDefs":[{
            "targets":0,
            "orderable": false
        }],
        "dom": "<t<'float-left'l><'float-right'p>>"
    });
    if ( $('#statusToggler').length > 0) {
        filterStatus();
        $('#statusToggler').change(filterStatus);
    }

    $('.employee-check').change(function() {
        if (!$(this).prop('checked')) {
            $('#selectAll').prop('checked', false);
        }
    });
});

function filterDepartment() {
    var term = $("#department").val();
    table.column(3).search(term);
    table.column(3).draw();
}

function filterEmployees() {
    var term = $("#searchBox").val();
    table.column(2).search(term);
    table.column(2).draw();
}

function filterStatus() {
    var colInd = table.columns().header().length - 1;
    if ($('#statusToggler').prop('checked')) {
        table.column(colInd).search('Inactive').draw();
    } else {
        table.column(colInd).search('Active').draw();
    }
}

function toggleSelectAll() {
    if ($('#selectAll').prop('checked')) {
        $('.employee-check').each(function() { $(this).prop('checked', true); });
    } else {
        $('.employee-check').each(function() { $(this).prop('checked', false); });
    }
}
