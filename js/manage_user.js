// multiselect for the director company mapping
const companyMultiple = new Choices("#multi_company_name", {
  removeItemButton: true,
  placeholder: true,
  placeholderValue: "Select Company Name",
  itemSelectText: "",
  allowHTML: false,
  searchEnabled: false,
});

$(document).ready(function () {
  // to change the user type director or staff
  $("#user_type").on("change", function () {
    var usertype = $(this).val();

    if (usertype == 1) {
      $(".director_div").show();
      $(".user_div").hide();
      getDirectorName();
      getCompanyNameDropdown();
    } else {
      $(".director_div").hide();
      $(".user_div").show();
      getCompanyName("#company_name");
    }

    // Reset Regularization fields
    $("#approval_required").val("").css("border", "");

    $(".regularization-type, .approval-type").each(function () {
      const choices = $(this).data("choices");

      if (choices) {
        choices.removeActiveItems();
      }

      $(this).closest(".choices").css("border", "");
    });

    // Hide Approval Types section
    $(".approval-type-container").hide();

    // Show/Hide Allowed Request Types
    if ($("#regularization").is(":checked")) {
      if (usertype == "1") {
        $(".regularization-request-container").hide();
      } else {
        $(".regularization-request-container").show();
      }
    }
  });

  $(document).on("change", ".submenu-checkbox", function () {
    $(this)
      .closest(".col-xl-3")
      .nextAll(".regularization-options")
      .slice(0, 3)
      .toggle(this.checked);
  });

  $(document).on("change", "#regularization", function () {
    if ($(this).is(":checked")) {
      $(".regularization-options").show();

      // Hide Allowed Request Types for Director
      if ($("#user_type").val() === "1") {
        $(".regularization-request-container").hide();
      } else {
        $(".regularization-request-container").show();
      }

      return;
    }

    // Hide all Regularization fields
    $(".regularization-options, .approval-type-container").hide();

    // Reset normal select
    $(".approval-required").val("").css("border", "");

    // Reset Choices.js controls
    $(".regularization-type, .approval-type").each(function () {
      let choices = $(this).data("choices");

      if (choices) {
        choices.removeActiveItems();
      }

      $(this).closest(".choices").css("border", "");
    });
  });

  $(document).on("change", ".approval-required", function () {
    let container = $(this).closest(".row").find(".approval-type-container");

    if ($(this).val() === "1") {
      container.show();
    } else {
      container.hide();
      $(".approval-type").each(function () {
        let choices = $(this).data("choices");
        if (choices) choices.removeActiveItems();

        // Clear validation border
        $(this).closest(".choices").css("border", "");
      });
    }
  });

  $(document).on("change", "#regularization-mainmenu", function () {
    if ($(this).is(":checked")) {
      return;
    }

    // Hide all Regularization fields
    $(".regularization-options").hide();
    $(".approval-type-container").hide();

    // Uncheck Regularization submenu
    $("#regularization").prop("checked", false);

    // Reset Approval Required
    $("#approval_required").val("").css("border", "");

    // Reset Choices.js fields
    $(".regularization-type, .approval-type").each(function () {
      const choices = $(this).data("choices");

      if (choices) {
        choices.removeActiveItems();
      }

      $(this).closest(".choices").css("border", "");
    });
  });

  // When clicking the Add User button
  $(".add_user_btn").click(async function () {
    $("#reset_btn").show();
    $(".add_user_btn").hide();
    $(".back_to_userList_btn").show();

    $("#search_container, .radio_container, .table_container").hide();
    $("#user_creation_content").show();

    $(".director_div").hide();
    $(".user_div").hide();

    $("#feedback_access_type_div").hide();

    $("#user_type").val("");
    $(".credential_info").find("input, select").val("");

    let userid = $("#user_creation_id").val();

    await getMenuSubMenuList(userid);
  });

  // when i click the back button
  $(".back_to_userList_btn").click(function () {
    if ($("#company_search").val() !== "" && $("#user_types").val() !== "") {
      $(".radio_container,.table_container").show();
      $("#view_staff").trigger("click");
    }
    $("#reset_btn").hide();
    $("#multi_company_name2").val("");
    $(".add_user_btn").show();
    $(".back_to_userList_btn").hide();
    $("#search_container").show();
    $("#user_creation_content").hide();
    $("#user_creation_id").val("0");
  });

  /* --- User Creation On Change & Click Events --- */
  $("#company_name").on("change", function () {
    var cmpy_id = $(this).val();
    $("#staff_name").val("");
    $("#staff_id").val("");
    $("#branch").val("");
    $("#department").val("");
    $("#team").val("");
    $("#designation").val("");
    // $("#role").val("");
    loadStaff();
  });

  // staff name on change event to get the staff details
  $("#staff_name").on("change", function () {
    getStaffInfo();
  });

  // active inactive on change
  $("input[name='outer_list']").on("change", function () {
    let cmpy_name = $("#company_search").val();
    let user_types = $("#user_types").val();
    if (cmpy_name == "" || user_types == "") {
      swalError("Warning", "Please Select All Fields!");
      return;
    } else {
      getUserCreationTable(cmpy_name, user_types);
    }
  });

  // password key up event
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

  // password on change event
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

  /* --- Feedback Access On Change --- */
  $("#feedback_access").on("change", function () {
    if ($(this).val() == "1") {
      // YES
      $("#feedback_access_type_div").show();
    } else {
      // NO or Select
      $("#feedback_access_type_div").hide();
      $("#feedback_access_type").val(""); // Empty the dropdown
      $("#feedback_access_type").css("border", "1px solid #cecece");
    }
  });

  /* --- Submit User Creation --- */
  $("#submit_user_creation").click(function (event) {
    event.preventDefault();

    // Collect selected submenu IDs
    let selectedSubmenuIds = [];

    $(".submenu-checkbox:checked").each(function () {
      selectedSubmenuIds.push($(this).val());
    });

    let userFormData = {
      user_type: $("#user_type").val(),
      director_name: $("#director_name").val(),
      multi_company_name: $("#multi_company_name").val(),
      company_name: $("#company_name").val(),
      staff_name: $("#staff_name").val(),
      staff_id: $("#staff_id").val(),
      user_name: $("#user_name").val(),
      password: $("#password").val(),
      confirm_password: $("#confirm_password").val(),
      download_access: $("#download_access").val(),
      home_access: $("#home_access").val(),
      allowed_request_type: $("#allowed_request_type").val(),
      approval_required: $("#approval_required").val(),
      approved_request_type: $("#approved_request_type").val(),
      report_access: $("#report_access").val(),
      feedback_access: $("#feedback_access").val(),
      feedback_access_type: $("#feedback_access_type").val(),
      submenus: selectedSubmenuIds,
      id: $("#user_creation_id").val(),
    };

    let userType = $("#user_type").val();

    if (!userType) {
      swalError("Warning", "Please select User Type!");
      return;
    }

    let data = [];
    let isValid = true;

    if (userType == "1") {
      data = [
        "director_name",
        "user_name",
        "password",
        "confirm_password",
        "download_access",
        "feedback_access",
        "home_access",
      ];

      let companyValid = validateMultiSelectField(
        "multi_company_name",
        companyMultiple,
      );

      if (!companyValid) {
        isValid = false;
      }
    } else if (userType == "2") {
      data = [
        "company_name",
        "staff_name",
        "staff_id",
        "user_name",
        "password",
        "confirm_password",
        "download_access",
        "feedback_access",
        "home_access",
      ];
    }

    // Validate report access only if Reports menu is selected
    if ($("#reports-mainmenu").is(":checked")) {
      data.push("report_access");
    }

    // Validate Feedback Access Type only if Feedback Access = YES
    if ($("#feedback_access").val() == "1") {
      data.push("feedback_access_type");
    }

    data.forEach(function (field) {
      if (!validateField($("#" + field).val(), field)) {
        isValid = false;
      }
    });

    if (!isValid) {
      return;
    }

    if (selectedSubmenuIds.length === 0) {
      swalError("Warning", "Please select at least one screen!");
      return;
    }

    if (!validateRegularization()) {
      return;
    }

    swalConfirm(
      "Are you sure?",
      "Do you want to submit this User Creation?",
      function () {
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
              if (
                $("#company_search").val() !== "" &&
                $("#user_types").val() !== ""
              ) {
                $(".radio_container,.table_container").show();
                $("#view_staff").trigger("click");
              }

              $("#reset_btn").hide();
              $(".add_user_btn").show();
              $(".back_to_userList_btn").hide();
              $("#search_container").show();
              $("#user_creation_content").hide();
              $("#user_creation_id").val("0");
            }

            let sessionId = $("#session_user_id").val();

            if (response.last_id == sessionId) {
              getLeftbarMenuList(); // Refresh left menu after update
            }
          },
          "json",
        );
      },
    );
  });

  /* --- Edit User Creation --- */
  $(document).on("click", ".userActionBtn", async function () {
    $("#reset_btn").hide();
    var id = $(this).attr("value"); // Get value attribute
    $(".add_user_btn").hide();
    $(".back_to_userList_btn").show();
    $("#search_container,.radio_container,.table_container").hide();
    $("#user_creation_content").show();

    try {
      const response = await $.ajax({
        url: "api/user_creation_files/user_creation_data.php",
        type: "POST",
        data: { id },
        dataType: "json",
      });

      $("#user_creation_id").val(id);
      let userid = $("#user_creation_id").val();

      await getMenuSubMenuList(userid);
      await getCompanyName("#company_name");
      await getCompanyNameDropdown();

      $("#user_type").val(response[0].user_type);
      if (response[0].user_type == 1) {
        await getDirectorName();
        $(".director_div").show();
        $(".user_div").hide();
        $("#director_name").val(response[0].director_name).trigger("change");
        $("#multi_company_name2").val(response[0].director_company);

        await getCompanyNameDropdown();
      } else {
        $(".director_div").hide();
        $(".user_div").show();
        $("#company_name").val(response[0].company_id);
        // $("#role").val(response[0].role);
        await getStaffName(response[0].company_id);
        $("#staff_name").val(response[0].staff_name);
        $("#staff_id").val(response[0].staff_id);

        $("#branch").val(response[0].branch_name);
        $("#department").val(response[0].department_name);
        $("#team").val(response[0].team_name);
        $("#designation").val(response[0].designation);
      }

      $("#user_name").val(response[0].user_name);
      $("#password").val(response[0].password);
      $("#confirm_password").val(response[0].password);
      $("#download_access").val(response[0].download_access);
      $("#feedback_access").val(response[0].feedback_access);
      $("#home_access").val(response[0].home_access);
      $("#report_access").val(response[0].report_access);

      // Apply Feedback Access Type visibility based on Feedback Access value
      if (response[0].feedback_access == "1") {
        $("#feedback_access_type_div").show();
        $("#feedback_access_type").val(response[0].feedback_access_type);
      } else {
        $("#feedback_access_type_div").hide();
        $("#feedback_access_type").val(""); // Clear the dropdown
      }

      // Check if Regularization permission is selected
      if ($("#regularization").is(":checked")) {
        $(".regularization-options").show();

        let requestChoices = $("#allowed_request_type").data("choices");

        if (requestChoices) {
          requestChoices.removeActiveItems();

          if (response[0].allowed_request_type) {
            requestChoices.setChoiceByValue(
              response[0].allowed_request_type.split(",").map((v) => v.trim()),
            );
          }
        }

        $("#approval_required").val(response[0].approval_required);

        if (response[0].approval_required == "1") {
          $(".approval-type-container").show();

          let approvalChoices = $("#approved_request_type").data("choices");

          if (approvalChoices) {
            approvalChoices.removeActiveItems();

            if (approvalChoices && response[0].approved_request_type) {
              approvalChoices.setChoiceByValue(
                response[0].approved_request_type
                  .split(",")
                  .map((v) => v.trim()),
              );
            }
          }
        }

        // Apply User Type visibility after showing regularization fields
        if (response[0].user_type == 1) {
          $(".regularization-request-container").hide();
        } else {
          $(".regularization-request-container").show();
        }
      }
    } catch (error) {
      console.error("Failed to fetch branch data:", error);
    }
  });

  /* --- Delete User Creation --- */
  $(document).on("click", ".userDeleteBtn", function () {
    var id = $(this).attr("value"); // Get value attribute
    swalConfirm("Delete", "Do you want to Delete the User?", deleteUser, id);
    return;
  });

  /* --- Reset Button Click --- */
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

    $("#company_name").css("border", "1px solid #cecece");
    // $("#role").css("border", "1px solid #cecece");
    $("#user_name").css("border", "1px solid #cecece");
    $("#password").css("border", "1px solid #cecece");
    $("#confirm_password").css("border", "1px solid #cecece");
    $("#download_access").css("border", "1px solid #cecece");
    $("#report_access").css("border", "1px solid #cecece");
    $("#staff_name").css("border", "1px solid #cecece");
    $("#home_access,#staff_id,#director_name,#multi_company_name").css(
      "border",
      "1px solid #cecece",
    );
  });

  $("#view_staff").on("click", function () {
    let company_id = $("#company_search").val();
    let user_type = $("#user_types").val();

    if (!company_id || !user_type) {
      swalError("Warning", "Please Select All Fields!");
      return;
    } else {
      $(".radio_container").show();
      $(".table_container").show();
      getUserCreationTable(company_id, user_type);
      getSessionValue();
    }
  });
}); //Document END.

/* --- On Laod --- */
$(function () {
  getCompanyName("#company_search");
});

/* --- Get Session Value --- */
function getSessionValue() {
  $.post(
    "api/base_api/getSessionData.php",
    function (response) {
      $("#session_user_id").val(response);
    },
    "json",
  );
}

/* --- Load Staff Name --- */
function loadStaff() {
  let company_id = $("#company_name").val();

  if (company_id != "") {
    getStaffName(company_id);
  }
}

/* --- Get Company Name --- */
// async function getCompanyName() {
//   return new Promise((resolve, reject) => {
//     $.post(
//       "api/attendance_files/get_company_list.php",
//       {},

//       function (response) {
//         let dropdown = $("#company_name");
//         dropdown.empty();
//         dropdown.append('<option value="">Select Company Name</option>');
//         $.each(response, function (index, item) {
//           dropdown.append(
//             `<option value="${item.id}">${item.company_name}
//                         </option>`,
//           );
//         });

//         resolve();
//       },

//       "json",
//     ).fail(function (xhr, status, error) {
//       reject(error);
//     });
//   });
// }

/* --- Get Staff Name --- */
async function getStaffName(company_id) {
  try {
    const response = await $.ajax({
      url: "api/user_creation_files/getStaffName.php",
      type: "POST",
      data: { company_id },
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

/* --- Get Staff Information --- */
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

/* --- Get User Creation Table --- */
function getUserCreationTable(company_id, user_type) {
  let status = $("input[name='outer_list']:checked").val();

  // Change table header
  if (user_type == 1) {
    $("#user_creation_table thead").html(`
      <tr>
        <th>S.No.</th>
        <th>Director Name</th>
        <th>User ID</th>
        <th>Companies</th>
        <th>Action</th>
      </tr>
    `);
  } else {
    $("#user_creation_table thead").html(`
      <tr>
        <th>S.No.</th>
        <th>Staff Name</th>
        <th>User ID</th>
        <th>Branch Name</th>
        <th>Department Name</th>
        <th>Team Name</th>
        <th>Designation</th>
        <th>Action</th>
      </tr>
    `);
  }

  $.post(
    "api/user_creation_files/user_creation_list.php",
    { status, company_id, user_type },
    function (response) {
      let userColumn = [];

      if (user_type == 1) {
        userColumn = [
          "sno",
          "director_name",
          "user_id",
          "company_names",
          "action",
        ];
      } else {
        userColumn = [
          "sno",
          "staff_name",
          "user_id",
          "branch_name",
          "department_name",
          "team_name",
          "designation",
          "action",
        ];
      }

      appendDataToTable("#user_creation_table", response, userColumn);
      setdtable("#user_creation_table", "User Creation List");
    },
    "json",
  );
}

/* --- Get Menu Sub Menu List --- */
function getMenuSubMenuList(userId) {
  return new Promise((resolve, reject) => {
    $.post(
      "api/user_creation_files/get_menu_submenu_list.php",
      function (response) {
        $("#dynamic-menus").empty();

        // Group submenus by main menu
        let grouped = {};

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

        let tabindex = 18;

        for (let mainMenuLink in grouped) {
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
            let submenuHtml = "";

            if (submenu.sub_menu.toLowerCase() === "regularization") {
              submenuHtml = `
              <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12">
                  <div class="custom-control custom-checkbox">
                      <input type="checkbox"
                          value="${submenu.sub_menu_id}"
                          class="submenu-checkbox"
                          name="${submenu.sub_menu_link}"
                          id="${submenu.sub_menu_link}"
                          tabindex="${tabindex}"
                          disabled>

                      <label class="custom-control-label"
                          for="${submenu.sub_menu_link}">
                          ${submenu.sub_menu}
                      </label>
                  </div>
              </div>

              <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12 regularization-request-container regularization-options" style="display:none;">
                  <div class="form-group">
                      <label>Allowed Request Types</label>
                      <select class="form-control regularization-type" id="allowed_request_type" multiple>
                          <option value="1">Leave</option>
                          <option value="2">Permission</option>
                          <option value="3">Week Off</option>
                          <option value="4">OT</option>
                      </select>
                  </div>
              </div>

              <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12 regularization-options" style="display:none;">
                  <div class="form-group">
                      <label>Approval Required</label>
                      <select class="form-control approval-required" id="approval_required">
                          <option value="">Select</option>
                          <option value="1">Yes</option>
                          <option value="2">No</option>
                      </select>
                  </div>
              </div>

              <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12 approval-type-container" style="display:none;">
                  <div class="form-group">
                      <label>Allowed Approval Types</label>
                      <select class="form-control approval-type" id="approved_request_type" multiple>
                          <option value="1">Leave</option>
                          <option value="2">Permission</option>
                          <option value="3">Week Off</option>
                          <option value="4">OT</option>
                      </select>
                  </div>
              </div>
            `;
            } else {
              submenuHtml = `
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="custom-control custom-checkbox">
                <input type="checkbox"
                    value="${submenu.sub_menu_id}"
                    class="submenu-checkbox"
                    name="${submenu.sub_menu_link}"
                    id="${submenu.sub_menu_link}"
                    tabindex="${tabindex}"
                    disabled>

                <label class="custom-control-label ms-2"
                    for="${submenu.sub_menu_link}">
                    ${submenu.sub_menu}
                </label>
            </div>
        </div>`;
            }

            $(`#${mainMenuLink}-mainmenu-submenus`).append(submenuHtml);

            tabindex++;
          });
        }

        // Initialize Choices only once
        initializeRegularizationChoices();

        // Get user permissions
        $.post(
          "api/user_creation_files/get_user_permissions.php",
          { user_id: userId },
          function (userPermissions) {
            userPermissions.forEach(function (permission) {
              $(`#${permission.main_menu_link}-mainmenu`)
                .prop("checked", true)
                .trigger("change");

              $(`#${permission.sub_menu_link}`).prop("checked", true);
            });

            // Everything finished
            resolve();
          },
          "json",
        ).fail(reject);
      },
      "json",
    ).fail(reject);
  });
}

/* --- Get Auto Increment User ID --- */
async function getUserID(cmpy_id, id) {
  try {
    const response = await $.ajax({
      url: "api/user_creation_files/get_user_id.php",
      type: "POST",
      data: { id, cmpy_id },
      dataType: "json",
    });

    $("#user_code").val(response);

    return response;
  } catch (error) {
    console.error("Error fetching User ID:", error);
    swalError("Warning", "Unable to fetch User ID");
  }
}

/* --- Delete User Creation --- */
function deleteUser(id) {
  $.post(
    "api/user_creation_files/delete_user.php",
    { id },
    function (response) {
      if (response == "1") {
        swalSuccess("Success", "User Deleted Successfully.");
        $("#view_staff").trigger("click");
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

// to get the company name drop down for multi select
async function getCompanyNameDropdown() {
  const multi_company_name2 = $("#multi_company_name2").val();

  try {
    const response = await $.ajax({
      url: "api/user_creation_files/get_company_name.php",
      type: "POST",
      data: {},
      dataType: "json",
    });

    companyMultiple.clearChoices();
    companyMultiple.removeActiveItems();

    const selectedIds = multi_company_name2
      ? multi_company_name2.split(",")
      : [];

    const items = response.map((val) => ({
      value: val.id,
      label: val.company_name,
      selected: selectedIds.includes(val.id.toString()),
      disabled: val.disabled && !selectedIds.includes(val.id.toString()),
    }));

    companyMultiple.setChoices(items, "value", "label", true);
  } catch (err) {
    console.error("Error loading department dropdown:", err);
  }
}

// to get the director name
function getDirectorName() {
  return new Promise((resolve, reject) => {
    $.post(
      "api/user_creation_files/get_director_name.php",
      {},

      function (response) {
        let dropdown = $("#director_name");
        dropdown.empty();
        dropdown.append('<option value="">Select Director Name</option>');
        $.each(response, function (index, item) {
          dropdown.append(
            `<option value="${item.id}">${item.director_name}
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

// to get the company name
async function getCompanyName(selector) {
  return new Promise((resolve, reject) => {
    $.post(
      "api/user_creation_files/get_company_name.php",
      {},

      function (response) {
        let dropdown = $(selector);
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

function initializeChoices(selector, placeholder) {
  $(selector).each(function () {
    if ($(this).data("choices")) return;

    $(this).data(
      "choices",
      new Choices(this, {
        removeItemButton: true,
        placeholder: true,
        placeholderValue: placeholder,
        itemSelectText: "",
        searchEnabled: false,
        allowHTML: false,
      }),
    );
  });
}

function initializeRegularizationChoices() {
  initializeChoices(".regularization-type", "Select Allowed Request");
  initializeChoices(".approval-type", "Select Approved Request");
}

function validateRegularization() {
  if (!$("#regularization").is(":checked")) {
    return true;
  }

  let valid = true;
  const userType = $("#user_type").val();

  const requestTypes = $(".regularization-type").val();
  const approvalRequired = $(".approval-required").val();
  const approvalTypes = $(".approval-type").val();

  // Reset borders
  $(".regularization-type").closest(".choices").css("border", "");
  $(".approval-type").closest(".choices").css("border", "");
  $(".approval-required").css("border", "");

  if (userType === "1") {
    // Director Validation

    // Approval Required is mandatory
    if (approvalRequired === "") {
      $(".approval-required").css("border", "1px solid red");
      valid = false;
    }

    // If Approval Required = Yes,
    // Allowed Approval Types is mandatory
    if (approvalRequired === "1") {
      if (!approvalTypes || approvalTypes.length === 0) {
        $(".approval-type").closest(".choices").css("border", "1px solid red");
        valid = false;
      }
    }
  } else {
    // Staff Validation

    // At least one field must be selected
    if (
      (!requestTypes || requestTypes.length === 0) &&
      approvalRequired === ""
    ) {
      $(".regularization-type")
        .closest(".choices")
        .css("border", "1px solid red");

      $(".approval-required").css("border", "1px solid red");

      swalError(
        "Warning",
        "Please select at least one field in the Regularization settings.",
      );

      return false;
    }

    // If Approval Required is No,
    // Allowed Request Types is not mandatory
    if (
      approvalRequired === "2" &&
      (!requestTypes || requestTypes.length === 0)
    ) {
      $(".regularization-type")
        .closest(".choices")
        .css("border", "1px solid red");

      valid = false;
    }

    // If Approval Required = Yes,
    // Allowed Approval Types is mandatory
    if (approvalRequired === "1") {
      if (!approvalTypes || approvalTypes.length === 0) {
        $(".approval-type").closest(".choices").css("border", "1px solid red");

        valid = false;
      }
    }
  }

  if (!valid) {
    swalError("Warning", "Please complete the Regularization settings.");
  }

  return valid;
}
