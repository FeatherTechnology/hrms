<div class="row gutters">
    <div class="col-12">
        <div class="card" id="collection_list">
			<div class="d-flex justify-content-between align-items-center m-3">
				<h5 class="card-title mb-0" style="padding-left: 10px;">Collection List</h5>
				<div class="dropdown">
					<button class="border-0 bg-transparent" type="button" id="filterDropdown" data-bs-toggle="dropdown"
						aria-expanded="false">
						<i class="fa fa-filter" style="color:#7ca5b8; font-size:35px; margin-right: 10px;"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
						<li>
							<a class="dropdown-item filter-option" id="due_nill_btn" value="Due Nil" data-filter="Due Nil">Due Nil</a>
						</li>
						<li>
							<a class="dropdown-item filter-option" id="all_btn" value="All" style="display: none;" data-filter="All">All</a>
						</li>
					</ul>
				</div>
			</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <table id="collection_list_table" class="table custom-table">
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
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 text-right" style="margin-bottom:10px">
            <button class="btn btn-primary" id="back_to_coll_list" style="display: none;"><span class="icon-arrow-left"></span> Back</button>
            <button class="btn btn-primary" id="back_to_loan_list" style="display: none;"><span class="icon-cancel"></span> cancel</button>
        </div>

        <div id="coll_main_container" style="display:none">
            <!-- Row start -->
            <div class="row gutters colls-cntnr">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Personal Info</h5>
                        </div>
                        <input type="hidden" name="pending_sts" id="pending_sts" value="" />
                        <input type="hidden" name="od_sts" id="od_sts" value="" />
                        <input type="hidden" name="due_nil_sts" id="due_nil_sts" value="" />
                        <input type="hidden" name="coll_sts" id="coll_sts" />
                       

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
                                                <input type="text" class="form-control" id="cus_name" name="cus_name" pattern="[a-zA-Z\s]+" tabindex="3" disabled>
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="cus_area">Area</label>
                                                <input type="text" class="form-control" id="cus_area" name="cus_area" tabindex="4" disabled>
                                                <input type="hidden" id="area_id" name="area_id">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="cus_branch">Branch</label>
                                                <input type="text" class="form-control" id="cus_branch" name="cus_branch" tabindex="5" disabled>
                                                <input type="hidden" id="branch_id" name="branch_id">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="cus_line">Line</label>
                                                <input type="text" class="form-control" id="cus_line" name="cus_line" tabindex="6" disabled>
                                                <input type="hidden" id="line_id" name="line_id">
                                            </div>
                                        </div>
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="cus_mobile"> Mobile No</label>
                                                <input type="number" class="form-control" id="cus_mobile" name="cus_mobile" tabindex="7" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="row">
                                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                            <div class="form-group">
                                                <label for="pic"> Photo</label><br>
                                                <img id='cus_image' class="img_show" src='img\avatar.png' />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Loan List</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <table id="loan_list_table" class=" table custom-table">
                                        <thead>
                                            <th width="50">S.No.</th>
                                            <th>Loan ID</th>
                                            <th>Loan Category</th>
                                            <th>Loan Date</th>
                                            <th>Loan Amount</th>
                                            <th>Balance Amount</th>
                                            <th>Status</th>
                                            <th>Sub Status</th>
                                            <th>Charts</th>
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
            <!-- /////////////////////////////////////////////////// Collection Info  START ///////////////////////////////////////// -->
            <div class="card coll_details" style="display: none;">
                <div class="card-header">
                    <h5 class="card-title">Collection Info</h5>
                </div>

                <input type="hidden" name="loan_category_id" id="loan_category_id">
                <input type="hidden" name="cp_id" id="cp_id">
                <input type="hidden" name="status" id="status">
                <input type="hidden" name="sub_status" id="sub_status">
                <input type="hidden" name="loan_type" id="loan_type" >
                <input type="hidden" name="bank_clr_bank_id" id="bank_clr_bank_id"/>
                <input type="hidden" name="bank_clr_trans_amnt" id="bank_clr_trans_amnt"/>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Total Amount</label>&nbsp;<span class="text-danger totspan">*</span>
                                        <input type="text" class="form-control" readonly id="tot_amt" name="tot_amt" value='' tabindex='8'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Paid Amount</label>&nbsp;<span class="text-danger paidspan">*</span>
                                        <input type="text" class="form-control" readonly id="paid_amt" name="paid_amt" value='' tabindex='9'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Balance Amount</label>&nbsp;<span class="text-danger balspan">*</span>
                                        <input type="text" class="form-control" readonly id="bal_amt" name="bal_amt" value='' tabindex='10'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Due Amount</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" class="form-control" readonly id="due_amt" name="due_amt" value='' tabindex='11'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Pending Amount</label>&nbsp;<span class="text-danger pendingspan">*</span>
                                        <input type="text" class="form-control" readonly id="pending_amt" name="pending_amt" value='' tabindex='12'>
                                        <input type="hidden" class="form-control" readonly id="pend_amt" name="pend_amt">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Payable Amount</label>&nbsp;<span class="text-danger payablespan">*</span>
                                        <input type="text" class="form-control" readonly id="payable_amt" name="payable_amt" value='' tabindex='13'>
                                        <input type="hidden" class="form-control" readonly id="payableAmount" name="payableAmount">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 till-date-int">
                                    <div class="form-group">
                                        <label for="disabledInput">Till Date Interest</label>&nbsp;<span class="text-danger ">*</span>
                                        <input type="text" class="form-control" readonly id="till_date_int" name="till_date_int" value='' tabindex='19'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Penalty</label>&nbsp;<span class="text-danger ">*</span>
                                        <input type="text" class="form-control" readonly id="penalty" name="penalty" value='' tabindex='14'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Fine</label>&nbsp;<span class="text-danger ">*</span>
                                        <input type="text" class="form-control" readonly id="coll_charge" name="coll_charge" value='' tabindex='15'>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /////////////////////////////////////////////////// Collection Info END ///////////////////////////////////////// -->

            <!-- /////////////////////////////////////////////////// Collection Track START ///////////////////////////////////////// -->
            <div class="card coll_details" style="display: none;">
                <div class="card-header">
                    <div class="card-title">Collection Track</div>
                </div>
                <div class="card-body">
                    <div class="row ">
                        <!--Fields -->
                        <div class="col-md-12 ">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 emiLoanDiv">
                                    <div class="form-group">
                                        <label for="disabledInput">Due Amount</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" class="form-control clearFields" id="due_amt_track" name="due_amt_track" value='' placeholder='Enter Due Amount'  onkeyup="this.value = this.value.replace(/[^0-9]/g,'');" tabindex='16'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 intLoanDiv" style="display: none;">
                                    <div class="form-group">
                                        <label for="disabledInput">Principal Amount</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" class="form-control clearFields" id="princ_amt_track" name="princ_amt_track" value='' placeholder='Enter Principal Amount' onkeyup="this.value = this.value.replace(/[^0-9]/g,'');" tabindex='17'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 intLoanDiv" style="display: none;">
                                    <div class="form-group">
                                        <label for="disabledInput">Interest Amount</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" class="form-control clearFields" id="int_amt_track" name="int_amt_track" value='' placeholder='Enter Interest Amount' onkeyup="this.value = this.value.replace(/[^0-9]/g,'');" tabindex='18'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Penalty</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" class="form-control clearFields" id="penalty_track" name="penalty_track" value='' placeholder='Enter Penalty Amount' onkeyup="this.value = this.value.replace(/[^0-9]/g,'');" tabindex='19'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Fine</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" class="form-control clearFields" id="coll_charge_track" name="coll_charge_track" value='' placeholder='Enter Fine' onkeyup="this.value = this.value.replace(/[^0-9]/g,'');" tabindex='20'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Total Paid</label>
                                        <input type="text" readonly class="form-control clearFields" id="total_paid_track" name="total_paid_track" value='' tabindex='21'>
                                    </div>
                                </div>
                            </div>

                            <!-- Waiver Access if the user have collection access. -->
                            <div class="row collection_access_div">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 emiLoanDiv">
                                    <div class="form-group">
                                        <label for="disabledInput">Pre Closure</label>
                                        <input type="text" class="form-control clearFields" id="pre_close_waiver" name="pre_close_waiver" value='' placeholder='Enter Pre Closure Amount' onkeyup="this.value = this.value.replace(/[^0-9]/g,'');" tabindex='22'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 intLoanDiv" style="display: none;">
                                    <div class="form-group">
                                        <label for="disabledInput">Principle Waiver</label>
                                        <input type="text" class="form-control clearFields" id="principal_waiver" name="principal_waiver" value='' placeholder='Enter Principle Waiver' onkeyup="this.value = this.value.replace(/[^0-9]/g,'');" tabindex='28'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 intLoanDiv" style="display: none;">
                                    <div class="form-group">
                                        <label for="disabledInput">Interest Waiver</label>
                                        <input type="text" class="form-control clearFields" id="interest_waiver" name="interest_waiver" value='' placeholder='Enter Interest Waiver' onkeyup="this.value = this.value.replace(/[^0-9]/g,'');" tabindex='28'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Penalty Waiver</label>
                                        <input type="text" class="form-control clearFields" id="penalty_waiver" name="penalty_waiver" value='' placeholder='Enter Penalty Waiver' onkeyup="this.value = this.value.replace(/[^0-9]/g,'');" tabindex='23'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Fine Waiver</label>
                                        <input type="text" class="form-control clearFields" id="coll_charge_waiver" name="coll_charge_waiver" value='' placeholder='Enter Fine Waiver' onkeyup="this.value = this.value.replace(/[^0-9]/g,'');" tabindex='24'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Total Waiver</label>
                                        <input type="text" readonly class="form-control clearFields" id="total_waiver" name="total_waiver" value='' tabindex='25'>
                                    </div>
                                </div>
                            </div>
                            <!-- Waiver Access if the user have collection access. -->

                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Collection Date</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" readonly class="form-control" id="collection_date" name="collection_date" tabindex='26'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Collection ID</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" readonly class="form-control" id="collection_id" name="collection_id" value='' tabindex='27'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="disabledInput">Collection Mode</label>&nbsp;<span class="text-danger">*</span>
                                        <select class='form-control clearFields' id='collection_mode' name='collection_mode' tabindex='28'>
                                            <option value=''>Select Collection Mode</option>
                                            <option value='1'>Cash</option>
                                            <option value='2'>Cheque</option>
                                            <option value='3'>ECS</option>
                                            <option value='4'>IMPS/NEFT/RTGS</option>
                                            <option value='5'>UPI Transaction</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 cheque transaction" style="display:none">
                                    <div class="form-group">
                                        <label for="disabledInput">Bank Name</label>&nbsp;<span class="text-danger">*</span>
                                        <select class='form-control' id='bank_id' name='bank_id' tabindex='29'>
                                            <option value=''>Select Bank Name</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 cheque" style="display:none">
                                    <div class="form-group">
                                        <label for="disabledInput">Cheque No</label>&nbsp;<span class="text-danger chequeSpan">*</span>
                                        <select class='form-control' id='cheque_no' name='cheque_no' tabindex='30'>
                                            <option value=''>Select Cheque No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 transaction" style="display:none">
                                    <div class="form-group">
                                        <label for="disabledInput">Transaction ID</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="text" class="form-control" id="trans_id" name="trans_id" value='' placeholder="Enter Transaction ID" tabindex='31'>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 transaction" style="display:none">
                                    <div class="form-group">
                                        <label for="disabledInput">Transaction Date</label>&nbsp;<span class="text-danger">*</span>
                                        <input type="date" class="form-control" id="trans_date" name="trans_date" value='' tabindex='32' readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /////////////////////////////////////////////////// Collection Track END ///////////////////////////////////////// -->

            <!-- Submit Button Start -->
            <div class="col-md-12 coll_details" style="display: none;">
                <div class="text-right">
                    <button type="submit" name="submit_collection" id="submit_collection" class="btn btn-primary" value="Submit" tabindex='33'><span class="icon-check"></span>&nbsp;Submit</button>
                </div>
            </div>
            <!-- Submit Button End -->

        </div>

    </div>
</div>
<div id="printcollection" style="display: none"></div>
<!-- /////////////////////////////////////////////////////////////////// Due Chart Modal Start ////////////////////////////////////////////////////////////////////// -->
<div class="modal fade bd-example-modal-lg" id="due_chart_model" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document" style="max-width: 70% !important">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="dueChartTitle">Due Chart</h5>
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
                <div class="container-fluid">
                    <div class="row" id="penalty_chart_table_div">
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
            <div class="modal-body overflow-x-cls" id="fine_chart_table_div">
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
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" onclick="closeChartsModal()" tabindex="4">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Fine Chart Modal END ////////////////////////////////////////////////////////////////////// -->

<!-- /////////////////////////////////////////////////////////////////// Fine Add Modal START ////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="fine_form_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Add Fine</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeFineChartModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="coll_date "> Date </label> <span class="required">&nbsp;*</span>
                            <input type="hidden" class="form-control" id="fine_cp_id" name="fine_cp_id">
                            <input type="text" class="form-control" id="fine_date" name="fine_date" readonly tabindex='1'>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="coll_purpose"> Purpose </label> <span class="required">&nbsp;*</span>
                            <input type="text" class="form-control" id="fine_purpose" name="fine_purpose" placeholder="Enter Purpose" onkeydown="return /[a-z ]/i.test(event.key)" tabindex='2'>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="form-group">
                            <label for="coll_amnt"> Amount </label> <span class="required">&nbsp;*</span>
                            <input type="number" class="form-control" id="fine_Amnt" name="fine_Amnt" placeholder="Enter Amount" tabindex='3'>
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-4 col-12">
                        <button type="button" tabindex="4" name="fine_form_submit" id="fine_form_submit" class="btn btn-primary" style="margin-top: 19px;">Submit</button>
                    </div>
                </div>
                </br>
                <div>
                    <table id="fine_form_table" class="table custom-table">
                        <thead>
                            <tr>
                                <th width="15%"> S.No </th>
                                <th> Date </th>
                                <th> Purpose </th>
                                <th> Amount </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeFineChartModal()">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /////////////////////////////////////////////////////////////////// Fine Add Modal END ////////////////////////////////////////////////////////////////////// -->