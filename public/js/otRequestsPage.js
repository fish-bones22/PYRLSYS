var table;
$(document).ready(function() {
    table = $("#otRequestTable").DataTable({
        "lengthChange": false,
        "info": false,
        "dom": "<t<'float-right'p>>",
        "columnDefs": [{
            "targets":7,
            "orderable": false
        }]
    });
});


function searchEmployee(term) {
    var term = $("#searchBox").val();
    table.column(0).search(term);
    table.column(0).draw();
}

function filterDepartment() {
    var deptId = $("#department").val();
    if (deptId == 0) {
        table.column(1).search('');
    }
    else {
        var text = $("#department option[value='"+deptId+"']").text();
        table.column(1).search(text);
    }
    table.column(1).draw();

}
