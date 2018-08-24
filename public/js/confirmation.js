
$(document).ready(function () {

    // $('form').one('submit', function() {
    //     $(this).find('[type="submit"]').attr('disabled', 'disabled');
    // });

    var condition = false;
    $('[data-confirm]').click(function (e) {
        var self = this;
        if (condition) {
            condition = false;
            return true;
        }

        var isOkay = true;

        $("input, select").each(function () {
            if ($(this).prop("validity").valid == false) {
                isOkay = false;
            }
        });

        e.preventDefault();

        if ($(self).attr("id") === "Delete") {
            isOkay = true;
        }

        if (!isOkay) {
            condition = true;
            $(self).trigger("click");
            return false;
        }

        dialog(
             $(self).attr("data-confirm"),
             function () {
                 condition = true;

                 if ($(self).attr("id") === "Delete") {
                     $("[required]").removeAttr("required");
                 }

                 $(self).trigger("click");
                 return true;
             },
             function () {
                 e.preventDefault();
             }
         );
    });
});

function dialog(message, yesCallback, noCallback) {
    $('#confirmationModal').modal("show");
    $('#dialog-modal .confirmationTitle').text(message);
    $("#confirmationModal .confirmationAction").text(message);
    $('.btn-confirm-yes').click(function () {
        $(this).attr('disabled', 'disabled');
        $('#confirmationModal').modal("hide");
        yesCallback();
    });
}
