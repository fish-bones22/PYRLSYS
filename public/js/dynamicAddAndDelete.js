function createNewRow(self, rowName) {

    var currentIndex = $("#" + rowName + "-index").val()*1;

    var protoRow;
    if ($('.' + rowName +'-default').length > 0) {
        protoRow = $('.' + rowName +'-default').clone();
    } else {
        protoRow = $("." + rowName + "-0").clone();
    }

    protoRow = makeIndexFromObject(protoRow, 'select', 'id', 0, currentIndex);
    protoRow = makeIndexFromObject(protoRow, 'select', 'name', 0, currentIndex);
    protoRow = makeIndexFromObject(protoRow, 'input', 'id', 0, currentIndex);
    protoRow = makeIndexFromObject(protoRow, 'input', 'name', 0, currentIndex);
    protoRow = makeIndexFromObject(protoRow, 'label', 'for', 0, currentIndex);
    protoRow.find("button.close").attr("data-index", currentIndex);
    protoRow.find("input, select").not("input[type='radio']").each(function() {
        $(this).val('');
    });
    protoRow.find("input[type='radio']").each(function() {
        $(this).prop('checked', false);
    });
    protoRow.removeClass(rowName + "-0").addClass(rowName + "-" + currentIndex);
    $(self).closest(".addContainer").before(protoRow);

    $("#" + rowName + "-index").val(++currentIndex);

}

function deleteRow(self, rowName) {

    var index = $(self).data("index");
    var currentIndex = $("#" + rowName + "-index").val()*1;
    var thisRow = $("." + rowName + "-" + index);

    if (currentIndex == 1) {

        thisRow.find("input, select").not("input[type='radio']").each(function() {
            $(this).val("");
        });
        thisRow.find("input[type='radio']").each(function() {
            $(this).prop('checked', false);
        });

        thisRow.find("div.display, span.display").each(function() {
            if ($(this).attr("data-default") != undefined)
                $(this).html("<i class='text-muted small'>" + $(this).data('default') + "</i>");
            else
                $(this).text('');
        })

        return;
    }

    for (var i = index + 1; i <= currentIndex; i++) {
        var protoRow = makeIndex(rowName, "select", "id", i, i-1);
        protoRow = makeIndex(rowName, "select", "name", i, i-1);
        protoRow = makeIndex(rowName, "input", "id", i, i-1);
        protoRow = makeIndex(rowName, "input", "name", i, i-1);
        protoRow = makeIndex(rowName, "label", "for", i, i-1);
        protoRow.find("button.close").attr("data-index", i-1);
        protoRow.removeClass(rowName + "-" + (i)).addClass(rowName + "-" + (i-1));
    }
    $("#" + rowName + "-index").val(--currentIndex);
    thisRow.remove();
}


function makeIndex(rowName, element, attribute, oldIndex, newIndex) {

    var protoRow = $("." + rowName + "-" + oldIndex);
    protoRow = makeIndexFromObject(protoRow, element, attribute, oldIndex, newIndex);
    return protoRow;

}


function makeIndexFromObject(object, element, attribute, oldIndex, newIndex) {

    object.find(element).each(function() {
        var elm = $(this);
        if (elm.attr(attribute) != undefined && elm.attr(attribute).indexOf("[" + oldIndex + "]") > -1) {
            var currAttr = elm.attr(attribute);
            var newAttr = currAttr.replace("[" + oldIndex + "]", "[" + newIndex + "]");
            elm.attr(attribute, newAttr);
            // Due to timepicki.js limitations,
            // this workaround reinitializes time inputs
            // for timepicki.js
            if (elm.attr("type") != undefined && elm.attr("type") === "time" && elm.attr("readonly") == undefined) {
                var name = elm.attr("name");
                var id = elm.attr("id");
                var class_ = elm.attr("class");
                var value = elm.attr("value");
                var onchange = elm.attr("onchange");
                var newInp = $("<input>")
                .attr("name", name)
                .attr("id", id)
                .attr("class", class_)
                .attr("type", "time")
                .attr("value", value)
                .attr("onchange", onchange);
                var timePick = elm.closest("div.time_pick");
                timePick.before(newInp);
                timePick.remove();
                newInp.timepicki();
            }
        }
    });

    return object;

}

