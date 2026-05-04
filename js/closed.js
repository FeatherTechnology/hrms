$(document).ready(function () {

    $(document).on('click', '.closed-details', function (event) {
        event.preventDefault();
        $('#closed_list').hide();
        $('#closed_main_container,.back_to_closed_list').show();
        let cus_id = $(this).attr('value');
        $.post('api/common_files/personal_info.php', { cus_id }, function (response) {
            if (response.length > 0) {
                $('#aadhar_num').val(response[0].aadhar_num);
                $('#cus_id').val(response[0].cus_id);
                $('#cus_name').val(response[0].cus_name);
                $('#area').val(response[0].area);
                $('#branch_name').val(response[0].branch_name);
                $('#line').val(response[0].linename);
                $('#mobile1').val(response[0].mobile1);
                let path = "uploads/loan_entry/cus_pic/";
                if (response[0].pic) {
                    $('#per_pic').val(response[0].pic);
                    var img = $('#imgshow');
                    img.attr('src', path + response[0].pic);
                }
                else {
                    $('#imgshow').attr('src', 'img/avatar.png');
                }
            }
        }, 'json');

        getClosedLoanList(cus_id);

    })

    $(document).on('click', '.closed-move', function () {
        let cus_id = $(this).attr('value');
        let cus_sts = 10;
        moveToNext(cus_id, cus_sts);
    });

    $('#back_to_closed_list').click(function (event) {
        event.preventDefault();
        $('#closed_main_container,.back_to_closed_list').hide();
        $('#closed_list').show();
        getClosedListTable();
    })
    $('#closed_remark_model').on('hidden.bs.modal', function () {
        $('#closed_remark_form')[0].reset();
    });

    $('#submit_closed_remark').click(function (event) {
        event.preventDefault();
        if (validate()) {
            let cus_profile_id = $('#cus_profile_id').val();
            let sub_status = $('#sub_status').val();
            let remark = $('#remark').val();
            $.post('api/closed_files/closed_submit.php', { sub_status, remark, cus_profile_id }, function (response) {
                if (response == '1') {
                    swalSuccess('Success', 'Closed Info Updated Successfully!');
                    $('#closed_remark_form input').val('');
                    $('#closed_remark_form select').val('');
                    $('#closed_remark_form textarea').val('');
                    $('#closed_remark_form input').css('border', '1px solid #cecece');
                    $('#closed_remark_form select').css('border', '1px solid #cecece');
                    $('#closed_remark_model').modal('hide');
                    let cus_id = $('#cus_id').val();
                    getClosedLoanList(cus_id);
                } else {
                    swalError('Error', 'Failed to Closed');
                }
            }, 'json');
        }
    });

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
        var cp_id = $(this).attr('value');
        fineChartList(cp_id)
        $('#fine_model').modal('show');
    });

    $(document).on('click', '.closed-view', function () {

        let id = $(this).attr('value');
        $('#cus_profile_id').val(id)
        $('#closed_remark_model').modal('show');
    });


});


$(function () {
    getClosedListTable();
});

function getClosedListTable() {
    serverSideTable('#closed_list_table', '', 'api/closed_files/close_list_table.php', 'Closed List');
}
function moveToNext(cus_id, cus_sts) {
    $.post('api/closed_files/close_move_to_next.php', { cus_id, cus_sts }, function (response) {
        if (response == '0') {
            let alertName;
            if (cus_sts == '10') {
                alertName = 'Moved To NOC';
            }
            swalSuccess('Success', alertName);
            getClosedListTable();
        } else {
            swalError('Alert', 'Failed To Move');
        }
    }, 'json');
}
function validate() {
    let isValid = true;

    // Validate sub_status
    if (!validateField($('#sub_status').val(), 'sub_status')) {
        isValid = false;
    }

    // Validate cus_profile_id
    if (!validateField($('#cus_profile_id').val(), 'cus_profile_id')) {
        isValid = false;
    }

    return isValid;
}

function getClosedLoanList(cus_id) {
    $.post('api/common_files/closed_loan_list.php', { cus_id }, function (response) {
        var columnMapping = [
            'sno',
            'loan_id',
            'loan_category',
            'loan_date',
            'closed_date',
            'loan_amount',
            'status',
            'sub_status',
            'charts',
            'action'
        ];
        appendDataToTable('#close_loan_table', response, columnMapping);
        setdtable('#close_loan_table', "Closed Loan List");
        //Dropdown in List Screen
        setDropdownScripts();
    }, 'json');
}

function closeChartsModal() {
    $('#due_chart_model').modal('hide');
    $('#penalty_model').modal('hide');
    $('#fine_model').modal('hide');
    $('#closed_remark_model').modal('hide');
    $('#closed_remark_form input').val('');
    $('#closed_remark_form select').val('');
    $('#closed_remark_form textarea').val('');
    $('#closed_remark_form input').css('border', '1px solid #cecece');
    $('#closed_remark_form select').css('border', '1px solid #cecece');

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