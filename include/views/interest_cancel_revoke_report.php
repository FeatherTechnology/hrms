<div class="row gutters">
    <div class="col-12">
        <div class="toggle-container col-12">
            <input type="date" id='from_date' name='from_date' class="toggle-button" value=''>
            <input type="date" id='to_date' name='to_date' class="toggle-button" value=''>
            <select type="text" class="toggle-button" id='type' name='type'>
                <option value=''>Select Type</option>
                <option value='1'>Cancel</option>
                <option value='2'>Revoke</option>
            </select>
            <select type="text" class="toggle-button" id='sel_screen' name='sel_screen'>
                <option value=''>Select Screen</option>
                <option value='1'>Approval</option>
                <option value='2'>Loan Issue</option>
            </select>
            <input type="button" id='reset_btn' name='reset_btn' class="toggle-button" style="background-color: #7CA5B8;color:white" value='Reload'>
        </div> <br> <br> 
        <!-- Cancel Revoke report Start -->
        <div class="card">
            <div class="card-body">
                <div id="request_table_div" class="table-divs" style="overflow-x: auto;">
                    <table id="cancel_revoke_table" class="table custom-table">
                        <thead>
                            <th>S.No</th>
                            <th>Aadhaar Number</th>
                            <th>Customer ID</th>
                            <th>Customer Name</th>
                            <th>Area</th>
                            <th>Loan Category</th>
                            <th>Loan Amount</th>
                            <th>User Type</th>
                            <th>User Name</th>
                            <th>Agent</th>
                            <th>Customer Data</th>
                            <th>Cancel / Revoke Date</th>
                            <th>Customer Status</th>
                            <th>Remarks</th>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6"></td>
                                <td></td>
                                <td colspan="7"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>