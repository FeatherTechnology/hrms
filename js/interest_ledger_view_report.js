$(document).ready(function () {

    //Closed Report Table
    $('#ledger_view_report_btn').click(function () {
        let toDate = $('#to_date').val();
        let loan_category = $('#loan_category').val();

        if (!toDate || !loan_category) {
            swalError('Missing Fields', 'Both Date and Loan Category are required.');
            return;
        }

        url = 'api/interest_report_files/get_interest_ledger_view_report.php';

        $.ajax({
            type: "POST",
            url: url,
            data: { toDate: toDate, loan_category: loan_category },
            success: function (data) {
                $('.reportDiv').empty().html(data);
            }
        });
    });

    // <------------------------------------------------------------------------ Due Chart Start --------------------------------------------------------------->

    $(document).on('click', '.due-chart', async function () {

        var cus_id = $(this).data('cusid');
        var cp_id = $(this).data('loanid');

        // Wait for chart to be rendered
        await dueChartList(cp_id, cus_id);

        // Bind print click event after content is loaded
        $('.print_due_coll').off('click').on('click', async function () {
            var id = $(this).attr('value');
            const result = await Swal.fire({
                title: 'Print',
                text: 'Do you want to print this collection?',
                imageUrl: 'img/printer.png',
                imageWidth: 300,
                imageHeight: 210,
                imageAlt: 'Custom image',
                showCancelButton: true,
                confirmButtonColor: '#009688',
                cancelButtonColor: '#d33',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes'
            });

            if (result.isConfirmed) {
                try {
                    const html = await new Promise((resolve, reject) => {
                        $.ajax({
                            url: 'api/collection_files/print_collection.php',
                            data: { 'coll_id': id },
                            type: 'POST',
                            cache: false,
                            success: resolve,
                            error: reject
                        });
                    });

                    $('#printcollection').html(html);
                    const content = $("#printcollection").html(); // Optional usage
                } catch (err) {
                    console.error('Print load failed:', err);
                }
            }
        });
    });


    $(document).on('click', '.due-chart', function () {
        $('#due_chart_model').modal('show');
    });

});

// <------------------------------------------------------------------------ Due Chart Function Start --------------------------------------------------------------->

function dueChartList(cp_id, cus_id) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'api/collection_files/get_due_chart_list.php',
            data: { 'cp_id': cp_id, 'cus_id': cus_id },
            type: 'POST',
            cache: false,
            success: function (response) {
                $('#due_chart_table_div').empty().html(response);

                // Now get the method name
                $.post('api/collection_files/get_due_method_name.php', { cp_id }, function (response) {
                    $('#dueChartTitle').text('Due Chart ( ' + response['due_method'] + ' - ' + response['loan_type'] + ' )');
                    resolve();
                }, 'json').fail(reject);
            },
            error: reject
        });
    });
}

function closeChartsModal() {
    $('#due_chart_model').modal('hide');
}

// <------------------------------------------------------------------------ Loan Category Function Start --------------------------------------------------------------->

$(function () {
    getLoanCategoryDropdown();
});

function getLoanCategoryDropdown() {
    return new Promise((resolve, reject) => {
        $.post('api/loan_category_creation/get_loan_category_list.php', function (response) {
            let appendLineNameOption = '';
            let loan_category2 = $('#loan_category2').val();
            appendLineNameOption += '<option value="">Select Loan Category</option>';

            $.each(response, function (index, val) {
                let selected = (val.id == loan_category2) ? 'selected' : '';
                appendLineNameOption += `<option value="${val.id}" ${selected}>${val.loan_category}</option>`;
            });

            $('#loan_category').empty().append(appendLineNameOption);
            resolve(); // Resolve once done
        }, 'json').fail(function (err) {
            reject(err); // Reject on error
        });
    });
}