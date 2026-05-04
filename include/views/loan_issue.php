<!-- Loan Issue List Start -->
<div class="card loanissue_table_content">
    <div class="card-body">
        <div class="col-12">
            <table id="loan_issue_table" class="table custom-table">
                <thead>
                    <tr>
                        <th>S.NO</th>
                        <th>Loan Date</th>
                        <th>Customer ID</th>
                        <th>Aadhar Number</th>
                        <th>Customer Name</th>
                        <th>Area</th>
                        <th>Line</th>
                        <th>Branch</th>
                        <th>Mobile</th>
                        <th>Loan Category</th>
                        <th>Loan Amount</th>
                        <th>Customer Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<!--Loan Issue List End-->
<div id="loan_issue_content" style="display:none;">
    
    <input type="hidden" name="bank_clr_bank_id" id="bank_clr_bank_id"/>
    <input type="hidden" name="bank_clr_trans_amnt" id="bank_clr_trans_amnt"/>

    <div class="text-right">
        <button type="button" class="btn btn-primary" id="back_btn"><span class="icon-arrow-left"></span>&nbsp; Back </button>
    </div>
    <br>
    <div class="radio-container">
        <div class="selector">
            <div class="selector-item">
                <input type="radio" id="documentation" name="loan_issue_type" class="selector-item_radio" value="loandoc" checked>
                <label for="documentation" class="selector-item_label">Documentation</label>
            </div>
            <div class="selector-item">
                <input type="radio" id="loan_issue" name="loan_issue_type" class="selector-item_radio" value="loanissue">
                <label for="loan_issue" class="selector-item_label">Loan Issue</label>
            </div>
        </div>
    </div>
    <br>
    <form id="documentation_form" name="documentation_form">
        <input type="hidden" id="customer_profile_id">
        <div class="text-right">
            <button type="button" class="btn btn-primary" id="print_doc"><span class="icon-print"></span>&nbsp; Print </button>
            <br><br>
        </div>
        <div class="row gutters">
            <div class="col-12">
                <!--- -------------------------------------- Document Need START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Document Need</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <table id="doc_need_table" class="table custom-table">
                                    <thead>
                                        <tr>
                                            <th width="500">S.No</th>
                                            <th>Document Name</th>
                                        </tr>
                                    </thead>
                                    <tbody> </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <!--- -------------------------------------- Document Need END ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Document Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="document_type">Document Type</label>
                                    <select class="form-control" id="document_type" name="document_type" tabindex="2">
                                        <option value="">Select Document Type</option>
                                        <option value="1">Cheque Info</option>
                                        <option value="2">Document Info</option>
                                        <option value="3">Mortgage Info</option>
                                        <option value="4">Endorsement Info</option>
                                        <option value="5">Gold Info</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Cheque Info START ------------------------------- -->
                <div class="card cheque-div" style="display: none;">
                    <div class="card-header">
                        <div class="card-title">Cheque Info
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_cheque_info_modal" style="padding: 5px 35px; float: right;" tabindex='9' onclick="getChequeCreationTable();"><span class="icon-add"></span></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="cheque_info_table" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Holder Type</th>
                                                <th>Holder Name</th>
                                                <th>Relationship</th>
                                                <th>Bank Name</th>
                                                <th>Cheque Count</th>
                                                <th>Upload</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Cheque Info END ------------------------------- -->

                <!--- -------------------------------------- Document Info START ------------------------------- -->
                <div class="card doc_div" style="display: none;">
                    <div class="card-header">
                        <div class="card-title">Document Info
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_doc_info_modal" onclick="getFamilyMember('Select Holder Name', '#doc_holder_name'); getDocCreationTable();" style="padding: 5px 35px; float: right;" tabindex='29'><span class="icon-add"></span></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="document_info" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Document Name</th>
                                                <th>Document Type</th>
                                                <th>Holder Name</th>
                                                <th>Relationship</th>
                                                <th>Upload</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Document Info END ------------------------------- -->

                <!--- -------------------------------------- Mortgage Info START ------------------------------- -->
                <div class="card mortgage-div" style="display: none;">
                    <div class="card-header">
                        <div class="card-title">Mortgage Info
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_mortgage_info_modal" onclick="getFamilyMember('Select Property Holder Name', '#property_holder_name');getMortCreationTable()" style="padding: 5px 35px; float: right;" tabindex='30'><span class="icon-add"></span></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="mortgage_info" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.No</th>
                                                <th>Property Holder Name</th>
                                                <th>Relationship</th>
                                                <th>Property Detail</th>
                                                <th>Mortgage Name</th>
                                                <th>Designation</th>
                                                <th>Mortgage Number</th>
                                                <th>Reg Office</th>
                                                <th>Mortgage Value</th>
                                                <th>Upload</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Mortgage Info END ------------------------------- -->

                <!--- -------------------------------------- Endorsement Info START ------------------------------- -->
                <div class="card endorsement-div" style="display: none;">
                    <div class="card-header">
                        <div class="card-title">Endorsement Info
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_endorsement_info_modal" onclick="getFamilyMember('Select Proof Of', '#owner_name');getEndorsementCreationTable();" style="padding: 5px 35px; float: right;" tabindex='31'><span class="icon-add"></span></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="endorsement_info" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Owner Name</th>
                                                <th>Relationship</th>
                                                <th>Vehicle Details</th>
                                                <th>Endorsement Name</th>
                                                <th>Key Original</th>
                                                <th>RC Original</th>
                                                <th>Upload</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Endorsement Info END ------------------------------- -->

                <!--- -------------------------------------- Gold Info START ------------------------------- -->
                <div class="card gold-div" style="display: none;">
                    <div class="card-header">
                        <div class="card-title">Gold Info
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_gold_info_modal" style="padding: 5px 35px; float: right;" tabindex='31' onclick="getGoldCreationTable()"><span class="icon-add"></span></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="gold_info" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Gold Type</th>
                                                <th>Purity</th>
                                                <th>Weight</th>
                                                <th>Value</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Gold Info END ------------------------------- -->

            </div>
        </div>
    </form>

    <!-- -------------------------------------- Loan Issue START ------------------------------ -->
    <form id="loan_issue_form" name="loan_issue_form" style="display: none;">
        <div class="row gutters">
            <input type="hidden" id="aadhar_num">
            <div class="col-12">
                <!--- -------------------------------------- Personal Info START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Personal Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="aadhar_nums"> Aadhar Number</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control personal_info_disble" name="aadhar_nums" id="aadhar_nums" tabindex="1"  placeholder="Enter Aadhar Number" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="cus_id"> Customer ID</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="cus_id" name="cus_id" tabindex="2" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="cus_name"> Customer Name</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="cus_name" name="cus_name" tabindex="3" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="cus_data"> Customer Data</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="cus_data" name="cus_data" tabindex="4" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="mobile1"> Mobile Number </label><span class="text-danger">*</span>
                                            <input type="number" class="form-control " id="mobile1" name="mobile1" tabindex="5" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="cus_area"> Area </label><span class="text-danger">*</span>
                                            <input type="text" class="form-control " id="cus_area" name="cus_area" tabindex="6" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="pic"> Photo</label><span class="text-danger">*</span><br>
                                            <img id='imgshow' class="img_show" src='img\avatar.png' />
                                            <input type="hidden" id="per_pic">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Personal Info END ------------------------------- -->

                <!--- -------------------------------------- Loan Info ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Loan Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_id_calc"> Loan ID</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="loan_id_calc" name="loan_id_calc" tabindex="7" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_category_calc"> Loan Category</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="loan_category_calc" name="loan_category_calc" tabindex="8" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="category_info_calc">Category Info</label>
                                    <textarea class="form-control" id="category_info_calc" name="category_info_calc" tabindex="9" readonly></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_amnt_calc">Loan Amount</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="loan_amnt_calc" name="loan_amnt_calc" tabindex="10" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="profit_type_calc">Profit Type</label><span class="text-danger">*</span>
                                    <select class="form-control" id="profit_type_calc" name="profit_type_calc" tabindex="11" disabled>
                                        <option value="">Select Profit Type</option>
                                        <option value="0">Calculation</option>
                                        <option value="1">Scheme</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Loan Info END --------------------------------->

                <!--- -------------------------------------- Calculation - Scheme START ------------------------------- -->
                <div class="card" id="profit_type_calc_scheme" style="display: none;">
                    <div class="card-header">
                        <div class="card-title calc_scheme_title">Calculation</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 calc" style="display:none">
                                <div class="form-group">
                                    <label for="due_method_calc">Due Method</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="due_method_calc" name="due_method_calc" value="Monthly" tabindex="12" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 calc" style="display:none">
                                <div class="form-group">
                                    <label for="due_type_calc">Due Type</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="due_type_calc" name="due_type_calc" tabindex="13" readonly>
                                </div>
                            </div>
                             <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 interest_type" style="display:none">
                                <div class="form-group">
                                    <label for="interest_calculate">Interest Calculate</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="interest_calculate" name="interest_calculate" tabindex="7" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 scheme" style="display:none">
                                <div class="form-group">
                                    <label for="scheme_due_method_calc">Due Method</label><span class="text-danger">*</span>
                                    <select class="form-control" id="scheme_due_method_calc" name="scheme_due_method_calc" tabindex="14" disabled>
                                        <option value="">Select Due Method</option>
                                        <option value="1">Monthly</option>
                                        <option value="2">Weekly</option>
                                        <option value="3">Daily</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 scheme_day" style="display:none">
                                <div class="form-group">
                                    <label for="scheme_day_calc">Day</label><span class="text-danger">*</span>
                                    <select class="form-control" id="scheme_day_calc" name="scheme_day_calc" tabindex="15" disabled>
                                        <option value="">Select Day</option>
                                        <option value="1">Monday</option>
                                        <option value="2">Tuesday</option>
                                        <option value="3">Wednesday</option>
                                        <option value="4">Thursday</option>
                                        <option value="5">Friday</option>
                                        <option value="6">Saturday</option>
                                        <option value="7">Sunday</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 scheme" style="display:none">
                                <div class="form-group">
                                    <label for="scheme_name_calc">Scheme Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="scheme_name_calc" name="scheme_name_calc" tabindex="16" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="profit_method_calc">Profit Method</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="profit_method_calc" name="profit_method_calc" tabindex="17" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="interest_rate_calc">Interest Rate</label><span class="text-danger">*</span><!-- Min and max intrest rate-->
                                    <input type="number" class="form-control" id="interest_rate_calc" name="interest_rate_calc" tabindex="18" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="due_period_calc">Due Period</label><span class="text-danger">*</span><!-- Min and max Profit Method-->
                                    <input type="number" class="form-control" id="due_period_calc" name="due_period_calc" tabindex="19" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_charge_calc">Document Charges</label><span class="text-danger">*</span><!-- Min and max Document charges-->
                                    <input type="number" class="form-control" id="doc_charge_calc" name="doc_charge_calc" tabindex="20" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="processing_fees_calc">Processing Fees</label><span class="text-danger">*</span><!-- Min and max Processing fee-->
                                    <input type="number" class="form-control" id="processing_fees_calc" name="processing_fees_calc" tabindex="21" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Calculation - Scheme END ------------------------------- -->

                <!--- -------------------------------------- Loan Calculate START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Loan Calculation</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="principal_amnt_calc">Principal Amount</label><span class="text-danger princ-diff">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="principal_amnt_calc" name="principal_amnt_calc" tabindex="22" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="interest_amnt_calc">Interest Amount</label><span class="text-danger int-diff">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="interest_amnt_calc" name="interest_amnt_calc" tabindex="23" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 interest_cal" >
                                <div class="form-group">
                                    <label for="total_amnt_calc">Total Amount</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="total_amnt_calc" name="total_amnt_calc" tabindex="17" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 interest_cal">
                                <div class="form-group">
                                    <label for="due_amnt_calc">Due Amount</label><span class="text-danger due-diff">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="due_amnt_calc" name="due_amnt_calc" tabindex="18" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_charge_calculate">Document Charges</label><span class="text-danger doc-diff">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="doc_charge_calculate" name="doc_charge_calculate" tabindex="26" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="processing_fees_calculate">Processing Fees</label><span class="text-danger proc-diff">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="processing_fees_calculate" name="processing_fees_calculate" tabindex="27" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="net_cash_calc">Net Cash</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="net_cash_calc" name="net_cash_calc" tabindex="28" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Loan Calculate END ------------------------------- -->

                <!--- -------------------------------------- Collection Info START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Collection Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_date_calc">Loan Date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="loan_date_calc" name="loan_date_calc" tabindex="29" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="due_startdate_calc">Due Start Date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="due_startdate_calc" name="due_startdate_calc" tabindex="30">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="maturity_date_calc">Maturity Date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="maturity_date_calc" name="maturity_date_calc" tabindex="31" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Collection Info END ------------------------------- -->

                <!--- -------------------------------------- Issue Info START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Issue Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="balance_net_cash">Balance Net Cash</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="balance_net_cash" name="balance_net_cash" tabindex="32" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="payment_type">Payment Type</label><span class="text-danger">*</span>
                                    <select class="form-control" id="payment_type" name="payment_type" tabindex="33">
                                        <option value="">Select Payment Type</option>
                                        <option value="1">Split Payment</option>
                                        <option value="2">Single Payment</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12  payment">
                                <div class="form-group">
                                    <label for="payment_mode">Payment Mode</label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="payment_mode" name="payment_mode" tabindex="34">
                                        <option value=""> Select Payment Mode</option>
                                        <option value="1"> Cash </option>
                                        <option value="2"> Bank Transfer </option>
                                        <option value="3"> Cheque </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12" id="bank_container" style="display: none;">
                                <div class="form-group">
                                    <label for="bank_name">Bank Name</label><span class="text-danger">*</span>
                                    <select class="form-control" id="bank_name" name="bank_name" tabindex="35">
                                        <option value="">Select Bank Name</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 cash_issue" style="display:none">
                                <div class="form-group">
                                    <label for="disabledInput">Cash</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="cash" name="cash" oninput="this.value = this.value.replace(/[^0-9]/g, '');" tabindex="36">
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 checque" style="display:none">
                                <div class="form-group">
                                    <label for="">Cheque number</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" id="chequeno" name="chequeno" tabindex="37">
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 checque" style="display:none">
                                <div class="form-group">
                                    <label for="">Cheque Value</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="chequeValue" name="chequeValue" tabindex="38">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 checque" style="display:none">
                                <div class="form-group">
                                    <label for="">Cheque Remark</label>
                                    <input type="text" class="form-control" id="chequeRemark" name="chequeRemark" tabindex="39">
                                </div>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 transaction" style="display:none">
                                <div class="form-group">
                                    <label for="disabledInput">Transaction ID</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="transaction_id" name="transaction_id" tabindex="40">
                                    <span class="text-danger" style="display: none;" id="transact_id"> Please Enter Transaction ID </span>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 transaction" style="display:none">
                                <div class="form-group">
                                    <label for="disabledInput">Transaction Date</label>&nbsp;<span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="trans_date" name="trans_date" value='' tabindex='32' readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 transaction" style="display:none">
                                <div class="form-group">
                                    <label for="disabledInput">Transaction Value </label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="transaction_value" name="transaction_value" tabindex="41">
                                    <span class="text-danger" style="display: none;" id="transact_val"> Please Enter Transaction Value </span>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 transaction" style="display:none">
                                <div class="form-group">
                                    <label for="disabledInput">Transaction Remark </label>
                                    <input type="text" class="form-control" id="transaction_remark" name="transaction_remark" tabindex="42">
                                    <span class="text-danger" style="display: none;" id="transact_remark"> Please Enter Transaction Remark </span>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 balance_remark_container" style="display:none">
                                <div class="form-group">
                                    <label for="disabledInput">Balance Amount </label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="balance_amount" name="balance_amount" readonly tabindex='43'>
                                </div>
                            </div>


                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="issue_date">Issue Date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="issue_date" name="issue_date" tabindex="44" readonly>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="issue_person"> Issue Person </label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="issue_person" name="issue_person" tabindex="45">
                                        <option value=""> Select Issue Person </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="issue_relationship">Relationship</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="issue_relationship" id="issue_relationship" tabindex="46" placeholder="Relationship" readonly>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card" style="display:none;" id="loan_count_div">
                    <div class="card-header">
                        <div class="card-title">Customer Summary</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_count"> Loan Count </label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="loan_count" name="loan_count" disabled placeholder="Loan Count" tabindex="47" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="first_loan_date">First Loan Date </label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="first_loan_date" name="first_loan_date" disabled placeholder="First Loan Date" tabindex="48" readonly>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!--- -------------------------------------- Issue Info END ------------------------------- -->

            <div class="col-12 mt-3 text-right">
                <button name="submit_loan_issue" id="submit_loan_issue" class="btn btn-primary" tabindex="46"><span class="icon-check"></span>&nbsp;Submit</button>
            </div>
        </div>
</div>
</form>
<!-- -------------------------------------- Loan Issue END ------------------------------ -->
</div> <!-- Loan Issue Content END - Customer profile & Loan Issue -->


<!-- ------------------------------------------------------------ Cheque Info Modal START ------------------------------------------------------------- -->
<div class="modal fade" id="add_cheque_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Cheque Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" tabindex="1" onclick="getChequeInfoTable(); refreshChequeModal();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="cheque_info_form">
                        <input type="hidden" name="cheque_info_id" id='cheque_info_id'>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cq_holder_type">Holder Type</label><span class="text-danger">*</span>
                                    <select class="form-control" name="cq_holder_type" id="cq_holder_type" tabindex="2">
                                        <option value="">Select Holder Type</option>
                                        <option value="1">Customer</option>
                                        <option value="2">Guarantor</option>
                                        <option value="3">Family Member</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 cq_fam_member" style="display:none">
                                <div class="form-group">
                                    <label for="cq_fam_mem"> Family Member </label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="cq_fam_mem" name="cq_fam_mem" tabindex="3">
                                        <option value=""> Select Family Member </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cq_holder_name">Holder Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="cq_holder_name" name="cq_holder_name" tabindex="4" placeholder="Holder Name" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cq_relationship">Relationship</label>
                                    <input type="text" class="form-control" name="cq_relationship" id="cq_relationship" tabindex="5" placeholder="Relationship" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cq_bank_name">Bank Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="cq_bank_name" name="cq_bank_name" tabindex="6" placeholder="Enter Bank Name">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cheque_count">Cheque Count</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="cheque_count" id="cheque_count" tabindex="7" placeholder="Enter Cheque Count">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cq_upload">Upload</label>
                                    <input type="file" class="form-control cq_upload" name="cq_upload[]" id="cq_upload" tabindex="8" onchange="compressImage(this, 200)" multiple>
                                    <input type="hidden" id="cq_upload_edit">
                                </div>
                            </div>
                        </div>

                        <div class="row" id="cheque_no"></div>

                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="" style="visibility:hidden"></label><br>
                                    <button name="submit_cheque_info" id="submit_cheque_info" class="btn btn-primary" tabindex="9"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_cheque_form" class="btn btn-outline-secondary" tabindex="10">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="cheque_creation_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th width="20">S.NO</th>
                                    <th>Holder Type</th>
                                    <th>Holder Name</th>
                                    <th>Relationship</th>
                                    <th>Bank Name</th>
                                    <th>Cheque Count</th>
                                    <th>Upload</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="11" onclick="getChequeInfoTable();refreshChequeModal();">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ------------------------------------------------------------ Cheque Info Modal END ------------------------------------------------------------- -->

<!-- ------------------------------------------------------------ Document Info Modal START --------------------------------------------------------------- -->
<div class="modal fade" id="add_doc_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Document Info</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="getDocInfoTable();refreshDocModal();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="doc_info_form">
                        <input type="hidden" name="doc_info_id" id='doc_info_id'>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_name">Document Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="doc_name" id="doc_name" tabindex="1" placeholder="Enter Document Name">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_type">Document Type</label><span class="text-danger">*</span>
                                    <select class="form-control" name="doc_type" id="doc_type" tabindex="2">
                                        <option value="">Select Document Type</option>
                                        <option value="1">Original</option>
                                        <option value="2">Xerox</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_holder_name">Holder Name</label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="doc_holder_name" name="doc_holder_name" tabindex="3">
                                        <option value="">Select Holder Name</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_relationship">Relationship</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="doc_relationship" id="doc_relationship" tabindex="4" placeholder="Relationship" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_upload">Upload</label>
                                    <input type="file" class="form-control" name="doc_upload" id="doc_upload" onchange="compressImage(this, 200)" tabindex="5">
                                    <input type="hidden" name="doc_upload_edit" id="doc_upload_edit">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <button name="submit_doc_info" id="submit_doc_info" class="btn btn-primary" tabindex="6" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_doc_form" class="btn btn-outline-secondary" style="margin-top: 18px;" tabindex="7">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="doc_creation_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th width="20">S.No.</th>
                                    <th>Document Name</th>
                                    <th>Document Type</th>
                                    <th>Holder Name</th>
                                    <th>Relationship</th>
                                    <th>Upload</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" onclick="getDocInfoTable();refreshDocModal()" tabindex="8">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ------------------------------------------------------------ Document Info Modal END --------------------------------------------------------------- -->

<!-- ------------------------------------------------------------ Mortgage Info Modal START --------------------------------------------------------------- -->
<div class="modal fade" id="add_mortgage_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Mortgage Info</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="getMortInfoTable();refreshMortModal();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="mortgage_form">
                        <input type="hidden" name="mortgage_info_id" id='mortgage_info_id'>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="property_holder_name">Property Holder Name</label><span class="text-danger">*</span>
                                    <select class="form-control" name="property_holder_name" id="property_holder_name" tabindex="1">
                                        <option value="">Select Property Holder Name </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="mort_relationship">Relationship</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="mort_relationship" id="mort_relationship" tabindex="2" placeholder="Relationship" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="mort_property_details">Property Details</label><span class="text-danger">*</span>
                                    <textarea class="form-control" name="mort_property_details" id="mort_property_details" tabindex="3"></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="mortgage_name">Mortgage Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="mortgage_name" id="mortgage_name" tabindex="4" placeholder="Enter Mortgage Name">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="mort_designation">Designation</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="mort_designation" id="mort_designation" tabindex="5" placeholder="Enter Designation">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="mortgage_no">Mortgage Number</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="mortgage_no" id="mortgage_no" tabindex="6" placeholder="Mortgage Number">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="reg_office">Reg Office</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="reg_office" id="reg_office" tabindex="7" placeholder="Reg Office">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="mortgage_value">Mortgage Value</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="mortgage_value" id="mortgage_value" tabindex="8" placeholder="Mortgage value">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="mort_upload">Upload</label>
                                    <input type="file" class="form-control" name="mort_upload" id="mort_upload" onchange="compressImage(this, 200)" tabindex="9">
                                    <input type="hidden" name="mort_upload_edit" id="mort_upload_edit">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <button name="submit_mortgage_info" id="submit_mortgage_info" class="btn btn-primary" tabindex="10" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_mortgage_form" class="btn btn-outline-secondary" style="margin-top: 18px;" tabindex="11">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="mortgage_creation_table" class="table-responsive custom-table">
                            <thead>
                                <tr>
                                    <th width="20">S.No.</th>
                                    <th>Property Holder Name</th>
                                    <th>Relationship</th>
                                    <th>Property Details</th>
                                    <th>Mortgage Name</th>
                                    <th>Designation</th>
                                    <th>Mortgage Number</th>
                                    <th>Reg Office</th>
                                    <th>Mortgage Value</th>
                                    <th>Upload</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="12" onclick="getMortInfoTable();refreshMortModal();">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ------------------------------------------------------------ Mortgage Info Modal END --------------------------------------------------------------- -->

<!-- ------------------------------------------------------------ Endorsement Info Modal START --------------------------------------------------------------- -->
<div class="modal fade" id="add_endorsement_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Endorsement Info</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="getEndorsementInfoTable();refreshEndorsementModal();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="endorsement_form">
                        <input type="hidden" name="endorsement_info_id" id='endorsement_info_id'>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="owner_name">Owner</label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="owner_name" name="owner_name" tabindex="2">
                                        <option value="">Select Proof Of</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="owner_relationship">Relationship</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="owner_relationship" id="owner_relationship" tabindex="3" placeholder="Relationship" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="vehicle_details">Vehicle Details</label><span class="text-danger">*</span>
                                    <textarea class="form-control" id="vehicle_details" name="vehicle_details" tabindex="4"></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="endorsement_name">Endorsement Name</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="endorsement_name" id="endorsement_name" tabindex="5" placeholder="Enter Endorsement Name">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="key_original">Key Original</label><span class="text-danger">*</span>
                                    <select class="form-control" name="key_original" id="key_original" tabindex="6">
                                        <option value="">Select Key Original</option>
                                        <option value="YES">YES</option>
                                        <option value="NO">NO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="rc_original">RC Original</label><span class="text-danger">*</span>
                                    <select class="form-control" name="rc_original" id="rc_original" tabindex="7">
                                        <option value="">Select RC Original</option>
                                        <option value="YES">YES</option>
                                        <option value="NO">NO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="endorsement_upload"> Upload</label>
                                    <input type="file" class="form-control" id="endorsement_upload" name="endorsement_upload" onchange="compressImage(this, 200)" tabindex="8">
                                    <input type="hidden" id="endorsement_upload_edit">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <button name="submit_endorsement" id="submit_endorsement" class="btn btn-primary" tabindex="9" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_endorsement_form" class="btn btn-outline-secondary" style="margin-top: 18px;" tabindex="10">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="endorsement_creation_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th width="20">S.No.</th>
                                    <th>Owner Name</th>
                                    <th>Relationship</th>
                                    <th>Vehicle Details</th>
                                    <th>Endorsement Name</th>
                                    <th>Key Original</th>
                                    <th>RC Original</th>
                                    <th>Upload</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="11" onclick="getEndorsementInfoTable();refreshEndorsementModal();">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ------------------------------------------------------------ Endorsement Info Modal END --------------------------------------------------------------- -->

<!-- ------------------------------------------------------------ Gold Info Modal END --------------------------------------------------------------- -->
<div class="modal fade" id="add_gold_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Gold</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" onclick="getGoldInfoTable();refreshGoldModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="gold_form">
                        <input type="hidden" name="gold_info_id" id='gold_info_id'>
                        <div class="row">
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="gold_type">Gold Type</label><span class="text-danger">*&nbsp;&nbsp;(Ornament's Name)</span>
                                    <input type="text" class="form-control" name="gold_type" id="gold_type" tabindex="1" placeholder="Enter Gold Type">
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="gold_purity">Purity</label><span class="text-danger">*&nbsp;&nbsp;(Carat)</span>
                                    <input type="text" class="form-control" name="gold_purity" id="gold_purity" tabindex="2" placeholder="Enter Purity">
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="gold_weight">Weight</label><span class="text-danger">*&nbsp;&nbsp;(Grams)</span>
                                    <input type="number" class="form-control" name="gold_weight" id="gold_weight" tabindex="3" placeholder="Enter Weight">
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="gold_value">Value</label><span class="text-danger">*&nbsp;&nbsp;(Rupees)</span>
                                    <input type="number" class="form-control" name="gold_value" id="gold_value" tabindex="4" placeholder="Enter Value">
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <button name="submit_gold_info" id="submit_gold_info" class="btn btn-primary" tabindex="5" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_gold_form" class="btn btn-outline-secondary" style="margin-top: 18px;" tabindex="6">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table id="gold_creation_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th width="20">S.NO</th>
                                    <th>Gold Type</th>
                                    <th>Purity</th>
                                    <th>Weight</th>
                                    <th>Value</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="7" onclick="getGoldInfoTable();refreshGoldModal();">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ------------------------------------------------------------ Gold Info Modal END --------------------------------------------------------------- -->

<!--------------------------------------------------------------Cancel And Revoke Modal start--------------------------------------------------------------------------->

<div class="modal fade" id="add_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title modal_revoke" id="exampleModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeRemarkModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="approve_form">
                        <div class="row">
                            <input type="hidden" name="cus_sts_id" id='cus_sts_id'>
                            <input type="hidden" name="customer_status" id='customer_status'>
                            <div class="col-sm-3 col-md-3 col-lg-3"></div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="remark">Remark</label><span class="text-danger">*</span>
                                    <textarea class="form-control" name="remark" id="remark" placeholder="Enter Remark Detail" tabindex="1"></textarea>
                                    <input type="hidden" id="addremark" value='0'>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <button name="submit_remark" id="submit_remark" class="btn btn-primary" tabindex="1" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" tabindex="1" onclick="closeRemarkModal()">Close</button>
            </div>
        </div>
    </div>
</div>

<!---------------------------------------------------------------------------Cancel and Revoke Modal End---------------------------------------------------------------->