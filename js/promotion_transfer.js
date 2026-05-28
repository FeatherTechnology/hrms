$(document).ready(function () {

    // Initially hide all contents
    $('#promotion_transfer_content').hide();
    $('.promotion-card').hide();
    $('.transfer-card').hide();
    $('.increment-card').hide();
    $('#back_btn').hide();

    // Button click event
    $('.staff_status_btn').click(function () {

        let buttonClass = $(this).attr('class');
        $('.outer_search_card').hide();


        // Hide table list
        $('.promotion_transfer_table_content').hide();
        $('.outer_status_card').hide();

        // Show form content
        $('#promotion_transfer_content').show();

        // Show back button
        $('#back_btn').show();

        // First hide all sections
        $('.promotion-card').hide();
        $('.transfer-card').hide();
        $('.increment-card').hide();

        // Promotion
        if ($(this).hasClass('promo_status')) {

            $('.promotion-card').show();
            $('.branch_div').hide();

        }

        // Transfer
        else if ($(this).hasClass('trans_status')) {

            $('.transfer-card').show();
            $('.reporting_person_div').hide();
             $('.branch_div').hide();

        }

        // Increment
        else if ($(this).hasClass('inc_status')) {

            $('.increment-card').show();
            $('.reporting_person_div').hide();
            $('.branch_div').hide();

        }
    });

    // Promotion
    $('.promo_status').click(async function () {

        $('.promotion_transfer_table_content').hide();

        $('#promotion_transfer_content').show();

        $('#back_btn').show();

        // Hide all cards first
        $('.promotion-card').hide();
        $('.transfer-card').hide();
        $('.increment-card').hide();
        $('#effective_date').val('');

        // Show promotion section
        $('.promotion-card').show();

        // Call Edit Function
        await editStaffProfile();

    });


    // Transfer
    $('.trans_status').click(async function () {
        $('.promotion_transfer_table_content').hide();
        $('#promotion_transfer_content').show();
        $('#back_btn').show();
        $('#effective_date').val('');
        // Hide all cards first
        $('.promotion-card').hide();
        $('.transfer-card').hide();
        $('.increment-card').hide();
        // Show transfer section
        $('.transfer-card').show();
        // Call Edit Function
        await editStaffProfile();
    });


    // Increment
    $('.inc_status').click(async function () {

        $('.promotion_transfer_table_content').hide();
        $('#promotion_transfer_content').show();
        $('#back_btn').show();
        // Hide all cards first
        $('.promotion-card').hide();
        $('.transfer-card').hide();
        $('.increment-card').hide();
        $('#effective_date').val('');
        // Show increment section
        $('.increment-card').show();
        // Call Edit Function
        await editStaffProfile();
    });


    // Back Button
    $('#back_btn').click(function () {
        $('#promotion_transfer_content').hide();
        $('.promotion-card').hide();
        $('.transfer-card').hide();
        $('.increment-card').hide();
        $('.promotion_transfer_table_content').show();
        $('.outer_search_card').show();
        $('.outer_status_card').show();
        $('#back_btn').hide();

    });


    // Company Change
    $('#company_search').on('change', function () {

        let company_id = $('#company_search').val();
        let dept_id = $('#department_search').val();
        let status = $('#status_search').val();
        if (company_id) {
            getDepartmentList(company_id, '#department_search', '');
        } else {
            $('#department_search').html('<option value="">Select Department</option>');
            $('#staff_search').html('<option value="">Select Staff Name</option>');
        }
    });


    // Department Change
    $('#department_search').on('change', function () {

        let company_id = $('#company_search').val();
        let dept_id = $('#department_search').val();
        let status = $('#status_search').val();
        getStaffList(company_id, dept_id, status);

    });


    // Status Change
    $('#status_search').on('change', function () {

        let company_id = $('#company_search').val();
        let dept_id = $('#department_search').val();
        let status = $('#status_search').val();

        getStaffList(company_id, dept_id, status);

    });

    $('#view_staff').on('click', function () {

        let company_id = $('#company_search').val();
        let status = $('#status_search').val();
        let department_id = $('#department_search').val();
        let staff_id = $('#staff_search').val();

        if (!company_id || !status || !department_id || !staff_id) {
            swalError('Warning', 'Please Select All Fields!');
            return;
        }
        if (status == 1) {
            $('.outer_status_card').show();
        } else {
            $('.outer_status_card').hide();
        }

        getStaffTable(company_id, status, department_id, staff_id);
    });

    $('#designation').on('change', async function () {

        let selectedLevel = parseInt($('#designation option:selected').data('level'));
        let company_id = $('#company_name').val();
        await getReportingPerson(company_id, selectedLevel);

    });

    $(document).on('input', '.ctc_amount', function () {

        let totalCTC = parseFloat($('#total_ctc').val()) || 0;

        if (totalCTC <= 0) {
            swalError('Warning', 'Please Enter Total CTC First');
            $(this).val('');
            return false;
        }

        let currentAmount = parseFloat($(this).val()) || 0;
        let category = $(this).closest('tr').find('td:eq(3)').text().trim();

        let percentage = 0;
        // Only Salary rows calculate %
        if (category == 'Salary') {
            percentage = (currentAmount / totalCTC) * 100;
            $(this).closest('tr').find('.ctc_percentage').val(percentage.toFixed(2));
        } else {
            // Reimbursement no %
            $(this).closest('tr').find('.ctc_percentage').val('0');
        }

        calculateTotals($(this));
    });


    $('#staff_type').on('change', function () {
        toggleReportingField();
    });

    $('#branch_admin').on('change', function () {
        toggleBranchField();
    });

    $('#department').on('change', function () {
        let dept_id = $(this).val();
        if (dept_id) {
            getTeamList(dept_id, '')
        } else {
            $('#team').empty().append('<option value="">Select Team</option>');
        }
    });

    $('#submit_staff_data').click(async function (event) {
        event.preventDefault();
        let company_id = $('#company_search').val();
        let status = $('#status_search').val();
        let department_id = $('#department_search').val();
        let id = $('#staff_search').val();
        // Selected Status
        let occ_status = $('.staff_status_btn.active').data('value');
        // Common Fields
        let company_name = $('#company_name').val();
        let effective_date = $('#effective_date').val();
        let staff_id = $('#staff_auto_id').val();
        let staff_type = $('#staff_type').val();
        let data = ['company_name', 'effective_date'];
        // Promotion
        if (occ_status == 1) {
            data.push('designation');
            if ($('#staff_type').val() != '1') {
                data.push('reporting_person');
            }
        }
        // Transfer
        else if (occ_status == 2) {
            data.push('branch_name', 'department', 'team', 'branch_admin');
            if ($('#branch_admin').val() == '1') {
                data.push('branch');
            }
        }
        // Increment
        else if (occ_status == 3) {
            data.push(
                'pf_available', 'esi_available', 'pt_available', 'total_ctc');
        }

        //    GET CTC DETAILS
        let ctcDetails = [];
        if (occ_status == 3) {
            let totalCTC = parseFloat($('#total_ctc').val().replace(/,/g, '')) || 0;

            let tableTotalAmount = 0;
            let salaryTotalAmount = 0;

            let ctcRowCount = $('#ctc_info_table tbody tr').length;

            if (ctcRowCount == 0) {
                swalError('Warning', 'Please Fill CTC Info Table!');
                return false;
            }

            $('#ctc_info_table tbody tr').each(function () {

                let amount = parseFloat($(this).find('.ctc_amount').val().replace(/,/g, '')) || 0;
                let category = $(this).find('td:eq(3)').text().trim();

                tableTotalAmount += amount;

                // Only Salary Components
                if (category == 'Salary') {
                    salaryTotalAmount += amount;
                }
            });

            // Salary components must equal Total CTC
            if (salaryTotalAmount != totalCTC) {

                swalError(
                    'Warning',
                    'Salary Components Total Must Be Equal to Total CTC!'
                );

                return false;
            }

            $('#ctc_info_table tbody tr').each(function () {
                let ctc_id = $(this).find('.ctc_id').val();
                let ctc_amount = $(this).find('.ctc_amount').val();
                let ctc_percentage = $(this).find('.ctc_percentage').val();
                ctcDetails.push({ ctc_id: ctc_id, ctc_amount: ctc_amount, ctc_percentage: ctc_percentage });
            });

        }
        let isValid = true;
        data.forEach(function (entry) {
            let fieldIsValid = validateField(
                $('#' + entry).val(),
                entry
            );
            if (!fieldIsValid) {
                isValid = false;
            }
        });

        let branch_name = $('#branch_name').val();
        let department = $('#department').val();
        let team = $('#team').val();
        let designation = $('#designation').val();
        let off_type = $('#off_type').val();
        let reporting_person = $('#reporting_person').val();
        let branch_admin = $('#branch_admin').val();
        let branch = $('#branch').val();
        let total_ctc = $('#total_ctc').val().replace(/,/g, '');
        let annual_ctc = parseFloat(total_ctc || 0) * 12;
        let pf_available = $('#pf_available').val();
        let esi_available = $('#esi_available').val();
        let pt_available = $('#pt_available').val();
        let staff_profile_id = $('#staff_profile_id').val();
        let total_ctc_amount = $('#total_ctc_amount').val();

        if (isValid) {

            let postData = {
                occ_status: occ_status,
                company_name: company_name,
                effective_date: effective_date,
                branch_name: branch_name,
                department: department,
                team: team,
                designation: designation,
                off_type: off_type,
                reporting_person: reporting_person,
                branch_admin: branch_admin,
                branch: branch,
                pf_available: pf_available,
                esi_available: esi_available,
                pt_available: pt_available,
                total_ctc: total_ctc,
                annual_ctc: annual_ctc,
                staff_id: staff_id,
                staff_type: staff_type,
                staff_profile_id: staff_profile_id,
                total_ctc_amount: total_ctc_amount
            };

            // Send CTC only for Increment
            if (occ_status == 3) {
                postData.ctcDetails = JSON.stringify(ctcDetails);
            }

            try {
                const response = await $.post('api/promotion_transfer/submit_staff_data.php', postData);
                let result = JSON.parse(response);
                if (result.result == '1') {
                    swalSuccess('Success', 'Submitted Successfully!');
                    getStaffTable(company_id, status, department_id, id);
                    $('#promotion_transfer_content').hide();

                    $('.promotion-card').hide();
                    $('.transfer-card').hide();
                    $('.increment-card').hide();

                    $('.promotion_transfer_table_content').show();
                    $('.outer_search_card').show();
                    $('.outer_status_card').show();
                    $('#back_btn').hide();
                } else {
                    swalError('Error', 'Something Went Wrong');

                }

            } catch (error) {
                console.error(error);
                swalError('Error', 'Ajax Error Occurred');
            }
        }
    });


    // Active Button Class
    $('.staff_status_btn').click(function () {

        $('.staff_status_btn').removeClass('active');

        $(this).addClass('active');

    });


    $('#total_ctc').on('input', function () {

        let monthly_ctc = parseFloat($(this).val()) || 0;

        let annual_ctc = monthly_ctc * 12;

        $('#annual_ctc').val(moneyFormatIndia(annual_ctc));
    });



});// Document End

$(function () {
    getCompanyName('#company_search')
})

function getStaffTable(company_id, status, department_id, staff_id) {
    let params = { 'company_id': company_id, 'status': status, 'department_id': department_id, 'staff_id': staff_id };
    serverSideTable('#promotion_transfer_table', params, 'api/promotion_transfer/get_staff_list.php', " Staff List");
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


async function getStaffList(company_id, dept_id, status) {
    // Validation
    if (company_id == '' || dept_id == '' || status == '') {

        $('#staff_search').html('<option value="">Select Staff Name</option>');
        return false;
    }
    try {
        const response = await $.ajax({
            url: 'api/staff_creation/company_mapped_staff.php',
            type: 'POST',
            dataType: 'json',
            data: {
                company_id: company_id,
                dept_id: dept_id,
                status: status
            }
        });

        let option = '<option value="">Select Staff Name</option>';
        $.each(response, function (index, val) {
            option += ` <option value="${val.id}">   ${val.staff_name}  </option> `;
        });
        $('#staff_search').empty().append(option);
    } catch (error) {
        console.error(error);

    }
}

async function editStaffProfile() {
    let staff_id = $('#staff_search').val();
    try {
        const response = await $.post('api/staff_creation/staff_profile_data.php', { id: staff_id }, null, 'json');
        if (!response || response.length === 0) {
            console.error("No customer data returned.");
            return;
        }
        const data = response.staff;
        const ctcData = response.ctc;
        $('#staff_profile_id').val(data.id);
        $('#staff_auto_id').val(data.staff_id);
        await getCompanyName('#company_name')
        $('#company_name').val(data.company_id);
        $('#company_name').trigger('change');
        await getBranchList(data.company_id, '#branch_name,#branch');
        await getDepartmentList(data.company_id, '#department', data.department);
        await getDesignationList(data.company_id, data.designation);
        $('#staff_name').val(data.staff_name);
        $('#staff_type').val(data.staff_type);
        let occ_status = $('.staff_status_btn.active').data('value');
        if (occ_status == 1) {
            $('#staff_type').trigger('change');
        }
        $('#joining_date').val(data.joining_date);
        $('#company_name').prop('disabled', true);
        $('#staff_type').prop('disabled', true);
        $('#team').val('')
        $('#branch_admin').val('')
        $('#pf_available').val('')
        $('#pt_available').val('')
        $('#esi_available').val('')
        $('#total_ctc').val('')
        $('#total_ctc_percentage').val('')
        $('#total_ctc_amount').val('')
        await getCTCInfoTable(data.company_id);
    } catch (error) {
        console.error('Error in editStaffProfile:', error);
    }
}


function getCTCInfoTable(company_id) {
    return $.ajax({
        url: 'api/staff_creation/get_ctc_info.php',
        type: 'POST',
        dataType: 'json',
        data: { company_id: company_id },
        success: function (response) {

            let tr = '';

            response.forEach((row, index) => {

                tr += `
                    <tr>
                        <td>${index + 1}</td>

                        <td>
                            ${row.salary_component}
                            <input type="hidden" class="ctc_id" value="${row.id}">
                        </td>

                        <td>${row.component_classification}</td>

                        <td>
                            ${row.component_category}
                        </td>

                        <td>
                            <input type="text"
                                   class="form-control ctc_amount"
                                   id="ctc_amount_${row.id}"
                                   min="0">
                        </td>

                        <td>
                            <input type="text"
                                   class="form-control ctc_percentage"
                                   id="ctc_percentage_${row.id}"
                                   readonly>
                        </td>
                    </tr>
                `;
            });

            $('#ctc_info_table tbody').html(tr);
        }
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

        let appendBranchOption = '<option value="">Select Branch</option>';

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
function toggleBranchField() {

    if ($('#branch_admin').val() == '1') {   // Yes
        $('.branch_div').show();
    } else {
        $('.branch_div').hide();
        $('#branch').val('');
    }

}

function toggleReportingField() {
      $('#reporting_person').val('');
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

function calculateTotals(currentInput) {

    let totalAmount = 0;
    let totalPercentage = 0;

    let salaryAmount = 0;
    let salaryPercentage = 0;

    let enteredCTC = parseFloat($('#total_ctc').val()) || 0;

    $('#ctc_info_table tbody tr').each(function () {

        let amount = parseFloat($(this).find('.ctc_amount').val()) || 0;
        let percentage = parseFloat($(this).find('.ctc_percentage').val()) || 0;
        let category = $(this).find('td:eq(3)').text().trim();

        totalAmount += amount;
        totalPercentage += percentage;

        // // Only Salary validation
        if (category == 'Salary') {
            salaryAmount += amount;
            salaryPercentage += percentage;
        }
    });

    totalPercentage = Math.round(totalPercentage * 100) / 100;

    if (totalPercentage > 100) {
        totalPercentage = 100;
    }

    $('#total_ctc_amount').val(totalAmount);
    $('#total_ctc_percentage').val(totalPercentage);

    // Salary should not exceed CTC
    if (salaryAmount > enteredCTC) {

        swalError('Warning', 'Salary Components should not exceed Total CTC');

        currentInput.val('');
        currentInput.closest('tr').find('.ctc_percentage').val('');

        recalculateTotals();
        return false;
    }
    // Salary % should not exceed 100
    salaryPercentage = Math.min(enteredCTC > 0 ? (salaryAmount / enteredCTC) * 100 : 0, 100);
    if (salaryPercentage > 100) {

        swalError('Warning', 'Salary Percentage should not exceed 100');

        currentInput.val('');
        currentInput.closest('tr').find('.ctc_percentage').val('');

        recalculateTotals();
        return false;
    }
}
function recalculateTotals() {

    let totalAmount = 0;
    let totalPercentage = 0;

    $('.ctc_amount').each(function () {

        totalAmount += parseFloat($(this).val()) || 0;
    });

    $('.ctc_percentage').each(function () {

        totalPercentage += parseFloat($(this).val()) || 0;
    });

    // Round to 2 decimal places to avoid 100.01, 99.999, etc.
    totalPercentage = Math.round(totalPercentage * 100) / 100;
    // Optional: cap at 100% (if you want max 100 shown)
    if (totalPercentage > 100) {
        totalPercentage = 100;
    }

    $('#total_ctc_amount').val(totalAmount);

    $('#total_ctc_percentage').val(totalPercentage);
}
