<div class="row gutters">
    <div class="col-12 search_details">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Staff Info</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Fields -->
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="cmpy_name">Company Name</label><span class="text-danger">*</span>
                            <select class="form-control" id="cmpy_name" name="cmpy_name" tabindex="1">
                                <option value="">Select Company Name</option>

                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="branch_name">Branch Name</label><span class="text-danger">*</span>
                            <select class="form-control" id="branch_name" name="branch_name" tabindex="1">
                                <option value="">Select Branch Name</option>

                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="date">Month & Year</label> <span class="text-danger">*</span>
                            <input type="month" class="form-control" id="date" name="date" tabindex="2">
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-6" style="display: flex;justify-content:right; align-items:center">
                        <button name="gen_pay_roll" id="gen_pay_roll" class="btn btn-primary" tabindex="5"></span>&nbsp;Gendrate Pay Roll</button>
                    </div>


                </div>
            </div>
        </div>

        <div class="payroll_list" style="display: none;">

            <div class="card">
                <div class="card-body">
                    <div id="payroll_table_div" class="table-divs" style="overflow-x: auto;">
                        <table id="payroll_table" class="table table-bordered display nowrap" style="width:100%">
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>