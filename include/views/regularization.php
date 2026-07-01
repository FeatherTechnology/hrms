<div class="row gutters">
    <div class="col-12">
        <div class="regularization_list">
            <div class="toggle-container col-12" style="display: flex;justify-content:right;align-items:center">
                <button class="btn btn-primary add_reg"><span class="icon-add"></span> Add Regularization</button>
            </div> <br> <br>
            <div class="radio-container" style="margin-top:20px;">
                <div class="selector">
                    <div class="selector-item" id="request_div">
                        <input type="radio" id="pending" name="regularization_type" class="selector-item_radio" value="Request">
                        <label for="pending" class="selector-item_label">My Requests</label>
                    </div>
                    <div class="selector-item" id="approval_div">
                        <input type="radio" id="approved" name="regularization_type" class="selector-item_radio" value="Approval">
                        <label for="approved" class="selector-item_label">My Approvals</label>
                    </div>
                </div>
            </div>
            <br> <br>

            <div class="card">
                <div class="card-body">
                    <div id="regularization_table_div" class="table-divs" style="overflow-x: auto;">
                        <table id="regularization_table" class="table custom-table">
                            <thead>
                                <th>S.No</th>
                                <th>Staff ID</th>
                                <th>Staff Name</th>
                                <th>Company</th>
                                <th>Branch</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Team</th>
                                <th>Request Date</th>
                                <th>Request Type</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>No. of Days/Hrs</th>
                                <th>Status</th>
                                <th>Action</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- request div start-->
        <div class="staff_info_div" style="display: none;">
            <div class="toggle-container col-12" style="display: flex;justify-content:right;align-items:center;margin:10px">
                <button type="button" class="btn btn-primary" id="back_btn" style="display: none;"><span class="icon-arrow-left"></span>&nbsp; Back </button>
            </div>
            <div class="card">

                <div class="card-header">
                    <h5 class="card-title">Staff Info</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Fields -->
                        <input type="hidden" name="stf_prf_id" id="stf_prf_id" value="" />
                        <input type="hidden" name="cmpy_id" id="cmpy_id" value="" />
                        <input type="hidden" name="branch_id" id="branch_id" value="" />
                        <input type="hidden" name="dep_id" id="dep_id" value="" />
                        <input type="hidden" name="des_id" id="des_id" value="" />
                        <input type="hidden" name="team_id" id="team_id" value="" />
                        <input type="hidden" name="hidden_id" id="hidden_id" value="" />
                        <input type="hidden" name="staff_type" id="staff_type" value="" />

                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="staff_id">Staff ID</label>
                                <input type="text" class="form-control" id="staff_id" name="staff_id" placeholder="Company Name" tabindex="1" readonly>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="staff_name">Staff Name</label>
                                <input type="text" class="form-control" id="staff_name" name="staff_name" placeholder="Staff Name" tabindex="2" readonly>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="cmpy_name">Company Name</label>

                                <input type="text" class="form-control" id="cmpy_name" name="cmpy_name" placeholder="Company Name" tabindex="3" readonly>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="branch_name">Branch Name</label>

                                <input type="text" class="form-control" id="branch_name" name="branch_name" placeholder="Branch_name" tabindex="4" readonly>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="department">Department</label>
                                <input type="text" class="form-control" id="department" name="department" placeholder="Department" tabindex="5" readonly>

                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="designation">Designation</label>
                                <input type="text" class="form-control" id="designation" name="designation" placeholder="Designation" tabindex="6" readonly>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="team">Team</label>
                                <input type="text" class="form-control" id="team" name="team" placeholder="Team" tabindex="7" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card req_div">
                <div class="card-header">
                    <h5 class="card-title">Request Details</h5>
                </div>
                <div class="card-body">
                    <form id="request_form">
                        <div class="row">
                            <input type="hidden" name="leave_type_id" id="leave_type_id" value="" />
                            <input type="hidden" name="total_min" id="total_min" value="" />

                            <!-- Fields -->
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="req_type">Request Type</label><span class="text-danger">*</span>
                                    <select class="form-control" id="req_type" name="req_type" tabindex="8">
                                        <option value="">Select Request Type</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 Lev_per" style="display: none;">
                                <div class="form-group">
                                    <label for="leave_period">Leave Period</label><span class="text-danger">*</span>
                                    <select class="form-control" id="leave_period" name="leave_period" tabindex="9">
                                        <option value="">Select Leve Period</option>
                                        <option value="1">First Half</option>
                                        <option value="2">Second Half</option>
                                        <option value="3">Full Day</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 leveType" style="display: none;">
                                <div class="form-group">
                                    <label for="leave_type">Leave Type</label><span class="text-danger">*</span>
                                    <select class="form-control" id="leave_type" name="leave_type" tabindex="9">
                                        <option value="">Select Leve Type</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="from_date">From Date</label><span class="text-danger">*</span>
                                    <input type="datetime-local" class="form-control" id="from_date" name="from_date" placeholder="Enter From Date" tabindex="12">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="to_date">To Date</label><span class="text-danger">*</span>
                                    <input type="datetime-local" class="form-control" id="to_date" name="to_date" placeholder="Enter To Date" tabindex="13">
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="total_days">Total Days/Hrs</label><span class="text-danger">*</span>
                                    <div id="total_days" class="form-control" style="height: 35px;background-color: #d3d2d2;"></div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 ot_req" style="display: none;">
                                <div class="form-group">
                                    <label for="current_month_ot_count">Current Month OT Count</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="current_month_ot_count" name="current_month_ot_count" tabindex="10" readonly>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 bal_req" style="display: none;">
                                <div class="form-group">
                                    <label for="balance_req">Balance Request</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="balance_req" name="balance_req" placeholder="Balance Request" tabindex="10" readonly>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="req_date">Request Date</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="req_date" name="req_date" placeholder="Request Date" tabindex="11" readonly>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="reason">Reason</label><span class="text-danger">*</span>
                                    <textarea type="textarea" class="form-control" id="reason" name="reason" placeholder="Enter Reason" tabindex="15"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- request div end -->

            <!-- approval div -->
            <div class="approval_div" style="display: none;">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Approval</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Fields -->
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="approval_type">Approval</label><span class="text-danger">*</span>
                                    <select class="form-control" id="approval_type" name="approval_type" tabindex="16">
                                        <option value="0">Select Approval Type</option>
                                        <option value="1">Approved</option>
                                        <option value="2">Cancel</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="remarks">Remarks</label><span class="text-danger">*</span>
                                    <textarea type="textarea" class="form-control" id="remarks" name="remarks" placeholder="Enter Remarks" tabindex="20"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- approval div end -->
            <div class="col-md-12 ">
                <div class="text-right">
                    <button type="submit" name="submit_regularization" id="submit_regularization" class="btn btn-primary" value="Submit" tabindex="21">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>