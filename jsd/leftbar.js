// 1. MODULE MAPPING
const moduleMapping = {
  home_page: "home",
  dashboard: "dashboard",

  company_creation: "organization",
  branch_creation: "organization",
  team_creation: "organization",
  ctc_creation: "organization",
  statutory_compliance: "organization",
  leave_master: "organization",
  holiday_creation: "organization",

  director_creation: "administration",
  staff_creation: "administration",
  reporting_person_mapping: "administration",
  staff_exit_management: "administration",

  staff_loan: "employee_loans_advances",
  staff_salary_advance: "employee_loans_advances",

  manage_user: "manage_user",

  regularization: "regularization",

  location_access: "attendance_Management",
  attendance: "attendance_Management",

  promotion_transfer: "promotion_transfer",

  payroll_processing: "payroll_management",
  pay_slip: "payroll_management",

  analytics_dashboard: "feedback_management",
  feedback_engagement: "feedback_management",

  feedback: "my_feedbacks",
  rating: "my_feedbacks",
  poll: "my_feedbacks",

  monitoring_chart: "monitoring_chart",

  target_setup: "target",
  target_evaluation: "target",
  target_incentive: "target",

  performance_analysis: "performance_traning",
  training_solution: "performance_traning",

  staff_report: "reports",
  regularization_report: "reports",
  promotion_transfer_report: "reports",
  location_access_report: "reports",
  feedback_report: "reports",
  attendance_report: "reports",
};

// 2. DOCUMENT READY
$(document).ready(function () {
  getLeftbarMenuList();
});

// 3. GET CURRENT PAGE
function getCurrentPage() {
  const urlParams = new URLSearchParams(window.location.search);

  return (
    urlParams.get("page") || localStorage.getItem("currentPage") || "home_page"
  );
}

// 4. GET LEFTBAR MENU LIST
function getLeftbarMenuList() {
  $.post(
    "api/base_api/menulist.php",

    function (response) {
      if (response && response.length != 0) {
        createSidebarMenu(response);
      }
    },

    "json",
  );
}

// 5. CREATE SIDEBAR MENU
function createSidebarMenu(response) {
  $(".sidebar-menu").empty();

  var sidebar = $("<ul></ul>");

  var grouped = {};

  /*--- Group submenus by main menu ---*/
  response.forEach(function (item) {
    if (!grouped[item.main_menu]) {
      grouped[item.main_menu] = [];
    }

    grouped[item.main_menu].push(item);
  });

  /*--- Create main menu ---*/
  for (var mainMenu in grouped) {
    var mainMenuItems = grouped[mainMenu];

    var mainMenuLi = $(
      '<li class="sidebar-dropdown ' +
        mainMenuItems[0].main_menu_link +
        '"></li>',
    );

    /*--- Main menu link ---*/
    var mainMenuLink = $('<a href="javascript:void(0)"></a>');

    mainMenuLink.append(
      '<i class="icon-' + mainMenuItems[0].main_menu_icon + '"></i>',
    );

    mainMenuLink.append('<span class="menu-text">' + mainMenu + "</span>");

    mainMenuLi.append(mainMenuLink);

    /*--- Submenu container ---*/
    var submenuDiv = $('<div class="sidebar-submenu"></div>');

    var submenuUl = $("<ul></ul>");

    submenuDiv.append(submenuUl);

    mainMenuLi.append(submenuDiv);

    /*--- Create submenu items ---*/
    mainMenuItems.forEach(function (subItem) {
      var subLi = $("<li></li>");

      var subLink = $('<a class="clickevent"></a>');

      /*--- Screen URL ---*/
      var pageUrl =
        "home.php?page=" + encodeURIComponent(subItem.sub_menu_link);

      subLink.attr("href", pageUrl);

      /*--- Screen name ---*/
      subLink.attr(
        "data-page",

        subItem.sub_menu_link,
      );

      /*--- Submenu icon ---*/
      subLink.append('<i class="icon-' + subItem.sub_menu_icon + '"></i>');

      /*--- Submenu name ---*/
      subLink.append($("<span></span>").text(subItem.sub_menu));

      /*--- Append submenu link ---*/
      subLi.append(subLink);

      /*--- Append submenu list item ---*/
      submenuUl.append(subLi);
    });

    /*--- Append main menu ---*/
    sidebar.append(mainMenuLi);
  }

  /* --- Append sidebar ----*/
  $(".sidebar-menu").append(sidebar);

  /* --- Initialize dropdown menu ---*/
  initializeSidebarDropdown();

  /* --- Initialize screen navigation ---*/
  initializeSidebarNavigation();

  /* --- Highlight current screen ---*/
  toggleSidebarSubmenus();
}

// 6. SIDEBAR DROPDOWN
function initializeSidebarDropdown() {
  $(".sidebar-dropdown > a")
    .off("click.sidebarDropdown")

    .on("click.sidebarDropdown", function (event) {
      event.preventDefault();

      var parent = $(this).parent();

      /*--- Close other menus ---*/
      $(".sidebar-dropdown")
        .not(parent)

        .removeClass("active");

      $(".sidebar-submenu")
        .not(parent.find(".sidebar-submenu"))

        .stop(true, true)

        .slideUp(200);

      /*--- Toggle current menu ---*/
      if (parent.hasClass("active")) {
        parent.removeClass("active");

        parent
          .find(".sidebar-submenu")

          .stop(true, true)

          .slideUp(200);
      } else {
        parent.addClass("active");

        parent
          .find(".sidebar-submenu")

          .stop(true, true)

          .slideDown(200);
      }
    });
}

// 7. SIDEBAR SCREEN NAVIGATION
function initializeSidebarNavigation() {
  $(".clickevent")
    .off("click.sidebarNavigation")

    .on("click.sidebarNavigation", function (event) {
      var currentPage = $(this).attr("data-page");

      if (!currentPage) {
        console.error("Page link is missing");

        return;
      }

      /*--- Ctrl + Click, Meta + Click, Shift + Click, Middle Click Allow browser native behavior ---*/
      if (
        event.ctrlKey ||
        event.metaKey ||
        event.shiftKey ||
        event.button === 1
      ) {
        return;
      }

      /*--- Normal navigation ---*/
      event.preventDefault();

      setlocalvariable(currentPage);
    });
}

// 8. SET CURRENT PAGE
function setlocalvariable(currentPage) {
  if (!currentPage) {
    console.error("Current page is missing");

    return;
  }

  /*--- Remember last selected screen ---*/
  localStorage.setItem(
    "currentPage",

    currentPage,
  );

  /*--- Navigate to selected screen ---*/
  window.location.href = "home.php?page=" + encodeURIComponent(currentPage);
}

// 9. TOGGLE SIDEBAR SUBMENUS
function toggleSidebarSubmenus() {
  /*--- Current screen ---*/
  var current_page = getCurrentPage();

  /*--- Current module ---*/
  var current_module = moduleMapping[current_page] || "home";

  /*--- Reset sidebar ---*/
  $(".sidebar-dropdown").removeClass("active");

  $(".sidebar-submenu").hide();

  $(".clickevent").css({
    background: "",
    color: "",
  });

  $(".clickevent").find("i").css("color", "");

  $(".sidebar-dropdown > a").css("background", "");

  /*--- Open current module ---*/
  $(".sidebar-dropdown").each(function () {
    var parentLi = $(this);

    if (parentLi.hasClass(current_module)) {
      parentLi.addClass("active");

      parentLi.find(".sidebar-submenu").show();
    }
  });

  /*--- Highlight current screen ---*/
  $(".clickevent").each(function () {
    var linkPage = $(this).attr("data-page");

    if (linkPage === current_page) {
      /*--- Highlight submenu ---*/
      $(this).css({
        background:
          "linear-gradient(90deg, #adadad 0%, rgb(96, 101, 105) 50%, #2a3740 100%)",
        color: "#fff",
      });

      /*--- Highlight submenu icon ---*/
      var icon = $(this).find("i");

      if (icon.length) {
        icon.css("color", "#f26b35");
      }

      /*--- Highlight main menu ---*/
      $(this)
        .closest(".sidebar-dropdown")
        .find("> a")
        .css("backgroundColor", "rgba(253, 253, 253, 0.2)");
    }
  });

  /*--- Dashboard highlight ---*/
  if (current_page === "dashboard") {
    $(".dashboard").css("backgroundColor", "rgba(253, 253, 253, 0.2)");
  }
}
