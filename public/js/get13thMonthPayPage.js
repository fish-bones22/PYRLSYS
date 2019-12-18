var table;
var idsToCheck = [];
var processInterval;
var procLock = false;
$(function() {
    table = $("#payrollMasterTable").DataTable({
        "info": false,
        "ordering": true,
        "pageLength": 50,
        "columnDefs":[{
            "targets":0,
            "orderable": false
        }],
        "order": [[1,'asc']],
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

    // Set start and end date on ready
    if ($('#startDate').val () === '' || $('#endDate').val () === '' ) {
        getDateRange();
    }
    $('#monthFrom, #yearFrom').on('change', autoSetDateRange);
    $('#monthFrom, #monthTo, #yearFrom, #yearTo').on('change', getDateRange);

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
        table.column(colInd).search('^Inactive$', true, false, true).draw();
    } else {
        table.column(colInd).search('^Active$', true, false, true).draw();
    }
}

function toggleSelectAll() {
    if ($('#selectAll').prop('checked')) {
        $('.employee-check').each(function() { $(this).prop('checked', true); });
    } else {
        $('.employee-check').each(function() { $(this).prop('checked', false); });
    }
}

function generate() {
    // Get checked rows
    $('.employee-check:checked').each(function() {
        idsToCheck.push($(this).data('employee-id'));
    });

    if (idsToCheck.length <= 0) {
        return;
    }
    // Start process
    $('#btnGenerate').text('Please wait...');
    $('#btnGenerate').attr('disabled', '');
    processInterval = window.setInterval(function() {
        if (procLock) return;

        procLock = true;

        if (idsToCheck.length <= 0) {
            window.clearInterval(processInterval);
            $('#btnGenerate').text('Generate');
            $('#btnGenerate').removeAttr('disabled');
            $('#btnSave').show();
        }

        var id = idsToCheck.pop();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/payroll/ajax_getamount',
            type: 'POST',
            data: {
                id: id,
                from: '2019-01-01',
                to: '2019-12-01'
            },
            success: function(res) {
                $('#amount-display-'+ id).text(res.total);
                $('#amount-display-'+ id).closest('td').css('background-color', '#fff8d1');
                $('#amount-'+ id).val(res.total);
                procLock = false;
            }
        });

    }, 500);
}

function getAmount(id) {

    var monthFrom = $('#monthFrom').val();
    var monthTo = $('#monthTo').val();
    var yearFrom = $('#yearFrom').val();
    var yearTo = $('#yearTo').val();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: '/payroll/ajax_getamount',
        type: 'POST',
        data: {
            id: id,
            from: yearFrom + '-01-' + monthFrom,
            to: yearTo + '-01-' + monthTo
        },
        success: function(res) {
            console.log(res);
        }
    });
}

function getDateRange() {
    var monthFrom = $('#monthFrom').val();
    var monthTo = $('#monthTo').val();
    var yearFrom = $('#yearFrom').val();
    var yearTo = $('#yearTo').val();
    $('#startDate').val(yearFrom + monthFrom + '-01');
    $('#endDate').val(yearTo + monthTo + '-01');
}


function autoSetDateRange() {

    var monthFrom = $('#monthFrom').val();
    var yearFrom = $('#yearFrom').val();
    if (monthFrom === '') return;
    if (yearFrom === '') return;

    var dateStart = yearFrom + '-' + monthFrom + '-01';
    var objDateStart = new Date(dateStart);

    var objDateEnd = new Date(objDateStart.setMonth(objDateStart.getMonth() + 11));
    var month = objDateEnd.getMonth() + 1;
    $('#monthTo').val(month <= 9 ? '0' + month : month + '');
    $('#yearTo').val(objDateEnd.getFullYear());
}
