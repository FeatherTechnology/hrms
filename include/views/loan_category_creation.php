<div class="row gutters">
    <div class="col-12">
        <div class="col-12 text-right">
            <button class="btn btn-primary add_loancategory_btn"><span class="icon-add"></span> Add Loan Category</button>
            <button class="btn btn-primary back_to_loancategory_btn" style="display: none;"><span class="icon-arrow-left"></span> Back</button>
        </div></br>
        <!----------------------------- CARD START  LOAN CATEGORY CREATION TABLE ------------------------------>
        <div class="card wow loan_category_table_content">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <table id="loancategory_creation_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th width="100">S.No.</th>
                                    <th>Loan Category</th>
                                    <th>Loan Limit</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!----------------------------- CARD END  LOAN CATEGORY CREATION TABLE ------------------------------>


        <!----------------------------- CARD START  LOAN CATEGORY CREATION FORM ------------------------------>
        <div id="loan_category_creation_content" style="display: none;">
            <form id="loan_category_creation" name="loan_category_creation" method="post" enctype="multipart/form-data">
                <input type="hidden" id="loan_cat_creation_id" value="">
                <!-- Row start -->
                <div class="row gutters">
                    <div class="col-12">
                        <!--- ---------------------- Loan Category Creation  START----------------------------- -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Loan Category Creation</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Fields -->
                                    <div class="col-lg-2 col-md-2">
                                        <div class="form-group">
                                            <label for="loan_category">Loan Category</label><span class="text-danger">*</span>
                                            <input type="hidden" id="loan_category2">
                                            <select class="form-control" id="loan_category" name="loan_category" tabindex="1">
                                                <option value="">Select Loan Category</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 col-md-2" style="margin-top: 18px;">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary modalBtnCss" data-toggle="modal" data-target="#add_loan_category_modal" tabindex="2" onclick="getLoanCategoryTable()"><span class="icon-add"></span></button>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4">
                                        <div class="form-group">
                                            <label for="loan_limit">Loan Limit</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="loan_limit" name="loan_limit" tabindex="3" placeholder="Enter Loan Limit">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--- ---------------------- Loan Category Creation  END ----------------------------- -->

                        <!--- ---------------------- Loan Calculation START  ----------------------------- -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Loan Calculation</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Fields -->
                                    <div class="col-md-3 col-sm-4">
                                        <div class="form-group">
                                            <label for="due_method">Due Method</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="due_method" name="due_method" tabindex="4" value="Monthly" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-4">
                                        <div class="form-group">
                                            <label for="due_type">Due Type</label><span class="text-danger">*</span>
                                            <select class="form-control" id="due_type" name="due_type" tabindex="1">
                                                <option value="">Select Due Type</option>
                                                <option value="emi">EMI</option>
                                                <option value="interest">Interest</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-4 interest_type" style="display: none;">
                                        <div class="form-group">
                                            <label for="interest_calculate">Interest Calculate</label><span class="text-danger">*</span>
                                            <select class="form-control" id="interest_calculate" name="interest_calculate" tabindex="9">
                                                <option value="">Select Interest Calculate</option>
                                                <option value="1">Month</option>
                                                <option value="2">Days</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-header">
                                <h5 class="card-title">Condition Info</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Fields -->
                                    <div class="col-md-3 col-sm-4">
                                        <div class="form-group">
                                            <label for="interest_rate_min">Interest Rate</label><span class="text-danger">*</span>
                                            <div class="input-group">
                                                <input type="number" class="form-control interest_minmax" id="interest_rate_min" name="interest_rate_min" tabindex="6" placeholder="Enter Min Interest Rate">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-4">
                                        <div class="form-group">
                                            <label for="interest_rate_max"> </label>
                                            <div class="input-group">
                                                <input type="number" class="form-control form-group-label-emptywith-input interest_minmax" id="interest_rate_max" name="interest_rate_max" tabindex="7" placeholder="Enter Max Interest Rate">
                                                <div class="input-group-append form-group-label-emptywith-input">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 col-sm-4">
                                        <div class="form-group">
                                            <label for="due_period_min">Due Period</label><span class="text-danger">*</span>
                                            <div class="input-group">
                                                <input type="number" class="form-control due_period_minmax" id="due_period_min" name="due_period_min" tabindex="8" placeholder="Enter Min Due Period">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-4">
                                        <div class="form-group">
                                            <label for="due_period_max"> </label>
                                            <div class="input-group">
                                                <input type="number" class="form-control form-group-label-emptywith-input due_period_minmax" id="due_period_max" name="due_period_max" tabindex="9" placeholder="Enter Max Due Period">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 col-sm-4">
                                        <div class="form-group">
                                            <label for="doc_charge_min">Document Charge</label><span class="text-danger">*</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <div class="interest_type_options" style="margin-top:-25px; margin-left:140px; display: none;">
                                                <input type="hidden" id="document_charge" value="percentage">
                                                <input type="radio" name="doc_charge_type" id="docpercentage" value="percentage" tabindex="13" checked>
                                                <label for="docpercentage">&nbsp;&nbsp;<b>%</b></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                <input type="radio" name="doc_charge_type" value="rupee" id="docamt" tabindex="14">
                                                <label for="docamt">&nbsp;&nbsp;₹</label>
                                            </div>

                                            <div class="input-group">
                                                <input type="number" class="form-control doc_charge_minmax" id="doc_charge_min" name="doc_charge_min" tabindex="10" placeholder="Enter Min Document Charge">
                                                <div class="input-group-append">
                                                    <span class="input-group-text document-span-val">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-4">
                                        <div class="form-group">
                                            <label for="doc_charge_max"> </label>
                                            <div class="input-group">
                                                <input type="number" class="form-control form-group-label-emptywith-input doc_charge_minmax" id="doc_charge_max" name="doc_charge_max" tabindex="11" placeholder="Enter Max Document Charge">
                                                <div class="input-group-append form-group-label-emptywith-input">
                                                    <span class="input-group-text document-span-val">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 col-sm-4">
                                        <div class="form-group">
                                            <label for="processing_fee_min">Processing Fee</label><span class="text-danger">*</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <div class="interest_type_options" style="margin-top:-25px; margin-left:125px; display: none;">
                                                <input type="hidden" id="processing_type" value="percentage">
                                                <input type="radio" name="process_fee_type" id="propercentage" value="percentage" tabindex="17" checked style="margin-left:15px;">
                                                <label for="propercentage">&nbsp;&nbsp;<b>%</b></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                <input type="radio" name="process_fee_type" value="rupee" id="procamt" tabindex="18">
                                                <label for="procamt">&nbsp;&nbsp;₹</label>
                                            </div>

                                            <div class="input-group">
                                                <input type="number" class="form-control processing_minmax" id="processing_fee_min" name="processing_fee_min" tabindex="12" placeholder="Enter Min Processing Fee">
                                                <div class="input-group-append">
                                                    <span class="input-group-text process-span-val">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-4">
                                        <div class="form-group">
                                            <label for="processing_fee_max"> </label>
                                            <div class="input-group">
                                                <input type="number" class="form-control form-group-label-emptywith-input processing_minmax" id="processing_fee_max" name="processing_fee_max" tabindex="13" placeholder="Enter Max Processing Fee">
                                                <div class="input-group-append form-group-label-emptywith-input">
                                                    <span class="input-group-text process-span-val">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 col-sm-4">
                                        <div class="form-group">
                                            <label for="overdue_penalty">Overdue Penalty</label><span class="text-danger">*</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <div class="interest_type_options" style="margin-top:-25px; margin-left:130px; display: none;">
                                                <input type="hidden" id="overdue_type" value="percentage">
                                                <input type="radio" name="over_due_type" id="overpercentage" value="percentage" tabindex="21" checked style="margin-left:10px;">
                                                <label for="overpercentage">&nbsp;&nbsp;<b>%</b></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                                <input type="radio" name="over_due_type" value="rupee" id="overamt">
                                                <label for="overamt">&nbsp;&nbsp;₹</label>
                                            </div>

                                            <div class="input-group">
                                                <input type="number" class="form-control" id="overdue_penalty" name="overdue_penalty" tabindex="14" placeholder="Enter Overdue Penalty">
                                                <div class="input-group-append">
                                                    <span class="input-group-text  overdue-span-val">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--- ---------------------- Loan Calculation END  ----------------------------- -->

                        <!--- ---------------------- Loan scheme  START----------------------------- -->
                        <div class="card interest_scheme">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">Loan Scheme</h5>
                                <button type="button" class="btn btn-primary modalBtnCss card-head-btn" data-toggle="modal" data-target="#add_loan_scheme_modal" tabindex="15" onclick="getSchemeTable()"><span class="icon-add"></span></button>
                            </div>
                            <div class="card-body bdy-cls">
                                <div class="row mb-3">
                                    <!-- Fields -->
                                    <div class="col-md-3 col-sm-4">
                                        <div class="form-group">
                                            <label for="scheme_name">Scheme Name</label><span class="text-danger">*</span>
                                            <input type="hidden" id="scheme_name2">
                                            <select class="form-control" id="scheme_name" name="scheme_name" tabindex="16" multiple>
                                                <option value="">Select Scheme Name</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <table id="loan_scheme_table" class="custom-table">
                                            <thead>
                                                <tr>
                                                    <th width='20'>S.No.</th>
                                                    <th>Scheme</th>
                                                    <th>Due Method</th>
                                                    <th>Profit Method</th>
                                                    <th>Interest Rate</th>
                                                    <th>Due Period</th>
                                                    <th>Document Charge Min</th>
                                                    <th>Document Charge Max</th>
                                                    <th>Processing Fee Min</th>
                                                    <th>Processing Fee Max</th>
                                                    <th>Overdue Penalty</th>
                                                </tr>
                                            </thead>
                                            <tbody> </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--- ---------------------- Loan scheme END ----------------------------- -->

                        <div class="col-12 mt-3 text-right">
                            <button name="submit_loan_category_creation" id="submit_loan_category_creation" class="btn btn-primary" tabindex="17"><span class="icon-check"></span>&nbsp;Submit</button>
                            <button type="reset" id="clear_loan_cat_form" class="btn btn-outline-secondary" tabindex="18">Clear</button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
        <!----------------------------- CARD END  LOAN CATEGORY CREATION FORM------------------------------>

    </div>
</div>

<!-- /////////////////////////////////////////////////////////////////// Loan Category Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="add_loan_category_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Loan Category</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="getLoanCategoryDropdown()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-3 col-md-3 col-lg-3"></div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label for="addloan_category_name">Loan Category</label><span class="text-danger">*</span>
                                <input class="form-control" name="addloan_category_name" id="addloan_category_name" tabindex="2" placeholder="Enter Loan Category">
                                <input type="hidden" id="addloan_category_id" value='0'>
                            </div>
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-4">
                            <div class="form-group">
                                <button name="submit_addloan_category" id="submit_addloan_category" class="btn btn-primary" tabindex="3" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <table id="loan_category_table" class="custom-table">
                            <thead>
                                <tr>
                                    <th width="20">S.No.</th>
                                    <th>Loan Category</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="4" onclick="getLoanCategoryDropdown()">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Loan Category Modal END ////////////////////////////////////////////////////////////////////// -->

<!-- /////////////////////////////////////////////////////////////////// Loan Scheme Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="add_loan_scheme_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Scheme</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="getSchemeDropdown()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form name="add_scheme_details" id="add_scheme_details">
                        <h5 class="card-title">Loan Scheme</h5>
                        <div class="row">
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="add_scheme_name">Scheme Name</label><span class="text-danger">*</span>
                                    <input class="form-control" name="add_scheme_name" id="add_scheme_name" tabindex="2" placeholder="Enter Scheme">
                                    <input type="hidden" id="add_scheme_id" value="0">
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="scheme_due_method">Due Method</label><span class="text-danger">*</span>
                                    <select class="form-control" id="scheme_due_method" name="scheme_due_method" tabindex="3">
                                        <option value="">Select Due Method</option>
                                        <option value="1">Monthly</option>
                                        <option value="2">Weekly</option>
                                        <option value="3">Daily</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="profit_method">Profit Method</label><span class="text-danger">*</span>
                                    <select class="form-control" id="profit_method" name="profit_method" tabindex="4">
                                        <option value="">Select Profit Method</option>
                                        <option value="Pre Benefit">Pre Benefit</option>
                                        <option value="After Benefit">After Benefit</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <h5 class="card-title">Condition Info</h5>
                        <div class="row">
                            <!-- Fields -->
                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label for="scheme_interest_rate">Interest Rate</label><span class="text-danger">*</span>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="scheme_interest_rate" name="scheme_interest_rate" tabindex="5" placeholder="Enter Min Interest Rate">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label for="scheme_due_period">Due Period</label><span class="text-danger">*</span>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="scheme_due_period" name="scheme_due_period" tabindex="6" placeholder="Enter Min Due Period">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label for="scheme_overdue_penalty">Overdue Penalty</label><span class="text-danger">*</span>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="scheme_overdue_penalty" name="scheme_overdue_penalty" tabindex="7" placeholder="Enter Overdue Penalty">
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label for="scheme_doc_charge_min">Document Charge</label><span class="text-danger">*</span>
                                    <input type="hidden" id="doc_charge_type" value="percent">
                                    <div class="input-group int-grp-label-empty">
                                        <div class="btn-group btn-group-toggle mb-1 radio-toggle-div-cls" data-toggle="buttons">
                                            <label class="btn btn-outline-primary active radio-toggle-label-cls doc-type" id="label_doc_charge_type_percent">
                                                <input type="radio" class="form-control" name="doc_charge_type" id="doc_charge_type_percent" value="percent" tabindex="8" checked>%
                                            </label>
                                            <label class="btn btn-outline-primary radio-toggle-label-cls doc-type" id="label_doc_charge_type_rupee">
                                                <input type="radio" class="form-control" name="doc_charge_type" id="doc_charge_type_rupee" value="rupee" tabindex="9"> ₹
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label for="scheme_doc_charge_min"> </label>
                                    <div class="input-group int-grp-label-empty">
                                        <input type="number" class="form-control form-group-label-emptywith-input scheme_doc_minmax" id="scheme_doc_charge_min" name="scheme_doc_charge_min" tabindex="10" placeholder="Enter Min Document Charge">
                                        <div class="input-group-append form-group-label-emptywith-input">
                                            <span class="input-group-text doc-span-val">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label for="scheme_doc_charge_max"> </label>
                                    <div class="input-group int-grp-label-empty">
                                        <input type="number" class="form-control form-group-label-emptywith-input scheme_doc_minmax" id="scheme_doc_charge_max" name="scheme_doc_charge_max" tabindex="11" placeholder="Enter Max Document Charge">
                                        <div class="input-group-append form-group-label-emptywith-input">
                                            <span class="input-group-text doc-span-val">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label>Processing Fee</label><span class="text-danger">*</span>
                                    <input type="hidden" id="processing_fee_type" value="percent">
                                    <div class="input-group int-grp-label-empty">
                                        <div class="btn-group btn-group-toggle mb-1 radio-toggle-div-cls" data-toggle="buttons">
                                            <label class="btn btn-outline-primary active radio-toggle-label-cls processing-type">
                                                <input type="radio" name="processing_fee_type" id="processing_fee_type_percent" value="percent" tabindex="12" checked>%
                                            </label>
                                            <label class="btn btn-outline-primary radio-toggle-label-cls processing-type">
                                                <input type="radio" name="processing_fee_type" id="processing_fee_type_rupee" value="rupee" tabindex="13"> ₹
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label for="scheme_processing_fee_min"> </label>
                                    <div class="input-group int-grp-label-empty">
                                        <input type="number" class="form-control form-group-label-emptywith-input scheme_processing_minmax" id="scheme_processing_fee_min" name="scheme_processing_fee_min" tabindex="14" placeholder="Enter Min Processing Fee">
                                        <div class="input-group-append form-group-label-emptywith-input">
                                            <span class="input-group-text processing-span-val">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label for="scheme_processing_fee_max"> </label>
                                    <div class="input-group int-grp-label-empty">
                                        <input type="number" class="form-control form-group-label-emptywith-input scheme_processing_minmax" id="scheme_processing_fee_max" name="scheme_processing_fee_max" tabindex="15" placeholder="Enter Max Processing Fee">
                                        <div class="input-group-append form-group-label-emptywith-input">
                                            <span class="input-group-text processing-span-val">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group text-right">
                                    <button name="submit_scheme" id="submit_scheme" class="btn btn-primary" tabindex="16" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                </div>
                            </div>
                        </div>
                        </br>
                    </form>
                </div>

                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="scheme_modal_table" class="custom-table">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Scheme</th>
                                    <th>Due Method</th>
                                    <th>Profit Method</th>
                                    <th>Interest Rate</th>
                                    <th>Due Period</th>
                                    <th>Document Charge Min</th>
                                    <th>Document Charge Max</th>
                                    <th>Processing Fee Min</th>
                                    <th>Processing Fee Max</th>
                                    <th>Overdue Penalty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="17" onclick="getSchemeDropdown()">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Loan Scheme Modal END ////////////////////////////////////////////////////////////////////// -->