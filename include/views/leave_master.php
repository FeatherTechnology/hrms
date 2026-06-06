<!-- Leave Master Start-->
<div id="leave_master_content">
    <form id="leave_master_creation" name="leave_master_creation" action="" method="post" enctype="multipart/form-data">
        <div class="row gutters">
            <div class="col-12">
                <!--- Leave Master Info --->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Leave Master Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                                <div class="form-group">
                                    <label for="company_name">Company Name</label><span class="text-danger">*</span>
                                    <select class="form-control" id="company_name" name="company_name" tabindex="1">
                                        <option value="">Select Company Name</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" style="display: flex; align-items: center;">
                                <button type="button" name="search_ctc" id="search_ctc" class="btn btn-primary" tabindex="2"></span>&nbsp;Search</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="leave_master_settings" style="display: none;">
                    <!--- Leave Master Info --->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Permission Policy</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="max_permission">Max Permission Per Month</label>
                                        <input type="number" class="form-control" id="max_permission" name="max_permission" placeholder="Enter Max Permission" tabindex="3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--- Week Off Info --->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                Week Off Info
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width:80px;">S.No</th>
                                            <th>Week Day</th>
                                            <th>Week Off Type</th>
                                        </tr>
                                    </thead>

                                    <tbody id="week_off_table_body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--- Leave Criteria Info --->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Leave Criteria Info
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_leave_info_modal" onclick="getLeaveInfoTable()" style="padding: 5px 35px; float: right;" tabindex='4'><span class="icon-add"></span></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="form-group">
                                        <table id="leave_info_table" class="table custom-table">
                                            <thead>
                                                <tr>
                                                    <th width="20">S.NO</th>
                                                    <th>Leave Type</th>
                                                    <th>No of Days</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--- Shift Timings Info --->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Shift Timings Info <span class="text-danger">*</span>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_shift_info_modal" onclick="getShiftInfoTable()" style="padding: 5px 35px; float: right;" tabindex='5'><span class="icon-add"></span></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="form-group">
                                        <table id="shift_info_table" class="table custom-table">
                                            <thead>
                                                <tr>
                                                    <th width="20">S.NO</th>
                                                    <th>Shift Name</th>
                                                    <th>Start Time</th>
                                                    <th>End Time</th>
                                                    <th>Shift Time</th>
                                                    <th>Grace Time</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--- submit_leave_master --->
                    <div class="col-md-12 ">
                        <div class="text-right">
                            <button type="submit" name="submit_leave_master" id="submit_leave_master" class="btn btn-primary" value="Submit" tabindex="6"><span class="icon-check"></span>&nbsp;Submit</button>
                            <button type="reset" class="btn btn-outline-secondary" tabindex="7">Clear</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>

<!------------------------------------------------------------------ Leave Criteria Info Modal start  ----------------------------------------------------------------------->

<div class="modal fade" id="add_leave_info_modal" tabindex="1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Leave Criteria</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="getLeaveCriteriaTable();" tabindex="1">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="leave_info_form">
                        <div class="row">
                            <input type="hidden" name="leave_criteria_id" id='leave_criteria_id'>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="leave_type">Leave Type</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="leave_type" id="leave_type" tabindex="1" placeholder="Enter Leave Type">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="no_of_days">No of Days</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="no_of_days" id="no_of_days" tabindex="1" placeholder="Enter No of Days">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="" style="visibility:hidden"></label><br>
                                    <button name="submit_leave_criteria" id="submit_leave_criteria" class="btn btn-primary" tabindex="1"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_leave_form" class="btn btn-outline-secondary" tabindex="1">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="leave_creation_table" class="custom-table">
                            <thead>
                                <tr>
                                    <th width="10">S.No.</th>
                                    <th>Leave Type</th>
                                    <th>No of Days</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="1" onclick="getLeaveCriteriaTable()">Close</button>
            </div>
        </div>
    </div>
</div>

<!----------------------------------------------------------------- Leave Criteria Modal End ----------------------------------------------------------------------------->

<!------------------------------------------------------------------ Shift Info Modal start  ----------------------------------------------------------------------->

<div class="modal fade" id="add_shift_info_modal" tabindex="1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Shift Timings</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="getShiftTable();" tabindex="1">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="shift_info_form">
                        <div class="row">
                            <input type="hidden" name="shift_id" id='shift_id'>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="shift_name">Shift Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="shift_name" id="shift_name" tabindex="1" placeholder="Enter Shift Name">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="start_time">Start Time</label><span class="text-danger">*</span>
                                    <input type="time" class="form-control" name="start_time" id="start_time" tabindex="1" placeholder="Enter Start Time">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="end_time">End Time</label><span class="text-danger">*</span>
                                    <input type="time" class="form-control" name="end_time" id="end_time" tabindex="1" placeholder="Enter End Time">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="shift_time">Shift Time</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="shift_time" id="shift_time" tabindex="1" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="grace_time">Grace Time</label><span class="text-danger">*</span> <span class="text-danger">(Minutes)</span>
                                    <input type="number" class="form-control" name="grace_time" id="grace_time" tabindex="1" placeholder="Enter Grace Time">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="" style="visibility:hidden"></label><br>
                                    <button name="submit_shift_info" id="submit_shift_info" class="btn btn-primary" tabindex="1"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_shift_form" class="btn btn-outline-secondary" tabindex="1">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="shift_creation_table" class="custom-table">
                            <thead>
                                <tr>
                                    <th width="10">S.No.</th>
                                    <th>Shift Name</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Shift Time</th>
                                    <th>Grace Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="1" onclick="getShiftTable()">Close</button>
            </div>
        </div>
    </div>
</div>

<!----------------------------------------------------------------- Shift info Modal End ----------------------------------------------------------------------------->