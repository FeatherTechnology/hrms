$(document).ready(function () {
  $(".add_user_btn, .back_to_userList_btn").click(function () {
    swapTableAndCreation();
  });

  $("#company_name").on("change", function () {
    $("#staff_name").val("");
    $("#staff_id").val("");
    $("#branch").val("");
    $("#department").val("");
    $("#team").val("");
    $("#designation").val("");
    $("#role").val("");

    loadStaff();
  });

  $("#role").on("change", function () {
    $("#staff_name").val("");
    $("#staff_id").val("");
    $("#branch").val("");
    $("#department").val("");
    $("#team").val("");
    $("#designation").val("");

    loadStaff();
  });

  $("#staff_name").on("change", function () {
    getStaffInfo();
  });

  $("input[name='outer_list']").on("change", function () {
    getUserCreationTable();
  });

  /////////////////////////////////////////////////////////// User Creation START ///////////////////////////////////////////////////////////////////////
  $("#submit_user_creation").click(function (event) {
    event.preventDefault();

    // Collect selected submenu IDs
    let selectedSubmenuIds = [];
    $('input[type="checkbox"]:checked').each(function () {
      if ($(this).hasClass("submenu-checkbox")) {
        selectedSubmenuIds.push($(this).val());
      }
    });

    let userFormData = {
      user_code: $("#user_code").val(),
      company_name: $("#company_name").val(),
      role: $("#role").val(),
      staff_name: $("#staff_name").val(),
      staff_id: $("#staff_id").val(),
      user_name: $("#user_name").val(),
      password: $("#password").val(),
      confirm_password: $("#confirm_password").val(),
      download_access: $("#download_access").val(),
      home_access: $("#home_access").val(),
      report_access: $("#report_access").val(),

      submenus: selectedSubmenuIds,
      id: $("#user_creation_id").val(),
    };
    var data = [
      "company_name",
      "role",
      "staff_name",
      "user_name",
      "password",
      "confirm_password",
      "download_access",
      "home_access",
      "report_access",
    ];

    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });

    if (isValid) {
      if (selectedSubmenuIds.length > 0) {
        $.post(
          "api/user_creation_files/submit_user_creation.php",
          userFormData,
          function (response) {
            if (response.status == "") {
              swalError("Error", "Creation Failed.");
            } else if (response.status == "1") {
              swalSuccess("Success", "User Updated Successfully!");
            } else if (response.status == "2") {
              swalSuccess("Success", "User Added Successfully!");
            } else if (response.status == "3") {
              swalError("Warning", "User Name Already Created.");
            }

            if (response.status != "" && response.status != "3") {
              swapTableAndCreation();
            }
            let sessionId = $("#session_user_id").val();
            if (response.last_id == sessionId) {
              getLeftbarMenuList(); //After Submit/Update Leftbar want to refresh to view the changes.
            }
          },
          "json",
        );
      } else {
        swalError("Warning", "Please fill out mandatory fields!");
      }
    }
    // }
  });

  $(document).on("click", ".userActionBtn", async function () {
    var id = $(this).attr("value"); // Get value attribute

    try {
      const response = await $.ajax({
        url: "api/user_creation_files/user_creation_data.php",
        type: "POST",
        data: { id },
        dataType: "json",
      });

      $("#user_creation_id").val(id);
      swapTableAndCreation();
      await getCompanyName();

      $("#user_code").val(response[0].user_code);
      $("#company_name").val(response[0].company_id);
      $("#role").val(response[0].role);
      await getStaffName(response[0].company_id, response[0].role);
      $("#staff_name").val(response[0].staff_name);
      $("#staff_id").val(response[0].staff_id);

      $("#branch").val(response[0].branch_name);
      $("#department").val(response[0].department_name);
      $("#team").val(response[0].team_name);
      $("#designation").val(response[0].designation);
      $("#user_name").val(response[0].user_name);
      $("#password").val(response[0].password);
      $("#confirm_password").val(response[0].password);
      $("#download_access").val(response[0].download_access);
      $("#home_access").val(response[0].home_access);
      $("#report_access").val(response[0].report_access);
    } catch (error) {
      console.error("Failed to fetch branch data:", error);
    }
  });

  $(document).on("click", ".userDeleteBtn", function () {
    var id = $(this).attr("value"); // Get value attribute
    swalConfirm("Delete", "Do you want to Delete the User?", deleteUser, id);
    return;
  });

  /////////////////////////////////////////////////////////// User Creation END ///////////////////////////////////////////////////////////////////////

  $("#password, #confirm_password").keyup(function () {
    const password = $("#password").val();
    const confirmPassword = $("#confirm_password").val();
    if (password != "" && confirmPassword != "") {
      if (password != confirmPassword) {
        $("#confirm_password").css("border", "1px solid red");
      } else {
        $("#confirm_password").css("border", "");
      }
    }
  });

  $("#password, #confirm_password").change(function () {
    const password = $("#password").val();
    const confirmPassword = $("#confirm_password").val();
    if (password != "" && confirmPassword != "") {
      if (password != confirmPassword) {
        $("#confirm_password").val("");
      }
    }
  });

  // Handle menu checkbox events
  $(document).on("change", '.main-menu input[type="checkbox"]', function () {
    const menuId = $(this).attr("id");
    const submenus = $(`#${menuId}-submenus input[type="checkbox"]`);
    submenus.prop("disabled", !this.checked);
    if (!this.checked) {
      submenus.prop("checked", false);
    }
  });

  $('button[type="reset"]').click(function (event) {
    event.preventDefault();

    $("input").each(function () {
      var id = $(this).attr("id");

      if (
        id != "user_code" &&
        id != "user_creation_id" &&
        id != "session_user_id" &&
        id != "active_list" &&
        id != "inactive_list"
      ) {
        $(this).val("");
      }
    });

    $("select").each(function () {
      $(this).val($(this).find("option:first").val());
    });

    $('input[type="checkbox"]').prop("checked", false);

    $("#user_code").css("border", "1px solid #cecece");
    $("#company_name").css("border", "1px solid #cecece");
    $("#role").css("border", "1px solid #cecece");
    $("#user_name").css("border", "1px solid #cecece");
    $("#password").css("border", "1px solid #cecece");
    $("#confirm_password").css("border", "1px solid #cecece");
    $("#download_access").css("border", "1px solid #cecece");
    $("#report_access").css("border", "1px solid #cecece");
  });
}); //Document END.

//ON Load
$(function () {
  getUserCreationTable();
  getSessionValue();
});

function getSessionValue() {
  $.post(
    "api/base_api/getSessionData.php",
    function (response) {
      $("#session_user_id").val(response);
    },
    "json",
  );
}

function swapTableAndCreation() {
  if ($(".user_creation_table_content").is(":visible")) {
    $(".user_creation_table_content").hide();
    $(".add_user_btn").hide();
    $("#user_creation_content").show();
    $(".back_to_userList_btn").show();
    $(".radio-container").hide();

    let userid = $("#user_creation_id").val();

    getUserID("");
    getCompanyName();
    getMenuSubMenuList(userid);
  } else {
    $(".user_creation_table_content").show();
    $(".add_user_btn").show();
    $(".radio-container").show();
    $("#user_creation_content").hide();
    $(".back_to_userList_btn").hide();
    $("#reset_btn").trigger("click");
    $("#active_list").prop("checked", true);
    getUserCreationTable();
    $("#user_creation_id").val("0");
  }
}

function loadStaff() {
  let company_id = $("#company_name").val();
  let role = $("#role").val();

  if (company_id != "" && role != "") {
    getStaffName(company_id, role);
  }
}

// <--------------------------------------------- Get Company Name Function Start ----------------------------------------------------->

async function getCompanyName() {
  return new Promise((resolve, reject) => {
    $.post(
      "api/branch_creation/getCompanyName.php",
      {},

      function (response) {
        let dropdown = $("#company_name");
        dropdown.empty();
        dropdown.append('<option value="">Select Company Name</option>');
        $.each(response, function (index, item) {
          dropdown.append(
            `<option value="${item.id}">${item.company_name}
                        </option>`,
          );
        });

        resolve();
      },

      "json",
    ).fail(function (xhr, status, error) {
      reject(error);
    });
  });
}

// <--------------------------------------------- Get Company Name Function End ----------------------------------------------------->

// <--------------------------------------------- Get Staff Name Function Start ----------------------------------------------------->

async function getStaffName(company_id, role) {
  try {
    const response = await $.ajax({
      url: "api/user_creation_files/getStaffName.php",
      type: "POST",
      data: { company_id, role },
      dataType: "json",
      cache: false,
    });

    let dropdown = $("#staff_name");

    dropdown.empty();

    dropdown.append('<option value="">Select Staff Name</option>');

    $.each(response, function (index, item) {
      dropdown.append(`
                <option value="${item.id}">
                    ${item.staff_name}
                </option>
            `);
    });
  } catch (error) {
    console.error(error);

    swalError("Error", "Unable to Fetch Staff Name");
  }
}

// <--------------------------------------------- Get Staff Name Function End ----------------------------------------------------->

// <--------------------------------------------- Get Staff Information Function Start ----------------------------------------------------->

function getStaffInfo() {
  let id = $("#staff_name").val();

  $.post(
    "api/user_creation_files/getStaffInfo.php",
    { id: id },

    function (response) {
      if (response.length > 0) {
        $("#staff_id").val(response[0].staff_id);
        $("#branch").val(response[0].branch_name);
        $("#department").val(response[0].department_name);
        $("#team").val(response[0].team_name);
        $("#designation").val(response[0].designation);
      } else {
        $("#staff_id").val("");
        $("#branch").val("");
        $("#department").val("");
        $("#team").val("");
        $("#designation").val("");

        swalError("Warning", "No Staff Info Found");
      }
    },
    "json",
  );
}

// <----------------------------------------------------------------- Get Staff Information Function End ----------------------------------------------------------->

// <------------------------------------------------------------- User Creation Outer List Function Start ----------------------------------------------------------->

function getUserCreationTable() {
  let status = $("input[name='outer_list']:checked").val();
  $.post(
    "api/user_creation_files/user_creation_list.php",
    { status: status },
    function (response) {
      let userColumn = [
        "sno",
        "company_name",
        "role",
        "staff_name",
        "branch_name",
        "department_name",
        "team_name",
        "designation",
        "action",
      ];
      appendDataToTable("#user_creation_table", response, userColumn);
      setdtable("#user_creation_table", "User Creation List");
    },
    "json",
  );
}

// <------------------------------------------------------------- User Creation Outer List Function End ------------------------------------------------------------->

// <------------------------------------------------------------------- User Menu & Sub Menu Function Start --------------------------------------------------------->

function getMenuSubMenuList(userId) {
  $.post(
    "api/user_creation_files/get_menu_submenu_list.php",
    function (response) {
      $("#dynamic-menus").empty();
      // Group submenus by main menu
      var grouped = {};
      response.forEach(function (item) {
        if (!grouped[item.main_menu_link]) {
          grouped[item.main_menu_link] = {
            main_menu: item.main_menu,
            submenus: [],
          };
        }
        if (item.sub_menu) {
          grouped[item.main_menu_link].submenus.push({
            sub_menu: item.sub_menu,
            sub_menu_link: item.sub_menu_link,
            sub_menu_id: item.sub_menu_id,
          });
        }
      });

      // Iterate over grouped data to generate HTML
      var tabindex = 18;
      for (var mainMenuLink in grouped) {
        var menu = grouped[mainMenuLink];
        const menuHtml = `
                <div class="custom-control custom-checkbox main-menu">
                    <input type="checkbox" value="Yes" name="${mainMenuLink}-mainmenu" id="${mainMenuLink}-mainmenu" tabindex="${tabindex}">&nbsp;&nbsp;
                    <label class="custom-control-label" for="${mainMenuLink}-mainmenu">
                        <h5>${menu.main_menu}</h5>
                    </label>
                </div> 
                </br>
                <div class="row" id="${mainMenuLink}-mainmenu-submenus">
                    <!-- Submenus will be appended here -->
                </div>
                <hr>
            `;
        $("#dynamic-menus").append(menuHtml);
        tabindex++;
        menu.submenus.forEach(function (submenu) {
          const submenuHtml = `
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" value="${submenu.sub_menu_id}" class=" submenu-checkbox" name="${submenu.sub_menu_link}" id="${submenu.sub_menu_link}" tabindex="${tabindex}" disabled>&nbsp;&nbsp;
                            <label class="custom-control-label" for="${submenu.sub_menu_link}">${submenu.sub_menu}</label>
                        </div>
                    </div>
                `;
          $(`#${mainMenuLink}-mainmenu-submenus`).append(submenuHtml);
          tabindex++;
        });
      }

      // Fetch user permissions and set checkbox states
      $.post(
        "api/user_creation_files/get_user_permissions.php",
        { user_id: userId },
        function (userPermissions) {
          // Set main menu checkboxes
          userPermissions.forEach(function (permission) {
            $(`#${permission.main_menu_link}-mainmenu`).prop("checked", true);
            $(`#${permission.main_menu_link}-mainmenu`).trigger("change"); // Trigger change event to enable submenus

            $(`#${permission.sub_menu_link}`).prop("checked", true);
          });
        },
        "json",
      );
    },
    "json",
  );
}

// <------------------------------------------------------------------- User Menu & Sub Menu Function End ----------------------------------------------------------->

// <-------------------------------------------------------------- User Auto Increment Code Function Start ---------------------------------------------------------->

async function getUserID(id) {
  try {
    const response = await $.ajax({
      url: "api/user_creation_files/get_user_id.php",
      type: "POST",
      data: { id },
      dataType: "json",
    });

    $("#user_code").val(response);

    return response;
  } catch (error) {
    console.error("Error fetching User ID:", error);
    swalError("Warning", "Unable to fetch User ID");
  }
}

// <-------------------------------------------------------------- User Auto Increment Code Function End ----------------------------------------------------------->

// <-------------------------------------------------------------- User Creation Delete Function Start ------------------------------------------------------------->

function deleteUser(id) {
  $.post(
    "api/user_creation_files/delete_user.php",
    { id },
    function (response) {
      if (response == "1") {
        swalSuccess("Success", "User Deleted Successfully.");
        getUserCreationTable();
        setTimeout(() => {
          let userSessionId = $("#session_user_id").val();
          if (userSessionId == id) {
            location.href = "logout.php";
          }
        }, 2500);
      } else if (response == "0") {
        swalError("Error", "User Delete Failed.");
      }
    },
    "json",
  );
}

// <----------------------------------------------------------------- User Creation Delete Function End -------------------------------------------------------->
