$(document).ready(function() {
    $("#otRequestTable").DataTable({
        "lengthChange": false,
        "info": false,
        "dom": "<t<'float-right'p>>"
    });
});


function searchEmployee(term) {
    table.column(0).search(term);
    table.column(0).draw();
}


function searchDepartment(term) {
    table.column(0).search(term);
    table.column(0).draw();
}
