$(document).ready(function () {
    $(document).on('click', '#add_staff, #back_btn', function () {
        swapTableAndCreation();

    });

    $('#add_staff').click(async function () {
        getStateList();
        $('.staff_content').hide();
        await autoGenStaffId();
        await getDocumentInfoTable();
        await getFamilyInfoTable();
        await getQualificationInfoTable();
        await getExperienceInfoTable();
        await getCompanyName()
    });
    $('#back_btn').click(function () {
        let staff_id = $('#staff_auto_id').val();
        let staff_profile_id = $('#staff_profile_id').val();
        $('.staff_content').show();
        $.post('api/staff_creation/staff_sts_check.php', { 'staff_id': staff_id, 'staff_profile_id': staff_profile_id }, function (response) {
            if (response.status == 0) {
                // If status is 0, proceed with confirmation
                swalConfirm('Warning', 'Are you sure you want to go back? Personal information will be lost because the staff profile is incomplete.', staffDeleteStatus, staff_id);
                return;

            } else {
                // Do nothing if cancelled
                swapTableAndCreation();
            }

        }, 'json');
    });

    $('#mobile1, #mobile2, #whatsapp_no, #fam_mobile,#whatsapp').change(function () {
        checkMobileNo($(this).val(), $(this).attr('id'));
    });


    $('#mailid').on('change', function () {
        validateEmail($(this).val(), $(this).attr('id'));
    });

    $('#company_name').on('change', function () {
        let company_id = $(this).val();
        if (company_id) {
            getBranchList(company_id);
            getDepartmentList(company_id);
            getDesignationList(company_id);
            getShiftList(company_id);
            getCTCInfoTable(company_id)
        } else {
            $('#branch,#branch_name').empty().append('<option value="">Select Branch Name</option>');
            $('#department').empty().append('<option value="">Select Department</option>');
            $('#designation').empty().append('<option value="">Select Designation</option>');
            $('#shift').empty().append('<option value="">Select Shift</option>');
            // Reset CTC Table
            $('#ctc_info_table tbody').empty();

            // Reset Total Fields
            $('#total_ctc_amount').val('');
            $('#total_ctc_percentage').val('');
        }
    });

    $('#designation').on('change', function () {

        let selectedLevel = parseInt(
            $('#designation option:selected').data('level')
        );

        let company_id = $('#company_name').val();

        if (!selectedLevel) {

            $('#reporting_person')
                .empty()
                .append('<option value="">Select Reporting Person</option>');

            return;
        }

        $.post(
            'api/staff_creation/company_mapped_designation.php',
            {
                company_id: company_id,
                designation_level: selectedLevel
            },
            function (response) {

                let option = '';

                option += '<option value="">Select Reporting Person</option>';

                $.each(response, function (index, value) {

                    option += `
                    <option value="${value.id}">
                        ${value.designation}
                    </option>
                `;
                });

                $('#reporting_person').html(option);

            },
            'json'
        );
    });

    $('#pic').change(function () {
        let pic = $('#pic')[0];
        let img = $('#imgshow');
        compressImage(this, 200)
        img.attr('src', URL.createObjectURL(pic.files[0]));
    })

    $('#marital_status').on('change', function () {
        toggleSpouseField();
    });

    $('#branch_admin').on('change', function () {
        toggleBranchField();
    });

    $('#staff_type').on('change', function () {
        toggleReportingField();
    });

    $('#ot_payment').on('change', function () {
        toggleOTField();
    });

    $('#state').change(function () {
        getDistrictList($(this).val());
    });

    /// Document Info

    /////Document Modal////
    $('#submit_document').click(function (event) {
        event.preventDefault();
        //Validation
        let staff_id = $('#staff_auto_id').val();
        let staff_profile_id = $('#staff_profile_id').val();
        let upload = $('#upload')[0].files[0]; let doc_upload = $('#doc_upload').val();
        let doc_name = $('#doc_name').val(); let doc_type = $("#doc_type").val(); let document_id = $('#document_id').val();

        var data = ['doc_name', 'doc_type']
        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
        if (isValid) {
            let docDetail = new FormData();
            docDetail.append('doc_name', doc_name);
            docDetail.append('doc_type', doc_type);
            docDetail.append('document_id', document_id);
            docDetail.append('upload', upload);
            docDetail.append('doc_upload', doc_upload);

            docDetail.append('staff_id', staff_id);
            docDetail.append('staff_profile_id', staff_profile_id);
            $.ajax({
                url: 'api/staff_creation/submit_document.php',
                type: 'post',
                data: docDetail,
                contentType: false,
                processData: false,
                cache: false,
                success: function (response) {
                    if (response == '2') {
                        swalSuccess('Success', 'Document Info Added Successfully!');
                    } else if (response == '1') {
                        swalSuccess('Success', 'Document Info Updated Successfully!');
                    } else {
                        swalError('Error', 'Error in Document Info Table');
                    }
                    getDocumentTable();
                }
            });
        }
    });

    // Edit Document Info
    $(document).on('click', '.documentActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/staff_creation/document_creation_data.php', { id: id }, function (response) {
            $('#document_id').val(id);
            $('#doc_name').val(response[0].doc_name);
            $('#doc_type').val(response[0].doc_type);
            $('#doc_upload').val(response[0].upload);
        }, 'json');
    });

    // Delete Document Info

    $(document).on('click', '.documentDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the Document?', getDocumentDelete, id);
        return;
    });
    // Document Info End

    /////family Modal////
    $('#submit_family').click(function (event) {
        event.preventDefault();
        // Validation

        let staff_id = $('#staff_auto_id').val();
        let staff_profile_id = $('#staff_profile_id').val();
        let fam_name = $('#fam_name').val();
        let fam_dob = $('#fam_dob').val();
        let fam_relationship = $('#fam_relationship').val();
        let fam_occupation = $('#fam_occupation').val();
        let fam_mobile = $('#fam_mobile').val();
        let family_id = $('#family_id').val();


        var data = ['fam_name', 'fam_relationship', 'fam_dob', 'fam_occupation', 'fam_mobile']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });

        if (isValid) {
            $.post('api/staff_creation/submit_family_info.php', { staff_id, staff_profile_id, fam_name, fam_relationship, fam_dob, fam_occupation, fam_mobile, family_id }, function (response) {
                if (response == '2') {
                    swalSuccess('Success', 'Family Info Added Successfully!');
                } else if (response == '1') {
                    swalSuccess('Success', 'Family Info Updated Successfully!');
                } else {
                    swalError('Error', 'Error in Family Info Table');
                }
                // Refresh the family table
                getFamilyTable();
            });
        }
    });

    $(document).on('click', '.familyActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/staff_creation/family_creation_data.php', { id: id }, function (response) {
            $('#family_id').val(id);
            $('#fam_name').val(response[0].fam_name);
            $('#fam_relationship').val(response[0].fam_relationship);
            $('#fam_dob').val(response[0].fam_dob);
            $('#fam_occupation').val(response[0].fam_occupation);
            $('#fam_mobile').val(response[0].fam_mobile);
        }, 'json');
    });

    $(document).on('click', '.familyDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the Family Details?', getFamilyDelete, id);
        return;
    });
    // Family Info End
    // Qualification Info Start
    $('#submit_qualification').click(function (event) {
        event.preventDefault();
        // Validation

        let staff_id = $('#staff_auto_id').val();
        let staff_profile_id = $('#staff_profile_id').val();
        let highest_qualification = $('#highest_qualification').val();
        let degree = $('#degree').val();
        let specialization = $('#specialization').val();
        let college = $('#college').val();
        let university = $('#university').val();
        let year_of_passing = $('#year_of_passing').val();
        let qualification_id = $('#qualification_id').val();


        var data = ['highest_qualification', 'degree', 'specialization', 'college', 'university', 'year_of_passing']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });

        if (isValid) {
            $.post('api/staff_creation/submit_qualification_info.php', { staff_id, highest_qualification, degree, specialization, college, university, year_of_passing, qualification_id, staff_profile_id }, function (response) {
                if (response == '2') {
                    swalSuccess('Success', 'Qualification Info Added Successfully!');
                } else if (response == '1') {
                    swalSuccess('Success', 'Qualification Info Updated Successfully!');
                } else {
                    swalError('Error', 'Error in Qualification Info Table');
                }
                // Refresh the qualification table
                getQualificationTable();
            });
        }
    });

    $(document).on('click', '.qualifyActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/staff_creation/qualification_creation_data.php', { id: id }, function (response) {
            $('#qualification_id').val(id);
            $('#highest_qualification').val(response[0].highest_qualification);
            $('#degree').val(response[0].degree);
            $('#specialization').val(response[0].specialization);
            $('#college').val(response[0].college);
            $('#university').val(response[0].university);
            $('#year_of_passing').val(response[0].year_of_passing);
        }, 'json');
    });

    $(document).on('click', '.qualifyDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the Qualification Details?', getQualificationDelete, id);
        return;
    });
    // Qualification Info End

    // Experience Info Start
    $('#submit_experience').click(function (event) {
        event.preventDefault();
        // Validation

        let staff_id = $('#staff_auto_id').val();
        let staff_profile_id = $('#staff_profile_id').val();
        let exp_type = $('#exp_type').val();
        let total_experience = $('#total_experience').val();
        let pre_company = $('#pre_company').val();
        let pre_designation = $('#pre_designation').val();
        let work_duration = $('#work_duration').val();
        let last_salary = $('#last_salary').val();
        let reason_for_leaving = $('#reason_for_leaving').val();
        let experience_id = $('#experience_id').val();


        var data = ['exp_type', 'total_experience', 'pre_company', 'pre_designation', 'work_duration', 'last_salary', 'reason_for_leaving']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });

        if (isValid) {
            $.post('api/staff_creation/submit_experience_info.php', { staff_id, exp_type, total_experience, pre_company, pre_designation, work_duration, last_salary, reason_for_leaving, experience_id, staff_profile_id }, function (response) {
                if (response == '2') {
                    swalSuccess('Success', 'Experience Info Added Successfully!');
                } else if (response == '1') {
                    swalSuccess('Success', 'Experience Info Updated Successfully!');
                } else {
                    swalError('Error', 'Error in Experience Info Table');
                }
                // Refresh the experience table
                getExperienceTable();
            });
        }
    });

    $(document).on('click', '.expActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/staff_creation/experience_creation_data.php', { id: id }, function (response) {
            $('#experience_id').val(id);
            $('#exp_type').val(response[0].exp_type);
            $('#total_experience').val(response[0].total_experience);
            $('#pre_company').val(response[0].pre_company);
            $('#pre_designation').val(response[0].pre_designation);
            $('#work_duration').val(response[0].work_duration);
            $('#last_salary').val(response[0].last_salary);
            $('#reason_for_leaving').val(response[0].reason_for_leaving);
        }, 'json');
    });

    $(document).on('click', '.expDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the Experience Details?', getExperienceDelete, id);
        return;
    });
    // Experience Info End
    // Submit Staff Creation BASIC Info
    $('#submit_staff').click(function (event) {
        event.preventDefault();
        // Validate form fields
        let pic = $('#pic')[0].files[0];
        let per_pic = $('#per_pic').val();
        let staff_id = $('#staff_auto_id').val();
        let staff_name = $('#staff_name').val();
        let staff_type = $("#staff_type").val();
        let address = $("#address").val();
        let state = $("#state").val();
        let district = $("#district").val();
        let place = $("#place").val();
        let pincode = $("#pincode").val();
        let dob = $('#dob').val();
        let blood_group = $('#blood_group').val();
        let gender = $('#gender').val();
        let marital_status = $('#marital_status').val();
        let spouse_name = $('#spouse_name').val();
        let anniversary_date = $('#anniversary_date').val();
        let joining_date = $('#joining_date').val();
        let relieve_date = $('#relieve_date').val();
        let notice_period = $('#notice_period').val();
        let pf_available = $('#pf_available').val();
        let esi_available = $('#esi_available').val();
        let pt_available = $('#pt_available').val();
        let staff_profile_id = $('#staff_profile_id').val();


        var data = ['staff_auto_id', 'staff_name', 'staff_type', 'address', 'state', 'district', 'place', 'pincode', 'gender', 'marital_status', 'joining_date']
        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
        if (pic === undefined && per_pic === '') {
            let isUploadValid = validateField('', 'pic');
            let isHiddenValid = validateField('', 'per_pic');
            if (!isUploadValid || !isHiddenValid) {
                isValid = false;
            }
            else {
                $('#pic').css('border', '1px solid #cecece');
                $('#per_pic').css('border', '1px solid #cecece');
            }
        }
        else {
            $('#pic').css('border', '1px solid #cecece');
            $('#per_pic').css('border', '1px solid #cecece');
        }

        if (isValid) {
            let personalDetail = new FormData();
            personalDetail.append('staff_id', staff_id);
            personalDetail.append('staff_name', staff_name);
            personalDetail.append('staff_type', staff_type);
            personalDetail.append('address', address);
            personalDetail.append('state', state);
            personalDetail.append('district', district);
            personalDetail.append('place', place);
            personalDetail.append('pincode', pincode);
            personalDetail.append('dob', dob);
            personalDetail.append('blood_group', blood_group);
            personalDetail.append('gender', gender);
            personalDetail.append('marital_status', marital_status);
            personalDetail.append('spouse_name', spouse_name);
            personalDetail.append('anniversary_date', anniversary_date);
            personalDetail.append('joining_date', joining_date);
            personalDetail.append('relieve_date', relieve_date);
            personalDetail.append('notice_period', notice_period);
            personalDetail.append('pf_available', pf_available);
            personalDetail.append('esi_available', esi_available);
            personalDetail.append('pt_available', pt_available);
            personalDetail.append('pic', pic);
            personalDetail.append('per_pic', per_pic);
            personalDetail.append('staff_profile_id', staff_profile_id);
            $.ajax({
                url: 'api/staff_creation/submit_personal_info.php',
                type: 'POST',
                data: personalDetail,
                contentType: false,
                processData: false,
                cache: false,
                dataType: 'json',
                success: function (response) {
                    // Handle success response
                    if (response.result == 0) {
                        swalError('Error', 'Personal Info Not Added!');
                    } else if (response.result == 1) {
                        swalSuccess('Success', 'Personal Info Added Successfully!');
                        $('.staff_content').show();
                        $('#staff_profile_id').val(response.last_id);
                        $('#per_pic').val(response.pic);
                        $('.personal_info_disble').attr("disabled", true);
                        $('#submit_staff').attr("disabled", true);
                    }

                },
            });

        }
    })
    // Submit Staff Creation BASIC Info
    $('#submit_staff_creation').click(function (event) {
        event.preventDefault();
        // Validate form fields
        let famInfoRowCount = $('#fam_info_table').DataTable().rows().count();
        let qualInfoRowCount = $('#qual_info_table').DataTable().rows().count();
        let ExpInfoRowCount = $('#exp_info_table').DataTable().rows().count();
        let pic = $('#pic')[0].files[0];
        let per_pic = $('#per_pic').val();
        let staff_id = $('#staff_auto_id').val();
        let staff_name = $('#staff_name').val();
        let staff_type = $("#staff_type").val();
        let address = $("#address").val();
        let state = $("#state").val();
        let district = $("#district").val();
        let place = $("#place").val();
        let pincode = $("#pincode").val();
        let dob = $('#dob').val();
        let blood_group = $('#blood_group').val();
        let gender = $('#gender').val();
        let marital_status = $('#marital_status').val();
        let spouse_name = $('#spouse_name').val();
        let anniversary_date = $('#anniversary_date').val();
        let joining_date = $('#joining_date').val();
        let relieve_date = $('#relieve_date').val();
        let notice_period = $('#notice_period').val();
        let pf_available = $('#pf_available').val();
        let esi_available = $('#esi_available').val();
        let pt_available = $('#pt_available').val();
        let email = $('#mailid').val();
        let mobile1 = $('#mobile1').val();
        let mobile2 = $('#mobile2').val();
        let whatsapp = $('#whatsapp').val();
        let instagram = $('#instagram').val();
        let facebook = $('#facebook').val();
        let acc_holder_name = $('#acc_holder_name').val();
        let bank_name = $('#bank_name').val();
        let acc_number = $('#acc_number').val();
        let ifsc_code = $('#ifsc_code').val();
        let bank_branch = $('#bank_branch').val();
        let company_name = $('#company_name').val();
        let branch_name = $('#branch_name').val();
        let department = $('#department').val();
        let team = $('#team').val();
        let designation = $('#designation').val();
        let reporting_person = $('#reporting_person').val();
        let branch_admin = $('#branch_admin').val();
        let branch = $('#branch').val();
        let total_ctc = $('#total_ctc').val();
        let annual_ctc = $('#annual_ctc').val();
        let shift = $('#shift').val();
        let ot_payment = $('#ot_payment').val();
        let ot_per_hour = $('#ot_per_hour').val();
        let ot_per_day = $('#ot_per_day').val();
        let staff_profile_id = $('#staff_profile_id').val();


        var data = ['staff_auto_id', 'staff_name', 'staff_type', 'address', 'state', 'district', 'place', 'pincode', 'gender', 'marital_status', 'joining_date']
        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
        if (pic === undefined && per_pic === '') {
            let isUploadValid = validateField('', 'pic');
            let isHiddenValid = validateField('', 'per_pic');
            if (!isUploadValid || !isHiddenValid) {
                isValid = false;
            }
            else {
                $('#pic').css('border', '1px solid #cecece');
                $('#per_pic').css('border', '1px solid #cecece');
            }
        }
        else {
            $('#pic').css('border', '1px solid #cecece');
            $('#per_pic').css('border', '1px solid #cecece');
        }



        if (isValid) {
            // CTC Table Validation
            let totalCTC = parseFloat($('#total_ctc').val()) || 0;

            let tableTotalAmount = 0;

            let ctcRowCount = $('#ctc_info_table tbody tr').length;

            if (ctcRowCount == 0) {

                swalError('Warning', 'Please Fill CTC Info Table!');

                return false;
            }

            // Check Empty Amount
            // CTC Total Validation
            let tableTotalAmount = 0;

            $('.ctc_amount').each(function () {

                tableTotalAmount += parseFloat($(this).val()) || 0;
            });

            if (totalCTC != tableTotalAmount) {

                swalError(
                    'Warning',
                    'Total CTC and CTC Table Total Amount Must Be Equal!'
                );

                return false;
            }
            if (famInfoRowCount === 0 || qualInfoRowCount === 0 || ExpInfoRowCount === 0) {
                swalError('Warning', 'Please Fill out Family Info and Qualification Info and Experience Info!');
                return false;
            }
            let staffDetail = new FormData();
            staffDetail.append('staff_id', staff_id);
            staffDetail.append('staff_name', staff_name);
            staffDetail.append('staff_type', staff_type);
            staffDetail.append('address', address);
            staffDetail.append('state', state);
            staffDetail.append('district', district);
            staffDetail.append('place', place);
            staffDetail.append('pincode', pincode);
            staffDetail.append('dob', dob);
            staffDetail.append('blood_group', blood_group);
            staffDetail.append('gender', gender);
            staffDetail.append('marital_status', marital_status);
            staffDetail.append('spouse_name', spouse_name);
            staffDetail.append('anniversary_date', anniversary_date);
            staffDetail.append('joining_date', joining_date);
            staffDetail.append('relieve_date', relieve_date);
            staffDetail.append('notice_period', notice_period);
            staffDetail.append('pf_available', pf_available);
            staffDetail.append('esi_available', esi_available);
            staffDetail.append('pt_available', pt_available);
            staffDetail.append('pic', pic);
            staffDetail.append('per_pic', per_pic);
            staffDetail.append('email', email);
            staffDetail.append('mobile1', mobile1);
            staffDetail.append('mobile2', mobile2);
            staffDetail.append('whatsapp', whatsapp);
            staffDetail.append('instagram', instagram);
            staffDetail.append('facebook', facebook);
            staffDetail.append('acc_holder_name', acc_holder_name);
            staffDetail.append('bank_name', bank_name);
            staffDetail.append('acc_number', acc_number);
            staffDetail.append('ifsc_code', ifsc_code);
            staffDetail.append('bank_branch', bank_branch);
            staffDetail.append('company_name', company_name);
            staffDetail.append('branch_name', branch_name);
            staffDetail.append('department', department);
            staffDetail.append('team', team);
            staffDetail.append('designation', designation);
            staffDetail.append('reporting_person', reporting_person);
            staffDetail.append('branch_admin', branch_admin);
            staffDetail.append('branch', branch);
            staffDetail.append('total_ctc', total_ctc);
            staffDetail.append('annual_ctc', annual_ctc);
            staffDetail.append('shift', shift);
            staffDetail.append('ot_payment', ot_payment);
            staffDetail.append('ot_per_hour', ot_per_hour);
            staffDetail.append('ot_per_day', ot_per_day);
            let ctcDetails = [];

            $('#ctc_info_table tbody tr').each(function () {

                let ctc_id = $(this).find('.ctc_id').val();

                let ctc_amount = $(this).find('.ctc_amount').val();

                let ctc_percentage = $(this).find('.ctc_percentage').val();

                ctcDetails.push({
                    ctc_id: ctc_id,
                    ctc_amount: ctc_amount,
                    ctc_percentage: ctc_percentage
                });
            });

            staffDetail.append(
                'ctcDetails',
                JSON.stringify(ctcDetails)
            );

            staffDetail.append('staff_profile_id', staff_profile_id);
            swalConfirm(
                'Are you sure?',
                'Do you want to submit this Staff Creation?',
                function () {

                    $.ajax({
                        url: 'api/staff_creation/submit_staff_info.php',
                        type: 'POST',
                        data: staffDetail,
                        contentType: false,
                        processData: false,
                        cache: false,
                        dataType: 'json',

                        success: function (response) {

                            if (response.result == 0) {

                                swalError(
                                    'Error',
                                    'Staff Info Not Added!'
                                );

                            } else if (response.result == 1) {

                                swalSuccess(
                                    'Success',
                                    'Staff Info Updated Successfully!'
                                );

                                $('.staff_content').show();

                                $('#staff_profile_id').val(response.last_id);

                                $('#per_pic').val(response.pic);

                                $('#submit_staff_creation').attr('disabled', true);
                            }
                        },

                        error: function () {

                            swalError(
                                'Error',
                                'Something went wrong!'
                            );
                        }
                    });
                }
            );

        }
    })

    $('#total_ctc').on('input', function () {

        let monthly_ctc = parseFloat($(this).val()) || 0;

        let annual_ctc = monthly_ctc * 12;

        $('#annual_ctc').val(annual_ctc);
    });

    $('#ot_payment').change(function () {

        let ot_payment = $(this).val();
        let total_ctc = parseFloat($('#total_ctc').val()) || 0;

        if (ot_payment == '1') {

            $('.ot_per_hour_div').show();

            // Monthly salary to hourly salary
            let ot_per_hour = total_ctc / 30 / 8;

            $('#ot_per_hour').val(ot_per_hour.toFixed(2));

        } else {

            $('.ot_per_hour_div').hide();
            $('#ot_per_hour').val('');
        }
    });


    $(document).on('input', '.ctc_amount', function () {

        let totalCTC = parseFloat($('#total_ctc').val()) || 0;

        // Validation
        if (totalCTC <= 0) {

            swalError('Warning', 'Please Enter Total CTC First');

            $(this).val('');

            return false;
        }

        let currentAmount = parseFloat($(this).val()) || 0;

        let percentage = 0;

        if (totalCTC > 0) {
            percentage = (currentAmount / totalCTC) * 100;
        }

        // Set percentage
        $(this)
            .closest('tr')
            .find('.ctc_percentage')
            .val(percentage + '%');

        // Calculate totals
        calculateTotals($(this));
    });

});

function swapTableAndCreation() {
    if ($('.staff_table_content').is(':visible')) {
        $('.staff_table_content').hide();
        $('#add_staff').hide();
        $('#staff_creation_content').show();
        $('#back_btn').show();

    } else {
        $('.staff_table_content').show();
        $('#add_staff').show();
        $('#staff_creation_content').hide();
        $('#back_btn').hide();
    }
}
async function autoGenStaffId(id = '') {

    try {

        let response = await $.ajax({
            url: "api/staff_creation/get_autostaff_id.php",
            type: "POST",
            data: { id: id },
            dataType: "json",
            cache: false
        });

        $('#staff_auto_id').val(response.staff_id);

    } catch (error) {
        console.error("AJAX Error:", error);
    }
}

function toggleSpouseField() {

    if ($('#marital_status').val() == '1') {   // Yes
        $('.spouse-div').show();
    } else {
        $('.spouse-div').hide();
        $('#spouse_name').val('');
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
function toggleOTField() {
    $('#ot_per_day').val('')

    if ($('#ot_payment').val() == '1') {   // Yes
        $('.ot_per_hour_div').show();
        $('.ot_per_day_div').hide();
    } else {
        $('.ot_per_hour_div').hide();
        $('.ot_per_day_div').show();
    }

}

// Get Document  Table
function getDocumentTable() {
    let staff_id = $('#staff_auto_id').val();
    $.post('api/staff_creation/document_list.php', { staff_id }, function (response) {
        var columnMapping = [
            'sno',
            'doc_name',
            'doc_type',
            'upload',
            'created_date',
            'return_date',
            'action'
        ];
        appendDataToTable('#document_table', response, columnMapping);
        setdtable('#document_table', "Document Info List");
        $('#document_form input').val('');
        $('#document_form input').css('border', '1px solid #cecece');
        $('#document_form select').css('border', '1px solid #cecece');
        $('#document_form select').each(function () {
            $(this).val($(this).find('option:first').val());
        });
    }, 'json')
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
            'return_date'
        ];

        appendDataToTable('#doc_info_table', response, columnMapping);
        setdtable('#doc_info_table', "Document Info List");

    } catch (error) {
        console.error("Document Table Error:", error);
    }
}

function getDocumentDelete(id) {
    $.post('api/staff_creation/delete_document.php', { id }, function (response) {
        if (response == '0') {
            swalError('Warning', 'Failed to Delete Document');
        } else if (response == '1') {
            swalSuccess('Success', 'Document Info Deleted Successfully!');
            getDocumentTable();
        }
    }, 'json');
}

async function getFamilyInfoTable() {
    let staff_id = $('#staff_auto_id').val();
    try {
        let response = await $.ajax({
            url: "api/staff_creation/family_creation_list.php",
            type: "POST",
            data: { staff_id: staff_id },
            dataType: "json"
        });

        var columnMapping = [
            'sno',
            'fam_name',
            'fam_relationship',
            'fam_dob',
            'fam_occupation',
            'fam_mobile',
        ];
        appendDataToTable('#fam_info_table', response, columnMapping);
        setdtable('#fam_info_table', "Family Info List");

    } catch (error) {
        console.error("Family Info Table Error:", error);
    }
}

function getFamilyTable() {
    let staff_id = $('#staff_auto_id').val();
    $.post('api/staff_creation/family_creation_list.php', { staff_id: staff_id }, function (response) {
        var columnMapping = [
            'sno',
            'fam_name',
            'fam_relationship',
            'fam_dob',
            'fam_occupation',
            'fam_mobile',
            'action'
        ];
        appendDataToTable('#family_creation_table', response, columnMapping);
        setdtable('#family_creation_table', "Family Info List");
        $('#family_form input').val('');
        $('#family_form input').css('border', '1px solid #cecece');
        $('#family_form select').css('border', '1px solid #cecece');
        $('#fam_relationship').val('');
    }, 'json')
}

function getFamilyDelete(id) {
    let staff_id = $('#staff_auto_id').val();
    let staff_profile_id = $('#staff_profile_id').val();
    $.post('api/staff_creation/delete_family_creation.php', { id, staff_id, staff_profile_id }, function (response) {
        if (response == '0') {
            swalError('Warning', 'Have to maintain atleast one Family Info');
        } else if (response == '1') {
            swalSuccess('Success', 'Family Info Deleted Successfully!');
            getFamilyTable();
        } else if (response == '2') {
            swalError('Access Denied', 'Family Member Already Used');
        } else {
            swalError('Warning', 'Error occur While Delete Family Info.');
        }
    }, 'json');
}

async function getQualificationInfoTable() {
    let staff_id = $('#staff_auto_id').val();
    try {
        let response = await $.ajax({
            url: "api/staff_creation/qualification_creation_list.php",
            type: "POST",
            data: { staff_id: staff_id },
            dataType: "json"
        });

        var columnMapping = [
            'sno',
            'highest_qualification',
            'degree',
            'specialization',
            'college',
            'university',
            'year_of_passing'
        ];
        appendDataToTable('#qual_info_table', response, columnMapping);
        setdtable('#qual_info_table', "Qualification Info List");
    } catch (error) {
        console.error("Qualification Info Table Error:", error);
    }
}

function getQualificationTable() {
    let staff_id = $('#staff_auto_id').val();
    $.post('api/staff_creation/qualification_creation_list.php', { staff_id: staff_id }, function (response) {
        var columnMapping = [
            'sno',
            'highest_qualification',
            'degree',
            'specialization',
            'college',
            'university',
            'year_of_passing',
            'action'
        ];
        appendDataToTable('#qualification_creation_table', response, columnMapping);
        setdtable('#qualification_creation_table', "Qualification Info List");
        $('#qualification_form input').val('');
        $('#qualification_form input').css('border', '1px solid #cecece');
        $('#qualification_form select').css('border', '1px solid #cecece');
    }, 'json')
}

function getQualificationDelete(id) {
    let staff_id = $('#staff_auto_id').val();
    let staff_profile_id = $('#staff_profile_id').val();
    $.post('api/staff_creation/delete_qualification_creation.php', { id, staff_id, staff_profile_id }, function (response) {
        if (response == '0') {
            swalError('Warning', 'Have to maintain atleast one Qualification Info');
        } else if (response == '1') {
            swalSuccess('Success', 'Qualification Info Deleted Successfully!');
            getQualificationTable();
        } else if (response == '2') {
            swalError('Access Denied', 'Qualification Info Already Used');
        } else {
            swalError('Warning', 'Error occur While Delete Qualification Info.');
        }
    }, 'json');
}

async function getExperienceInfoTable() {
    let staff_id = $('#staff_auto_id').val();
    try {
        let response = await $.ajax({
            url: "api/staff_creation/experience_creation_list.php",
            type: "POST",
            data: { staff_id: staff_id },
            dataType: "json"
        });

        var columnMapping = [
            'sno',
            'exp_type',
            'total_experience',
            'pre_company',
            'pre_designation',
            'work_duration',
            'last_salary',
            'reason_for_leaving'
        ];
        appendDataToTable('#exp_info_table', response, columnMapping);
        setdtable('#exp_info_table', "Experience Info List");
    } catch (error) {
        console.error("Experience Info Table Error:", error);
    }
}

function getExperienceTable() {
    let staff_id = $('#staff_auto_id').val();
    $.post('api/staff_creation/experience_creation_list.php', { staff_id: staff_id }, function (response) {
        var columnMapping = [
            'sno',
            'exp_type',
            'total_experience',
            'pre_company',
            'pre_designation',
            'work_duration',
            'last_salary',
            'reason_for_leaving',
            'action'
        ];
        appendDataToTable('#experience_creation_table', response, columnMapping);
        setdtable('#experience_creation_table', "Experience Info List");
        $('#experience_form input').val('');
        $('#experience_form input').css('border', '1px solid #cecece');
        $('#experience_form select').css('border', '1px solid #cecece');
        $('#exp_type').val('');
    }, 'json')
}

function getExperienceDelete(id) {
    let staff_id = $('#staff_auto_id').val();
    let staff_profile_id = $('#staff_profile_id').val();
    $.post('api/staff_creation/delete_experience_creation.php', { id, staff_id, staff_profile_id }, function (response) {
        if (response == '0') {
            swalError('Warning', 'Have to maintain atleast one Experience Info');
        } else if (response == '1') {
            swalSuccess('Success', 'Experience Info Deleted Successfully!');
            getExperienceTable();
        } else if (response == '2') {
            swalError('Access Denied', 'Experience Info Already Used');
        } else {
            swalError('Warning', 'Error occur While Delete Experience Info.');
        }
    }, 'json');
}

function getStateList() {
    $.post('api/common_files/get_state_list.php', function (response) {
        let appendStateOption = '';
        appendStateOption += "<option value=''>Select State</option>";
        $.each(response, function (index, val) {
            appendStateOption += "<option value='" + val.id + "'>" + val.state_name + "</option>";
        });
        $('#state').empty().append(appendStateOption);
    }, 'json');
}

function getDistrictList(state_id) {
    $.post('api/common_files/get_district_list.php', { state_id }, function (response) {
        let appendDistrictOption = '';
        appendDistrictOption += "<option value=''>Select District</option>";
        $.each(response, function (index, val) {
            appendDistrictOption += "<option value='" + val.id + "'>" + val.district_name + "</option>";
        });
        $('#district').empty().append(appendDistrictOption);
    }, 'json');
}

function staffDeleteStatus(staff_id) {
    let staff_profile_id = $('#staff_profile_id').val();
    // Proceed with deletion
    $.post('api/staff_creation/staff_sts_delete.php', { 'staff_id': staff_id, 'staff_profile_id': staff_profile_id }, function (deleteResponse) {
        if (deleteResponse.success) {
            swalSuccess('Success', 'Personal Info Deleted Successfully.');
            // clearStaffProfileForm('1');
            swapTableAndCreation();
            // getLoanEntryTable();
        } else {
            swalError('Error', 'Failed to delete personal info.');
        }
    }, 'json');
}

async function getCompanyName() {
    return new Promise((resolve, reject) => {
        $.post(
            "api/branch_creation/getCompanyName.php",
            {},

            function (response) {
                let dropdown = $("#company_name");
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

function getBranchList(company_id) {

    $.post(
        'api/staff_creation/company_mapped_branches.php', { company_id },
        function (response) {

            let branchOption = '';

            branchOption += '<option value="">Select Branch Name</option>';

            $.each(response, function (index, value) {

                branchOption += `
                    <option value="${value.id}">
                        ${value.branch_name}
                    </option>
                `;
            });

            // Fill both selects
            $('#branch_name, #branch').empty().html(branchOption);

        },
        'json'
    );
}
function getDepartmentList(company_id) {

    $.post(
        'api/staff_creation/company_mapped_department.php', { company_id },
        function (response) {

            let deptOption = '';

            deptOption += '<option value="">Select Department</option>';

            $.each(response, function (index, value) {

                deptOption += `
                    <option value="${value.id}">
                        ${value.department}
                    </option>
                `;
            });

            // Fill both selects
            $('#department').empty().html(deptOption);

        },
        'json'
    );
}
function getShiftList(company_id) {

    $.post(
        'api/staff_creation/company_mapped_shift.php', { company_id },
        function (response) {

            let shiftOption = '';

            shiftOption += '<option value="">Select Shift</option>';

            $.each(response, function (index, value) {

                shiftOption += `
                    <option value="${value.id}">
                        ${value.shift_name}
                    </option>
                `;
            });

            // Fill both selects
            $('#shift').empty().html(shiftOption);

        },
        'json'
    );
}

function getDesignationList(company_id) {

    $.post(
        'api/staff_creation/company_mapped_designation.php',
        { company_id },
        function (response) {

            let designationOption = '';

            designationOption += '<option value="">Select Designation</option>';

            $.each(response, function (index, value) {

                designationOption += `
                    <option 
                        value="${value.id}"
                        data-level="${value.designation_level}">
                        ${value.designation}
                    </option>
                `;
            });

            $('#designation').empty().html(designationOption);

        },
        'json'
    );
}

function getCTCInfoTable(company_id) {

    $.ajax({
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

                        <td>${row.component_cat}</td>

                        <td>
                            <input type="number"
                                   class="form-control ctc_amount"
                                   placeholder="Enter Amount"
                                   min="0">
                        </td>

                        <td>
                            <input type="text"
                                   class="form-control ctc_percentage"
                                   readonly>
                        </td>

                    </tr>
                `;
            });

            $('#ctc_info_table tbody').html(tr);
        }
    });
}

function calculateTotals(currentInput) {

    let totalAmount = 0;
    let totalPercentage = 0;

    $('.ctc_amount').each(function () {

        totalAmount += parseFloat($(this).val()) || 0;
    });

    $('.ctc_percentage').each(function () {

        totalPercentage += parseFloat($(this).val()) || 0;
    });

    $('#total_ctc_amount').val(totalAmount);

    $('#total_ctc_percentage').val(totalPercentage);

    let enteredCTC = parseFloat($('#total_ctc').val()) || 0;

    // Amount Validation
    if (totalAmount > enteredCTC) {

        swalError('Warning', 'Total CTC Amount should not exceed Total CTC');

        currentInput.val('');

        currentInput
            .closest('tr')
            .find('.ctc_percentage')
            .val('');

        recalculateTotals();

        return false;
    }

    // Percentage Validation
    if (totalPercentage > 100) {

        swalError('Warning', 'Total Percentage should not exceed 100');

        currentInput.val('');

        currentInput
            .closest('tr')
            .find('.ctc_percentage')
            .val('');

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

    $('#total_ctc_amount').val(totalAmount);

    $('#total_ctc_percentage').val(totalPercentage);
}