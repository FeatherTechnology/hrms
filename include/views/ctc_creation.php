<!--CTC Creation Start-->
<div id="ctc_creation_content">
    <form id="ctc_creation" name="ctc_creation" action="" method="post" enctype="multipart/form-data">
        <input type="hidden" id="ctc_id">
        <div class="row gutters">
            <div class="col-12">

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">CTC Info</div>
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
                            <div class="col-md-3" style="display: flex; align-items: center;">
                                <button type="button" name="search_ctc" id="search_ctc" class="btn btn-primary" tabindex="7"></span>&nbsp;Search</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" id="ctc_settings" style="display: none;">
                    <div class="card-header">
                        <div class="card-title">CTC Settings</div>
                    </div>

                    <div class="card-body">
                        <form id="ctc_settings_form">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="company_names"> Company Name </label>
                                        <input type="text" class="form-control" id="company_names" name="company_names" readonly tabindex="10">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="salary_component">Salary Component</label><span class="text-danger">*</span>
                                        <input type="text" class="form-control" id="salary_component" name="salary_component" placeholder="Enter Salary Component" tabindex="11">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="component_classification">Component Classification</label><span class="text-danger">*</span>
                                        <select class="form-control" name="component_classification" id="component_classification" tabindex="1">
                                            <option value="">Select Component Classification</option>
                                            <option value="1">CTC</option>
                                            <option value="2">NON CTC</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="component_category">Component Category</label><span class="text-danger">*</span>
                                        <select class="form-control" name="component_category" id="component_category" tabindex="1">
                                            <option value="">Select Component Category</option>
                                            <option value="1">Salary</option>
                                            <option value="2">Reimbursement</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="pay_frequency">Pay Frequency</label><span class="text-danger">*</span>
                                        <select class="form-control" name="pay_frequency" id="pay_frequency" tabindex="1">
                                            <option value="">Select Pay Frequency</option>
                                            <option value="1">Per Month</option>
                                            <option value="2">Per Day</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3" style="display: flex; align-items: center;">
                                    <button type="submit" name="submit_ctc_settings_info" id="submit_ctc_settings_info" class="btn btn-primary" value="Add" tabindex="18"> &nbsp;Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="ctc_creation_table" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Salary Component</th>
                                                <th>Component Classification</th>
                                                <th>Component Category</th>
                                                <th>Pay Frequency</th>
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
        </div>
    </form>
</div>