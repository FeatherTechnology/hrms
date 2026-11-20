<div class="row gutters">

    <div class="col-12">
        <div class="toggle-container col-12">
            <select class="toggle-button" name='company_id' id='company_id'>
                <option value=''>Select Company</option>
            </select>
            <select class="toggle-button" name='branch_id' id='branch_id'>
                <option value=''>Select Branch</option>
            </select>
            <select class="toggle-button" name='status' id='status'>
                <option value=''>Select Status</option>
                <option value='1'>Active</option>
                <option value='2'>Completed</option>
            </select>
            <input type="button" id='staff_btn' name='staff_btn' class="toggle-button btn-primary" style="background-color: #f26b35;color:white" value='Search'>
        </div> <br> <br>

        <div class="card staff_loan_table_content">
            <div class="card-body">
                <div class="row">
                    <div class="col-12" style="overflow-x: auto;">
                        <table id="staff_loan_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Company Name</th>
                                    <th>Branch Name</th>
                                    <th>Staff Name</th>
                                    <th>Loan Amount</th>
                                    <th>Intrest Rate</th>
                                    <th>Due Period</th>
                                    <th>Total Intrest</th>
                                    <th>Due Amount</th>
                                    <th>Due Start Date</th>
                                    <th>Due End Date</th>
                                    <th>Balance Amount</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>