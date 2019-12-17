var table;
$(function() {
    table = $("#dailyWorkingHoursTable").DataTable({
        "lengthChange": false,
        "info": false,
        "order": [[ 3, "asc" ], [1, "asc"]],
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
    filterStatus();
    $('#statusToggler').change(filterStatus);
});

function filterDepartment() {
    var deptId = $("#department").val();
    if (deptId == 0) {
        table.column(2).search('');
    }
    else {
        var text = $("#department option[value='"+deptId+"']").text();
        table.column(2).search(text);
    }
    table.column(2).draw();

}

function filterRecords() {
    var term = $("#searchBox").val();

    table.search(term);
    table.columns().draw();
}

function filterStatus() {
    if ($('#statusToggler').prop('checked')) {
        table.column(16).search('^Inactive$', true, false, true).draw();
    } else {
        table.column(16).search('^Active$', true, false, true).draw();
    }
}

function toggleMode() {
    var mode = $("input[name='mode']:checked").val();

    if (mode === 'daily') {
        $("#dailyRow").show();
        $("#monthlyRow").hide();
        $("#periodicRow").hide();
        $("#daterangeRow").hide();
    }
    else if (mode === 'periodic') {
        $("#periodicRow").show();
        $("#monthlyRow").hide();
        $("#dailyRow").hide();
        $("#daterangeRow").hide();
    }
    else if (mode === 'monthly') {
        $("#dailyRow").hide();
        $("#periodicRow").hide();
        $("#monthlyRow").show();
        $("#daterangeRow").hide();
    }
    else if (mode === 'daterange') {
        $("#dailyRow").hide();
        $("#periodicRow").hide();
        $("#monthlyRow").hide();
        $("#daterangeRow").show();
    }
    var rand =  Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
    table.search(rand);
    table.draw();

    $("#filterForm").submit();
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
