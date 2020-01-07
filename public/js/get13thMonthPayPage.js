var table;
var idsToCheck = [];
var processInterval;
var procLock = false;
var nextInd = 0;
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
        "select": {
            "style":    'multi',
            "selector": 'td:first-child'
        },
        "buttons": [
            {
                filename: getFileName,
                extend: 'excelHtml5',
                exportOptions: {
                    rows: 'tr.include-report'
                }
            },
            {
                filename: getFileName,
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'LETTER',
                exportOptions: {
                    rows: 'tr.include-report'
                }
            }
        ],
        "dom": "<t<'float-left'l><'float-right'p>>"
    })
    // Row is selected
    .on('select', function(e, dt, type, indexes) {
        toggleCheck(indexes, true);
    })
    // Row is deselected
    .on('deselect', function(e, dt, type, indexes) {
        toggleCheck(indexes, false);
    })
    .on('draw', function() {
        $('.employee-check[value="on"]').each(function() {
            var empId = $(this).data('employee-id');
            if ($('#amount-' + empId + '[data-new="true"]').length <= 0) return;
            $('#amount-display-' + empId).text($('#amount-' + empId).val());
            $('#amount-display-' + empId).closest('td').css('background-color', '#fff8d1');
        });
    });

    $('#selectAll').click(function () {
        var checked = $(this).data('checked');
        if (checked) {
            table.rows().select();
        } else {
            table.rows().deselect();
        }
        $(this).data('checked', !checked);
    });

    if ( $('#statusToggler').length > 0) {
        filterStatus();
        $('#statusToggler').change(filterStatus);
    }

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
function saveAsExcel() {
    table.button(0).trigger();
}
function saveAsPDF() {
    table.button(1).trigger();
}

function toggleCheck(indexes, on) {
    if (Array.isArray(indexes)) {
        for (var i = 0; i < indexes.length; i++) {
            var rowDom = $('.row-' + indexes[i]);
            var employeeId = rowDom.data('employee-id');
            rowDom.find('input[type="checkbox"]').prop('checked', on);
            $('#include-' + employeeId).val(on ? 'on' : '');
        }
    }
    else {
        $('.row-' + indexes).find('input[type="checkbox"]').prop('checked', on);
        var rowDom = $('.row-' + indexes);
        var employeeId = rowDom.data('employee-id');
        rowDom.find('input[type="checkbox"]').prop('checked', on);
        $('#include-' + employeeId).val(on ? 'on' : '');
    }
}

function generate() {
    // Get checked rows
    $('.employee-check[value="on"]').each(function() {
        var id = $(this).data('employee-id');
        if (idsToCheck.indexOf(id) >= 0) return true;
        idsToCheck.push(id);
    });

    if (idsToCheck.length <= 0) {
        return;
    }
    // Start process
    $('#btnGenerate').text('Please wait...');
    $('#btnGenerate').attr('disabled', '');
    $('.amount-display[data-new="true"]').text('');
    $('.amount-display[data-new="true"]').closest('td').css('background-color', 'unset');
    $('.amount-input[data-new="true"]').each(function() {
        var oldVal = $(this).data('old');
        $(this).val(oldVal);
    })
    procLock = false;
    nextInd = 0;
    processInterval = window.setInterval(function() {
        if (procLock) return;

        procLock = true;

        if (idsToCheck.length <= nextInd) {
            window.clearInterval(processInterval);
            $('#btnGenerate').text('Generate');
            $('#btnGenerate').removeAttr('disabled');
            $('#btnSave').show();
        }

        var id = idsToCheck[nextInd];

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
                console.log(res);
                $('#amount-display-'+ id).text(res.total);
                $('#amount-display-'+ id).closest('td').css('background-color', '#fff8d1');
                $('#amount-display-'+ id).attr('data-new', 'true');
                $('#amount-'+ id).attr('data-old', $('#amount-'+ id).val());
                $('#amount-'+ id).val(res.total);
                $('#amount-'+ id).attr('data-new', 'true');
                nextInd++;
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
    $('#startDate').val(yearFrom + '-' + monthFrom + '-01');
    $('#endDate').val(yearTo + '-' + monthTo + '-01');
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

function getFileName() {
    return $("#title").text().replaceAll(" ", "-").replaceAll("---", "-").toLowerCase() + "-" + Date.now();
}
