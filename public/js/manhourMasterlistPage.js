var table;

$(function() {
    table = $("#masterListTable").DataTable({
        "lengthChange": false,
        "info": false,
        "ordering": true,
        "dom": "<t<'float-right'p>>",
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
