<div class="row gutters">
    <div class="col-12">
        <div class="col-12 text-right">
            <button class="btn btn-primary add_bc_btn"><span class="icon-add"></span>Add Bank Clearance</button>
            <button class="btn btn-primary back_to_bcList_btn" style="display: none;"><span class="icon-arrow-left"></span>Back</button>
        </div></br>
        <!----------------------------- CARD START  BANK CLEARANCE VIEW ------------------------------>
        <div class="card bank_clearance_view_content">
            <!--- ---------------------- Transaction Details START----------------------------- -->
            <div class="card-header">
                <h5 class="card-title">Transaction Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label for="bank_name">Bank Name</label><span class="text-danger">*</span>
                                    <select class="form-control clr-trans-detail" id="bank_name" name="bank_name" tabindex="1">
                                        <option value="">Select Bank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label for="from_date">From Date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control clr-trans-detail" id="from_date" name="from_date" tabindex="2">
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label for="to_date">To Date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control clr-trans-detail" id="to_date" name="to_date" tabindex="3">
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <div class="form-group">
                                    <label for="view_btn" style="visibility: hidden;">View</label></br>
                                    <button class="btn btn-primary" id="view_btn" name="view_btn" tabindex="4">View</button>
                                </div>
                            </div>
                        </div>
                        <!--- ---------------------- Transaction Details END ----------------------------- -->
                    </div>
                </div>
            </div>
        </div>
        <!----------------------------- CARD END  BANK CLEARANCE VIEW ------------------------------>

        <!----------------------------- CARD START  BANK CLEARANCE FORM ------------------------------>
        <div id="bank_clearance_add_content" style="display: none;">
            <form id="bank_clearance_form" name="bank_clearance_form" method="post" enctype="multipart/form-data">
                <!-- Row start -->
                <div class="row gutters">
                    <div class="col-12">
                        <!--- ---------------------- Transaction Details  START----------------------------- -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Transaction Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="bank_name_form">Bank Name</label><span class="text-danger">*</span>
                                            <select class="form-control" id="bank_name_form" name="bank_name_form" tabindex="1">
                                                <option value="">Select Bank Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="acc_no">Account Number</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="acc_no" name="acc_no" tabindex="2" placeholder="Account Number" readonly>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="transaction_date">Transaction Date</label><span class="text-danger">*</span>
                                            <input type="date" class="form-control" id="transaction_date" name="transaction_date" tabindex="3">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="transaction_id">Transaction ID</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="transaction_id" name="transaction_id" tabindex="4" placeholder="Enter Transaction ID">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="narration">Narration</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="narration" name="narration" tabindex="5" placeholder="Enter Narration">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="cr_dr">Credit/Debit</label><span class="text-danger">*</span>
                                            <select class="form-control" id="cr_dr" name="cr_dr" tabindex="6">
                                                <option value="">Select Credit/Debit</option>
                                                <option value="1">Credit</option>
                                                <option value="2">Debit</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="amount">Amount</label><span class="text-danger">*</span>
                                            <input type="number" class="form-control" id="amount" name="amount" tabindex="7" placeholder="Enter Amount">
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-md-4 col-lg-4">
                                        <div class="form-group">
                                            <label for="balance">Balance</label><span class="text-danger">*</span>
                                            <input type="number" class="form-control" id="balance" name="balance" tabindex="8" placeholder="Enter Balance">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--- ---------------------- Transaction Details  END ----------------------------- -->
                        <hr>
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                <button type="button"  tabindex="9"  id="download_bank_stmt" name="download_bank_stmt" class="btn btn-primary"><span class="icon-download"></span>&nbsp;Download Format</button>
                                <button type="button" data-toggle="modal" data-target="#bankUploadModal" tabindex="10"  id="upload_bank_stmt" name="upload_bank_stmt" class="btn btn-primary"><span class="icon-upload"></span>&nbsp;Upload</button>		
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="text-right">
                                <button name="submit_bank_clearance" id="submit_bank_clearance" class="btn btn-primary" tabindex="11"><span class="icon-check"></span>&nbsp;Submit</button>
                                <button type="reset" class="btn btn-outline-secondary" id="reset_btn" tabindex="12">Clear</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!----------------------------- CARD END  BANK CLEARANCE FORM------------------------------>

    <!----------------------------- CARD START  BANK STATEMENT TABLE ------------------------------>
    <div class="card bank_statement_table_content" style="display: none;">
        <div class="card-header">
            <h5 class="card-title">Bank Statement</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <table id="bank_statement_table" class="table custom-table">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Date</th>
                                <th>Narration</th>
                                <th>Transaction ID</th>
                                <th>Credit</th>
                                <th>Debit</th>
                                <th>Balance</th>
                                <th>Clear Category</th>
                                <th>Ref ID</th>
                                <th>Clearance</th>
                            </tr>
                        </thead>
                        <tbody> </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!----------------------------- CARD END  BANK STATEMENT TABLE ------------------------------>

    </div>
</div>

<!-- ////////////////////////////////////////////////////////// Bank Clearance Upload Modal Start //////////////////////////////////////////////////////////////// -->
<div class="modal fade" id="bankUploadModal" tabindex="-1" role="dialog" aria-labelledby="vCenterModalTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content" style="background-color: white">
			<div class="modal-header">
				<h5 class="modal-title" id="vCenterModalTitle">Bulk Upload</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearUploadModal()">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form action="" method="post" enctype="multipart/form-data" name="bank_upd" id="bank_upd">
					<div class="row">
						<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
							<div class="form-group">
								<label class="label">Select Bank Name</label><span class="text-danger">*</span>
								<select name="bank_id_upload" id="bank_id_upload" class="form-control"></select>
							</div>
						</div> 
						<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
							<div class="form-group">
								<label class="label">Select File</label><span class="text-danger">*</span>
								<input type="file" name="file" id="file" class="form-control">
								<div id="insertsuccess" style="color: green; font-weight: bold; display:none">Bank statement Added Successfully</div>
								<div id="notinsertsuccess" style="color: red; font-weight: bold;display:none">Problem Importing File or Duplicate Entry found</div>
							</div>
						</div> 
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="submit_stmt_upload" name="submit_stmt_upload">Upload</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal" id='close_upd_modal' onclick="clearUploadModal()">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- ////////////////////////////////////////////////////////// Bank Clearance Upload Modal END ////////////////////////////////////////////////////////////////////// -->
