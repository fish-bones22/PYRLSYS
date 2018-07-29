function addDependent() {

    var ind = $("#currentIndex").val();

    var container = $(
        '<div class="row dependent-' + ind + '">\
            <div class="col-md-8 form-paper border-left">\
                <div class="row">\
                    <div class="col-md-4">\
                        <div class="form-group lastname"></div>\
                    </div>\
                    <div class="col-md-4">\
                        <div class="form-group firstname"></div>\
                    </div>\
                    <div class="col-md-4">\
                        <div class="form-group middlename"></div>\
                    </div>\
                </div>\
            </div>\
            <div class="col-md-4 form-paper">\
                <div class="form-group relationship"></div>\
            </div>\
        </div>'
    );

    $("#addDependentContainer").before(container);
    $container = $(".dependent-"+ind);

    var labelL = $("<label>")
        .addClass("form-paper-label")
        .attr("for", "dependentLastName["+ind+"]")
        .text("Last Name");
    var inputL = $("<input>")
        .addClass("form-control")
        .attr("id", "dependentLastName["+ind+"]")
        .attr("name", "dependent_last_name["+ind+"]")
        .attr("type", "text");

    container.find(".lastname").append(labelL);
    container.find(".lastname").append(inputL);

    var labelF = $("<label>")
        .addClass("form-paper-label")
        .attr("for", "dependentFirstName["+ind+"]")
        .text("First Name");
    var inputF = $("<input>")
        .addClass("form-control")
        .attr("id", "dependentFirstName["+ind+"]")
        .attr("name", "dependent_first_name["+ind+"]")
        .attr("type", "text");

    container.find(".firstname").append(labelF);
    container.find(".firstname").append(inputF);

    var labelM = $("<label>")
        .addClass("form-paper-label")
        .attr("for", "dependentMiddleName["+ind+"]")
        .text("Middle Name");
    var inputM = $("<input>")
        .addClass("form-control")
        .attr("id", "dependentMiddleName["+ind+"]")
        .attr("name", "dependent_middle_name["+ind+"]")
        .attr("type", "text");

    container.find(".middlename").append(labelM);
    container.find(".middlename").append(inputM);

    var labelR = $("<label>")
    .addClass("form-paper-label")
    .attr("for", "dependentRelationship["+ind+"]")
    .text("Relationship");
    var inputR = $("<input>")
    .addClass("form-control")
    .attr("id", "dependentRelationship["+ind+"]")
    .attr("name", "dependent_relationship["+ind+"]")
    .attr("type", "text");

    var deleteBtn = $("<button>")
    .addClass("btn close text-muted")
    .attr("type", "button")
    .text("×");

    container.find(".relationship").append(deleteBtn);
    container.find(".relationship").append(labelR);
    container.find(".relationship").append(inputR);

    $("#currentIndex").val(++ind);
}
