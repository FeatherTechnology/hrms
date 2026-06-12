<div class="row gutters">
    <div class="col-12">
        <div class="toggle-container col-12">
            <input type="date" id='from_date' name='from_date' class="toggle-button" value=''>
            <input type="date" id='to_date' name='to_date' class="toggle-button" value=''> 
            <select class="toggle-button" name='company_id' id='company_id'>
                <option value=''>Select Company</option>
            </select>
            <select class="toggle-button" name='department_id' id='department_id'>
                <option value=''>Select Department</option>
            </select>
            <select class="toggle-button" name='reg_status' id='reg_status'>
                <option value=''>Select Status</option>
                <option value='0'>Pending</option>
                <option value='1'>Approved</option>
                <option value='2'>Cancelled</option>
            </select>
            <input type="button" id='regularization_btn' name='regularization_btn' class="toggle-button" style="background-color: #f26b35;color:white" value='Search'>
        </div> <br/>
        <!-- Regularization report Start -->
        <div class="card">
            <div class="card-body overflow-x-cls">
                <div class="col-12">
                    <table id="regularization_report_table" class="table custom-table">
                        <thead id="regularization_thead"></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--Regularization report End-->
    </div>
</div>

