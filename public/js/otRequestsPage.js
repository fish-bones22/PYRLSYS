var table;
$(document).ready(function() {
    table = $("#otRequestTable").DataTable({
        "lengthChange": false,
        "info": false,
        "dom": "<t<'float-right'p>>",
        "order": [[ 1, "asc" ]],
        "columnDefs": [{
            "targets": [0, 8],
            "orderable": false
        }]
    });

    checkBatchApproval();
    $("#cb-batch-approval-all").change(function() {
        if ($(this).prop('checked')) {
            $(".cb-batch-approval").prop('checked', true);
        }
        else {
            $(".cb-batch-approval").prop('checked', false);
        }
        checkBatchApproval();
    });

    $(".cb-batch-approval").change(function() {
        if (!$(this).prop('checked')) {
            $("#cb-batch-approval-all").prop('checked', false);
        }
        checkBatchApproval();
    });
});

function checkBatchApproval() {
    var hasChecked = false;
    $(".cb-batch-approval").each(function() {
        if ($(this).prop('checked')) {
            hasChecked = true;
            return;
        }
    });
    if (hasChecked) {
        $("#btn-batch-approve").show();
    }
    else {
        $("#btn-batch-approve").hide();
    }
}

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

