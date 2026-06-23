// ==========================
// MODULE MAPPING (ONLY ONCE)
// ==========================
const moduleMapping = {
  home_page: "home",
  dashboard: "dashboard",
  company_creation: "organization_management",
  branch_creation: "organization_management",
  team_creation: "organization_management",
  ctc_creation: "organization_management",
  statutory_compliance: "organization_management",
  leave_master: "organization_management",
  holiday_creation: "organization_management",
  director_creation: "staff_management",
  staff_creation: "staff_management",
  staff_exit_management: "staff_management",
  manage_user: "staff_management",
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
  staff_report: "reports",
  regularization_report: "reports",
  promotion_transfer_report: "reports",
  location_access_report: "reports",
  feedback_report: "reports",
  attendance_report: "reports",
};

// ==========================
// DOCUMENT READY
// ==========================
$(document).ready(function () {
  const current_page = localStorage.getItem("currentPage");
  const current_module = moduleMapping[current_page] || "home_page";

  // highlight AFTER DOM ready
  toggleSidebarSubmenus(current_module);

  getLeftbarMenuList();
});

// ==========================
// GET MENU LIST
// ==========================
function getLeftbarMenuList() {
  $.post(
    "api/base_api/menulist.php",
    function (response) {
      if (response.length > 0) {
        createSidebarMenu(response);
      }

      // bind dropdown ONCE
      $(document)
        .off("click.sidebarDropdown")
        .on("click.sidebarDropdown", ".sidebar-dropdown > a", function () {
          $(".sidebar-submenu").slideUp(200);

          if ($(this).parent().hasClass("active")) {
            $(".sidebar-dropdown").removeClass("active");
            $(this).parent().removeClass("active");
          } else {
            $(".sidebar-dropdown").removeClass("active");
            $(this).next(".sidebar-submenu").slideDown(200);
            $(this).parent().addClass("active");
          }
        });
    },
    "json",
  );
}

// ==========================
// CREATE SIDEBAR MENU
// ==========================
function createSidebarMenu(response) {
  $(".sidebar-menu").empty();

  let sidebar = $("<ul></ul>");

  let grouped = {};

  response.forEach((item) => {
    if (!grouped[item.main_menu]) {
      grouped[item.main_menu] = [];
    }
    grouped[item.main_menu].push(item);
  });

  for (let mainMenu in grouped) {
    let mainMenuLi = $(`
      <li class="sidebar-dropdown ${grouped[mainMenu][0].main_menu_link}">
      </li>
    `);

    let mainMenuLink = $('<a href="javascript:void(0)"></a>').appendTo(
      mainMenuLi,
    );

    mainMenuLink.append(
      `<i class="icon-${grouped[mainMenu][0].main_menu_icon}"></i>`,
    );

    mainMenuLink.append(`<span class="menu-text">${mainMenu}</span>`);

    let submenuDiv = $('<div class="sidebar-submenu"></div>').appendTo(
      mainMenuLi,
    );
    let submenuUl = $("<ul></ul>").appendTo(submenuDiv);

    grouped[mainMenu].forEach((subItem) => {
      let subLi = $("<li></li>").appendTo(submenuUl);

      let subLink = $(`
        <a href="${subItem.sub_menu_link}" class="clickevent"></a>
      `).appendTo(subLi);

      subLink.append(`<i class="icon-${subItem.sub_menu_icon}"></i>`);
      subLink.append(subItem.sub_menu);
    });

    sidebar.append(mainMenuLi);
  }

  $(".sidebar-menu").append(sidebar);

  // ==========================
  // CLICK EVENT (OPTIMIZED)
  // ==========================
  $(document)
    .off("click.clickevent")
    .on("click.clickevent", ".clickevent", function (event) {
      event.preventDefault();
      setlocalvariable(this);
    });

  // ==========================
  // HIGHLIGHT AFTER MENU LOAD
  // ==========================
  const current_page = localStorage.getItem("currentPage");
  const current_module = moduleMapping[current_page] || "home_page";

  toggleSidebarSubmenus(current_module);
}

// ==========================
// SAVE PAGE + REDIRECT
// ==========================
function setlocalvariable(element) {
  const hrefValue = $(element).attr("href");
  localStorage.setItem("currentPage", hrefValue);
  window.location.href = "home.php";
}

// ==========================
// SIDEBAR HIGHLIGHT
// ==========================
function toggleSidebarSubmenus(current_module) {
  // open correct parent menu
  document.querySelectorAll(".sidebar-submenu").forEach((submenu) => {
    let parentLi = submenu.closest("li");

    if (parentLi && parentLi.classList.contains(current_module)) {
      let mainmenu = submenu.closest(".sidebar-dropdown");
      mainmenu.classList.add("active");
    }
  });

  // highlight active link
  const current_page = localStorage.getItem("currentPage");

  document
    .querySelectorAll(".sidebar-menu .sidebar-submenu ul li a")
    .forEach((link) => {
      if (link.getAttribute("href") === current_page) {
        link.style.background =
          "linear-gradient(90deg, #adadad 0%, rgb(96, 101, 105) 50%, #2a3740 100%)";
        link.style.color = "#fff";
        const icon = link.querySelector("i");
        if (icon) {
          icon.style.color = "#f26b35";
        }
      }
    });
}
