var table1, table2;

$(function() {
    table1 = $("#recordSummaryTable").DataTable({
        "ordering": false,
        "lengthChange": false,
        "info": false,
        "dom": "<t<'float-right'p>>",
        "buttons": [
            {
                filename: getFileName,
                extend: 'excelHtml5',
            },
            {
                filename: getFileName,
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'FOLIO',
                //customize : function(doc) {doc.pageMargins = [10, 10, 10,10 ]; }
            }
        ]
    });
    table2 = $("#outlierSummaryTable").DataTable({
        "lengthChange": false,
        "info": false,
        "dom": "<t<'float-right'p>>",
        "buttons": [
            {
                filename: getFileName,
                extend: 'excelHtml5',
            },
            {
                filename: getFileName,
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'LETTER'
            }
        ]
    });
    filterStatus();
    $('#statusToggler').change(filterStatus);
});

function filterDepartment() {
    var term = $("#department").val();
    table1.column(3).search(term);
    table2.column(3).search(term);
    table1.column(3).draw();
    table2.column(3).draw();
}

function filterTables() {
    var term = $("#searchBox").val();
    table1.search(term);
    table2.search(term);
    table1.draw();
    table2.draw();
}

function changeMode(self) {
    $(".mode-view").toggle();
    var outlier = $(self).val();
    $("#currentOutlier").val(outlier);
}


function saveAsExcel() {
    if ($("#currentOutlier").val() == 'record')
        table1.button(0).trigger();
    else
        table2.button(0).trigger();
}
function saveAsPDF() {
    if ($("#currentOutlier").val() == 'record')
        table1.button(1).trigger();
    else
        table2.button(1).trigger();
}

function getFileName() {
    return $("#currentOutlier").val() + "-" + $("#title").text().replaceAll(" ", "-").replaceAll("---", "-").toLowerCase() + "-" + Date.now();
}

function filterStatus() {
    var colInd = table1.columns().header().length - 1;
    var colInd2 = table2.columns().header().length - 1;
    if ($('#statusToggler').prop('checked')) {
        table1.column(colInd).search('Inactive').draw();
        table2.column(colInd2).search('Inactive').draw();
    } else {
        table1.column(colInd).search('Active').draw();
        table2.column(colInd2).search('Active').draw();
    }
}
