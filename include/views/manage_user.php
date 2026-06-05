<div class="row gutters">
    <div class="radio-container col-12" style="margin-top: 10px;">
        <div class="selector">
            <div class="selector-item">
                <input type="radio" id="active_list" name="outer_list" class="selector-item_radio" value="active" checked>
                <label for="active_list" class="selector-item_label">Active List</label>
            </div>
            <div class="selector-item">
                <input type="radio" id="inactive_list" name="outer_list" class="selector-item_radio" value="inactive">
                <label for="inactive_list" class="selector-item_label">Inactive List</label>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="col-12 text-right">
            <button class="btn btn-primary add_user_btn"><span class="icon-add"></span>Add User Creation</button>
            <button class="btn btn-primary back_to_userList_btn" style="display: none;"><span class="icon-arrow-left"></span>Back</button>
        </div></br>
        <!----------------------------- CARD START  USER CREATION TABLE ------------------------------>
        <div class="card user_creation_table_content">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <table id="user_creation_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Company Name</th>
                                    <th>Role</th>
                                    <th>Staff Name</th>
                                    <th>User ID</th>
                                    <th>Branch Name</th>
                                    <th>Department Name</th>
                                    <th>Team Name</th>
                                    <th>Designation</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!----------------------------- CARD END  USER CREATION TABLE ------------------------------>

        <!----------------------------- CARD START  USER CREATION FORM ------------------------------>
        <div id="user_creation_content" style="display: none;">
            <form id="user_creation_form" name="user_creation_form" method="post" enctype="multipart/form-data">
                <input type="hidden" id="user_creation_id" value="0">
                <input type="hidden" id="session_user_id">
                <!-- Row start -->
                <div class="row gutters">
                    <div class="col-12">
                        <!--- ---------------------- User Info  START----------------------------- -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">User Info</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="user_code">User ID</label>
                                            <input type="text" class="form-control" id="user_code" name="user_code" tabindex="2" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="company_name">Company Name</label><span class="text-danger">*</span>
                                            <select class="form-control" id="company_name" name="company_name" tabindex="6">
                                                <option value="">Select Company Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="role">Role</label><span class="text-danger">*</span>
                                            <select class="form-control" id="role" name="role" tabindex="3">
                                                <option value="">Select Role</option>
                                                <option value="1">Employer</option>
                                                <option value="2">Employee</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="staff_name">Staff Name</label><span class="text-danger">*</span>
                                            <select class="form-control" id="staff_name" name="staff_name" tabindex="6">
                                                <option value="">Select Staff Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="staff_id">Staff ID</label>
                                            <input type="text" class="form-control" id="staff_id" name="staff_id" tabindex="7" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="branch">Branch</label>
                                            <input type="text" class="form-control" id="branch" name="branch" tabindex="7" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="department">Department</label>
                                            <input type="text" class="form-control" id="department" name="department" tabindex="7" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="team">Team</label>
                                            <input type="text" class="form-control" id="team" name="team" tabindex="7" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="designation">Designation</label>
                                            <input type="text" class="form-control" id="designation" name="designation" tabindex="7" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--- ---------------------- User Info  END ----------------------------- -->

                        <!--- ---------------------- Credential Info START  ----------------------------- -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Credential Info</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="user_name">User Name</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="user_name" name="user_name" tabindex="11" placeholder="Enter User Name">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="password">Password</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="password" name="password" tabindex="12" placeholder="Enter Password">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="confirm_password">Confirm Password</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="confirm_password" name="confirm_password" tabindex="13" placeholder="Enter Confirm Password">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="download_access">Download Access</label><span class="text-danger">*</span>
                                            <select class="form-control" id="download_access" name="download_access" tabindex="18">
                                                <option value="">Select Download Access</option>
                                                <option value="1">YES</option>
                                                <option value="2">NO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="report_access">Report Access</label><span class="text-danger">*</span>
                                            <select class="form-control" id="report_access" name="report_access" tabindex="18">
                                                <option value="">Select Report Access</option>
                                                <option value="1">Over All</option>
                                                <option value="2">Individual</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="home_access">Home Access</label><span class="text-danger">*</span>
                                            <select class="form-control" id="home_access" name="home_access" tabindex="18">
                                                <option value="">Select Home Access</option>
                                                <option value="1">YES</option>
                                                <option value="2">NO</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--- ---------------------- Credential Info END  ----------------------------- -->

                        <!--- ---------------------- Screen Mapping START  ----------------------------- -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Screen Mapping <span class="text-danger">*</span></h5>
                            </div>
                            <div class="card-body" id="dynamic-menus">

                            </div>
                        </div>
                        <!--- ---------------------- Screen Mapping END  ----------------------------- -->

                        <div class="col-12 mt-3 text-right">
                            <button name="submit_user_creation" id="submit_user_creation" class="btn btn-primary" tabindex="51"><span class="icon-check"></span>&nbsp;Submit</button>
                            <button type="reset" class="btn btn-outline-secondary" id="reset_btn" tabindex="52">Clear</button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
        <!----------------------------- CARD END  USER CREATION FORM------------------------------>
    </div>
</div>