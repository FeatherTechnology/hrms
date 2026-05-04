$(document).ready(function () {

    $('#from_date').change(function () {
        const fromDate = $(this).val();
        const toDate = $('#to_date').val();
        $('#to_date').attr('min', fromDate);

        // Check if from_date is greater than to_date
        if (toDate && fromDate > toDate) {
            $('#to_date').val(''); // Clear the invalid value
        }
    });

    $('#reset_btn').click(function () {
        // Get the values of the input fields
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var type = $('#type').val();
        var sel_screen = $('#sel_screen').val();

        // Check if all fields are selected
        if (from_date === '' || to_date === '' || type === '' || sel_screen === '') {
            // If any field is empty, show an alert
            swalError('Warning', 'Please select all required fields')
        } else {
            // If all fields are filled, reload the table
            // cancel_revoke_table.ajax.reload();
            cancelRevokeTable();
        }
    });

});

function cancelRevokeTable() {
    $('#cancel_revoke_table').DataTable().destroy();
    getUserAccess(function (downloadAccess) {
        let buttons = [];

        // Add Excel button only if download access is granted
        if (downloadAccess === 1) {
            excelTitle = "Interest Cancel Revoke Report List";
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

        $('#cancel_revoke_table').DataTable({
            "order": [
                [0, "desc"]
            ],
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'ajax': {
                'url': 'api/interest_report_files/get_interest_cancel_revoke_report.php',
                'data': function (data) {
                    var search = $('input[type=search]').val();
                    data.search = search;
                    data.from_date = $('#from_date').val();
                    data.to_date = $('#to_date').val();
                    data.type = $('#type').val();
                    data.sel_screen = $('#sel_screen').val();
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
                var columnsToSum = [6];

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
