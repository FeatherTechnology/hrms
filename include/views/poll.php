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

        /* ------------------- Base & Table Styling ------------------- */
        .poll_table_content {
            background-color: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
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

        /* Typography */
        .poll-heading {
            font-size: 14px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 25px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 10px;
        }

        .poll-instruction {
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }

        /* ------------------- Dynamic Poll Options ------------------- */
        .poll-options-container {
            display: flex;
            flex-direction: column;
            gap: 25px;
            /* Smooth spacing between injected options */
        }

        .poll-option-wrapper input[type="radio"] {
            display: none;
            /* Hide the default browser radio button */
        }

        /* The Clickable Tile */
        .poll-option-label {
            display: flex;
            align-items: center;
            background-color: #ffffff;
            border: 2px solid #e2e8f0;
            padding: 16px 24px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 0;
            position: relative;
            overflow: hidden;
        }

        /* Hover State */
        .poll-option-label:hover {
            border-color: #94a3b8;
            background-color: #f8fafc;
            transform: translateY(-2px);
            /* Slight lift effect */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Inner Elements */
        .opt-num {
            width: 30px;
            font-weight: 700;
            color: #94a3b8;
            font-size: 16px;
        }

        .radio-circle {
            width: 24px;
            height: 24px;
            border: 2px solid #cbd5e1;
            border-radius: 50%;
            margin-right: 15px;
            position: relative;
            transition: all 0.2s ease;
            background-color: #fff;
            flex-shrink: 0;
        }

        .opt-text {
            color: #334155;
            font-size: 16px;
            font-weight: 600;
        }

        /* ------------------- Active/Checked State (The Magic) ------------------- */
        .poll-option-wrapper input[type="radio"]:checked+.poll-option-label {
            border-color: #3b82f6;
            /* Modern primary blue */
            background-color: #eff6ff;
            /* Very light blue tint */
            transform: translateY(0);
            box-shadow: none;
        }

        .poll-option-wrapper input[type="radio"]:checked+.poll-option-label .radio-circle {
            border-color: #3b82f6;
            background-color: #3b82f6;
        }

        /* The white dot inside the active radio */
        .poll-option-wrapper input[type="radio"]:checked+.poll-option-label .radio-circle::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 8px;
            background: #ffffff;
            border-radius: 50%;
        }

        .poll-option-wrapper input[type="radio"]:checked+.poll-option-label .opt-num,
        .poll-option-wrapper input[type="radio"]:checked+.poll-option-label .opt-text {
            color: #1e3a8a;
            /* Deep blue text when active */
        }

        /* ------------------- Form Inputs & Buttons ------------------- */
        .custom-form-control {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 16px;
            font-size: 15px;
            color: #334155;
            transition: all 0.2s;
            background-color: #f8fafc;
        }

        .custom-form-control:focus {
            border-color: #3b82f6;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .btn-submit-poll {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            border: none;
            color: white;
            padding: 14px 40px;
            font-size: 16px;
            font-weight: 700;
            border-radius: 50px;
            /* Pill shape */
            box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
        }

        .btn-submit-poll:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.4);
            color: white;
        }
    </style>

    <!--- Back Button --->
    <div class="col-12 text-right">
        <button class="btn btn-outline-secondary backBtn" style="display: none; border-radius: 8px;">
            <span class="icon-arrow-left"></span> Back
        </button>
    </div><br />

    <!--- Poll Table Content --->
    <div class="card poll_table_content">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <table id="poll_table" class="table custom-table">
                        <thead>
                            <tr>
                                <th width="50">S.No.</th>
                                <th>Poll Question</th>
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

    <!--- Poll Question Content --->
    <div class="col-12 d-flex justify-content-center">
        <div id="poll_question_content" style="display: none; width: 100%;">
            <form id="poll_question_form" name="poll_question_form" method="post" enctype="multipart/form-data">
                <input type="hidden" id="poll_titles_id">
                <div class="ratings-ui-card">
                    <div class="row align-items-center mb-4">
                        <div class="col-12">
                            <h4 class="mb-2"
                                style="font-size: 18px; color: #334155;">
                                Poll Question
                            </h4>
                        </div>
                    </div>

                    <div class="outer-rating-box">
                        <p id="poll_question"
                            style="font-size: 25px ; margin-left: 20px; color: #475569; margin-bottom: 10px;">
                        </p>
                        <div class="row">
                            <p class="poll-instruction" style="font-size: 16px ; margin-left: 370px; margin-bottom: -5px;">Please select one of the following options:</p>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 poll-options-container" id="poll_options_container" style="margin-left: 350px;margin-top: 20px; ">
                            </div>
                        </div>
                        <div class="row" style="margin-left: 335px;margin-top: 20px;">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <textarea class="form-control custom-form-control" id="reason" name="reason" tabindex="10" rows="3" placeholder="Add your comments here..."></textarea>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12" style="margin-top: 30px;">
                                <button name="submit_poll_question" id="submit_poll_question" class="btn btn-primary px-4" tabindex="14">
                                    <span class="icon-check"></span>&nbsp;Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>