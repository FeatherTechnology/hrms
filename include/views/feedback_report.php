<br>
<div class="row gutters">
    <div class="col-12">
        <div class="toggle-container col-12">
            <input type="date" id='from_date' name='from_date' class="toggle-button" value=''>
            <input type="date" id='to_date' name='to_date' class="toggle-button" value=''>
            <select class="toggle-button" name='feedback_type' id='feedback_type'>
                <option value="">Select Feedback Type</option>
                <option value="1">General Feedback</option>
                <option value="2">Feedback Configuration</option>
                <option value="3">Rating</option>
                <option value="4">Poll</option>
            </select>
            <select class="toggle-button" name='company_id' id='company_id'>
                <option value=''>Select Company</option>
            </select>
            <select class="toggle-button" name='department_id' id='department_id' style="display: none">
                <option value=''>Select Department</option>
            </select>
            <select class="toggle-button" name='title' id='title'>
                <option value=''>Select Title</option>
            </select>
            <select class="toggle-button" name='question' id='question' style="display: none">
                <option value=''>Select Question</option>
            </select>
            <input type="button" id='feedback_btn' name='feedback_btn' class="toggle-button" style="background-color: #f26b35;color:white" value='Search'>
        </div> <br> <br>
        <!-- Feedback report Start -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header" id="report_header">Feedback Report</div>
                <div class="card-body">
                    <div id="feedback_table_div" style="overflow-x: auto;">
                        <table id="general_feedback_table" class="table custom-table" style="display: none">
                            <thead>
                                <tr>
                                    <th>S.NO</th>
                                    <th>Staff ID</th>
                                    <th>Staff Name</th>
                                    <th>Feedback Title</th>
                                    <th>Comments</th>
                                    <th>Attachment</th>
                                    <th>Submited Date</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <table id="feedback_configuration_table" class="table custom-table" style="display: none">
                            <thead>
                                <tr>
                                    <th>S.NO</th>
                                    <th>Staff ID</th>
                                    <th>Staff Name</th>
                                    <th>Department Name</th>
                                    <th>Feedback Title</th>
                                    <th>Question</th>
                                    <th>Answer</th>
                                    <th>Submited Date</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <table id="rating_table" class="table custom-table" style="display: none">
                            <thead>
                                <tr>
                                    <th>S.NO</th>
                                    <th>Staff ID</th>
                                    <th>Staff Name</th>
                                    <th>Department Name</th>
                                    <th>Rating Title</th>
                                    <th>Rating</th>
                                    <th>Comments</th>
                                    <th>Submited Date</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <table id="poll_table" class="table custom-table" style="display: none">
                            <thead>
                                <tr>
                                    <th>S.NO</th>
                                    <th>Staff ID</th>
                                    <th>Staff Name</th>
                                    <th>Department Name</th>
                                    <th>Poll Title</th>
                                    <th>Answer</th>
                                    <th>Comments</th>
                                    <th>Submited Date</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Feedback report End -->
    </div>
</div>