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

    $('#other_report_btn').click(function () {
        const from_date = $('#from_date').val();
        const to_date = $('#to_date').val();
        var sheet_type = $('#sheet_type').val();
        if ((from_date === '' || to_date === '')) {
            swalError('Please Fill Dates!', 'Both From and To dates are required.');
            return false;
        }
        if (sheet_type == '') {
            swalError('Warning', 'Select Balance Sheet Type');
            return false;
        }

        // Hide all tables and their DataTable wrappers
        $('#other_transaction_table, #expenses_table').hide();

        $('.dataTables_wrapper').hide();

        // Show the selected table and call its function
        if (sheet_type >= 1 && sheet_type <= 9) {
            $('#other_transaction_table').show();
            otherTransactionReportTable();
            $('#other_transaction_table_wrapper').show();
        } else if (sheet_type === '10') {
            $('#expenses_table').show();
            expenseReportTable();
            $('#expenses_table_wrapper').show();
        } else {
            swalError('Select a Balance Sheet', 'Please select a valid sheet type to view report.');
        }
    });

});

function otherTransactionReportTable() {
    $('#other_transaction_table').DataTable().destroy();
    $('#other_transaction_table').DataTable({
        "order": [
            [0, "Asc"]
        ],
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url': 'api/report_files/get_other_transaction_report.php',
            'data': function (data) {
                var search = $('input[type=search]').val();
                data.search = search;
                data.from_date = $('#from_date').val();
                data.to_date = $('#to_date').val();
                data.sheet_type = $('#sheet_type').val();
            }
        },
        dom: 'lBfrtip',
        buttons: [{
            extend: 'excel',
            title: "Contra Report List"
        },
        {
            extend: 'colvis',
            collectionLayout: 'fixed four-column',
        }
        ],
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
            var columnsToSum = [9];

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
}

function expenseReportTable() {
    $('#expenses_table').DataTable().destroy();
    $('#expenses_table').DataTable({
        "order": [
            [0, "Asc"]
        ],
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url': 'api/report_files/get_expense_report.php',
            'data': function (data) {
                var search = $('input[type=search]').val();
                data.search = search;
                data.from_date = $('#from_date').val();
                data.to_date = $('#to_date').val();
            }
        },
        dom: 'lBfrtip',
        buttons: [{
            extend: 'excel',
            title: "Contra Report List"
        },
        {
            extend: 'colvis',
            collectionLayout: 'fixed four-column',
        }
        ],
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
            var columnsToSum = [11];

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
}