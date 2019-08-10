function selectImage(self) {

    var image = $(self).closest('.previous-image');

    if (image.hasClass('selected')) {
        image.removeClass('selected');
        $("#selectedFilename").val('');
        $("#selectedLocation").val('');
        return;
    }

    $('.previous-image.selected').removeClass('selected');
    image.addClass('selected');

    $("#selectedFilename").val(image.data('filename'));
    $("#selectedLocation").val(image.data('location'));

}

