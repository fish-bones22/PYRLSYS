var table;
$(function() {
    table = $("#usersTable").DataTable({
        "lengthChange": false,
        "info": false,
        "dom": "<t<'float-right'p>>"
    });
});

function filterUsers() {
    var term = $("#searchBox").val();
    table.column(0).search(term);
    table.column(0).draw();
}

