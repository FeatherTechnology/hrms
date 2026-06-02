<div class="col-12">
    <style>
        /* Modern Card Styling */
        .ratings-ui-card {
            background-color: #f0f4f8;
            /* Soft blue-grey background */
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 25px 30px;
            margin-top: 20px;
        }

        /* Star Rating Container */
        .star-rating-wrapper {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 70px;
            /* Space between stars */
        }

        .rating-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 70px;
            /* Fixed width to keep text balanced */
        }

        .rating-item input[type="radio"] {
            display: none;
        }

        /* Star Styling */
        .rating-item label {
            font-size: 70px;
            color: #cbd5e1;
            /* Light grey by default */
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            line-height: 1;
            margin-bottom: 8px;
        }

        /* Hover State */
        .rating-item label:hover {
            transform: scale(1.15);
        }

        /* Checked State Base - Makes the selected star larger */
        .rating-item input[type="radio"]:checked+label {
            transform: scale(1.25);
        }

        /* --- 1. Poor (Red) --- */
        #star1:checked+label {
            color: #ef4444;
            text-shadow: 0 0 12px rgba(239, 68, 68, 0.4);
        }

        #star1:checked~.rating-text {
            color: #ef4444;
        }

        /* --- 2. Below Average (Orange) --- */
        #star2:checked+label {
            color: #f97316;
            text-shadow: 0 0 12px rgba(249, 115, 22, 0.4);
        }

        #star2:checked~.rating-text {
            color: #f97316;
        }

        /* --- 3. Average (Yellow) --- */
        #star3:checked+label {
            color: #eab308;
            text-shadow: 0 0 12px rgba(234, 179, 8, 0.4);
        }

        #star3:checked~.rating-text {
            color: #eab308;
        }

        /* --- 4. Above Average (Light Green) --- */
        #star4:checked+label {
            color: #70ee70;
            text-shadow: 0 0 12px rgba(34, 197, 94, 0.4);
        }

        #star4:checked~.rating-text {
            color: #70ee70;
        }

        /* --- 5. Excellent (Dark Green) --- */
        #star5:checked+label {
            color: #16a34a;
            text-shadow: 0 0 12px rgba(22, 163, 74, 0.4);
        }

        #star5:checked~.rating-text {
            color: #16a34a;
        }

        /* Text Under Stars */
        .rating-text {
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            line-height: 1.2;
        }

        /* Form Inputs Customization */
        .custom-form-label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }

        .custom-form-control {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background-color: #ffffff;
            transition: border-color 0.2s;
        }

        .custom-form-control:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .rating-badge {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 5px;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            background-color: #94a3b8;
            min-width: 160px;
            text-align: center;
            transition: 0.3s;
        }

        /* Rating Colors */
        .rating-poor {
            background-color: #ef4444;
        }

        .rating-below {
            background-color: #f97316;
        }

        .rating-average {
            background-color: #eab308;
        }

        .rating-above {
            background-color: #70ee70;
        }

        .rating-excellent {
            background-color: #16a34a;
        }

        /* NEW: The Outer Box */
        .outer-rating-box {
            border: 1px solid #cbd5e1;
            /* Clean grey border */
            border-radius: 10px;
            /* Rounded corners */
            padding: 25px 20px;
            /* Spacing inside the box */
            background-color: #ffffff;
            /* White background inside the box */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            /* Subtle shadow for depth */
            margin-bottom: 25px;
        }
    </style>

    <!--- Back Button --->
    <div class="col-12 text-right">
        <button class="btn btn-primary backBtn" style="display: none;"><span class="icon-arrow-left"></span> Back</button>
    </div><br />

    <!--- Ratings Table Content --->
    <div class="card ratings_table_content">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <table id="ratings_table" class="table custom-table">
                        <thead>
                            <tr>
                                <th width="50">S.No.</th>
                                <th>Ratings Question</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody> </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--- Ratings Question Content --->
    <div id="ratings_question_content" style="display: none;">
        <form id="ratings_question_form" name="ratings_question_form" method="post" enctype="multipart/form-data">
            <input type="hidden" id="rating_titles_id">
            <div class="ratings-ui-card">
                <div class="row align-items-center mb-4">
                    <div class="col-12">
                        <h4 class="mb-2"
                            style="font-size: 18px; color: #334155;">
                            Ratings Question
                        </h4>
                    </div>
                </div>
                <div class="outer-rating-box">
                    <p id="rating_question"
                        style="font-size: 25px ; margin-left: 20px; color: #475569; margin-bottom: 0;">
                    </p>
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12" style="margin-left: 350px;margin-top: 20px; ">
                            <div class="star-rating-wrapper">
                                <div class="rating-item">
                                    <input type="radio" id="star1" name="ratingOption" value="1" data-rating="Poor">
                                    <label for="star1">&#9734;</label>
                                    <span class="rating-text">Poor</span>
                                </div>
                                <div class="rating-item">
                                    <input type="radio" id="star2" name="ratingOption" value="2" data-rating="Below Average">
                                    <label for="star2">&#9734;</label>
                                    <span class="rating-text">Below<br>Average</span>
                                </div>
                                <div class="rating-item">
                                    <input type="radio" id="star3" name="ratingOption" value="3" data-rating="Average">
                                    <label for="star3">&#9734;</label>
                                    <span class="rating-text">Average</span>
                                </div>
                                <div class="rating-item">
                                    <input type="radio" id="star4" name="ratingOption" value="4" data-rating="Above Average">
                                    <label for="star4">&#9734;</label>
                                    <span class="rating-text">Above<br>Average</span>
                                </div>
                                <div class="rating-item">
                                    <input type="radio" id="star5" name="ratingOption" value="5" data-rating="Excellent">
                                    <label for="star5">&#9734;</label>
                                    <span class="rating-text">Excellent</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0" style="margin-left: 200px;margin-top: 50px;">
                            <div id="ratings_badge" class="rating-badge">
                                Please select a rating</div>
                        </div>
                    </div>
                    <div class="row" style="margin-left: 285px;margin-top: 20px;">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <textarea class="form-control custom-form-control" id="reason" name="reason" tabindex="10" rows="3" placeholder="Add your comments here..."></textarea>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12" style="margin-top: 15px;">
                            <button name="submit_rating_question" id="submit_rating_question" class="btn btn-primary px-4" tabindex="14">
                                <span class="icon-check"></span>&nbsp;Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>