    <div class="row gutters">
    <div class="col-12">
        <div class="col-12 text-right">
            <button class="btn btn-primary radio-card" id="add_staff"><span class="icon-add"></span> Add Staff</button>
            <button class="btn btn-primary" id="back_btn" style="display: none;"><span class="icon-arrow-left"></span> Back</button>
        </div></br>

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
        <div class="card staff_table_content">
            <div class="card-header">
                <div class="card-title">Staff List</div>
            </div>
            <!-- Center Radio Buttons -->
            <br>
            <div class="col-md-12 text-center radio-card" style="display: none;">
                <div class="form-group mb-0 outer_search_card">

                    <input type="radio" name="staff_status" id="active" value="1" checked>

                    <label for="active">&nbsp;Active</label>

                    &nbsp;&nbsp;&nbsp;&nbsp;

                    <input type="radio" name="staff_status" id="inactive" value="2">

                    <label for="inactive">&nbsp;In-Active</label>

                </div>
            </div>
            <div class="card-body">
                <table id="staff_create" class="table custom-table">
                    <thead>
                        <tr>
                            <th>S.NO</th>
                            <th>Staff ID</th>
                            <th>Staff Name</th>
                            <th>Staff Type</th>
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

        <!--Staff Creation Start-->
        <div id="staff_creation_content" style="display: none;">
            <form id="staff_creation" name="staff_creation" action="" method="post" enctype="multipart/form-data">
                <input type="hidden" id="staff_profile_id">
                <div class="row gutters">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">General Info</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="row">
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <label for="company_name">Company Name</label><span class="text-danger">*</span>
                                                    <select class="form-control personal_info_disble" id="company_name" name="company_name" tabindex="1">
                                                        <option value="">Select Company Name</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <label for="staff_auto_id">Staff ID</label><span class="text-danger">*</span>
                                                    <input type="text" class="form-control" name="staff_auto_id" id="staff_auto_id" tabindex="1" placeholder="Enter Staff ID" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <label for="staff_name">Staff Name</label><span class="text-danger">*</span>
                                                    <input type="text" class="form-control personal_info_disble" id="staff_name" name="staff_name" placeholder="Enter Staff Name" tabindex="2">
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <label for="staff_type">Staff Type</label><span class="text-danger">*</span>
                                                    <select type="text" class="form-control personal_info_disble" id="staff_type" name="staff_type" tabindex="3">
                                                        <option value="">Select Staff Type</option>
                                                        <option value="1">Employer</option>
                                                        <option value="2">Employee</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <label for="address">Address</label><span class="text-danger">*</span>
                                                    <input type="text" class="form-control personal_info_disble" id="address" name="address" placeholder="Enter Address" tabindex="4">
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <label for="state">State</label><span class="text-danger">*</span>
                                                    <select class="form-control personal_info_disble" id="state" name="state" tabindex="5">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <label for="district">District</label><span class="text-danger">*</span>
                                                    <select class="form-control personal_info_disble" id="district" name="district" tabindex="6">
                                                        <option value="">Select District</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <label for="place">Place</label><span class="text-danger">*</span>
                                                    <input type="text" class="form-control personal_info_disble" id="place" name="place" placeholder="Enter Place" tabindex="7">
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <label for="pincode">Pincode</label><span class="text-danger">*</span>
                                                    <input type="number" class="form-control personal_info_disble" id="pincode" name="pincode" placeholder="Enter Pincode" onKeyPress="if(this.value.length==6) return false;" tabindex="8">
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <label for="dob">DOB</label>
                                                    <input type="date" class="form-control personal_info_disble" id="dob" name="dob" placeholder="Date of Birth" tabindex="9">
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <label for="age"> Age</label>
                                                    <input type="number" class="form-control  personal_info_disble" id="age" name="age" readonly placeholder="Age" tabindex="10">
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <label for="blood_group">Blood Group</label>
                                                    <input type="text" class="form-control personal_info_disble" id="blood_group" name="blood_group" placeholder="Enter Blood Group" tabindex="11">
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <label for="gender">Gender</label><span class="text-danger">*</span>
                                                    <select class="form-control personal_info_disble" id="gender" name="gender" tabindex="12">
                                                        <option value="">Select Gender</option>
                                                        <option value="1">Male</option>
                                                        <option value="2">Female</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <label for="marital_status">Marital Status</label><span class="text-danger">*</span>
                                                    <select class="form-control personal_info_disble" id="marital_status" name="marital_status" tabindex="13">
                                                        <option value="">Select Marital Status</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 spouse-div" style="display: none;">
                                                <div class="form-group">
                                                    <label for="spouse_name">Spouse Name</label>
                                                    <input type="text" class="form-control personal_info_disble" id="spouse_name" name="spouse_name" placeholder="Enter Spouse Name" tabindex="14">
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 spouse-div" style="display: none;">
                                                <div class="form-group">
                                                    <label for="anniversary_date">Anniversary Date</label>
                                                    <input type="date" class="form-control personal_info_disble" id="anniversary_date" name="anniversary_date" placeholder="Anniversary Date" tabindex="15">
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <label for="joining_date">Joining Date</label><span class="text-danger">*</span>
                                                    <input type="date" class="form-control personal_info_disble" id="joining_date" name="joining_date" placeholder="Joining Date" tabindex="16">
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <label for="relieve_date">Relieve Date</label>
                                                    <input type="date" class="form-control personal_info_disble" id="relieve_date" name="relieve_date" placeholder="Relieve Date" tabindex="17" readonly>
                                                </div>
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                                <div class="form-group">
                                                    <label for="notice_period">Notice Period(month)</label><span class="text-danger">*</span>
                                                    <input type="number" class="form-control personal_info_disble" id="notice_period" name="notice_period" placeholder="Notice Period" tabindex="18">
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
                                                    <input type="file" class="form-control personal_info_disble" id="pic" name="pic" tabindex="19">
                                                    <input type="hidden" id="per_pic">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title">Communication Info</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Fields -->
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="mailid">Mail ID</label><span class="text-danger">*</span>
                                            <input type="email" class="form-control personal_info_disble" id="mailid" name="mailid" placeholder="Enter Mail ID" tabindex="20">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="mobile1"> Mobile Number 1</label><span class="text-danger">*</span>
                                            <input type="number" class="form-control personal_info_disble" id="mobile1" name="mobile1" placeholder="Enter Mobile Number 1" onKeyPress="if(this.value.length==10) return false;" tabindex="21">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="mobile2"> Mobile Number 2</label>
                                            <input type="number" class="form-control personal_info_disble" id="mobile2" name="mobile2" placeholder="Enter Mobile Number 2" onKeyPress="if(this.value.length==10) return false;" tabindex="22">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="whatsapp"> Whatsapp Number</label>
                                            <input type="number" class="form-control personal_info_disble" id="whatsapp" name="whatsapp" placeholder="Enter Whatsapp Number" onKeyPress="if(this.value.length==10) return false;" tabindex="23">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="instagram">Instagram ID</label>
                                            <input type="text" class="form-control personal_info_disble" id="instagram" name="instagram" placeholder="Enter instagram ID" tabindex="24">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="facebook">Facebook ID</label>
                                            <input type="text" class="form-control personal_info_disble" id="facebook" name="facebook" placeholder="Enter Facebook ID" tabindex="25">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title">Bank Info</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Fields -->
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="acc_holder_name">Account Holder Name</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control personal_info_disble" id="acc_holder_name" name="acc_holder_name" placeholder="Enter Account Holder Name" tabindex="26">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="bank_name"> Bank Name</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control personal_info_disble" id="bank_name" name="bank_name" placeholder="Enter Bank Name" tabindex="27">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="acc_number"> Account Number </label><span class="text-danger">*</span>
                                            <input type="number" class="form-control personal_info_disble" id="acc_number" name="acc_number" placeholder="Enter Account Number" tabindex="28">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="ifsc_code"> IFSC Code</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control personal_info_disble" id="ifsc_code" name="ifsc_code" placeholder="Enter IFSC Code" tabindex="29">
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="bank_branch">Branch</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control personal_info_disble" id="bank_branch" name="bank_branch" placeholder="Enter Branch" tabindex="30">
                                        </div>
                                    </div>
                                    <div class="col-md-12 ">
                                        <div class="text-right">
                                            <button type="submit" name="submit_staff" id="submit_staff" class="btn btn-primary" value="Submit"><span class="icon-check"></span>&nbsp;Next</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="staff_content" style="display:none;">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Documents Info
                                        <button type="button" class="btn btn-primary" id="add_document" name="add_document" data-toggle="modal" data-target="#add_document_info_modal" onclick="getDocumentTable()" style="padding: 5px 35px; float: right;" tabindex='31'><span class="icon-add"></span></button>
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
                                    <div class="card-title">Family Info <span class="text-danger">*</span>
                                        <button type="button" class="btn btn-primary" id="add_family" name="add_family" data-toggle="modal" data-target="#add_fam_info_modal" onclick="getFamilyTable()" style="padding: 5px 35px; float: right;" tabindex='32'><span class="icon-add"></span></button>
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
                                                            <th>DOB</th>
                                                            <th>Occupation</th>
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
                                    <div class="card-title">Qualification Info <span class="text-danger">*</span>
                                        <button type="button" class="btn btn-primary" id="add_qualification" name="add_qualification" data-toggle="modal" data-target="#add_qual_info_modal" onclick="getQualificationTable()" style="padding: 5px 35px; float: right;" tabindex='33'><span class="icon-add"></span></button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">
                                                <table id="qual_info_table" class="table custom-table">
                                                    <thead>
                                                        <tr>
                                                            <th width="20">S.NO</th>
                                                            <th>Highest Qualification</th>
                                                            <th>Course/Degree</th>
                                                            <th>Specialization</th>
                                                            <th>College/Institution</th>
                                                            <th>University/Board</th>
                                                            <th>Year of Passing</th>
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
                                    <div class="card-title">Experience Info <span class="text-danger">*</span>
                                        <button type="button" class="btn btn-primary" id="add_experience" name="add_experience" data-toggle="modal" data-target="#add_experience_info_modal" onclick="getExperienceTable()" style="padding: 5px 35px; float: right;" tabindex='34'><span class="icon-add"></span></button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                            <div class="form-group">
                                                <table id="exp_info_table" class="table custom-table">
                                                    <thead>
                                                        <tr>
                                                            <th width="20">S.NO</th>
                                                            <th>Experience Type</th>
                                                            <th>Total Experience</th>
                                                            <th>Previous Company</th>
                                                            <th>Designation</th>
                                                            <th>Worked Duration</th>
                                                            <th>Last Salary</th>
                                                            <th>Reasoned for Leaving</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
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
                                        <!-- Fields -->
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="company">Company Name</label><span class="text-danger">*</span>
                                                <input type="text" class="form-control" id="company" name="company" readonly tabindex="35">
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="branch_name">Branch Name</label><span class="text-danger">*</span>
                                                <select class="form-control" id="branch_name" name="branch_name" tabindex="36">
                                                    <option value="">Select Branch Name</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="department">Department</label><span class="text-danger">*</span>
                                                <select class="form-control" id="department" name="department" tabindex="37">
                                                    <option value="">Select Department</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="team">Team</label><span class="text-danger">*</span>
                                                <select class="form-control" id="team" name="team" tabindex="38">
                                                    <option value="">Select Team</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="designation">Designation</label><span class="text-danger">*</span>
                                                <select class="form-control" id="designation" name="designation" tabindex="39">
                                                    <option value="">Select Designation</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="off_type">Type</label><span class="text-danger">*</span>
                                                <select class="form-control" id="off_type" name="off_type" tabindex="40">
                                                    <option value="">Select Type</option>
                                                    <option value="1">Office</option>
                                                    <option value="2">Field</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 reporting_person_div" style="display: none;">
                                            <div class="form-group">
                                                <label for="reporting_person">Reporting Person</label><span class="text-danger">*</span>
                                                <select class="form-control" id="reporting_person" name="reporting_person" tabindex="41">
                                                    <option value="">Select Reporting Person</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="branch_admin">Branch Admin</label><span class="text-danger">*</span>
                                                <select class="form-control" id="branch_admin" name="branch_admin" tabindex="42">
                                                    <option value="">Select Branch Admin</option>
                                                    <option value="1">Yes</option>
                                                    <option value="2">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 branch_div" style="display: none;">
                                            <div class="form-group">
                                                <label for="branch">Branch</label><span class="text-danger">*</span>
                                                <select class="form-control" id="branch" name="branch" tabindex="43">
                                                    <option value="">Select Branch</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="pf_available">PF Available</label><span class="text-danger">*</span>
                                                <select class="form-control" id="pf_available" name="pf_available" tabindex="44">
                                                    <option value="">Select PF Availability</option>
                                                    <option value="1">Yes</option>
                                                    <option value="2">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="esi_available">ESI Available</label><span class="text-danger">*</span>
                                                <select class="form-control" id="esi_available" name="esi_available" tabindex="45">
                                                    <option value="">Select ESI Availability</option>
                                                    <option value="1">Yes</option>
                                                    <option value="2">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="pt_available">PT Available</label><span class="text-danger">*</span>
                                                <select class="form-control" id="pt_available" name="pt_available" tabindex="46">
                                                    <option value="">Select PT Availability</option>
                                                    <option value="1">Yes</option>
                                                    <option value="2">No</option>
                                                </select>
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
                                                <label for="total_ctc">Total CTC Per Month</label><span class="text-danger">*</span>
                                                <input type="number" class="form-control" id="total_ctc" name="total_ctc" placeholder="Enter Total CTC Per Month" tabindex="47">
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="annual_ctc"> Annual CTC</label>
                                                <input type="text" class="form-control" id="annual_ctc" name="annual_ctc" placeholder="Annual CTC" tabindex="48" readonly>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="shift">Shift</label><span class="text-danger">*</span>
                                                <select class="form-control" id="shift" name="shift" tabindex="49">
                                                    <option value="">Select Shift</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                            <div class="form-group">
                                                <label for="ot_payment">OT Payment</label><span class="text-danger">*</span>
                                                <select class="form-control" id="ot_payment" name="ot_payment" tabindex="50">
                                                    <option value="">Select OT Payment</option>
                                                    <option value="1">CTC Based</option>
                                                    <option value="2">Fixed Amount</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 ot_per_hour_div" style="display: none;">
                                            <div class="form-group">
                                                <label for="ot_per_hour">OT Per Hour</label>
                                                <input type="text" class="form-control" id="ot_per_hour" name="ot_per_hour" placeholder="Enter OT Per Hour" tabindex="51" readonly>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12  ot_per_day_div" style="display: none;">
                                            <div class="form-group">
                                                <label for="ot_per_day">OT Per Day</label>
                                                <input type="text" class="form-control" id="ot_per_day" name="ot_per_day" placeholder="Enter OT Per Day" tabindex="52">
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

                                    <button type="submit" name="submit_staff_creation" id="submit_staff_creation" class="btn btn-primary" value="Submit" tabindex="53"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_staff" class="btn btn-outline-secondary" tabindex="54">Clear</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        </div>
        </form>
    </div>
    <!----------------------------- CARD END  STAFF CREATION FORM------------------------------>

</div>
</div>

<!--Document Info Modal-->
<div class="modal fade" id="add_document_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Document Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="getDocumentInfoTable();" tabindex="1">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="document_form">
                        <div class="row">
                            <input type="hidden" name="document_id" id='document_id'>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_name">Document Name</label><span class="text-danger">*</span>
                                    <input class="form-control" name="doc_name" id="doc_name" tabindex="1" placeholder="Enter Document Name">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_type">Document Type</label><span class="text-danger">*</span>
                                    <select type="text" class="form-control" id="doc_type" name="doc_type" tabindex="1">
                                        <option value=""> Select Document Type </option>
                                        <option value="1">Original </option>
                                        <option value="2">Copy </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="upload"> Document Upload</label>
                                    <input type="file" class="form-control" id="upload" name="upload" onchange="compressImage(this, 200)" tabindex="1">
                                    <input type="hidden" id="doc_upload">
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="" style="visibility:hidden"></label><br>
                                    <button name="submit_document" id="submit_document" class="btn btn-primary" tabindex="1"><span class="icon-check"></span>&nbsp;Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="document_table" class="custom-table">
                            <thead>
                                <tr>
                                    <th width="10">S.No.</th>
                                    <th>Document Name</th>
                                    <th>Document Type </th>
                                    <th>Document</th>
                                    <th>Submitted Date</th>
                                    <th>Returned Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="1" onclick="getDocumentInfoTable();">Close</button>
            </div>
        </div>
    </div>
</div>
<!--Document Modal End-->
<!--Family Info Modal-->
<div class="modal fade" id="add_fam_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Family Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="getFamilyInfoTable()" tabindex="1">
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

                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="fam_dob">DOB</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" name="fam_dob" id="fam_dob" tabindex="1" placeholder="Enter DOB">
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="fam_occupation">Occupation</label><span class="text-danger">*</span>
                                    <input class="form-control" name="fam_occupation" id="fam_occupation" tabindex="1" placeholder="Enter Occupation">

                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="fam_mobile">Mobile No</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="fam_mobile" id="fam_mobile" onKeyPress="if(this.value.length==10) return false;" tabindex="1" placeholder="Enter Mobile Number">

                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="" style="visibility:hidden"></label><br>
                                    <button name="submit_family" id="submit_family" class="btn btn-primary" tabindex="1"><span class="icon-check"></span>&nbsp;Submit</button>
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
                                    <th>DOB</th>
                                    <th>Occupation</th>
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
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="1" onclick="getFamilyInfoTable()">Close</button>
            </div>
        </div>
    </div>
</div>
<!--Family Modal End-->
<!-- Qualification Info Modal-->
<div class="modal fade" id="add_qual_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Qualification Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="getQualificationInfoTable()" tabindex="1">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="qualification_form">
                        <div class="row">
                            <input type="hidden" name="qualification_id" id='qualification_id'>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="highest_qualification">Highest Qualification</label><span class="text-danger">*</span>
                                    <input class="form-control" name="highest_qualification" id="highest_qualification" tabindex="1" placeholder="Enter Highest Qualification">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="degree">Course/Degree</label><span class="text-danger">*</span>
                                    <input class="form-control" name="degree" id="degree" tabindex="1" placeholder="Enter Course/Degree">

                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="specialization">Specialization</label><span class="text-danger">*</span>
                                    <input class="form-control" name="specialization" id="specialization" tabindex="1" placeholder="Enter Specialization">
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="college">College/Institution</label><span class="text-danger">*</span>
                                    <input class="form-control" name="college" id="college" tabindex="1" placeholder="Enter College/Institution">
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="university">University/Board</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="university" id="university" tabindex="1" placeholder="Enter University/Board">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="year_of_passing">Year of Passing</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="year_of_passing" id="year_of_passing" tabindex="1" placeholder="Enter Year of Passing">
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="" style="visibility:hidden"></label><br>
                                    <button name="submit_qualification" id="submit_qualification" class="btn btn-primary" tabindex="1"><span class="icon-check"></span>&nbsp;Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="qualification_creation_table" class="custom-table">
                            <thead>
                                <tr>

                                    <th width="20">S.NO</th>
                                    <th>Highest Qualification</th>
                                    <th>Course/Degree</th>
                                    <th>Specialization</th>
                                    <th>College/Institution</th>
                                    <th>University/Board</th>
                                    <th>Year of Passing</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="1" onclick="getQualificationInfoTable();">Close</button>
            </div>
        </div>
    </div>
</div>
<!--Qualification Modal End-->



<!-- Experience Info Modal-->
<div class="modal fade" id="add_experience_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Experience Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="getExperienceInfoTable()" tabindex="1">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="experience_form">
                        <div class="row">
                            <input type="hidden" name="experience_id" id='experience_id'>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="exp_type">Experience Type</label><span class="text-danger">*</span>
                                    <select class="form-control" id="exp_type" name="exp_type" tabindex="1">
                                        <option value="">Select Experience Type</option>
                                        <option value="1">Fresher</option>
                                        <option value="2">Experienced</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 experience">
                                <div class="form-group">
                                    <label for="total_experience">Total Experience</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="total_experience" id="total_experience" tabindex="1" placeholder="Enter Total Experience">

                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 experience">
                                <div class="form-group">
                                    <label for="pre_company">Previous Company</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="pre_company" id="pre_company" tabindex="1" placeholder="Enter Previous Company">
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 experience">
                                <div class="form-group">
                                    <label for="pre_designation">Designation</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="pre_designation" id="pre_designation" tabindex="1" placeholder="Enter Designation">

                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 experience">
                                <div class="form-group">
                                    <label for="work_duration">Work Duration</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="work_duration" id="work_duration" tabindex="1" placeholder="Enter Work Duration">

                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 experience">
                                <div class="form-group">
                                    <label for="last_salary">Last Salary</label><span class="text-danger">*</span>
                                    <input type="number" class="form-control" name="last_salary" id="last_salary" tabindex="1" placeholder="Enter Last Salary">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 experience">
                                <div class="form-group">
                                    <label for="reason_for_leaving">Reason for Leaving</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" name="reason_for_leaving" id="reason_for_leaving" tabindex="1" placeholder="Enter Reason for Leaving">
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 experience">
                                <div class="form-group">
                                    <label for="" style="visibility:hidden"></label><br>
                                    <button name="submit_experience" id="submit_experience" class="btn btn-primary" tabindex="1"><span class="icon-check"></span>&nbsp;Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="experience_creation_table" class="custom-table">
                            <thead>
                                <tr>

                                    <th width="20">S.NO</th>
                                    <th>Experience Type</th>
                                    <th>Total Experience</th>
                                    <th>Previous Company</th>
                                    <th>Designation</th>
                                    <th>Worked Duration</th>
                                    <th>Last Salary</th>
                                    <th>Reasoned for Leaving</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="1" onclick="getExperienceInfoTable()">Close</button>
            </div>
        </div>
    </div>
</div>
<!--Experience Modal End-->