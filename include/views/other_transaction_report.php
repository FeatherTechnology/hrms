<br>
<div class="row gutters">
    <div class="col-12">
        <div class="row gutters">
            <div class="col-12">
                <div class="row justify-content-center"> <!-- centers the inner row -->
                    <div class="col-md-10"> <!-- set the width of the content -->
                        <div class="row align-items-end justify-content-center"> <!-- center items in inner row -->
                            <div class="col-md-2">
                                <label for="from_date" style="margin-left: 10px;">From Date</label>
                                <input type="date" id="from_date" name="from_date" class="toggle-button" />
                            </div>
                            <div class="col-md-2">
                                <label for="to_date" style="margin-left: 10px;">To Date</label>
                                <input type="date" id="to_date" name="to_date" class="toggle-button" />
                            </div>
                            <div class="col-md-3">
                                <label for="sheet_type">Balance Sheet Type</label>
                                <select class="form-control" name="sheet_type" id="sheet_type" tabindex="5">
                                    <option value="">Select Balance Sheet Type</option>
                                    <option value="1">Deposit</option>
                                    <option value="2">Investment</option>
                                    <option value="3">EL</option>
                                    <option value="4">Exchange</option>
                                    <option value="5">Bank Deposit</option>
                                    <option value="6">Bank Withdrawal</option>
                                    <option value="8">Other Income</option>
                                    <option value="9">Bank Unbilled</option>
                                    <option value="10">Expense</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label>&nbsp;</label>
                                <input type="button" id="other_report_btn" name="other_report_btn" class="toggle-button" style="background-color: #7CA5B8; color: white;"
                                    value="Search" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><br><br>

        <div class="card">
            <div class="card-header">Other Transaction Report</div>
            <div class="card-body overflow-x-cls">
                <div class="col-12">

                    <!-- Other Transaction report Start -->

                    <table id="other_transaction_table" class="table custom-table" style="display: none;">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Date</th>
                                <th>Coll Mode</th>
                                <th>Bank Name</th>
                                <th>Transaction Category</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Reference ID</th>
                                <th>Transaction ID</th>
                                <th>Amount</th>
                                <th>Remark</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    <!--Other Transaction report END-->

                    <!--Expense report Start-->

                    <table id="expenses_table" class="table custom-table" style="display: none;">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Date</th>
                                <th>Cash Mode</th>
                                <th>Bank Name</th>
                                <th>Invoice ID</th>
                                <th>Branch</th>
                                <th>Expense Category</th>
                                <th>Agent Name</th>
                                <th>Total Issue</th>
                                <th>Total Amount</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Transaction ID</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    <!--Expense report END-->

                </div>
            </div>
        </div>

    </div>
</div>