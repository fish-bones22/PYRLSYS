function addContactDetails() {

    var index = $("#contactsSize").val();

    var div = $("<div></div>").addClass("form-group");
    var inputValue = $("<input />")
    .attr("placeholder", "Number")
    .attr("type", "text")
    .attr("name", "other_contacts["+ index +"][value]")
    .addClass("form-control");
    var inputDetail = $("<input />")
    .attr("placeholder", "Details")
    .attr("type", "text")
    .attr("name", "other_contacts["+ index +"][detail]")
    .addClass("form-control form-control-sm");
    var inputId = $("<input />")
    .attr("type", "hidden")
    .attr("name", "other_contacts["+ index +"][id]")
    .val(0);
    var inputKey = $("<input />")
    .attr("type", "hidden")
    .attr("name", "other_contacts["+ index +"][key]")
    .val('contact');
    var inputDisplayName = $("<input />")
    .attr("type", "hidden")
    .attr("name", "other_contacts["+ index +"][displayName]")
    .val("Contact Number");

    div.append(inputValue);
    div.append(inputDetail);
    div.append(inputId);
    div.append(inputKey);
    div.append(inputDisplayName);
    $("#addNewContactButton").before(div);

    $("#contactsSize").val(++index);

}
