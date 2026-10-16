<div class="row gutters">
<style>
    .payslip-container {
    background: #ffffff;
    color: #222222;
    padding: 20px;
    font-size: 13px;
}

.payslip-header {
    text-align: center;
    padding: 12px 0;
    margin-bottom: 20px;
    border-bottom: 2px solid #f26b35;
}

.payslip-header h2 {
    margin: 0 0 8px;
    color: #f26b35;
    font-size: 22px;
    font-weight: 700;
}

.payslip-header p {
    margin: 0;
    font-size: 15px;
    font-weight: 600;
}

.payslip-section-title {
    margin: 20px 0 10px;
    padding: 8px;
    background: #f5e7e1;
    border-left: 4px solid #f26b35;
    font-size: 14px;
    font-weight: 700;
}

.employee-table,
.salary-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 15px;
}

.employee-table td,
.salary-table th,
.salary-table td {
    border: 1px solid #e7e7e7;
    padding: 8px;
    vertical-align: middle;
}

.employee-table td:nth-child(odd) {
    background: #e7e7e7
}

.salary-table thead th {
    background: #565c61;
    color: #e7e7e7;
    text-align: left;
}

.salary-table .amount-cell,
.salary-table th:nth-child(2),
.salary-table th:nth-child(4) {
    text-align: right;
    white-space: nowrap;
}

.salary-table tfoot th {
    background: #d5d5d5;
    color: #565c61;
    font-weight: 700;
}

.salary-table .net-salary-row th {
    background: #565c61;
    color: #fdffff;
    font-size: 15px;
}

@media print {
    .payslip-container {
        padding: 0;
    }
}

</style>



    <div class="col-12 search_details">

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Pay Slip</h5>
            </div>

            <div class="card-body">
                <div class="row">

                    <input type="hidden" id="cmpy_id" name="cmpy_id">
                    <input type="hidden" id="branch_id" name="branch_id">
                    <input type="hidden" id="stf_prf_id" name="stf_prf_id">

                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="date">Month &amp; Year</label>
                            <span class="text-danger">*</span>

                            <input
                                type="month"
                                class="form-control"
                                id="date"
                                name="date"
                                tabindex="1"
                                max="<?php echo date('Y-m'); ?>"
                            >
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6"
                         style="display:flex;justify-content:center;align-items:center">

                        <div class="form-group">
                            <button
                                type="button"
                                class="btn btn-primary"
                                id="gen_pay_slip"
                                tabindex="2"
                                style="margin-top:15px;"
                            >
                                View Pay Slip
                            </button>
                        </div>

                    </div>

                </div>
            </div>
        </div>


        <!-- PAYSLIP RESULT -->
        <div class="pay_slip_details" style="display:none;">

            <div class="card">
                <div class="card-body">

                    <div class="row">
                        <div class="col-12 text-end mb-3"
                             style="display:flex;justify-content:flex-end;">

                            <button
                                type="button"
                                class="btn btn-primary"
                                id="download_payslip"
                            >
                                Download Payslip
                            </button>

                        </div>
                    </div>


                    <div class="payslip-container">

                        <!-- HEADER -->
                        <div class="payslip-header">
                            <h2 id="ps_company_name"></h2>
                            <p id="ps_month"></p>
                        </div>


                        <!-- EMPLOYEE AND ATTENDANCE DETAILS -->
                        <h5 class="payslip-section-title">
                            Employee &amp; Attendance Details
                        </h5>

                        <table class="employee-table">

                            <tbody>
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

                                    <td><b>Total No. of Days</b></td>
                                    <td id="ps_total_days"></td>
                                </tr>

                                <tr>
                                    <td><b>Working Days</b></td>
                                    <td id="ps_working_days"></td>

                                    <td><b>Present Days</b></td>
                                    <td id="ps_present_days"></td>
                                </tr>

                                <tr>
                                    <td><b>Approved Leave</b></td>
                                    <td id="ps_approved_leave"></td>

                                    <td><b>LOP Days</b></td>
                                    <td id="ps_lop_days"></td>
                                </tr>

                                <tr>
                                    <td><b>Holidays</b></td>
                                    <td id="ps_holidays"></td>

                                    <td><b>Week Off</b></td>
                                    <td id="ps_weekoff"></td>
                                </tr>

                                <tr>
                                    <td><b>Extra Working Days</b></td>
                                    <td id="ps_extra_working"></td>

                                    <td><b>OT Days</b></td>
                                    <td id="ps_ot_days"></td>
                                </tr>

                                <tr>
                                    <td><b>OT Hours</b></td>
                                    <td id="ps_ot_hours" colspan="3"></td>
                                </tr>
                            </tbody>

                        </table>


                        <!-- SALARY INFORMATION -->
                        <h5 class="payslip-section-title">
                            Salary Information
                        </h5>

                        <table class="employee-table">

                            <tbody>
                                <tr>
                                    <td><b>Total CTC</b></td>
                                    <td id="ps_total_ctc"></td>

                                    <td><b>Earned Salary</b></td>
                                    <td id="ps_earned_salary"></td>
                                </tr>

                                <tr>
                                    <td><b>One Day Salary</b></td>
                                    <td id="ps_one_day_salary"></td>

                                    <td><b>LOP Amount</b></td>
                                    <td id="ps_lop_amount"></td>
                                </tr>

                                <tr>
                                    <td><b>PT Eligible Earnings</b></td>
                                    <td id="ps_pt_eligible_earnings" colspan="3"></td>
                                </tr>
                            </tbody>

                        </table>


                        <!-- EARNINGS AND DEDUCTIONS -->
                        <h5 class="payslip-section-title">
                            Earnings &amp; Deductions
                        </h5>

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

                                <tr>
                                    <th colspan="3">
                                        Net Salary
                                    </th>
                                    <th id="ps_net_salary"></th>
                                </tr>

                                <tr class="net-salary-row">
                                    <th colspan="3">Take Home Salary</th>
                                    <th id="ps_take_home"></th>
                                </tr>

                            </tfoot>

                        </table>

                    </div>
                    <!-- END PAYSLIP CONTAINER -->

                </div>
            </div>

        </div>

    </div>

</div>