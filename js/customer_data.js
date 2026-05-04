const scheme_choices = new Choices('#existing_details', {
    removeItemButton: true,
    noChoicesText: 'Select Existing Details',
    allowHTML: true
});
const repromotion_details = new Choices('#repromotion_details', {
    removeItemButton: true,
    noChoicesText: 'Select Repromotion Details',
    allowHTML: true
});
$(document).ready(function () {
    $('.new_table_content').show();
    $('.existing_table_content').hide();
    // Event handler for radio buttons
    $('input[name=customer_data]').click(function () {
        let customerDataType = $(this).val();
        if (customerDataType == 'new_list') {
            $('.new_table_content').show();
            $('.existing_table_content').hide();
            $('.repromotion_table_content').hide();
        } else if (customerDataType == 'existing_list') {
            $('.new_table_content').hide();
            $('.existing_table_content').show();
            $('.repromotion_table_content').hide();
        } else if (customerDataType == 'repromotion_list') {
            $('.new_table_content').hide();
            $('.existing_table_content').hide();
            $('.repromotion_table_content').show();
            getRePromotionTable('');
        }
    });
    $('#submit_new').click(function (event) {
        event.preventDefault();

        // Collect form data
        let cus_name = $('#cus_name').val();
        let area = $('#area').val();
        let mobile = $('#mobile').val();
        let loan_cat = $('#loan_cat').val();
        let loan_amount = $('#loan_amount').val().replace(/,/g, '');
        let new_promotion_id = $('#new_Promotion_id').val();

        // Fields to validate
        var data = ['cus_name', 'area', 'mobile', 'loan_cat', 'loan_amount'];

        // Validate fields
        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });
        // Check if mobile number is empty
        if (mobile.trim() === '') {
            // If mobile number is empty, return without executing AJAX
            return;
        }
        $.post('api/customer_data_files/get_existing_mobiles.php', { mobile: mobile }, function (response) {
            if (response.exists) {
                // Show an alert with the customer status if the mobile number already exists
                let statusMsg = "";

                switch (parseInt(response.status)) {
                    case 1: statusMsg = "Customer Profile Insert"; break;
                    case 2: statusMsg = "Loan Calculation Insert"; break;
                    case 3: statusMsg = "Moved To Approval"; break;
                    case 4: statusMsg = "Approved"; break;
                    case 5: statusMsg = "Cancel in Approval"; break;
                    case 6: statusMsg = "Revoke in Approval"; break;
                    case 7: statusMsg = "Loan Issue"; break;
                    case 8: statusMsg = "In Close"; break;
                    case 9: statusMsg = "Closed"; break;
                    case 10: statusMsg = "In NOC"; break;
                    case 11: statusMsg = "NOC Completed"; break;
                    case 12: statusMsg = "NOC Removed"; break;
                    case 13: statusMsg = "Cancel in Loan Issue"; break;
                    case 14: statusMsg = "Revoke in Loan Issue"; break;
                    default: statusMsg = " "; break;
                }
                swalError('Warning', 'Mobile number already exists. Customer status: ' + statusMsg);
                return;
            }
            if (isValid) {
                // Proceed with form submission
                $.post('api/customer_data_files/submit_new.php', { cus_name, area, mobile, loan_cat, loan_amount, new_promotion_id }, function (response) {
                    if (response == '1') {
                        swalSuccess('Success', 'Customer Data Added Successfully!');
                        $('#new_form input').val('');
                        $('#new_form input').css('border', '1px solid #cecece');
                    } else {
                        swalError('Error', 'Failed to add customer data.');
                    }
                });
            }
        }, 'json');
    });

    $('#mobile').change(function () {
        checkMobileNo($(this).val(), $(this).attr('id'));
    });

    $(document).on('click', '.newPromoDeleteBtn', function () {
        var id = $(this).attr('value');
        swalConfirm('Delete', 'Do you want to Delete the Customer Details?', getNewPromoDelete, id);
        return;
    });

    // Reset form fields when modal is hidden
    $('#add_new_list_modal').on('hidden.bs.modal', function () {
        $('#new_form').trigger('reset'); // Reset the form with id 'new_form'
        $('#new_form input').css('border', '1px solid #cecece');
    });

    // Reset form fields when modal backdrop is clicked
    $('#add_new_list_modal').on('click', function (e) {
        if ($(e.target).hasClass('modal')) {
            $('#new_form').trigger('reset'); // Reset the form with id 'new_form'
            $('#new_form input').css('border', '1px solid #cecece');
        }
    });

    $(document).on('click', '.exs_needed, .exs_later', function () {
        var cus_id = $(this).attr('value');
        var coll_data = $(this).attr('data');
        var datas = { cus_id: cus_id, coll_data: coll_data };
        // var checkbox = $(this); // Reference to the checkbox element
        swalConfirm('Are You Sure', '', getExistingData, datas, function () {
        });
        return;
    });

    $(document).on('click', '.needed, .later', function () {
        var cus_id = $(this).attr('value');
        var coll_data = $(this).attr('data');
        var datas = { cus_id: cus_id, coll_data: coll_data };
        swalConfirm('Are You Sure', '', InsertRepromotionData, datas, function () {
            checkbox.prop('checked', false); // Uncheck the checkbox if the user presses "No"
        });
        return;
    });
    $(document).on('click', '#existing_detail_btn', function () {
        var existing_details = $("#existing_details").val();
        getExistingPromotionTable(existing_details);
    });
    $(document).on('click', '#repromotion_detail_btn', function () {
        var repromotion_details = $("#repromotion_details").val();
        getRePromotionTable(repromotion_details);
    });
    $('#loan_amount').on('keyup', function () {
        let raw = $(this).val().replace(/,/g, '');
        $(this).val(formatIndianNumber(raw));
    });
})


$(function () {
    getNewPromotionTable();
    getExistingPromotionTable('');

});
function getNewPromotionTable() {
    $.post('api/customer_data_files/get_new_promotion.php', function (response) {
        var columnMapping = [
            'sno',
            'cus_name',
            'area',
            'mobile',
            'loan_cat',
            'loan_amount',
            'action'
        ];
        appendDataToTable('#new_list_table', response, columnMapping);
        setdtable('#new_list_table', "New Promotion List");
        $('#new_form input').val('');
        $('#new_form input').css('border', '1px solid #cecece');
    }, 'json')
}
function getNewPromoDelete(id) {
    $.post('api/customer_data_files/delete_new_promotion.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('Success', 'Customer Data Deleted Successfully!');
            getNewPromotionTable()
        } else {
            swalError('Error', 'Failed to Delete Customer Data: ' + response);
        }
    }, 'json');
}
function getExistingPromotionTable(existing_details) {

    $.post('api/customer_data_files/get_existing_promotion.php', { existing_details }, function (response) {
        var columnMapping = [
            'sno',
            'cus_id',
            'aadhar_num',
            'cus_name',
            'mobile1',
            'area',
            'linename',
            'branch_name',
            'c_sts',
            'c_substs',
            'action'
        ];
        appendDataToTable('#existing_list_table', response, columnMapping);
        setdtable('#existing_list_table',"Existing List");
        setDropdownScripts();

    }, 'json')
}

function getRePromotionTable(repromotion_details) {
    $.post('api/customer_data_files/get_repromotion_list.php', { repromotion_details }, function (response) {
        var columnMapping = [
            'sno',
            'cus_id',
            'aadhar_num',
            'cus_name',
            'mobile1',
            'area',
            'linename',
            'branch_name',
            'c_sts',
            'action'
        ];
        appendDataToTable('#repromotion_list_table', response, columnMapping);
        setdtable('#repromotion_list_table',"Re-Promotion List");
        setDropdownScripts();
    }, 'json')
}

function getExistingData(datas) {
    let cus_id = datas.cus_id;
    let coll_data = datas.coll_data;
    $.post('api/customer_data_files/get_existing_data.php', { cus_id, coll_data }, function (response) {
        if (response == '1') {
            swalSuccess("Success", "Existing Data Added Successfully!", "success");
            getExistingPromotionTable('');
        } else {
            swalError("Error", "Failed to Add Existing Data", "error");
        }
    });
}

function InsertRepromotionData(datas) {

    let cus_id = datas.cus_id;
    let repro_data = datas.coll_data;
    $.post('api/customer_data_files/submit_repromotion.php', { cus_id, repro_data }, function (response) {
        if (response == '1') {
            swalSuccess("Success", "Repromotion Data Added Successfully!", "success");
            // Replace the checkbox with "Needed" text
            getRePromotionTable('');
        } else {
            swalError("Error", "Failed to Add Repromotion Data", "error");
        }
    });
}