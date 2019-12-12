var mainMarginRef = 0.1;
var subEntryMarginRef = 0.2;
var col2MarginNarrowRef = 0.5;
var col2MarginRef = 0.7;
var col2MarginWiderRef = 0.8;
var col3MarginRef = 1.5;
var headerMarginRef = 0.4;
var endMarginRef = 2.2;

var isDone = false;
var currPage = 0;

var slider = 0.5;
var counter = 0;
var size = 0;

var ajaxRequests = [];

function printOne(id, date) {
    var doc = newDoc();
    size = 1;
    getJson(id, date, doc, 'save', null);
}

function printAll(date) {

    mainMarginRef = 0.1;
    subEntryMarginRef = 0.2;
    col2MarginRef = 0.7;
    col2MarginWiderRef = 0.8;
    col3MarginRef = 1.5;
    headerMarginRef = 0.4;
    endMarginRef = 2.2;

    isDone = false;
    currPage = 0;

    slider = 0.5;
    counter = 0;
    size = 0;

    date =  $("#payslipDate").length != 0 && $("#payslipDate").val() != '' ? $("#payslipDate").val() : date;
    console.log(date);

    var emp;
    console.log('printingall');

    var url = '/payroll/getemployees/' + date;
    $.ajax({
        url: url,
        contentType: 'text/plain',
        dataType:"json",
        success: function(result) {
            emp =  result;
            size = Object.keys(emp).length;
            var doc = newDoc();
            var mode = 'add';
            var filename = 'all-payslip-' + getTimestamp() + '.pdf';

            var interval = window.setInterval(function() {
                if (size*2 === counter) {
                    if (filename === null) {
                        filename = (result.employeeName + '-payslip-'+ getTimestamp()).toLowerCase().replaceAll(" ", "-").replaceAll("/", "-").replaceAll(".", "") + ".pdf";
                    }
                    doc.save(filename);
                    window.clearInterval(interval);
                }
            }, 1000);

            for (var i = 0; i < size; i++) {
                getJson(emp[i].id, date, doc, mode, filename);
            }
        }
    });
}

function getJson(id, date, doc, mode, filename) {

    slider = 0.5;
    //counter = 0;
    isDone = false;
    currPage = 0;

    var url = '/payroll/get/' + id + '/' + date;
    $.ajax({
        url: url,
        contentType: 'text/plain',
        dataType:"json",
        async: false,
        success: function(result) {
            if (result['basicPay'] != undefined && result['basicPay'] != null && result['basicPay'] != 0) {
                print(result, doc, mode, 'Employee', filename);
            } else {
                counter += 2;
            }
        }
    });

}

function newDoc() {
    doc = new jsPDF({
        orientation: 'landscape',
        unit: 'in',
        format: [8.5, 11]
    });

    return doc;
}

function print(result, doc, mode, copy, filename) {

    var logo = new Image();

    logo.onload = function() {
        doc.addImage(logo, 'JPEG', 0.4 + slider, mainMarginRef + 0.5, 1, 0.5, 'logo');
        printText(doc, result, copy);
        counter++;
        //console.log(counter);

        if (mode === 'save' && copy === 'Company' && size*2 === counter) {
            if (filename === null) {
                filename = (result.employeeName + '-payslip-'+ getTimestamp()).toLowerCase().replaceAll(" ", "-").replaceAll("/", "-").replaceAll(".", "") + ".pdf";
            }
            doc.save(filename);
        } else {
            if (counter % 4 == 0 && size*2 != counter) {
                doc.addPage();
                slider = 0.5;
            }
            else {
                slider += 2.1;
            }
        }

        if (copy === "Employee") {
            print(result, doc, mode, 'Company',filename);
        }
    }
    logo.src = "/images/logo-small-black.jpg";
}

function spacer(i) {
    return i + 0.15;
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
    console.log(result);

    var mainMargin = mainMarginRef + slider;
    var subEntryMargin = subEntryMarginRef + slider;
    var col2MarginNarrow = col2MarginNarrowRef + slider;
    var col2Margin = col2MarginRef + slider;
    var col2MarginWider = col2MarginWiderRef + slider;
    var col3Margin = col3MarginRef + slider;
    var headerMargin = headerMarginRef + slider;
    var endMargin = endMarginRef + slider;

    addBorders();

    var i = 0.6;

    //doc.text("+", mainMargin-0.2, i-0.1);
    doc.text(copy + "'s copy", mainMargin, i);

    i = 1.05;
    doc.setFontSize(6);
    doc.text('CJI GENERAL SERVICES INC.', headerMargin, i = spacer(i));
    doc.setFontSize(10);
    doc.text('PAY SLIP', col2Margin, i = spacer(i));

    doc.setFontSize(7);

    doc.text('Payroll period:', mainMargin, i = spacer(i));
    doc.text(result.period, col2MarginWider, i);
    underline(doc, col2MarginWider, i, 20);

    doc.text('No:', mainMargin, i = spacer(i));
    doc.text(result.employeeId, col2MarginWider, i);
    underline(doc, col2MarginWider, i, 20);

    doc.text('Name:', mainMargin, i = spacer(i));
    if (result.employeeName.length >= 18) {
        doc.setFontSize(6);
        doc.text(result.employeeName, col2MarginWider, i);
        doc.setFontSize(7);
    } else {
        doc.text(result.employeeName, col2MarginWider, i);
    }
    underline(doc, col2MarginWider, i, 20);

    doc.text('Department:', mainMargin, i = spacer(i));
    if (result.employeeDepartment.length >= 18) {
        doc.setFontSize(6);
        doc.text(result.employeeDepartment, col2MarginWider, i);
        doc.setFontSize(7);
    } else {
        doc.text(result.employeeDepartment, col2MarginWider, i);
    }
    underline(doc, col2MarginWider, i, 20);

    doc.text('Rate:', mainMargin, i = spacer(i));
    var margin = col2MarginWider;
    var undlLength = 20;
    for (var j = 0; j < result.details.length; j++) {
        var dateShortStr = '';
        if (result.details.length > 1) {
            dateShortStr = formatToShortDate(result.details[j].startdate) + ': ';
            margin = col2MarginNarrow;
            undlLength = 25;
        }

        doc.text(dateShortStr + result.details[j].rate + ' ' + result.details[j].ratebasis + ', ' + result.details[j].paymentmode.displayName, margin, i);
        if (j < result.details.length - 1)
            i = spacer(i);
    }
    underline(doc, margin, i, undlLength);

    doc.text("*************************************************", mainMargin, i = spacer(i));

    doc.text('Basic Pay:', mainMargin, i = spacer(i));
    doc.text(result.regularHours + ' hrs', col2MarginWider, i);
    doc.text(addCommas(result.basicPayBase) + '', col3Margin, i);

    doc.text('Basic Adj:', mainMargin, i = spacer(i));
    doc.text(result.adjustmentsDetails.hasOwnProperty('basicadjustment') ? addCommas(result.adjustmentsDetails['basicadjustment']) : '' + '', col3Margin, i);

    doc.text('Overtime', mainMargin, i = spacer(i));

    doc.text('ROT', subEntryMargin, i = spacer(i));
    doc.text('1.25', col2Margin, i);
    doc.text(result.otDetails != null && 'rot' in result.otDetails ? result.otDetails.rot + ' hrs' : '0', 1.05 + slider, i);
    doc.text(result.otDetails != null &&'rotrate' in result.otDetails ? addCommas(result.otDetails.rotrate) + '' : '0', col3Margin, i);

    doc.text('RXT', subEntryMargin, i = spacer(i));
    doc.text('1.25', col2Margin, i);
    doc.text(result.otDetails != null && 'xot' in result.otDetails ? result.otDetails.rot + ' hrs' : '0', 1.05 + slider, i);
    doc.text(result.otDetails != null &&'xotrate' in result.otDetails ? addCommas(result.otDetails.rotrate) + '' : '0', col3Margin, i);

    doc.text('SOT/SPH', subEntryMargin, i = spacer(i));
    doc.text('1.3', col2Margin, i);
    doc.text(result.otDetails != null && 'sot' in result.otDetails ? result.otDetails.sot + ' hrs' : '0', 1.05 + slider, i);
    doc.text(result.otDetails != null && 'sotrate' in result.otDetails ? addCommas(result.otDetails.sotrate) + '' : '0', col3Margin, i);

    doc.text('XSOT', subEntryMargin, i = spacer(i));
    doc.text('1.3', col2Margin, i);
    doc.text(result.otDetails != null && 'xsot' in result.otDetails ? result.otDetails.xsot + ' hrs' : '0', 1.05 + slider, i);
    doc.text(result.otDetails != null && 'xsotrate' in result.otDetails ? addCommas(result.otDetails.xsotrate) + '' : '0', col3Margin, i);

    doc.text('LHOT', subEntryMargin, i = spacer(i));
    doc.text('1.3', col2Margin, i);
    doc.text(result.otDetails != null && 'lhot' in result.otDetails ? result.otDetails.lhot + ' hrs' : '0', 1.05 + slider, i);
    doc.text(result.otDetails != null && 'lhotrate' in result.otDetails ? addCommas(result.otDetails.lhotrate) + '' : '0', col3Margin, i);

    doc.text('XLHOT', subEntryMargin, i = spacer(i));
    doc.text('1.3', col2Margin, i);
    doc.text(result.otDetails != null && 'xlhot' in result.otDetails ? result.otDetails.xlhot + ' hrs' : '0', 1.05 + slider, i);
    doc.text(result.otDetails != null && 'xlhotrate' in result.otDetails ? addCommas(result.otDetails.xlhotrate) + '' : '0', col3Margin, i);
    underline(doc, col3Margin, i, 7);

    doc.text('Overtime Adj:', mainMargin, i = spacer(i));
    doc.text(result.adjustmentsDetails.hasOwnProperty('overtimeadjustment') ? addCommas(result.adjustmentsDetails['overtimeadjustment']) : '' + '', col3Margin, i);

    doc.text('Gross Pay:', mainMargin, i = spacer(i));
    doc.text(addCommas(result.grossPay) + '', col3Margin, i);

    doc.text('Less: Deductions', mainMargin, i = spacer(i));

    doc.text('Withholding Tax', subEntryMargin, i = spacer(i));
    doc.text('Withholding Tax' in result.exemptionDetails ? addCommas(result.exemptionDetails["Withholding Tax"]) : '0', col3Margin, i);
    //delete result.exemptionDetails["Withholding Tax"];
    doc.text('SSS', subEntryMargin, i = spacer(i));
    doc.text('SSS' in result.exemptionDetails ? addCommas(result.exemptionDetails["SSS"]) : '0', col3Margin, i);
    //delete result.exemptionDetails["SSS"];
    doc.text('PhilHealth', subEntryMargin, i = spacer(i));
    doc.text('PhilHealth' in result.exemptionDetails ? addCommas(result.exemptionDetails["PhilHealth"]) : '0', col3Margin, i);
    //delete result.exemptionDetails["PhilHealth"];
    doc.text('PAGIBIG', subEntryMargin, i = spacer(i));
    doc.text('PAGIBIG' in result.exemptionDetails ? addCommas(result.exemptionDetails["PAGIBIG"]) : '0', col3Margin, i);
    //delete result.exemptionDetails["PAGIBIG"];
    doc.text('SSS Loan', subEntryMargin, i = spacer(i));
    doc.text('SSS Loan' in result.exemptionDetails ? addCommas(result.exemptionDetails["SSS Loan"]) : '0', col3Margin, i);
    //delete result.exemptionDetails["SSS Loan"];
    doc.text('PAGIBIG Loan', subEntryMargin, i = spacer(i));
    doc.text('PAGIBIG Loan' in result.exemptionDetails ? addCommas(result.exemptionDetails["PAGIBIG Loan"]) : '0', col3Margin, i);
    //delete result.exemptionDetails["PAGIBIG Loan"];

    for (var key in result.exemptionDetails){
        if (result.exemptionDetails.hasOwnProperty(key)) {
            if (key === '_TOTAL' || key === '_TOTAL_BEFORE_TAX'
            ||  key === 'Withholding Tax' ||  key === 'SSS' ||  key === 'PhilHealth'
            ||  key === 'PAGIBIG' ||  key === 'SSS Loan' ||  key === 'PAGIBIIG Loan')
                continue;
            doc.text(key, subEntryMargin, i = spacer(i));
            doc.text(addCommas(result.exemptionDetails[key]) + "", col3Margin, i);
        }
    }

    underline(doc, col3Margin, i, 7);

    doc.text('Net Pay:', mainMargin, i = spacer(i));
    doc.text(addCommas(result.netPay) + '', col3Margin, i);

    doc.text('Add:', mainMargin, i = spacer(i));
    doc.text("Allowance", subEntryMargin, i = spacer(i));
    doc.text(addCommas(result.allowance) + "", col3Margin, i);

    for (var key in result.adjustmentsDetails){
        if (result.adjustmentsDetails.hasOwnProperty(key)) {
            if (key === '_TOTAL' || key === '_OTHER_ADJUSTMENTS' || key === 'basicadjustment' || key === 'overtimeadjustment')
                continue;
            doc.text(key, subEntryMargin, i = spacer(i));
            doc.text(addCommas(result.adjustmentsDetails[key]) + "", col3Margin, i);
        }
    }

    underline(doc, col3Margin, i, 7);

    doc.text('Take Home Pay:', mainMargin, i = spacer(i));
    doc.text(addCommas(result.takeHomePay) + '', col3Margin, i);
    underline(doc, col3Margin, i, 7);
    underline(doc, col3Margin, i+0.025, 7);

    i = 6.25;
    underline(doc, col3Margin-0.4, i+0.05, 15);

    doc.text(new Date(($("#payslipDate").length != 0 && $("#payslipDate").val() != '' ? $("#payslipDate").val() : Date.now())).toDateString(), mainMargin, i = spacer(i));
    doc.text("Signature", col3Margin-0.2, i);

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

function addCommas(nStr) {
    nStr = (nStr*1).toFixed(2);
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return (x1 + x2);
}

function addBorders() {

    var i = 0.6;
    var mainMargin = mainMarginRef + slider;
    var subEntryMargin = subEntryMarginRef + slider;
    var col2Margin = col2MarginRef + slider;
    var col2MarginWider = col2MarginWiderRef + slider;
    var col3Margin = col3MarginRef + slider;
    var headerMargin = headerMarginRef + slider;
    var endMargin = endMarginRef + slider;

    doc.setFontSize(10);
    doc.text("|", mainMargin-0.17, i-0.05);
    doc.text("_", mainMargin-0.155, i-0.174);
    doc.text("|", endMargin-0.17, i-0.05);
    doc.text("_", endMargin-0.225, i-0.174);

    for (var x = 0; x <= 2.09; x += 0.08) {
        doc.text("_", mainMargin-0.155+x, i-0.174);
    }

    for (; i < 6.5; i += 0.08) {
        doc.text("|", mainMargin-0.17, i);
        doc.text("|", endMargin-0.17, i);
    }

    for (var x = 0; x <= 2.08; x += 0.08) {
        doc.text("_", mainMargin-0.155+x, i-0.08);
    }

    doc.setFontSize(5);
}

function formatToShortDate(dateString) {
    var date = new Date(dateString);
    months = [
        'Jan', 'Feb', 'Mar',
        'Apr', 'May', 'Jun',
        'Jul', 'Aug', 'Sep',
        'Oct', 'Nov', 'Dec'
    ];
    return months[date.getMonth()] + ' ' + date.getDate();
}
