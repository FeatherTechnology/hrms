<div class="row gutters">
    <div class="col-12">
        <div class="card" id="closed_list">
            <div class="card-header">
                <h5 class="card-title">Closed List</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <table id="closed_list_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th width="50">S.No.</th>
                                    <th>Customer ID</th>
                                    <th>Aadhar Number</th>
                                    <th>Customer Name</th>
                                    <th>Area</th>
                                    <th>Line</th>
                                    <th>Branch</th>
                                    <th>Mobile No</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 text-right back_to_closed_list" style="margin-bottom:10px">
            <button class="btn btn-primary back_to_closed_list" id="back_to_closed_list" style="display: none;"><span class="icon-arrow-left"></span> Back</button>
        </div>
        <div id="closed_main_container" style="display:none">
            <!-- Row start -->
            <div class="row gutters" id="personal_info">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Personal Info</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-8">
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="aadhar_num">Aadhar Number</label><span class="text-danger">*</span>
                                                <input type="text" class="form-control personal_info_disble" name="aadhar_num" id="aadhar_num" tabindex="2" disabled>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="cus_id">Customer ID</label>
                                                <input type="text" class="form-control" id="cus_id" name="cus_id" tabindex="1" disabled>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="cus_name">Customer Name</label>
                                                <input type="text" class="form-control" id="cus_name" name="cus_name" pattern="[a-zA-Z\s]+" tabindex="2" disabled>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="cus_area">Area</label>
                                                <input type="text" class="form-control" id="area" name="area" tabindex="3" disabled>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="cus_branch">Branch</label>
                                                <input type="text" class="form-control" id="branch_name" name="branch_name" tabindex="4" disabled>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="cus_line">Line</label>
                                                <input type="text" class="form-control" id="line" name="line" tabindex="5" disabled>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="mobile1"> Mobile No</label>
                                                <input type="number" class="form-control" id="mobile1" name="mobile1" tabindex="6" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="pic"> Photo</label><br>
                                                <img id='imgshow' class="img_show" src='img\avatar.png' />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" id="loan_list">
                <div class="card-header">
                    <h5 class="card-title">Loan List</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table id="close_loan_table" class=" table custom-table">
                                <thead>
                                    <th width="50">S.No.</th>
                                    <th>Loan ID</th>
                                    <th>Loan Category</th>
                                    <th>Loan Date</th>
                                    <th>Closed Date</th>
                                    <th>Loan Amount</th>
                                    <th>Status</th>
                                    <th>Sub Status</th>
                                    <th>Charts</th>
                                    <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="closeChartsModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="closed_remark_form" method="post">
                        <input type="hidden" id="cus_profile_id">
                        <div class="col-12 row">
                            <div class="col-sm-2 col-md-2 col-lg-2"></div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="sub_status">Sub Status</label><span class="required">*</span>
                                    <select name="sub_status" id="sub_status" class="form-control" tabindex="2">
                                        <option value="">Select Sub Status</option>
                                        <option value="1">Consider</option>
                                        <option value="2">Reject</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <div class="form-group">
                                    <label for="remark">Remark</label>
                                    <textarea class="form-control" name="remark" id="remark" tabindex="3" placeholder="Enter Remarks"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <div class="modal-footer">
                <button name="submit_closed_remark" id="submit_closed_remark" class="btn btn-primary" tabindex="4"><span class="icon-check"></span>&nbsp;Submit</button>
                <button class="btn btn-secondary" data-dismiss="modal" onclick="closeChartsModal()" tabindex="6">Close</button>
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
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="closeChartsModal()">
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
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="closeChartsModal()">
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
                <button type="button" class="close" data-dismiss="modal" tabindex="1" aria-label="Close" onclick="closeChartsModal()">
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