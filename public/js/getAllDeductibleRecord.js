var table;
var employees = [];
var isQuerying = false;
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

}

function filterEmployees() {
    var term = $("#searchBox").val();
    table.search(term);
    table.draw();
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

function getFileName() {
    return $("#title").text().replaceAll(" ", "-").replaceAll("---", "-").toLowerCase() + "-" + Date.now();
}

function autogenerate() {
    $('#generation-notification').hide();
    $('#generateDialogModal').modal({
        backdrop: 'static',
        keyboard: false,
        show: true
    });
}

function startGeneration() {
    $('.btn-generate').attr('disabled', true);
    getAllEmployees();
    $('#generation-notification').show();
}

function getAllEmployees() {

    $.ajax({
        url: '/deductibles/getallemployees_ajax',
        dataType: 'json',
        method: 'get',
        success: function(result) {
            employees = result;
            $('#generate-current').text('0');
            $('#generate-total').text(result.length);
            var inter = setInterval(function() {
                if (isQuerying) return;
                if (employees === null || employees.length <= 0) {
                    clearInterval(inter);
                    window.location.reload();
                };
                generateDeductibles();
            }, 100);
        },
        error: function(result) {
            console.error(result);
        }
    });
}

function generateDeductibles() {

    isQuerying = true;
    var employeeId = employees.pop();
    $.ajax({
        url: '/deductibles/autogenerate_ajax',
        data: {
            'id': employeeId,
            'override': $('#overrideValues').prop('checked'),
            'date': $('#date').val()
        },
        method: 'get',
        contentType: 'application/json',
        dataType: 'json',
        success: function(result) {
            console.log(result);
            if (result) {
                $('#generate-current').text($('#generate-current').text()*1+1);
            }
            isQuerying = false;
        },
        error: function(result) {
            console.error(result);
            isQuerying = false;
        }
    })
}
