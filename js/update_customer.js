$(document).ready(function () {
    //Move Loan Entry  
    // Loan Entry Tab Change Radio buttons
    $(document).on('click', '#add_loan, #back_btn', function () {
        swapTableAndCreation();
    });


    $('#back_btn').click(function () {
        getcusUpdateTable();
        clearCusProfileForm('1');//To Clear Customer Profile
        $('#document_type_div').hide();
        $('#cheque_info_card').hide();
        $('#document_info_card').hide();
        $('#mortgage_info_card').hide();
        $('#endorsement_info_card').hide();
        $('#gold_info_card').hide();

    });

    $(document).on('click', '.edit-cus-update', function () {
        let id = $(this).attr('value');
        $('#customer_profile_id').val(id);

        swapTableAndCreation();
        editCustmerProfile(id)
    });

    $('input[name=update_type]').click(function () {
        let updateType = $(this).val();
        if (updateType == 'cus_profile') {
            $('#cus_update_customer_profile').show(); $('#update_documentation').hide();

        } else if (updateType == 'loan_doc') {
            $('#cus_update_customer_profile').hide(); $('#update_documentation').show();
            $('#document_type_div').hide();
            $('#cheque_info_card').hide();
            $('#document_info_card').hide();
            $('#mortgage_info_card').hide();
            $('#endorsement_info_card').hide();
            $('#gold_info_card').hide();

        }
    })
    $('.selector-item_label').click(function () {
        var radioId = $(this).attr('for');
        $('#' + radioId).prop('checked', true);
    });
    // Function to format Aadhaar number input
    $('input[data-type="adhaar-number"]').keyup(function () {
        var value = $(this).val();
        value = value.replace(/\D/g, "").split(/(?:([\d]{4}))/g).filter(s => s.length > 0).join(" ");
        $(this).val(value);
    });

    $('input[data-type="adhaar-number"]').change(function () {
        let len = $(this).val().length;
        if (len < 14) {
            $(this).val('');
            swalError('Warning', 'Kindly Enter Valid Aadhaar Number');
        }
    });

    $('#cus_name').on('blur', function () {
        var customerName = $(this).val().trim();
        if (customerName) {
            updateCustomerName(customerName);
        } else {
            removeCustomerName();
        }
    });


    $('#mobile1').on('blur', function () {
        let cus_id = $('#auto_gen_cus_id').val();
        let aadhar_num = $('#aadhar_nums').val().trim().replace(/\s/g, '');
        let cus_name = $('#cus_name').val();
        let customerMobile = $(this).val().trim();
        if (customerMobile) {
            dataCheckList(cus_id, cus_name, customerMobile , aadhar_num)
        } else {
            removeCustomerMobile();
        }
    });

    $(document).on('click', '#documentation', function (event) {
        event.preventDefault();
        let cus_id = $('#cus_id_upd').val();
        OnLoadFunctions(cus_id)
    })
    $('#cus_name').on('blur', function () {
        let aadhar_num = $('#aadhar_nums').val().trim().replace(/\s/g, '');
        let customerName = $('#cus_name').val().trim();
        if (aadhar_num && customerName) {
            addPropertyHolder(aadhar_num, customerName);
        }
        else {
            removeCustomerEntries();
        }
    });

    $('#pic').change(function () {
        let pic = $('#pic')[0];
        let img = $('#imgshow');
        compressImage(this, 200)
        img.attr('src', URL.createObjectURL(pic.files[0]));
    })

    $('#gu_pic').change(function () {
        let pic = $('#gu_pic')[0];
        let img = $('#gur_imgshow');
        compressImage(this, 200)
        img.attr('src', URL.createObjectURL(pic.files[0]));
    })

    /////family Modal////
    $('#submit_family').click(function (event) {
        event.preventDefault();
        // Validation
        let cus_profile_id = $('#customer_profile_id').val();
        let cus_id = $('#auto_gen_cus_id').val();
        let fam_name = $('#fam_name').val();
        let remarks = $('#remarks').val();
        let fam_relationship = $('#fam_relationship').val();
        let fam_age = $('#fam_age').val();
        let fam_live = $('#fam_live').val();
        let fam_occupation = $('#fam_occupation').val();
        let fam_aadhar = $('#fam_aadhar').val().replace(/\s/g, '');
        let fam_mobile = $('#fam_mobile').val();
        let family_id = $('#family_id').val();

        if (cus_profile_id == '') {
            swalError('Warning', 'Kindly Fill the Personal Info');
            return false;
        }
        var data = ['fam_name', 'fam_relationship', 'fam_live', 'fam_aadhar', 'fam_mobile']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });

        if (isValid) {
            $.post('api/loan_entry/submit_family_info.php', { cus_id, fam_name, fam_relationship, remarks, fam_age, fam_live, fam_occupation, fam_aadhar, fam_mobile, family_id }, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Family Info Added Successfully!');
                } else {
                    swalSuccess('Success', 'Family Info Updated Successfully!');
                }
                // Refresh the family table
                getFamilyTable();
            });
        }
    });


    $(document).on('click', '.familyActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/loan_entry/family_creation_data.php', { id: id }, function (response) {
            $('#family_id').val(id);
            $('#fam_name').val(response[0].fam_name);
            $('#fam_relationship').val(response[0].fam_relationship);
            $('#remarks').val(response[0].remarks);
            $('#fam_age').val(response[0].fam_age);
            $('#fam_live').val(response[0].fam_live);
            $('#fam_occupation').val(response[0].fam_occupation);
            $('#fam_aadhar').val(response[0].fam_aadhar);
            $('#fam_mobile').val(response[0].fam_mobile);
        }, 'json');
    });

    $(document).on('click', '.familyDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the Family Details?', getFamilyDelete, id);
        return;
    });

    ////Proerty Modal////
    $('#submit_property').click(function () {
        event.preventDefault();
        //Validation
        let cus_profile_id = $('#customer_profile_id').val();
        let cus_id = $('#auto_gen_cus_id').val();;
        let property = $('#property').val(); let property_detail = $('#property_detail').val(); let property_holder = $('#property_holder').val(); let property_id = $('#property_id').val();
        if (cus_profile_id == '') {
            swalError('Warning', 'Kindly Fill the Personal Info');
            return false;
        }
        var data = ['property', 'property_detail', 'property_holder', 'prop_relationship']
        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
        if (isValid) {
            $.post('api/loan_entry/submit_property.php', { cus_id, property, property_detail, property_holder, property_id, cus_profile_id }, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Property Info Added Successfully!');
                } else {
                    swalSuccess('Success', 'Property Info Updated Successfully!')
                }
                getPropertyTable();
            });
        }
    });

    $(document).on('click', '.propertyActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/loan_entry/property_creation_data.php', { id: id }, function (response) {
            $('#property_id').val(id);
            $('#property').val(response[0].property);
            $('#property_detail').val(response[0].property_detail);
            $('#property_holder').val(response[0].property_holder);
            if (response[0].fam_relationship == null) {
                $('#prop_relationship').val('Customer');
            } else {
                $('#prop_relationship').val(response[0].fam_relationship);
            }
        }, 'json');
    });

    $(document).on('click', '.propertyDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the Property Details?', getPropertyDelete, id);
        return;
    });
    $(document).on('click', '#add_kyc', function () {
        let customerID = $('#cus_id_upd').val().trim().replace(/\s/g, '');
        getKycLoanId(customerID);
        getKycTable();
        fetchProofList();
    });

    $('#property_holder').change(function () {
        var propertyHolderId = $(this).val();
        if (propertyHolderId != '' && propertyHolderId != 0) {
            getRelationshipName(propertyHolderId);
        } else if (propertyHolderId == 0) {
            $('#prop_relationship').val('Customer');
        } else {
            $('#prop_relationship').val('');
        }
    });

    $('#proof_of').change(function () {
        var proofOf = $(this).val();
        if (proofOf == "2") { // Family Member selected
            $('.fam_mem_div').show();
            $('.kyc_name_div').hide();
            $('#kyc_relationship').val('');
            getFamilyMember('Select Family Member', '#fam_mem');
        } else { // Customer or any other selection
            let cus_name = $("#cus_name").val();
            $('#kyc_name').val(cus_name);
            $('.kyc_name_div').show();
            $('.fam_mem_div').hide();
            $('#kyc_relationship').val('NIL');
        }
    });
    $('#fam_mem').change(function () {
        var familyMemberId = $(this).val();
        if (familyMemberId) {
            getKycRelationshipName(familyMemberId);
        } else {
            $('#kyc_relationship').val('');
        }
    });

    //////Bank Modal/////
    $('#submit_bank').click(function () {
        event.preventDefault();
        //Validation
        let cus_profile_id = $('#customer_profile_id').val();
        let cus_id = $('#auto_gen_cus_id').val();
        let bank_name = $('#bank_name').val(); let branch_name = $('#branch_name').val(); let acc_holder_name = $('#acc_holder_name').val(); let acc_number = $('#acc_number').val(); let ifsc_code = $('#ifsc_code').val(); let bank_id = $('#bank_id').val();
        if (cus_profile_id == '') {
            swalError('Warning', 'Kindly Fill the Personal Info');
            return false;
        }
        var data = ['bank_name', 'branch_name', 'acc_holder_name', 'acc_number', 'ifsc_code']
        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
        if (isValid) {
            $.post('api/loan_entry/submit_bank.php', { cus_id, bank_name, branch_name, acc_holder_name, acc_number, ifsc_code, bank_id, cus_profile_id }, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Bank Info Added Successfully!');
                } else {
                    swalSuccess('Success', 'Bank Info Updated Successfully!')
                }
                getBankTable();
            });
        }
    })

    $(document).on('click', '.bankActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/loan_entry/bank_creation_data.php', { id: id }, function (response) {
            $('#bank_id').val(id);
            $('#bank_name').val(response[0].bank_name);
            $('#branch_name').val(response[0].branch_name);
            $('#acc_holder_name').val(response[0].acc_holder_name);
            $('#acc_number').val(response[0].acc_number);
            $('#ifsc_code').val(response[0].ifsc_code);
        }, 'json');
    });

    $(document).on('click', '.bankDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the Bank Details?', getBankDelete, id);
        return;
    });

    ////////////Kyc Modal///////
    $('#submit_kyc').click(function (event) {
        event.preventDefault();
        //Validation
        let cus_profile_id = $('#customer_profile_id').val();
        let cus_id = $('#auto_gen_cus_id').val();
        let upload = $('#upload')[0].files[0]; let kyc_upload = $('#kyc_upload').val();
        let proof_of = $('#proof_of').val(); let fam_mem = $("#fam_mem").val(); let proof = $('#proof').val(); let proof_detail = $('#proof_detail').val(); let kyc_id = $('#kyc_id').val();
        if (cus_profile_id == '') {
            swalError('Warning', 'Kindly Fill the Personal Info');
            return false;
        }
        var data = ['proof_of', 'kyc_relationship', 'proof', 'proof_detail']
        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
        if (isValid) {
            let kycDetail = new FormData();
            kycDetail.append('proof_of', proof_of)
            if (proof_of !== 'Customer') {
                kycDetail.append('fam_mem', fam_mem);
            }
            kycDetail.append('cus_profile_id', cus_profile_id)
            kycDetail.append('cus_id', cus_id)
            kycDetail.append('proof', proof)
            kycDetail.append('proof_detail', proof_detail)
            kycDetail.append('upload', upload)
            kycDetail.append('kyc_upload', kyc_upload)
            kycDetail.append('kyc_id', kyc_id)
            $.ajax({
                url: 'api/loan_entry/submit_kyc.php',
                type: 'post',
                data: kycDetail,
                contentType: false,
                processData: false,
                cache: false,
                success: function (response) {
                    if (response = 'Success') {
                        if (kyc_id == '') {
                            swalSuccess('Success', 'KYC Added Successfully!');
                            $('.kyc_name_div').hide();
                            $('.fam_mem_div').hide();
                        } else {
                            swalSuccess('Success', 'KYC Updated Successfully!')
                            $('.kyc_name_div').hide();
                            $('.fam_mem_div').hide();
                        }
                    } else {
                        swalError('Error', 'Error in table');
                    }
                    getKycTable();
                }
            });
        }
    });

    $(document).on('click', '.kycActionBtn', function () {
        var id = $(this).attr('value');
        $.post('api/update_customer_files/update_kyc_creation_data.php', { id: id }, function (response) {
            if (response && response.length > 0) {
                $('#kyc_id').val(id);
                $('#proof_of').val(response[0].proof_of);
                $('#proof').val(response[0].proof);
                $('#proof_detail').val(response[0].proof_detail);
                $("#kyc_upload").val(response[0].upload);

                if (response[0].proof_of == 1) { // Assuming 1 is for customer
                    $('.fam_mem_div').hide();
                    $('.kyc_name_div').show();
                    let cus_name = $("#cus_name").val();
                    $('#kyc_name').val(cus_name);
                    $('#fam_mem').val('');
                } else {
                    $('.kyc_name_div').hide();
                    $('#kyc_name').val('');
                    getFamilyMember('Select Family Member', '#fam_mem');
                    setTimeout(() => {
                        $("#fam_mem").val(response[0].fam_mem);
                    }, 100);
                    $('.fam_mem_div').show();
                    // $('#kyc_relationship').val(response[0].fam_relationship);
                }
                if (response[0].proof_of == 1) {
                    $('#kyc_relationship').val('NIL');
                } else {
                    $('#kyc_relationship').val(response[0].fam_relationship);
                }

                getKycLoanId(); // Fetch all loan ID options
                setTimeout(() => {
                    $('#kycloan_id').val(response[0].cus_profile_id);
                }, 500);

            } else {
                alert('No data found for the selected KYC ID.');
            }
        }, 'json');
    });
    $('#clear_kyc_form').on('click', function () {
        $('.fam_mem_div').hide();
        $('#fam_mem').val('');
    });

    $('.kycmodal_close').on('click', function () {
        $('.fam_mem_div').hide();
        $('#fam_mem').val('');
    });

    $(document).on('click', '.kycDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the KYC Details?', getKycDelete, id);
        return;
    });

    $('#proof_of').on('change', function () {
        if ($(this).val() == "2") {
            $('.fam_mem_div').show();
            $('.kyc_name_div').hide();
        } else {
            $('.kyc_name_div').show();
            $('.fam_mem_div').hide();
        }
    });
    //////KyC Proof Modal///////
    $('#submit_proof').click(function () {
        event.preventDefault();
        //Validation
        let addProof_name = $('#addProof_name').val(); let proof_id = $('#proof_id').val();
        var data = ['addProof_name']
        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
        if (isValid) {
            $.post('api/loan_entry/submit_proof.php', { addProof_name, proof_id }, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Proof Info Added Successfully!');
                } else {
                    swalSuccess('Success', 'Proof Info Updated Successfully!')
                }

                $('#clear_proof_form').trigger('click')
                $('#proof_id').val('')
                $('#add_proof_info_modal').modal('hide');
                getProofTable();
                fetchProofList();
            });
        }
    });

    $(document).on('click', '.proofActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/loan_entry/proof_creation_data.php', { id: id }, function (response) {
            $('#proof_id').val(id);
            $('#addProof_name').val(response[0].addProof_name);
        }, 'json');
    });

    $(document).on('click', '.proofDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the Proof Details?', getProofDelete, id);
        return;
    });

    $('#mobile1,#mobile2,#fam_mobile').change(function () {
        checkMobileNo($(this).val(), $(this).attr('id'));
    });

    $('#area').change(function () {
        var areaId = $(this).val();
        if (areaId) {
            getAlineName(areaId);
        }
    });

    $('#dob').on('change', function () {
        var dob = new Date($(this).val());
        var today = new Date();
        var age = today.getFullYear() - dob.getFullYear();
        var m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        $('#age').val(age);
    });

    $('#guarantor_name').change(function () {
        var guarantorId = $(this).val();
        if (guarantorId) {
            getGrelationshipName(guarantorId);
        } else {
            $('#relationship').val('');
        }
    })

    $('input[name="mobile_whatsapp"]:radio').change(function () {
        let selectedValue = $(this).val();  // Get selected radio value
        let mobileNumber = '';              // Initialize mobile number variable

        // Get the corresponding mobile number based on selected radio value
        if (selectedValue === 'mobile1') {
            mobileNumber = $('#mobile1').val();
        } else if (selectedValue === 'mobile2') {
            mobileNumber = $('#mobile2').val();
        }

        // Set the selected mobile number in the WhatsApp field
        $('#whatsapp_no').val(mobileNumber);

    });


    $('#submit_customer_profile').click(function (event) {
        event.preventDefault();
        // Validate form fields
        let famInfoRowCount = $('#fam_info_table').DataTable().rows().count();
        let kycInfoRowCount = $('#kyc_info').DataTable().rows().count();
        let pic = $('#pic')[0].files[0];
        let per_pic = $('#per_pic').val();
        let gu_pic = $('#gu_pic')[0].files[0];
        let gur_pic = $('#gur_pic').val();
        let cus_id = $('#auto_gen_cus_id').val();
        let aadhar_num = $('#aadhar_nums').val().replace(/\s/g, '');
        let cus_name = $("#cus_name").val();
        let gender = $('#gender').val();
        let dob = $('#dob').val();
        let age = $('#age').val();
        let mobile1 = $('#mobile1').val();
        let mobile2 = $('#mobile2').val();
        let whatsapp_no = $('#whatsapp_no').val();
        let guarantor_name = $('#guarantor_name').val();
        let cus_data = $('#cus_data').val();
        let cus_status = $('#cus_status').val();
        let res_type = $('#res_type').val();
        let res_detail = $('#res_detail').val();
        let res_address = $('#res_address').val();
        let native_address = $('#native_address').val();
        let occupation = $('#occupation').val();
        let occ_detail = $('#occ_detail').val();
        let occ_income = $('#occ_income').val().replace(/,/g, '');
        let occ_address = $('#occ_address').val();
        let area_confirm = $('#area_confirm').val();
        let area = $('#area').val();
        let line = $('#line').attr('data-id');
        let cus_limit = $('#cus_limit').val().replace(/,/g, '');
        let about_cus = $('#about_cus').val();
        let customer_profile_id = $('#customer_profile_id').val();
        if (customer_profile_id === '') {
            swalError('Warning', 'Please Fill out personal Info!');
            return false;
        }
        let isValid = true;
        // Validate fields based on area_confirm value
        if (area_confirm == '1') {
            let validationResults = [
                validateField(res_type, 'res_type'),
                validateField(res_detail, 'res_detail'),
                validateField(res_address, 'res_address'),
                validateField(native_address, 'native_address')
            ];
            if (!validationResults.every(result => result)) {
                isValid = false;
            }
        } else if (area_confirm == '2') {
            let validationResults = [
                validateField(occupation, 'occupation'),
                validateField(occ_detail, 'occ_detail'),
                validateField(occ_income, 'occ_income'),
                validateField(occ_address, 'occ_address')
            ];
            if (!validationResults.every(result => result)) {
                isValid = false;
            }
        }
        data = ['cus_name', 'gender', 'mobile1', 'guarantor_name', 'area_confirm', 'area', 'line', 'cus_limit'];

        //  var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });

        if (isValid) {
            if (famInfoRowCount === 0 || kycInfoRowCount === 0) {
                swalError('Warning', 'Please Fill out Family Info and KYC Info!');
                return false;
            }
            // Prepare form data using FormData object
            let entryDetail = new FormData();
            entryDetail.append('cus_id', cus_id);
            entryDetail.append('cus_name', cus_name);
            entryDetail.append('gender', gender);
            entryDetail.append('dob', dob);
            entryDetail.append('age', age);
            entryDetail.append('mobile1', mobile1);
            entryDetail.append('mobile2', mobile2);
            entryDetail.append('whatsapp_no', whatsapp_no);
            entryDetail.append('aadhar_num', aadhar_num);
            entryDetail.append('pic', pic);
            entryDetail.append('per_pic', per_pic);
            entryDetail.append('guarantor_name', guarantor_name);
            entryDetail.append('gu_pic', gu_pic);
            entryDetail.append('gur_pic', gur_pic);
            entryDetail.append('cus_data', cus_data);
            entryDetail.append('cus_status', cus_status);
            entryDetail.append('res_type', res_type);
            entryDetail.append('res_detail', res_detail);
            entryDetail.append('res_address', res_address);
            entryDetail.append('native_address', native_address);
            entryDetail.append('occupation', occupation);
            entryDetail.append('occ_detail', occ_detail);
            entryDetail.append('occ_income', occ_income);
            entryDetail.append('occ_address', occ_address);
            entryDetail.append('area_confirm', area_confirm);
            entryDetail.append('area', area);
            entryDetail.append('line', line);
            entryDetail.append('cus_limit', cus_limit);
            entryDetail.append('about_cus', about_cus);
            entryDetail.append('customer_profile_id', customer_profile_id)

            // AJAX call to submit data
            $.ajax({
                url: 'api/loan_entry/submit_cus_profile.php',
                type: 'POST',
                data: entryDetail,
                contentType: false,
                processData: false,
                cache: false,
                dataType: 'json',
                success: function (response) {
                    // Handle success response
                    if (response.status == 0) {
                        swalSuccess('Success', 'Customer Profile Updated Successfully!');
                        $('html, body').animate({
                            scrollTop: $('.page-content').offset().top
                        }, 3000);
                    }
                    $('#customer_profile_id').val(response.last_id);
                    $('#per_pic').val(response.pic);
                    $('#cus_data').val(response.cus_data);
                    if (response.cus_data == 'Existing') {
                        $('.cus_status_div').show();
                        $('#data_checking_div').show();
                        $('#checking_hide').show();
                    }
                },
                error: function () {
                    swalError('Error', 'Error occurred while processing your request.');
                }
            });

        }
    });
    $('#area_confirm').on('change', function () {
        resetValidate();
    });
    $('#name_check, #aadhar_check, #mobile_check').on('input', function () {
        var name = $('#name_check').val().trim();
        var aadhar = $('#aadhar_check').val().trim();
        var mobile = $('#mobile_check').val().trim();
        let cus_profile_id = $('#customer_profile_id').val();

        // Check which field triggered the event
        if ($(this).attr('id') === 'name_check') {
            // Clear aadhar_check and mobile_check if searching by name
            $('#aadhar_check').val('');
            $('#mobile_check').val('');
            aadhar = ''; // Reset aadhar variable
            mobile = ''; // Reset mobile variable
        } else if ($(this).attr('id') === 'aadhar_check') {
            // Clear aadhar_check and mobile_check if searching by name
            $('#name_check').val('');
            $('#mobile_check').val('');
            name = ''; // Reset aadhar variable
            mobile = ''; //{
        } else if ($(this).attr('id') === 'mobile_check') {
            // Clear aadhar_check and mobile_check if searching by name
            $('#aadhar_check').val('');
            $('#name_check').val('');
            name = ''; // Reset aadhar variable
            aadhar = ''; //{
        }

        // Fetch data for both customer and family tables
        $('#data_checking_table_div').show();
        fetchCustomerData(name, aadhar, mobile, cus_profile_id);
    });

    $('#clear_loan').click(function () {
        event.preventDefault();
        clearCusProfileForm('2');
    });

    $('#proof_modal_btn').click(function () {
        if ($('#add_kyc_info_modal').is(':visible')) {
            $('#add_kyc_info_modal').hide();
        }
    });

    $('.kyc_proof_close').click(function () {
        if ($('#add_kyc_info_modal').is(':hidden')) {
            $('#add_kyc_info_modal').show();
        }
    });
    $('#cus_limit,#occ_income').on('keyup', function () {
        let raw = $(this).val().replace(/,/g, '');
        $(this).val(formatIndianNumber(raw));
    });

}); ///////////////////////////////////////////////////////////////// Customer Profile - Document END ////////////////////////////////////////////////////////////////////

//On Load function 
$(function () {
    getcusUpdateTable();
});

function getcusUpdateTable() {
    serverSideTable('#cus_update_table', '', 'api/update_customer_files/update_customer_list.php',"Update Customer List");
}

function swapTableAndCreation() {
    if ($('.update_table_content').is(':visible')) {
        $('.update_table_content').hide();
        $('#add_loan').hide();
        $('#update_content').show();
        $('#back_btn').show();

    } else {
        $('.update_table_content').show();
        $('#add_loan').show();
        $('#update_content').hide();
        $('#back_btn').hide();
        $('#customer_profile').trigger('click')
    }
}

function clearCusProfileForm(type) {
    // Clear input fields except those with IDs 'loan_id_calc' and 'loan_date_calc'
    $('#cus_update_customer_profile').find('input').each(function () {
        let id = $(this).attr('id');
        if (type == '1') {
            cusid = '';
            $('.personal_info_disble').val('');
            $('#submit_personal_info').attr('disabled', false);
        } else if (type == '2') {
            cusid = 'customer_profile_id';
        }
        $('#cus_update_customer_profile input').css('border', '1px solid #cecece');
        $('#cus_update_customer_profile select').css('border', '1px solid #cecece');
        if (id !== cusid && id != 'cus_id' && id != 'cus_name' && id != 'dob' && id != 'mobile1' && id != 'mobile2' && id != 'pic' && id != 'age' && id != 'per_pic') {
            $(this).val('');
        }
    });

    // Clear all textarea fields within the specific form
    $('#cus_update_customer_profile').find('textarea').val('');

    //clear all upload inputs within the form.
    $('#cus_update_customer_profile').find('input[type="file"]').val('');

    // Reset all select fields within the specific form
    $('#cus_update_customer_profile').find('select').each(function () {
        let selectid = $(this).attr('id');
        if (selectid != 'gender') {
            $(this).val($(this).find('option:first').val());
        }
    });

    //Reset all  images within the form
    $('#imgshow').attr('src', 'img/avatar.png');
    $('#gur_imgshow').attr('src', 'img/avatar.png');
}

function fetchCustomerData(name, aadhar_num, mobile, cus_profile_id) {
    $.post('api/loan_entry/search_customer.php', { name, aadhar_num, mobile, cus_profile_id }, function (response) {
        // Process customer data
        var customerMapping = ['index', 'cus_id', 'cus_name', 'mobiles'];
        var customerData = response.customers.map(function (customer, index) {
            let mobiles = customer.mobile1;
            if (customer.mobile2) {
                mobiles += `, ${customer.mobile2}`;
            }
            return {
                index: index + 1,
                cus_id: customer.cus_id,
                cus_name: customer.cus_name,
                mobiles: mobiles
            };
        });
        appendDataToTable('#cus_info', customerData, customerMapping);

        // Process family data
        var familyMapping = ['index', 'aadhar_num', 'fam_name', 'fam_relationship', 'under_customer_name', 'under_customer_id'];
        var familyData = response.family.map(function (member, index) {
            return {
                index: index + 1,
                aadhar_num: member.fam_aadhar,
                fam_name: member.fam_name,
                fam_relationship: member.fam_relationship,
                under_customer_name: member.under_customer_name,
                under_customer_id: member.under_customer_id
            };
        });
        appendDataToTable('#family_info', familyData, familyMapping);

    }, 'json');
}

function addCustomerMobile(mobile) {
    $('#mobile_check .custom-option').remove();
    $('#mobile_check').append('<option class="custom-option" value="' + mobile + '">' + mobile + '</option>');
}

function removeCustomerMobile() {
    $('#mobile_check .custom-option').remove();
}

function updateCustomerID(id) {
    $('#aadhar_check .custom-option').remove();
    $('#aadhar_check').append('<option class="custom-option" value="' + id + '">' + id + '</option>');
}

function removeCustomerID() {
    $('#aadhar_check .custom-option').remove();
}

function updateCustomerName(name) {
    $('#name_check .custom-option').remove();
    $('#name_check').append('<option class="custom-option" value="' + name + '">' + name + '</option>');
}

function removeCustomerName() {
    $('#name_check .custom-option').remove();
}

function addPropertyHolder(aadhar_num, name) {
    $('#property_holder .custom-option').remove();
    $('#property_holder').append('<option class="custom-option" value="' + aadhar_num + '">' + name + '</option>');
}

function removeCustomerEntries() {
    $('#property_holder .custom-option').remove();
}

function getFamilyInfoTable() {
    let cus_id = $('#cus_id_upd').val();
    $.post('api/loan_entry/family_creation_list.php', { cus_id }, function (response) {
        var columnMapping = [
            'sno',
            'fam_name',
            'fam_relationship',
            'remarks',
            'fam_age',
            'fam_live',
            'fam_occupation',
            'fam_aadhar',
            'fam_mobile',
        ];
        appendDataToTable('#fam_info_table', response, columnMapping);
        setdtable('#fam_info_table', "Family Info List");
    }, 'json')
}

function getFamilyTable() {
    let cus_id = $('#cus_id_upd').val();
    $.post('api/loan_entry/family_creation_list.php', { cus_id: cus_id }, function (response) {
        var columnMapping = [
            'sno',
            'fam_name',
            'fam_relationship',
            'remarks',
            'fam_age',
            'fam_live',
            'fam_occupation',
            'fam_aadhar',
            'fam_mobile',
            'action'
        ];
        appendDataToTable('#family_creation_table', response, columnMapping);
        setdtable('#family_creation_table', "Family Info List");
        $('#family_form input').val('');
        $('#family_form input').css('border', '1px solid #cecece');
        $('#family_form select').css('border', '1px solid #cecece');
        $('#fam_relationship').val('');
        $('#remarks').val('');
        $('#fam_live').val('');
    }, 'json')
}
function checkAdditionalRenewal(cus_id) {
    $.post('api/loan_entry/check_additional_renewal.php', { cus_id }, function (response) {
        $('#cus_status').val(response);
    }, 'json');
}
function getFamilyDelete(id) {
    let cus_id = $('#cus_id_upd').val();
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_entry/delete_family_creation.php', { id, cus_id, cus_profile_id }, function (response) {
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
function resetValidate() {
    const fieldsToReset = [
        'res_type',
        'res_detail',
        'res_address',
        'native_address',
        'occupation',
        'occ_detail',
        'occ_income',
        'occ_address'
    ];

    fieldsToReset.forEach(fieldId => {
        $('#' + fieldId).css('border', '1px solid #cecece');

    });
}
function getGuarantorName() {
    let cus_id = $('#cus_id_upd').val().replace(/\s/g, '');
    $.post('api/loan_entry/get_guarantor_name.php', { cus_id }, function (response) {
        let appendGuarantorOption = '';
        appendGuarantorOption += "<option value=''>Select Guarantor Name</option>";
        $.each(response, function (index, val) {
            let selected = '';
            let editGId = $('#guarantor_name_edit').val();
            if (val.id == editGId) {
                selected = 'selected';
            }
            appendGuarantorOption += "<option value='" + val.id + "' " + selected + ">" + val.fam_name + "</option>";
        });
        $('#guarantor_name').empty().append(appendGuarantorOption);
    }, 'json');
}
function getKycLoanId() {
    let cus_id = $('#cus_id_upd').val();
    $.post('api/update_customer_files/get_kyc_loan.php', { cus_id }, function (response) {
        let appendLoanIdOption = '';
        appendLoanIdOption += "<option value=''>Select Loan ID</option>";
        $.each(response, function (index, val) {
            let selected = '';
            appendLoanIdOption += "<option value='" + val.cus_profile_id + "' " + selected + ">" + val.loan_id + "</option>";
        });
        $('#kycloan_id').empty().append(appendLoanIdOption);
    }, 'json');
}

function getGrelationshipName(guarantorId) {
    $.ajax({
        url: 'api/loan_entry/getGrelationship.php',
        type: 'POST',
        data: { guarantor_id: guarantorId },
        dataType: 'json',
        cache: false,
        success: function (response) {
            $('#relationship').val(response.relationship);
        },
        error: function (xhr, status, error) {
            console.error('AJAX error: ' + status, error);
            // Optionally handle errors here, such as displaying an error message to the user
        }
    });
}

function getPropertyTable() {
    let cus_id = $('#cus_id_upd').val();

    $.post('api/update_customer_files/update_property_creation_list.php', { cus_id }, function (response) {
        var columnMapping = [
            'sno',
            'property',
            'property_detail',
            'property_holder',
            'fam_relationship',
            'action'
        ];
        appendDataToTable('#property_creation_table', response, columnMapping);
        setdtable('#property_creation_table', "Property Info List");
        $('#property_form input').val('');
        $('#property_form input').css('border', '1px solid #cecece');
        $('#property_form select').css('border', '1px solid #cecece');
        $('textarea').css('border', '1px solid #cecece');
        $('#property_holder').val('');
        $('#property_detail').val('');
    }, 'json')
}

function getPropertyInfoTable() {
    let cus_id = $('#cus_id_upd').val();

    $.post('api/update_customer_files/update_property_creation_list.php', { cus_id }, function (response) {
        var columnMapping = [
            'sno',
            'property',
            'property_detail',
            'property_holder',
            'fam_relationship',
        ];
        appendDataToTable('#prop_info', response, columnMapping);
        setdtable('#prop_info', "Property Info List");
    }, 'json')
}

function getPropertyHolder() {
    let cus_id = $('#cus_id').val();
    let cus_name = $('#cus_name').val();
    $.post('api/loan_entry/get_guarantor_name.php', { cus_id }, function (response) {
        let appendHolderOption = '';
        appendHolderOption += "<option value=''>Select Property Holder</option>";
        appendHolderOption += "<option value='" + 0 + "'>" + cus_name + "</option>";
        $.each(response, function (index, val) {
            appendHolderOption += "<option value='" + val.id + "'>" + val.fam_name + "</option>";
        });
        $('#property_holder').empty().append(appendHolderOption);
    }, 'json');
}

function getPropertyDelete(id) {
    $.post('api/loan_entry/delete_property_creation.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('Success', 'Property Info Deleted Successfully!');
            getPropertyTable();
        } else {
            swalError('Error', 'Failed to Delete Property: ' + response);
        }
    }, 'json');
}

function getRelationshipName(propertyHolderId) {
    $.ajax({
        url: 'api/loan_entry/getRelationshipName.php',
        type: 'POST',
        data: { property_holder_id: propertyHolderId },
        dataType: 'json',
        cache: false,
        success: function (response) {
            $('#prop_relationship').val(response.prop_relationship);
        },
    });
}

function getBankDelete(id) {
    $.post('api/loan_entry/delete_bank_creation.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('Success', 'Bank Info Deleted Successfully!');
            getBankTable();
        } else {
            swalError('Error', 'Failed to Delete Bank: ' + response);
        }
    }, 'json');
}

function getBankTable() {
    let cus_id = $('#cus_id_upd').val();
    //let cus_profile_id=$('#customer_profile_id').val();
    $.post('api/update_customer_files/update_bank_creation_list.php', { cus_id }, function (response) {
        var columnMapping = [
            'sno',
            'bank_name',
            'branch_name',
            'acc_holder_name',
            'acc_number',
            'ifsc_code',
            'action'
        ];
        appendDataToTable('#bank_creation_table', response, columnMapping);
        setdtable('#bank_creation_table', "Bank Info List");
        $('#bank_form input').val('');
        $('#bank_form input').css('border', '1px solid #cecece');

    }, 'json')
}

function getBankInfoTable() {
    let cus_id = $('#cus_id_upd').val();
    // let cus_profile_id=$('#customer_profile_id').val()
    $.post('api/update_customer_files/update_bank_creation_list.php', { cus_id }, function (response) {
        var columnMapping = [
            'sno',
            'bank_name',
            'branch_name',
            'acc_holder_name',
            'acc_number',
            'ifsc_code',
        ];
        appendDataToTable('#bank_info', response, columnMapping);
        setdtable('#bank_info', "Bank Info List");
    }, 'json')
}

function getKycDelete(id) {
    let cus_id = $('#cus_id_upd').val();
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/update_customer_files/customer_delete_kyc_creation.php', { id, cus_id, cus_profile_id }, function (response) {
        if (response == '0') {
            swalError('Warning', 'Have to maintain atleast one Kyc Info');
        } else if (response == '1') {
            swalSuccess('Success', 'Kyc Info Deleted Successfully!');
            getKycTable();
        } else {
            swalError('Error', 'Failed to Delete Kyc');
        }
    }, 'json');
}

function getKycTable() {
    let cus_id = $('#cus_id_upd').val();
    $.post('api/update_customer_files/update_kyc_creation_list.php', { cus_id }, function (response) {
        var columnMapping = [
            'sno',
            'loan_id',
            'proof_of',
            'fam_relationship',
            'proof',
            'proof_detail',
            'upload',
            'action'
        ];
        appendDataToTable('#kyc_creation_table', response, columnMapping);
        setdtable('#kyc_creation_table', "Kyc Info List");
        $('#kyc_form input').val('');
        $('#kyc_form input').css('border', '1px solid #cecece');
        $('#kyc_form select').css('border', '1px solid #cecece');
        $('#Kyc_form.kyc_name_div').hide();
        $('#Kyc_form.fam_mem_div').hide();
        $('#kyc_form select').each(function () {
            $(this).val($(this).find('option:first').val());
        });
    }, 'json')
}

function getKycInfoTable() {
    let cus_id = $('#cus_id_upd').val();
    $.post('api/update_customer_files/update_kyc_creation_list.php', { cus_id }, function (response) {
        var columnMapping = [
            'sno',
            'loan_id',
            'proof_of',
            'fam_relationship',
            'proof',
            'proof_detail',
            'upload',
        ];
        appendDataToTable('#kyc_info', response, columnMapping);
        setdtable('#kyc_info', "Kyc Info List");
    }, 'json')
}



function getKycRelationshipName(familyMemberId) {
    $.ajax({
        url: 'api/loan_entry/getKycRelationshipName.php',
        type: 'POST',
        data: { family_member_id: familyMemberId },
        dataType: 'json',
        cache: false,
        success: function (response) {
            $('#kyc_relationship').val(response.kyc_relationship);
        },
    });
}

function getProofDelete(id) {
    $.post('api/loan_entry/delete_proof_creation.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('Success', 'proof Info Deleted Successfully!');
            getProofTable();
        } else if (response == '0') {
            swalError('Access Denied', 'proof Info Already Used');
        } else {
            swalError('Warning', 'Error occur While Delete Proof Info.');
        }
    }, 'json')
}

function getProofTable() {
    $.post('api/loan_entry/proof_creation_list.php', function (response) {
        var columnMapping = [
            'sno',
            'addProof_name',
            'action'
        ];
        appendDataToTable('#proof_creation_table', response, columnMapping);
        setdtable('#proof_creation_table', "Proof Info List");
    }, 'json')
}

function fetchProofList() {
    $.ajax({
        url: 'api/loan_entry/get_proof_list.php',
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            $('#proof').empty().append('<option value="">Select proof</option>');
            $.each(response, function (index, proof) {
                $('#proof').append('<option value="' + proof.id + '">' + proof.addProof_name + '</option>');
                //$('input').css('border', '1px solid #cecece');
            });
            $('#proof_form input').val('');
            $('#proof_form input').css('border', '1px solid #cecece');

        }
    });
}

function getAreaName() {
        let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_entry/get_area.php',{cus_profile_id}, function (response) {
        let appendAreaOption = '';
        appendAreaOption += "<option value=''>Select Area Name</option>";
        $.each(response, function (index, val) {
            let selected = '';
            let editArea = $('#area_edit').val();
            if (val.id == editArea) {
                selected = 'selected';
            }
            appendAreaOption += "<option value='" + val.id + "' " + selected + ">" + val.areaname + "</option>";
        });
        $('#area').empty().append(appendAreaOption);
    }, 'json');
}

function getAlineName(areaId) {
        let cus_profile_id = $('#customer_profile_id').val();
    $.ajax({
        url: 'api/loan_entry/getAlineName.php',
        type: 'POST',
        data: { aline_id: areaId ,cus_profile_id:cus_profile_id},
        dataType: 'json',
        cache: false,
        success: function (response) {
            if (response != '') {
                $('#line').val(response[0].linename);
                $('#line').attr('data-id', response[0].line_id);
            } else {
                $('#line').val('');
                $('#line').attr('data-id', '');
            }
        },
    });
}

function dataCheckList( cus_id, cus_name, cus_mble_no, aadhar_num) {
    $.post('api/loan_entry/datacheck_name.php', { cus_id }, function (response) {
        //Name
        $('#name_check').empty();
        $('#name_check').append("<option value=''>Select Name</option>");
        (cus_name != '') ? $('#name_check').append('<option value="' + cus_name + '">' + cus_name + '</option>') : '';
        $.each(response, function (index, val) {
            $('#name_check').append("<option value='" + val.fam_name + "'>" + val.fam_name + "</option>");
        });

        //Adhar no
        $('#aadhar_check').empty();
        $('#aadhar_check').append("<option value=''>Select Aadhar Number</option>");

        // Append the provided Aadhar number with the customer name if both are present
        if (aadhar_num && cus_name) {
            $('#aadhar_check').append('<option value="' + aadhar_num + '">' + aadhar_num + ' - ' + cus_name + '</option>');
        }

        // Loop through the response and append Aadhar numbers with family names
        $.each(response, function (index, val) {
            if (val.fam_aadhar && val.fam_name) {
                $('#aadhar_check').append('<option value="' + val.fam_aadhar + '">' + val.fam_aadhar + ' - ' + val.fam_name + '</option>');
            }
        });


        //Mobile no 
        $('#mobile_check').empty();
        $('#mobile_check').append("<option value=''>Select Mobile Number</option>");

        // Append the provided customer mobile number and name if they are present
        if (cus_mble_no && cus_name) {
            $('#mobile_check').append('<option value="' + cus_mble_no + '">' + cus_mble_no + ' - ' + cus_name + '</option>');
        }

        // Loop through the response and append mobile numbers with family names
        $.each(response, function (index, val) {
            if (val.fam_mobile && val.fam_name) {
                $('#mobile_check').append('<option value="' + val.fam_mobile + '">' + val.fam_mobile + ' - ' + val.fam_name + '</option>');
            }
        });

    }, 'json');
}

function editCustmerProfile(id) {
    $.post('api/loan_entry/customer_profile_data.php', { id: id }, function (response) {
        $('#customer_profile_id').val(response[0].id);
        $('#area_edit').val(response[0].area);
        $('#auto_gen_cus_id').val(response[0].cus_id);
        $('#aadhar_nums').val(response[0].aadhar_num);
        $('#cus_id_upd').val(response[0].cus_id);
        $('#cus_name').val(response[0].cus_name);
        $('#gender').val(response[0].gender);
        $('#dob').val(response[0].dob);
        $('#age').val(response[0].age);
        $('#mobile2').val(response[0].mobile2);
        $('#mobile1').val(response[0].mobile1);
        $('#whatsapp_no').val(response[0].whatsapp_no);
        $('#guarantor_name_edit').val(response[0].guarantor_name);
        $('#cus_data').val(response[0].cus_data);
        $('#cus_status').val(response[0].cus_status);
        $('#res_type').val(response[0].res_type);
        $('#res_detail').val(response[0].res_detail);
        $('#res_address').val(response[0].res_address);
        $('#native_address').val(response[0].native_address);
        $('#occupation').val(response[0].occupation);
        $('#occ_address').val(response[0].occ_address);
        $('#occ_detail').val(response[0].occ_detail);
        $('#occ_income').val(moneyFormatIndia(response[0].occ_income));
        $('#area_confirm').val(response[0].area_confirm);
        $('#line').val(response[0].line);
        $('#cus_limit').val(moneyFormatIndia(response[0].cus_limit));
        $('#about_cus').val(response[0].about_cus);
        dataCheckList(response[0].cus_id, response[0].cus_name, response[0].mobile1 , response[0].aadhar_num);
        if (response[0].whatsapp_no === response[0].mobile1) {
            $('#mobile1_radio').prop('checked', true);
            $('#selected_mobile_radio').val('mobile1');
        } else if (response[0].whatsapp_no === response[0].mobile2) {
            $('#mobile2_radio').prop('checked', true);
            $('#selected_mobile_radio').val('mobile2');
        }
        getGuarantorName()
        getAreaName()
        setTimeout(() => {
            getFamilyInfoTable()
            getPropertyInfoTable()
            getBankInfoTable()
            getKycInfoTable()
            $('#area').trigger('change');
            $('#guarantor_name').trigger('change');
        }, 5000);
        if (response[0].cus_data == 'Existing') {
            $('.cus_status_div').show();
            checkAdditionalRenewal(response[0].cus_id);
            $('#data_checking_div').show();
            $('#checking_hide').show();

        } else {
            $('.cus_status_div').hide();
            $('#data_checking_div').hide();
            $('#checking_hide').hide();
            $('#data_checking_table_div').hide();
        }
        let path = "uploads/loan_entry/cus_pic/";
        if (response[0].pic) {
            $('#per_pic').val(response[0].pic);
            var img = $('#imgshow');
            img.attr('src', path + response[0].pic);
        }
        else {
            $('#imgshow').attr('src', 'img/avatar.png');
        }
        let paths = "uploads/loan_entry/gu_pic/";
        if (response[0].gu_pic) {
            $('#gur_pic').val(response[0].gu_pic);
            $('#gur_imgshow').attr('src', paths + response[0].gu_pic);
        } else {
            $('#gur_imgshow').attr('src', 'img/avatar.png');
        }
        $('.personal_info_disble').attr("disabled", true);
        $('#submit_personal_info').attr('disabled', true);
    }, 'json');
}
///////////////////////////////////////////////Customer Profile js End//////////////////////////////
//////////////////////////////////////////////////////////////// Documentation START //////////////////////////////////////////////////////////////////////
$(document).ready(function () {
    $(document).on('click', '.doc-update', function () {
        let id = $(this).attr('value'); //Customer Profile id From List page.
        $('#customer_profile_id').val(id);
        let cusID = $(this).attr('data-id'); //Cus id From List Page.
        $('#cus_id_upd').val(cusID);
        $('#document_type_div').show();
        $('#document_type').val('');
        $('#cheque_info_card').hide();
        $('#document_info_card').hide();
        $('#mortgage_info_card').hide();
        $('#endorsement_info_card').hide();
        $('#gold_info_card').hide();
        getChequeInfoTable();
        getDocInfoTable();
        getMortInfoTable();
        getEndorsementInfoTable();
        getGoldInfoTable();
    });


    $('#document_type').change(function () {
        var documentType = $(this).val();
        // Hide all         
        $('#cheque_info_card').hide();
        $('#document_info_card').hide();
        $('#mortgage_info_card').hide();
        $('#endorsement_info_card').hide();
        $('#gold_info_card').hide();
        if (documentType == '1') {
            $('#cheque_info_card').show();
        } else if (documentType == '2') {
            $('#document_info_card').show();
        } else if (documentType == '3') {
            $('#mortgage_info_card').show();
        }
        else if (documentType == '4') {
            $('#endorsement_info_card').show();
        }
        else if (documentType == '5') {
            $('#gold_info_card').show();
        }
        getChequeInfoTable();
        getDocInfoTable();
        getMortInfoTable();
        getEndorsementInfoTable();
        getGoldInfoTable();
    });
    ///////////////////////////////////////////////////////////////////Cheque info START ////////////////////////////////////////////////////////////////////////////
    $('#cq_holder_type').change(function () {
        let holderType = $(this).val();
        emptyholderFields();
        if (holderType == '1' || holderType == '2') {
            $('.cq_fam_member').hide();
            let cus_profile_id = $('#customer_profile_id').val();
            getNameRelationship(cus_profile_id, holderType);
        } else if (holderType == '3') {
            getFamilyMember('Select Family Member', '#cq_fam_mem');
            $('.cq_fam_member').show();
        } else {
            $('.cq_fam_member').hide();
        }
    });

    $('#cq_fam_mem').change(function () {
        let famMemId = $(this).val();
        if (famMemId != '') {
            getNameRelationship(famMemId, '3');
        }
    });

    $('#cheque_count').keyup(function () {
        $('#cheque_no').empty();
        let cnt = $(this).val();
        if (cnt != '') {
            for (let i = 1; i <= cnt; i++) {
                $('#cheque_no').append("<div class='col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12'><div class='form-group'><input type='number' class='form-control chequeno' name='chequeno[]' id='chequeno'/> </div></div>")
            }
        }
    });

    $('#submit_cheque_info').click(function (event) {
        event.preventDefault();
        let cus_id = $('#cus_id_upd').val();
        let cq_holder_type = $('#cq_holder_type').val();
        let cq_holder_name = $("#cq_holder_name").val();
        let cq_holder_id = $("#cq_holder_name").attr('data-id');
        let cq_relationship = $('#cq_relationship').val();
        let cq_bank_name = $('#cq_bank_name').val();
        let cheque_count = $('#cheque_count').val();
        let cq_upload = $('#cq_upload')[0].files;
        let cq_upload_edit = $('#cq_upload_edit').val();
        let customer_profile_id = $('#customer_profile_id').val();
        let cheque_info_id = $('#cheque_info_id').val();

        let chequeNoArr = []; //for storing cheque no
        let i = 0;
        $('.chequeno').each(function () {//cheque numbers input box
            chequeNoArr[i] = $(this).val();//store each numbers in an array
            i++;
        });
        var data = ['cq_holder_type', 'cq_holder_name', 'cq_relationship', 'cq_bank_name', 'cheque_count']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
        if (isValid) {
            let chequeInfo = new FormData();
            chequeInfo.append('cq_holder_type', cq_holder_type)
            chequeInfo.append('cq_holder_name', cq_holder_name)
            chequeInfo.append('cq_holder_id', cq_holder_id)
            chequeInfo.append('cq_relationship', cq_relationship)
            chequeInfo.append('cheque_count', cheque_count)
            chequeInfo.append('cq_bank_name', cq_bank_name)
            chequeInfo.append('cq_upload', cq_upload)
            chequeInfo.append('cq_upload_edit', cq_upload_edit)
            chequeInfo.append('cheque_no', chequeNoArr)
            chequeInfo.append('cus_id', cus_id)
            chequeInfo.append('customer_profile_id', customer_profile_id)
            chequeInfo.append('id', cheque_info_id)

            for (var a = 0; a < cq_upload.length; a++) {
                chequeInfo.append('cq_upload[]', cq_upload[a])
            }

            $.ajax({
                url: 'api/loan_issue_files/submit_cheque_info.php',
                type: 'post',
                data: chequeInfo,
                contentType: false,
                processData: false,
                cache: false,
                success: function (response) {
                    if (response == '1') {
                        swalSuccess('Success', 'Cheque Info Updated Successfully')
                    } else if (response == '2') {
                        swalSuccess('Success', 'Cheque Info Added Successfully')
                    } else {
                        swalSuccess('Alert', 'Failed')
                    }
                    getChequeCreationTable();
                    $('#clear_cheque_form').trigger('click');
                    $('#cheque_info_id').val('');
                    $('.cq_fam_member').hide();
                }
            });
        }
    });

    $(document).on('click', '.chequeActionBtn', function () {
        let id = $(this).attr('value');
        $.post('api/loan_issue_files/cheque_info_data.php', { id }, function (response) {
            $('#cq_holder_type').val(response.result[0].holder_type);
            $('#cq_holder_name').val(response.result[0].holder_name);
            $('#cq_holder_name').attr('data-id', response.result[0].holder_id);
            $('#cq_relationship').val(response.result[0].relationship);
            $('#cq_bank_name').val(response.result[0].bank_name);
            $('#cheque_count').val(response.result[0].cheque_cnt);
            $('#cq_upload_edit').val(response.result[0].upload);
            $('#cheque_info_id').val(response.result[0].id);
            if (response.result[0].holder_type == '3') {
                getFamilyMember('Select Family Member', '#cq_fam_mem')
                $('.cq_fam_member').show();
                setTimeout(() => {
                    $('#cq_fam_mem').val(response.result[0].holder_id);
                }, 1000);
            } else {
                $('#cq_fam_mem').val('');
                $('.cq_fam_member').hide();
            }

            $('#cheque_no').empty();
            for (let key in response.no) {
                let cheque = response.no[key];
                $('#cheque_no').append("<div class='col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12'><div class='form-group'><input type='number' class='form-control chequeno' name='chequeno[]' id='chequeno' value='" + cheque['cheque_no'] + "'/> </div></div>");
            }

        }, 'json');
    });

    $(document).on('click', '.chequeDeleteBtn', function () {
        let id = $(this).attr('value');
        swalConfirm('Delete', 'Are you sure you want to delete this Cheque?', deleteChequeInfo, id);
    });

    $('#clear_cheque_form').click(function () {
        $('#cheque_no').empty();
        $('#cheque_info_id').val('');
        $('#cheque_info_form input').css('border', '1px solid #cecece');
        $('#cheque_info_form select').css('border', '1px solid #cecece');
        $('.cq_fam_member').hide();
    });
    ///////////////////////////////////////////////////////////////////Cheque info END ////////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////Document info START ////////////////////////////////////////////////////////////////////////////
    $('#doc_holder_name').change(function () {
        let id = $(this).val();
        if (id != '' && id != 0) {
            getRelationship(id, '#doc_relationship')
        } else if (id == 0) {
            $('#doc_relationship').val('Customer');
        }
        else {
            $('#doc_relationship').val('');
        }

    });

    $('#submit_doc_info').click(function (event) {
        event.preventDefault();
        let doc_name = $('#doc_name').val();
        let doc_type = $('#doc_type').val();
        let doc_holder_name = $('#doc_holder_name').val();
        let doc_relationship = $('#doc_relationship').val();
        let doc_upload = $('#doc_upload')[0].files[0];
        let doc_upload_edit = $('#doc_upload_edit').val();
        let doc_info_id = $('#doc_info_id').val();
        let cus_id = $('#cus_id_upd').val();
        let customer_profile_id = $('#customer_profile_id').val();

        var data = ['doc_name', 'doc_type', 'doc_holder_name', 'doc_relationship']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
        if (doc_upload === undefined && doc_upload_edit === '') {
            let isUploadValid = validateField('', 'doc_upload');
            let isHiddenValid = validateField('', 'doc_upload_edit');
            if (!isUploadValid || !isHiddenValid) {
                isValid = false;
            }
            else {
                $('#doc_upload').css('border', '1px solid #cecece');
                $('#doc_upload_edit').css('border', '1px solid #cecece');
            }
        }
        else {
            $('#doc_upload').css('border', '1px solid #cecece');
            $('#doc_upload_edit').css('border', '1px solid #cecece');
        }

        if (isValid) {
            let docInfo = new FormData();
            docInfo.append('doc_name', doc_name);
            docInfo.append('doc_type', doc_type);
            docInfo.append('doc_holder_name', doc_holder_name);
            docInfo.append('doc_relationship', doc_relationship);
            docInfo.append('doc_upload', doc_upload);
            docInfo.append('doc_upload_edit', doc_upload_edit);
            docInfo.append('cus_id', cus_id);
            docInfo.append('customer_profile_id', customer_profile_id);
            docInfo.append('id', doc_info_id);

            $.ajax({
                url: 'api/loan_issue_files/submit_document_info.php',
                type: 'post',
                data: docInfo,
                contentType: false,
                processData: false,
                cache: false,
                success: function (response) {
                    if (response == '1') {
                        swalSuccess('Success', 'Document Info Updated Successfully')
                    } else if (response == '2') {
                        swalSuccess('Success', 'Document Info Added Successfully')
                    } else {
                        swalError('Alert', 'Failed')
                    }
                    getDocCreationTable();
                    $('#clear_doc_form').trigger('click');
                    $('#doc_info_id').val('');
                }
            });
        }
    });

    $(document).on('click', '.docActionBtn', function () {
        let id = $(this).attr('value');
        $.post('api/loan_issue_files/doc_info_data.php', { id }, function (response) {
            $('#doc_name').val(response[0].doc_name);
            $('#doc_type').val(response[0].doc_type);
            $('#doc_holder_name').val(response[0].holder_name);
            $('#doc_relationship').val(response[0].relationship);
            $('#doc_upload_edit').val(response[0].upload);
            $('#doc_info_id').val(response[0].id);
        }, 'json');
    });

    $(document).on('click', '.docDeleteBtn', function () {
        let id = $(this).attr('value');
        swalConfirm('Delete', 'Are you sure you want to delete this document?', deleteDocInfo, id);
    });

    $('#clear_doc_form').click(function () {
        $('#doc_info_id').val('');
        $('#doc_upload_edit').val('');
        $('#doc_info_form input').css('border', '1px solid #cecece');
        $('#doc_info_form select').css('border', '1px solid #cecece');
    })
    ///////////////////////////////////////////////////////////////////Document info END ////////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////Mortgage info START ////////////////////////////////////////////////////////////////////////////
    $('#property_holder_name').change(function () {
        let id = $(this).val();
        if (id != '' && id != 0) {
            getRelationship(id, '#mort_relationship')
        } else if (id == 0) {
            $('#mort_relationship').val('Customer');
        } else {
            $('#mort_relationship').val('');
        }

    });

    $('#submit_mortgage_info').click(function (event) {
        event.preventDefault();
        let property_holder_name = $('#property_holder_name').val();
        let mort_relationship = $('#mort_relationship').val();
        let mort_property_details = $('#mort_property_details').val();
        let mortgage_name = $('#mortgage_name').val();
        let mort_designation = $('#mort_designation').val();
        let mortgage_no = $('#mortgage_no').val();
        let reg_office = $('#reg_office').val();
        let mortgage_value = $('#mortgage_value').val();
        let mortgage_info_id = $('#mortgage_info_id').val();
        let cus_id = $('#cus_id_upd').val();
        let customer_profile_id = $('#customer_profile_id').val();
        let mort_upload = $('#mort_upload')[0].files[0];
        let mort_upload_edit = $('#mort_upload_edit').val();

        var data = ['property_holder_name', 'mort_relationship', 'mort_property_details', 'mortgage_name', 'mort_designation', 'mortgage_no', 'reg_office', 'mortgage_value']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
        if (mort_upload === undefined && mort_upload_edit === '') {
            let isUploadValid = validateField('', 'mort_upload');
            let isHiddenValid = validateField('', 'mort_upload_edit');
            if (!isUploadValid || !isHiddenValid) {
                isValid = false;
            }
            else {
                $('#mort_upload').css('border', '1px solid #cecece');
                $('#mort_upload').css('border', '1px solid #cecece');
            }
        }
        else {
            $('#mort_upload').css('border', '1px solid #cecece');
            $('#mort_upload_edit').css('border', '1px solid #cecece');
        }

        if (isValid) {
            let mortgageInfo = new FormData();
            mortgageInfo.append('property_holder_name', property_holder_name);
            mortgageInfo.append('mort_relationship', mort_relationship);
            mortgageInfo.append('mort_property_details', mort_property_details);
            mortgageInfo.append('mortgage_name', mortgage_name);
            mortgageInfo.append('mort_designation', mort_designation);
            mortgageInfo.append('mortgage_no', mortgage_no);
            mortgageInfo.append('reg_office', reg_office);
            mortgageInfo.append('mortgage_value', mortgage_value);
            mortgageInfo.append('mort_upload', mort_upload);
            mortgageInfo.append('mort_upload_edit', mort_upload_edit);
            mortgageInfo.append('cus_id', cus_id);
            mortgageInfo.append('customer_profile_id', customer_profile_id);
            mortgageInfo.append('id', mortgage_info_id);

            $.ajax({
                url: 'api/loan_issue_files/submit_mortgage_info.php',
                type: 'post',
                data: mortgageInfo,
                contentType: false,
                processData: false,
                cache: false,
                success: function (response) {
                    if (response == '1') {
                        swalSuccess('Success', 'Mortgage Info Updated Successfully')
                    } else if (response == '2') {
                        swalSuccess('Success', 'Mortgage Info Added Successfully')
                    } else {
                        swalError('Alert', 'Failed')
                    }
                    getMortCreationTable()
                    $('#clear_mortgage_form').trigger('click');
                    $('#mortgage_info_id').val('');
                }
            });
        }
    });

    $(document).on('click', '.mortActionBtn', function () {
        let id = $(this).attr('value');
        $.post('api/loan_issue_files/mortgage_info_data.php', { id }, function (response) {
            $('#property_holder_name').val(response[0].property_holder_name);
            $('#mort_relationship').val(response[0].relationship);
            $('#mort_property_details').val(response[0].property_details);
            $('#mortgage_name').val(response[0].mortgage_name);
            $('#mort_designation').val(response[0].designation);
            $('#mortgage_no').val(response[0].mortgage_number);
            $('#reg_office').val(response[0].reg_office);
            $('#mortgage_value').val(response[0].mortgage_value);
            $('#mort_upload_edit').val(response[0].upload);
            $('#mortgage_info_id').val(response[0].id);
        }, 'json');
    });

    $(document).on('click', '.mortDeleteBtn', function () {
        let id = $(this).attr('value');
        swalConfirm('Delete', 'Are you sure you want to delete this Mortgage?', deleteMortgageInfo, id);
    });

    $('#clear_mortgage_form').click(function () {
        $('#mortgage_info_id').val('');
        $('#mort_upload_edit').val('');
        $('#mortgage_form input').css('border', '1px solid #cecece');
        $('#mortgage_form select').css('border', '1px solid #cecece');
        $('#mortgage_form textarea').css('border', '1px solid #cecece');
    })
    ///////////////////////////////////////////////////////////////////Mortgage info END ////////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////Endorsement info START ////////////////////////////////////////////////////////////////////////////
    $('#owner_name').change(function () {
        let id = $(this).val();
        if (id != '' && id != 0) {
            getRelationship(id, '#owner_relationship')
        } else if (id == 0) {
            $('#owner_relationship').val('Customer');
        } else {
            $('#owner_relationship').val('');
        }
    });

    $('#submit_endorsement').click(function (event) {
        event.preventDefault();
        let owner_name = $('#owner_name').val();
        let owner_relationship = $('#owner_relationship').val();
        let vehicle_details = $('#vehicle_details').val();
        let endorsement_name = $('#endorsement_name').val();
        let key_original = $('#key_original').val();
        let rc_original = $('#rc_original').val();
        let endorsement_upload = $('#endorsement_upload')[0].files[0];
        let endorsement_upload_edit = $('#endorsement_upload_edit').val();
        let endorsement_info_id = $('#endorsement_info_id').val();
        let cus_id = $('#cus_id_upd').val();
        let customer_profile_id = $('#customer_profile_id').val();
        var data = ['owner_name', 'owner_relationship', 'vehicle_details', 'endorsement_name', 'key_original', 'rc_original']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
        if (endorsement_upload === undefined && endorsement_upload_edit === '') {
            let isUploadValid = validateField('', 'endorsement_upload');
            let isHiddenValid = validateField('', 'endorsement_upload_edit');
            if (!isUploadValid || !isHiddenValid) {
                isValid = false;
            }
            else {
                $('#endorsement_upload').css('border', '1px solid #cecece');
                $('#endorsement_upload_edit').css('border', '1px solid #cecece');
            }
        }
        else {
            $('#endorsement_upload').css('border', '1px solid #cecece');
            $('#endorsement_upload_edit').css('border', '1px solid #cecece');
        }

        if (isValid) {
            let endorsementInfo = new FormData();
            endorsementInfo.append('owner_name', owner_name);
            endorsementInfo.append('owner_relationship', owner_relationship);
            endorsementInfo.append('vehicle_details', vehicle_details);
            endorsementInfo.append('endorsement_name', endorsement_name);
            endorsementInfo.append('key_original', key_original);
            endorsementInfo.append('rc_original', rc_original);
            endorsementInfo.append('endorsement_upload', endorsement_upload);
            endorsementInfo.append('endorsement_upload_edit', endorsement_upload_edit);
            endorsementInfo.append('cus_id', cus_id);
            endorsementInfo.append('customer_profile_id', customer_profile_id);
            endorsementInfo.append('id', endorsement_info_id);

            $.ajax({
                url: 'api/loan_issue_files/submit_endorsement_info.php',
                type: 'post',
                data: endorsementInfo,
                contentType: false,
                processData: false,
                cache: false,
                success: function (response) {
                    if (response == '1') {
                        swalSuccess('Success', 'Endorsement Info Updated Successfully')
                    } else if (response == '2') {
                        swalSuccess('Success', 'Endorsement Info Added Successfully')
                    } else {
                        swalError('Alert', 'Failed')
                    }
                    getEndorsementCreationTable()
                    $('#clear_endorsement_form').trigger('click');
                    $('#endorsement_info_id').val('');
                }
            });
        }
    });


    $(document).on('click', '.endorseActionBtn', function () {
        let id = $(this).attr('value');
        $.post('api/loan_issue_files/endorsement_info_data.php', { id }, function (response) {
            $('#owner_name').val(response[0].owner_name);
            $('#owner_relationship').val(response[0].relationship);
            $('#vehicle_details').val(response[0].vehicle_details);
            $('#endorsement_name').val(response[0].endorsement_name);
            $('#key_original').val(response[0].key_original);
            $('#rc_original').val(response[0].rc_original);
            $('#endorsement_upload_edit').val(response[0].upload);
            $('#endorsement_info_id').val(response[0].id);
        }, 'json');
    });

    $(document).on('click', '.endorseDeleteBtn', function () {
        let id = $(this).attr('value');
        swalConfirm('Delete', 'Are you sure you want to delete this Endorsement?', deleteEndorsementInfo, id);
    });

    $('#clear_endorsement_form').click(function () {
        $('#endorsement_info_id').val('');
        $('#endorsement_upload_edit').val('');
        $('#endorsement_form input').css('border', '1px solid #cecece');
        $('#endorsement_form select').css('border', '1px solid #cecece');
        $('#endorsement_form textarea').css('border', '1px solid #cecece');
    });

    ///////////////////////////////////////////////////////////////////Endorsement info END ////////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////Gold info START ////////////////////////////////////////////////////////////////////////////
    $('#submit_gold_info').click(function (event) {
        event.preventDefault();
        let goldInfo = {
            'cus_id': $('#cus_id_upd').val(),
            'customer_profile_id': $('#customer_profile_id').val(),
            'gold_type': $('#gold_type').val(),
            'purity': $('#gold_purity').val(),
            'weight': $('#gold_weight').val(),
            'value': $('#gold_value').val(),
            'id': $('#gold_info_id').val(),
        };
        var data = ['gold_type', 'gold_purity', 'gold_weight', 'gold_value']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
        if (isValid) {
            $.post('api/loan_issue_files/submit_gold_info.php', goldInfo, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Gold Info Updated Successfully')
                } else if (response == '2') {
                    swalSuccess('Success', 'Gold Info Added Successfully')
                } else {
                    swalError('Alert', 'Failed')
                }
                getGoldCreationTable()
                $('#clear_gold_form').trigger('click');
                $('#gold_info_id').val('');
            });
        }
    });

    $(document).on('click', '.goldActionBtn', function () {
        let id = $(this).attr('value');
        $.post('api/loan_issue_files/gold_info_data.php', { id }, function (response) {
            $('#gold_type').val(response[0].gold_type);
            $('#gold_purity').val(response[0].purity);
            $('#gold_weight').val(response[0].weight);
            $('#gold_value').val(response[0].value);
            $('#gold_info_id').val(response[0].id);
        }, 'json');
    });

    $(document).on('click', '.goldDeleteBtn', function () {
        let id = $(this).attr('value');
        swalConfirm('Delete', 'Are you sure you want to delete this Gold Info?', deleteGoldInfo, id);
    });

    $('#clear_gold_form').click(function () {
        $('#gold_info_id').val('');
        $('#gold_form input').css('border', '1px solid #cecece');
        $('#gold_form select').css('border', '1px solid #cecece');
    });
    / ///////////////////////////////////////////////////////////////////Gold info END ////////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////Document Print START ////////////////////////////////////////////////////////////////////////////
    $(document).on('click', '.doc-print', function () {
        let cus_profile_id = $('#customer_profile_id').val();
        // Open a new window or tab
        var printWindow = window.open('', '_blank');

        // Make sure the popup window is not blocked
        if (printWindow) {
            // Load the content into the popup window
            $.ajax({
                url: 'api/loan_issue_files/print_document.php',
                data: { cus_profile_id },
                cache: false,
                type: "post",
                success: function (html) {
                    // Write the content to the new window
                    printWindow.document.open();
                    printWindow.document.write(html);
                    printWindow.document.close();

                    // Optionally, print the content
                    printWindow.print();
                },
                error: function () {
                    // Handle error
                    printWindow.close();
                    alert('Failed to load print content.');
                }
            });
        } else {
            alert('Popup blocked. Please allow popups for this website.');
        }
    })
    ///////////////////////////////////////////////////////////////////Document Print END ////////////////////////////////////////////////////////////////////////////

}); ///////////////////////////////////////////////////////////////// Documentation - Document END ////////////////////////////////////////////////////////////////////

//On Load function 
$(function () {
});

function getLoanListTable(cus_id, pending_sts, od_sts, due_nil_sts, balAmnt) {
    // let cus_id = $('#cus_id_upd').val();
    $.post('api/update_customer_files/update_document_list.php', { cus_id, pending_sts, od_sts, due_nil_sts, balAmnt }, function (response) {
        var columnMapping = [
            'sno',
            'loan_id',
            'loan_category',
            'loan_date',
            'loan_amount',
            'closed_date',
            'c_sts',
            'sub_status',
            'action'
        ];
        appendDataToTable('#loan_list_table', response, columnMapping);
        setdtable('#loan_list_table', "Loan List");
        //Dropdown in List Screen
        setDropdownScripts();
    }, 'json');
}

function OnLoadFunctions(cus_id) {
    //To get loan sub Status
    var pending_arr = [];
    var od_arr = [];
    var due_nil_arr = [];
    var balAmnt = [];
    $.ajax({
        url: 'api/collection_files/resetCustomerStatus.php',
        data: { 'cus_id': cus_id },
        dataType: 'json',
        type: 'post',
        cache: false,
        success: function (response) {
            if (response.follow_cus_sts != null) {
                for (var i = 0; i < response['pending_customer'].length; i++) {
                    pending_arr[i] = response['pending_customer'][i]
                    od_arr[i] = response['od_customer'][i]
                    due_nil_arr[i] = response['due_nil_customer'][i]
                    balAmnt[i] = response['balAmnt'][i]
                }
                var pending_sts = pending_arr.join(',');
                $('#pending_sts').val(pending_sts);
                var od_sts = od_arr.join(',');
                $('#od_sts').val(od_sts);
                var due_nil_sts = due_nil_arr.join(',');
                $('#due_nil_sts').val(due_nil_sts);
                balAmnt = balAmnt.join(',');
            }
        }
    }).then(function () {
        showOverlay();//loader start
        var pending_sts = $('#pending_sts').val()
        var od_sts = $('#od_sts').val()
        var due_nil_sts = $('#due_nil_sts').val()
        getLoanListTable(cus_id, pending_sts, od_sts, due_nil_sts, balAmnt)
        hideOverlay();//loader stop
    });
}//Auto Load function END

function getFamilyMember(optn, selector) {
    let cus_id = $('#cus_id_upd').val();
    let cus_name = $('#cus_name').val();
    $.post('api/loan_entry/get_guarantor_name.php', { cus_id }, function (response) {
        let appendOption = '';
        appendOption += "<option value=''>" + optn + "</option>"; // Default option
        appendOption += "<option value='" + 0 + "'>" + cus_name + "</option>";

        // Dynamic options from the response
        $.each(response, function (index, val) {
            appendOption += "<option value='" + val.id + "'>" + val.fam_name + "</option>";
        });

        $(selector).empty().append(appendOption);
    }, 'json');
}

function getNameRelationship(id, type) {
    $.post('api/loan_issue_files/get_cus_fam_members.php', { id, type }, function (response) {
        if (type == '1') {
            $('#cq_holder_name').val(response[0].cus_name);
            $('#cq_relationship').val('Customer');
        } else {
            $('#cq_holder_name').val(response[0].fam_name);
            $('#cq_holder_name').attr('data-id', response[0].id);
            $('#cq_relationship').val(response[0].fam_relationship);
        }
    }, 'json');
}

function getRelationship(id, selector) {
    $.post('api/loan_entry/family_creation_data.php', { id }, function (response) {
        $(selector).val(response[0].fam_relationship);
    }, 'json');
}

function emptyholderFields() {
    $('#cq_fam_mem').val('');
    $('#cq_holder_name').val('');
    $('#cq_holder_name').attr('data-id', '');
    $('#cq_relationship').val('');
}

function getChequeCreationTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/cheque_info_list.php', { cus_profile_id }, function (response) {
        let chequeColumn = [
            "sno",
            "holder_type",
            "holder_name",
            "relationship",
            "bank_name",
            "cheque_cnt",
            "upload",
            "action"
        ]
        appendDataToTable('#cheque_creation_table', response, chequeColumn);
        setdtable('#cheque_creation_table', "Cheque List");
    }, 'json');
}

function getChequeInfoTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/cheque_info_list.php', { cus_profile_id }, function (response) {
        if (response && response.length > 0) {
            // Show the cheque div and populate the table if the condition is met
            $('#cheque_info_card').show();
        }
        let chequeColumn = [
            "sno",
            "holder_type",
            "holder_name",
            "relationship",
            "bank_name",
            "cheque_cnt",
            "upload"
        ]
        appendDataToTable('#cheque_info_table', response, chequeColumn);
        setdtable('#cheque_info_table', "Cheque List");
    }, 'json');
}

function deleteChequeInfo(id) {
    $.post('api/loan_issue_files/delete_cheque_info.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('success', 'Cheque Info Deleted Successfully');
            getChequeCreationTable();
        } else {
            swalError('Alert', 'Delete Failed')
        }
    }, 'json');
}

function getDocCreationTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/doc_info_list.php', { cus_profile_id }, function (response) {
        let docInfoColumn = [
            "sno",
            "doc_name",
            "doc_type",
            "holder_name",
            "relationship",
            "upload",
            "action"
        ]
        appendDataToTable('#doc_creation_table', response, docInfoColumn);
        setdtable('#doc_creation_table', "Document List");
    }, 'json');
}

function refreshChequeModal() {
    $('#clear_cheque_form').trigger('click');
}

function getDocInfoTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/doc_info_list.php', { cus_profile_id }, function (response) {
        if (response && response.length > 0) {
            // Show the cheque div and populate the table if the condition is met
            $('#document_info_card').show();
        }
        let docColumn = [
            "sno",
            "doc_name",
            "doc_type",
            "holder_name",
            "relationship",
            "upload"
        ]
        appendDataToTable('#document_info', response, docColumn);
        setdtable('#document_info', "Document List");
    }, 'json');
}

function deleteDocInfo(id) {
    $.post('api/loan_issue_files/delete_doc_info.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('success', 'Doc Info Deleted Successfully');
            getDocCreationTable();
        } else {
            swalError('Alert', 'Delete Failed')
        }
    }, 'json');
}

function refreshDocModal() {
    $('#clear_doc_form').trigger('click');
}

function getMortCreationTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/mortgage_info_list.php', { cus_profile_id }, function (response) {
        let mortInfoColumn = [
            "sno",
            "holder_name",
            "relationship",
            "property_details",
            "mortgage_name",
            "designation",
            "mortgage_number",
            "reg_office",
            "mortgage_value",
            "upload",
            "action"
        ]
        appendDataToTable('#mortgage_creation_table', response, mortInfoColumn);
        setdtable('#mortgage_creation_table', "Mortgage List");
    }, 'json');
}

function getMortInfoTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/mortgage_info_list.php', { cus_profile_id }, function (response) {
        if (response && response.length > 0) {
            // Show the cheque div and populate the table if the condition is met
            $('#mortgage_info_card').show();
        }
        let mortgageColumn = [
            "sno",
            "holder_name",
            "relationship",
            "property_details",
            "mortgage_name",
            "designation",
            "mortgage_number",
            "reg_office",
            "mortgage_value",
            "upload"
        ]
        appendDataToTable('#mortgage_info', response, mortgageColumn);
        setdtable('#mortgage_info', "Mortgage List");
    }, 'json');
}

function deleteMortgageInfo(id) {
    $.post('api/loan_issue_files/delete_mortgage_info.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('success', 'Mortgage Info Deleted Successfully');
            getMortCreationTable();
        } else {
            swalError('Alert', 'Delete Failed')
        }
    }, 'json');
}

function refreshMortModal() {
    $('#clear_mortgage_form').trigger('click');
}

function getEndorsementCreationTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/endorsement_info_list.php', { cus_profile_id }, function (response) {
        let endorsementInfoColumn = [
            "sno",
            "holder_name",
            "relationship",
            "vehicle_details",
            "endorsement_name",
            "key_original",
            "rc_original",
            "upload",
            "action"
        ]
        appendDataToTable('#endorsement_creation_table', response, endorsementInfoColumn);
        setdtable('#endorsement_creation_table', "Endorsement Info List");
    }, 'json');
}

function getEndorsementInfoTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/endorsement_info_list.php', { cus_profile_id }, function (response) {
        if (response && response.length > 0) {
            // Show the cheque div and populate the table if the condition is met
            $('#endorsement_info_card').show();
        }
        let endorsementColumn = [
            "sno",
            "holder_name",
            "relationship",
            "vehicle_details",
            "endorsement_name",
            "key_original",
            "rc_original",
            "upload"
        ]
        appendDataToTable('#endorsement_info', response, endorsementColumn);
        setdtable('#endorsement_info', "Endorsement Info List");
    }, 'json');
}

function deleteEndorsementInfo(id) {
    $.post('api/loan_issue_files/delete_endorsement_info.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('success', 'Endorsement Info Deleted Successfully');
            getEndorsementCreationTable();
        } else {
            swalError('Alert', 'Delete Failed')
        }
    }, 'json');
}

function refreshEndorsementModal() {
    $('#clear_endorsement_form').trigger('click');
}

function getGoldCreationTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/gold_info_list.php', { cus_profile_id }, function (response) {
        let goldInfoColumn = [
            "sno",
            "gold_type",
            "purity",
            "weight",
            "value",
            "action"
        ]
        appendDataToTable('#gold_creation_table', response, goldInfoColumn);
        setdtable('#gold_creation_table', "Gold Info List");
    }, 'json');
}

function getGoldInfoTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/gold_info_list.php', { cus_profile_id }, function (response) {
        if (response && response.length > 0) {
            // Show the cheque div and populate the table if the condition is met
            $('#gold_info_card').show();
        }
        let goldColumn = [
            "sno",
            "gold_type",
            "purity",
            "weight",
            "value"
        ]
        appendDataToTable('#gold_info', response, goldColumn);
        setdtable('#gold_info', "Gold Info List");
    }, 'json');
}

function deleteGoldInfo(id) {
    $.post('api/loan_issue_files/delete_gold_info.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('success', 'Gold Info Deleted Successfully');
            getGoldCreationTable();
        } else {
            swalError('Alert', 'Delete Failed')
        }
    }, 'json');
}

function refreshGoldModal() {
    $('#clear_gold_form').trigger('click');
}


//////////////////////////////////////////////
