// multiselect for the director company mapping
const reportingStaffInstance = new Choices("#reporting_staff", {
  removeItemButton: true,
  placeholder: true,
  placeholderValue: "Select Reporting Staff",
  itemSelectText: "",
  allowHTML: false,
  searchEnabled: false,
});

$(document).ready(function () {
  /* --- Add Company Button & Back Button Click --- */
  $(document).on("click", "#add_reporting_person,#back_btn", function () {
    $("#director_div").hide();
    $(".staff_div").show();
    swapTableAndCreation();
  });

  $(document).on(
    "click",
    "#hierarchyModal .close, #hierarchyModal .btn-secondary",
    function () {
      $("#hierarchyModal").modal("hide");
    },
  );

  // User Type On Change
  $("#user_type").on("change", function () {
    const user_type = $(this).val();

    // Clear Reporting Staff
    reportingStaffInstance.clearChoices();
    reportingStaffInstance.removeActiveItems();

    if (user_type === "1") {
      // ==========================
      // DIRECTOR
      // ==========================

      $("#director_div").show();
      $(".staff_div").hide();

      const company_id = $("#company_name").val();

      getDirectorName(company_id);
    } else {
      // ==========================
      // STAFF
      // ==========================

      $("#director_div").hide();
      $(".staff_div").show();

      // Clear Director
      $("#director_name").val("");
    }
  });

  // Company Name On Change
  $("#company_name").on("change", function () {
    let company_id = $("#company_name").val();
    getDesignation(company_id);
  });

  // Designation On Change
  $("#designation").on("change", function () {
    let designation = $("#designation").val();
    getReportingPerson(designation);

    $("#reporting_person").empty();
    $("#reporting_person").append(
      '<option value="">Select Reporting Person</option>',
    );
    $("#reporting_person").val("");

    $("#reporting_staff2").val("");

    if (reportingStaffInstance) {
      reportingStaffInstance.removeActiveItems();

      reportingStaffInstance.clearChoices();
    }
  });

  // Reporting Person On Change
  $("#reporting_person").on("change", function () {
    const reporting_person = $(this).val();

    if (!reporting_person) {
      reportingStaffInstance.clearChoices();
      reportingStaffInstance.removeActiveItems();
      return;
    }

    getReportingStaff(reporting_person, "staff");
  });

  // Director Name On Change
  $("#director_name").on("change", function () {
    const director_id = $(this).val();

    if (!director_id) {
      reportingStaffInstance.clearChoices();
      reportingStaffInstance.removeActiveItems();
      return;
    }

    getReportingStaff(director_id, "director");
  });

  /* --- Submit Reporting Person Mapping --- */
  $("#submit_reporting_person_mapping").click(function (event) {
    event.preventDefault();

    let company_name = $("#company_name").val();
    let user_type = $("#user_type").val();
    let designation = $("#designation").val();
    let reporting_person = $("#reporting_person").val();

    // Get selected staff IDs
    let reporting_staff = reportingStaffInstance.getValue(true);

    // Existing saved staff IDs
    let reporting_staff2 = $("#reporting_staff2").val();

    let director_name = $("#director_name").val() || "";
    let reporting_person_id = $("#reporting_person_id").val();

    /*
     * Validation
     */
    var data = ["company_name", "user_type"];

    var isValid = true;

    /*
     * Validate common fields
     */
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);

      if (!fieldIsValid) {
        isValid = false;
      }
    });

    /*
     * User Type = Staff
     *
     * Designation and Reporting Person are mandatory
     */
    if ($("#user_type").val() == "2") {
      var designationValid = validateField(
        $("#designation").val(),
        "designation",
      );

      var reportingPersonValid = validateField(
        $("#reporting_person").val(),
        "reporting_person",
      );

      if (!designationValid || !reportingPersonValid) {
        isValid = false;
      }
    }

    let repoetingStaffValid = validateMultiSelectField(
      "reporting_staff",
      reportingStaffInstance,
    );

    if (isValid && repoetingStaffValid) {
      swalConfirm(
        "Are you sure?",
        "Do you want to submit this Reporting Person Mapping?",
        function () {
          $.post(
            "api/reporting_person_files/submit_reporting_person_mapping.php",
            {
              company_name: company_name,
              user_type: user_type,
              designation: designation,
              reporting_person: reporting_person,

              // Send staff IDs
              reporting_staff: reporting_staff,

              // Existing staff IDs
              reporting_staff2: reporting_staff2,

              director_name: director_name,
              reporting_person_id: reporting_person_id,
            },
            function (response) {
              if (response == "1") {
                swalSuccess("Success", "Reporting Person Added Successfully!");
              } else if (response == "2") {
                swalSuccess(
                  "Success",
                  "Reporting Person Updated Successfully!",
                );
              }

              $("#reporting_person_id").val("");
              $("#reporting_staff").val("");
              $("#reporting_staff2").val("");

              reportingStaffInstance.clearChoices();
              reportingStaffInstance.removeActiveItems();

              $("#reporting_person_mapping").trigger("reset");

              geteReportingPersonMapping();

              swapTableAndCreation();
            },
          );
        },
      );
    }
  });

  /* --- Edit Reporting Person Mapping --- */
  $(document).on("click", ".reportingPersonActionBtn", async function () {
    $("#reset_btn").hide();

    const id = $(this).attr("value");

    try {
      const response = await $.ajax({
        url: "api/reporting_person_files/get_reporting_person_mapping_data.php",
        type: "POST",
        data: {
          id: id,
        },
        dataType: "json",
      });

      if (!response || response.length === 0) {
        console.error("Reporting person data not found");
        return;
      }

      const data = response[0];

      await swapTableAndCreation();

      $("#reporting_person_id").val(data.reporting_person_id);

      // Company
      await getCompanyName();
      $("#company_name").val(String(data.company_id));

      // User Type
      $("#user_type").val(String(data.user_type));

      // =====================================================
      // DIRECTOR
      // =====================================================

      if (data.user_type == 1) {
        $("#director_div").show();
        $(".staff_div").hide();

        // Load Director dropdown
        const company_id = data.company_id;

        // Set selected Director
        await getDirectorName(
          company_id,
          data.director_id && data.director_id != 0
            ? String(data.director_id)
            : "",
        );
      }

      // =====================================================
      // STAFF
      // =====================================================
      else {
        $("#director_div").hide();
        $(".staff_div").show();

        // Clear Director
        $("#director_name").val("");
      }

      // =====================================================
      // DESIGNATION
      // =====================================================

      await getDesignation(data.company_id);

      $("#designation").val(String(data.designation));

      // =====================================================
      // REPORTING PERSON / REPORTING STAFF
      // =====================================================

      $("#reporting_staff2").val(data.reporting_staff2 || "");

      if (data.user_type == 1) {
        // Director
        await getReportingStaff(data.director_id, "director");
      } else {
        // Staff
        await getReportingPerson(data.designation, data.reporting_person_id);

        $("#reporting_person").val(String(data.reporting_person));

        await getReportingStaff(data.reporting_person, "staff");
      }
    } catch (error) {
      console.error("Failed to fetch reporting person data:", error);
    }
  });

  /* --- Reporting Person Mapping Reset --- */
  $('button[type="reset"], #back_btn').click(function (event) {
    event.preventDefault();

    $("input").val("");

    $("#designation").empty();
    $("#designation").append('<option value="">Select Designation</option>');
    $("#designation").val("");

    $("#reporting_person").empty();
    $("#reporting_person").append(
      '<option value="">Select Reporting Person</option>',
    );
    $("#reporting_person").val("");

    $("#reporting_staff2").val("");

    if (reportingStaffInstance) {
      reportingStaffInstance.removeActiveItems();

      reportingStaffInstance.clearChoices();
    }

    $("select")
      .not("#designation, #reporting_person, #reporting_staff")
      .each(function () {
        $(this).val($(this).find("option:first").val());
      });

    $("input").css("border", "1px solid #cecece");

    $("select").css("border", "1px solid #cecece");

    $("#reporting_staff")
      .closest(".choices")
      .find(".choices__inner")
      .css("border", "1px solid #cecece");
  });

  // Reporting Person Hierarchy
  $(document).on("click", ".reportingPersonHierarchyBtn", async function () {
    const reportingPersonId = $(this).attr("value");

    if (!reportingPersonId) {
      return;
    }

    $("#hierarchyChart").html(`
            <div class="text-center p-4">
                Loading hierarchy...
            </div>
        `);

    $("#hierarchyModal").modal("show");

    try {
      const response = await $.ajax({
        url: "api/reporting_person_files/get_reporting_hierarchy.php",

        type: "POST",

        data: {
          reporting_person_id: reportingPersonId,
        },

        dataType: "json",
      });

      if (!response || !response.status || !response.data) {
        $("#hierarchyChart").html(`
                    <div class="text-center text-muted p-4">
                        No hierarchy found.
                    </div>
                `);

        return;
      }

      $("#hierarchyChart").html(buildHierarchyTree(response.data));
    } catch (error) {
      console.error("Error loading hierarchy:", error);

      $("#hierarchyChart").html(`
                <div class="text-center text-danger p-4">
                    Failed to load hierarchy.
                </div>
            `);
    }
  });
});

// function start
$(function () {
  geteReportingPersonMapping();
});

/* --- Get Reporting Person Mapping Outer List Table --- */
function geteReportingPersonMapping() {
  serverSideTable(
    "#reporting_person_mapping_table",
    "",
    "api/reporting_person_files/reporting_person_mapping_list.php",
    "Reporting Person Mapping List",
  );
}

// when we click back and add button this function call
async function swapTableAndCreation() {
  if ($("#reporting_person_content").is(":visible")) {
    $("#reporting_person_content").hide();
    $(".addReportingPersonbtn").show();
    $(".reporting_person_table_content").show();
    $(".backBtn").hide();
  } else {
    $("#reporting_person_content").show();
    $(".reporting_person_table_content").hide();
    $(".backBtn").show();
    $(".addReportingPersonbtn").hide();
    getCompanyName();
  }
}

/* --- Get Company Name --- */
async function getCompanyName() {
  return new Promise((resolve, reject) => {
    $.post(
      "api/attendance_files/get_company_list.php",
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

/* --- Get Company Based On Designation --- */
function getDesignation(company_id) {
  return new Promise((resolve, reject) => {
    $.post(
      "api/reporting_person_files/get_designation.php",
      { company_id: company_id },

      function (response) {
        let dropdown = $("#designation");

        dropdown.empty();
        dropdown.append('<option value="">Select Designation</option>');

        $.each(response, function (index, item) {
          dropdown.append(
            `<option value="${item.id}">${item.designation}</option>`,
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

/* --- Get Designation Based On Reporting Person --- */
function getReportingPerson(designation, reporting_person_id = "") {
  return new Promise((resolve, reject) => {
    $.post(
      "api/reporting_person_files/get_reporting_person.php",

      {
        designation: designation,
        reporting_person_id: reporting_person_id,
      },

      function (response) {
        let dropdown = $("#reporting_person");

        dropdown.empty();

        dropdown.append('<option value="">Select Reporting Person</option>');

        $.each(response, function (index, item) {
          const isMapped = Number(item.is_mapped) === 1;

          const isCurrent = String(item.id) === String(reporting_person_id);

          const disabled = isMapped && !isCurrent;

          dropdown.append(
            `<option
                value="${item.id}"
                ${disabled ? "disabled" : ""}
            >
                ${item.staff_name}
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

/* --- Get Reporting Staff Based On Reporting Person --- */
async function getReportingStaff(person_id, type) {
  const company_id = $("#company_name").val();
  const reporting_staff2 = $("#reporting_staff2").val();
  const reporting_person_id = $("#reporting_person_id").val();

  try {
    const response = await $.ajax({
      url: "api/reporting_person_files/get_reporting_staff.php",
      type: "POST",
      data: {
        person_id: person_id,
        type: type,
        company_id: company_id,
        reporting_person_id: reporting_person_id,
      },
      dataType: "json",
    });

    reportingStaffInstance.clearChoices();
    reportingStaffInstance.removeActiveItems();

    const selectedIds = reporting_staff2
      ? reporting_staff2.split("||").map((id) => String(id).trim())
      : [];

    const items = response.map((val) => {
      const staffId = String(val.id);

      const isSelected = selectedIds.includes(staffId);
      const isMapped = Number(val.is_mapped) === 1;

      return {
        value: staffId,
        label: val.label,
        selected: isSelected,
        disabled: isMapped && !isSelected,
      };
    });

    reportingStaffInstance.setChoices(items, "value", "label", true);
  } catch (err) {
    console.error("Error loading reporting staff:", err);
  }
}

/* --- Get Director Name --- */
function getDirectorName(company_id, selectedDirectorId = "") {
  return new Promise((resolve, reject) => {
    $.post(
      "api/reporting_person_files/get_director_name.php",
      {
        company_id: company_id,
      },
      function (response) {
        let dropdown = $("#director_name");

        dropdown.empty();

        dropdown.append('<option value="">Select Director</option>');

        $.each(response, function (index, item) {
          const directorId = String(item.id);
          const isMapped = Number(item.is_mapped) === 1;

          const isSelected =
            selectedDirectorId && directorId === String(selectedDirectorId);

          dropdown.append(`
            <option
              value="${directorId}"
              ${isSelected ? "selected" : ""}
              ${isMapped && !isSelected ? "disabled" : ""}
            >
              ${item.director_name}
            </option>
          `);
        });

        resolve();
      },
      "json",
    ).fail(function (xhr, status, error) {
      console.error("Error loading director:", error);

      reject(error);
    });
  });
}

// Build hierarchy tree
function buildHierarchyTree(node) {
  let html = `
          <div class="hierarchy-node-wrapper">

              <div class="hierarchy-node">

                  <div class="staff-name">
                      ${escapeHtml(node.staff_name || "")}
                  </div>

                  <div class="designation">
                      ${escapeHtml(node.designation_name || "")}
                  </div>
      `;

  if (node.branch_name || node.department_name || node.team_name) {
    html += `
              <div class="staff-info">
                  ${escapeHtml(node.branch_name || "")}
                  ${node.department_name ? " - " + escapeHtml(node.department_name) : ""}
                  ${node.team_name ? " - " + escapeHtml(node.team_name) : ""}
              </div>
          `;
  }

  html += `</div>`;

  /*
   * ==========================================================
   * CHILDREN
   * ==========================================================
   */

  if (node.children && node.children.length > 0) {
    html += `
              <div class="hierarchy-children">
          `;

    node.children.forEach(function (child) {
      html += `
                  <div class="hierarchy-child">
                      ${buildHierarchyTree(child)}
                  </div>
              `;
    });

    html += `</div>`;
  }

  html += `
          </div>
      `;

  return html;
}

// Escape HTML
function escapeHtml(value) {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}
