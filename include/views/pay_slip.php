<div class="row gutters">

    <div class="col-12 search_details">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Pay Slip</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <input type="hidden" name="cmpy_id" id="cmpy_id" value="" />
                    <input type="hidden" name="branch_id" id="branch_id" value="" />
                    <input type="hidden" name="stf_prf_id" id="stf_prf_id" value="" />
                    <!-- Fields -->
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="date">Month & Year</label> <span class="text-danger">*</span>
                            <input type="month" class="form-control" id="date" name="date" tabindex="1" max="<?php echo date('Y-m'); ?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6" style="display: flex;justify-content:center;align-items:center">
                        <div class="form-group">
                            <button name="gen_pay_slip" id="gen_pay_slip" class="btn btn-primary" tabindex="2" style="margin-top: 15px;"></span>&nbsp;Gendrate Pay Slip</button>

                        </div>
                    </div>


                </div>
            </div>
        </div>

        <div class="pay_slip_details" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 text-end mb-3" style="display: flex;justify-content:right;align-items:center">
                            <button type="button" class="btn btn-primary" id="download_payslip">
                                Download Payslip
                            </button>
                        </div>
                    </div>
                    <!-- PAY SLIP -->
                    <div class="pay_slip_details" style="display:none;">

                        <div class="payslip-container">


                            <div class="payslip-header">
                                <h2 id="ps_company_name"></h2>
                                <p id="ps_month"></p>
                            </div>

                            <table class="employee-table">
                                <tr>
                                    <td><b>Staff ID</b></td>
                                    <td id="ps_staff_id"></td>

                                    <td><b>Department</b></td>
                                    <td id="ps_department"></td>
                                </tr>

                                <tr>
                                    <td><b>Staff Name</b></td>
                                    <td id="ps_staff_name"></td>

                                    <td><b>Designation</b></td>
                                    <td id="ps_designation"></td>
                                </tr>
                                <tr>
                                    <td><b>Team Name</b></td>
                                    <td id="team_name"></td>
                                </tr>

                                <tr>
                                    <td><b>Total No Of Days</b></td>
                                    <td id="ps_total_days"></td>

                                    <td><b>Working Days</b></td>
                                    <td id="ps_working_days"></td>
                                </tr>

                                <tr>
                                    <td><b>Present Days</b></td>
                                    <td id="ps_present_days"></td>

                                    <td><b>Approved Leave</b></td>
                                    <td id="ps_approved_leave"></td>
                                </tr>

                                <tr>
                                    <td><b>LOP Days</b></td>
                                    <td id="ps_lop_days"></td>

                                    <td><b>Extra Working Days</b></td>
                                    <td id="ps_extra_working"></td>
                                </tr>
                            </table>

                            <table class="salary-table">

                                <thead>
                                    <tr>
                                        <th>Earnings</th>
                                        <th>Amount</th>

                                        <th>Deductions</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>

                                <tbody id="salary_component_body">

                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th>Total Earnings</th>
                                        <th id="ps_gross_total"></th>

                                        <th>Total Deductions</th>
                                        <th id="ps_deduction_total"></th>
                                    </tr>

                                    <tr class="net-salary-row">
                                        <th colspan="3">Net Salary</th>
                                        <th id="ps_net_salary"></th>
                                    </tr>
                                </tfoot>

                            </table>

                        </div>

                    </div>



                </div>
            </div>
        </div>
    </div>
</div>