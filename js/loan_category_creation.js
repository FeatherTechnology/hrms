//Scheme Name Multi select initialization
const scheme_choices = new Choices('#scheme_name', {
    removeItemButton: true,
    noChoicesText: 'Select Scheme Name',
    allowHTML: true
});

$(document).ready(function () {
    $('.add_loancategory_btn, .back_to_loancategory_btn').click(function () {
        clearLoanCategoryCreationForm();
        swapTableAndCreation();
    });
    $("#due_type").change(function () {
        let dueType = $(this).val();
        $(".doc_charge_minmax").val(" ");
        $(".processing_minmax").val(" ");
        $("#overdue_penalty").val(" ");
        $(".overdue-span-val").text("%");
        $(".process-span-val").text("%");
        $(".document-span-val").text("%");
        if (dueType === "interest") {
            // Show penalty with radio options  
            $(".interest_type_options").show();
            $(".interest_type").show();
            $(".interest_scheme").hide();
        }
        else if (dueType === "emi") {
            // Show penalty but force percentage only
            $(".interest_type_options").hide();
            $(".interest_type").hide();
            $(".interest_scheme").show();
            $("input[name='over_due_type'][value='percentage']").prop("checked", true);
            $("input[name='doc_charge_type'][value='percentage']").prop("checked", true);
            $("input[name='process_fee_type'][value='percentage']").prop("checked", true);
        }

    });

    // Change penalty unit on radio select
    // $("input[name='over_due_type']").change(function () {
    //     let type = $(this).val();
    //     $("#penalty_unit").text(type === "percentage" ? "%" : "₹");
    // });
    $('.interest_minmax').change(function () {
        checkMinMaxValue('#interest_rate_min', '#interest_rate_max');
    });

    $('.due_period_minmax').change(function () {
        checkMinMaxValue('#due_period_min', '#due_period_max');
    });

    $('.doc_charge_minmax').change(function () {
        checkMinMaxValue('#doc_charge_min', '#doc_charge_max');
    });

    $('.processing_minmax').change(function () {
        checkMinMaxValue('#processing_fee_min', '#processing_fee_max');
    });

    $('.doc-type').click(function () {
        let typeValue = $(this).find('input').val();
        let type = (typeValue == 'percent') ? '%' : '₹';
        $('.doc-span-val').text(type);
        $('#doc_charge_type').val(typeValue);
    });

    $('.processing-type').click(function () {
        let processingTypeValue = $(this).find('input').val();
        let processingType = (processingTypeValue == 'percent') ? '%' : '₹';
        $('.processing-span-val').text(processingType);
        $('#processing_fee_type').val(processingTypeValue);
    });
    $('input[name=doc_charge_type]').click(function () {

        let Value = $(this).val();
        let type = (Value === 'percentage') ? '%' : '₹';
        $('.document-span-val').text(type);
        $('#document_charge').val(Value);
    });

    $('input[name=process_fee_type]').click(function () {
        let Value = $(this).val();
        let type = (Value === 'percentage') ? '%' : '₹';
        $('.process-span-val').text(type);
        $('#processing_type').val(Value);
    });

    $('input[name=over_due_type]').click(function () {
        let Value = $(this).val();
        let type = (Value === 'percentage') ? '%' : '₹';
        $('.overdue-span-val').text(type);
        $('#overdue_type').val(Value);
    });
    $('.scheme_doc_minmax').change(function () {
        checkMinMaxValue('#scheme_doc_charge_min', '#scheme_doc_charge_max');
    });

    $('.scheme_processing_minmax').change(function () {
        checkMinMaxValue('#scheme_processing_fee_min', '#scheme_processing_fee_max');
    });

    $('#scheme_name').change(function () {
        getSchemeListTable($(this).val());
    });

    /////////////////////////////////////////////////////Loan Category Modal START///////////////////////////////////////////////////
    $('#submit_addloan_category').click(function () {
        event.preventDefault();
        let loanCategoryName = $('#addloan_category_name').val(); let id = $('#addloan_category_id').val();
        var data = ['addloan_category_name']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });

        if (loanCategoryName != '') {
            if (isValid) {
                $.post('api/loan_category_creation/submit_loan_category.php', { loanCategoryName, id }, function (response) {
                    if (response == '1') {
                        swalSuccess('Success', 'Loan Category Added Successfully!');
                    } else if (response == '0') {
                        swalSuccess('Success', 'Loan Category Updated Successfully!');
                    } else if (response == '2') {
                        swalError('Warning', 'Loan Category Already Exists!');
                    }

                    getLoanCategoryTable();
                }, 'json');
                clearLoanCategory(); //To Clear All Fields in Loan Category creation.
            }

        }
    });

    $(document).on('click', '.loancatActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/loan_category_creation/get_loan_category_data.php', { id }, function (response) {
            $('#addloan_category_id').val(id);
            $('#addloan_category_name').val(response[0].loan_category);
        }, 'json');
    });

    $(document).on('click', '.loancatDeleteBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        swalConfirm('Delete', 'Do you want to Delete the Loan Category?', deleteLoanCategory, id);
        return;
    });
    /////////////////////////////////////////////////////Loan Category Modal END///////////////////////////////////////////////////

    /////////////////////////////////////////////////////Scheme Modal START///////////////////////////////////////////////////
    $('#submit_scheme').click(function (event) {
        event.preventDefault();

        let schemeFormData = {
            addSchemeName: $('#add_scheme_name').val(),
            schemeDueMethod: $('#scheme_due_method').val(),
            profitMethod: $('#profit_method').val(),
            schemeInterestRate: $('#scheme_interest_rate').val(),
            schemeDuePeriod: $('#scheme_due_period').val(),
            schemeOverduePenalty: $('#scheme_overdue_penalty').val(),
            docChargeType: $('#doc_charge_type').val(),
            schemeDocChargeMin: $('#scheme_doc_charge_min').val(),
            schemeDocChargeMax: $('#scheme_doc_charge_max').val(),
            processingFeeType: $('#processing_fee_type').val(),
            schemeProcessingFeeMin: $('#scheme_processing_fee_min').val(),
            schemeProcessingFeeMax: $('#scheme_processing_fee_max').val(),
            id: $('#add_scheme_id').val()
        }
        var data = ['add_scheme_name', 'scheme_due_method', 'profit_method', 'scheme_interest_rate', 'scheme_due_period', 'scheme_overdue_penalty', 'doc_charge_type', 'scheme_doc_charge_min', 'scheme_doc_charge_max', 'processing_fee_type', 'scheme_processing_fee_min', 'scheme_processing_fee_max']

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($('#' + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });

        // if (isFormDataValid(schemeFormData)) {
        if (isValid) {
            $.post('api/loan_category_creation/submit_scheme.php', schemeFormData, function (response) {
                if (response == '0') {
                    swalError('Warning', 'Processing Failed!');
                } else if (response == '1') {
                    swalSuccess('Success', 'Scheme Updated Successfully!');
                } else if (response == '2') {
                    swalSuccess('Success', 'Scheme Added Successfully!');
                } else if (response == '3') {
                    swalError('Access Denied', 'Scheme Already Added.');
                }
                clearSchemeForm();
                getSchemeTable();
            }, 'json');
        }
        // }
    });


    $(document).on('click', '.schemeActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/loan_category_creation/get_scheme_data.php', { id }, function (response) {
            $('#add_scheme_id').val(id);
            $('#add_scheme_name').val(response[0].scheme_name);
            $('#scheme_due_method').val(response[0].due_method);
            $('#profit_method').val(response[0].profit_method.trim());
            $('#scheme_interest_rate').val(response[0].interest_rate_percent);
            $('#scheme_due_period').val(response[0].due_period_percent);
            $('#scheme_overdue_penalty').val(response[0].overdue_penalty_percent);
            $('#doc_charge_type').val(response[0].doc_charge_type);
            $('#scheme_doc_charge_min').val(response[0].doc_charge_min);
            $('#scheme_doc_charge_max').val(response[0].doc_charge_max);
            $('#processing_fee_type').val(response[0].processing_fee_type);
            $('#scheme_processing_fee_min').val(response[0].processing_fee_min);
            $('#scheme_processing_fee_max').val(response[0].processing_fee_max);

            // Toggle the appropriate radio button based on the hidden input value
            if (response[0].doc_charge_type === 'percent') {
                $('#doc_charge_type_percent').prop('checked', true).closest('label').addClass('active');
                $('#doc_charge_type_rupee').prop('checked', false).closest('label').removeClass('active');
                $('.doc-span-val').text('%');
            } else if (response[0].doc_charge_type === 'rupee') {
                $('#doc_charge_type_rupee').prop('checked', true).closest('label').addClass('active');
                $('#doc_charge_type_percent').prop('checked', false).closest('label').removeClass('active');
                $('.doc-span-val').text('₹');
            }

            if (response[0].processing_fee_type === 'percent') {
                $('#processing_fee_type_percent').prop('checked', true).closest('label').addClass('active');
                $('#processing_fee_type_rupee').prop('checked', false).closest('label').removeClass('active');
                $('.processing-span-val').text('%');
            } else if (response[0].processing_fee_type === 'rupee') {
                $('#processing_fee_type_rupee').prop('checked', true).closest('label').addClass('active');
                $('#processing_fee_type_percent').prop('checked', false).closest('label').removeClass('active');
                $('.processing-span-val').text('₹');
            }

        }, 'json');
    });

    $(document).on('click', '.schemeDeleteBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        swalConfirm('Delete', 'Do you want to Delete the Scheme?', deleteScheme, id);
        return;
    });
    /////////////////////////////////////////////////////Scheme Modal END///////////////////////////////////////////////////

    $('#submit_loan_category_creation').click(function (event) {
        event.preventDefault();
        let isValid = true;

        if (!checkAllInputs()) {
            event.preventDefault();
            isValid = false;
           swalError('Warning', 'Please fill Loan Calculation Or Scheme ');
        }

        // Validate main form fields
        if (!validateField($('#loan_category').val(), 'loan_category')) {
            isValid = false;
        }
        if (!validateField($('#loan_limit').val(), 'loan_limit')) {
            isValid = false;
        }

        // Proceed with form submission if either Loan Calculation or Loan Scheme card is fully valid
        if (isValid) {
            let loan_limit_value = $('#loan_limit').val().replace(/,/g, '')
            let formData = {
                loan_category: $('#loan_category').val(),

                loan_limit: loan_limit_value,
                due_method: $('#due_method').val(),
                due_type: $('#due_type').val(),
                interest_calculate: $('#interest_calculate').val(),
                interest_rate_min: $('#interest_rate_min').val(),
                interest_rate_max: $('#interest_rate_max').val(),
                due_period_min: $('#due_period_min').val(),
                due_period_max: $('#due_period_max').val(),
                document_charge_type: $('#document_charge').val(),
                doc_charge_min: $('#doc_charge_min').val(),
                doc_charge_max: $('#doc_charge_max').val(),
                processing_fee_type: $('#processing_type').val(),
                processing_fee_min: $('#processing_fee_min').val(),
                processing_fee_max: $('#processing_fee_max').val(),
                overdue_type: $('#overdue_type').val(),
                overdue_penalty: $('#overdue_penalty').val(),
                scheme_name: $('#scheme_name').val(),
                id: $('#loan_cat_creation_id').val()
            };

            if (Array.isArray(formData.scheme_name)) {
                formData.scheme_name = formData.scheme_name.join(",");
            }

            // Submit form via AJAX
            $.post('api/loan_category_creation/submit_loan_category_creation.php', formData, function (response) {
                if (response === '2') {
                    swalSuccess('Success', 'Loan Category Added Successfully!');
                } else if (response === '1') {
                    swalSuccess('Success', 'Loan Category Updated Successfully!');
                } else {
                    swalError('Error', 'Error Occurred!');
                }
                clearLoanCategoryCreationForm();
                getLoanCategoryCreationTable();
                swapTableAndCreation(); // Change to table content
            });
        }
    });

    ///////////////////////////////////// EDIT Screen START   /////////////////////////////////////
    $(document).on('click', '.loanCatCreationActionBtn', function () {
        var id = $(this).attr('value'); // Get value attribute
        $.post('api/loan_category_creation/loan_category_creation_data.php', { id }, function (response) {
            $('#loan_cat_creation_id').val(id);
            $('#loan_category2').val(response[0].loan_category);
            $('#loan_limit').val(moneyFormatIndia(response[0].loan_limit));
            $('#due_type').val(response[0].due_type);
            $('#interest_rate_min').val(response[0].interest_rate_min);
            $('#interest_rate_max').val(response[0].interest_rate_max);
            $('#due_period_min').val(response[0].due_period_min);
            $('#due_period_max').val(response[0].due_period_max);
            $('#document_charge').val(response[0].document_charge_type);
            $('#doc_charge_min').val(response[0].doc_charge_min);
            $('#doc_charge_max').val(response[0].doc_charge_max);
            $('#processing_type').val(response[0].processing_fees_type);
            $('#processing_fee_min').val(response[0].processing_fee_min);
            $('#processing_fee_max').val(response[0].processing_fee_max);
            $('#overdue_type').val(response[0].penalty_type);
            $('#overdue_penalty').val(response[0].overdue_penalty);
            $('#scheme_name2').val(response[0].scheme_name);

            if (response[0].due_type == 'interest') {
                $(".interest_type_options").show();
                $('#interest_calculate').val(response[0].interest_calculate);
                $(".interest_type").show();
                $(".interest_scheme").hide();

                handleTypeChange($('#document_charge').val(), 'docpercentage', 'docamt', 'document-span-val');
                handleTypeChange($('#processing_type').val(), 'propercentage', 'procamt', 'process-span-val');
                handleTypeChange($('#overdue_type').val(), 'overpercentage', 'overamt', 'overdue-span-val');
            } else {
                $(".interest_type_options").hide();
                $(".interest_type").hide();
                $(".interest_scheme").show();
                $("input[name='over_due_type'][value='percentage']").prop("checked", true);
                $("input[name='doc_charge_type'][value='percentage']").prop("checked", true);
                $("input[name='process_fee_type'][value='percentage']").prop("checked", true);
            }

            setTimeout(() => {
                getLoanCategoryDropdown();
                getSchemeDropdown();
            }, 1000);

            swapTableAndCreation(); //to change to div to table content.
        }, 'json');
    });

    ///////////////////////////////////// EDIT Screen END  /////////////////////////////////////
    ///////////////////////////////////// Delete Screen START  /////////////////////////////////////
    $(document).on('click', '.loanCatCreationDeleteBtn', function () {
        let id = $(this).attr('value'); // Get value attribute
        swalConfirm('Delete', 'Do you want to Delete the Loan Category Creation?', deleteLoanCategoryCreation, id);
        return;
    });
    ///////////////////////////////////// Delete Screen END  /////////////////////////////////////

    $('#clear_loan_cat_form').click(() => {
        clearLoanCategoryCreationForm();
    })
    $('#loan_limit').on('keyup', function () {
        let raw = $(this).val().replace(/,/g, '');
        $(this).val(formatIndianNumber(raw));
    });

});//Document END.

//OnLoad/////
$(function () {
    setdtable('#loan_scheme_table');
    getLoanCategoryDropdown();
    getLoanCategoryCreationTable();
    getSchemeDropdown();
});

function getLoanCategoryCreationTable() {
    $.post('api/loan_category_creation/loan_category_creation_list.php', function (response) {
        var columnMapping = [
            'sno',
            'loan_category',
            'loan_limit',
            'action'
        ];
        appendDataToTable('#loancategory_creation_table', response, columnMapping);
        setdtable('#loancategory_creation_table', "Loan Category Creation List");
    }, 'json');
}

function swapTableAndCreation() {
    if ($('.loan_category_table_content').is(':visible')) {
        $('.loan_category_table_content').hide();
        $('.add_loancategory_btn').hide();
        $('#loan_category_creation_content').show();
        $('.back_to_loancategory_btn').show();
    } else {
        $('.loan_category_table_content').show();
        $('.add_loancategory_btn').show();
        $('#loan_category_creation_content').hide();
        $('.back_to_loancategory_btn').hide();
        getSchemeDropdown();
    }
}

function getLoanCategoryTable() {
    $.post('api/loan_category_creation/get_loan_category_list.php', function (response) {
        let loanCategoryColumn = [
            "sno",
            "loan_category",
            "action"
        ]
        appendDataToTable('#loan_category_table', response, loanCategoryColumn);
        setdtable('#loan_category_table', "Loan Category List");
    }, 'json');
}

function getLoanCategoryDropdown() {
    $.post('api/loan_category_creation/get_loan_category_list.php', function (response) {
        let appendLineNameOption = '';
        let loan_category2 = $('#loan_category2').val();
        appendLineNameOption += '<option value="">Select Loan Category</option>';
        $.each(response, function (index, val) {
            let selected = '';
            if (val.id == loan_category2) {
                selected = 'selected';
            }
            appendLineNameOption += '<option value="' + val.id + '" ' + selected + '>' + val.loan_category + '</option>';
        });
        $('#loan_category').empty().append(appendLineNameOption);

        clearLoanCategory();
    }, 'json');
}

function getSchemeTable() {
    $.post('api/loan_category_creation/get_scheme_list.php', function (response) {
        let schemeColumn = [
            "sno",
            "scheme_name",
            "due_method",
            "profit_method",
            "interest_rate_percent",
            "due_period_percent",
            "doc_charge_min",
            "doc_charge_max",
            "processing_fee_min",
            "processing_fee_max",
            "overdue_penalty_percent",
            "action"
        ]
        appendDataToTable('#scheme_modal_table', response, schemeColumn);
        setdtable('#scheme_modal_table', "Scheme List");
    }, 'json');
}

function getSchemeListTable(scheme_id) {
    if (Array.isArray(scheme_id)) {
        scheme_id = scheme_id.join(",");
    }

    $.post('api/loan_category_creation/get_scheme_list_based_scheme_dropdown.php', { scheme_id }, function (response) {
        let schemeColumn = [
            "sno",
            "scheme_name",
            "due_method",
            "profit_method",
            "interest_rate_percent",
            "due_period_percent",
            "doc_charge_min",
            "doc_charge_max",
            "processing_fee_min",
            "processing_fee_max",
            "overdue_penalty_percent"
        ]

        appendDataToTable('#loan_scheme_table', response, schemeColumn);
        setTimeout(function () {
            setdtable('#loan_scheme_table', "Loan Scheme List");
        }, 0);
    }, 'json');
}

function getSchemeDropdown() {
    $.post('api/loan_category_creation/get_scheme_list.php', function (response) {
        scheme_choices.clearStore();
        let selectedSchemeId = [];

        // Clean and prepare the selected IDs
        let schemename2 = ($('#scheme_name2').val() || '')
            .split(',')
            .map(s => s.trim()); // trim whitespace

        $.each(response, function (index, val) {
            let selected = '';

            if (schemename2.includes(val.id.toString())) {
                selected = 'selected';
                selectedSchemeId.push(val.id);
            }
            let items = [
                {
                    value: val.id,
                    label: val.scheme_name,
                    selected: selected
                }
            ];
            scheme_choices.setChoices(items);
            scheme_choices.init();
        });

        clearSchemeForm();
        getSchemeListTable(selectedSchemeId);
    }, 'json');
}

function deleteLoanCategory(id) {
    $.post('api/loan_category_creation/delete_loan_category.php', { id }, function (response) {
        if (response == '1') {
            swalSuccess('Success', 'Loan Category Deleted Successfully.');
            getLoanCategoryTable();
        } else if (response == '0') {
            swalError('Access Denied', 'Used in Loan Category Creation');
        } else {
            swalError('Error', 'Loan Category Delete Failed.');
        }
        $('#addloan_category_name').val('');
        $('#addloan_category_id').val('');
    }, 'json');
}

function deleteScheme(id) {
    $.post('api/loan_category_creation/delete_scheme.php', { id }, function (response) {
        if (response == '2') {
            swalSuccess('Success', 'Scheme Deleted Successfully');
            getSchemeTable();
        } else if (response == '1') {
            swalError('Access Denied', 'Used in Loan Category Creation');
        } else {
            swalError('Warning', 'Error occur while Delete Scheme.');
        }
    }, 'json');
}

function deleteLoanCategoryCreation(id) {
    $.post('api/loan_category_creation/delete_loan_category_creation.php', { id }, function (response) {
        if (response == '0') {
            swalSuccess('Success', 'Loan Category creation Deleted Successfully');
            getLoanCategoryCreationTable();
        } else if (response == '1') {
            swalError('Access Denied', 'Used in Another Screen.');
        } else if (response == '2') {
            swalError('Warning', 'Used in User Creation.');
        } else {
            swalError('Warning', 'Loan Category Creation Delete Failed!');
        }
    }, 'json');
}

function clearSchemeForm() {
    $('#add_scheme_id').val('0');
    $('#add_scheme_details').trigger('reset');

    $('#doc_charge_type_percent').prop('checked', true).closest('label').addClass('active');
    $('#doc_charge_type_rupee').prop('checked', false).closest('label').removeClass('active');
    $('.doc-span-val').text('%');
    $('#processing_fee_type_percent').prop('checked', true).closest('label').addClass('active');
    $('#processing_fee_type_rupee').prop('checked', false).closest('label').removeClass('active');
    $('.processing-span-val').text('%');
    $('#doc_charge_type').val('percent');
    $('#processing_fee_type').val('percent');
    $('#add_scheme_details input').css('border', '1px solid #cecece');
    $('#add_scheme_details select').css('border', '1px solid #cecece');
    $('#scheme_name').closest('.choices').find('.choices__inner').css('border', '1px solid #cecece');
}

function clearLoanCategory() {
    $('#addloan_category_name').val('');
    $('#addloan_category_id').val('0');
    $('#addloan_category_name').css('border', '1px solid #cecece');


}

function clearLoanCategoryCreationForm() {
    // Reset all input fields except the ones specified
    $('input:not(#due_method, #profit_method, #doc_charge_type, #processing_fee_type, #doc_charge_type_percent, #doc_charge_type_rupee, #processing_fee_type_percent, #processing_fee_type_rupee ,#overpercentage,#propercentage,#docpercentage,#docamt,#procamt,#overamt,#document_charge,#overdue_type,#processing_type)').val('');
    // Reset all select fields to their first option
    $('select').each(function () {
        $(this).val($(this).find('option:first').val());
    });
    $(".interest_type").hide();
    $(".overdue-span-val").text("%");
    $(".process-span-val").text("%");
    $(".document-span-val").text("%");
     $('#overdue_type').val('percentage');
     $('#document_charge').val('percentage');
     $('#processing_type').val('percentage');
    $(".interest_type_options").hide();
    $(".interest_scheme").show();
    $("input[name='over_due_type'][value='percentage']").prop("checked", true);
    $("input[name='doc_charge_type'][value='percentage']").prop("checked", true);
    $("input[name='process_fee_type'][value='percentage']").prop("checked", true);
    $('#loan_limit, #interest_rate_min, #interest_rate_max, #due_period_min, #due_period_max, #doc_charge_min, #doc_charge_max, #processing_fee_min, #processing_fee_max, #overdue_penalty').css('border', '1px solid #cecece');
    // Reset all select fields to their first option
    $('#loan_category_creation select').css('border', '1px solid #cecece');
    scheme_choices.clearInput();
    // getSchemeDropdown();
}

function checkMinMaxValue(minSelector, maxSelector) {
    let min = parseFloat($(minSelector).val());
    let max = parseFloat($(maxSelector).val());
    // Only proceed if both values are numbers
    if (!isNaN(min) && !isNaN(max)) {
        if (min > max) {
            swalError('Warning', 'Minimum value should be less than or equal to Maximum value');
            $(minSelector).val('');
            $(maxSelector).val('');
        }
    }
}
function handleTypeChange(type, percentId, rupeeId, spanClass) {
    if (type === 'percentage') {
        $(`#${percentId}`).prop('checked', true);
        $(`#${rupeeId}`).prop('checked', false);
        $(`.${spanClass}`).text('%');
    } else if (type === 'rupee') {
        $(`#${rupeeId}`).prop('checked', true);
        $(`#${percentId}`).prop('checked', false);
        $(`.${spanClass}`).text('₹');
    }
}
function checkAllInputs() {
    let due_type = $("#due_type").val();

    // Base selector list
    let selectorList = [
        '#due_type',
        '#interest_rate_min', '#interest_rate_max',
        '#due_period_min', '#due_period_max',
        '#doc_charge_min', '#doc_charge_max',
        '#processing_fee_min', '#processing_fee_max',
        '#overdue_penalty'
    ];

    // Add dynamic input
    if (due_type === 'interest') {
        selectorList.push('#interest_calculate');
    }

    // Create jQuery object
    let inputs = $(selectorList.join(', '));

    // Count filled normal inputs
    let filledInputs = inputs.filter(function () {
        if ($(this).is('select')) {
            let val = $(this).val();
            return val !== null && val.length > 0;
        } else {
            return $(this).val().trim() !== "";
        }
    }).length;

    // Multi-select value
    let schemeFilled = scheme_choices.getValue().length > 0;

    // Total inputs
    let totalInputs = inputs.length;

    // Validation rules
    if (filledInputs === 0 && !schemeFilled) {
        return false;
    } else if (filledInputs === totalInputs || (filledInputs === 0 && schemeFilled)) {
        return true;
    } else {
        return false;
    }
}