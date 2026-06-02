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
    </style>
    <div class="col-12">
        <div class="col-12 text-right">
            <button class="btn btn-primary add_team_btn"><span class="icon-add"></span> Add Team Creation</button>
            <button class="btn btn-primary back_to_team_btn" tabindex="8" style="display: none;"><span class="icon-arrow-left"></span> Back</button>
        </div></br>
        <!----------------------------- CARD START TEAM CREATION TABLE ------------------------------>
        <div class="card team_table_content">
            <div class="card-body">
                <div class="row">
                    <div class="col-12" style="overflow-x: auto;">
                        <table id="team_creation_table" class="table custom-table">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Company Name</th>
                                    <th>Department Name</th>
                                    <th>Team Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!----------------------------- CARD END TEAM CREATION TABLE ------------------------------>

        <!----------------------------- CARD START TEAM CREATION FORM ------------------------------>
        <div id="team_creation_content" style="display: none;">
            <form id="team_creation" name="team_creation" method="post" enctype="multipart/form-data">
                <input type="hidden" id="team_creation_id" value="">
                <!-- Row start -->
                <div class="row gutters">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">General Info</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Fields -->
                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                                        <div class="form-group">
                                            <label for="company_name">Company Name</label><span class="text-danger">*</span>
                                            <select class="form-control" id="company_name" name="company_name" tabindex="1">
                                                <option value="">Select Company Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-4">
                                        <div class="form-group">
                                            <label for="department_name">Department Name</label><span class="text-danger">*</span>
                                            <select class="form-control" id="department_name" name="department_name" tabindex="2">
                                                <option value="">Select Department Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-4">
                                        <div class="form-group">
                                            <label for="team_name">Team Name</label><span class="text-danger">*</span>
                                            <input type="hidden" id="team_name2">
                                            <select class="form-control" id="team_name" name="team_name[]" tabindex="3" multiple></select>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-sm-2" style="margin-top: 18px;">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary modalBtnCss" tabindex="4" data-toggle="modal" data-target="#add_team_info" onclick="getTeamNameTable()"><span class="icon-add"></span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-3 text-right">
                            <button name="submit_team_creation" id="submit_team_creation" class="btn btn-primary" tabindex="5"><span class="icon-check"></span>&nbsp;Submit</button>
                            <button type="reset" class="btn btn-outline-secondary" tabindex="6">Clear</button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
        <!----------------------------- CARD END TEAM CREATION FORM------------------------------>

    </div>
</div>

<!------------------------------------------------------------------ Team Info Modal start  ----------------------------------------------------------------------------->

<div class="modal fade" id="add_team_info" tabindex="1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content" style="background-color: white">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Team</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="getTeamNameDropdown();" tabindex="1">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form id="team_form">
                        <div class="row">
                            <input type="hidden" name="team_id" id='team_id'>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="modal_team_code">Team Code</label><span class="text-danger">*</span>
                                    <input class="form-control" name="modal_team_code" id="modal_team_code" tabindex="1" readonly>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                <div class="form-group">
                                    <label for="modal_team_name">Team Name</label><span class="text-danger">*</span>
                                    <input class="form-control" name="modal_team_name" id="modal_team_name" tabindex="1" placeholder="Enter Team Name">
                                </div>
                            </div>

                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12" style="margin-top: 3px;">
                                <div class="form-group">
                                    <label for="" style="visibility:hidden"></label><br>
                                    <button name="submit_team" id="submit_team" class="btn btn-primary" tabindex="1"><span class="icon-check"></span>&nbsp;Submit</button>
                                    <button type="reset" id="clear_team_form" class="btn btn-outline-secondary" tabindex="1">Clear</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-12 overflow-x-cls">
                        <table id="team_modal_table" class="custom-table">
                            <thead>
                                <tr>
                                    <th width="10">S.No.</th>
                                    <th>Team Code</th>
                                    <th>Team Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal" tabindex="1" onclick="getTeamNameDropdown()">Close</button>
            </div>
        </div>
    </div>
</div>

<!----------------------------------------------------------------- Team Info Modal End ----------------------------------------------------------------------------->