$(document).ready(function () {
    $('#from_date').change(function () {
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();
        if(from_date > to_date){
            $('#to_date').val('');
        }
        $('#to_date').attr('min', from_date);
    });

    $('#interest_loan_issue_report_btn').click(function (event) {
        event.preventDefault();
        let data = {
            'from_date' : $('#from_date').val(),
            'to_date' : $('#to_date').val()
        };

        serverSideTable('#interest_loan_issue_report_table', data, 'api/interest_report_files/get_interest_loan_issue_report.php', 'Interest Loan Issue Report List');

    });

});
