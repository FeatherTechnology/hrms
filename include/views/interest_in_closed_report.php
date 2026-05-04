<div class="row gutters">
    <div class="col-12">
        <div class="toggle-container col-12">
            <input type="date" id='from_date' name='from_date' class="toggle-button" value=''>
            <input type="date" id='to_date' name='to_date' class="toggle-button" value=''>
            <input type="button" id='reset_btn' name='reset_btn' class="toggle-button" style="background-color: #7CA5B8;color:white" value='Reload'>
        </div> <br> <br>

        <div class="card">
            <div class="card-body">
                <div id="in_closed_table_div" class="table-divs" style="overflow-x: auto;">
                    <table id="in_closed_report_table" class="table custom-table">
                        <thead>
                            <th>S.No</th>
                            <th>Line</th>
                            <th>Loan ID</th>
                            <th>Loan Date</th>
                            <th>Aadhaar Number</th>
                            <th>Customer ID</th>
                            <th>Customer Name</th>
                            <th>Area</th>
                            <th>Loan Category</th>
                            <th>Agent Name</th>
                            <th>Loan Amount</th>
                            <th>Maturity Date</th>
                            <th>Closed Date</th>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="10"></td>
                                <td></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>