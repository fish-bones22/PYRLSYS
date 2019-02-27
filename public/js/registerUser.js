$(document).ready(function() {
    // Department Access Select All CB
    $("#cb-department-access-all").change(function() {
        if ($(this).prop("checked")) {
            $(".cb-department-access").prop("checked", true);
        } else {
            $(".cb-department-access").prop("checked", false);
        }
    });
    // User Access Select All CB
    $("#cb-user-access-all").change(function() {
        if ($(this).prop("checked")) {
            $(".cb-user-access").prop("checked", true);
        } else {
            $(".cb-user-access").prop("checked", false);
        }
    });
    // Department Access uncheck Select All CB if any dept access CB is unchecked
    $(".cb-department-access").change(function() {
        if (!$(this).prop("checked")) {
            $("#cb-department-access-all").prop("checked", false);
        }
    });
    // User Access uncheck Select All CB if any dept access CB is unchecked
    $(".cb-user-access").change(function() {
        if (!$(this).prop("checked")) {
            $("#cb-user-access-all").prop("checked", false);
        }
    });
})
