<!--Statutory Compliance Creation List Start-->
<div class="col-12">
    <div class="text-right">
        <button type="button" class="btn btn-primary " id="add_statutory_compliance" tabindex="8"><span class="fa fa-plus"></span>&nbsp; Add Statutory Compliance</button>
        <button type="button" class="btn btn-primary" id="back_btn" tabindex="9" style="display: none;"><span class="icon-arrow-left"></span>&nbsp; Back </button>
    </div>
    <br>
    <div class="card statutory_compliance_table_content">
        <div class="card-body">
            <div class="col-12">
                <table id="statutory_compliance_table" class="table custom-table">
                    <thead>
                        <tr>
                            <th>S.NO</th>
                            <th>Company Name</th>
                            <th>State</th>
                            <th>PF Applicable</th>
                            <th>ESI Applicable</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--Statutory Compliance Creation List End-->
    <!--Statutory Compliance Creation Start-->
    <div id="statutory_compliance_creation_content" style="display:none;">
        <form id="statutory_compliance_creation" name="statutory_compliance_creation" action="" method="post" enctype="multipart/form-data">
            <input type="hidden" id="statutory_compliance_id">
            <div class="row gutters">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Statutory Compliance Info</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
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
                                        <label for="state">State</label><span class="text-danger">*</span>
                                        <select class="form-control" id="state" name="state" tabindex="5">
                                            <option value="">Select State</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">PF Components Info</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="pf_applicable">PF Applicable</label> <span class="text-danger">*</span>
                                        <select class="form-control" id="pf_applicable" name="pf_applicable" tabindex="6">
                                            <option value="">Select PF Applicable</option>
                                            <option value="1">Yes</option>
                                            <option value="2">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 pf_apply">
                                    <div class="form-group">
                                        <label for="pf_number">PF Number</label>
                                        <input type="number" class="form-control" id="pf_number" name="pf_number" placeholder="Enter PF Number" tabindex="8">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 pf_apply">
                                    <div class="form-group">
                                        <label for="employee_contribution">Employee Contribution</label> <span class="text-danger">(%)</span>
                                        <input type="number" class="form-control" id="employee_contribution" name="employee_contribution" placeholder="Enter Employee Contribution" tabindex="8">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 pf_apply">
                                    <div class="form-group">
                                        <label for="employer_contribution">Employer Contribution</label><span class="text-danger">(%)</span>
                                        <input type="number" class="form-control" id="employer_contribution" name="employer_contribution" placeholder="Enter Employer Contribution" tabindex="8">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 pf_apply">
                                    <div class="form-group">
                                        <label for="admin_charge">Admin Charge</label> <span class="text-danger">(%)</span>
                                        <input type="number" class="form-control" id="admin_charge" name="admin_charge" placeholder="Enter Admin Charge" tabindex="8">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 pf_apply">
                                    <div class="form-group">
                                        <label for="pension">Pension</label> <span class="text-danger">(%)</span>
                                        <input type="number" class="form-control" id="pension" name="pension" placeholder="Enter Pension" tabindex="8">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 pf_apply">
                                    <div class="form-group">
                                        <label for="apply_wage_limit">Apply Wage Limit</label>
                                        <select class="form-control" id="apply_wage_limit" name="apply_wage_limit" tabindex="6">
                                            <option value="">Select Apply Wage Limit</option>
                                            <option value="1">Yes</option>
                                            <option value="2">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12" id="pf_wage_div" style="display:none;">
                                    <div class="form-group">
                                        <label for="pf_wage_limit">PF Wage Limit</label>
                                        <input type="number" class="form-control" id="pf_wage_limit" name="pf_wage_limit" placeholder="Enter PF Wage Limit" tabindex="8">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">ESI Components Info</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="esi_applicable">ESI Applicable</label> <span class="text-danger">*</span>
                                        <select class="form-control" id="esi_applicable" name="esi_applicable" tabindex="6">
                                            <option value="">Select ESI Applicable</option>
                                            <option value="1">Yes</option>
                                            <option value="2">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 esi_apply">
                                    <div class="form-group">
                                        <label for="employee_share">Employee Share</label> <span class="text-danger">(%)</span>
                                        <input type="number" class="form-control" id="employee_share" name="employee_share" placeholder="Enter Employee Share" tabindex="8">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 esi_apply">
                                    <div class="form-group">
                                        <label for="employer_share">Employer Share</label> <span class="text-danger">(%)</span>
                                        <input type="number" class="form-control" id="employer_share" name="employer_share" placeholder="Enter Employer Share" tabindex="8">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Professional Tax Info</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="professional_tax_applicable">Professional Tax Applicable</label>
                                        <select class="form-control" id="professional_tax_applicable" name="professional_tax_applicable" tabindex="6">
                                            <option value="">Select Professional Tax Applicable</option>
                                            <option value="1">Yes</option>
                                            <option value="2">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 professional_tax_apply">
                                    <div class="form-group">
                                        <label for="calculation_type">Calculation Type</label>
                                        <select class="form-control" id="calculation_type" name="calculation_type" tabindex="6">
                                            <option value="">Select Calculation Type</option>
                                            <option value="1">Percentage</option>
                                            <option value="2">Slab</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12" id="percentage_div" style="display:none;">
                                    <div class="form-group">
                                        <label for="percentage">Percentage</label> <span class="text-danger">(%)</span>
                                        <input type="number" class="form-control" id="percentage" name="percentage" placeholder="Enter Percentage" tabindex="8">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12" id="slab_div" style="display:none;">
                                    <div class="form-group">
                                        <label for="slab">Slab</label>
                                        <input type="number" class="form-control" id="slab" name="slab" placeholder="Enter Slab" tabindex="8">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 ">
                    <div class="text-right">
                        <button type="submit" name="submit_statutory_compliance" id="submit_statutory_compliance" class="btn btn-primary" value="Submit" tabindex="7"><span class="icon-check"></span>&nbsp;Submit</button>
                        <button type="reset" class="btn btn-outline-secondary" tabindex="8">Clear</button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>