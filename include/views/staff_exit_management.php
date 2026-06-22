<div class="row gutters">
    <div class="col-12">

        <div class="card outer_search_card">
            <div class="card-header">
                <div class="card-title"> Search Info </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                        <div class="form-group">
                            <label for="company_search">Company Name</label><span class="text-danger">*</span>
                            <select class="form-control" id="company_search" name="company_search" tabindex="1">
                                <option value="">Select Company Name</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                        <div class="form-group">
                            <label for="branch_search">Branch Name</label><span class="text-danger">*</span>
                            <select class="form-control" id="branch_search" name="branch_search" tabindex="1">
                                <option value="">Select Branch Name</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                        <div class="form-group">
                            <label for="department_search">Department</label><span class="text-danger">*</span>
                            <select class="form-control" id="department_search" name="department_search" tabindex="1">
                                <option value="">Select Department</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                        <button type="button" class="btn btn-primary" id="view_staff" style="margin-top:20px;">Search</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side Buttons -->
        <div class="text-right">
            <button type="button" class="btn btn-primary" id="back_btn" style="display:none;">
                <span class="icon-arrow-left"></span>&nbsp; Back
            </button>
        </div>

        <br>
        <div class="card staff_exit_table_content">
            <div class="card-body">
                <div class="col-12">

                    <table id="staff_exit" class="table custom-table">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Staff ID</th>
                                <th>Staff Name</th>
                                <th>Company</th>
                                <th>Branch</th>
                                <th>Department</th>
                                <th>Team</th>
                                <th>Designation</th>
                                <th>Mobile</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!--Staff Creation Start-->
        <div id="staff_exit_content" style="display: none;">
            <form id="staff_exit_form" name="staff_exit_form" action="" method="post" enctype="multipart/form-data">
                <input type="hidden" id="staff_profile_id">
                <div class="row gutters">
                    <div class="col-12">

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title">General Info</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Fields -->
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="staff_auto_id">Staff ID</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" name="staff_auto_id" id="staff_auto_id" tabindex="1" placeholder="Enter Staff ID" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="staff_name">Staff Name</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control personal_info_disble" id="staff_name" name="staff_name" placeholder="Enter Staff Name" tabindex="2" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="staff_type">Staff Type</label><span class="text-danger">*</span>
                                            <select type="text" class="form-control personal_info_disble" id="staff_type" name="staff_type" tabindex="3" readonly>
                                                <option value="">Select Staff Type</option>
                                                <option value="1">Employer</option>
                                                <option value="2">Employee</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="off_type">Type</label><span class="text-danger">*</span>
                                            <select class="form-control" id="off_type" name="off_type" tabindex="4" readonly>
                                                <option value="">Select Type</option>
                                                <option value="1">Office</option>
                                                <option value="2">Field</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="joining_date">Joining Date</label><span class="text-danger">*</span>
                                            <input type="date" class="form-control personal_info_disble" id="joining_date" name="joining_date" placeholder="Joining Date" tabindex="5" readonly>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title">Occupation Info</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="company_name">Company Name</label><span class="text-danger">*</span>
                                            <select class="form-control" id="company_name" name="company_name" tabindex="1">
                                                <option value="">Select Company Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="branch_name">Branch Name</label><span class="text-danger">*</span>
                                            <select class="form-control" id="branch_name" name="branch_name" tabindex="7">
                                                <option value="">Select Branch Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="department">Department</label><span class="text-danger">*</span>
                                            <select class="form-control" id="department" name="department" tabindex="8">
                                                <option value="">Select Department</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="team">Team</label><span class="text-danger">*</span>
                                            <select class="form-control" id="team" name="team" tabindex="9">
                                                <option value="">Select Team</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="designation">Designation</label><span class="text-danger">*</span>
                                            <select class="form-control" id="designation" name="designation" tabindex="37">
                                                <option value="">Select Designation</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 reporting_person_div" style="display: none;">
                                        <div class="form-group">
                                            <label for="reporting_person">Reporting Person</label><span class="text-danger">*</span>
                                            <select class="form-control" id="reporting_person" name="reporting_person" tabindex="10">
                                                <option value="">Select Reporting Person</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="branch_admin">Branch Admin</label><span class="text-danger">*</span>
                                            <select class="form-control" id="branch_admin" name="branch_admin" tabindex="11">
                                                <option value="">Select Branch Admin</option>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 branch_div" style="display: none;">
                                        <div class="form-group">
                                            <label for="branch">Branch</label><span class="text-danger">*</span>
                                            <select class="form-control" id="branch" name="branch" tabindex="12">
                                                <option value="">Select Branch</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Documents Info
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="doc_info_table" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Document Name</th>
                                                <th>Document Type</th>
                                                <th>Document</th>
                                                <th>Submitted Date</th>
                                                <th>Returned Date</th>
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

                <div class="card mt-3" id="exit_detail_div">
                    <div class="card-header">
                        <h5 class="card-title">Exit Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Fields -->
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="notice_per_served">Notice Period Served</label><span class="text-danger">*</span>
                                    <select class="form-control" id="notice_per_served" name="notice_per_served" tabindex="13">
                                        <option value="">Select Notice Period Served</option>
                                        <option value="1">Yes</option>
                                        <option value="2">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 notice-div" style="display:none">
                                <div class="form-group">
                                    <label for="notice_period"> Notice Period</label>
                                    <input type="number" class="form-control" id="notice_period" name="notice_period" placeholder="Notice Period" readonly tabindex="14 ">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="last_wrk_day">Last Working Day</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="last_wrk_day" name="last_wrk_day" tabindex="15">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="exit_type">Exit Type</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="exit_type" name="exit_type" placeholder="Enter Exit Type" tabindex="16">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="reason">Reason</label><span class="text-danger">*</span>
                                    <textarea class="form-control" name="reason" id="reason" tabindex="17"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!----------------------------- CARD END STAFF EXIT FORM ------------------------------>

                <div class="col-md-12 ">
                    <div class="text-right">
                        <button type="submit" name="submit_staff_exit" id="submit_staff_exit" class="btn btn-primary" value="Submit" tabindex="6"><span class="icon-check"></span>&nbsp;Submit</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>