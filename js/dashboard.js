$(document).ready(function () {
    $('#branch_id').change(function () {
        let branch_id = $(this).val();
        $.post('api/dashboard_files/get_branch_lines.php', { branch_id }, function (response) {
            $('#line_id').val(response);
            setTimeout(() => {
                getLoanEntryCounts();
                getApprovalCounts();
                getLoanIssueCounts();
                getCollectionCounts();
                getClosedCounts();
            }, 500);
        }, 'json');
    });

    $('#loan_entry_title').click(function () {
        $('#loan_entry_body').slideToggle();
        setTimeout(() => {
            if ($('#loan_entry_body').is(':visible')) {
                getLoanEntryCounts();
            }
        }, 500);
    });

    $('#approval_title').click(function () {
        $('#approval_body').slideToggle();
        setTimeout(() => {
            if ($('#approval_body').is(':visible')) {
                getApprovalCounts();
            }
        }, 500);
    });

    $('#loan_issue_title').click(function () {
        $('#loan_issue_body').slideToggle();
        setTimeout(() => {
            if ($('#loan_issue_body').is(':visible')) {
                getLoanIssueCounts();
            }
        }, 500);
    });

    $('#collection_title').click(function () {
        $('#collection_body').slideToggle();
        setTimeout(() => {
            if ($('#collection_body').is(':visible')) {
                getCollectionCounts();
            }else{
                $('#total_coll').trigger('click');
            }
        }, 500);
    });

    $('#closed_title').click(function () {
        $('#closed_body').slideToggle();
        setTimeout(() => {
            if ($('#closed_body').is(':visible')) {
                getClosedCounts();
            }
        }, 500);
    });

    $('input[name="coll"]').click(function(){
        let collVal = $(this).val();
        if(collVal =='1'){
            callCollectionChart();
        }else if(collVal =='2'){
            todayCollectionChart();
        }

    });

});

$(function () {
    checkUserScreenAccess();
});

function checkUserScreenAccess() {
    $.post('api/common_files/check_user_screen_access.php', {}, function (response) {

        let screens = response[0].screens.split(','); // Split the comma-separated string into an array

        if (screens.includes('9')) {
            $('.loan-entry-card').show();
        }
        if (screens.includes('10')) {
            $('.approval-card').show();
        }
        if (screens.includes('11')) {
            $('.loan-issue-card').show();
        }
        if (screens.includes('12')) {
            $('.collection-card').show();
        }
        if (screens.includes('13')) {
            $('.closed-card').show();
        }
    }, 'json').then(function () {
        getBranchList();
    });
}


function getBranchList() {
    $.post('api/common_files/get_branch_list.php', function (response) {
        let appendBranchOption = '';
        appendBranchOption += '<option value="">Select Branch</option>';
        appendBranchOption += '<option value="0">All Branch</option>';
        $.each(response, function (index, val) {
            appendBranchOption += '<option value="' + val.id + '">' + val.branch_name + '</option>';
        });
        $('#branch_id').empty().append(appendBranchOption);

    }, 'json');
}

function getLoanEntryCounts() {
    let lineId = $('#line_id').val();
    $.post('api/dashboard_files/loan_entry_details.php', { lineId }, function (response) {
        $('#tot_entry').text(response['total_loan_entry'])
        $('#tot_issued').text(response['total_loan_issued'])
        $('#tot_bal').text(response['total_loan_balance'])
        $('#today_entry').text(response['today_loan_entry'])
        $('#today_issued').text(response['today_loan_issued'])
        $('#today_bal').text(response['today_loan_balance'])
    }, 'json');
}

function getApprovalCounts() {
    let lineId = $('#line_id').val();
    $.post('api/dashboard_files/approval_details.php', { lineId }, function (response) {
        $('#tot_approval').text(response['total_approved'])
        $('#tot_approval_issued').text(response['total_loan_issued'])
        $('#tot_approval_bal').text(response['total_approve_balance'])
        $('#today_approval').text(response['today_approved'])
        $('#today_approval_issued').text(response['today_loan_issued'])
        $('#today_approval_bal').text(response['today_approve_balance'])
    }, 'json');
}

function getLoanIssueCounts() {
    let lineId = $('#line_id').val();
    $.post('api/dashboard_files/loan_issue_details.php', { lineId }, function (response) {
        $('#tot_loan_issue').text(response['total_loan_issue'])
        $('#tot_issue_issued').text(response['total_loan_issued'])
        $('#tot_issue_bal').text(response['total_loan_balance'])
        $('#today_loan_issue').text(response['today_loan_issue'])
        $('#today_issue_issued').text(response['today_loan_issued'])
        $('#today_issue_bal').text(response['today_loan_balance'])
    }, 'json');
}

function getCollectionCounts() {
    let lineId = $('#line_id').val();
    $.post('api/dashboard_files/collection_details.php', { lineId }, function (response) {
        $('#tot_paid').text(response['total_paid'])
        $('#tot_penalty').text(response['total_penalty'])
        $('#tot_fine').text(response['total_fine'])
        $('#today_paid').text(response['today_paid'])
        $('#today_penalty').text(response['today_penalty'])
        $('#today_fine').text(response['today_fine'])
    }, 'json').then(function () {
        // google.charts.load('current', { 'packages': ['bar'] });
        // google.charts.setOnLoadCallback(callCollectionChart);
        google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(callCollectionChart);
    });
}

function getClosedCounts() {
    let lineId = $('#line_id').val();
    $.post('api/dashboard_files/closed_details.php', { lineId }, function (response) {
        $('#tot_closed').text(response['total_in_closed'])
        $('#tot_consider').text(response['total_consider'])
        $('#tot_rejected').text(response['total_rejected'])
        $('#today_closed').text(response['today_in_closed'])
        $('#today_consider').text(response['today_consider'])
        $('#today_rejected').text(response['today_rejected'])
    }, 'json');
}

function callCollectionChart() {
    let lineId = $('#line_id').val();
    $.ajax({
        type: 'POST',
        data: { lineId },
        url: 'api/dashboard_files/collection_points_details.php',
        dataType: 'json',
        success: function (response) {
            let totPaidCnt = parseInt(response['total_paid_current']);
            let totPaid = parseInt(response['current_paid']);
            let totPendingCnt = parseInt(response['total_paid_pending']);
            let totPending = parseInt(response['pending_paid']);
            let totOdCnt = parseInt(response['total_paid_od']);
            let totOd = parseInt(response['od_paid']);
            let paidCnt = parseInt(response['total_penalty_current']);
            let penPaid = parseInt(response['current_penalty']);
            let PendingCnt = parseInt(response['total_penalty_pending']);
            let penPending = parseInt(response['pending_penalty']);
            let OdCnt = parseInt(response['total_penalty_od']);
            let penOd = parseInt(response['od_penalty']);
            let finePaidCnt = parseInt(response['total_fine_current']);
            let finePaid = parseInt(response['current_fine']);
            let finePendingCnt = parseInt(response['total_fine_pending']);
            let finePending = parseInt(response['pending_fine']);
            let fineOdCnt = parseInt(response['total_fine_od']);
            let fineOd = parseInt(response['od_fine']);

            getPaidDetails(totPaid, totPaidCnt, totPending, totPendingCnt, totOd, totOdCnt,'Total');
            getPendingDetails(penPaid, paidCnt, penPending, PendingCnt, penOd, OdCnt,'Total');
            getOdDetails(finePaid, finePaidCnt, finePending, finePendingCnt, fineOd, fineOdCnt,'Total');
        }
    });
}

function todayCollectionChart() {
    let lineId = $('#line_id').val();
    $.ajax({
        type: 'POST',
        data: { lineId },
        url: 'api/dashboard_files/collection_today_points.php',
        dataType: 'json',
        success: function (response) {
            let todayPaidCnt = parseInt(response['today_paid_current']);
            let todayPaid = parseInt(response['current_todaypaid']);
            let todayPendingCnt = parseInt(response['today_paid_pending']);
            let todayPending = parseInt(response['pending_todaypaid']);
            let todayOdCnt = parseInt(response['today_paid_od']);
            let todayOd = parseInt(response['od_todaypaid']);
            let paidCnt = parseInt(response['today_penalty_current']);
            let penPaid = parseInt(response['current_todaypenalty']);
            let PendingCnt = parseInt(response['today_penalty_pending']);
            let penPending = parseInt(response['pending_todaypenalty']);
            let OdCnt = parseInt(response['today_penalty_od']);
            let penOd = parseInt(response['od_todaypenalty']);
            let finePaidCnt = parseInt(response['today_fine_current']);
            let finePaid = parseInt(response['current_todayfine']);
            let finePendingCnt = parseInt(response['today_fine_pending']);
            let finePending = parseInt(response['pending_todayfine']);
            let fineOdCnt = parseInt(response['today_fine_od']);
            let fineOd = parseInt(response['od_todayfine']);

            getPaidDetails(todayPaid, todayPaidCnt, todayPending, todayPendingCnt, todayOd, todayOdCnt, 'Today');
            getPendingDetails(penPaid, paidCnt, penPending, PendingCnt, penOd, OdCnt, 'Today');
            getOdDetails(finePaid, finePaidCnt, finePending, finePendingCnt, fineOd, fineOdCnt, 'Today');
        }
    });
}

function getPaidDetails(totPaid, totPaidCnt, totPending, totPendingCnt, totOd, totOdCnt, tit){
    //Total Paid
    var data = google.visualization.arrayToDataTable([
        ['Task', 'Count'],
        ['Current', totPaid],
        ['Current Points', totPaidCnt],
        ['Pending', totPending],
        ['Pending Points', totPendingCnt],
        ['OD', totOd],
        ['OD Points', totOdCnt],
    ]);
    var Options = {
        height: 400,
        title: 'Collection - '+tit +' Paid',
        is3D: true, //3d chart
        // pieHole: 0.4, //donut chart
        pieSliceText: 'value', //to show value instead of percentage
        sliceVisibilityThreshold: 0, //to show task if its count is zero because if value is 0 then  it will not be shown in the donut chart
        colors: ['#02bf33', '#619e71', '#FF69B4', '#f5057d', '#0091D5', '#6dbde3'],
        tooltip: {
            trigger: 'selection',
            text: 'value'
        }, // show only the value in the tooltip
    };
    var chart = new google.visualization.PieChart(document.getElementById('collection_paid'));
    chart.draw(data, Options);
}

function getPendingDetails(penPaid, paidCnt, penPending, PendingCnt, penOd, OdCnt, tit){
    //Total Pending
    var data = google.visualization.arrayToDataTable([
        ['Task', 'Count'],
        ['Current', penPaid],
        ['Current Points', paidCnt],
        ['Pending', penPending],
        ['Pending Points', PendingCnt],
        ['OD', penOd],
        ['OD Points', OdCnt],
    ]);
    var Options = {
        height: 400,
        title: 'Collection - '+tit +' Pending',
        is3D: true, //3d chart
        // pieHole: 0.4, //donut chart
        pieSliceText: 'value', //to show value instead of percentage
        sliceVisibilityThreshold: 0, //to show task if its count is zero because if value is 0 then  it will not be shown in the donut chart
        colors: ['#02bf33', '#619e71', '#FF69B4', '#f5057d', '#0091D5', '#6dbde3'],
        tooltip: {
            trigger: 'selection',
            text: 'value'
        }, // show only the value in the tooltip
    };
    var chart = new google.visualization.PieChart(document.getElementById('collection_pending'));
    chart.draw(data, Options);
}

function getOdDetails(finePaid, finePaidCnt, finePending, finePendingCnt, fineOd, fineOdCnt, tit){
    //Total OD
    var data = google.visualization.arrayToDataTable([
        ['Task', 'Count'],
        ['Current', finePaid],
        ['Current Points', finePaidCnt],
        ['Pending', finePending],
        ['Pending Points', finePendingCnt],
        ['OD', fineOd],
        ['OD Points', fineOdCnt],
    ]);
    var Options = {
        height: 400,
        title: 'Collection - '+tit +' OD',
        is3D: true, //3d chart
        // pieHole: 0.4, //donut chart
        pieSliceText: 'value', //to show value instead of percentage
        sliceVisibilityThreshold: 0, //to show task if its count is zero because if value is 0 then  it will not be shown in the donut chart
        colors: ['#02bf33', '#619e71', '#FF69B4', '#f5057d', '#0091D5', '#6dbde3'],
        tooltip: {
            trigger: 'selection',
            text: 'value'
        }, // show only the value in the tooltip
    };
    var chart = new google.visualization.PieChart(document.getElementById('collection_od'));
    chart.draw(data, Options);
}