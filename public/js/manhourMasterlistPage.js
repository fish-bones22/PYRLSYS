var table;

$(function() {
    table = $("#masterListTable").DataTable({
        "lengthChange": false,
        "info": false,
        "dom": "<t<'float-right'p>>"
    });
});

function filterDepartment() {
    var term = $("#department").val();
    table.column(2).search(term);
    table.column(2).draw();
}

function filterEmployees() {
    var term = $("#searchBox").val();
    table.column(1).search(term);
    table.column(1).draw();
}
