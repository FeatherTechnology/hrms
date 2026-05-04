<div class="row gutters">
    <div class="col-12">
        <div class="toggle-container col-12">
            <input type="date" id='to_date' name='to_date' class="toggle-button" value=''>
            <input type="button" id='reset_btn' name='reset_btn' class="toggle-button" style="background-color: #7CA5B8;color:white" value='Search'>
        </div> <br> <br>
        <!-- Uncleared report Start -->
        <div class="card">
            <div class="card-body">
                <div id="uncleared_table_div" class="table-divs" style="overflow-x: auto;">
                    <table id="uncleared_report_table" class="table custom-table">
                        <thead>
                            <th>S.No</th>
                            <th>Bank Name</th>
                            <th>Transaction Date</th>
                            <th>Narration</th>
                            <th>Transaction ID</th>
                            <th>Credit</th>
                            <th>Debit</th>
                            <th>Balance</th>
                            <th>Status</th>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td colspan="1"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>