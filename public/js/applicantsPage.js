var table;
$(document).ready(function() {
    table = $("#dataTable").DataTable({
        "lengthChange": false,
        "info": false,
        "dom": "<t<'float-right'p>>"
    });
});

function filterStatus() {
    var term = $("#status").val();

    table.column(3).search(term);
    table.column(3).draw();
}

function filterApplicants() {
    var term = $("#searchBox").val();
    table.column(0).search(term);
    table.column(0).draw();
}
