<div class="row gutters">
    <div class="col-12">
        <input type="hidden" id="line_id">
        <div class="branch-div">
            <select name="branch_id" id="branch_id" class="branch-dropdown">
                <option value="">Choose Branch</option>
            </select>
        </div></br>
        <!----------------------------- CARD START Loan Entry ------------------------------>
        <div class="card loan-entry-card" style="display: none;">
            <div class="card-header" id="loan_entry_title">
                <div class="card-title dashboard-count-header">Loan Entry</div>
            </div>
            <div class="card-body" id="loan_entry_body" style="display: none;">
                <div class="row card-row">
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Total Loan Entry</p>
                                    <p class="cnt-value-p" id="tot_entry">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Total Issued</p>
                                    <p class="cnt-value-p" id="tot_issued">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Total Balance</p>
                                    <p class="cnt-value-p" id="tot_bal">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row card-row">
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Today Loan Entry</p>
                                    <p class="cnt-value-p" id="today_entry">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Today Issued</p>
                                    <p class="cnt-value-p" id="today_issued">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Today Balance</p>
                                    <p class="cnt-value-p" id="today_bal">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!----------------------------- CARD END Loan Entry ------------------------------>

        <!----------------------------- CARD START Approval ------------------------------>
        <div class="card approval-card" style="display: none;">
            <div class="card-header" id="approval_title">
                <div class="card-title dashboard-count-header">Approval</div>
            </div>
            <div class="card-body" id="approval_body" style="display: none;">
                <div class="row card-row">
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Total Approval</p>
                                    <p class="cnt-value-p" id="tot_approval">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Total Issued</p>
                                    <p class="cnt-value-p" id="tot_approval_issued">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Total Balance</p>
                                    <p class="cnt-value-p" id="tot_approval_bal">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row card-row">
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Today Approval</p>
                                    <p class="cnt-value-p" id="today_approval">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Today Issued</p>
                                    <p class="cnt-value-p" id="today_approval_issued">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Today Balance</p>
                                    <p class="cnt-value-p" id="today_approval_bal">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!----------------------------- CARD END Approval ------------------------------>

        <!----------------------------- CARD START Loan Issue ------------------------------>
        <div class="card loan-issue-card" style="display: none;">
            <div class="card-header" id="loan_issue_title">
                <div class="card-title dashboard-count-header">Loan Issue</div>
            </div>
            <div class="card-body" id="loan_issue_body" style="display: none;">
                <div class="row card-row">
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Total Loan Issue</p>
                                    <p class="cnt-value-p" id="tot_loan_issue">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Total Issued</p>
                                    <p class="cnt-value-p" id="tot_issue_issued">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Total Balance</p>
                                    <p class="cnt-value-p" id="tot_issue_bal">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row card-row">
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Today Loan Issue</p>
                                    <p class="cnt-value-p" id="today_loan_issue">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Today Issued</p>
                                    <p class="cnt-value-p" id="today_issue_issued">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Today Balance</p>
                                    <p class="cnt-value-p" id="today_issue_bal">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!----------------------------- CARD END Loan Issue ------------------------------>

        <!----------------------------- CARD START Collection ------------------------------>
        <div class="card collection-card" style="display: none;">
            <div class="card-header" id="collection_title">
                <div class="card-title dashboard-count-header">Collection</div>
            </div>
            <div class="card-body" id="collection_body" style="display: none;">
                <div class="row card-row">
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Total Paid</p>
                                    <p class="cnt-value-p" id="tot_paid">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Total Penalty</p>
                                    <p class="cnt-value-p" id="tot_penalty">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Total Fine</p>
                                    <p class="cnt-value-p" id="tot_fine">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row card-row">
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Today Paid</p>
                                    <p class="cnt-value-p" id="today_paid">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Today Penalty</p>
                                    <p class="cnt-value-p" id="today_penalty">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Today Fine</p>
                                    <p class="cnt-value-p" id="today_fine">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <br/>
                <div class="row">
                    <div class="radio-container chrt-div-cntnr">
                        <div class="selector">
                            <div class="selector-item">
                                <input type="radio" id="total_coll" name="coll" class="selector-item_radio" value='1' checked>
                                <label for="total_coll" class="selector-item_label">Total</label>
                            </div>
                            <div class="selector-item">
                                <input type="radio" id="today_coll" name="coll" class="selector-item_radio" value='2'>
                                <label for="today_coll" class="selector-item_label">Today</label>
                            </div>
                        </div>
                    </div>
                </div>
                <br/><br/>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div id="collection_paid"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div id="collection_pending"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div id="collection_od"></div>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>
        </div>
        <!----------------------------- CARD END Collection ------------------------------>

        <!----------------------------- CARD START Closed ------------------------------>
        <div class="card closed-card" style="display: none;">
            <div class="card-header" id="closed_title">
                <div class="card-title dashboard-count-header">Closed</div>
            </div>
            <div class="card-body" id="closed_body" style="display: none;">
                <div class="row card-row">
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Total In Closed</p>
                                    <p class="cnt-value-p" id="tot_closed">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Total Consider</p>
                                    <p class="cnt-value-p" id="tot_consider">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Total Rejected</p>
                                    <p class="cnt-value-p" id="tot_rejected">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row card-row">
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Today In closed</p>
                                    <p class="cnt-value-p" id="today_closed">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Today Consider</p>
                                    <p class="cnt-value-p" id="today_consider">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <p class="count-head">Today Rejected</p>
                                    <p class="cnt-value-p" id="today_rejected">0</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!----------------------------- CARD END Closed ------------------------------>
    </div>
</div>