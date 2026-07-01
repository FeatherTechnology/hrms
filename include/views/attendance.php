<div class="row gutters">
    <script type="text/javascript" src="https://unpkg.com/vis-timeline@latest/standalone/umd/vis-timeline-graph2d.min.js"></script>
    <link href="https://unpkg.com/vis-timeline@latest/styles/vis-timeline-graph2d.min.css" rel="stylesheet" type="text/css" />
    <style>
        /* This creates the dark vertical border next to the names */
        .vis-panel.vis-left {
            border-right: 2px solid #000000 !important;
        }

        /* This creates the dark horizontal border above the times */
        .vis-panel.vis-bottom {
            border-top: 2px solid #000000 !important;
        }

        /* Style the inner grid lines like an Excel sheet */
        .vis-time-axis .vis-grid.vis-minor {
            border-left: 1px solid #e5e5e5;
        }

        /* Remove vertical grid lines from the timeline background */
        .vis-time-axis .vis-grid.vis-minor,
        .vis-time-axis .vis-grid.vis-major {
            border-left: none !important;
            border-right: none !important;
        }

        /* Ensure horizontal lines between groups remain visible */
        .vis-itemset .vis-background .vis-group {
            border-bottom: 1px solid #e5e5e5;
            /* You can adjust the color/thickness */
        }

        /* Add space above and below the time labels */
        .vis-time-axis .vis-text {
            padding-top: 20px !important;
        }

        /* Center the staff names vertically and horizontally */
        .vis-labelset .vis-label {
            display: flex !important;
            align-items: center !important;
            /* Centers vertically */
            justify-content: center !important;
            /* Centers horizontally */
        }

        /* Remove default padding so the text doesn't look slightly off-center */
        .vis-labelset .vis-label .vis-inner {
            padding: 10px !important;
            text-align: center !important;
        }

        /* Decrease chart width and center it horizontally */
        #timeline_chart {
            width: 80% !important;
            /* Adjust this number (e.g., 70%, 900px) */
            margin: 0 auto !important;
            /* This is the magic rule that centers it */
        }

        .attendance-chart-modal {
            width: 75%;
            max-width: 1500px;
        }
    </style>

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
                            <label for="date">Date</label><span class="text-danger">*</span>
                            <input type="date" class="form-control" id="date" name="date" max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" tabindex="3">
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-6" style="display: flex;justify-content:right; align-items:center">
                        <button name="submit_search" id="submit_search" class="btn btn-primary" tabindex="4"><span class="icon-check"></span>&nbsp;Search</button>
                    </div>


                </div>
            </div>
        </div>

        <div class="attendance_list">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Attendance List</h5>
                </div>
                <div class="card-body">
                    <div id="attendance_table_div" class="table-divs" style="overflow-x: auto;">
                        <table id="attendance_table" class="table custom-table">
                            <thead>
                                <th>S.No</th>
                                <th>Staff ID</th>
                                <th>Staff Name</th>
                                <th>Company</th>
                                <th>Branch</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Team</th>
                                <th>Staff Type</th>
                                <th>Entry Time</th>
                                <th>Updated By</th>
                                <th>Reason</th>
                                <th>Attendance Chart</th>
                                <th>Action</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-12 attendance_details" style="display: none;">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Staff Details</h5>
            </div>
            <div class="card-body">
                <div class="row">

                    <input type="hidden" name="stf_prf_id" id="stf_prf_id" value="" />
                    <input type="hidden" name="att_id" id="att_id" value="" />
                    <input type="hidden" name="cmpy_id" id="cmpy_id" value="" />
                    <input type="hidden" name="branch_id" id="branch_id" value="" />
                    <input type="hidden" name="dep_id" id="dep_id" value="" />
                    <input type="hidden" name="des_id" id="des_id" value="" />
                    <input type="hidden" name="team_id" id="team_id" value="" />
                    <input type="hidden" name="staff_type_id" id="staff_type_id" value="" />

                    <!-- Fields -->
                    <div class="col-md-12 ">
                        <div class="text-right">
                            <button type="button" class="btn btn-primary" id="back_btn" tabindex="5"><span class="icon-arrow-left"></span>&nbsp; Back </button>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="staff_id">Staff ID</label>

                            <input type="text" class="form-control" id="staff_id" name="staff_id" placeholder="Company Name" tabindex="6" readonly>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="staff_name">Staff Name</label>
                            <input type="text" class="form-control" id="staff_name" name="staff_name" placeholder="Staff Name" tabindex="7" readonly>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="company_name">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Company Name" tabindex="8" readonly>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="brch_name">Branch Name</label>

                            <input type="text" class="form-control" id="brch_name" name="brch_name" placeholder="Branch_name" tabindex="9" readonly>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="department">Department</label>
                            <input type="text" class="form-control" id="department" name="department" placeholder="Department" tabindex="10" readonly>

                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="designation">Designation</label>
                            <input type="text" class="form-control" id="designation" name="designation" placeholder="Designation" tabindex="11" readonly>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="team">Team</label>
                            <input type="text" class="form-control" id="team" name="team" placeholder="Team" tabindex="12" readonly>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="staff_type">Staff Type</label>
                            <input type="text" class="form-control" id="staff_type" name="staff_type" placeholder="Staff Type" tabindex="13" readonly>
                        </div>
                    </div>

                </div>
                <br><br><br>
                <div class="row" id="update_attendance_div">
                    <div class="col-md-12 col-sm-12" style="margin-bottom: 10px;">

                        <h5 class="card-title">Attendance Details</h5>

                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="row">
                            <div class="col-md-8 col-sm-6">
                                <div class="form-group">
                                    <label for="entry_date">Entry Date</label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="entry_date" name="entry_date" tabindex="14" readonly>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label for="entry_time">Entry Time</label><span class="text-danger">*</span>
                                    <input type="time" class="form-control" id="entry_time" name="entry_time" tabindex="15">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label for="reason">Reason</label><span class="text-danger">*</span>
                            <textarea type="textarea" class="form-control" id="reason" name="reason" placeholder="Enter Reason" tabindex="16"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12 ">
                        <div class="text-right">
                            <button type="submit" name="submit_attendance" id="submit_attendance" class="btn btn-primary" value="Submit" tabindex="17">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Moinitoring Chart Modal -->

<div class="modal fade" id="attendanceChartModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered attendance-chart-modal">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Monitoring Chart</h5>

                <button type="button"
                    class="close"
                    onclick="$('#attendanceChartModal').modal('hide');">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="custom_chart_legend" style="display: flex; justify-content: center; gap: 25px; margin-top: 20px; flex-wrap: wrap; font-size: 14px; 
                font-weight: 500; margin-bottom: 30px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 20px; height: 20px; background-color: #4CAF50; border-radius: 3px;"></div>
                        <span>Working Hours</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 20px; height: 20px; background-color: #2196F3; border-radius: 3px;"></div>
                        <span>OT Hours</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 20px; height: 20px; background-color: #F44336; border-radius: 3px;"></div>
                        <span>Later Entry</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 20px; height: 20px; background-color: #FF9800; border-radius: 3px;"></div>
                        <span>Permission Hours</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 20px; height: 20px; background-color: #9C27B0; border-radius: 3px;"></div>
                        <span>Grace Time</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 20px; height: 20px; background-color: #009688; border-radius: 3px;"></div>
                        <span>Paid Leave</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 20px; height: 20px; background-color: #9E9E9E; border-radius: 3px;"></div>
                        <span>Week Off</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 20px; height: 20px; background-color: #424242; border-radius: 3px;"></div>
                        <span>LOP</span>
                    </div>
                </div>
                <div id="timeline_chart"></div>
            </div>

            <div class="modal-footer">
                <button type="button"
                    class="btn btn-secondary"
                    onclick="$('#attendanceChartModal').modal('hide');">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>