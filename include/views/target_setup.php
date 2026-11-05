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

        <!--- Target Setup List Start --->
        <div class="text-right addtargetBtn">
            <button type="button" class="btn btn-primary " id="add_target"><span class="fa fa-plus"></span>&nbsp; Add</button>
        </div>
        <br>

        <div class="card target_setup_table_content">
            <div class="card-body">
                <div class="col-12" style="overflow-x: auto;">
                    <table id="target_setup_table" class="table custom-table dtable">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Company Name</th>
                                <th>Branch Name</th>
                                <th>Department</th>
                                <th>Staff Name</th>
                                <th>Effective Month</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--- Target Setup List End --->

        <!--- Target Setup --->

        <div id="target_setup_content" style="display:none;">
            <div class="text-right backBtn">
                <button type="button" class="btn btn-primary backBtn" id="back_btn"><span class="icon-arrow-left"></span>&nbsp; Back </button>
            </div>
            <br>
            <form id="target_setup" name="target_setup" action="" method="post" enctype="multipart/form-data">
                <input type="hidden" id="target_id" value="">
                <div class="row gutters">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Target Setup</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="company_name">Company Name</label><span class="text-danger">*</span>
                                            <select type="text" class="form-control" id="company_name" name="company_name" tabindex="1">
                                                <option value="">Select Company Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="branch_name">Branch Name</label><span class="text-danger">*</span>
                                              <input type="hidden" id="branch_name2">
                                            <select class="form-control" id="branch_name" name="branch_name" tabindex="2" multiple>
                                                <option value="">Select Branch Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="department_name">Department</label><span class="text-danger">*</span>
                                             <input type="hidden" id="department_name2">
                                            <select class="form-control" id="department_name" name="department_name" tabindex="3" multiple>
                                                <option value="">Select Department</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="effective_month">Effective Month</label><span class="text-danger">*</span>
                                            <input type="month" class="form-control" id="effective_month" name="effective_month" tabindex="4" min="<?php echo date('Y-m'); ?>">
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Staff Info</div>
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
                                                        <th>Action</th>
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

                            <button type="submit" name="submit_target_setup" id="submit_target_setup" class="btn btn-primary" value="Submit" tabindex="15"><span class="icon-check"></span>&nbsp;Submit</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
        <!----------------------------- CARD END  Target Setup FORM------------------------------>
    </div>
</div>