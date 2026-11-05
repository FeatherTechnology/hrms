<style>
#staff_info_table {
    width: 100%;
    min-width: 1550px;
    table-layout: auto;
}

#staff_info_table th,
#staff_info_table td {
    white-space: nowrap;
    vertical-align: middle;
    padding: 10px;
}

#staff_info_table th:nth-child(2),
#staff_info_table td:nth-child(2) {
    min-width: 130px;
    width: 130px;
}

#staff_info_table th:nth-child(8),
#staff_info_table td:nth-child(8) {
    min-width: 150px;
    width: 150px;
}

#staff_info_table .staff_id,
#staff_info_table .payout_type {
    width: 100%;
    min-width: 120px;
    height: 38px;
}

#staff_info_table .form-control {
    font-size: 13px;
}

#staff_info_table td:last-child {
    min-width: 85px;
}
</style>
<div class="row gutters">
    <div class="col-12">

        <!--- Target Incentive List Start --->
        <br>

        <div class="card target_incentive_table_content">
            <div class="card-body">
                <div class="col-12" style="overflow-x: auto;">
                    <table id="target_incentive_table" class="table custom-table dtable">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Company Name</th>
                                <th>Branch Name</th>
                                <th>Department</th>
                                <th>Staff Name</th>
                                <th>Effective Month</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--- Target Incentive List End --->

        <!--- Target Incentive --->

        <div id="target_incentive_content" style="display:none;">
            <div class="text-right backBtn">
                <button type="button" class="btn btn-primary backBtn" id="back_btn"><span class="icon-arrow-left"></span>&nbsp; Back </button>
            </div>
            <br>
            <form id="target_incentive" name="target_incentive" action="" method="post" enctype="multipart/form-data">
                <input type="hidden" id="target_id" value="">
                <div class="row gutters">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Target Incentive</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12" style="overflow-x: auto;">
                                        <div class="form-group">
                                            <table id="staff_info_table" class="table custom-table">
                                                <thead>
                                                    <tr>
                                                        <th width="20">S.NO</th>
                                                        <th>Staff Name</th>
                                                        <th>Incentive Title</th>
                                                        <th>Target Count</th>
                                                        <th>Target Amount</th>
                                                        <th>Minimum Count</th>
                                                        <th>Minimum Amount</th>
                                                        <th>Incentive Payout</th>
                                                        <th>CTC Percentage</th>
                                                        <th>Fixed Amount</th>
                                                        <th>Achievement Basis</th>
                                                        <th>Achieved value</th>
                                                        <th>Incentive Amount</th>
                                                        <th>Final Incentive</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="col-md-12 ">
                        <div class="text-right">

                            <button type="submit" name="submit_target_incentive" id="submit_target_incentive" class="btn btn-primary" value="Submit" tabindex="15"><span class="icon-check"></span>&nbsp;Submit</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
        <!----------------------------- CARD END  Target Incentive FORM------------------------------>
    </div>
</div>