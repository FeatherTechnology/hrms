<br>
<div class="row gutters">
    <div class="col-12">
        <div class="toggle-container col-12">
            <input type="date" id='from_date' name='from_date' class="toggle-button" value=''>
            <input type="date" id='to_date' name='to_date' class="toggle-button" value=''>
            <select class="toggle-button" name='company_id' id='company_id'>
                <option value=''>Select Company</option>
            </select>
            <select class="toggle-button" name='branch_id' id='branch_id'>
                <option value=''>Select Branch</option>
            </select>
            <select class="toggle-button" name='department_id' id='department_id'>
                <option value=''>Select Department</option>
            </select>
            <input type="button" id='location_access_btn' name='location_access_btn' class="toggle-button" style="background-color: #016091;color:white" value='Search'>
        </div> <br> <br>
        <!-- Location Access report Start -->
        <div class="card">
            <div class="card-body overflow-x-cls">
                <div class="col-12">
                    <table id="location_access_report_table" class="table custom-table">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Staff ID</th>
                                <th>Staff Name</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Default Branch</th>
                                <th>Assigned Branch</th>
                                <th>Branch Location</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>No of Days</th>
                                <th>Assigned By</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Location Access report End -->
    </div>
</div>