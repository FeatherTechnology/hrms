<div class="row gutters">
    <div class="col-12">
        <div class="feedback">
            <div class="radio-container">
                <div class="selector">
                    <div class="selector-item">
                        <input type="radio" id="general_feedback" name="feedback_type" class="selector-item_radio" value="0" checked>
                        <label for="general_feedback" class="selector-item_label">General Feedback</label>
                    </div>
                    <div class="selector-item">
                        <input type="radio" id="scheduled_feedback" name="feedback_type" class="selector-item_radio" value="1">
                        <label for="scheduled_feedback" class="selector-item_label">Scheduled Feedback</label>
                    </div>

                </div>
            </div>
            <br> <br>

            <div class="card" id="general_feedback_div" style="display: none;">
                <div class="card-header">
                    <h5 class="card-title">General Feedback</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="feedback_name">Feedback Name</label><span class="text-danger">*</span>
                                <select class="form-control" id="feedback_name" name="feedback_name" tabindex="1">
                                    <option> Select Feedback Name</option>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="commants">Commants</label><span class="text-danger">*</span>
                                <textarea type="textarea" class="form-control" id="commants" name="commants" placeholder="Enter Commants" tabindex="2"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="user_name">User Name</label><span class="text-danger">*</span>
                                <select class="form-control" id="user_name" name="user_name" tabindex="1">
                                    <option> Select User Name</option>

                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <label for="attachment">Attachment</label>
                                <input type="file" class="form-control" name="attachment" id="attachment" onchange="compressImage(this, 200)" tabindex="3">
                            </div>
                        </div>
                        <div class="col-12" style="text-align:right">
                            <div class="form-group">
                                <button name="submit_gen_feedback" id="submit_gen_feedback" class="btn btn-primary" tabindex="4" style="margin-top: 18px;"><span class="icon-check"></span>&nbsp;Submit</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <div class="card scheduled_feedback_div" style="display: none;">


                <div class="card-header">
                    <h5 class="card-title">Scheduled Feedback List</h5>
                </div>
                <div class="card-body">
                    <div id="scheduled_feedback_div" class="table-divs" style="overflow-x: auto;">
                        <table id="scheduled_feedback_table" class="table custom-table">
                            <thead>
                                <th>S.No</th>
                                <th>Feedback Questions</th>
                                <th>Status</th>
                                <th>Action</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>



            <div class="card scheduled_feedback_ans_div" style="display: none;">

                <div class="card-header">
                    <div style="text-align: left;">
                        <h5 class="card-title">Feedback Question</h5>
                    </div>
                    <div style="text-align: right;">
                        <button type="button" class="btn btn-primary" id="back_btn"><span class="icon-arrow-left"></span>&nbsp; Back </button>
                    </div>

                </div>
                <div class="card-body">
                    <div id="scheduled_feedback_ans_div">
                        <div class="feedbackQuestionDiv"></div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>