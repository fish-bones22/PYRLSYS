$(document).ready(function() {

    $("#dateFrom").change(function() {
        var dateFrom = $(this).val();
        console.log(dateFrom);
        $("#dateTo").attr('min', dateFrom);
    });

});
