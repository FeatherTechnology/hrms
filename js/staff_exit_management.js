$(document).ready(function () {
    $(document).on('click', '#back_btn', function () {
        $("#exit_detail_div input").css("border", "1px solid #cecece");
        $("#exit_detail_div select").css("border", "1px solid #cecece");
        $("#exit_detail_div textarea").css("border", "1px solid #cecece");
        swapTableAndCreation();

    });

    // Radio Change
    $('#view_staff').on('click', function () {

        let company_id = $('#company_search').val();
        let branch_id = $('#branch_search').val();
        let department_id = $('#department_search').val();

        if (!company_id && !branch_id && !department_id) {
            swalError('Warning', 'Please Select Atleast One Fields!');
            return;
        }

        getStaffTable(company_id, branch_id, department_id);
    });

    $('#company_search').on('change', function () {
        let company_id = $(this).val();
        if (company_id) {
            getBranchList(company_id, '#branch_search');
            getDepartmentList(company_id, '#department_search', '');
        } else {
            $('#branch_search').empty().append('<option value="">Select Branch Name</option>');
            $('#department_search').empty().append('<option value="">Select Department</option>');
        }
    });

    $('#staff_type').on('change', function () {
        toggleReportingField();
    });

    $(document).on('click', '.staffEditBtn', function () {
        let id = $(this).attr('value');
        $('#staff_profile_id').val(id);
        swapTableAndCreation();
        editStaffProfile(id)

    });

    $('#notice_per_served').on('change', function () {
        toggleNoticeField();
    });

    $(document).on('click', '.returnBtn', async function () {
        event.preventDefault();
        let id = $(this).val();
        try {

            let response = await $.ajax({
                url: "api/staff_creation/update_return_date.php",
                type: "POST",
                data: { id: id }
            });

            if (response.trim() == 'success') {
                swalSuccess('Success', 'Document Returned Successfully');
                getDocumentInfoTable();
            }

        } catch (error) {
            console.error(error);
        }

    });
    $('#submit_staff_exit').click(function (event) {

        event.preventDefault();

        let company_id = $('#company_search').val();
        let branch_id = $('#branch_search').val();
        let department_id = $('#department_search').val();

        // Check document rows and return date before submit
        let rowCount = $('#doc_info_table').DataTable().rows().count();

        if (rowCount > 0) {

            let allReturned = true;

            $('#doc_info_table tbody tr').each(function () {

                let returnDate = $(this).find('td:eq(5)').text().trim();

                if (returnDate === '' || returnDate === '-' || returnDate == null) {
                    allReturned = false;
                    return false; // break loop
                }
            });

            if (!allReturned) {
                swalError('Warning', 'All documents must be returned before Staff Exit!');
                return false;
            }
        }

        // Validation
        let staff_profile_id = $('#staff_profile_id').val();
        let notice_per_served = $('#notice_per_served').val();
        let last_wrk_day = $('#last_wrk_day').val();
        let exit_type = $('#exit_type').val();
        let reason = $('#reason').val();

        let data = ['notice_per_served', 'last_wrk_day', 'exit_type', 'reason'];
        let isValid = true;

        data.forEach(function (entry) {

            let fieldIsValid = validateField($('#' + entry).val(), entry);

            if (!fieldIsValid) {
                isValid = false;
            }
        });

        if (!isValid) {
            return false;
        }

        swalConfirm(
            'Are you sure?',
            'Do you want to submit this Staff Exit?',
            function () {

                $('#submit_staff_exit').prop('disabled', true);

                $.post(
                    'api/staff_creation/submit_staff_exit.php',
                    {
                        staff_profile_id: staff_profile_id,
                        notice_per_served: notice_per_served,
                        last_wrk_day: last_wrk_day,
                        exit_type: exit_type,
                        reason: reason
                    },
                    function (response) {

                        $('#submit_staff_exit').prop('disabled', false);

                        if (response == '1') {

                            swalSuccess('Success', 'Staff Exit Added Successfully!');

                            getStaffTable(company_id, branch_id, department_id);
                            swapTableAndCreation();

                        } else {

                            swalError('Error', 'Something Went Wrong');
                        }
                    }
                );
            }
        );

    });

});

function swapTableAndCreation() {
    if ($('.staff_exit_table_content').is(':visible')) {
        $('.staff_exit_table_content').hide();
        $('.outer_search_card').hide();
        $('#staff_exit_content').show();
        $('#back_btn').show();

    } else {
        $('.staff_exit_table_content').show();
        $('.outer_search_card').show();
        $('#staff_exit_content').hide();
        $('#back_btn').hide();
    }
}


$(function () {
    getCompanyName('#company_search')
})

function getStaffTable(company_id, branch_id, department_id) {

    let status = 1;
    let params = { 'company_id': company_id, 'branch_id': branch_id, 'department_id': department_id, 'status': status };
    serverSideTable('#staff_exit', params, 'api/staff_creation/staff_list.php', " Staff List");
}


async function editStaffProfile(id) {
    try {
        const response = await $.post('api/staff_creation/staff_profile_data.php', { id: id }, null, 'json');

        if (!response || response.length === 0) {
            console.error("No customer data returned.");
            return;
        }

        const data = response.staff;
        const ctcData = response.ctc;
        $('#staff_profile_id').val(id);
        $('#staff_auto_id').val(data.staff_id);
        await getCompanyName('#company_name')
        $('#company_name').val(data.company_id);
        $('#company_name').trigger('change');
        await getBranchList(data.company_id, '#branch_name,#branch');
        await getDepartmentList(data.company_id, '#department', data.department);
        await getDesignationList(data.company_id, data.designation);
        $('#staff_name').val(data.staff_name);
        $('#staff_type').val(data.staff_type);
        $('#joining_date').val(data.joining_date);
        $('#notice_period').val(data.notice_period);
        $('#branch_name').val(data.branch_id);
        $('#branch').val(data.branch);
        $('#department').val(data.department);
        $('#designation').val(data.designation);
        $('#off_type').val(data.off_type);
        $('#relieve_date').val(data.relieve_date);
        $('#branch_admin').val(moneyFormatIndia(data.branch_admin));

        let selectedLevel = parseInt(
            $('#designation option:selected').data('level')
        );

        await getTeamList(data.department, data.team)
        /* then set selected team */
        $('#team').val(data.team);

        await getReportingPerson(data.company_id, selectedLevel);
        $('#reporting_person').val(data.reporting_person);
        await getDocumentInfoTable();
        toggleBranchField()
        $('#staff_type').trigger('change');
        $('#branch_admin').trigger('change');

        $('#branch_name').prop('disabled', true);
        $('#company_name').prop('disabled', true);
        $('#department').prop('disabled', true);
        $('#team').prop('disabled', true);
        $('#designation').prop('disabled', true);
        $('#off_type').prop('disabled', true);
        $('#reporting_person').prop('disabled', true);
        $('#branch_admin').prop('disabled', true);
        $('#branch').prop('disabled', true);

        $('#notice_per_served').val('');
        $('#last_wrk_day').val('');
        $('#exit_type').val('');
        $('#reason').val('');
        toggleNoticeField()

    } catch (error) {
        console.error('Error in editStaffProfile:', error);
    }
}

async function getCompanyName(selector) {
    return new Promise((resolve, reject) => {
        $.post(
            "api/branch_creation/getCompanyName.php",
            {},

            function (response) {
                let dropdown = $(selector);
                dropdown.empty();
                dropdown.append('<option value="">Select Company Name</option>');
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

async function getBranchList(company_id, selector) {

    try {

        const response = await $.ajax({
            url: 'api/staff_creation/company_mapped_branches.php',
            data: { company_id: company_id },
            type: 'POST',
            dataType: 'json'
        });

        let appendBranchOption = '<option value="">Select Branch Name</option>';

        $.each(response, function (index, val) {

            appendBranchOption += `
                <option value="${val.id}">
                    ${val.branch_name}
                </option>
            `;
        });

        $(selector).empty().append(appendBranchOption);

    } catch (error) {

        console.error("Error loading branch list:", error);
    }
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

async function getTeamList(dep_id, selected_team = '') {
    let company_id = $('#company_name').val();

    try {
        const response = await $.ajax({
            url: 'api/staff_creation/company_mapped_team.php',
            type: 'POST',
            dataType: 'json',
            data: {
                dep_id: dep_id,
                company_id: company_id,
                selected_team: selected_team
            }
        });

        let teamOption = '<option value="">Select Team</option>';

        $.each(response, function (index, val) {
            teamOption += `
                <option value="${val.id}">
                    ${val.team_name}
                </option>
            `;
        });

        $('#team').empty().append(teamOption);

    } catch (error) {
        console.error("Error loading Team list:", error);
    }
}

async function getReportingPerson(company_id, selectedLevel) {

    try {

        if (!selectedLevel) {
            $('#reporting_person')
                .empty()
                .append('<option value="">Select Reporting Person</option>');

            return;
        }

        const response = await $.ajax({
            url: 'api/staff_creation/get_reporting_person.php',
            type: 'POST',
            dataType: 'json',
            data: {
                company_id: company_id,
                designation_level: selectedLevel
            }
        });

        let option =
            '<option value="">Select Reporting Person</option>';

        $.each(response, function (index, value) {

            option += `
                <option value="${value.id}">
                    ${value.staff_name} (${value.designation})
                </option>
            `;
        });

        $('#reporting_person').empty().append(option);

    } catch (error) {

        console.error(
            "Error loading Reporting Person:",
            error
        );
    }
}

async function getDocumentInfoTable() {

    let staff_id = $('#staff_auto_id').val();

    if (staff_id == '') return false;

    try {

        let response = await $.ajax({
            url: "api/staff_creation/document_list.php",
            type: "POST",
            data: { staff_id: staff_id },
            dataType: "json"
        });

        var columnMapping = [
            'sno',
            'doc_name',
            'doc_type',
            'upload',
            'created_date',
            'return_date',
            'info'
        ];

        appendDataToTable('#doc_info_table', response, columnMapping);
        setdtable('#doc_info_table', "Document Info List");

    } catch (error) {
        console.error("Document Table Error:", error);
    }
}
function toggleNoticeField() {
    if ($('#notice_per_served').val() == '1') {   // Yes
        $('.notice-div').show();
    } else {
        $('.notice-div').hide();
    }

}

function toggleBranchField() {

    if ($('#branch_admin').val() == '1') {   // Yes
        $('.branch_div').show();
    } else {
        $('.branch_div').hide();
        $('#branch').val('');
    }

}

function toggleReportingField() {
    if ($('#staff_type').val() != '1') {   // Yes
        $('.reporting_person_div').show();
    } else {
        $('.reporting_person_div').hide();
        $('#reporting_person').val('');
    }

}
async function getDesignationList(company_id, selected_designation = '') {
    try {
        const response = await $.ajax({
            url: 'api/staff_creation/company_mapped_designation.php',
            type: 'POST',
            dataType: 'json',
            data: {
                company_id: company_id,
                selected_designation: selected_designation
            }
        });

        let designationOption = '<option value="">Select Designation</option>';

        $.each(response, function (index, val) {
            designationOption += `
                <option 
                    value="${val.id}"
                    data-level="${val.designation_level}">
                    ${val.designation}
                </option>
            `;
        });

        $('#designation').empty().append(designationOption);

    } catch (error) {
        console.error("Error loading Designation list:", error);
    }
}