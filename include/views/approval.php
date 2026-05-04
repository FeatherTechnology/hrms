<!-- Approval List Start -->
<div class="card loan_table_content">
    <div class="card-body">
        <div class="col-12">
            <table id="loan_entry_table" class="table custom-table">
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
                        <th class="loan-amount">Loan Amount</th>
                        <th>Customer Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<!--Approval List End-->

<div id="loan_entry_content" style="display:none;">
    <div class="text-right">
        <button type="button" class="btn btn-primary" id="back_btn"><span class="icon-arrow-left"></span>&nbsp; Back </button>
    </div>
    <br>
    <div class="radio-container">
        <div class="selector">
            <div class="selector-item">
                <input type="radio" id="customer_profile" name="loan_entry_type" class="selector-item_radio" value="cus_profile" checked>
                <label for="customer_profile" class="selector-item_label">Customer Profile</label>
            </div>
            <div class="selector-item">
                <input type="radio" id="loan_calculation" name="loan_entry_type" class="selector-item_radio" value="loan_calc">
                <label for="loan_calculation" class="selector-item_label">Loan Calculation</label>
            </div>
        </div>
    </div>
    <br>
    <form id="loan_entry_customer_profile" name="loan_entry_customer_profile">
        <input type="hidden" id="customer_profile_id">
        <div class="row gutters">
            <div class="col-12">
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
                                            <input type="text" class="form-control personal_info_disble" name="aadhar_nums" id="aadhar_nums" tabindex="2" maxlength="14" data-type="adhaar-number" placeholder="Enter Aadhar Number">
                                            <input type="hidden" id="aadhar_num_upd" name="aadhar_num_upd">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="auto_gen_cus_id"> Customer ID</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control personal_info_disble" id="auto_gen_cus_id" name="auto_gen_cus_id" tabindex="1" data-type="adhaar-number" readonly>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="cus_data"> Customer Data</label>
                                            <input type="text" class="form-control" id="cus_data" name="cus_data" disabled tabindex="3">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="cus_name"> Customer Name</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control personal_info_disble" id="cus_name" name="cus_name" pattern="[a-zA-Z\s]+" placeholder="Enter Customer Name" tabindex="4">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 cus_status_div" style="display:none;">
                                        <div class="form-group">
                                            <label for="cus_status"> Customer Status</label>
                                            <input type="text" class="form-control" id="cus_status" name="cus_status" disabled tabindex="5">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="gender">Gender</label><span class="text-danger">*</span>
                                            <select type="text" class="form-control  personal_info_disble" id="gender" name="gender" tabindex="6">
                                                <option value="">Select Gender</option>
                                                <option value="1">Male</option>
                                                <option value="2">Female</option>
                                                <option value="3">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="dob"> DOB</label>
                                            <input type="date" class="form-control  personal_info_disble" id="dob" name="dob" placeholder="Enter Date Of Birth" tabindex="7">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="age"> Age</label>
                                            <input type="number" class="form-control  personal_info_disble" id="age" name="age" readonly placeholder="Age" tabindex="8">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="mobile1"> Mobile Number 1</label><span class="text-danger">*</span>
                                            <input type="number" class="form-control  personal_info_disble" id="mobile1" name="mobile1" placeholder="Enter Mobile Number 1" onKeyPress="if(this.value.length==10) return false;" tabindex="9">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="mobile2"> Mobile Number 2</label>
                                            <input type="number" class="form-control  personal_info_disble" id="mobile2" name="mobile2" onKeyPress="if(this.value.length==10) return false;" placeholder="Enter Mobile Number 2" tabindex="10">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label>Choose Mobile Number for WhatsApp:</label><br>
                                            <label>
                                                <input type="radio" name="mobile_whatsapp" value="mobile1" id="mobile1_radio" class="personal_info_disble">
                                                Mobile Number 1
                                            </label><br>
                                            <label>
                                                <input type="radio" name="mobile_whatsapp" value="mobile2" id="mobile2_radio" class="personal_info_disble">
                                                Mobile Number 2
                                            </label>
                                            <input type="hidden" id="selected_mobile_radio">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="whatsapp_no"> WhatsApp Number </label>
                                            <input type="number" class="form-control  personal_info_disble" id="whatsapp_no" name="whatsapp_no" onKeyPress="if(this.value.length==10) return false;" placeholder="Enter WhatsApp Number" tabindex="11">
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
                                            <input type="file" class="form-control  personal_info_disble" id="pic" name="pic" tabindex="12">
                                            <input type="hidden" class="personal_info_disble" id="per_pic">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 ">
                                <div class="text-right">
                                    <button type="submit" name="submit_personal_info" id="submit_personal_info" class="btn btn-primary" value="Submit"><span class="icon-check"></span>&nbsp;Next</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Family Info <span class="text-danger">*</span>
                            <button type="button" class="btn btn-primary" id="add_group" name="add_group" data-toggle="modal" data-target="#add_fam_info_modal" onclick="getFamilyTable()" style="padding: 5px 35px; float: right;" tabindex='13'><span class="icon-add"></span></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="fam_info_table" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Name</th>
                                                <th>Relationship</th>
                                                <th>Remarks</th>
                                                <th>Age</th>
                                                <th>Live/Deceased</th>
                                                <th>Occupation</th>
                                                <th>Aadhar No</th>
                                                <th>Mobile No</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Guarantor Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="guarantor_name"> Guarantor Name</label><span class="text-danger">*</span>
                                            <input type="hidden" id="guarantor_name_edit">
                                            <select type="text" class="form-control" id="guarantor_name" name="guarantor_name" tabindex="14">
                                                <option value="">Select Guarantor Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="relationship"> Relationship</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="relationship" name="relationship" pattern="[a-zA-Z\s]+" disabled placeholder="Enter Relationship" tabindex="15">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="pic"> Photo</label><br>
                                            <img id='gur_imgshow' class="img_show" src='img\avatar.png' />
                                            <input type="file" class="form-control" id="gu_pic" name="gu_pic" tabindex="16">
                                            <input type="hidden" id="gur_pic">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="checking_hide">
                    <div class="card">
                        <div id="data_checking_div">
                            <div class="card-header">
                                <div class="card-title">Data Checking</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="name_check">Name</label>
                                            <select type="text" class="form-control" id="name_check" name="name_check" tabindex="17">
                                                <option value="">Select Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="aadhar_check">Aadhar</label>
                                            <select type="text" class="form-control" id="aadhar_check" name="aadhar_check" tabindex="18">
                                                <option value="">Select Aadhar</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="mobile_check">Mobile</label>
                                            <select type="text" class="form-control" id="mobile_check" name="mobile_check" tabindex="19">
                                                <option value="">Select Mobile</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="data_checking_table_div" style="display: none;">
                            <div class="card-header">
                                <div class="card-title">Customer Data</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="form-group">
                                            <table id="cus_info" class="table custom-table">
                                                <thead>
                                                    <tr>
                                                        <th width="20">S.NO</th>
                                                        <th>Customer ID</th>
                                                        <th>Customer Name</th>
                                                        <th>Mobile Number</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-header">
                                <div class="card-title">Family Data</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                        <div class="form-group">
                                            <table id="family_info" class="table custom-table">
                                                <thead>
                                                    <tr>
                                                        <th width="20">S.NO</th>
                                                        <th>Aadhar Number</th>
                                                        <th>Name</th>
                                                        <th>Relationship</th>
                                                        <th>Under Customer Name</th>
                                                        <th>Under Customer ID</th>
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

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Resident Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="res_type">Residential Type</label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="res_type" name="res_type" tabindex="20">
                                        <option value="">Select Residential Type</option>
                                        <option value="1">Own</option>
                                        <option value="2">Rental</option>
                                        <option value="3">Lease</option>
                                        <option value="4">Quaters</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="res_detail"> Residential Details </label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="res_detail" name="res_detail" pattern="[a-zA-Z\s]+" placeholder="Enter Residential Details" tabindex="21">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="res_address"> Address </label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="res_address" name="res_address" placeholder="Enter Address" tabindex="22">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="native_address"> Native Address </label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="native_address" name="native_address" placeholder="Enter Native Address" tabindex="23">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Occupation Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="occupation"> Occupation </label>
                                    <input type="text" class="form-control" id="occupation" name="occupation" pattern="[a-zA-Z\s]+" placeholder="Enter Occupation" tabindex="24">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="occ_detail"> Occupation Detail</label>
                                    <input type="text" class="form-control" id="occ_detail" name="occ_detail" placeholder="Enter Occupation Detail " tabindex="25">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="occ_income"> Income</label>
                                    <input type="text" class="form-control" id="occ_income" name="occ_income" placeholder="Enter Income" tabindex="26">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="occ_address"> Address </label>
                                    <input type="text" class="form-control" id="occ_address" name="occ_address" placeholder="Enter Address" tabindex="27">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Area Confirmation</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="area_confirm">Area Confirm</label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="area_confirm" name="area_confirm" tabindex="28">
                                        <option value="">Select Area Confirm</option>
                                        <option value="1">Resident</option>
                                        <option value="2">Occupation</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="area">Area</label><span class="text-danger">*</span>
                                    <input type="hidden" id="area_edit">
                                    <select type="text" class="form-control" id="area" name="area" tabindex="29">
                                        <option value="">Select Area</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="line"> Line </label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="line" name="line" disabled placeholder="Enter line" tabindex="30">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Property Info
                            <button type="button" class="btn btn-primary" id="add_property" name="add_property" data-toggle="modal" data-target="#add_prop_info_modal" onclick="getPropertyTable();getPropertyHolder()" style="padding: 5px 35px; float: right;" tabindex='31'><span class="icon-add"></span></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="prop_info" class="custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Property</th>
                                                <th>Property Detail</th>
                                                <th>Property Holder</th>
                                                <th>Relationship</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Bank Info
                            <button type="button" class="btn btn-primary" id="add_bank" name="add_bank" data-toggle="modal" data-target="#add_bank_info_modal" onclick="getBankTable()" style="padding: 5px 35px; float: right;" tabindex='32'><span class="icon-add"></span></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="bank_info" class="custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.No.</th>
                                                <th>Bank Name</th>
                                                <th>Branch Name</th>
                                                <th>Account Holder Name</th>
                                                <th>Account Number</th>
                                                <th>IFSC Code</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">KYC Info <span class="text-danger">*</span>
                            <button type="button" class="btn btn-primary" id="add_kyc" name="add_kyc" data-toggle="modal" data-target="#add_kyc_info_modal" onclick="getKycTable();fetchProofList()" style="padding: 5px 35px; float: right;" tabindex='33'><span class="icon-add"></span></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="kyc_info" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Proof Of</th>
                                                <th>Relationship</th>
                                                <th>Proof</th>
                                                <th>Proof Number</th>
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

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Customer Summary</div>
                    </div>
                    <div class="card-body" style="display:none;" id="loan_count_div">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_count"> Loan Count </label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="loan_count" name="loan_count" disabled placeholder="Loan Count" tabindex="34" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="first_loan_date">First Loan Date </label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="first_loan_date" name="first_loan_date" disabled placeholder="First Loan Date" tabindex="35" readonly>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cus_limit"> Customer Limit</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="cus_limit" name="cus_limit" placeholder="Enter Customer Limit" tabindex="36">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="about_cus"> About Customer </label>
                                    <textarea class="form-control" name="about_cus" id="about_cus" placeholder="Enter About Customer" tabindex="37"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 ">
                <div class="text-right">
                    <button type="submit" name="submit_customer_profile" id="submit_customer_profile" class="btn btn-primary" value="Submit" tabindex="38"><span class="icon-check"></span>&nbsp;Submit</button>
                    <button type="reset" id="clear_loan" class="btn btn-outline-secondary" tabindex="39">Clear</button>
                </div>
            </div>
        </div>
    </form>

    <!-- -------------------------------------- Loan Calculation START ------------------------------ -->
    <form id="loan_entry_loan_calculation" name="loan_entry_loan_calculation" style="display: none;">
        <input type="hidden" id="loan_calculation_id">
        <input type="hidden" id="calc_val" >
        <input type="hidden" id="scheme_val">
        <input type="hidden" id="int_rate_upd">
        <input type="hidden" id="due_period_upd">
        <input type="hidden" id="doc_charge_upd">
        <input type="hidden" id="proc_fees_upd">

        <div class="row gutters">
            <div class="col-12">
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
                                    <input type="text" class="form-control" id="loan_id_calc" name="loan_id_calc" tabindex="1" value="LD" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_category_calc"> Loan Category</label><span class="text-danger">*</span>
                                    <input type="hidden" id="loan_category_calc2">
                                    <select class="form-control" id="loan_category_calc" name="loan_category_calc" tabindex="2">
                                        <option value="">Select Loan Category</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="category_info_calc">Category Info</label>
                                    <textarea class="form-control" id="category_info_calc" name="category_info_calc" tabindex="3"></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_amount_calc"> Loan Amount</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="loan_amount_calc" name="loan_amount_calc" tabindex="4">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="profit_type_calc">Profit Type</label><span class="text-danger">*</span>
                                    <select class="form-control" id="profit_type_calc" name="profit_type_calc" tabindex="5">
                                        <option value="">Select Profit Type</option>
                                        <option value="0">Calculation</option>
                                        <option value="1">Scheme</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Loan Info END ------------------------------- -->
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
                                    <input type="text" class="form-control" id="due_method_calc" name="due_method_calc" value="Monthly" tabindex="6" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 calc" style="display:none">
                                <div class="form-group">
                                    <label for="due_type_calc">Due Type</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="due_type_calc" name="due_type_calc" tabindex="7" readonly>
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
                                    <select class="form-control" id="scheme_due_method_calc" name="scheme_due_method_calc" tabindex="6">
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
                                    <select class="form-control to_clear" id="scheme_day_calc" name="scheme_day_calc" tabindex="7">
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
                                    <input type="hidden" id="scheme_name_edit">
                                    <select class="form-control to_clear" id="scheme_name_calc" name="scheme_name_calc" tabindex="8">
                                        <option value="">Select Scheme Name</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 calcs">
                                <div class="form-group">
                                    <label for="profit_method_calc">Profit Method</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="profit_method_calc" name="profit_method_calc" tabindex="8" value="After Benefit" readonly>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="interest_rate_calc">Interest Rate</label><span class="text-danger min-max-int">*</span><!-- Min and max intrest rate-->
                                    <input type="number" class="form-control to_clear" id="interest_rate_calc" name="interest_rate_calc" tabindex="9">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="due_period_calc">Due Period</label><span class="text-danger min-max-due">*</span><!-- Min and max Profit Method-->
                                    <input type="number" class="form-control to_clear" id="due_period_calc" name="due_period_calc" tabindex="10">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_charge_calc">Document Charges</label><span class="text-danger min-max-doc">*</span><!-- Min and max Document charges-->
                                    <input type="number" class="form-control to_clear" id="doc_charge_calc" name="doc_charge_calc" tabindex="11">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="processing_fees_calc">Processing Fees</label><span class="text-danger min-max-proc">*</span><!-- Min and max Processing fee-->
                                    <input type="number" class="form-control to_clear" id="processing_fees_calc" name="processing_fees_calc" tabindex="12">
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
                        <input type="button" class="btn btn-outline-primary card-head-btn" id="refresh_cal" tabindex="13" value="Calculate">
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_amnt_calc">Loan Amount</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="loan_amnt_calc" name="loan_amnt_calc" tabindex="14" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="principal_amnt_calc">Principal Amount</label><span class="text-danger princ-diff">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="principal_amnt_calc" name="principal_amnt_calc" tabindex="15" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="interest_amnt_calc">Interest Amount</label><span class="text-danger int-diff">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="interest_amnt_calc" name="interest_amnt_calc" tabindex="16" readonly>
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
                                    <input type="text" class="form-control refresh_loan_calc" id="doc_charge_calculate" name="doc_charge_calculate" tabindex="19" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="processing_fees_calculate">Processing Fees</label><span class="text-danger proc-diff">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="processing_fees_calculate" name="processing_fees_calculate" tabindex="20" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="net_cash_calc">Net Cash</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control refresh_loan_calc" id="net_cash_calc" name="net_cash_calc" tabindex="21" readonly>
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
                                    <input type="date" class="form-control" id="loan_date_calc" name="loan_date_calc" tabindex="22" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="due_startdate_calc">Due Start Date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="due_startdate_calc" name="due_startdate_calc" tabindex="23">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="maturity_date_calc">Maturity Date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="maturity_date_calc" name="maturity_date_calc" tabindex="24" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Collection Info END ------------------------------- -->

                <!--- -------------------------------------- Other Info START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Other Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="referred_calc">Referred</label><span class="text-danger">*</span>
                                    <select class="form-control" id="referred_calc" name="referred_calc" tabindex="25">
                                        <option value="">Select Referred</option>
                                        <option value="0">Yes</option>
                                        <option value="1">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="agent_id_calc">Agent Name</label><span class="text-danger">*</span>
                                    <select class="form-control" id="agent_id_calc" name="agent_id_calc" tabindex="26" disabled>
                                        <option value="">Select Agent Name</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="agent_name_calc">Agent ID</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="agent_name_calc" name="agent_name_calc" tabindex="27" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--- -------------------------------------- Other Info END ------------------------------- -->

                <!--- -------------------------------------- Documents START ------------------------------- -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Documents</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12"></div>
                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12">
                                <div class="form-group">
                                    <label for="doc_need_calc">Documents</label>
                                    <input type="text" class="form-control" id="doc_need_calc" name="doc_need_calc" tabindex="28">
                                </div>
                            </div>
                            <div class="col-xl-1 col-lg-1 col-md-1 col-sm-1 col-12">
                                <div class="form-group">
                                    <label for="submit_doc_need"> </label>
                                    <input type="button" class="btn btn-primary modalBtnCss" id="submit_doc_need" name="submit_doc_need" value="Add" tabindex="29" style="margin: 16px;">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12"></div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <table id="doc_need_table" class="table custom-table">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Document Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody> </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <!--- -------------------------------------- Documents END ------------------------------- -->

                <div class="col-12 mt-3 text-right">
                    <button name="submit_loan_calculation" id="submit_loan_calculation" class="btn btn-primary" tabindex="30"><span class="icon-check"></span>&nbsp;Submit</button>
                    <button type="reset" id="clear_loan_calc_form" class="btn btn-outline-secondary" tabindex="31">Clear</button>
                </div>
            </div>
        </div>
    </form>
    <!-- -------------------------------------- Loan Calculation END ------------------------------ -->
</div> <!-- Loan entry Content END - Customer profile & Loan Calculation -->


<!--Family Info Modal-->
<div class="modal fade" id="add_fam_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Family Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="getFamilyInfoTable();getGuarantorName()" tabindex="1">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="family_form">
                        <div class="row">
                            <input type="hidden" name="family_id" id='family_id'>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="fam_name">Name</label><span class="text-danger">*</span>
                                    <input class="form-control" name="fam_name" id="fam_name" tabindex="1" placeholder="Enter Name">
                                    <input type="hidden" id="addfam_name_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="fam_relationship">Relationship</label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="fam_relationship" name="fam_relationship" tabindex="1">
                                        <option value=""> Select Relationship </option>
                                        <option value="Father"> Father </option>
                                        <option value="Mother"> Mother </option>
                                        <option value="Spouse"> Spouse </option>
                                        <option value="Son"> Son </option>
                                        <option value="Daughter"> Daughter </option>
                                        <option value="Brother"> Brother </option>
                                        <option value="Sister"> Sister </option>
                                        <option value="Other"> Other </option>
                                    </select>
                                    <input type="hidden" id="addrelationship_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="remarks">Remarks</label>
                                    <textarea class="form-control" name="remarks" id="remarks" tabindex="1"></textarea>
                                    <!-- <input type="hidden" id="addpropdetail_id" value='0'> -->
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="fam_age">Age</label>
                                    <input type="number" class="form-control" name="fam_age" id="fam_age" tabindex="1" placeholder="Enter Age">
                                    <input type="hidden" id="addage_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="fam_live">Live/Deceased</label><span class="text-danger">*</span>
                                    <select class="form-control" id="fam_live" name="fam_live" tabindex="17">
                                        <option value="">Select Live/Deceased</option>
                                        <option value="1">Live</option>
                                        <option value="2">Deceased</option>
                                    </select>
                                    <input type="hidden" id="add_live_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="fam_occupation">Occupation</label>
                                    <input class="form-control" name="fam_occupation" id="fam_occupation" tabindex="1" placeholder="Enter Occupation">
                                    <input type="hidden" id="addoccupation_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="fam_aadhar">Aadhar No</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="fam_aadhar" id="fam_aadhar" tabindex="1" maxlength="14" data-type="adhaar-number" placeholder="Enter Aadhar Number">
                                    <input type="hidden" id="addaadhar_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="fam_mobile">Mobile No</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="fam_mobile" id="fam_mobile" onKeyPress="if(this.value.length==10) return false;" tabindex="1" placeholder="Enter Mobile Number">
                                    <input type="hidden" id="addmobile_id" value='0'>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="" style="visibility:hidden"></label><br>
                                    <button name="submit_family" id="submit_family" class="btn btn-primary" tabindex="1"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_fam_form" class="btn btn-outline-secondary" tabindex="">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="family_creation_table" class="custom-table">
                            <thead>
                                <tr>
                                    <th width="10">S.No.</th>
                                    <th>Name</th>
                                    <th>Relationship</th>
                                    <th>Remarks</th>
                                    <th>Age</th>
                                    <th>Live/Deceased</th>
                                    <th>Occupation</th>
                                    <th>Aadhar No</th>
                                    <th>Mobile No</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="1" onclick="getFamilyInfoTable();getGuarantorName()">Close</button>
            </div>
        </div>
    </div>
</div>
<!--Family Modal End-->

<!--Property Info Modal Start-->
<div class="modal fade" id="add_prop_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Property Info</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" onclick="getPropertyInfoTable()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="property_form">
                        <div class="row">
                            <input type="hidden" name="property_id" id='property_id'>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="property">Property</label><span class="text-danger">*</span>
                                    <input class="form-control" name="property" id="property" tabindex="1" placeholder="Enter Property">
                                    <input type="hidden" id="addprop_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="property_detail">Property Detail</label><span class="text-danger">*</span>
                                    <textarea class="form-control" name="property_detail" id="property_detail" tabindex="1"></textarea>
                                    <input type="hidden" id="addpropdetail_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="property_holder">Property Holder</label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="property_holder" name="property_holder" tabindex="1">
                                        <option value="">Select Property Holder</option>

                                    </select>
                                    <input type="hidden" id="addholder_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="prop_relationship">Relationship</label><span class="text-danger">*</span>
                                    <input class="form-control" name="prop_relationship" id="prop_relationship" disabled tabindex="1" placeholder="Enter Relationship">
                                    <input type="hidden" id="addproprelation_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <button name="submit_property" id="submit_property" class="btn btn-primary" tabindex="6" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_prop_form" class="btn btn-outline-secondary" style="margin-top: 18px;" tabindex="1">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="row">
                    <div class="col-12">
                        <table id="property_creation_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th width="20">S.No.</th>
                                    <th>Property</th>
                                    <th>Property Detail</th>
                                    <th>Property Holder</th>
                                    <th>Relationship</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" onclick="getPropertyInfoTable()" tabindex="1">Close</button>
            </div>
        </div>
    </div>
</div>
<!--Proerty Info Modal End-->

<!--Bank Info Modal Start-->
<div class="modal fade" id="add_bank_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Bank Info</h5>
                <button type="button" class="close" data-dismiss="modal" tabindex="1" onclick="getBankInfoTable()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="bank_form">
                        <div class="row">
                            <input type="hidden" name="bank_id" id='bank_id'>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="bank_name">Bank Name</label><span class="text-danger">*</span>
                                    <input class="form-control" name="bank_name" id="bank_name" tabindex="1" placeholder="Enter Bank Name">
                                    <input type="hidden" id="addbank_name_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="branch_name">Branch Name</label><span class="text-danger">*</span>
                                    <input class="form-control" name="branch_name" id="branch_name" tabindex="1" placeholder="Enter Branch Name">
                                    <input type="hidden" id="addbranch_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="acc_holder_name">Account Holder Name</label><span class="text-danger">*</span>
                                    <input class="form-control" name="acc_holder_name" id="acc_holder_name" tabindex="1" placeholder="Enter Account Holder Name">
                                    <input type="hidden" id="addacc_holder_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="acc_number">Account Number</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="acc_number" id="acc_number" tabindex="1" placeholder="Enter Account Number">
                                    <input type="hidden" id="addacc_number_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="ifsc_code">IFSC Code</label><span class="text-danger">*</span>
                                    <input class="form-control" name="ifsc_code" id="ifsc_code" tabindex="1" placeholder="Enter IFSC Code">
                                    <input type="hidden" id="addifsc_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <button name="submit_bank" id="submit_bank" class="btn btn-primary" tabindex="1" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_bank_form" class="btn btn-outline-secondary" style="margin-top: 18px;" tabindex="8">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="bank_creation_table" class="custom-table">
                            <thead>
                                <tr>
                                    <th width="20">S.No.</th>
                                    <th>Bank Name</th>
                                    <th>Branch Name</th>
                                    <th>Account Holder Name</th>
                                    <th>Account Number</th>
                                    <th>IFSC Code</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" onclick=" getBankInfoTable()" tabindex="1">Close</button>
            </div>
        </div>
    </div>
</div>
<!--Bank Info Modal End-->

<!--KYC Info Modal Start-->
<div class="modal fade" id="add_kyc_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add KYC Info</h5>
                <button type="button" class="close kycmodal_close" data-dismiss="modal" tabindex="1" onclick="getKycInfoTable()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="kyc_form">
                        <div class="row">
                            <input type="hidden" name="kyc_id" id='kyc_id'>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="proof_of">Proof Of</label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="proof_of" name="proof_of" tabindex="1">
                                        <option value="">Select Proof Of</option>
                                        <option value="1">Customer</option>
                                        <option value="2">Family Member</option>
                                    </select>
                                    <input type="hidden" id="add_proofOf_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 kyc_name_div" style="display:none">
                                <div class="form-group">
                                    <label for="kyc_name">Name</label><span class="text-danger">*</span>
                                    <input class="form-control" name="kyc_name" id="kyc_name" tabindex="1" disabled placeholder="Enter Name">
                                    <input type="hidden" id="kyc_nameid" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 fam_mem_div" style="display:none">
                                <div class="form-group">
                                    <label for="fam_mem"> Family Member </label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="fam_mem" name="fam_mem">
                                        <option value=""> Select Family Member </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="kyc_relationship">Relationship</label><span class="text-danger">*</span>
                                    <input class="form-control" name="kyc_relationship" id="kyc_relationship" tabindex="1" disabled placeholder="Enter Relationship">
                                    <input type="hidden" id="addkycrelationship_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                                <div class="form-group">
                                    <label for="proof">Proof</label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="proof" name="proof" tabindex="1">
                                        <option value="">Select proof</option>
                                    </select>
                                    <input type="hidden" id="add_proof" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-1 col-lg-1 col-md-1 col-sm-1 col-12" style="margin-top: 18px; padding-left: 0px !important">
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary modalBtnCss" id="proof_modal_btn" data-toggle="modal" data-target="#add_proof_info_modal" onclick="getProofTable()" tabindex="1">+</button>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="proof_detail">Proof Number</label><span class="text-danger">*</span>
                                    <input class="form-control" name="proof_detail" id="proof_detail" tabindex="1" placeholder="Enter Proof Number">
                                    <input type="hidden" id="addproofdetail_id" value='0'>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="upload"> Upload</label>
                                    <input type="file" class="form-control" id="upload" name="upload" tabindex="1">
                                    <input type="hidden" id="kyc_upload">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <button name="submit_kyc" id="submit_kyc" class="btn btn-primary" tabindex="1" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_kyc_form" class="btn btn-outline-secondary" style="margin-top: 18px;" tabindex="9">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table id="kyc_creation_table" class=" custom-table">
                            <thead>
                                <tr>
                                    <th width="20">S.No.</th>
                                    <th>Proof Of</th>
                                    <th>Relationship</th>
                                    <th>Proof</th>
                                    <th>Proof Number</th>
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
                <button class="btn btn-secondary kycmodal_close" data-dismiss="modal" onclick="getKycInfoTable()" tabindex="1">Close</button>
            </div>
        </div>
    </div>
</div>
<!--KYC Info Modal End-->

<!--KYC Proof Modal Start-->
<div class="modal fade" id="add_proof_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Proof</h5>
                <button type="button" class="close kyc_proof_close" data-dismiss="modal" onclick="fetchProofList()" tabindex="1">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="proof_form">
                        <div class="row">
                            <input type="hidden" name="proof_id" id='proof_id'>
                            <div class="col-sm-3 col-md-3 col-lg-3"></div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="addProof_name">Proof</label><span class="text-danger">*</span>
                                    <input class="form-control" name="addProof_name" id="addProof_name" tabindex="1" placeholder="Enter Proof">
                                    <input type="hidden" id="addline_name_id" value='0'>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <button name="submit_proof" id="submit_proof" class="btn btn-primary" tabindex="1" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_proof_form" class="btn btn-outline-secondary" style="margin-top: 18px;" tabindex="1">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table id="proof_creation_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th width="20">S.No.</th>
                                    <th>Proof </th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary kyc_proof_close" data-dismiss="modal" onclick="fetchProofList()" tabindex="1">Close</button>
            </div>
        </div>
    </div>
</div>
<!--KYC Proof Modal End-->

<!--Cancel And Revoke Modal start-->

<div class="modal fade" id="add_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeRemarkModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="approve_form">
                        <div class="row">
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

<!--Cancel and Revoke Modal End-->