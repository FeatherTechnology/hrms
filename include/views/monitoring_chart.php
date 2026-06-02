<!--CTC Creation Start-->
<div id="attendance_ot_monitor_content">
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
    </style>
    <form id="attendance_ot_monitor_chart" name="attendance_ot_monitor_chart" action="" method="post" enctype="multipart/form-data">
        <div class="row gutters">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">General Info</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12">
                                <div class="form-group">
                                    <label for="company_name">Company Name</label><span class="text-danger">*</span>
                                    <select class="form-control" id="company_name" name="company_name" tabindex="1">
                                        <option value="">Select Company Name</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12">
                                <div class="form-group">
                                    <label for="shift_name">Shift Name</label><span class="text-danger">*</span>
                                    <select class="form-control" id="shift_name" name="shift_name" tabindex="2">
                                        <option value="">Select Shift Name</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12">
                                <div class="form-group">
                                    <label for="staff_name">Staff Name</label><span class="text-danger">*</span>
                                    <select class="form-control" id="staff_name" name="staff_name" tabindex="3">
                                        <option value="">Select Staff Name</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-2 col-12">
                                <div class="form-group">
                                    <label for="date"> Date </label><span class="text-danger">*</span>
                                    <input type="date" class="form-control" id="date" name="date" tabindex="4">
                                </div>
                            </div>
                            <div class="col-md-3" style="display: flex; align-items: center;">
                                <button type="button" name="search_attendance_ot_monitor" id="search_attendance_ot_monitor" class="btn btn-primary" tabindex="7"></span>&nbsp;Search</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="custom_chart_legend" style="display: flex; justify-content: center; gap: 25px; margin-top: 20px; flex-wrap: wrap; font-size: 14px; 
                font-weight: 500; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 20px; height: 20px; background-color: #66AA00; border-radius: 3px;"></div>
                        <span>Working Hours</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 20px; height: 20px; background-color: #4285F4; border-radius: 3px;"></div>
                        <span>OT Hours</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 20px; height: 20px; background-color: #EA4335; border-radius: 3px;"></div>
                        <span>Later Entry</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 20px; height: 20px; background-color: #FBBC05; border-radius: 3px;"></div>
                        <span>Permission Hours</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 20px; height: 20px; background-color: #A142F4; border-radius: 3px;"></div>
                        <span>Grace Time</span>
                    </div>
                </div>
                <div id="timeline_chart"></div>
            </div>
        </div>
    </form>
</div>