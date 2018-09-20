var mainMargin = 0.05;
var subEntryMargin = 0.1;
var col2Margin = 0.45;
var col2MarginWider = 0.47;
var col3Margin = 0.79;

var isDone = false;
var currPage = 0;


function printOne(id, date) {
    var doc = newDoc();
    getJson(id, date, doc, 'save', null);
}

function printAll(date) {

    var emp;
    console.log('printingall');

    var url = '/payroll/getemployees/' + date;
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
                getJson(emp[i].id, date, doc, mode, filename);
            }
        }
    });
}

function getJson(id, date, doc, mode, filename) {

    var url = '/payroll/get/' + id + '/' + date;
    $.ajax({
        url: url,
        contentType: 'text/plain',
        dataType:"json",
        success: function(result) {
            print(result, doc, 'new', 'Employee', filename);
            print(result, doc, mode, 'Company',filename);
        }
    });

}

function newDoc() {
    doc = new jsPDF({
        orientation: 'portrait',
        unit: 'in',
        format: [1, 3]
    });

    return doc;
}

function print(result, doc, mode, copy, filename) {

    var logo = new Image();

    logo.onload = function() {
        doc.addImage(logo, 'JPEG', 0.18, 0.11, 0.6, 0.3, 'logo');
        printText(doc, result, copy);

        if (mode === 'save') {
            if (filename === null) {

                filename = (result.employeeName + '-payslip-'+ getTimestamp()).toLowerCase().replaceAll(" ", "-").replaceAll("/", "-").replaceAll(".", "") + ".pdf";
            }
            doc.save(filename);
        } else {
            doc.addPage();
        }
    }
    logo.src = "/images/logo-small.jpg";
}

function spacer(i) {
    return i + 0.06;
}

function underline(doc, x, y, length) {
    var str = "";
    for (var i = 0; i < length; i++) {
        str += "_";
    }
    doc.text(str, x-0.01, y);
}


function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}



function printText(doc, result, copy) {
    var i = 0.1;

    doc.setFontSize(2);
    doc.text(copy + "'s copy", 0.1, i);

    i = 0.4;
    doc.setFontSize(4);
    doc.text('CJI GENERAL SERVICES INC.', 0.12, i = spacer(i));
    doc.setFontSize(5);
    doc.text('PAY SLIP', 0.35, i = spacer(i));
    i = spacer(i)

    doc.setFontSize(3);

    doc.text('Payroll period:', mainMargin, i = spacer(i));
    doc.text(result.period, col2MarginWider, i);
    underline(doc, col2MarginWider, i, 20);

    doc.text('No:', mainMargin, i = spacer(i));
    doc.text(result.employeeId, col2MarginWider, i);
    underline(doc, col2MarginWider, i, 20);

    doc.text('Name:', mainMargin, i = spacer(i));
    doc.text(result.employeeName, col2MarginWider, i);
    underline(doc, col2MarginWider, i, 20);

    doc.text('Department:', mainMargin, i = spacer(i));
    doc.text(result.employeeDepartment, col2MarginWider, i);
    underline(doc, col2MarginWider, i, 20);

    doc.text('Rate:', mainMargin, i = spacer(i));
    doc.text(result.rate, col2MarginWider, i);
    underline(doc, col2MarginWider, i, 20);

    doc.text('Payment:', mainMargin, i = spacer(i));
    doc.text(result.modeOfPayment, col2MarginWider, i);
    underline(doc, col2MarginWider, i, 20);

    doc.text("*******************************************************", mainMargin, i = spacer(i));

    doc.text('Basic Pay:', mainMargin, i = spacer(i));
    doc.text(result.regularHours + ' hrs', col2Margin, i);
    doc.text(result.basicPay + '', col3Margin, i);

    doc.text('Overtime', mainMargin, i = spacer(i));

    doc.text('ROT', subEntryMargin, i = spacer(i));
    doc.text('1.25', col2Margin, i);
    doc.text('rot' in result.otDetails ? result.otDetails.rot + ' hrs' : '0', 1.05, i);
    doc.text('rotrate' in result.otDetails ? result.otDetails.rotrate + '' : '0', col3Margin, i);

    doc.text('SOT/SPH', subEntryMargin, i = spacer(i));
    doc.text('1.3', col2Margin, i);
    doc.text('sot' in result.otDetails ? result.otDetails.sot + ' hrs' : '0', 1.05, i);
    doc.text('sotrate' in result.otDetails ? result.otDetails.sotrate + '' : '0', col3Margin, i);

    doc.text('XSOT', subEntryMargin, i = spacer(i));
    doc.text('1.3', col2Margin, i);
    doc.text('xsot' in result.otDetails ? result.otDetails.xsot + ' hrs' : '0', 1.05, i);
    doc.text('xsotrate' in result.otDetails ? result.otDetails.xsotrate + '' : '0', col3Margin, i);

    doc.text('LHOT', subEntryMargin, i = spacer(i));
    doc.text('1.3', col2Margin, i);
    doc.text('lhot' in result.otDetails ? result.otDetails.lhot + ' hrs' : '0', 1.05, i);
    doc.text('lhotrate' in result.otDetails ? result.otDetails.lhotrate + '' : '0', col3Margin, i);

    doc.text('XLHOT', subEntryMargin, i = spacer(i));
    doc.text('1.3', col2Margin, i);
    doc.text('xlhot' in result.otDetails ? result.otDetails.xlhot + ' hrs' : '0', 1.05, i);
    doc.text('xlhotrate' in result.otDetails ? result.otDetails.xlhotrate + '' : '0', col3Margin, i);
    underline(doc, col3Margin, i, 7);

    doc.text('Gross Pay:', mainMargin, i = spacer(i));
    doc.text(result.grossPay + '', col3Margin, i);

    doc.text('Less: Deductions', mainMargin, i = spacer(i));

    doc.text('Withholding Tax', subEntryMargin, i = spacer(i));
    doc.text('Withholding Tax' in result.exemptionDetails ? result.exemptionDetails["Withholding Tax"] : '0', col3Margin, i);
    delete result.exemptionDetails["Withholding Tax"];
    doc.text('SSS', subEntryMargin, i = spacer(i));
    doc.text('SSS' in result.exemptionDetails ? result.exemptionDetails["SSS"] : '0', col3Margin, i);
    delete result.exemptionDetails["SSS"];
    doc.text('PhilHealth', subEntryMargin, i = spacer(i));
    doc.text('PhilHealth' in result.exemptionDetails ? result.exemptionDetails["PhilHealth"] : '0', col3Margin, i);
    delete result.exemptionDetails["PhilHealth"];
    doc.text('PAGIBIG', subEntryMargin, i = spacer(i));
    doc.text('PAGIBIG' in result.exemptionDetails ? result.exemptionDetails["PAGIBIG"] : '0', col3Margin, i);
    delete result.exemptionDetails["PAGIBIG"];
    doc.text('SSS Loan', subEntryMargin, i = spacer(i));
    doc.text('SSS Loan' in result.exemptionDetails ? result.exemptionDetails["SSS Loan"] : '0', col3Margin, i);
    delete result.exemptionDetails["SSS Loan"];
    doc.text('PAGIBIG Loan', subEntryMargin, i = spacer(i));
    doc.text('PAGIBIG Loan' in result.exemptionDetails ? result.exemptionDetails["PAGIBIG Loan"] : '0', col3Margin, i);
    delete result.exemptionDetails["PAGIBIG Loan"];

    for (var key in result.exemptionDetails){
        if (result.exemptionDetails.hasOwnProperty(key)) {
            if (key === '_TOTAL' || key === '_TOTAL_BEFORE_TAX')
                continue;
            doc.text(key, subEntryMargin, i = spacer(i));
            doc.text(result.exemptionDetails[key] + "", col3Margin, i);
        }
    }

    underline(doc, col3Margin, i, 7);

    doc.text('Net Pay:', mainMargin, i = spacer(i));
    doc.text(result.netPay + '', col3Margin, i);

    if (Object.keys(result.adjustmentsDetails).length > 1) {
        doc.text('Add: Allowance:', mainMargin, i = spacer(i));
    }

    for (var key in result.adjustmentsDetails){
        if (result.adjustmentsDetails.hasOwnProperty(key)) {
            if (key === '_TOTAL')
                continue;
            doc.text(key, subEntryMargin, i = spacer(i));
            doc.text(result.adjustmentsDetails[key] + "", col3Margin, i);
        }
    }

    underline(doc, col3Margin, i, 7);

    doc.text('Take Home Pay:', mainMargin, i = spacer(i));
    doc.text(result.takeHomePay + '', col3Margin, i);
    underline(doc, col3Margin, i, 7);
    underline(doc, col3Margin, i+0.01, 7);

    i = 2.5;
    underline(doc, col3Margin-0.2, i+0.05, 15);

    doc.text(new Date(($("#payslipDate").val() != '' ? $("#payslipDate").val() : Date.now())).toDateString(), mainMargin, i = spacer(i));
    doc.text("Signature", col3Margin-0.12, i + 0.04);

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
