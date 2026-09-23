<div class="row gutters">

    <style>
        /* Remove borders and outlines from the Choices.js search input */
        .choices__inner input.choices__input {
            border: none !important;
            outline: none !important;
            background: transparent !important;
            box-shadow: none !important;
            margin-bottom: 0 !important;
            /* Prevents awkward spacing */
        }

        /* Ensure the input doesn't stretch weirdly on focus */
        .choices__inner input.choices__input:focus {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
        }

        .hierarchy-chart {
            width: 100%;
            overflow: auto;
            padding: 20px;
            text-align: center;
        }

        /* Tree container */
        .hierarchy-tree,
        .hierarchy-child {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Node wrapper */
        .hierarchy-node-wrapper {
            position: relative;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
        }

        /* =========================
   NODE
   ========================= */

        .hierarchy-node {
            min-width: 160px;
            max-width: 190px;

            padding: 8px 12px;

            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;

            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);

            position: relative;
            z-index: 2;
        }

        /* Staff name */

        .staff-name {
            font-size: 13px;
            font-weight: 750;
            color: #f26b35;
        }

        /* Designation */

        .designation {
            margin-top: 3px;
            font-size: 11px;
            font-weight: 750;
            color: #374151;
        }

        /* Branch / Department / Team */

        .staff-info {
            margin-top: 4px;
            font-size: 10px;
            color: #666;
            line-height: 1.3;
        }

        /* =========================
   CHILDREN
   ========================= */

        .hierarchy-children {
            display: flex;
            justify-content: center;

            gap: 20px;

            position: relative;

            margin-top: 30px;
        }

        /* Vertical line from parent */

        .hierarchy-children::before {
            content: "";

            position: absolute;

            top: -18px;
            left: 50%;

            width: 1px;
            height: 18px;

            background: #999;
        }

        /* Child */

        .hierarchy-child {
            position: relative;

            padding-top: 18px;
        }

        /* Vertical line for child */

        .hierarchy-child::before {
            content: "";

            position: absolute;

            top: 0;
            left: 50%;

            width: 1px;
            height: 18px;

            background: #999;
        }

        /* Horizontal connector */

        .hierarchy-child:not(:only-child)::after {
            content: "";

            position: absolute;

            top: 0;

            height: 1px;

            background: #999;

            left: -10px;
            right: -10px;
        }

        /* First child */

        .hierarchy-child:first-child::after {
            left: 50%;
        }

        /* Last child */

        .hierarchy-child:last-child::after {
            right: 50%;
        }

        /* Only one child */

        .hierarchy-children:has(> .hierarchy-child:only-child)::after {
            display: none;
        }

        /* =========================
   MODAL
   ========================= */

        .hierarchy-modal-dialog {
            max-width: 90%;
            width: 90%;

            height: 85vh;

            margin: 7vh auto;
        }

        .hierarchy-modal-dialog .modal-content {
            height: 100%;
        }

        .hierarchy-modal-dialog .modal-body {
            overflow: auto;
        }
    </style>

    <div class="col-12">

        <!--- Reporting Person Mapping List Start --->
        <div class="text-right addReportingPersonbtn">
            <button type="button" class="btn btn-primary addReportingPersonbtn" id="add_reporting_person"><span class="fa fa-plus"></span>&nbsp; Add Reporting Person</button>
        </div>
        <br>

        <div class="card reporting_person_table_content">
            <div class="card-body">
                <div class="col-12" style="overflow-x: auto;">
                    <table id="reporting_person_mapping_table" class="table custom-table dtable">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Company Name</th>
                                <th>User Type</th>
                                <th>Reporting Person</th>
                                <th>Designation</th>
                                <th>Reporting Staff</th>
                                <th>Hierarchy</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--- Reporting Person Mapping List End --->

        <!--- Reporting Person Mapping --->

        <div id="reporting_person_content" style="display:none;">
            <div class="text-right backBtn">
                <button type="button" class="btn btn-primary backBtn" id="back_btn"><span class="icon-arrow-left"></span>&nbsp; Back </button>
            </div>
            <br>
            <form id="reporting_person_mapping" name="reporting_person_mapping" action="" method="post" enctype="multipart/form-data">
                <input type="hidden" id="reporting_person_id" value="">
                <div class="row gutters">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">General Info</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="company_name">Company Name</label><span class="text-danger">*</span>
                                            <select class="form-control" id="company_name" name="company_name" tabindex="6">
                                                <option value="">Select Company Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="user_type">User Type</label><span class="text-danger">*</span>
                                            <select class="form-control" id="user_type" name="user_type" tabindex="3">
                                                <option value="">Select User Type</option>
                                                <option value="1">Director</option>
                                                <option value="2">Staff</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12" id="director_div" style="display:none;">
                                        <div class="form-group">
                                            <label for="director_name">Director Name</label><span class="text-danger">*</span>
                                            <select class="form-control" id="director_name" name="director_name" tabindex="6">
                                                <option value="">Select Director Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 staff_div">
                                        <div class="form-group">
                                            <label for="designation">Designation</label><span class="text-danger">*</span>
                                            <select class="form-control" id="designation" name="designation" tabindex="6">
                                                <option value="">Select Designation</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 staff_div">
                                        <div class="form-group">
                                            <label for="reporting_person">Reporting Person</label><span class="text-danger">*</span>
                                            <select class="form-control" id="reporting_person" name="reporting_person" tabindex="6">
                                                <option value="">Select Reporting Person</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                        <div class="form-group">
                                            <label for="reporting_staff">Reporting Staff</label><span class="text-danger">*</span>
                                            <input type="hidden" id="reporting_staff2">
                                            <select class="form-control" id="reporting_staff" name="reporting_staff[]" tabindex="13" multiple></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-12 ">
                        <div class="text-right">
                            <button type="submit" name="submit_reporting_person_mapping" id="submit_reporting_person_mapping" class="btn btn-primary" value="Submit" tabindex="7"><span class="icon-check"></span>&nbsp;Submit</button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
        <!----------------------------- CARD END  director CREATION FORM------------------------------>
    </div>
</div>

<div class="modal fade"
    id="hierarchyModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="hierarchyModalTitle"
    aria-hidden="true">

    <div class="modal-dialog hierarchy-modal-dialog" role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="hierarchyModalTitle">
                    Reporting Hierarchy
                </h5>
                <button type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div id="hierarchyChart" class="hierarchy-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal">

                    Close
                </button>
            </div>
        </div>
    </div>
</div>