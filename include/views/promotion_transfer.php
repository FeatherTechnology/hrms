<div class="row gutters" >
    <div class="col-12" id="Overall_outer_shell">
        <div class="card outer_search_card">
            <div class="card-header">
                <h5 class="card-title">Staff Selection</h5>
            </div>
            <div class="card-body">

                <div class="row ">

                    <div class="col-xl-1 col-lg-1 col-md-1 col-sm-1 col-12">

                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12">
                        <div class="form-group">
                            <label for="company_search">Company Name</label><span class="text-danger">*</span>
                            <select class="form-control" id="company_search" name="company_search" tabindex="1">
                                <option value="">Select Company Name</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12">
                        <div class="form-group">
                            <label for="department_search">Department</label><span class="text-danger">*</span>
                            <select class="form-control" id="department_search" name="department_search" tabindex="1">
                                <option value="">Select Department</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12">
                        <div class="form-group">
                            <label for="status_search">Status</label><span class="text-danger">*</span>
                            <select class="form-control" id="status_search" name="status_search" tabindex="1">
                                <option value="">Select Status</option>
                                <option value="1">Active</option>
                                <option value="2">Inactive</option>
                            </select>
                        </div>
                    </div>


                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12">
                        <div class="form-group">
                            <label for="staff_search">Staff Name</label><span class="text-danger">*</span>
                            <select class="form-control" id="staff_search" name="staff_search" tabindex="1">
                                <option value="">Select Staff Name</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12">
                        <button type="button" class="btn btn-primary" id="view_staff" style="margin-top:20px;">Search</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 text-right">
            <button class="btn btn-primary" id="back_btn" style="display: none;"><span class="icon-arrow-left"></span> Back</button>
        </div>
        <br>
        <div class="card promotion_transfer_table_content">
            <div class="card-header">
                <h5 class="card-title">Staff Info</h5>
            </div>

            <!-- Center Buttons -->
            <div class="col-md-12 text-center">
                <div class="form-group mb-0 outer_status_card justify-content-center" style="display: none;">
                    <button type="button" class="btn btn-primary staff_status_btn promo_status" data-value="1" style="margin-right: 20px;"> Promotion </button>&nbsp;&nbsp;
                    <button type="button" class="btn btn-primary staff_status_btn trans_status" data-value="2" style="margin-right: 20px;"> Transfer </button> &nbsp;&nbsp;
                    <button type="button" class="btn btn-primary staff_status_btn inc_status" data-value="3" style="margin-right: 20px;"> Increment </button>
                </div>
            </div>
            <div class="card-body">
                <div class="col-12 overflow-x-cls">

                    <table id="promotion_transfer_table" class="table custom-table">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Staff ID</th>
                                <th>Staff Name</th>
                                <th>Joining Date</th>
                                <th>Relieve Date</th>
                                <th>Company</th>
                                <th>Branch</th>
                                <th>Department</th>
                                <th>Team</th>
                                <th>Designation</th>
                                <th>Reporting Person</th>
                                <th>PF Applicable</th>
                                <th>ESI Applicable</th>
                                <th>PT Applicable</th>
                                <th>Total CTC</th>
                                <th>Effective From</th>
                                <th>Process</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!--Staff Creation Start-->
        <div id="promotion_transfer_content" style="display: none;">
            <form id="promotion_transfer_form" name="promotion_transfer_form" action="" method="post" enctype="multipart/form-data">
                <input type="hidden" id="staff_profile_id">
                <input type="hidden" id="annual_ctc">
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
                                            <label for="effective_date">Effective From</label><span class="text-danger">*</span>
                                            <input type="date" class="form-control" id="effective_date" name="effective_date" tabindex="15">
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


                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title">CTC Info</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Fields -->
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="pf_available">PF Available</label><span class="text-danger">*</span>
                                    <select class="form-control" id="pf_available" name="pf_available" tabindex="16">
                                        <option value="">Select PF Availability</option>
                                        <option value="1">Yes</option>
                                        <option value="2">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="esi_available">ESI Available</label><span class="text-danger">*</span>
                                    <select class="form-control" id="esi_available" name="esi_available" tabindex="17">
                                        <option value="">Select ESI Availability</option>
                                        <option value="1">Yes</option>
                                        <option value="2">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="pt_available">PT Available</label><span class="text-danger">*</span>
                                    <select class="form-control" id="pt_available" name="pt_available" tabindex="18">
                                        <option value="">Select PT Availability</option>
                                        <option value="1">Yes</option>
                                        <option value="2">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="total_ctc">Total CTC</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="total_ctc" name="total_ctc" placeholder="Enter Total CTC" tabindex="41">
                                </div>
                            </div>



                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="ctc_info_table" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Salary Component</th>
                                                <th>Component Classification</th>
                                                <th>Component Category</th>
                                                <th>CTC Amount</th>
                                                <th>CTC Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4" style="text-align:right">Total</th>
                                                <th>
                                                    <input type="text" id="total_ctc_amount" class="form-control" readonly>
                                                </th>
                                                <th>
                                                    <input type="text" id="total_ctc_percentage" class="form-control" readonly>
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 ">
                    <div class="text-right">

                        <button type="submit" name="submit_staff_data" id="submit_staff_data" class="btn btn-primary" value="Submit" tabindex="6"><span class="icon-check"></span>&nbsp;Submit</button>
                    </div>
                </div>

        </div>
    </div>

</div>
</form>
</div>
<!----------------------------- CARD END  STAFF EXIT FORM------------------------------>

</div>
</div>