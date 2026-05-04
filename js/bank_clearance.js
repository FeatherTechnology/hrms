$(document).ready(function () {

    $('.add_bc_btn, .back_to_bcList_btn').click(function () {
        swapTableAndCreation();
    });

    $('#from_date').change(function () {
        let fromDate = $(this).val();
        $('#to_date').val('').attr('min', fromDate);
    });

    $('#to_date').change(function () {
        var fromDate = new Date($('#from_date').val());
        var toDate = new Date($(this).val());

        if (fromDate > toDate) { // if anyone enters to date manually in to date less than from date, it empty's the to date value
            $(this).val('');
        }
    });

    $('#view_btn').click(function () {
        let bank_id = $('#bank_name').val(); let from_date = $('#from_date').val(); let to_date = $('#to_date').val();
        if (bank_id == '' || from_date == '' || to_date == '') {
            swalError('Warning', 'Kindly fill the Mandatory Fields');
            return;
        } else {
            $.ajax({
                url: 'api/accounts_files/bank_clearance_files/bank_clearance_stmt.php',
                data: { 'bank_id': bank_id, 'from_date': from_date, 'to_date': to_date },
                type: 'post',
                cache: false,
                success: function (response) {
                    if (response.includes('Given Date Has No Statements!')) {
                        swalError('Alert', 'Kindly try with other date!');
                        $('.bank_statement_table_content').hide();
                        return false;
                    } else {
                        $('.bank_statement_table_content').show();
                        $('#bank_statement_table').empty();
                        $('#bank_statement_table').html(response);
                    }
                }
            }).then(function () {
                clrcatClickEvent();
                getUnclearTotal();
            })
        }
    })

    $('#bank_name_form').change(function () {
        let accNo = $('#bank_name_form :selected').attr('data-id');
        $('#acc_no').val(accNo);
    });

    /////////////////////////////////////////////////////////// Transaction Details START ///////////////////////////////////////////////////////////////////////
    $('#submit_bank_clearance').click(function (event) {
        event.preventDefault();
        let bcData = {
            bank_id: $('#bank_name_form').val(),
            acc_no: $('#acc_no').val(),
            transaction_date: $('#transaction_date').val(),
            transaction_id: $('#transaction_id').val(),
            narration: $('#narration').val(),
            cr_dr: $('#cr_dr').val(),
            amount: $('#amount').val(),
            balance: $('#balance').val()
        };
        if (isFormDataValid(bcData)) {
            $.post('api/accounts_files/bank_clearance_files/submit_bank_clearance.php', bcData, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Transaction details Added Successfully!');
                    $('#reset_btn').trigger('click'); //To Clear All Fields in Transaction Details.
                } else {
                    swalError('Error', 'Failed to Insert!');
                }
            }, 'json');

        } else {
            swalError('Warning', 'Kindly Fill the Mandatory Fields!')
        }
    });
    /////////////////////////////////////////////////////////// Transaction Details END ///////////////////////////////////////////////////////////////////////

    $('#download_bank_stmt').click(function () {
        window.location.href = 'uploads/excel_format/bank_statement_format.xlsx';
    });


    $("#submit_stmt_upload").click(function () {
        var bank_id = $('#bank_id_upload').val();
        if (bank_id != '') { //allows only if bank id selected
            $('#bank_id_uploadCheck').hide();
            var file_data = $('#file').prop('files')[0];
            var bank_id = $('#bank_id_upload').val();
            var bcstmt = new FormData();
            bcstmt.append('file', file_data);
            bcstmt.append('bank_id', bank_id);

            if (file.files.length == 0) { //if no file selected
                swalError('Warning', 'Please Select File!');
                return false;
            } else {
                $.ajax({
                    url: 'api/accounts_files/bank_clearance_files/checkExcelforOverwrite.php',
                    data: bcstmt,
                    type: 'post',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (response) {
                        if (response == 0) {
                            submitUpload();
                        } else if (response == 1) {
                            Swal.fire({
                                title: 'Your Statement Has existing transaction Dates!',
                                text: 'Do you want to overwrite?',
                                icon: 'question',
                                showConfirmButton: true,
                                showCancelButton: true,
                                confirmButtonColor: '#009688',
                                cancelButtonColor: '#cc4444',
                                cancelButtonText: 'No',
                                confirmButtonText: 'Yes'
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    removeAndSubmitUpload();
                                }
                            })
                        } else {
                            swalError('Error', 'Error occured when uploading');
                            return false;
                        }
                    }
                })
            }
        } else {
            swalError('Warning', 'Kindly Select Mandatory Fields!')
            return false;
        }
    });

}); //Document END.

//ON Load
$(function () {
    getBankName();
});

function swapTableAndCreation() {
    if ($('.bank_clearance_view_content').is(':visible')) {
        $('.bank_clearance_view_content').hide();
        $('.bank_statement_table_content').hide();
        $('.add_bc_btn').hide();
        $('#bank_clearance_add_content').show();
        $('.back_to_bcList_btn').show();

    } else {
        $('.bank_clearance_view_content').show();
        $('.add_bc_btn').show();
        $('#bank_clearance_add_content').hide();
        $('.back_to_bcList_btn').hide();
        $('#reset_btn').trigger('click');
        $('.clr-trans-detail').val('');
    }
}

function getBankName() {
    $.post('api/common_files/bank_name_list.php', function (response) {
        var bankName = '<option value="">Select Bank Name</option>';
        $.each(response, function (index, value) {
            bankName += '<option value="' + value.id + '" data-id="' + value.account_number + '">' + value.bank_name + '</option>';
        });
        $('#bank_name').html(bankName);
        $('#bank_name_form').html(bankName);
        $('#bank_id_upload').html(bankName);
    }, 'json');
}

// Function to check if all values in an object are not empty
function isFormDataValid(formData) {
    for (let key in formData) {
        if (formData[key] == '' || formData[key] == null || formData[key] == undefined) {
            return false;
        }
    }
    return true;
}

//submit uploaded excel file if those transaction dates are not exist in db
function submitUpload() {

    var file_data = $('#file').prop('files')[0];
    var bank_id = $('#bank_id_upload').val();
    var area = new FormData();
    area.append('file', file_data);
    area.append('bank_id', bank_id);

    $.ajax({
        url: 'api/accounts_files/bank_clearance_files/submitUploadedBankStmt.php',
        type: 'POST',
        data: area,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
            $('#file').attr("disabled", true);
            $('#submit_stmt_upload').attr("disabled", true);
        },
        success: function (data) {
            if (data == 0) {
                $("#file").val('');
                Swal.fire({
                    title: 'Statement Uploaded!',
                    icon: 'success',
                    showConfirmButton: true,
                    confirmButtonColor: '#009688',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        // getBankClearanceTable();
                    }
                })
            } else if (data > 0) {
                $("#file").val('');
                swalError('Warning', 'File Not Uploaded!')
            }
        },
        complete: function () {
            $('#file').attr("disabled", false);
            $('#submit_stmt_upload').attr("disabled", false);
        }
    });
}

//remove entries and submit uploaded excel file if table has transaction dates of excel file
function removeAndSubmitUpload() {

    var file_data = $('#file').prop('files')[0];
    var bank_id = $('#bank_id_upload').val();
    var area = new FormData();
    area.append('file', file_data);
    area.append('bank_id', bank_id);

    $.ajax({
        url: 'api/accounts_files/bank_clearance_files/removeAndSubmitUpload.php',
        type: 'POST',
        data: area,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
            $('#bank_id_upload').attr("disabled", true);
            $('#file').attr("disabled", true);
            $('#submit_stmt_upload').attr("disabled", true);
        },
        success: function (data) {
            if (data == 0) {
                $("#file").val('');
                swalSuccess('Success', 'Statement Uploaded!');
            } else if (data > 0) {
                $("#file").val('');
                swalError('Error', 'File Not Uploaded!')
            }
        },
        complete: function () {
            $('#bank_id_upload').attr("disabled", false);
            $('#file').attr("disabled", false);
            $('#submit_stmt_upload').attr("disabled", false);
        }
    });
}

function clearUploadModal() {
    $('#bank_id_upload').val('');
    $('#file').val('');
}

//function for click event when user clicks on a cash tally modes to get the ref codes
function clrcatClickEvent() {
    $('.clr_cat').change(function () {
        var clr_cat = $(this).val();
        var bank_id = $(this).prev().val();
        var crdb = $(this).next().val();
        var trans_id = $(this).parent().prev().prev().prev().prev().text();
        var ref_id_box = $(this).parent().next().children();//represents ref id select box

        $.ajax({
            url: 'api/accounts_files/bank_clearance_files/ref_code_to_clear.php',
            data: { 'clr_cat': clr_cat, 'bank_id': bank_id, 'crdb': crdb, 'trans_id': trans_id },
            dataType: 'json',
            type: 'post',
            cache: false,
            success: function (response) {
                ref_id_box.empty();
                ref_id_box.append("<option value=''>Select Ref ID</option>");
                $.each(response, function (ind, val) {
                    ref_id_box.append("<option value='" + val['ref_code'] + "'>" + val['ref_code'] + "</option>")
                })

            }
        })
    })

    $('.ref-id').change(function () {
        if ($(this).val() != '') { // only true if ref id choosen

            $(this).parent().next().children().hide();//hiding span uncleared text
            $(this).parent().next().children().after('<input type="button" class="btn btn-primary clear_btn" value="Clear" id="" name="">')//adding new button after span

            $(this).parent().prev().children().attr('disabled', true)//disabling clear category dropdown
            $(this).attr('disabled', true)//disabling ref_id dropdown

            $('.clear_btn').off('click');//turning off existing click event
            $('.clear_btn').click(function () {
                var clear_btn = $(this)
                var bank_stmt_id = $(this).parent().next().val();// to get bank statement table if which is stored inside hidden input
                $.ajax({
                    url: 'api/accounts_files/bank_clearance_files/clear_transaction.php',
                    data: { 'bank_stmt_id': bank_stmt_id },
                    type: 'post',
                    cache: false,
                    success: function (response) {
                        if (response == 0) {
                            clear_btn.prev().show();
                            clear_btn.prev().text('Cleared');
                            clear_btn.prev().addClass('text-success');
                            clear_btn.prev().removeClass('text-danger');
                            clear_btn.hide();
                            getUnclearTotal();// to reset unclear total amounts
                        } else {
                            swalError('Not Cleared', 'Error While Submitting');
                        }
                    }
                })
            })
        }
    })

}

function getUnclearTotal() {
    var unclear_credit = 0;
    var unclear_debit = 0;
    $('.clr-status').each(function () {
        var clr_status = $(this).text();
        if (clr_status == 'Unclear') {
            var credit = $(this).parent().prev().prev().prev().prev().prev().text(); // credit amount
            var debit = $(this).parent().prev().prev().prev().prev().text(); // debit amount
            unclear_credit += parseInt(credit) || 0;
            unclear_debit += parseInt(debit) || 0;
        }
    })
    unclear_credit = moneyFormatIndia(unclear_credit)
    unclear_debit = moneyFormatIndia(unclear_debit)
    $('#ucl_credit').text(unclear_credit).css('font-weight', 'bold');
    $('#ucl_debit').text(unclear_debit).css('font-weight', 'bold');
}