$(document).ready(function () {

    $('#reset_btn').click(function () {
        requestReportTable();
    })

});

function requestReportTable() {
    $('#due_list_report_table').DataTable().destroy();
    getUserAccess(function (downloadAccess) {
        let buttons = [];

        // Add Excel button only if download access is granted
        if (downloadAccess === 1) {
            excelTitle = "Interest Due List Report List";
            buttons.push({
                extend: 'excelHtml5',
                action: function (e, dt, button, config) {
                    excelExportAction(e, dt, button, config, excelTitle);
                }
            });
        }

        // Add column visibility button
        buttons.push({
            extend: 'colvis',
            collectionLayout: 'fixed four-column',
        });

        $('#due_list_report_table').DataTable({
            "order": [
                [0, "desc"]
            ],
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': 'api/interest_report_files/get_interest_due_list_report.php',
                'data': function (data) {
                    var search = $('input[type=search]').val();
                    data.search = search;
                    data.to_date = $('#to_date').val();
                }
            },
            dom: 'lBfrtip',
            buttons: buttons,  // Use the dynamically constructed buttons array
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api();

                // Remove formatting to get integer data for summation
                var intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                // Array of column indices to sum
                var columnsToSum = [];

                // Loop through each column index
                columnsToSum.forEach(function (colIndex) {
                    // Total over all pages for the current column
                    var total = api
                        .column(colIndex)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    // Update footer for the current column
                    $(api.column(colIndex).footer()).html(`<b>` + total.toLocaleString() + `</b>`);
                });
            }
        });
    });
}
