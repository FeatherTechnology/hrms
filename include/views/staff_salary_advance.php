<div class="row gutters">

    <div class="col-12">
        <div class="col-12 text-right">
            <button class="btn btn-primary add_advance_salary"><span class="icon-add"></span> Add Salary Advance</button>
            <button class="btn btn-primary back_to_list" tabindex="8" style="display: none;"><span class="icon-arrow-left"></span> Back</button>
        </div></br>
        <!----------------------------- CARD START TEAM CREATION TABLE ------------------------------>
        <div class="card staff_salary_advance_table_content">
            <div class="card-body">
                <div class="row">
                    <div class="col-12" style="overflow-x: auto;">
                        <table id="staff_salary_advance_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Company Name</th>
                                    <th>Staff Name</th>
                                    <th>Advance Amount</th>
                                    <th>Dedection Month</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!----------------------------- CARD END TEAM CREATION TABLE ------------------------------>

        <!----------------------------- CARD START TEAM CREATION FORM ------------------------------>
        <div id="advance_salary_content" style="display: none;">
            <form id="team_creation" name="team_creation" method="post" enctype="multipart/form-data">
                <input type="hidden" id="advance_salary_id" value="">
                <!-- Row start -->
                <div class="row gutters">
                    <div class="col-12">
                        <div class="card user_info_card">
                            <div class="card-header">
                                <h5 class="card-title">User Info</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 user_div">
                                        <div class="form-group">
                                            <label for="company_name">Company Name</label><span class="text-danger">*</span>
                                            <select class="form-control" id="company_name" name="company_name" tabindex="6">
                                                <option value="">Select Company Name</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 user_div">
                                        <div class="form-group">
                                            <label for="staff_name">Staff Name</label><span class="text-danger">*</span>
                                            <select class="form-control" id="staff_name" name="staff_name" tabindex="6">
                                                <option value="">Select Staff Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4 user_div">
                                        <div class="form-group">
                                            <label for="staff_id">Staff ID</label>
                                            <input type="text" class="form-control" id="staff_id" name="staff_id" tabindex="7" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4 user_div">
                                        <div class="form-group">
                                            <label for="branch">Branch</label>
                                            <input type="text" class="form-control" id="branch" name="branch" tabindex="7" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4 user_div">
                                        <div class="form-group">
                                            <label for="department">Department</label>
                                            <input type="text" class="form-control" id="department" name="department" tabindex="7" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4 user_div">
                                        <div class="form-group">
                                            <label for="team">Team</label>
                                            <input type="text" class="form-control" id="team" name="team" tabindex="7" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4 user_div">
                                        <div class="form-group">
                                            <label for="designation">Designation</label>
                                            <input type="text" class="form-control" id="designation" name="designation" tabindex="7" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card salary_advance_card">
                            <div class="card-header">
                                <h5 class="card-title">Salary Advance</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="advance_amount">Advance Amount</label><span class="text-danger">*</span>
                                            <input type="number" class="form-control" id="advance_amount" name="advance_amount" tabindex="7" oninput="this.value = this.value.replace(/[^0-9]/g, '')" >
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="dedection_month">Dedection Month</label>
                                            <span class="text-danger">*</span>
                                            <input type="month"  class="form-control"  id="dedection_month" name="dedection_month"  tabindex="7"  min="<?php echo date('Y-m'); ?>" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-3 text-right">
                            <button name="submit_staff_salary_advance" id="submit_staff_salary_advance" class="btn btn-primary" tabindex="5"><span class="icon-check"></span>&nbsp;Submit</button>
                            <button type="reset" class="btn btn-outline-secondary" id="reset_btn" tabindex="6">Clear</button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
        <!----------------------------- CARD END TEAM CREATION FORM------------------------------>

    </div>
</div>