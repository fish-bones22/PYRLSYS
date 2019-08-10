var table;

$(function() {
    table = $("#masterListTable").DataTable({
        "info": false,
        "ordering": true,
        "pageLength": 50,
        "dom": "<t<'float-left'l><'float-right'p>>",
        "columnDefs": [
            {"targets": 3, "orderable": false}
        ]
    });
});

function filterDepartment() {
    var term = $("#department").val();
    console.log(term);
    table.column(2).search(term);
    table.column(2).draw();
}

function filterEmployees() {
    var term = $("#searchBox").val();
    table.column(1).search(term);
    table.column(1).draw();
}
