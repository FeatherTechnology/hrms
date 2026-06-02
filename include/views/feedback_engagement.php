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

    <div class="radio-container col-12" style="margin-top: 10px;">
        <div class="selector">
            <div class="selector-item">
                <input type="radio" id="general_feedback" name="general_feedback_type" class="selector-item_radio" value="general" checked>
                <label for="general_feedback" class="selector-item_label">General Feedback</label>
            </div>
            <div class="selector-item">
                <input type="radio" id="scheduled_feedback" name="general_feedback_type" class="selector-item_radio" value="scheduled">
                <label for="scheduled_feedback" class="selector-item_label">Scheduled Feedback</label>
            </div>
        </div>
    </div>
    <div class="col-12">

        <!-------------------------------------------------------------------- General Feedback Start ------------------------------------------------------->

        <form id="general_feedback" name="general_feedback" action="" method="post" enctype="multipart/form-data">
            <div class="row gutters">
                <div class="col-12">
                    <div id="general_feedback_content" style="margin-top: 20px;">
                        <input type="hidden" id="general_feedback_id">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">General Feedback</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                                        <div class="form-group">
                                            <label for="general_company_name">Company Name</label><span class="text-danger">*</span>
                                            <select class="form-control" id="general_company_name" name="general_company_name" tabindex="6">
                                                <option value="">Select Company Name</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                                        <div class="form-group">
                                            <label for="feedback_name">Feedback Name</label><span class="text-danger">*</span>
                                            <input type="text" class="form-control" id="feedback_name" name="feedback_name" placeholder="Enter Feedback Name" tabindex="11">
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                                        <div class="form-group">
                                            <label for="status">Status</label><span class="text-danger">*</span>
                                            <select class="form-control" name="status" id="status" tabindex="1">
                                                <option value="">Select Status</option>
                                                <option value="0">Active</option>
                                                <option value="1">In Active</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3" style="display: flex; align-items: center;">
                                        <button type="submit" name="general_feedback_submit" id="general_feedback_submit" class="btn btn-primary" value="Add" tabindex="18"> &nbsp;Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="general_feedback_table_content" style="display: none;">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <div class="form-group">
                                    <table id="general_feedback_table" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="20">S.NO</th>
                                                <th>Feedback Name</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-------------------------------------------------------------------- General Feedback End --------------------------------------------------------->

        <!-------------------------------------------------------------------- Scheduled Feedback Start ------------------------------------------------------->

        <form id="scheduled_feedback_creation" name="scheduled_feedback_creation" action="" method="post" enctype="multipart/form-data">
            <div class="row gutters">
                <div class="col-12">
                    <div id="scheduled_feedback_content" style="margin-top: 20px; display: none;">
                        <input type="hidden" id="scheduled_feedback_id">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">General Info</div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                                        <div class="form-group">
                                            <label for="scheduled_feedback_type">Scheduled Feedback Type</label><span class="text-danger">*</span>
                                            <select class="form-control" name="scheduled_feedback_type" id="scheduled_feedback_type" tabindex="1">
                                                <option value="">Select Scheduled Feedback Type</option>
                                                <option value="1">Feedback Configuration</option>
                                                <option value="2">Ratings</option>
                                                <option value="3">Poll</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="display: flex; align-items: center;">
                                        <button type="button" name="scheduled_feedback_search" id="scheduled_feedback_search" class="btn btn-primary" value="Add" tabindex="18"> &nbsp;Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!---------------------------------------------------------------- Feedback Configuration Start ----------------------------------------------------------->

                    <div class="card" id="scheduled_feedback_configuration" style="display: none;">
                        <div class="card-header">
                            <div class="card-title">Feedback Configuration Info</div>
                        </div>
                        <div class="card-body">
                            <input type="hidden" id="feedback_titles_id">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="feedback_config_company_name">Company Name</label><span class="text-danger">*</span>
                                        <select class="form-control" id="feedback_config_company_name" name="feedback_config_company_name" tabindex="6">
                                            <option value="">Select Company Name</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="feedback_config_department_name">Department</label><span class="text-danger">*</span>
                                        <input type="hidden" id="feedback_config_department_name2">
                                        <select class="form-control" id="feedback_config_department_name" name="feedback_config_department_name[]" tabindex="4" multiple></select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="feedback_config_start_date">Start Date & Time</label>
                                        <span class="text-danger">*</span>
                                        <input type="datetime-local" class="form-control" id="feedback_config_start_date" name="feedback_config_start_date" tabindex="10">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="feedback_config_end_date">End Date & Time</label>
                                        <span class="text-danger">*</span>
                                        <input type="datetime-local" class="form-control" id="feedback_config_end_date" name="feedback_config_end_date" tabindex="11">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="feedback_title">Feedback Title</label><span class="text-danger">*</span>
                                        <input type="text" class="form-control" id="feedback_title" name="feedback_title" placeholder="Enter Feedback Title" tabindex="4">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="feedback_status">Status</label><span class="text-danger">*</span>
                                        <select class="form-control" name="feedback_status" id="feedback_status" tabindex="1">
                                            <option value="">Select Status</option>
                                            <option value="0">Active</option>
                                            <option value="1">In Active</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table
                                            class="table table-bordered align-middle question-table"
                                            id="feedback_question_table"
                                            data-type="feedback">

                                            <thead class="table-success">
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Feedback Questions</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="feedback_question_body">

                                                <tr>
                                                    <td>1</td>
                                                    <td>
                                                        <input
                                                            type="text"
                                                            class="form-control feedback_question"
                                                            name="feedback_question[]"
                                                            placeholder="Enter Feedback Question">
                                                    </td>
                                                    <td>
                                                        <button
                                                            type="button"
                                                            class="btn btn-success add-question-row">
                                                            Add
                                                        </button>
                                                        <button
                                                            type="button"
                                                            class="btn btn-danger remove-question-row">
                                                            Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-3 text-right">
                                <button name="submit_feedback_configuration" id="submit_feedback_configuration" class="btn btn-primary" tabindex="14"><span class="icon-check"></span>&nbsp;Submit</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="form-group">
                                        <table id="feedback_configuration_table" class="table custom-table">
                                            <thead>
                                                <tr>
                                                    <th width="20">S.NO</th>
                                                    <th>Feedback Title</th>
                                                    <th>Start Date & Time</th>
                                                    <th>End Date & Time</th>
                                                    <th>Department</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!----------------------------------------------------------------- Feedback Configuration End ------------------------------------------------------------>

                    <!-------------------------------------------------------------------- Ratings Start ---------------------------------------------------------------------->

                    <div class="card" id="scheduled_rating" style="display: none;">
                        <div class="card-header">
                            <div class="card-title">Rating Info</div>
                        </div>
                        <div class="card-body">
                            <input type="hidden" id="rating_titles_id">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="rating_company_name">Company Name</label><span class="text-danger">*</span>
                                        <select class="form-control" id="rating_company_name" name="rating_company_name" tabindex="6">
                                            <option value="">Select Company Name</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="rating_department_name">Department</label><span class="text-danger">*</span>
                                        <input type="hidden" id="rating_department_name2">
                                        <select class="form-control" id="rating_department_name" name="rating_department_name[]" tabindex="4" multiple></select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="rating_start_date">Start Date & Time</label>
                                        <span class="text-danger">*</span>
                                        <input type="datetime-local" class="form-control" id="rating_start_date" name="rating_start_date" tabindex="10">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="rating_end_date">End Date & Time</label>
                                        <span class="text-danger">*</span>
                                        <input type="datetime-local" class="form-control" id="rating_end_date" name="rating_end_date" tabindex="11">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="rating_title">Rating Title</label><span class="text-danger">*</span>
                                        <input type="text" class="form-control" id="rating_title" name="rating_title" placeholder="Enter Rating Title" tabindex="4">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="rating_description">Description</label><span class="text-danger">*</span>
                                        <textarea class="form-control custom-form-control" id="rating_description" name="rating_description" tabindex="10" placeholder="Add your description here..."></textarea>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="rating_status">Status</label><span class="text-danger">*</span>
                                        <select class="form-control" name="rating_status" id="rating_status" tabindex="1">
                                            <option value="">Select Status</option>
                                            <option value="0">Active</option>
                                            <option value="1">In Active</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-3 text-right">
                                <button name="submit_rating" id="submit_rating" class="btn btn-primary" tabindex="14"><span class="icon-check"></span>&nbsp;Submit</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="form-group">
                                        <table id="rating_table" class="table custom-table">
                                            <thead>
                                                <tr>
                                                    <th width="20">S.NO</th>
                                                    <th>Rating Title</th>
                                                    <th>Start Date & Time</th>
                                                    <th>End Date & Time</th>
                                                    <th>Department</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-------------------------------------------------------------------- Ratings End ---------------------------------------------------------------------->

                    <!--------------------------------------------------------------------- Poll Start ---------------------------------------------------------------------->

                    <div class="card" id="scheduled_poll" style="display: none;">
                        <div class="card-header">
                            <div class="card-title">Poll Info</div>
                        </div>
                        <div class="card-body">
                            <input type="hidden" id="poll_titles_id">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="poll_company_name">Company Name</label><span class="text-danger">*</span>
                                        <select class="form-control" id="poll_company_name" name="poll_company_name" tabindex="6">
                                            <option value="">Select Company Name</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="poll_department_name">Department</label><span class="text-danger">*</span>
                                        <input type="hidden" id="poll_department_name2">
                                        <select class="form-control" id="poll_department_name" name="poll_department_name[]" tabindex="4" multiple></select>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="poll_start_date">Start Date & Time</label>
                                        <span class="text-danger">*</span>
                                        <input type="datetime-local" class="form-control" id="poll_start_date" name="poll_start_date" tabindex="10">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="poll_end_date">End Date & Time</label>
                                        <span class="text-danger">*</span>
                                        <input type="datetime-local" class="form-control" id="poll_end_date" name="poll_end_date" tabindex="11">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="poll_title">Poll Title</label><span class="text-danger">*</span>
                                        <input type="text" class="form-control" id="poll_title" name="poll_title" placeholder="Enter Poll Title" tabindex="4">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="poll_description">Description</label><span class="text-danger">*</span>
                                        <textarea class="form-control custom-form-control" id="poll_description" name="poll_description" tabindex="10" placeholder="Add your description here..."></textarea>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                                    <div class="form-group">
                                        <label for="poll_status">Status</label><span class="text-danger">*</span>
                                        <select class="form-control" name="poll_status" id="poll_status" tabindex="1">
                                            <option value="">Select Status</option>
                                            <option value="0">Active</option>
                                            <option value="1">In Active</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table
                                            class="table table-bordered align-middle question-table"
                                            id="poll_options_table"
                                            data-type="poll">
                                            <thead class="table-success">
                                                <tr>
                                                    <th>S.No</th>
                                                    <th>Poll Options</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="poll_question_body">
                                                <tr>
                                                    <td>1</td>
                                                    <td>
                                                        <input
                                                            type="text"
                                                            class="form-control poll_option"
                                                            name="poll_option[]"
                                                            placeholder="Enter Poll Question">
                                                    </td>
                                                    <td>
                                                        <button
                                                            type="button"
                                                            class="btn btn-success add-question-row">
                                                            Add
                                                        </button>

                                                        <button
                                                            type="button"
                                                            class="btn btn-danger remove-question-row">
                                                            Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-3 text-right">
                                <button name="submit_poll" id="submit_poll" class="btn btn-primary" tabindex="14"><span class="icon-check"></span>&nbsp;Submit</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="form-group">
                                        <table id="poll_table" class="table custom-table">
                                            <thead>
                                                <tr>
                                                    <th width="20">S.NO</th>
                                                    <th>Poll Title</th>
                                                    <th>Start Date & Time</th>
                                                    <th>End Date & Time</th>
                                                    <th>Department</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!------------------------------------------------------------------ Poll End ---------------------------------------------------------------------->

                </div>
            </div>
        </form>

        <!-------------------------------------------------------------------- Scheduled Feedback End ---------------------------------------------------------------->

    </div>
</div>