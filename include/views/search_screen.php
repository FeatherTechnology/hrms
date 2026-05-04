<div class="row gutters">
    <div class="col-12">
        <!----------------------------- CARD START  SEARCH FORM ------------------------------>
        <div>
            <form id="search_form" name="search_form" method="post" enctype="multipart/form-data">
                <!-- Row start -->
                <input type="hidden" name="pending_sts" id="pending_sts" value="" />
                <input type="hidden" name="od_sts" id="od_sts" value="" />
                <input type="hidden" name="due_nil_sts" id="due_nil_sts" value="" />
                <input type="hidden" name="bal_amt" id="bal_amt" value="bal_amt" />
                <div class="row gutters">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Search Customer</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Fields -->
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="aadhar_nums"> Aadhar Number</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control personal_info_disble" name="aadhar_nums" id="aadhar_nums" tabindex="2" maxlength="14" data-type="adhaar-number" placeholder="Enter Aadhar Number">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="cust_id">Customer ID</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="cust_id" name="cust_id" placeholder="Enter Customer ID" tabindex="1">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="cust_name">Customer Name</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="cust_name" name="cust_name" placeholder="Enter Customer Name" tabindex="2">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="cus_area">Area</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="cus_area" name="cus_area" placeholder="Enter Area" tabindex="3">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-group">
                                            <label for="cus_mobile">Mobile Number</label><span class="text-danger">*</span>
                                            <input type="number" class="form-control" id="cus_mobile" name="cus_mobile" placeholder="Enter Mobile Number" tabindex="4" onKeyPress="if(this.value.length==10) return false;">
                                        </div>
                                    </div>
                                    <div class="col-12 mt-3 text-right">
                                        <button name="submit_search" id="submit_search" class="btn btn-primary" tabindex="5"><span class="icon-check"></span>&nbsp;Search</button>
                                        <button type="reset" class="btn btn-outline-secondary" tabindex="6">Clear</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!----------------------------- CARD END SEARCH FORM------------------------------>
            <div class="card" id="custome_list" style="display:none">
                <div class="card-header">
                    <h5 class="card-title">Customer List</h5>
                </div>
                <div class="card-body">
                    <div class="col-12">
                        <table id="search_table" class="table custom-table">
                            <thead>
                                <th width="20">S No.</th>
                                <th>Customer ID</th>
                                <th>Aadhar Number</th>
                                <th>Customer Name</th>
                                <th>Area</th>
                                <th>Branch</th>
                                <th>Line</th>
                                <th>Mobile Number</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card mt-3" id="customer_status" style="display:none">
                <div class="card-header">
                    <h5 class="card-title">Customer Status&nbsp;<button type="button" id="back_to_search" style="float:right" class="btn btn-primary"><span class="icon-arrow-left"></span>&nbsp;Back</button></h5>
                </div>
                <div class="card-body">
                    <div class="col-12">
                        <table id="status_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th>S No.</th>
                                    <th>Date</th>
                                    <th>Loan ID</th>
                                    <th>Loan Category</th>
                                    <th>Loan Amount</th>
                                    <th colspan="2">Loan Status</th>
                                    <th colspan="2">Details</th>
                                </tr>
                                <tr>
                                    <th colspan="5"></th>
                                    <th>Status</th>
                                    <th>Sub Status</th>
                                    <th>Info</th>
                                    <th>Charts</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row gutters" id="noc_summary" style="display:none">
                <input type="hidden" id="cp_id">
                <div class="col-12">
                    <div class="card">
                        <!-- style="box-shadow: none;background-color: transparent;" -->
                        <div class="card-header">
                            <h5 class="card-title">NOC Summary&nbsp;<button type="button" id="back_to_cus_status" style="float:right" class="btn btn-primary "><span class="icon-arrow-left"></span>&nbsp;Back</button></h5>
                        </div>
                        <div class="card-body">
                            <div class="card cheque-div" style="display:none">
                                <div class="card-header">
                                    <h5 class="card-title">Cheque List</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table custom-table" id="noc_cheque_list_table">
                                        <thead>
                                            <th>S No.</th>
                                            <th>Holder Type</th>
                                            <th>Holder Name</th>
                                            <th>Relationship</th>
                                            <th>Bank Name</th>
                                            <th>Cheque No.</th>
                                            <th>Date of NOC</th>
                                            <th>Handover Person</th>
                                            <th>Relationship</th>
                                            <th>Checklist</th>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card mortgage-div" style="display:none">
                                <div class="card-header">
                                    <h5 class="card-title">Mortgage List</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table custom-table" id="noc_mortgage_list_table">
                                        <thead>
                                            <th>S No.</th>
                                            <th>Property Holder Name</th>
                                            <th>Relationship</th>
                                            <th>Property Details</th>
                                            <th>Mortgage Name</th>
                                            <th>Desigantion</th>
                                            <th>Reg Office</th>
                                            <th>Date of NOC</th>
                                            <th>Handover Person</th>
                                            <th>Relationship</th>
                                            <th>Checklist</th>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card endorsement-div" style="display:none">
                                <div class="card-header">
                                    <h5 class="card-title">Endorsement List</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table custom-table" id="noc_endorsement_list_table">
                                        <thead>
                                            <th>S No.</th>
                                            <th>Owner Name</th>
                                            <th>Relationship</th>
                                            <th>Vehicle Details</th>
                                            <th>Endorsement Name</th>
                                            <th>RC</th>
                                            <th>KEY</th>
                                            <th>Date of NOC</th>
                                            <th>Handover Person</th>
                                            <th>Relationship</th>
                                            <th>Checklist</th>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card doc_div" style="display:none">
                                <div class="card-header">
                                    <h5 class="card-title">Other Document List</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table custom-table" id="noc_document_list_table">
                                        <thead>
                                            <th>S No.</th>
                                            <th>Document Name</th>
                                            <th>Document Type</th>
                                            <th>Document Holder</th>
                                            <th>Document</th>
                                            <th>Date of NOC</th>
                                            <th>Handover Person</th>
                                            <th>Relationship</th>
                                            <th>Checklist</th>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card gold-div" style="display:none">
                                <div class="card-header">
                                    <h5 class="card-title">Gold List</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table custom-table" id="noc_gold_list_table">
                                        <thead>
                                            <th>S No.</th>
                                            <th>Gold Type</th>
                                            <th>Purity</th>
                                            <th>Weight</th>
                                            <th>Date of NOC</th>
                                            <th>Handover Person</th>
                                            <th>Relationship</th>
                                            <th>Checklist</th>
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
    </div>

</div>

<div id="printcollection" style="display: none"></div>

<!-- /////////////////////////////////////////////////////////////////// Closed Remark Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="closed_remark_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Closed Remark</h5>
                <button type="button" class="close" data-dismiss="modal" onclick="closeChartsModal()" tabindex="1" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="closed_remark_form" method="post">
                        <input type="hidden" id="cus_profile_id">
                        <div class="row">
                            <div class="col-sm-2 col-md-2 col-lg-2"></div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="sub_status">Sub Status</label>
                                    <input class="form-control" name="sub_status" id="sub_status" tabindex="2" disabled>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="remark">Remark</label>
                                    <textarea class="form-control" name="remark" id="remark" tabindex="3" disabled></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" onclick="closeChartsModal()" tabindex="4">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Closed Remark Modal END ////////////////////////////////////////////////////////////////////// -->

<!-- /////////////////////////////////////////////////////////////////// Due Chart Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade bd-example-modal-lg" id="due_chart_model" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document" style="max-width: 70% !important">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Due Chart</h5>
                <button type="button" class="close" data-dismiss="modal" onclick="closeChartsModal()" tabindex="1" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid" id="due_chart_table_div">
                    <table class="table custom-table">
                        <thead>
                            <th>Due No.</th>
                            <th>Due Month</th>
                            <th>Month</th>
                            <th>Due Amount</th>
                            <th>Pending</th>
                            <th>Payable</th>
                            <th>Collection Date</th>
                            <th>Collection Amount</th>
                            <th>Balance Amount</th>
                            <th>Pre Closure</th>
                            <th>Role</th>
                            <th>User ID</th>
                            <th>Collection Method</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" onclick="closeChartsModal()" tabindex="4">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Due Chart Modal END ////////////////////////////////////////////////////////////////////// -->
<!-- /////////////////////////////////////////////////////////////////// Penalty Chart Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="penalty_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Penalty Chart</h5>
                <button type="button" class="close" data-dismiss="modal" onclick="closeChartsModal()" tabindex="1" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid" id="penalty_chart_table_div">
                    <div class="row">
                        <table class="table custom-table">
                            <thead>
                                <th>S No.</th>
                                <th>Penalty Date</th>
                                <th>Penalty</th>
                                <th>Paid Date</th>
                                <th>Paid Amount</th>
                                <th>Balance Amount</th>
                                <th>Waiver Amount</th>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" onclick="closeChartsModal()" tabindex="4">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Penalty Chart Modal END ////////////////////////////////////////////////////////////////////// -->
<!-- /////////////////////////////////////////////////////////////////// Fine Chart Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="fine_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Fine Chart</h5>
                <button type="button" class="close" data-dismiss="modal" onclick="closeChartsModal()" tabindex="1" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="overflow-x: auto;">
                <div class="container-fluid" id="fine_chart_table_div">
                    <div class="row">
                        <table class="table custom-table">
                            <thead>
                                <th>S No.</th>
                                <th>Date</th>
                                <th>Fine</th>
                                <th>Purpose</th>
                                <th>Paid Date</th>
                                <th>Paid Amount</th>
                                <th>Balance Amount</th>
                                <th>Waiver Amount</th>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" onclick="closeChartsModal()" tabindex="4">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Fine Chart Modal END ////////////////////////////////////////////////////////////////////// -->
<!-- /////////////////////////////////////////////////////////////////// customer Profile start ////////////////////////////////////////////////////////////////////// -->
<div id="loan_entry_content" style="display:none;">
    <div class="text-right">
        <button type="button" class="btn btn-primary" id="back_btn"><span class="icon-arrow-left"></span>&nbsp; Back </button>
        <br><br>
    </div>
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
                                            <label for="cus_id"> Customer ID</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control personal_info_disble" id="cus_id" name="cus_id" data-type="adhaar-number" placeholder="Enter Customer ID" tabindex="1" maxlength="14">
                                            <input type="hidden" id="cus_id_upd" name="cus_id_upd">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="adhar_num"> Aadhar Number</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control personal_info_disble" name="adhar_num" id="adhar_num" tabindex="2" maxlength="14" data-type="adhaar-number" placeholder="Enter Aadhar Number">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="cus_name"> Customer Name</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control personal_info_disble" id="cus_name" name="cus_name" pattern="[a-zA-Z\s]+" placeholder="Enter Customer Name" tabindex=" 2">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="gender">Gender</label><span class="text-danger">*</span>
                                            <select type="text" class="form-control  personal_info_disble" id="gender" name="gender" tabindex="3">
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
                                            <input type="date" class="form-control  personal_info_disble" id="dob" name="dob" placeholder="Enter Date Of Birth" tabindex="4">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="age"> Age</label>
                                            <input type="number" class="form-control  personal_info_disble" id="age" name="age" readonly placeholder="Age" tabindex="5">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="mobile1"> Mobile Number 1</label><span class="text-danger">*</span>
                                            <input type="number" class="form-control  personal_info_disble" id="mobile1" name="mobile1" placeholder="Enter Mobile Number 1" onKeyPress="if(this.value.length==10) return false;" tabindex="6">
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="mobile2"> Mobile Number 2</label>
                                            <input type="number" class="form-control  personal_info_disble" id="mobile2" name="mobile2" onKeyPress="if(this.value.length==10) return false;" placeholder="Enter Mobile Number 2" tabindex="7">
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
                                            <input type="hidden" class="personal_info_disble" id="per_pic">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Family Info
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
                                            <label for="guarantor_name"> Guarantor Name</label><span class="text-danger" disabled>*</span>
                                            <input type="hidden" id="guarantor_name_edit">
                                            <select type="text" class="form-control" id="guarantor_name" name="guarantor_name" disabled tabindex="10">
                                                <option value="">Select Guarantor Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="relationship"> Relationship</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="relationship" name="relationship" pattern="[a-zA-Z\s]+" disabled placeholder="Enter Relationship" tabindex="11">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label for="pic"> Photo</label><span class="text-danger">*</span><br>
                                            <img id='gur_imgshow' class="img_show" src='img\avatar.png' />
                                            <input type="hidden" id="gur_pic">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Data Analyis</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cus_data"> Customer Data</label>
                                    <input type="text" class="form-control" id="cus_data" name="cus_data" disabled placeholder="New/Existing" disabled tabindex="13">
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12" style="display:none;">
                                <div class="form-group">
                                    <label for="cus_status"> Customer Status</label>
                                    <input type="text" class="form-control" id="cus_status" name="cus_status" disabled placeholder="Additional/Renewal" tabindex="14">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="data_checking_div" style="display: none;">
                        <div class="card-header">
                            <div class="card-title">Data Checking</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="name_check">Name</label>
                                        <select type="text" class="form-control" id="name_check" name="name_check" disabled tabindex="15">
                                            <option value="">Select Name</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="aadhar_check">Aadhar</label>
                                        <select type="text" class="form-control" id="aadhar_check" name="aadhar_check" disabled tabindex="16">
                                            <option value="">Select Aadhar</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="mobile_check">Mobile</label>
                                        <select type="text" class="form-control" id="mobile_check" name="mobile_check" disabled tabindex="17">
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
                                                    <th>Customer ID</th>
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

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Resident Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="res_type">Residential Type</label>
                                    <select type="text" class="form-control" id="res_type" name="res_type" tabindex="18" disabled>
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
                                    <label for="res_detail"> Residential Details </label>
                                    <input type="text" class="form-control" id="res_detail" name="res_detail" pattern="[a-zA-Z\s]+" placeholder="Enter Residential Details" disabled tabindex="19">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="res_address"> Address </label>
                                    <input type="text" class="form-control" id="res_address" name="res_address" placeholder="Enter Address" disabled tabindex="20">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="native_address"> Native Address </label>
                                    <input type="text" class="form-control" id="native_address" name="native_address" placeholder="Enter Native Address" disabled tabindex="21">
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
                                    <input type="text" class="form-control" id="occupation" name="occupation" pattern="[a-zA-Z\s]+" disabled placeholder="Enter Occupation" tabindex="22">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="occ_detail"> Occupation Detail</label>
                                    <input type="text" class="form-control" id="occ_detail" name="occ_detail" disabled placeholder="Enter Occupation Detail " tabindex="23">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="occ_income"> Income</label>
                                    <input type="text" class="form-control" id="occ_income" name="occ_income" disabled placeholder="Enter Income"  tabindex="24">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="occ_address"> Address </label>
                                    <input type="text" class="form-control" id="occ_address" name="occ_address" disabled placeholder="Enter Address" tabindex="25">
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
                                    <select type="text" class="form-control" id="area_confirm" name="area_confirm" disabled tabindex="26">
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
                                    <select type="text" class="form-control" id="area" name="area" tabindex="27" disabled>
                                        <option value="">Select Area</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="line"> Line </label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="line" name="line" disabled placeholder="Enter line" tabindex="28">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Property Info

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
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="cus_limit"> Customer Limit</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="cus_limit" name="cus_limit" disabled placeholder="Enter Limit" tabindex="32">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="about_cus"> About Customer </label>
                                    <textarea class="form-control" name="about_cus" id="about_cus" disabled placeholder="Enter About Customer" tabindex="33"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- /////////////////////////////////////////////////////////////////// customer Profile END ////////////////////////////////////////////////////////////////////// -->
<!-- /////////////////////////////////////////////////////////////////// Loan CalCulation Start ////////////////////////////////////////////////////////////////////// -->
<div id="loan_content" style="display:none;">
    <div class="text-right">
        <button type="button" class="btn btn-primary" id="loan_back_btn"><span class="icon-arrow-left"></span>&nbsp; Back </button>
        <br><br>
    </div>
    <form id="loan_entry_loan_calculation" name="loan_entry_loan_calculation">
        <input type="hidden" id="loan_calculation_id">
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
                                    <input type="text" class="form-control" id="loan_id_calc" name="loan_id_calc" disabled tabindex="1" value="LD" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_category_calc"> Loan Category</label><span class="text-danger">*</span>
                                    <input type="hidden" id="loan_category_calc2">
                                    <select class="form-control" id="loan_category_calc" name="loan_category_calc" disabled tabindex="2">
                                        <option value="">Select Loan Category</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="category_info_calc">Category Info</label>
                                    <textarea class="form-control" id="category_info_calc" name="category_info_calc" disabled tabindex="3"></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="loan_amount_calc"> Loan Amount</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="loan_amount_calc" name="loan_amount_calc" disabled tabindex="4">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="profit_type_calc">Profit Type</label><span class="text-danger">*</span>
                                    <select class="form-control" id="profit_type_calc" name="profit_type_calc" disabled tabindex="5">
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
                                    <input type="text" class="form-control" id="due_method_calc" name="due_method_calc" disabled value="Monthly" tabindex="6" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 calc" style="display:none">
                                <div class="form-group">
                                    <label for="due_type_calc">Due Type</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="due_type_calc" name="due_type_calc" disabled tabindex="7" readonly>
                                </div>
                            </div>
                             <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 interest_type" style="display:none">
                                <div class="form-group">
                                    <label for="interest_calculate">Interest Calculate</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="interest_calculate" name="interest_calculate" tabindex="7" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 calc" style="display:none">
                                <div class="form-group">
                                    <label for="profit_method_calc">Profit Method</label><span class="text-danger">*</span>
                                    <input type="text" class="form-control" id="profit_method_calc" name="profit_method_calc" disabled tabindex="8" value="After Benefit" readonly>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 scheme" style="display:none">
                                <div class="form-group">
                                    <label for="scheme_due_method_calc">Due Method</label><span class="text-danger">*</span>
                                    <select class="form-control" id="scheme_due_method_calc" name="scheme_due_method_calc" disabled tabindex="6">
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
                                    <select class="form-control to_clear" id="scheme_day_calc" name="scheme_day_calc" disabled tabindex="7">
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
                                    <select class="form-control to_clear" id="scheme_name_calc" name="scheme_name_calc" disabled tabindex="8">
                                        <option value="">Select Scheme Name</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="interest_rate_calc">Interest Rate</label><span class="text-danger min-max-int">*</span><!-- Min and max intrest rate-->
                                    <input type="number" class="form-control to_clear" id="interest_rate_calc" name="interest_rate_calc" disabled tabindex="9">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="due_period_calc">Due Period</label><span class="text-danger min-max-due">*</span><!-- Min and max Profit Method-->
                                    <input type="number" class="form-control to_clear" id="due_period_calc" name="due_period_calc" disabled tabindex="10">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="doc_charge_calc">Document Charges</label><span class="text-danger min-max-doc">*</span><!-- Min and max Document charges-->
                                    <input type="number" class="form-control to_clear" id="doc_charge_calc" name="doc_charge_calc" disabled tabindex="11">
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="processing_fees_calc">Processing Fees</label><span class="text-danger min-max-proc">*</span><!-- Min and max Processing fee-->
                                    <input type="number" class="form-control to_clear" id="processing_fees_calc" name="processing_fees_calc" disabled tabindex="12">
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
                                    <label for="loan_date_calc">Loan date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="loan_date_calc" name="loan_date_calc" tabindex="22" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="due_startdate_calc">Due Start Date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="due_startdate_calc" name="due_startdate_calc" disabled tabindex="23">
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
                                    <select class="form-control" id="referred_calc" name="referred_calc" disabled tabindex="25">
                                        <option value="">Select Referred</option>
                                        <option value="0">Yes</option>
                                        <option value="1">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="agent_id_calc">Agent ID</label><span class="text-danger">*</span>
                                    <select class="form-control" id="agent_id_calc" name="agent_id_calc" disabled tabindex="26">
                                        <option value="">Select Agent ID</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="agent_name_calc">Agent Name</label><span class="text-danger">*</span>
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
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <table id="doc_need_table" class="table custom-table">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Document Name</th>
                                        </tr>
                                    </thead>
                                    <tbody> </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <!--- -------------------------------------- Documents END ------------------------------- -->
            </div>
        </div>
    </form>
</div>




<!-- /////////////////////////////////////////////////////////////////// Loan Calculation END ////////////////////////////////////////////////////////////////////// -->
<!-- /////////////////////////////////////////////////////////////////// Documentation Start ////////////////////////////////////////////////////////////////////// -->
<div id="loan_issue_content" style="display:none;">
    <div class="text-right">
        <button type="button" class="btn btn-primary" id="doc_back_btn"><span class="icon-arrow-left"></span>&nbsp; Back </button>
        <br><br>
    </div>
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
                                <table id="doc_table" class="table custom-table">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
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

                <!--- -------------------------------------- Cheque Info START ------------------------------- -->
                <div class="card cheque-div" style="display: none;">
                    <div class="card-header">
                        <div class="card-title">Cheque Info</div>
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


</div>

<!-- /////////////////////////////////////////////////////////////////// Documentation End ////////////////////////////////////////////////////////////////////// -->