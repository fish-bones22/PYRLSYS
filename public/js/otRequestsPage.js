var table;
$(document).ready(function() {
    table = $("#otRequestTable").DataTable({
        "lengthChange": false,
        "info": false,
        "dom": "<t<'float-right'p>>"
    });
});


function searchEmployee(term) {
    var term = $("#searchBox").val();
    table.column(0).search(term);
    table.column(0).draw();
}

function filterDepartment() {
    var deptId = $("#dapartment").val();
    if (deptId == 0) {
        table.column(1).search('');
    }
    else {
        var text = $("#dapartment option[value='"+deptId+"']").text();
        table.column(1).search(text);
    }
    table.column(1).draw();

}
