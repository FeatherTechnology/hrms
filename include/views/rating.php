<div class="col-12">
    <style>
        /* Modern Card Styling */
        .ratings-ui-card {
            background-color: #f0f4f8;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 25px 30px;
            margin-top: 20px;
        }

        /* Star Rating Container */
        .star-rating-wrapper {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            flex-wrap: wrap;
            gap: 18px;
        }

        .rating-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 42px;
            position: relative;
        }

        .rating-item input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        /* Star Styling */
        .rating-item label {
            font-size: 43px;
            line-height: 1.2;
            cursor: pointer;
            color: #cbd5e1;
            transition: color 0.25s ease, transform 0.25s ease;
            margin-bottom: 0;
        }

        /* Hover State */
        .rating-item label:hover {
            transform: scale(1.15);
        }

        /* Selected Star Base & Glow */
        .rating-item input[type="radio"]:checked+label {
            transform: scale(1.15);
            filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.18));
        }

        /* Text Under Stars */
        .rating-item .rating-text {
            font-size: 13px;
            font-weight: 700;
            margin-top: 5px;
            color: #536174;
        }

        /* --- Selected Star Colors (1 to 10) --- */
        .rating-item:nth-child(1) input:checked+label,
        .rating-item:nth-child(1) input:checked~.rating-text {
            color: #b91c1c;
        }

        .rating-item:nth-child(2) input:checked+label,
        .rating-item:nth-child(2) input:checked~.rating-text {
            color: #f04545;
        }

        .rating-item:nth-child(3) input:checked+label,
        .rating-item:nth-child(3) input:checked~.rating-text {
            color: #ea580c;
        }

        .rating-item:nth-child(4) input:checked+label,
        .rating-item:nth-child(4) input:checked~.rating-text {
            color: #f97316;
        }

        .rating-item:nth-child(5) input:checked+label,
        .rating-item:nth-child(5) input:checked~.rating-text {
            color: #eab308;
        }

        .rating-item:nth-child(6) input:checked+label,
        .rating-item:nth-child(6) input:checked~.rating-text {
            color: #c0ca33;
        }

        .rating-item:nth-child(7) input:checked+label,
        .rating-item:nth-child(7) input:checked~.rating-text {
            color: #84b547;
        }

        .rating-item:nth-child(8) input:checked+label,
        .rating-item:nth-child(8) input:checked~.rating-text {
            color: #4d9f54;
        }

        .rating-item:nth-child(9) input:checked+label,
        .rating-item:nth-child(9) input:checked~.rating-text {
            color: #25864a;
        }

        .rating-item:nth-child(10) input:checked+label,
        .rating-item:nth-child(10) input:checked~.rating-text {
            color: #166534;
        }

        /* Badge Default & Dynamic Colors */
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

        .rating-badge.rating-1 {
            background: #b91c1c;
            color: #fff;
        }

        .rating-badge.rating-2 {
            background: #f04545;
            color: #fff;
        }

        .rating-badge.rating-3 {
            background: #ea580c;
            color: #fff;
        }

        .rating-badge.rating-4 {
            background: #f97316;
            color: #fff;
        }

        .rating-badge.rating-5 {
            background: #eab308;
            color: #fff;
        }

        .rating-badge.rating-6 {
            background: #c0ca33;
            color: #26320a;
        }

        .rating-badge.rating-7 {
            background: #84b547;
            color: #fff;
        }

        .rating-badge.rating-8 {
            background: #4d9f54;
            color: #fff;
        }

        .rating-badge.rating-9 {
            background: #25864a;
            color: #fff;
        }

        .rating-badge.rating-10 {
            background: #166534;
            color: #fff;
        }

        /* Form Inputs Customization */
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

        /* NEW: The Outer Box */
        .outer-rating-box {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 25px 20px;
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            margin-bottom: 25px;
        }

        @media (max-width: 576px) {
            .star-rating-wrapper {
                gap: 8px;
            }

            .rating-item {
                min-width: 24px;
            }

            .rating-item label {
                font-size: 30px;
            }

            .rating-item .rating-text {
                font-size: 12px;
            }
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
                <div class="col-12" style="overflow-x: auto;">
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

            <div class="ratings-ui-card p-4">
                <!-- Header -->
                <div class="row align-items-center mb-3">
                    <div class="col-12">
                        <h4 class="mb-0" style="font-size: 18px; color: #334155;">
                            Ratings Question
                        </h4>
                    </div>
                </div>

                <div class="outer-rating-box">
                    <!-- Question Title -->
                    <p id="rating_question" style="font-size: 25px; color: #475569; margin-bottom: 20px;">
                        1. xxxx
                    </p>

                    <!-- Rating Stars & Badge Row -->
                    <div class="row align-items-center justify-content-center my-4">
                        <!-- Stars Wrapper (Centered/Left) -->
                        <div class="col-auto">
                            <div class="star-rating-wrapper d-flex gap-3">
                                <div class="rating-item text-center">
                                    <input type="radio" id="star1" name="ratingOption" value="1" data-rating="1">
                                    <label for="star1">&#9733;</label>
                                    <span class="rating-text d-block">1</span>
                                </div>
                                <div class="rating-item text-center">
                                    <input type="radio" id="star2" name="ratingOption" value="2" data-rating="2">
                                    <label for="star2">&#9733;</label>
                                    <span class="rating-text d-block">2</span>
                                </div>
                                <div class="rating-item text-center">
                                    <input type="radio" id="star3" name="ratingOption" value="3" data-rating="3">
                                    <label for="star3">&#9733;</label>
                                    <span class="rating-text d-block">3</span>
                                </div>
                                <div class="rating-item text-center">
                                    <input type="radio" id="star4" name="ratingOption" value="4" data-rating="4">
                                    <label for="star4">&#9733;</label>
                                    <span class="rating-text d-block">4</span>
                                </div>
                                <div class="rating-item text-center">
                                    <input type="radio" id="star5" name="ratingOption" value="5" data-rating="5">
                                    <label for="star5">&#9733;</label>
                                    <span class="rating-text d-block">5</span>
                                </div>
                                <div class="rating-item text-center">
                                    <input type="radio" id="star6" name="ratingOption" value="6" data-rating="6">
                                    <label for="star6">&#9733;</label>
                                    <span class="rating-text d-block">6</span>
                                </div>
                                <div class="rating-item text-center">
                                    <input type="radio" id="star7" name="ratingOption" value="7" data-rating="7">
                                    <label for="star7">&#9733;</label>
                                    <span class="rating-text d-block">7</span>
                                </div>
                                <div class="rating-item text-center">
                                    <input type="radio" id="star8" name="ratingOption" value="8" data-rating="8">
                                    <label for="star8">&#9733;</label>
                                    <span class="rating-text d-block">8</span>
                                </div>
                                <div class="rating-item text-center">
                                    <input type="radio" id="star9" name="ratingOption" value="9" data-rating="9">
                                    <label for="star9">&#9733;</label>
                                    <span class="rating-text d-block">9</span>
                                </div>
                                <div class="rating-item text-center">
                                    <input type="radio" id="star10" name="ratingOption" value="10" data-rating="10">
                                    <label for="star10">&#9733;</label>
                                    <span class="rating-text d-block">10</span>
                                </div>
                            </div>
                        </div>

                        <!-- Badge Moved to Right Side of Stars -->
                        <div class="col-auto ms-4">
                            <div id="ratings_badge" class="rating-badge">
                                Please select a rating
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="ratings" name="ratings">
                    <div class="row justify-content-center align-items-center mt-4">
                        <div class="col-xl-6 col-lg-7 col-md-8 col-sm-12">
                            <div class="d-flex align-items-end">
                                <textarea class="form-control custom-form-control flex-grow-1" id="reason" name="reason" tabindex="10" rows="3" placeholder="Add your comments here..."></textarea>

                                <!-- Added wrapper container with left margin -->
                                <div class="col-auto ms-4">
                                    <button name="submit_rating_question" id="submit_rating_question" class="btn btn-primary px-4" tabindex="14">
                                        <span class="icon-check"></span>&nbsp;Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>