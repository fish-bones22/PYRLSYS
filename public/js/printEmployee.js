var col1 = 0.5;
var col2 = 1.125;
var col3 = 1.75;
var col4 = 2.375;
var col5 = 3;
var col6 = 3.625;
var col7 = 4.25;
var col8 = 4.875;
var col9 = 5.5;
var col10 = 6.125;
var col11 = 6.75;
var col12 = 7.375;

var labelSize = 8;
var mainSize = 12;

var isDone = false;
var currPage = 0;

// function printOne(id) {
//     var doc = newDoc();
//     getJson(id, doc, 'save', null);
// }

function printOne(id) {
    // These variables are to be use removing items that must not include in pdf
    var rateDiv = $('#rateDivId');
    var allowanceDiv = $('#allowanceDivId');
    var buttonsDiv = $('#buttonsDivId');
    var viewScheduleHistoryDiv = $('#viewScheduleHistoryDivId');
    var viewHistoryOfTransferDivId = $('#viewHistoryOfTransferDivId');

    // removing all unnecessaries
    $('#rateDivId').remove();
    $('#allowanceDivId').remove();
    $('#viewScheduleHistoryDivId').remove();
    $('#viewHistoryOfTransferDivId').remove();
    $('#buttonsDivId').remove();

    var htmlValue = $('#pageContainerDivId').html();
    //console.log(htmlValue);
    console.log(id);
    // returning back all removed item
    $('#rateAndAllowanceDivId').append(rateDiv).append(allowanceDiv);
    $('#scheduleDivId').append(viewScheduleHistoryDiv);
    $(viewHistoryOfTransferDivId).insertAfter("#scheduleRowDivId");
    $('form').append(buttonsDiv);

    //
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url:'/employee/view/viewPfd',
        data: {'htmlValue' : htmlValue},
        //data: {'id' : id},
        success: function(data) {
            var blob=new Blob([data]);
            var link=document.createElement('a');
            link.href=window.URL.createObjectURL(blob);
            link.download="samplePDF.pdf";
            link.click();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(JSON.stringify(jqXHR));
            console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
        }
    });
}

function printAll() {

    var emp;
    console.log('printingall');

    var url = '/payroll/getemployees/';
    $.ajax({
        url: url,
        contentType: 'text/plain',
        dataType:"json",
        success: function(result) {
            console.log(result);
            emp =  result;
            size = Object.keys(emp).length;
            var doc = newDoc();
            var mode = 'add';
            var filename = 'all-payslip-' + getTimestamp() + '.pdf';
            for (var i = 0; i < size; i++) {
                if (i === size-1) {
                    mode = 'save';
                }
                getJson(emp[i].id, doc, mode, filename);
            }
        }
    });
}

function getJson(id, doc, mode, filename) {

    var url = '/employee/get/' + id;
    $.ajax({
        url: url,
        contentType: 'text/plain',
        dataType:"json",
        success: function(result) {
            //console.log(result);
            print(result, doc, 'save', 'Employee', filename);
            //print(result, doc, mode, 'Company',filename);
        }
    });

}

function newDoc() {
    doc = new jsPDF({
        orientation: 'portrait',
        unit: 'in',
        format: [8.5, 11]
    });

    return doc;
}

function print(result, doc, mode, copy, filename) {

    var logo = new Image();

    logo.onload = function() {
        doc.addImage(logo, 'JPEG', 7, 0.5, 1, 1, 'logo');
        printText(doc, result, copy);

        if (mode === 'save') {
            if (filename === null) {

                filename = (result.fullName + '-information-'+ getTimestamp()).toLowerCase().replaceAll(" ", "-").replaceAll("/", "-").replaceAll(".", "") + ".pdf";
            }
            doc.save(filename);
        } else {
            doc.addPage();
        }
    }
    logo.src = $("#primaryImage").attr("src");
}

function spacer(i) {
    return i + 0.20;
}


function underline(doc, x, y, length) {
    var str = "";
    for (var i = 0; i < length; i++) {
        str += "_";
    }
    doc.text(str, x, y);
}


function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}


function printText(doc, result, copy) {

    var i = 1.5;
    doc.setFontSize(24);
    doc.text(result.fullName, col1, i);
    doc.setFontSize(14);
    underline(doc, col1, i, 70);

    // Header
    doc.setFontSize(labelSize);
    doc.text("PERSONAL INFORMATION", col1, i = spacer(i));
    underline(doc, col1, i += 0.05, 122);

    // Info
    doc.setFontSize(labelSize);
    doc.text("Employee ID: ", col1, i = spacer(i));
    doc.text("Sex: ", col4, i);
    doc.text("Civil Status: ", col7, i);
    doc.text("Birthday: ", col10, i);

    doc.setFontSize(mainSize);
    doc.text(result.employeeId, col1, i = spacer(i));
    doc.text(result.sex === 'm' ? 'Male' : 'Female', col4, i);
    doc.text(result.details.civilstatus.value, col7, i);
    doc.text(result.details.birthday.value, col10, i);

    // Name
    doc.setFontSize(labelSize);
    underline(doc, col1, i += 0.1, 122);
    doc.text("Last Name: ", col1, i = spacer(i));
    doc.text("First Name: ", col5, i);
    doc.text("Middle Name: ", col9, i);

    doc.setFontSize(mainSize);
    doc.text(result.lastName, col1, i = spacer(i));
    doc.text(result.firstName, col5, i);
    doc.text(result.middleName, col9, i);

    // Spouse
    if (result.details.spouse != undefined) {

        var size = Object.keys(result.details.spouse).length;

        doc.setFontSize(labelSize);
        underline(doc, col1, i += 0.1, 122);
        doc.text("Spouse: ", col1, i = spacer(i));
        doc.text("Last Name: ", col1, i += 0.15);
        doc.text("First Name: ", col5, i);
        doc.text("Middle Name: ", col9, i);

        for (var j = 0; j < size; j++) {
            doc.setFontSize(mainSize);
            doc.text(result.details.spouse[j].lastname.value, col1, i = spacer(i));
            doc.text(result.details.spouse[j].firstname.value, col5, i);
            doc.text(result.details.spouse[j].middlename.value, col9, i);
        }

    }

    // Dependent
    if (result.details.dependent != undefined) {

        var size = Object.keys(result.details.dependent).length;

        doc.setFontSize(labelSize);
        underline(doc, col1, i += 0.1, 122);
        doc.text("Dependents ", col1, i = spacer(i));
        doc.text("Last Name: ", col1, i  += 0.15);
        doc.text("First Name: ", col4, i);
        doc.text("Middle Name: ", col7, i);
        doc.text("Relationship: ", col10, i);
        doc.setFontSize(mainSize);

        for (var j = 0; j < size; j++) {
            doc.text(result.details.dependent[j].lastname.value, col1, i = spacer(i));
            doc.text(result.details.dependent[j].firstname.value, col4, i);
            doc.text(result.details.dependent[j].middlename.value, col7, i);
            doc.text(result.details.dependent[j].relationship != undefined ? result.details.dependent[j].relationship.value : "NA", col10, i);
        }

    }

    // Other Info
    doc.setFontSize(labelSize);
    underline(doc, col1, i += 0.1, 122);
    doc.text("Address: ", col1, i = spacer(i));
    doc.text("Phone Number: ", col7, i);
    doc.text("Email Address: ", col10, i);

    doc.setFontSize(mainSize);
    doc.text(result.details.address != undefined ? result.details.address.value : 'Not set', col1, i = spacer(i));
    doc.text(result.details.phonenumber != undefined ? result.details.phonenumber.value : 'Not set', col7, i);
    doc.text(result.details.email != undefined ? result.details.email.value : 'Not set', col10, i);

    // Emergency Info
    doc.setFontSize(labelSize);
    underline(doc, col1, i += 0.1, 122);
    doc.text("Person to contact in case of emergency: ", col1, i = spacer(i));

    doc.setFontSize(mainSize);
    doc.text(result.details.emergencyname != undefined ? result.details.emergencyname.value : 'Not set', col1, i = spacer(i));
    doc.text(result.details.emergencyphone != undefined ? result.details.emergencyphone.value : 'Not set', col9, i);

    // Header
    doc.setFontSize(labelSize);
    underline(doc, col1, i = spacer(i), 122);
    doc.text("EMPLOYMENT INFORMATION", col1, i = spacer(i));
    underline(doc, col1, i += 0.05, 122);

    // Row 1
    doc.setFontSize(labelSize);
    doc.text("Time Card: ", col1, i = spacer(i));
    doc.text("Department: ", col3, i);
    doc.text("Position: ", col7, i);
    doc.text("Employment Type: ", col11, i);

    doc.setFontSize(mainSize);
    doc.text(result.current.timecard != undefined ? result.current.timecard : 'Not set', col1, i = spacer(i));
    doc.text(result.current.department != undefined ? result.current.department.displayName : 'Not set', col3, i);
    doc.text(result.current.position != undefined ? result.current.position : 'Not set', col7, i);
    doc.text(result.current.employmenttype != undefined ? result.current.employmenttype.displayName : 'Not set', col11, i);

    // Row 2
    doc.setFontSize(labelSize);
    doc.text("Date Started: ", col1, i = spacer(i));
    doc.text("Until", col5, i);
    doc.text("Status: ", col9, i);

    doc.setFontSize(mainSize);
    doc.text(result.current.datestarted != undefined ? result.current.datestarted : 'Not set', col1, i = spacer(i));
    doc.text(result.current.datetransfered != undefined ? result.current.datetransfered : 'Not set', col5, i);
    doc.text(result.current.contractstatus != undefined ? result.current.contractstatus.displayName : 'Not set', col9, i);

    // Row 3
    doc.setFontSize(labelSize);
    doc.text("Type of Payment: ", col1, i = spacer(i));
    doc.text("Mode of Payment: ", col4, i);
    doc.text("Rate Basis: ", col7, i);
    doc.text("Rate: ", col9, i);
    doc.text("Allowance: ", col11, i);

    doc.setFontSize(mainSize);
    doc.text(result.current.paymenttype != undefined ? result.current.paymenttype.displayName : 'Not set', col1, i = spacer(i));
    doc.text(result.current.paymentmode != undefined ? result.current.paymentmode.displayName : 'Not set', col4, i);
    doc.text(result.current.ratebasis != undefined ? result.current.ratebasis : 'Not set', col7, i);
    doc.text(result.current.rate != undefined ? result.current.rate : 'Not set', col9, i);
    doc.text(result.current.allowance != undefined ? result.current.allowance : 'Not set', col11, i);

    // Row 4
    doc.setFontSize(labelSize);
    doc.text("Time In: ", col1, i = spacer(i));
    doc.text("Time Out: ", col5, i);
    doc.text("Break: ", col9, i);

    doc.setFontSize(mainSize);
    doc.text(result.timeTable.timein != undefined ? formatTime(result.timeTable.timein) : 'Not set', col1, i = spacer(i));
    doc.text(result.timeTable.timeout != undefined ? formatTime(result.timeTable.timeout) : 'Not set', col5, i);
    doc.text(result.timeTable.break != null ? result.timeTable.break + " hrs": 'Not set', col9, i);

     // Header
     doc.setFontSize(labelSize);
     underline(doc, col1, i = spacer(i), 122);
     doc.text("OTHER INFORMATION", col1, i = spacer(i));
     underline(doc, col1, i += 0.05, 122);

     // Row 1
     doc.setFontSize(labelSize);
     doc.text("TIN #: ", col1, i = spacer(i));
     doc.text("SS #: ", col4, i);
     doc.text("PhilHealth: ", col7, i);
     doc.text("PAGIBIG: ", col10, i);

     doc.setFontSize(mainSize);
     doc.text(result.deductibles.tin != undefined ? result.deductibles.tin.value : 'Not set', col1, i = spacer(i));
     doc.text(result.deductibles.sss != undefined ? result.deductibles.sss.value : 'Not set', col4, i);
     doc.text(result.deductibles.philhealth != undefined ? result.deductibles.philhealth.value : 'Not set', col7, i);
     doc.text(result.deductibles.pagibig != undefined ? result.deductibles.pagibig.value : 'Not set', col10, i);

     // Row 2
     doc.setFontSize(labelSize);
     doc.text("Number of Memo: ", col1, i = spacer(i));
     doc.text("Remarks: ", col3, i);

     doc.setFontSize(mainSize);
     doc.text(result.details.numberofmemo != undefined ? result.details.numberofmemo.value : 'None', col1, i = spacer(i));
     doc.text(result.details.remarks != undefined ? result.details.remarks.value : 'None', col3, i);

}


function getTimestamp() {
    var now = new Date(Date.now());
    var year = now.getFullYear();
    var month = now.getMonth();
    var day = now.getDay();
    var hour = now.getHours();
    var minutes  = now.getMinutes();
    var sec = now.getSeconds();
    var mill = now.getMilliseconds();
    return year + "" + month + "" + day + "" + hour + "" + minutes + "" + sec + "" + mill;
}

function formatDate(date) {
    var now = new Date(date);
    var year = now.getFullYear();
    var month = now.getMonth();
    var day = now.getDay();
    return year + "-" + month + "-" + day;
}

function formatTime(date) {
    var now = new Date(date);
    var hour = now.getHours();
    var minutes  = now.getMinutes();
    minutes = minutes < 10 ? '0' + minutes : minutes;
    hour = hour < 10 ? '0' + hour : hour;

    return hour + ":" + minutes;
}
