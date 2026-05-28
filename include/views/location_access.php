<div class="row gutters">
    <div class="col-12">
        <div class="location_search">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">General Info</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                            <div class="form-group">
                                <label for="company_name">Company Name</label><span class="text-danger">*</span>
                                <select class="form-control" id="company_name" name="company_name" tabindex="6">
                                    <option value="">Select Company Name</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                            <div class="form-group">
                                <label for="branch_name_one">Branch Name</label><span class="text-danger">*</span>
                                <select class="form-control" id="branch_name_one" name="branch_name_one" tabindex="6">
                                    <option value="">Select Branch Name</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                            <div class="form-group">
                                <label for="department_name_one">Department Name</label><span class="text-danger">*</span>
                                <select class="form-control" id="department_name_one" name="department_name_one" tabindex="6">
                                    <option value="">Select Department Name</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" style="display: flex; align-items: center;">
                            <button type="button" name="search_location" id="search_location" class="btn btn-primary" tabindex="7"></span>&nbsp;Search</button>
                        </div>
                    </div>
                </div>
            </div>

            <!------------------------------------------------------------ Location Access Table Start ---------------------------------------------------------------------->

            <div class="card location_table_content">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12" style="overflow-x: auto;">
                            <table id="location_creation_table" class="table custom-table">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Staff ID</th>
                                        <th>Staff Name</th>
                                        <th>Department Name</th>
                                        <th>Default Branch</th>
                                        <th>Assigned Branch</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Latitude / Longitude</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody> </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!------------------------------------------------------------ Location Access Table End ---------------------------------------------------------------------->

        <!------------------------------------------------------------- Staff Information Card Start ------------------------------------------------------------------->

        <div class="card staff_information" style="display: none;">
            <div class="text-right" id="backBtnContainer">
                <button type="button" class="btn btn-primary backBtn" id="back_btn"><span class="icon-arrow-left"></span>&nbsp; Back </button>
            </div>
            <div class="card-header">
                <div class="card-title">Staff Information</div>
            </div>
            <div class="card-body">
                <form id="location_form">
                    <input type="hidden" id="staff_info_id" value="">
                    <input type="hidden" id="location_access_id" value="">
                    <input type="hidden" id="staff_profile_id">
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                            <div class="form-group">
                                <label for="branch_name_two"> Branch Name </label>
                                <input type="text" class="form-control" id="branch_name_two" name="branch_name_two" readonly tabindex="10">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                            <div class="form-group">
                                <label for="department_name_two"> Department Name </label>
                                <input type="text" class="form-control" id="department_name_two" name="department_name_two" readonly tabindex="10">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                            <div class="form-group">
                                <label for="staff_name"> Staff Name </label>
                                <input type="text" class="form-control" id="staff_name" name="staff_name" readonly tabindex="10">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                            <div class="form-group">
                                <label for="staff_id"> Staff ID </label>
                                <input type="text" class="form-control" id="staff_id" name="staff_id" readonly tabindex="10">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                            <div class="form-group">
                                <label for="from_date"> From Date </label><span class="text-danger">*</span>
                                <input type="date" class="form-control" id="from_date" name="from_date" tabindex="10">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                            <div class="form-group">
                                <label for="to_date"> To Date </label><span class="text-danger">*</span>
                                <input type="date" class="form-control" id="to_date" name="to_date" tabindex="10">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                            <div class="form-group">
                                <label for="branch_name_three">Branch Name</label><span class="text-danger">*</span>
                                <select class="form-control" id="branch_name_three" name="branch_name_three" tabindex="6">
                                    <option value="">Select Branch Name</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                            <div class="form-group">
                                <label for="branch_location"> Branch Location </label>
                                <input type="text" class="form-control" id="branch_location" name="branch_location" readonly tabindex="10">
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                            <div class="form-group">
                                <label for="reason"> Reason </label>
                                <textarea type="textarea" class="form-control" id="reason" name="reason" tabindex="10"> </textarea>
                            </div>
                        </div>
                        <div class="col-md-12 ">
                            <div class="text-right">
                                <button type="submit" name="submit_location_access" id="submit_location_access" class="btn btn-primary" value="Submit" tabindex="14"><span class="icon-check"></span>&nbsp;Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="form-group">
                            <table id="location_mapping_table" class="table custom-table">
                                <thead>
                                    <tr>
                                        <th width="20">S.NO</th>
                                        <th>Default Branch</th>
                                        <th>Assigned Branch</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Latitude / Longitude</th>
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

        <!------------------------------------------------------------------- Staff Information Card End ---------------------------------------------------------------->

    </div>
</div>