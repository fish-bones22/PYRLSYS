var table;
$(function() {
    table = $("#dailyWorkingHoursTable").DataTable({
        "lengthChange": false,
        "info": false,
        "order": [[ 3, "asc" ], [1, "asc"]],
        "dom": "<t<'float-right'p>>"
    });
});

function filterDepartment() {
    var deptId = $("#dapartment").val();
    if (deptId == 0) {
        table.column(2).search('');
    }
    else {
        var text = $("#dapartment option[value='"+deptId+"']").text();
        table.column(2).search(text);
    }
    table.column(2).draw();

}

function filterRecords() {
    var term = $("#searchBox").val();

    table.search(term);
    table.columns().draw();
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
