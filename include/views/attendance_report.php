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
                            <select class="form-control" id="branch_name" name="branch_name" tabindex="2">
                                <option value="">Select Branch Name</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="dep_name">Department Name</label><span class="text-danger">*</span>
                            <select class="form-control" id="dep_name" name="dep_name" tabindex="3">
                                <option value="">Select Department Name</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="stf_name">Staff Name</label><span class="text-danger">*</span>
                            <select class="form-control" id="stf_name" name="stf_name" tabindex="4">
                                <option value="">Select Staff Name</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="date">Month</label><span class="text-danger">*</span>
                            <input type="month" class="form-control" id="date" name="date" tabindex="5" max="<?= date('Y-m') ?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6" style="display: flex;justify-content:left; align-items:center;margin-top: 20px;">
                        <div class="form-group">
                            <button name="submit_search" id="submit_search" class="btn btn-primary" tabindex="6"><span class="icon-check"></span>&nbsp;Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="attendance_report" style="display: none;">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Attendance Report</h5>
                </div>
                <div class="card-body">
                    <div id="attendance_table_div" class="table-divs" style="overflow-x: auto;">
                        <table id="attendance_table" class="table table-bordered table-striped">
                            <thead id="att_head"></thead>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>