$(document).ready(function () {

    $('#cus_mobile').change(function () {
        let mobileValue = $(this).val().trim();  // Retrieve and trim the value of the mobile input

        // Check if mobileValue is not empty
        if (mobileValue !== '') {
            checkMobileNo(mobileValue, $(this).attr('id'));
        }
    });


    $(document).on('click', '.view_customer', function (event) {
        event.preventDefault();
        $('#customer_status').show();
        $('#custome_list, #search_form').hide();
        let cus_id = $(this).closest('tr').find('td:nth-child(2)').text();
        // let cus_id = $('#cust_id').val().replace(/\s/g, '');
        let aadhar_num = $('#aadhar_nums').val().replace(/\s/g, '');
        let cus_name = $('#cust_name').val();
        let area = $('#cus_area').val();
        let mobile = $('#cus_mobile').val();

        OnLoadFunctions(cus_id, aadhar_num, cus_name, area, mobile)
    })
    $(document).on('click', '.noc-summary', function (event) {
        event.preventDefault();
        $('#noc_summary').show();
        $('#customer_status, #custome_list, #search_form').hide();
        let cp_id = $(this).attr('value');
        $('#cp_id').val(cp_id)
        callAllFunctions(cp_id);
    })
    $('#back_btn').click(function () {
        $('#customer_status').show();
        $('#loan_entry_content').hide();
    });
    $('#loan_back_btn').click(function () {

        $('#customer_status').show();
        $('#loan_content').hide();
    });
    $('#doc_back_btn').click(function () {

        $('#customer_status').show();
        $('#loan_issue_content').hide();
    });
    $('#back_to_search').click(function (event) {
        event.preventDefault();
        $('#customer_status').hide();
        $('#custome_list, #search_form').show();
    })
    $('#back_to_cus_status').click(function (event) {
        event.preventDefault();
        $('#noc_summary').hide();
        $('#customer_status').show();
    })
    $(document).on('click', '.due-chart', function () {

        $('#due_chart_model').modal('show');
        var cp_id = $(this).attr('value');
        var cus_id = $('#cus_id').val();
        dueChartList(cp_id, cus_id); // To show Due Chart List.
        setTimeout(() => {
            $('.print_due_coll').click(function () {
                var id = $(this).attr('value');
                Swal.fire({
                    title: 'Print',
                    text: 'Do you want to print this collection?',
                    imageUrl: 'img/printer.png',
                    imageWidth: 300,
                    imageHeight: 210,
                    imageAlt: 'Custom image',
                    showCancelButton: true,
                    confirmButtonColor: '#009688',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'No',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'api/collection_files/print_collection.php',
                            data: { 'coll_id': id },
                            type: 'post',
                            cache: false,
                            success: function (html) {
                                $('#printcollection').html(html)
                                // Get the content of the div element
                                //var content = $("#printcollection").html();

                            }
                        })
                    }
                })
            })
        }, 1000)
    });

    $(document).on('click', '.penalty-chart', function () {

        $('#penalty_model').modal('show');
        let cp_id = $(this).attr('value');
        $('#cus_profile_id').val(cp_id)

        let cus_id = $('#cus_id').val();
        $.ajax({
            //to insert penalty by on click
            url: 'api/collection_files/collection_loan_details.php',
            data: { 'cp_id': cp_id },
            dataType: 'json',
            type: 'post',
            cache: false,
            success: function (response) {
                penaltyChartList(cp_id, cus_id); //To show Penalty List.
            }
        })
    });

    $(document).on('click', '.fine-chart', function () {

        $('#fine_model').modal('show');
        var cp_id = $(this).attr('value');
        fineChartList(cp_id) //To Show Fine Chart List
    });


    $(document).on('click', '.closed-remark', function () {
        $('#closed_remark_model').modal('show');

        let id = $(this).attr('value');
        $('#cus_profile_id').val(id)
        $.post('api/search_files/remark_info.php', { id }, function (response) {
            if (response.length > 0) {
                $('#sub_status').val(response[0].sub_status);
                $('#remark').val(response[0].remark);
            }
        }, 'json');

    });

    $(document).on('click', '.customer-profile', function () {
        $('#loan_entry_content').show();
        $('#customer_status, #custome_list, #search_form').hide();
        let id = $(this).attr('value');
        $('#cus_profile_id').val(id)
        editCustmerProfile(id)

    });


    $(document).on('click', '.loan-calculation', function () {
        $('#loan_content').show();
        $('#customer_status, #custome_list, #search_form').hide();
        let loanCalcId = $(this).attr('value');
        $('#loan_calculation_id').val(loanCalcId);
        loanCalculationEdit(loanCalcId);
        callLoanCaculationFunctions();

    });

    $(document).on('click', '.documentation', function () {
        $('#loan_issue_content').show();
        $('#customer_status, #custome_list, #search_form').hide();
        let id = $(this).attr('value'); //Customer Profile id From List page.
        $('#customer_profile_id').val(id);
        let cusID = $(this).attr('data-id'); //Cus id From List Page.
        $('#cus_id').val(cusID);
        $('.cheque-div').hide();
        $('.doc_div').hide();
        $('.mortgage-div').hide();
        $('.endorsement-div').hide();
        $('.gold-div').hide();
        getDocTable(id);
        getChequeInfoTable();
        getDocInfoTable();
        getMortInfoTable();
        getEndorsementInfoTable();
        getGoldInfoTable();

    });

    $('#submit_search').click(function (event) {
        event.preventDefault();
        let cus_id = $('#cust_id').val();
        let aadhar_num = $('#aadhar_nums').val().replace(/\s/g, '');
        let cus_name = $('#cust_name').val();
        let area = $('#cus_area').val();
        let mobile = $('#cus_mobile').val();

        if (validate()) {
            $.ajax({
                url: 'api/search_files/search_customer.php',
                type: 'POST',
                data: { cus_id, aadhar_num, cus_name, area, mobile },
                success: function (data) {
                    $('#custome_list').show();
                    getSearchTable(data);
                }
            });
        } else {
            $('#custome_list').hide();
        }
    });
    //////////////////////////////////////////////////////////////customer profile////////////////////////////////////////////////////
    $('#guarantor_name').change(function () {
        var guarantorId = $(this).val();
        if (guarantorId) {
            getGrelationshipName(guarantorId);
        } else {
            $('#relationship').val('');
        }
    })


    $('#area').change(function () {
        var areaId = $(this).val();
        if (areaId) {
            getAlineName(areaId);
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

});
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
function validate() {
    let response = true;

    let cus_id = $('#cust_id').val().trim();
    let aadhar_num = $('#aadhar_nums').val().trim();
    let cus_name = $('#cust_name').val().trim();
    let area = $('#cus_area').val().trim();
    let mobile = $('#cus_mobile').val().trim();

    // Reset all field borders initially
    $('#cust_id, #cust_name, #cus_area, #cus_mobile').css('border', '1px solid #cecece');

    // Check if any one field is filled
    if (cus_id || aadhar_num || cus_name || area || mobile) {
        // If any field is filled, reset the other fields' borders
        if (cus_id) {
            $('#cust_name, #cus_area, #cus_mobile ,#aadhar_nums').css('border', '1px solid #cecece');
        } else if (aadhar_num) {
            $('#cust_id , #cust_name, #cus_area, #cus_mobile').css('border', '1px solid #cecece');
        } else if (cus_name) {
            $('#cust_id ,#aadhar_nums , #cus_area, #cus_mobile').css('border', '1px solid #cecece');
        } else if (area) {
            $('#cust_id , #aadhar_nums , #cust_name, #cus_mobile ').css('border', '1px solid #cecece');
        } else if (mobile) {
            $('#cust_id, #aadhar_nums , #cust_name, #cus_area').css('border', '1px solid #cecece');
        }
    } else {
        // If no fields are filled, show validation errors
        if (!validateField(cus_id, 'cust_id')) {
            response = false;
        }
        if (!validateField(aadhar_num, 'aadhar_nums')) {
            response = false;
        }
        if (!validateField(cus_name, 'cust_name')) {
            response = false;
        }
        if (!validateField(area, 'cus_area')) {
            response = false;
        }
        if (!validateField(mobile, 'cus_mobile')) {
            response = false;
        }

    }

    return response;
}

function getSearchTable(data) {
    // Assuming response is in JSON format and contains customer data
    let response = JSON.parse(data);
    // if (response && response.length > 0) {
    var columnMapping = [
        'sno',
        'cus_id',
        'aadhar_num',
        'cus_name',
        'area',
        'branch_name',
        'linename',
        'mobile1',
        'action'
    ];
    appendDataToTable('#search_table', response, columnMapping);
    setdtable('#search_table', "Customer List");
    setDropdownScripts();
    // }
}
function getLoanTable(cus_id, aadhar_num, cus_name, area, mobile, pending_sts, od_sts, due_nil_sts, balAmnt) {
    $.post('api/search_files/search_loan.php', { cus_id, aadhar_num, cus_name, area, mobile, pending_sts, od_sts, due_nil_sts, balAmnt }, function (response) {
        var columnMapping = [
            'sno',
            'loan_date',
            'loan_id',
            'loan_category',
            'loan_amount',
            'status',
            'sub_status',
            'info',
            'charts'
        ];
        appendDataToTable('#status_table', response, columnMapping);
        setdtable('#status_table', "Loan List");
        //Dropdown in List Screen
        setDropdownScripts();
    }, 'json');
}

function closeChartsModal() {
    $('#due_chart_model').modal('hide');
    $('#penalty_model').modal('hide');
    $('#fine_model').modal('hide');
    $('#closed_remark_model').modal('hide');
}
function dueChartList(cp_id, cus_id) {
    $.ajax({
        url: 'api/collection_files/get_due_chart_list.php',
        data: { 'cp_id': cp_id, 'cus_id': cus_id },
        type: 'post',
        cache: false,
        success: function (response) {
            $('#due_chart_table_div').empty();
            $('#due_chart_table_div').html(response);
        }
    }).then(function () {

        $.post('api/collection_files/get_due_method_name.php', { cp_id }, function (response) {
            $('#dueChartTitle').text('Due Chart ( ' + response['due_method'] + ' - ' + response['loan_type'] + ' ');
        }, 'json');
    })

}
//Penalty Chart List
function penaltyChartList(cp_id, cus_id) {
    $.ajax({
        url: 'api/collection_files/get_penalty_chart_list.php',
        data: { 'cp_id': cp_id, 'cus_id': cus_id },
        type: 'post',
        cache: false,
        success: function (response) {
            $('#penalty_chart_table_div').empty()
            $('#penalty_chart_table_div').html(response)
        }
    });//Ajax End.
}


//Fine Chart List
function fineChartList(cp_id) {
    $.ajax({
        url: 'api/collection_files/get_fine_chart_list.php',
        data: { 'cp_id': cp_id },
        type: 'post',
        cache: false,
        success: function (response) {
            $('#fine_chart_table_div').empty()
            $('#fine_chart_table_div').html(response)
        }
    });//Ajax End.
}

function OnLoadFunctions(cus_id, aadhar_num, cus_name, area, mobile) {
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
        var bal_amt = balAmnt;
        getLoanTable(cus_id, aadhar_num, cus_name, area, mobile, pending_sts, od_sts, due_nil_sts, balAmnt)
        hideOverlay();//loader stop
    });
}//Auto Load function END

/////////////////////////////////////////////////////////////////////////customer profile//////////////////////////////////////////////
function getFamilyInfoTable() {
    let cus_id = $('#cus_id').val();
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
function getPropertyInfoTable() {
    let cus_id = $('#cus_id').val();
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_entry/property_creation_list.php', { cus_id, cus_profile_id }, function (response) {
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
function getBankInfoTable() {
    let cus_id = $('#cus_id').val();
    let cus_profile_id = $('#customer_profile_id').val()
    $.post('api/loan_entry/bank_creation_list.php', { cus_id, cus_profile_id }, function (response) {
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
function getKycInfoTable() {
    let cus_id = $('#cus_id').val();
    let cus_profile_id = $('#customer_profile_id').val()
    $.post('api/loan_entry/kyc_creation_list.php', { cus_id, cus_profile_id }, function (response) {
        var columnMapping = [
            'sno',
            'proof_of',
            'fam_relationship',
            'proof',
            'proof_detail',
            'upload',
        ];
        appendDataToTable('#kyc_info', response, columnMapping);
        setdtable('#kyc_info', "KYC Info List");
    }, 'json')
}
function getAreaName() {
        let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_entry/get_area.php',{cus_profile_id},function (response) {
        let appendAreaOption = '';
        appendAreaOption += "<option value='0'>Select Area Name</option>";
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

function dataCheckList(cus_id, aadhar_num, cus_name, cus_mble_no) {
    $.post('api/loan_entry/datacheck_name.php', { cus_id }, function (response) {
        //Name
        $('#name_check').empty();
        $('#name_check').append("<option value=''>Select Name</option>");
        $('#name_check').append('<option value="' + cus_name + '">' + cus_name + '</option>');
        $.each(response, function (index, val) {
            $('#name_check').append("<option value='" + val.fam_name + "'>" + val.fam_name + "</option>");
        });

        //Adhar no
        $('#aadhar_check').empty();
        $('#aadhar_check').append("<option value=''>Select Aadhar Number</option>");
        $('#aadhar_check').append('<option value="' + aadhar_num + '">' + aadhar_num + '</option>');
        $.each(response, function (index, val) {
            $('#aadhar_check').append("<option value='" + val.fam_aadhar + "'>" + val.fam_aadhar + "</option>");
        });

        //Mobile no 
        $('#mobile_check').empty();
        $('#mobile_check').append("<option value=''>Select Mobile Number</option>");
        $('#mobile_check').append('<option value="' + cus_mble_no + '">' + cus_mble_no + '</option>');
        $.each(response, function (index, val) {
            $('#mobile_check').append("<option value='" + val.fam_mobile + "'>" + val.fam_mobile + "</option>");
        });

    }, 'json');
}
function getGuarantorName() {
    let cus_id = $('#cus_id').val();
    $.post('api/loan_entry/get_guarantor_name.php', { cus_id }, function (response) {
        let appendGuarantorOption = '';
        appendGuarantorOption += "<option value='0'>Select Guarantor Name</option>";
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

function editCustmerProfile(id) {
    $.post('api/loan_entry/customer_profile_data.php', { id: id }, function (response) {
        $('#customer_profile_id').val(response[0].id);
        $('#area_edit').val(response[0].area);
        $('#cus_id').val(response[0].cus_id);
        $('#adhar_num').val(response[0].aadhar_num);
        $('#cus_name').val(response[0].cus_name);
        $('#gender').val(response[0].gender);
        $('#dob').val(response[0].dob);
        $('#age').val(response[0].age);
        $('#mobile2').val(response[0].mobile2);
        $('#mobile1').val(response[0].mobile1);
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
        dataCheckList(response[0].cus_id, response[0].aadhar_num, response[0].cus_name, response[0].mobile1)
        getGuarantorName()
        getAreaName()
        setTimeout(() => {
            getFamilyInfoTable()
            getPropertyInfoTable()
            getBankInfoTable()
            getKycInfoTable()
            $('#area').trigger('change');
            $('#guarantor_name').trigger('change');
        }, 1000);

        if (response[0].cus_data == 'Existing') {
            $('#cus_status').show();
            $('#data_checking_div').show();
        } else {
            $('#cus_status').hide();
            $('#data_checking_div').hide();
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
///////////////////////////////////////////////////////////Documentation////////////////////////////////////////////////////////////////////
function getChequeInfoTable() {
    let cus_profile_id = $('#customer_profile_id').val();
    $.post('api/loan_issue_files/cheque_info_list.php', { cus_profile_id }, function (response) {
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
        ]
        appendDataToTable('#cheque_info_table', response, chequeColumn);
        setdtable('#cheque_info_table', 'Cheque Info List');
    }, 'json');
}
function getDocTable(cusProfileId) {
    $.post('api/loan_entry/loan_calculation/document_need_list.php', { cusProfileId }, function (response) {
        let docColumn = [
            "sno",
            "document_name"
        ]
        appendDataToTable('#doc_table', response, docColumn);
        setdtable('#doc_table', 'Document List');
    }, 'json');
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
        setdtable('#document_info', 'Document Info List');
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

/////////////////////////////////////////////////////////////Loan CalCulation ///////////////////////////////////////////////////////////////////
$(document).ready(function () {

    $('#loan_category_calc').change(function () {
        if ($(this).val() != '') {
            $('#loan_amount_calc').val('')
            getLoanCatDetails($(this).val());
            $('#profit_type_calc').val('').trigger('change');
            $('#loan_category_calc2').val($(this).val())
        }
    });


    $('#scheme_due_method_calc').change(function () {
        let schemeDueMethod = $(this).val();
        let loanCatId = $('#loan_category_calc').val();
        dueMethodScheme(schemeDueMethod, loanCatId);
        $('#due_startdate_calc').val('');
        $('#maturity_date_calc').val('');
    });

    $('#scheme_name_calc').change(function () { //Scheme Name change event
        let scheme_id = $(this).val();
        schemeCalAjax(scheme_id);
        $('#due_startdate_calc').val('');
        $('#maturity_date_calc').val('');
    });

    $('#refresh_cal').click(function () {
        $('.int-diff').text('*'); $('.due-diff').text('*'); $('.doc-diff').text('*'); $('.proc-diff').text('*'); $('.refresh_loan_calc').val('');
        let loan_amt = $('#loan_amount_calc').val(); let int_rate = $('#interest_rate_calc').val(); let due_period = $('#due_period_calc').val(); let doc_charge = $('#doc_charge_calc').val(); let proc_fee = $('#processing_fees_calc').val();

        if (loan_amt != '' && int_rate != '' && due_period != '' && doc_charge != '' && proc_fee != '') {
            let due_type = $('#due_type_calc').val(); //If Changes not found in profit method, calculate loan amt for monthly basis
            if (due_type == 'Interest') {
                getLoanInterest(loan_amt, int_rate, doc_charge, proc_fee);

            } else if (due_type == 'EMI') {
                getLoanAfterInterest(loan_amt, int_rate, due_period, doc_charge, proc_fee);
            }

            let due_method_scheme = $('#scheme_due_method_calc').val();
            if (due_method_scheme == '1') {//Monthly scheme as 1
                getLoanMonthly(loan_amt, int_rate, due_period, doc_charge, proc_fee);

            } else if (due_method_scheme == '2') {//Weekly scheme as 2
                getLoanWeekly(loan_amt, int_rate, due_period, doc_charge, proc_fee);

            } else if (due_method_scheme == '3') {//Daily scheme as 3
                getLoanDaily(loan_amt, int_rate, due_period, doc_charge, proc_fee);

            }
            changeInttoBen();
        } else {
            swalError('Warning', 'Kindly Fill the Calculation fields.')
        }
    });

    {
        // Get today's date
        var today = new Date().toISOString().split('T')[0];
        //Set loan date
        $('#loan_date_calc').val(today);
        //Due start date -- set min date = current date.
        $('#due_startdate_calc').attr('min', today);
    }

    $('#scheme_day_calc').change(function () {
        $('#due_start_from').val('');
        $('#maturity_month').val('');
    })

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

    $('#referred_calc').change(function () {
        let referred = $('#referred_calc').val();
        if (referred == '0') {
            $('#agent_id_calc').prop('disabled', false).val('');
            $('#agent_name_calc').val('');
            getAgentID();
        } else {
            $('#agent_id_calc').prop('disabled', true).val('');
            $('#agent_name_calc').prop('readonly', true).val('');
        }
    });




}); //Document END.

function callLoanCaculationFunctions() {
    getLoanCategoryName();
    let loan_calc_id = $('#loan_calculation_id').val();

    let cus_profile_id = $('#customer_profile_id').val();
    getDocNeedTable(cus_profile_id);
}



function getLoanCategoryName() {
    $.post('api/common_files/get_loan_category_creation.php', function (response) {
        let appendLoanCatOption = '';
        appendLoanCatOption += '<option value="">Select Loan Category</option>';
        $.each(response, function (index, val) {
            let selected = '';
            let loan_category_calc2 = $('#loan_category_calc2').val();
            if (val.id == loan_category_calc2) {
                selected = 'selected';
            }
            appendLoanCatOption += '<option value="' + val.id + '" ' + selected + '>' + val.loan_category + '</option>';
        });
        $('#loan_category_calc').empty().append(appendLoanCatOption);
    }, 'json');
}

function getAgentID() {
    $.post('api/agent_creation/agent_creation_list.php', function (response) {
        let appendAgentIdOption = '';
        appendAgentIdOption += '<option value="">Select Agent ID</option>';
        $.each(response, function (index, val) {
            let selected = '';
            let agent_id_edit_it = '';
            if (val.id == agent_id_edit_it) {
                selected = 'selected';
            }
            appendAgentIdOption += '<option value="' + val.id + '" ' + selected + '>' + val.agent_code + '</option>';
        });
        $('#agent_id_calc').empty().append(appendAgentIdOption);
    }, 'json');
}

function getLoanCatDetails(id) {
    $.post('api/loan_entry/loan_calculation/getLoanCatDetails.php', { id }, function (response) {
        $('#due_method_calc').val(response[0].due_method);

        if (response[0].due_type == 'emi') {
            $('#due_type_calc').val('EMI');
        } else if (response[0].due_type == 'interest') {
            $('#due_type_calc').val('Interest');
        }
        let cus_limit = parseInt($('#cus_limit').val());
        let loan_limit = parseInt(response[0].loan_limit);
        let min_loan_limit;

        if (isNaN(cus_limit) || isNaN(loan_limit)) {
            min_loan_limit = 0; // Both values are NaN
        } else {
            min_loan_limit = (cus_limit < loan_limit) ? cus_limit : loan_limit; // Both values are valid numbers
        }
        $('#loan_amount_calc').attr('onChange', `if( parseFloat($(this).val()) > '` + min_loan_limit + `' ){ alert("Enter Lesser than '${min_loan_limit}'"); $(this).val(""); }`); //To check value between range

        var int_rate_upd = ($('#int_rate_upd').val()) ? $('#int_rate_upd').val() : '';
        var due_period_upd = ($('#due_period_upd').val()) ? $('#due_period_upd').val() : '';
        var doc_charge_upd = ($('#doc_charge_upd').val()) ? $('#doc_charge_upd').val() : '';
        var proc_fee_upd = ($('#proc_fees_upd').val()) ? $('#proc_fees_upd').val() : '';
        //To set min and maximum 
        $('.min-max-int').text('* (' + response[0].interest_rate_min + '% - ' + response[0].interest_rate_max + '%) ');
        $('#interest_rate_calc').attr('onChange', `if( parseFloat($(this).val()) > '` + response[0].interest_rate_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else
                            if( parseFloat($(this).val()) < '`+ response[0].interest_rate_min + `' && parseFloat($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
        $('#interest_rate_calc').val(int_rate_upd);
        $('.min-max-due').text('* (' + response[0].due_period_min + ' - ' + response[0].due_period_max + ') ');
        $('#due_period_calc').attr('onChange', `if( parseInt($(this).val()) > '` + response[0].due_period_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else
                            if( parseInt($(this).val()) < '`+ response[0].due_period_min + `' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
        $('#due_period_calc').val(due_period_upd);

         let docType = (response[0].document_charge_type == 'percentage') ? '%' : '₹';
        $('.min-max-doc').text('* (' + response[0].doc_charge_min + ' ' + docType + ' - ' + response[0].doc_charge_max + ' ' + docType + ') ');

        $('#doc_charge_calc').attr('onChange', `if( parseFloat($(this).val()) > '${response[0].doc_charge_max}' ){ alert("Enter Lesser Value"); $(this).val(""); }else if( parseFloat($(this).val()) < '${response[0].doc_charge_min}' && $(this).val() != '' ){ alert("Enter Higher Value"); $(this).val(""); }`);
        $('#doc_charge_calc').val(doc_charge_upd);

        let procType = (response[0].processing_fees_type == 'percentage') ? '%' : '₹';
        $('.min-max-proc').text('* (' + response[0].processing_fee_min + ' ' + procType + ' - ' + response[0].processing_fee_max + ' ' + procType + ')');

        $('#processing_fees_calc').attr('onChange', `if( parseFloat($(this).val()) > '${response[0].processing_fee_max}' ){ alert("Enter Lesser Value"); $(this).val(""); }else if( parseFloat($(this).val()) < '${response[0].processing_fee_min}' && $(this).val() != '' ){ alert("Enter Higher Value"); $(this).val(""); }`);
        $('#processing_fees_calc').val(proc_fee_upd);

    }, 'json');
}

function dueMethodScheme(schemeDueMethod, loanCatId) {
    $.post('api/common_files/get_due_method_scheme.php', { schemeDueMethod, loanCatId }, function (response) {

        let appendSchemeNameOption = '';
        appendSchemeNameOption += '<option value="">Select Scheme Name</option>';
        $.each(response, function (index, val) {
            let selected = '';
            let scheme_edit_it = $('#scheme_name_edit').val();
            if (val.id == scheme_edit_it) {
                selected = 'selected';
            }
            appendSchemeNameOption += '<option value="' + val.id + '" ' + selected + '>' + val.scheme_name + '</option>';
        });
        $('#scheme_name_calc').empty().append(appendSchemeNameOption);
    }, 'json');

    if (schemeDueMethod == '2') {
        $('.scheme_day').show();
    } else {
        $('.scheme_day').hide();
        $('.scheme_day_calc').val('');
    }
}

function schemeCalAjax(id) {

    if (id != '') {
        let doc_charge_upd = ($('#doc_charge_upd').val()) ? $('#doc_charge_upd').val() : '';
        let proc_fee_upd = ($('#proc_fees_upd').val()) ? $('#proc_fees_upd').val() : '';

        $.post('api/loan_category_creation/get_scheme_data.php', { id }, function (response) {
            //To set min and maximum 
            $('#interest_rate_calc').val(response[0].interest_rate_percent);// setting readonly due to fixed interest
            $('#due_period_calc').val(response[0].due_period_percent);// setting readonly due to fixed due period

            (response[0].doc_charge_type == 'percent') ? type = '%' : type = '₹';//Setting symbols
            $('.min-max-doc').text('* (' + response[0].doc_charge_min + ' ' + type + ' - ' + response[0].doc_charge_max + ' ' + type + ') '); //setting min max values in span
            $('#doc_charge_calc').attr('onChange', `if( parseInt($(this).val()) > '` + response[0].doc_charge_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else
                                    if( parseInt($(this).val()) < '`+ response[0].doc_charge_min + `' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
            $('#doc_charge_calc').val(doc_charge_upd);

            (response[0].processing_fee_type == 'percent') ? type = '%' : type = '₹';//Setting symbols
            $('.min-max-proc').text('* (' + response[0].processing_fee_min + ' ' + type + ' - ' + response[0].processing_fee_max + ' ' + type + ') ');//setting min max values in span
            $('#processing_fees_calc').attr('onChange', `if( parseInt($(this).val()) > '` + response[0].processing_fee_max + `' ){ alert("Enter Lesser Value"); $(this).val(""); }else
                                if( parseInt($(this).val()) < '`+ response[0].processing_fee_min + `' && parseInt($(this).val()) != '' ){ alert("Enter Higher Value"); $(this).val(""); } `); //To check value between range
            $('#processing_fees_calc').val(proc_fee_upd);

        }, 'json');

    }
}

function getDocNeedTable(cusProfileId) {
    $.post('api/loan_entry/loan_calculation/document_need_list.php', { cusProfileId }, function (response) {
        let loanCategoryColumn = [
            "sno",
            "document_name",
        ]
        appendDataToTable('#doc_need_table', response, loanCategoryColumn);
        setdtable('#doc_need_table', "Document Need List");
    }, 'json');
}

function loanCalculationEdit(id) {
    $.post('api/loan_entry/loan_calculation/loan_calculation_data.php', { id }, function (response) {
        $('#loan_id_calc').val(response[0].loan_id);
        $('#loan_category_calc').val(response[0].loan_category);
        $('#loan_category_calc2').val(response[0].loan_category);
        $('#category_info_calc').val(response[0].category_info);
        $('#loan_amount_calc').val(response[0].loan_amount);
        $('#profit_type_calc').val(response[0].profit_type);
        $('#due_method_calc').val(response[0].due_method);
        $('#due_type_calc').val(response[0].due_type);
        $('#profit_method_calc').val(response[0].profit_method);
        $('#scheme_due_method_calc').val(response[0].scheme_due_method);
        $('#scheme_day_calc').val(response[0].scheme_day);
        $('#scheme_name_edit').val(response[0].scheme_name);
        $('#int_rate_upd').val(response[0].interest_rate);
        $('#due_period_upd').val(response[0].due_period);
        $('#doc_charge_upd').val(response[0].doc_charge);
        $('#proc_fees_upd').val(response[0].processing_fees);
        $('#loan_amnt_calc').val(moneyFormatIndia(response[0].loan_amnt));
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
        $('#referred_calc').val(response[0].referred);
        $('#referred_calc').trigger('change');

        $('#profit_type_calc_scheme').show();
          if (response[0].due_type === 'EMI') {
                $('#due_type_calc').val('EMI');
                $('#profit_type_calc').prop('disabled', false)
                $('.interest_type').hide();
                $('.interest_cal').show();
                $('#calc_val').val(response[0].interest_rate_min);
                $('#scheme_val').val(response[0].scheme_name);
            } else if (response[0].due_type === 'Interest') {
                $('#due_type_calc').val('Interest');
                $('.interest_type').show();
                $('.interest_cal').hide();
                $('#interest_calculate').val(response[0].interest_calculate);
                $('#profit_type_calc').val(0);
                $('#profit_type_calc').prop('disabled', true)
            }
        if (response[0].profit_type == '0') {//Loan Calculation
            $('.calc').show();
            $('.scheme').hide();
            $('.scheme_day').hide();
            getLoanCatDetails(response[0].loan_category);
        } else if (response[0].profit_type == '1') { //Scheme
            dueMethodScheme(response[0].scheme_due_method, response[0].loan_category)
            $('.calc').hide();
            $('.scheme').show();
            schemeCalAjax(response[0].scheme_name)
        }


        setTimeout(() => {

            $('#agent_id_calc').val(response[0].agent_id);
            $('#agent_name_calc').val(response[0].agent_name);
            $('#refresh_cal').trigger('click');
        }, 1000);
    }, 'json');
}
//////////////////////////////////////////////////////////////// Loan Calculation END //////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////// NOC //////////////////////////////////////////////////////////////////////
function callAllFunctions(cp_id) {
    $('.cheque-div').hide();
    $('.doc_div').hide();
    $('.mortgage-div').hide();
    $('.endorsement-div').hide();
    $('.gold-div').hide();
    getChequeList(cp_id);
    getMortgageList(cp_id);
    getEndorsementList(cp_id);
    getOtherDocumentList(cp_id);
    getGoldList(cp_id);

    $('#noc_relation').val('');
    getFamilyMember();
    setTimeout(() => {
        setSubmittedDisabled();
    }, 1000);
}

function getChequeList(cp_id) {
    $.post('api/noc_files/noc_cheque_list.php', { cp_id }, function (response) {
        if (response && response.length > 0) {
            $('.cheque-div').show();
        }
        let nocChequeColumns = [
            'sno',
            'holder_type',
            'holder_name',
            'relationship',
            'bank_name',
            'cheque_no',
            'date_of_noc',
            'noc_member',
            'noc_relationship',
            'action'
        ];
        appendDataToTable('#noc_cheque_list_table', response, nocChequeColumns);
        setdtable('#noc_cheque_list_table', "Cheque Info List");

    }, 'json');
}

function getMortgageList(cp_id) {
    $.post('api/noc_files/noc_mortgage_list.php', { cp_id }, function (response) {
        if (response && response.length > 0) {
            $('.mortgage-div').show();
        }
        let nocMortgageColumns = [
            'sno',
            'holder_name',
            'relationship',
            'property_details',
            'mortgage_name',
            'designation',
            'reg_office',
            'date_of_noc',
            'noc_member',
            'noc_relationship',
            'action'
        ];
        appendDataToTable('#noc_mortgage_list_table', response, nocMortgageColumns);
        setdtable('#noc_mortgage_list_table', "Mortgage Info List");
    }, 'json');
}

function getEndorsementList(cp_id) {
    $.post('api/noc_files/noc_endorsement_list.php', { cp_id }, function (response) {
        if (response && response.length > 0) {
            $('.endorsement-div').show();
        }
        let nocEndorseColumns = [
            'sno',
            'holder_name',
            'relationship',
            'vehicle_details',
            'endorsement_name',
            'key_original',
            'rc_original',
            'date_of_noc',
            'noc_member',
            'noc_relationship',
            'action'
        ];
        appendDataToTable('#noc_endorsement_list_table', response, nocEndorseColumns);
        setdtable('#noc_endorsement_list_table', "Endorsement Info List");
    }, 'json');
}

function getOtherDocumentList(cp_id) {
    $.post('api/noc_files/noc_document_info_list.php', { cp_id }, function (response) {
        if (response && response.length > 0) {
            $('.doc_div').show();
        }
        let nocDocInfoColumns = [
            'sno',
            'doc_name',
            'doc_type',
            'holder_name',
            'upload',
            'date_of_noc',
            'noc_member',
            'noc_relationship',
            'action'
        ];
        appendDataToTable('#noc_document_list_table', response, nocDocInfoColumns);
        setdtable('#noc_document_list_table', "Document Info List");
    }, 'json');
}
function getGoldList(cp_id) {
    $.post('api/noc_files/noc_gold_list.php', { cp_id }, function (response) {
        if (response && response.length > 0) {
            $('.gold-div').show();
        }
        let nocGoldColumns = [
            'sno', 'gold_type', 'purity', 'weight', 'date_of_noc',
            'noc_member', 'noc_relationship', 'action'
        ];
        appendDataToTable('#noc_gold_list_table', response, nocGoldColumns);
        setdtable('#noc_gold_list_table', "Gold Info List");
    }, 'json');
}


function getFamilyMember() {
    let cus_id = $('#cus_id').val();
    let cus_name = $('#cus_name').val();
    $.post('api/loan_entry/get_guarantor_name.php', { cus_id }, function (response) {
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

function setSubmittedDisabled() {
    $('.noc_cheque_chkbx, .noc_mortgage_chkbx, .noc_endorsement_chkbx, .noc_doc_info_chkbx, .noc_gold_chkbx').each(function () {
        if ($(this).attr('data-id') == '1') {
            $(this).closest('tr').addClass('disabled-row');
            $(this).attr('checked', true).attr('disabled', true);
        }
    });

    var cheque_checkDisabled = $('.noc_cheque_chkbx:disabled').length === $('.noc_cheque_chkbx').length;
    var mort_checkDisabled = $('.noc_mortgage_chkbx:disabled').length === $('.noc_mortgage_chkbx').length;
    var endorse_checkDisabled = $('.noc_endorsement_chkbx:disabled').length === $('.noc_endorsement_chkbx').length;
    var doc_checkDisabled = $('.noc_doc_info_chkbx:disabled').length === $('.noc_doc_info_chkbx').length;
    var gold_checkDisabled = $('.noc_gold_chkbx:disabled').length === $('.noc_gold_chkbx').length;

    if (cheque_checkDisabled && mort_checkDisabled && endorse_checkDisabled && doc_checkDisabled && gold_checkDisabled) {
        $('#submit_noc').hide();
    } else {
        $('#submit_noc').show();
    }

}


