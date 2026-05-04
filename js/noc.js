$(document).ready(function () {
    $(document).on('click', '.noc-details', function (event) {
        event.preventDefault();
        $('#noc_list').hide();
        $('#noc_main_container,.back_to_noc_list').show();
        let cusid = $(this).attr('value');
        getPersonalInfo(cusid);
        getNOCLoanList(cusid);
    });
    $(document).on('click', '#remove-noc', function (event) {
        event.preventDefault();
        let cid = $(this).attr('value');
        swalConfirm('Remove', 'Do you want to remove the NOC?', removenoc, cid);
        return;
    });
    $('#back_to_noc_list').click(function (event) {
        event.preventDefault();
        getNOCList();
        $('#noc_main_container,.back_to_noc_list').hide();
        $('#noc_list').show();
    })
    $(document).on('click', '.noc-summary', function (event) {
        event.preventDefault();
        $('#noc_summary,.back_to_loan_list').show();
        $('#loan_list, #personal_info,.back_to_noc_list').hide();
        let cp_id = $(this).attr('value');
        $('#cp_id').val(cp_id)
        callAllFunctions(cp_id);
    })

    $(document).on('click', '#remark_view', function (event) {
        event.preventDefault();
        let cp_id = $(this).attr('value');
        $.post('api/noc_files/closed_remark_details.php', { cp_id }, function (response) {
            $('#sub_status').val(response['sub_status']);
            $('#remark').val(response['remark']);
        }, 'json');
    });

    $('#back_to_loan_list').click(function (event) {
        event.preventDefault();
        $('#noc_summary, .back_to_loan_list').hide();
        $('#loan_list, #personal_info,.back_to_noc_list').show();
    
        $('#noc_member').css('border', '1px solid #cecece');
        $('#noc_relation').css('border', '1px solid #cecece');
    });

    $('#noc_member').change(function () {
        let id = $(this).val();
        let cus_name = $('#cus_name').val();
        if (id != '' && id != cus_name) {
            getRelationship(id);
        } else if (id == cus_name) {
            $('#noc_relation').val('Customer');
        } else {
            $('#noc_relation').val('');
        }

        setTimeout(() => {
            setValuesInTables();
        }, 1000);
    });

    $(document).on('click', '.noc_cheque_chkbx, .noc_mortgage_chkbx, .noc_endorsement_chkbx, .noc_doc_info_chkbx, .noc_gold_chkbx', function () {
        setValuesInTables();
        removeValuesInTables();
    });

    $('#submit_noc').click(function (event) {
        event.preventDefault();

        let chequeId = [];
        let mortId = [];
        let endorsementId = [];
        let docId = [];
        let goldId = [];

    // Helper: check if table has at least one enabled row
    function hasEnabledRows(selector) {
        return $(selector).filter(':not(:disabled)').length > 0;
    }

    // Collect values only if checkbox is enabled
    $('.noc_cheque_chkbx').each(function () {
        if (!$(this).is(':disabled') && $(this).is(':checked') && $(this).attr('data-id') == '0') {
            chequeId.push($(this).val());
        }
    });

    $('.noc_mortgage_chkbx').each(function () {
        if (!$(this).is(':disabled') && $(this).is(':checked') && $(this).attr('data-id') == '0') {
            mortId.push($(this).val());
        }
    });

    $('.noc_endorsement_chkbx').each(function () {
        if (!$(this).is(':disabled') && $(this).is(':checked') && $(this).attr('data-id') == '0') {
            endorsementId.push($(this).val());
        }
    });

    $('.noc_doc_info_chkbx').each(function () {
        if (!$(this).is(':disabled') && $(this).is(':checked') && $(this).attr('data-id') == '0') {
            docId.push($(this).val());
        }
    });

    $('.noc_gold_chkbx').each(function () {
        if (!$(this).is(':disabled') && $(this).is(':checked') && $(this).attr('data-id') == '0') {
            goldId.push($(this).val());
        }
    });

    // Check if any table requires validation
    let needValidation =
        hasEnabledRows('.noc_cheque_chkbx') ||
        hasEnabledRows('.noc_mortgage_chkbx') ||
        hasEnabledRows('.noc_endorsement_chkbx') ||
        hasEnabledRows('.noc_doc_info_chkbx') ||
        hasEnabledRows('.noc_gold_chkbx');

    // Validation: only if at least one enabled row exists
    if (needValidation && chequeId.length === 0 && mortId.length === 0 && endorsementId.length === 0 && docId.length === 0 && goldId.length === 0) {
        swalError('Warning', 'Kindly check at least one checkbox');
        $('#noc_member').val('');
        $('#noc_relation').val('');
        return;
    }

    // Get row counts (for extra info if needed)
    let cheque_list_cnt = $('#noc_cheque_list_table').DataTable().rows().count();
    let mort_list_cnt = $('#noc_mortgage_list_table').DataTable().rows().count();
    let endorsemnt_list_cnt = $('#noc_endorsement_list_table').DataTable().rows().count();
    let doc_list_cnt = $('#noc_document_list_table').DataTable().rows().count();
    let gold_list_cnt = $('#noc_gold_list_table').DataTable().rows().count();

    // Form values
    let date_of_noc = $('#date_of_noc').val();
    let noc_member = $('#noc_member').val();
    let noc_relation = $('#noc_relation').val();
    let cpid = $('#cp_id').val();
    let cus_id = $('#cus_id').val();

    // Mandatory field validation
    var data = ['date_of_noc', 'noc_member', 'noc_relation'];
    var isValid = true;
    data.forEach(function (entry) {
        var fieldIsValid = validateField($('#' + entry).val(), entry);
        if (!fieldIsValid) {
            isValid = false;
        }
    });

    if (isValid) {
        let nocData = {
            'chequeId': chequeId,
            'mortId': mortId,
            'endorsementId': endorsementId,
            'docId': docId,
            'goldId': goldId,
            'date_of_noc': date_of_noc,
            'noc_member': noc_member,
            'noc_relation': noc_relation,
            'cpid': cpid,
            'cus_id': cus_id,
            'cheque_list_cnt': cheque_list_cnt,
            'mort_list_cnt': mort_list_cnt,
            'endorsemnt_list_cnt': endorsemnt_list_cnt,
            'doc_list_cnt': doc_list_cnt,
            'gold_list_cnt': gold_list_cnt
        }

        $.post('api/noc_files/submit_noc.php', nocData, function (response) {
            if (response == '1') {
                swalSuccess('Success', 'NOC submitted successfully.');
                callAllFunctions(cpid);
            } else {
                swalError('Error', 'NOC submission failed.');
            }
        }, 'json');
    }
});

setDropdownScripts();
setCurrentDate('#date_of_noc');
});  // Document END
 //Document END.

$(function () {
    getNOCList();
});

function getNOCList() {
    serverSideTable('#noc_list_table', '', 'api/noc_files/noc_list.php', 'NOC List');
}

function getPersonalInfo(cus_id) {
    $.post('api/common_files/personal_info.php', { cus_id }, function (response) {
        $('#cus_id').val(response[0].cus_id);
        $('#cus_name').val(response[0].cus_name);
        $('#cus_area').val(response[0].area);
        $('#cus_branch').val(response[0].branch_name);
        $('#cus_line').val(response[0].linename);
        $('#cus_mobile').val(response[0].mobile1);
        $('#aadhar_num').val(response[0].aadhar_num);
        let path = "uploads/loan_entry/cus_pic/";
        if (response[0].pic) {
            $('#per_pic').val(response[0].pic);
            var img = $('#cus_image');
            img.attr('src', path + response[0].pic);
        }
        else {
            $('#imgshow').attr('src', 'img/avatar.png');
        }
    }, 'json');
}

function getNOCLoanList(cus_id) {
    $.ajax({
        url: 'api/noc_files/noc_loan_list.php',
        data: { 'cus_id': cus_id },
        type: 'post',
        dataType: 'json',
        cache: false,
        success: function (response) {
            var columnMapping = [
                'sno',
                'loan_id',
                'loan_category',
                'issue_date',
                'closed_date',
                'loan_amount',
                'status',
                'sub_status',
                'action'
            ];
            appendDataToTable('#noc_loan_list_table', response, columnMapping);
            setdtable('#noc_loan_list_table', "NOC Loan List");
            //Dropdown in List Screen
            setDropdownScripts();
        }
    });
}

 async function callAllFunctions(cp_id) {
        // Run all the functions concurrently
        // $('.cheque-div').hide();
        // $('.doc_div').hide();
        // $('.mortgage-div').hide();
        // $('.endorsement-div').hide();
        // $('.gold-div').hide();
        await Promise.all([
            getChequeList(cp_id),
            getMortgageList(cp_id),
            getEndorsementList(cp_id),
            getOtherDocumentList(cp_id),
            getGoldList(cp_id)
        ]);

        // Get today's date
        const today = new Date().toISOString().split('T')[0];
        // Set NOC date
        $('#date_of_noc').val(today);

        // Clear the NOC relation field
        $('#noc_relation').val('');

        // Wait for getFamilyMember to complete
        await getFamilyMember();
        
        // Set the submitted disabled state
        setSubmittedDisabled();
}

function getChequeList(cp_id) {
    return $.post('api/noc_files/noc_cheque_list.php', { cp_id }, function (response) {
        if (response && response.length > 0) {
            $('.cheque-div').show();
        }
        let nocChequeColumns = [
            'sno', 'holder_type', 'holder_name', 'relationship', 'bank_name', 
            'cheque_no', 'date_of_noc', 'noc_member', 'noc_relationship', 'action'
        ];
        appendDataToTable('#noc_cheque_list_table', response, nocChequeColumns);
        setdtable('#noc_cheque_list_table', "NOC Cheque List");
    }, 'json');
}

function getMortgageList(cp_id) {
    return $.post('api/noc_files/noc_mortgage_list.php', { cp_id }, function (response) {
        if (response && response.length > 0) {
            $('.mortgage-div').show();
        }
        let nocMortgageColumns = [
            'sno', 'holder_name', 'relationship', 'property_details', 'mortgage_name', 
            'designation', 'reg_office', 'date_of_noc', 'noc_member', 'noc_relationship', 'action'
        ];
        appendDataToTable('#noc_mortgage_list_table', response, nocMortgageColumns);
        setdtable('#noc_mortgage_list_table', "NOC Mortgage List");
    }, 'json');
}

function getEndorsementList(cp_id) {
    return $.post('api/noc_files/noc_endorsement_list.php', { cp_id }, function (response) {
        if (response && response.length > 0) {
            $('.endorsement-div').show();
        }
        let nocEndorseColumns = [
            'sno', 'holder_name', 'relationship', 'vehicle_details', 'endorsement_name', 
            'key_original', 'rc_original', 'date_of_noc', 'noc_member', 'noc_relationship', 'action'
        ];
        appendDataToTable('#noc_endorsement_list_table', response, nocEndorseColumns);
        setdtable('#noc_endorsement_list_table', "NOC Endorsement List");
    }, 'json');
}

function getOtherDocumentList(cp_id) {
    return $.post('api/noc_files/noc_document_info_list.php', { cp_id }, function (response) {
        if (response && response.length > 0) {
            $('.doc_div').show();
        }
        let nocDocInfoColumns = [
            'sno', 'doc_name', 'doc_type', 'holder_name', 'upload', 'date_of_noc', 
            'noc_member', 'noc_relationship', 'action'
        ];
        appendDataToTable('#noc_document_list_table', response, nocDocInfoColumns);
        setdtable('#noc_document_list_table', "NOC Document List");
    }, 'json');
}

function getGoldList(cp_id) {
    return $.post('api/noc_files/noc_gold_list.php', { cp_id }, function (response) {
        if (response && response.length > 0) {
            $('.gold-div').show();
        }
        let nocGoldColumns = [
            'sno', 'gold_type', 'purity', 'weight', 'date_of_noc', 
            'noc_member', 'noc_relationship', 'action'
        ];
        appendDataToTable('#noc_gold_list_table', response, nocGoldColumns);
        setdtable('#noc_gold_list_table', "NOC Gold List");
    }, 'json');
}
function getFamilyMember() {
    let cus_id = $('#cus_id').val();
    let cus_name = $('#cus_name').val();
    return  $.post('api/loan_entry/get_guarantor_name.php', { cus_id }, function (response) {
        let appendOption = '';
        appendOption += "<option value=''>Select Member Name</option>";
        appendOption += "<option value='" + cus_name + "'>" + cus_name + "</option>";
        $.each(response, function (index, val) {
            appendOption += "<option value='" + val.id + "'>" + val.fam_name + "</option>";
        });
        $('#noc_member').empty().append(appendOption);
    }, 'json');
}

function getRelationship(id) {
    $.post('api/loan_entry/family_creation_data.php', { id }, function (response) {
        let relationship = response[0].fam_relationship;
        $('#noc_relation').val(relationship);
    }, 'json');
}

function setValuesInTables() {
    let member = $('#noc_member').val();
    let date = $('#date_of_noc').val();
    let formattedDate = (member != '') ? formatDate(date) : '';
    let name = (member != '') ? $('#noc_member').find(":selected").text() : '';
    let relationship = (member != '') ? $('#noc_relation').val() : '';
    let checked = false;
    $('.noc_cheque_chkbx').each(function () {
        if ($(this).is(':checked') && $(this).attr('data-id') == '0') {
            checked = true;
            $(this).closest('tr').find('td:nth-child(9)').text(relationship);
            $(this).closest('tr').find('td:nth-child(8)').text(name);
            $(this).closest('tr').find('td:nth-child(7)').text(formattedDate);
        }
    });
    $('.noc_mortgage_chkbx').each(function () {
        if ($(this).is(':checked') && $(this).attr('data-id') == '0') {
            checked = true;
            $(this).closest('tr').find('td:nth-child(10)').text(relationship);
            $(this).closest('tr').find('td:nth-child(9)').text(name);
            $(this).closest('tr').find('td:nth-child(8)').text(formattedDate);
        }
    });
    $('.noc_endorsement_chkbx').each(function () {
        if ($(this).is(':checked') && $(this).attr('data-id') == '0') {
            checked = true;
            $(this).closest('tr').find('td:nth-child(10)').text(relationship);
            $(this).closest('tr').find('td:nth-child(9)').text(name);
            $(this).closest('tr').find('td:nth-child(8)').text(formattedDate);
        }
    });
    $('.noc_doc_info_chkbx').each(function () {
        if ($(this).is(':checked') && $(this).attr('data-id') == '0') {
            checked = true;
            $(this).closest('tr').find('td:nth-child(8)').text(relationship);
            $(this).closest('tr').find('td:nth-child(7)').text(name);
            $(this).closest('tr').find('td:nth-child(6)').text(formattedDate);
        }
    });
    $('.noc_gold_chkbx').each(function () {
        if ($(this).is(':checked') && $(this).attr('data-id') == '0') {
            checked = true;
            $(this).closest('tr').find('td:nth-child(7)').text(relationship);
            $(this).closest('tr').find('td:nth-child(6)').text(name);
            $(this).closest('tr').find('td:nth-child(5)').text(formattedDate);
        }
    });

    // if (!checked) {
    //     swalError('Warning', 'Kindly check Atleast one checkbox');
    //     $('#noc_member').val('');
    //     $('#noc_relation').val('');

    //     return;
    // }
}

function removeValuesInTables() {
    $('.noc_cheque_chkbx').each(function () {
        if (!$(this).is(':checked')) {
            $(this).closest('tr').find('td:nth-child(9)').text('');
            $(this).closest('tr').find('td:nth-child(8)').text('');
            $(this).closest('tr').find('td:nth-child(7)').text('');
        }
    });
    $('.noc_mortgage_chkbx').each(function () {
        if (!$(this).is(':checked')) {
            $(this).closest('tr').find('td:nth-child(10)').text('');
            $(this).closest('tr').find('td:nth-child(9)').text('');
            $(this).closest('tr').find('td:nth-child(8)').text('');
        }
    });
    $('.noc_endorsement_chkbx').each(function () {
        if (!$(this).is(':checked')) {
            $(this).closest('tr').find('td:nth-child(10)').text('');
            $(this).closest('tr').find('td:nth-child(9)').text('');
            $(this).closest('tr').find('td:nth-child(8)').text('');
        }
    });
    $('.noc_doc_info_chkbx').each(function () {
        if (!$(this).is(':checked')) {
            $(this).closest('tr').find('td:nth-child(8)').text('');
            $(this).closest('tr').find('td:nth-child(7)').text('');
            $(this).closest('tr').find('td:nth-child(6)').text('');
        }
    });
    $('.noc_gold_chkbx').each(function () {
        if (!$(this).is(':checked')) {
            $(this).closest('tr').find('td:nth-child(7)').text('');
            $(this).closest('tr').find('td:nth-child(6)').text('');
            $(this).closest('tr').find('td:nth-child(5)').text('');
        }
    });
}

function setSubmittedDisabled() {
    $('.noc_cheque_chkbx, .noc_mortgage_chkbx, .noc_endorsement_chkbx, .noc_doc_info_chkbx, .noc_gold_chkbx').each(function () {
        if ($(this).attr('data-id') == '1') {
            $(this).closest('tr').addClass('disabled-row');
            $(this).attr('checked', true).attr('disabled', true);
        }
    });

    // var cheque_checkDisabled = $('.noc_cheque_chkbx:disabled').length === $('.noc_cheque_chkbx').length;
    // var mort_checkDisabled = $('.noc_mortgage_chkbx:disabled').length === $('.noc_mortgage_chkbx').length;
    // var endorse_checkDisabled = $('.noc_endorsement_chkbx:disabled').length === $('.noc_endorsement_chkbx').length;
    // var doc_checkDisabled = $('.noc_doc_info_chkbx:disabled').length === $('.noc_doc_info_chkbx').length;
    // var gold_checkDisabled = $('.noc_gold_chkbx:disabled').length === $('.noc_gold_chkbx').length;
    // if (cheque_checkDisabled && mort_checkDisabled && endorse_checkDisabled && doc_checkDisabled && gold_checkDisabled) {
    //     $('#submit_noc').hide();
    // } else {
    //     $('#submit_noc').show();
    // }

}

function formatDate(inputDate) {
    // Split the input date into year, month, and day components
    let parts = inputDate.split('-');
    // Rearrange them in dd-mm-yyyy format
    return parts[2] + '-' + parts[1] + '-' + parts[0];
}

function validate() {
    let response = true;

    return response;
}

function removenoc(cid) {
    $.post('api/common_files/move_to_next.php', { 'cus_sts': '12', 'cus_sts_id': cid }, function (response) {
        if (response == '0') {
            swalSuccess('Success', 'NOC Removed Successfully.');
            getNOCList();
        } else {
            swalError('Error', 'Something went wrong. Please try again later.');
        }
    }, 'json');
}