<div class="row gutters">

    <!-- Dashboard Cards -->
    <div class="col-12">
        <div class="row">

            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12">
                <div class="card" id="counts_div">
                    <div class="card-body d-flex justify-content-between align-items-center">

                        <div>
                            <h6 class="mb-1">Active Feedback</h6>

                            <h1 class="active_feedback m-2 font-weight-bold" style="font-size:70px">0</h1>

                            <span id="feedbackViewAll" class="text-primary" style="cursor:pointer;"> View All >>>></span>
                        </div>

                        <div>
                            <span class="icon-message-square"
                                style="font-size:60px;color:#4e73df;">
                            </span>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12">
                <div class="card" id="counts_div">
                    <div class="card-body d-flex justify-content-between align-items-center">

                        <div>
                            <h6 class="mb-1">Active Rating</h6>

                            <h1 class="active_rating m-2 font-weight-bold" style="font-size:70px">0</h1>

                            <span
                                id="ratingViewAll"
                                class="text-primary"
                                style="cursor:pointer;">
                                View All >>>>
                            </span>
                        </div>

                        <div>
                            <span class="icon-star"
                                style="font-size:60px;color:#1cc88a;">
                            </span>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12">
                <div class="card" id="counts_div">
                    <div class="card-body d-flex justify-content-between align-items-center">

                        <div>
                            <h6 class="mb-1">Active Poll</h6>

                            <h1 class="active_poll m-2 font-weight-bold" style="font-size:70px">0</h1>

                            <span
                                id="pollViewAll"
                                class="text-primary"
                                style="cursor:pointer;">
                                View All >>>>
                            </span>
                        </div>

                        <div>
                            <span class="icon-bar-chart-2"
                                style="font-size:60px;color:#f6c23e;">
                            </span>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 col-12">
                <div class="card" style="padding: 12px;" id="counts_div">
                    <div class="card-body d-flex justify-content-between align-items-center">

                        <div>
                            <h6 class="mb-1">Average Rating</h6>

                            <h1 class="average_rating m-2 font-weight-bold" style="font-size:80px">0</h1>
                        </div>

                        <div>
                            <span class="icon-award"
                                style="font-size:60px;color:#9b59b6;">
                            </span>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Attendance Overview -->
    <div class="col-lg-6 col-md-12 mt-3">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title">Active Feedback</h5>
            </div>

            <div class="card-body">

                <div class="feedback_list">
                    <div id="feedback_table_div" class="table-divs" style="overflow-x: auto;">
                        <table id="feedback_table" class="table custom-table">
                            <thead>
                                <th>S.No</th>
                                <th>Feedback Title</th>
                                <th>Start Date</th>
                                <th>End Rate Date</th>
                                <th>Total Response</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>


                </div>


            </div>

        </div>
    </div>
    <div class="col-lg-6 col-md-12 mt-3">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title">Active Rating</h5>
            </div>

            <div class="card-body">

                <div class="rating_list">
                    <div id="ratings_table_div" class="table-divs" style="overflow-x: auto;">
                        <table id="ratings_table" class="table custom-table">
                            <thead>
                                <th>S.No</th>
                                <th>Rating Questions</th>
                                <th>Start Date</th>
                                <th>End Rate Date</th>
                                <th>Total Response</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>


                </div>
                
            </div>

        </div>
    </div>
    <div class="col-lg-12 col-md-12 mt-3">
        <div class="card">

            <div class="card-header">
                <h5 class="card-title">Active Poll</h5>
            </div>

            <div class="card-body">

                <div class="poll_list">
                    <div id="polls_table_div" class="table-divs" style="overflow-x: auto;">
                        <table id="polls_table" class="table custom-table">
                            <thead>
                                <th>S.No</th>
                                <th>Poll Questions</th>
                                <th>Start Date</th>
                                <th>End Rate Date</th>
                                <th>Total Response</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>


                </div>
            </div>

        </div>
    </div>

</div>



<!-- in this css we used the card body, main css also have the style for card . so we use the style here seperately -->
<style>
    /* =========================================
   DASHBOARD BACKGROUND
========================================= */
.body {
    background: #f4f7fc;
    background-image:
        radial-gradient(circle at top right,
            rgba(78, 115, 223, 0.08),
            transparent 30%),
        radial-gradient(circle at bottom left,
            rgba(28, 200, 138, 0.08),
            transparent 30%);
}

/* =========================================
   COMMON CARD STYLE
========================================= */
.card {
    border: none;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    overflow: hidden;
}

#counts_div:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
}

/* =========================================
   KPI CARDS
========================================= */
.col-xl-3 .card {
    color: #fff;
}


/* Active Feedback */
.col-xl-3:nth-child(1) .card {
    background: linear-gradient(135deg, #667eea, #764ba2);
}

/* Active Rating */
.col-xl-3:nth-child(2) .card {
    background: linear-gradient(135deg, #00c9a7, #00e4d0);
}

/* Active Poll */
.col-xl-3:nth-child(3) .card {
    background: linear-gradient(135deg, #f7971e, #ffd200);
}

/* Average Rating */
.col-xl-3:nth-child(4) .card {
    background: linear-gradient(135deg, #ff416c, #fd9a60);
}

/* =========================================
   KPI TITLE
========================================= */
.col-xl-3 h6 {
    font-size: 14px;
    font-weight: 600;
    opacity: 0.9;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

/* =========================================
   KPI NUMBER
========================================= */
.active_feedback,
.active_rating,
.active_poll,
.average_rating {
    font-size: 60px !important;
    font-weight: 700 !important;
    line-height: 1;
    margin: 10px 0 !important;
    color: #fff;
}

/* =========================================
   ICON DESIGN
========================================= */
.icon-message-square,
.icon-star,
.icon-bar-chart-2,
.icon-award {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255,255,255,0.18);
    color: #fff !important;
    font-size: 36px !important;
}

/* =========================================
   VIEW ALL BUTTONS
========================================= */
#feedbackViewAll,
#ratingViewAll,
#pollViewAll {
    display: inline-block;
    margin-top: 10px;
    font-weight: 600;
    color: rgba(255,255,255,0.9) !important;
    text-decoration: none;
    transition: all .3s ease;
}

#feedbackViewAll:hover,
#ratingViewAll:hover,
#pollViewAll:hover {
    color: #fff !important;
    letter-spacing: 1px;
}

/* =========================================
   SECTION CARDS
========================================= */
.col-lg-6 .card,
.col-lg-12 .card {
    background: #fff;
    color: #333;
}

/* =========================================
   SECTION HEADERS
========================================= */
.card-header {
    background: transparent;
    border-bottom: 1px solid #edf2f9;
    padding: 18px 25px;
}

.card-title {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #344767;
}

/* =========================================
   TABLE STYLE
========================================= */
.table {
    margin-bottom: 0;
}

.table thead {
    background: #4e73df;
}

.table thead th {
    color: #fff;
    border: none;
    padding: 14px;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
}

.table tbody td {
    padding: 14px;
    vertical-align: middle;
    border-color: #edf2f9;
}

.table tbody tr {
    transition: all .3s ease;
}

.table tbody tr:hover {
    background: #f7faff;
}

/* =========================================
   TABLE SCROLLBAR
========================================= */
.table-divs::-webkit-scrollbar {
    height: 6px;
}

.table-divs::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.table-divs::-webkit-scrollbar-thumb {
    background: #4e73df;
    border-radius: 10px;
}

/* =========================================
   SECTION VIEW ALL LINKS
========================================= */
.col-lg-6 .text-primary,
.col-lg-12 .text-primary {
    color: #4e73df !important;
    font-weight: 600;
    cursor: pointer;
    transition: all .3s ease;
}

.col-lg-6 .text-primary:hover,
.col-lg-12 .text-primary:hover {
    letter-spacing: 1px;
}

/* =========================================
   CARD BODY SPACING
========================================= */
.card-body {
    padding: 25px;
}

/* =========================================
   RESPONSIVE
========================================= */
@media (max-width: 768px) {

    .active_feedback,
    .active_rating,
    .active_poll,
    .average_rating {
        font-size: 45px !important;
    }

    .icon-message-square,
    .icon-star,
    .icon-bar-chart-2,
    .icon-award {
        width: 60px;
        height: 60px;
        font-size: 28px !important;
    }

    .card-body {
        padding: 18px;
    }
}

/* =========================================
   HOVER SHINE EFFECT
========================================= */

.col-xl-3 .card {
    position: relative;
    overflow: hidden;
}

.col-xl-3 .card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -120%;
    width: 50%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255,255,255,0.4),
        transparent
    );
    transform: skewX(-25deg);
    z-index: 1;
    pointer-events: none;
}

.col-xl-3 .card:hover::before {
    animation: shine 0.8s ease-in-out;
}

@keyframes shine {
    from {
        left: -120%;
    }
    to {
        left: 150%;
    }
}
</style>