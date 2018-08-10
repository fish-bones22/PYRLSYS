var table;
$(function() {
    table = $("#employeesTable").DataTable({
        "lengthChange": false,
        "info": false,
        "dom": "<t<'float-right'p>>"
    });
});

function filterEmployees() {
    var term = $("#searchBox").val();
    table.column(1).search(term);
    table.column(1).draw();
}
