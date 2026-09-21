<!-- Page wrapper start -->
<style>
    .notification-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        background: #FFFFFF;
    }

    .notification-table th,
    .notification-table td {
        border: 1px solid #000;
        padding: 6px 8px;
    }

    .notification-table thead th {
        background: #4a5258;
        /* Light gray header */
        color: #FFFFFF;
        /* Black text */
        font-weight: 600;
    }

    .notification-table .table-title {
        background: #FFFFFF;
        /* Slightly darker for title */
        color: #f26b35;
        text-align: center;
        font-weight: 600;
    }

    .notification-table td:first-child {
        text-align: left;
    }

    .notification-table td.text-center,
    .notification-table th {
        text-align: center;
    }

    .notification-table tbody tr:hover {
        background: #FFFFFF;
        cursor: pointer;
    }

    .notification-tooltip {
        position: fixed;
        display: none;
        min-width: 250px;
        background: #fff;
        border: 1px solid #ccc;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .25);
        z-index: 999999;
        padding: 0;
    }

    .notification-tooltip table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
    }

    .notification-tooltip th,
    .notification-tooltip td {
        border: 1px solid #000;
        padding: 6px 8px;
        text-align: center;
    }

    .notification-tooltip td:first-child {
        text-align: center;
    }

    .notification-tooltip th {
        background: #5b6168;
        color: #fff;
    }
</style>

<div class="page-wrapper">
    <?php include "include/common/leftbar.php"; ?>

    <!-- Page content start  -->
    <div class="page-content">

        <!-- Header start -->
        <header class="header">

            <div class="toggle-btns" style="display: flex; align-items: center; justify-content: space-between;">
                <a id="toggle-sidebar" href="#">
                    <i class="icon-list"></i>
                </a>
                <a id="pin-sidebar" href="#">
                    <i class="icon-list"></i>
                </a>
            </div>
            <div class="header-items">
                <!-- Custom search start -->
                <ul class="header-actions">
                    <li class="dropdown"></li>
                    <li class="dropdown">
                        <!-- <div class="custom-search">
                            <input type="text" id="search_input_" class="search-query" placeholder="Search here ..." onkeypress="if (event.key === 'Enter') {$('#search_screens').trigger('click');}">
                            <i id="search_screens" class="icon-search" style="color:white"></i>
                        </div> -->
                        <div class="dropdown-menu dropdown-menu-right lrg" aria-labelledby="notifications" style="left: -52px;top: 43px;">
                            <div class="dropdown-menu-header">
                                Results
                            </div>
                            <div class="header-notifications"></div>
                        </div>
                    </li>
                </ul>
                <!-- Custom search end -->

                <!-- Header actions start -->
                <ul class="header-actions">
                    <li class="dropdown"></li>
                    <li class="dropdown">
                        <a href="#" id="notifications" data-toggle="dropdown" aria-haspopup="true">
                            <i class="icon-message-circle"></i>
                            <span class="count-label"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right lrg" aria-labelledby="notifications" style="left: -238px;">
                            <div class="dropdown-menu-header">
                                Notifications
                            </div>
                            <div class="customScroll5 quickscard">
                                <div class="header-notifications"></div>
                            </div>
                        </div>
                        <div id="notificationTooltip" class="notification-tooltip"></div>
                    </li>
                    <li class="dropdown">
                        <a href="#" id="userSettings" class="user-settings" data-toggle="dropdown" aria-haspopup="true">
                            <span class="user-name show-username"></span>
                            <span class="avatar">
                                <img src="img/av1.png" alt="avatar">
                                <span class="status online"></span>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userSettings">
                            <div class="header-profile-actions">
                                <div class="header-user-profile">
                                    <div class="header-user">
                                        <img src="img/av1.png" alt="Admin Template">
                                    </div>
                                    <h5 class="show-username"></h5>
                                    <p class="show-username"></p>
                                </div>
                                <!-- <a href="#"><i class="icon-user1"></i> My Profile</a> -->
                                <a href="logout.php" class="logout-link"><i class="icon-log-out1"></i>Log Out</a>
                            </div>
                        </div>
                    </li>
                </ul>
                <!-- Header actions end -->
            </div>
        </header>

        <br>
        <div class="page-header">
            <div style="width:100%; padding:12px;  font-size: 20px; border-radius:5px; display: flex; align-items: center;justify-content: left;height:100px">
                <b style="padding: 10px; color: #f26b35">HRMS</b><span id="pageHeaderName" style="color: white;"></span>
            </div>
        </div><br>
        <div class="main-container" id="main-container"></div>

    </div>
</div>