function togglePasswordBox() {
    $("#passwordBox").toggle();
    $("#passwordBoxToggler").toggle();
}

function toggleAdminBox() {
    $("label[for='password']").toggle();
    $("#password").toggle();
    $("#adminBox").toggle();
}
