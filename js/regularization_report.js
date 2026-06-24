$(document).ready(function () {
    $('#from_date').change(function () {
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();
        if (from_date > to_date) {
            $('#to_date').val('');
        }
        $('#to_date').attr('min', from_date);
    });


    $('#company_id').on('change', function () {
        let company_id = $(this).val();
        if (company_id) {
            getDepartmentList(company_id, '#department_id', '');
        } else {
            $('#department_id').empty().append('<option value="">Select Department</option>');
        }
    });

    loadTableHeader('');
    
    $('#regularization_btn').click(function (event) {
        event.preventDefault();

        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();
        let company_id = $('#company_id').val();
        let department_id = $('#department_id').val();
        let status = $('#reg_status').val();

        if (
            from_date != '' &&
            to_date != '' &&
            company_id != '' &&
            department_id != '' &&
            status != ''
        ) {

            if ($.fn.DataTable.isDataTable('#regularization_report_table')) {
                $('#regularization_report_table').DataTable().clear().destroy();
            }

            $('#regularization_report_table tbody').empty();

            loadTableHeader(status);

            let data = {
                from_date: from_date,
                to_date: to_date,
                company_id: company_id,
                department_id: department_id,
                status: status
            };

            serverSideTable(
                '#regularization_report_table',
                data,
                'api/report_files/get_regularization_report.php'
            );

        } else {

            swalError('Please Fill All Fields!', 'All fields are required.');
        }
    });

});


$(function () {
    getCompanyName('#company_id')
})

async function getCompanyName(selector) {
    return new Promise((resolve, reject) => {
        $.post(
            "api/attendance_files/get_company_list.php",
            {},

            function (response) {
                let dropdown = $(selector);
                dropdown.empty();
                dropdown.append('<option value="">Select Company</option>');
                $.each(response, function (index, item) {
                    dropdown.append(
                        `<option value="${item.id}">${item.company_name}
                        </option>`,
                    );
                });

                resolve();
            },

            "json",
        ).fail(function (xhr, status, error) {
            reject(error);
        });
    });
}

async function getDepartmentList(company_id, selector, selected_dept = '') {
    try {
        const response = await $.ajax({
            url: 'api/staff_creation/company_mapped_department.php',
            type: 'POST',
            dataType: 'json',
            data: {
                company_id: company_id,
                selected_dept: selected_dept
            }
        });

        let deptOption = '<option value="">Select Department</option>';

        $.each(response, function (index, val) {
            deptOption += `<option value="${val.id}">${val.department_name}</option>`;
        });

        $(selector).empty().append(deptOption);

    } catch (error) {
        console.error(error);
    }
}


function loadTableHeader(status) {

    let header = `
    <tr>
        <th>S.NO</th>
        <th>Staff ID</th>
        <th>Staff Name</th>
        <th>Staff Type</th>
        <th>Company</th>
        <th>Branch</th>
        <th>Department</th>
        <th>Designation</th>
        <th>Request Type</th>
        <th>Leave Type</th>
        <th>Request Date</th>
        <th>From Date</th>
        <th>To Date</th>
        <th>Requested Days/Hrs</th>
        <th>Reason</th>
    `;

    // Pending
    if (status == '0') {

        header += `
            <th>Assigned To</th>
            <th>Status</th>
        `;
    }

    // Approved
    else if (status == '1') {

        header += `
            <th>Approved By</th>
            <th>Remarks</th>
            <th>Status</th>
        `;
    }

    // Cancelled
    else if (status == '2') {

        header += `
            <th>Cancelled By</th>
            <th>Cancel Date</th>
            <th>Remarks</th>
            <th>Status</th>
        `;
    }

    // If no status selected
    else {

        header += `
            <th>Status</th>
        `;
    }

    header += `</tr>`;

    $('#regularization_thead').html(header);
}