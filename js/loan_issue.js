$(document).ready(function () {
    $(document).on('click', '.edit-loan-issue', function () {
        let id = $(this).attr('value'); //Customer Profile id From List page.
        $('#customer_profile_id').val(id);
        let cusID = $(this).attr('data-id'); //Cus id From List Page.
        $('#cus_id').val(cusID);
        $('.cheque-div').hide();
        $('.doc_div').hide();
        $('.mortgage-div').hide();
        $('.endorsement-div').hide();
        $('.gold-div').hide();
        $('#document_type').val('')
        swapTableAndCreation();
        getDocNeedTable(id);
        getChequeInfoTable();
        getDocInfoTable();
        getMortInfoTable();
        getEndorsementInfoTable();
        getGoldInfoTable();
    });

    $(document).on('click', '.loan-issue-cancel', function () {
        let cus_sts_id = $(this).attr('value');
        let cus_sts = 13;
        $('#add_info_modal').modal('show');
        $('.modal_revoke').text('Cancel');
        $('#cus_sts_id').val(cus_sts_id);
        $('#customer_status').val(cus_sts);
        $('#remark').val('');
    });

    $(document).on('click', '.loan-issue-revoke', function () {
        let cus_sts_id = $(this).attr('value');
        let cus_sts = 14;
        $('#add_info_modal').modal('show');
        $('.modal_revoke').text('Revoke');
        $('#cus_sts_id').val(cus_sts_id);
        $('#customer_status').val(cus_sts);
        $('#remark').val('');
    });

    $(document).on('click', '#submit_remark', function () {
        event.preventDefault();
        let action = $('.modal_revoke').text().toLowerCase(); // Get action (cancel or revoke)
        let cus_sts_id = $('#cus_sts_id').val();
        let cus_sts = $('#customer_status').val();
        let remark = $('#remark').val();

        if (remark === '') {
            alert('Please enter a remark.');
            return;
        }

        submitForm(action, cus_sts_id, cus_sts, remark);
    });

    $('#back_btn').click(function () {
        swapTableAndCreation();
    });

    $('input[name=loan_issue_type]').click(function () {
        let loanIssueType = $(this).val();
        if (loanIssueType == 'loandoc') {
            $('#documentation_form').show(); $('#loan_issue_form').hide();
        } else if (loanIssueType == 'loanissue') {
            $('#documentation_form').hide(); $('#loan_issue_form').show();
            callLoanCaculationFunctions();
        }
    })

    $('#document_type').change(function () {
        var documentType = $(this).val();
        // Hide all         
        $('.cheque-div').hide();
        $('.doc_div').hide();
        $('.mortgage-div').hide();
        $('.endorsement-div').hide();
        $('.gold-div').hide();

        if (documentType == '1') {
            $('.cheque-div').show();
        } else if (documentType == '2') {
            $('.doc_div').show();
        } else if (documentType == '3') {
            $('.mortgage-div').show();
        }
        else if (documentType == '4') {
            $('.endorsement-div').show();
        }
        else if (documentType == '5') {
            $('.gold-div').show();
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
        let cus_id = $('#cus_id').val();
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
                dataType: 'json',
                success: function (response) {
                    if (response == '1') {
                        swalSuccess('Success', 'Cheque Info Updated Successfully')
                    } else if (response == '2') {
                        swalSuccess('Success', 'Cheque Info Added Successfully')
                    } else {
                        swalError('Alert', 'Failed')
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
            if (response.upd.length > 0) {
                let uploadFiles = response.upd.map(fileObj => fileObj.uploads).filter(Boolean);
                $('#cq_upload_edit').val(uploadFiles.join(','));
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
        let cus_id = $('#cus_id').val();
        let customer_profile_id = $('#customer_profile_id').val();
        var data = ['doc_name', 'doc_type', 'doc_holder_name', 'doc_relationship']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
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
        let cus_id = $('#cus_id').val();
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
        let cus_id = $('#cus_id').val();
        let customer_profile_id = $('#customer_profile_id').val();

        var data = ['owner_name', 'owner_relationship', 'vehicle_details', 'endorsement_name', 'key_original', 'rc_original']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
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
            'cus_id': $('#cus_id').val(),
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
    ///////////////////////////////////////////////////////////////////Gold info END ////////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////Document Print START ////////////////////////////////////////////////////////////////////////////
    $('#print_doc').click(function () {
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
    getLoanIssueTable();
});

function getLoanIssueTable() {
    serverSideTable('#loan_issue_table', '', 'api/loan_issue_files/loan_issue_list.php', 'Loan Issue List');
}

function moveToNext(cus_sts_id, cus_sts) {
    $.post('api/common_files/move_to_next.php', { cus_sts_id, cus_sts }, function (response) {
        if (response == '0') {
            let alertName;
            if (cus_sts == '13') {
                alertName = 'Cancelled Successfully';
            }
            else if (cus_sts == '14') {
                alertName = 'Revoked Successfully';
            }
            swalSuccess('Success', alertName);
            getLoanIssueTable();
        } else {
            swalError('Alert', 'Failed To Move');
        }
    }, 'json');
}

function submitForm(action, cus_sts_id, cus_sts, remark) {
    $.post('api/common_files/update_status.php', { cus_sts_id, remark, cus_sts }, function (response) {
        if (response == '0') {
            $('#add_info_modal').modal('hide');
            moveToNext(cus_sts_id, cus_sts);
        } else {
            swalError('Alert', 'Failed to ' + action);
        }
    }, 'json');
}

function closeRemarkModal() {
    $('#add_info_modal').modal('hide');
}

function swapTableAndCreation() {
    if ($('.loanissue_table_content').is(':visible')) {
        $('.loanissue_table_content').hide();
        $('#loan_issue_content').show();
        $('#back_btn').show();

    } else {
        $('.loanissue_table_content').show();
        $('#loan_issue_content').hide();
        $('#back_btn').hide();
        $('#documentation').trigger('click');
        refreshIssueInfo();
    }
}

function getDocNeedTable(cusProfileId) {
    $.post('api/loan_entry/loan_calculation/document_need_list.php', { cusProfileId }, function (response) {
        let docColumn = [
            "sno",
            "document_name"
        ]
        appendDataToTable('#doc_need_table', response, docColumn);
        setdtable('#doc_need_table');
    }, 'json');
}

function getFamilyMember(optn, selector) {
    let cus_id = $('#cus_id').val();
    let holderType = $('#cq_holder_type').val(); // Get current holder type
    $.post('api/loan_issue_files/get_guarantor.php', { cus_id }, function (response) {
        let appendOption = '';
        appendOption += "<option value=''>" + optn + "</option>"; // Default option 

        // Dynamic options from the response
        $.each(response, function (index, val) {
            // Differentiating between customer and family member
            if (val.type === 'Customer' && holderType !== '3') {
                appendOption += "<option value='0'>" + val.name + "</option>";  // Customer
            } else if (val.type === 'Family') {
                appendOption += "<option value='" + val.id + "'>" + val.name + " </option>";  // Family Member
            }
        });

        $(selector).empty().append(appendOption);  // Append options to the select element
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
        setdtable('#cheque_creation_table', "Cheque Creation List");
    }, 'json');
}

function getChequeInfoTable() {
    let cus_profile_id = $('#customer_profile_id').val();

    $.post('api/loan_issue_files/cheque_info_list.php', { cus_profile_id }, function (response) {
        // Check if the response length is greater than 0
        if (response && response.length > 0) {
            // Show the cheque div and populate the table if the condition is met
            $('.cheque-div').show();
        }
        let chequeColumn = [
            "sno",
            "holder_type",
            "holder_name",
            "relationship",
            "bank_name",
            "cheque_cnt",
            "upload"
        ];

        appendDataToTable('#cheque_info_table', response, chequeColumn);
        setdtable('#cheque_info_table', "Cheque Info List");

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
        setdtable('#doc_creation_table', "Document Info List");
    }, 'json');
}

function refreshChequeModal() {
    $('#clear_cheque_form').trigger('click');
}

function getDocInfoTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/doc_info_list.php', { cus_profile_id }, function (response) {
        if (response && response.length > 0) {
            $('.doc_div').show();
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
        setdtable('#document_info', "Document Info List");

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
        setdtable('#mortgage_creation_table', "Mortgage Info List");
    }, 'json');
}

function getMortInfoTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/mortgage_info_list.php', { cus_profile_id }, function (response) {
        if (response && response.length > 0) {
            $('.mortgage-div').show();
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
        setdtable('#mortgage_info', "Mortgage Info List");
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
            $('.endorsement-div').show();
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
            $('.gold-div').show();
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


/////////////////////////////////////////////////////////// Loan Issue START////////////////////////////////////////////////
$(document).ready(function () {

    {
        // Get today's date
        var today = new Date().toISOString().split('T')[0];
        //Set loan date
        $('#issue_date').val(today);
    }

    $('#due_startdate_calc').change(function () {
        var due_start_from = $('#due_startdate_calc').val(); // get start date to calculate maturity date
        var due_period = parseInt($('#due_period_calc').val()); //get due period to calculate maturity date
        var profit_type = $('#profit_type_calc').val()
        if (profit_type == '0') { //Based on the profit method choose due method from input box
            var due_method = $('#due_method_calc').val()
        } else if (profit_type == '1') {
            var due_method = $('#scheme_due_method_calc').val()
        }

        if (due_period == '' || isNaN(due_period)) {
            swalError('Warning', 'Kindly Fill the Due Period field.');
            $(this).val('');
        } else {
            if (due_method == 'Monthly' || due_method == '1') { // if due method is monthly or 1(for scheme) then calculate maturity by month

                var maturityDate = moment(due_start_from, 'YYYY-MM-DD').add(due_period, 'months').subtract(1, 'month').format('YYYY-MM-DD');//subract one month because by default its showing extra one month
                $('#maturity_date_calc').val(maturityDate);

            } else if (due_method == '2') {//if Due method is weekly then calculate maturity by week

                var due_day = parseInt($('#scheme_day_calc').val());

                var momentStartDate = moment(due_start_from, 'YYYY-MM-DD').startOf('day').isoWeekday(due_day);//Create a moment.js object from the start date and set the day of the week to the due day value

                var weeksToAdd = Math.floor(due_period - 1);//Set the weeks to be added by giving due period. subract 1 because by default it taking extra 1 week

                momentStartDate.add(weeksToAdd, 'weeks'); //Add the calculated number of weeks to the start date.

                if (momentStartDate.isBefore(due_start_from)) {
                    momentStartDate.add(1, 'week'); //If the resulting maturity date is before the start date, add another week.
                }

                var maturityDate = momentStartDate.format('YYYY-MM-DD'); //Get the final maturity date as a formatted string.

                $('#maturity_date_calc').val(maturityDate);

            } else if (due_method == '3') {
                var momentStartDate = moment(due_start_from, 'YYYY-MM-DD').startOf('day');
                var daysToAdd = Math.floor(due_period - 1);
                momentStartDate.add(daysToAdd, 'days');
                var maturityDate = momentStartDate.format('YYYY-MM-DD');
                $('#maturity_date_calc').val(maturityDate);
            }
        }
    });

    $('#payment_type').change(function () {
        $('#payment_mode, #transaction_id, #chequeno, #cash, #chequeValue, #transaction_value, #bank_name, #chequeRemark, #transaction_remark').css('border', '1px solid #cecece');

        $('#cash').removeAttr('readonly');
        $('#chequeValue').removeAttr('readonly');
        $('#transaction_value').removeAttr('readonly');

        $('.cash_issue').hide();
        $('.checque').hide();
        $('.transaction').hide();
        $('#bank_container').hide(); // Hide bank section
        $('.balance_remark_container').hide();
        $('#balance_amount').val('');
        $('#payment_mode').val('');

    })

    // Payment Mode
    $('#payment_mode').change(function () {
        $('#payment_mode, #transaction_id, #chequeno, #cash, #chequeValue, #transaction_value, #bank_name, #chequeRemark, #transaction_remark').css('border', '1px solid #cecece');

        $('#transaction_id').val('');
        $('#chequeno').val('');
        $('#transaction_remark').val('');
        $('#chequeRemark').val('');
        var type = $(this).val();
        let paymentType = $('#payment_type').val();

        if (paymentType == '1') {  // Split Payment (Editable)

            $('#cash').attr('readonly', false);
            $('#chequeValue').attr('readonly', false);
            $('#transaction_value').attr('readonly', false);

            if (type == '1') {
                $('.balance_remark_container').show();
                $('#cash').val('');
                $('#chequeValue').val('');
                $('#transaction_value').val('');
                $('.transaction').hide();
                $('.checque').hide();
                $('.cash_issue').show();
                $('#bank_container').hide();
                $('#balance_amount').val('');

            } else if (type == '2') {
                $('#cash').val('');
                $('#chequeValue').val('');
                getBankName();
                $('#transaction_value').val('');
                $('.transaction').show();
                $('.checque').hide();
                $('.cash_issue').hide();
                $('#bank_container').show();
                $('.balance_remark_container').show();
                $('#balance_amount').val('');

            } else if (type == '3') {
                $('#transaction_value').val('');
                $('#cash').val('');
                $('#chequeValue').val('');
                getBankName();
                $('.transaction').hide();
                $('.checque').show();
                $('.cash_issue').hide();
                $('#bank_container').show();
                $('.balance_remark_container').show();
                $('#balance_amount').val('');
            }
            else {
                $('.transaction').hide();
                $('.checque').hide();
                $('.cash_issue').hide();
                $('#bank_container').hide();//hide bank id
            }
        } else if (paymentType == '2') {  // Single Payment (Read-only)

            $('#cash').attr('readonly', true);
            $('#chequeValue').attr('readonly', true);
            $('#transaction_value').attr('readonly', true);

            var netcash = $('#balance_net_cash').val();

            if (type == '1') {
                $('#cash').val(netcash);
                $('#chequeValue').val('');
                $('#transaction_value').val('');
                $('.transaction').hide();
                $('.checque').hide();
                $('.cash_issue').show();
                $('#bank_container').hide();

            } else if (type == '2') {
                $('#cash').val('');
                $('#chequeValue').val('');
                getBankName();
                $('#transaction_value').val(netcash);
                $('.transaction').show();
                $('.checque').hide();
                $('.cash_issue').hide();
                $('#bank_container').show();

            } else if (type == '3') {
                $('#transaction_value').val('');
                $('#cash').val('');
                $('#chequeValue').val(netcash);
                getBankName();
                $('.transaction').hide();
                $('.checque').show();
                $('.cash_issue').hide();
                $('#bank_container').show();
            }
            else {
                $('.transaction').hide();
                $('.checque').hide();
                $('.cash_issue').hide();
                $('#bank_container').hide();//hide bank id
            }
        }

        // $.post('api/loan_issue_files/get_balance_amount.php',{ 'cus_id': $('#cus_id').val(), 'payment_mode': type },function(response){
        //     let balance = parseFloat(response.balance);
        //     let issueAmount = parseFloat($('#issue_amount').val());
        //     let alertMessage = response.alert_message;
        //     if (issueAmount > balance) {
        //         let formattedMessage = `${alertMessage} ,\n\n Available Balance: ${balance}`;
        //         swalError('Warning', formattedMessage);
        //         $('#submit_loan_issue').attr('disabled', true);
        //     }else{
        //         $('#submit_loan_issue').attr('disabled', false);
        //     }
        // }, 'json');

    });
    $('#cash, #chequeValue, #transaction_value').on('input', function () {
        // Remove commas first, then parse to float
        let raw = $(this).val().replace(/,/g, '');
        $(this).val(formatIndianNumber(raw));
        let settle_balance = parseFloat($('#balance_net_cash').val().replace(/,/g, '')) || 0; // Convert to float, default to 0 if empty
        let payment_type = $('#payment_type').val();
        let cash_amount = parseFloat($('#cash').val().replace(/,/g, '')) || 0; // Convert to float, default to 0 if empty
        let che_amount = parseFloat($('#chequeValue').val().replace(/,/g, '')) || 0; // Convert to float, default to 0 if empty
        let trans_amount = parseFloat($('#transaction_value').val().replace(/,/g, '')) || 0; // Convert to float, default to 0 if empty
        if (payment_type == '1') { // Split Payment
            var totalAmount = cash_amount + che_amount + trans_amount;
            calculateBalance();
            // Compare totalAmount with settle_balance
            if (totalAmount > settle_balance) {
                swalError('Warning', 'The entered amount exceeds the Net Cash Balance.');
                $('#cash').val('');
                $('#chequeValue').val('');
                $('#transaction_value').val('');
                $('#balance_amount').val(0);
            }
        }
    });

    $('#issue_person').change(function () {
        let id = $('#issue_person :selected').attr('data-val');
        if (id != '' && id != 'Customer') {
            getRelationship(id, '#issue_relationship');
        } else if (id == 'Customer') {
            $('#issue_relationship').val('Customer');
        } else {
            $('#issue_relationship').val('');
        }
    });
    $('#bank_name').change(function(){
        $('#transaction_id, #trans_date, #bank_clr_bank_id, #bank_clr_trans_amnt').val('');
    });
    $("#transaction_id").keydown(function () {
        $('#trans_date').val('');
    });

    $('#submit_loan_issue').click(function (event) {
        event.preventDefault();
        let loanIssue = {
            'cus_id': $('#cus_id').val(),
            'cus_profile_id': $('#customer_profile_id').val(),
            'loan_amnt': $('#loan_amnt_calc').val().replace(/,/g, ''),
            'due_startdate': $('#due_startdate_calc').val(),
            'maturity_date': $('#maturity_date_calc').val(),
            'bal_net_cash': $('#balance_net_cash').val().replace(/,/g, ''),
            'bal_amount': $('#balance_amount').val().replace(/,/g, ''),
            'payment_type': $('#payment_type').val(),
            'cash': $('#cash').val().replace(/,/g, ''),
            'bank_name': $('#bank_name').val(),
            'chequeValue': $('#chequeValue').val().replace(/,/g, ''),
            'chequeRemark': $('#chequeRemark').val(),
            'transaction_remark': $('#transaction_remark').val(),
            'transaction_value': $('#transaction_value').val().replace(/,/g, ''),
            'net_cash': $('#net_cash_calc').val().replace(/,/g, ''),
            'payment_mode': $('#payment_mode').val(),
            'transaction_id': $('#transaction_id').val(),
            'chequeno': $('#chequeno').val(),
            'issue_date': $('#issue_date').val(),
            'issue_person': $('#issue_person').val(),
            'issue_relationship': $('#issue_relationship').val(),
            'bank_clr_bank_id' : $('#bank_clr_bank_id').val(),
            'bank_clr_trans_amnt' : $('#bank_clr_trans_amnt').val()
        }

        if (isFormDataValid(loanIssue)) {
            $.post('api/loan_issue_files/submit_loan_issue.php', loanIssue, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Loan Issued Successfully');
                    swapTableAndCreation();
                    getLoanIssueTable();
                } else {
                    swalError('Warning', 'Loan Issue Failed.');
                }
            });
        }

    });
$("#transaction_id").blur(async function () {
    try {

        let bankId = $('#bank_name').val();
        if(!bankId){
            swalError('Warning', 'Kindly select Bank Name!');
            $('#transaction_id').val('');
            return;
        }

        let payment_mode = $('#payment_mode').val();
        let transactionValue ='';

        if(payment_mode =='2'){
            transactionValue = $('#transaction_value').val() != '' 
                ? $('#transaction_value').val().replace(/,/g, '') 
                : 0;
        }

        if(!transactionValue){
            swalError('Warning', 'Kindly enter Transaction Value!');
            $('#transaction_id').val('');
            return;
        }

        let transId = $('#transaction_id').val();

        let response = await checkBankTransactionDetails('debit', bankId, transId, transactionValue);

        if (!response.status) {
            swalError('Warning', response.message);
            $('#transaction_id').val('');
            return;
        }

        let alertStatus = response.data.alert_status;

        if(alertStatus){
            swalError('Warning', response.data.alert);
            $('#transaction_id').val('');
        }else{
            $('#trans_date').val(response.data.trans_date);
            $('#bank_clr_bank_id').val(response.data.id);
            $('#bank_clr_trans_amnt').val(response.data.transaction_amount);
        }

    } catch (error) {
        console.error(error);
        swalError('Error', 'Something went wrong while checking transaction!');
    }
});

}); ///Document END.

$(function () {

});
function callLoanCaculationFunctions() {
    personalInfo();
    setTimeout(() => {
        checkBalance();
    }, 1000);

}

    function personalInfo() {
    let id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/loan_issue_data.php', { id }, function (response) {
        $('#aadhar_nums').val(response[0].aadhar_num);
        $('#cus_id').val(response[0].cus_id);
        $('#cus_name').val(response[0].cus_name);
        $('#cus_data').val(response[0].cus_data);
        $('#mobile1').val(response[0].mobile1);
        $('#cus_area').val(response[0].areaname);
        $('#loan_id_calc').val(response[0].loan_id);
        $('#loan_category_calc').val(response[0].loan_category);
        $('#category_info_calc').val(response[0].category_info);
        $('#loan_amnt_calc').val(moneyFormatIndia(response[0].loan_amnt));
        $('#profit_type_calc').val(response[0].profit_type);
        $('#due_method_calc').val(response[0].due_method);
        $('#scheme_due_method_calc').val(response[0].scheme_due_method);
        $('#scheme_day_calc').val(response[0].scheme_day);
        $('#due_type_calc').val(response[0].due_type);
        $('#scheme_name_calc').val(response[0].scheme_name);
        $('#profit_method_calc').val(response[0].profit_method);
        $('#interest_rate_calc').val(response[0].interest_rate);
        $('#due_period_calc').val(response[0].due_period);
        $('#doc_charge_calc').val(response[0].doc_charge);
        $('#processing_fees_calc').val(response[0].processing_fees);
        $('#principal_amnt_calc').val(moneyFormatIndia(response[0].principal_amnt));
        $('#interest_amnt_calc').val(moneyFormatIndia(response[0].interest_amnt));
        $('#total_amnt_calc').val(moneyFormatIndia(response[0].total_amnt));
        $('#due_amnt_calc').val(moneyFormatIndia(response[0].due_amnt));
        $('#doc_charge_calculate').val(moneyFormatIndia(response[0].doc_charge_calculate));
        $('#processing_fees_calculate').val(moneyFormatIndia(response[0].processing_fees_calculate));
        $('#net_cash_calc').val(moneyFormatIndia(response[0].net_cash));
        $('#loan_date_calc').val(response[0].loan_date);
        $('#due_startdate_calc').val(response[0].due_startdate);
        $('#maturity_date_calc').val(response[0].maturity_date);
        $('#aadhar_num').val(response[0].aadhar_num);
        getIssuePerson(response[0].cus_name);
        $('#due_startdate_calc').attr('min', response[0].loan_date);

        if (response[0].cus_data == 'Existing') {
            $('#loan_count_div').show();
            let cus_id = response[0].cus_id; // Add this line
            getLoanCount(cus_id);
        } else {
            $('#loan_count_div').hide();
        }

        let path = "uploads/loan_entry/cus_pic/";
        $('#per_pic').val(response[0].pic);
        var img = $('#imgshow');
        img.attr('src', path + response[0].pic);

        $('.calc_scheme_title').text((response[0].profit_type == '0') ? 'Calculation' : 'Scheme');
        $('#profit_type_calc_scheme').show();
        if (response[0].due_type === 'EMI') {
            $('#due_type_calc').val('EMI');
            $('.interest_type').hide();
            $('.interest_cal').show();
        } else if (response[0].due_type === 'Interest') {
            $('#due_type_calc').val('Interest');
            $('.interest_type').show();
            $('.interest_cal').hide();
            $('#interest_calculate').val(response[0].interest_calculate);
            $('#profit_type_calc').val(0);
        }
        if (response[0].profit_type == '0') { // Loan Calculation
            $('.calc').show();
            $('.scheme').hide();
            $('.scheme_day').hide();

        } else if (response[0].profit_type == '1') { // Scheme
            $('.calc').hide();
            $('.scheme').show();

            if (response[0].scheme_due_method == '2') {
                $('.scheme_day').show();
            } else {
                $('.scheme_day').hide();
                $('.scheme_day_calc').val('');
            }
        }

    }, 'json');
}

function getLoanCount(cus_id) {
    $.ajax({
        url: 'api/loan_entry/get_loan_count.php',
        type: 'POST',
        data: { cus_id: cus_id },
        dataType: 'json',
        cache: false,
        success: function (response) {
            $('#loan_count').val(response.loan_count);
            let formattedDate = response.first_loan_date.split('-').reverse().join('-');
            $('#first_loan_date').val(formattedDate);
        },
    });
}

function getIssuePerson(cus_name) {
    let cus_id = $('#cus_id').val();
    $.post('api/loan_entry/get_guarantor_name.php', { cus_id }, function (response) {
        let appendOption = '';
        appendOption += "<option value='' data-val=''>Select Issue Person</option>";
        appendOption += "<option value='" + cus_name + "' data-val='Customer'>" + cus_name + "</option>";
        $.each(response, function (index, val) {
            appendOption += "<option value='" + val.fam_name + "' data-val='" + val.id + "'>" + val.fam_name + "</option>";
        });
        $('#issue_person').empty().append(appendOption);
    }, 'json');
}

function refreshIssueInfo() {
    $('#payment_mode').val('');
    $('#payment_type').val('');
    $('#transaction_id').val('');
    $('#chequeno').val('');
    $('#issue_amount').val('');
    $('#issue_person').val('');
    $('#issue_relationship').val('');
    $('.transaction').hide();
    $('.checque').hide();
    $('.cash_issue').hide();
    $('.balance_remark_container').hide();
    $('#bank_container').hide();//hide bank id
    resetFieldBorders(['payment_mode', 'payment_type', 'cash', 'transaction_value', 'chequeValue', 'issue_person']);
}

function resetFieldBorders(fields) {
    fields.forEach(field => {
        document.getElementById(field).style.border = '1px solid #cecece';
    });
}

// Function to check if all values in an object are not empty
function isFormDataValid(formData) {
    let isValid = true;

    // Reset border styles for all fields
    $('#payment_mode, #issue_amount, #transaction_id, #chequeno, #cash, #chequeValue, #transaction_value, #bank_name, #chequeRemark, #transaction_remark').css('border', '1px solid #cecece');

    // Validate required fields
    if (!validateField(formData['issue_person'], 'issue_person')) {
        isValid = false;
    }

    if (!validateField(formData['payment_type'], 'payment_type')) {
        isValid = false;
    }

    if (!validateField(formData['payment_mode'], 'payment_mode')) {
        isValid = false;
    }

    // Check if payment_type is "1" (Split Payment)
    if (formData['payment_type'] === "1") {
        // Validate payment_mode again
        if (!validateField(formData['payment_mode'], 'payment_mode')) {
            isValid = false;
        }

        // Validate specific fields based on payment_mode
        if (formData['payment_mode'] === "1") { // Cash
            if (!validateField(formData['cash'], 'cash')) {
                isValid = false;
            }
        } else if (formData['payment_mode'] === "2") { // Cheque
            if (!validateField(formData['transaction_id'], 'transaction_id')) {
                isValid = false;
            }
            if (!validateField(formData['transaction_value'], 'transaction_value')) {
                isValid = false;
            }
            if (!validateField(formData['bank_name'], 'bank_name')) {
                isValid = false;
            }
        } else if (formData['payment_mode'] === "3") { // Transaction
            if (!validateField(formData['chequeno'], 'chequeno')) {
                isValid = false;
            }
            if (!validateField(formData['chequeValue'], 'chequeValue')) {
                isValid = false;
            }
            if (!validateField(formData['bank_name'], 'bank_name')) {
                isValid = false;
            }
        }
        // Ensure that at least one payment method is filled
        let isCashFilled = formData['cash'] > 0;
        let isTransactionFilled = formData['transaction_value'] > 0;
        let isChequeFilled = formData['chequeValue'] > 0;

        if (!(isCashFilled || isChequeFilled || isTransactionFilled)) {
            isValid = false;
            $('#cash, #transaction_value , #chequeValue').css('border', '1px solid #ff0000');
        } else {
            resetFieldBorders(['cash', 'transaction_value', 'chequeValue']);
        }
    }
    else if (formData['payment_type'] == "2") { // Single Payment
        if (!validateField(formData['payment_mode'], 'payment_mode')) {
            isValid = false;
        }

        if (formData['payment_mode'] == "1") { // Cash
            if (!validateField(formData['cash'], 'cash')) {
                isValid = false;
            }
        } else if (formData['payment_mode'] == "2") { // Cheque
            if (!validateField(formData['transaction_id'], 'transaction_id')) {
                isValid = false;
            }
            if (!validateField(formData['transaction_value'], 'transaction_value')) {
                isValid = false;
            }
            if (!validateField(formData['bank_name'], 'bank_name')) {
                isValid = false;
            }
        } else if (formData['payment_mode'] == "3") { // Transaction
            if (!validateField(formData['chequeno'], 'chequeno')) {
                isValid = false;
            }
            if (!validateField(formData['chequeValue'], 'chequeValue')) {
                isValid = false;
            }
            if (!validateField(formData['bank_name'], 'bank_name')) {
                isValid = false;
            }
        }
    }
    // Check other mandatory fields not related to payment_mode
    for (let key in formData) {
        if (key !== 'payment_mode' && key !== 'bal_amount' && key !== 'payment_type' && key !== 'transaction_id' && key !== 'chequeno' && key !== 'cash' && key !== 'chequeValue' && key !== 'transaction_value' && key !== 'transaction_remark' && key !== 'chequeRemark' && key !== 'bank_name') {
            if (!validateField(formData[key], key)) {
                return false;
            }
        }
    }

    return isValid;
}

function checkBalance() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.ajax({
        url: 'api/loan_issue_files/get_loan_balance.php',
        type: 'POST',
        data: { 'cus_profile_id': cus_profile_id },
        dataType: 'json',
        success: function (response) {
            if (response && response.balance_amount !== undefined) {
                // Check if balance amount is zero
                let balanceAmount = response.balance_amount;
                if (balanceAmount === 'null' || balanceAmount === null) {
                    $('#balance_net_cash').val($('#net_cash_calc').val());
                    $('#due_startdate_calc').attr('readonly', false);
                } else {
                    $('#due_startdate_calc').attr('readonly', true);
                    $('#balance_net_cash').val(moneyFormatIndia(balanceAmount));
                }

            } else {
                console.error('Balance amount not found in response');
            }
        }
    });
}

function calculateBalance() {
    // Get the settlement balance and remove commas, then parse it as a float
    let settlementBalance = parseFloat($('#balance_net_cash').val().replace(/,/g, '')) || 0;
    let cashVal = parseFloat($('#cash').val().replace(/,/g, '')) || 0;
    let cheqVal = parseFloat($('#chequeValue').val().replace(/,/g, '')) || 0;
    let transVal = parseFloat($('#transaction_value').val().replace(/,/g, '')) || 0;
    // Calculate the remaining balance
    let remainingBalance = settlementBalance - (cashVal + cheqVal + transVal);

    // Format the remaining balance using the moneyFormatIndia function
    $('#balance_amount').val(moneyFormatIndia(remainingBalance));
}

function getBankName() {
    $.post('api/common_files/bank_name_list.php', function (response) {
        let appendBankOption = "<option value=''>Select Bank Name</option>";
        $.each(response, function (index, val) {
            let selected = '';
            let editGId = $('#bank_name_edit').val(); // Existing guarantor ID (if any)
            if (val.id == editGId) {
                selected = 'selected';
            }
            appendBankOption += "<option value='" + val.id + "' " + selected + ">" + val.bank_name + "</option>";
        });
        $('#bank_name').empty().append(appendBankOption);
    }, 'json');
}
/////////////////////////////////////////////////////////// Loan Issue END////////////////////////////////////////////////