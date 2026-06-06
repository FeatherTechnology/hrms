$(document).ready(function () {
  /* --- Add Location Access & Back Button Click --- */
  $(document).on("click", ".backBtn", function () {
    let company_id = $("#company_name").val();
    let branch_id = $("#branch_name_one").val();
    let department_id = $("#department_name_one").val();
    getLocationAccessTable(company_id, branch_id, department_id);
    swapTableAndCreation();
  });

  /* --- Location Access On Change & Click Events --- */
  $("#company_name").on("change", function () {
    let compnay_id = $("#company_name").val();
    getBranchName(compnay_id);
    getDepartmentName(compnay_id);
  });

  $("#branch_name_three").on("change", function () {
    let branch_id = $(this).val();
    getBranchLocation(branch_id);
  });

  // Set min date for from_date on page load
  {
    let today = new Date().toISOString().split("T")[0];
    $("#from_date").attr("min", today);
  }

  // When from_date changes
  $("#from_date").change(function () {
    let from_date = $("#from_date").val();
    let to_date = $("#to_date").val();

    // Clear to_date if it is less than from_date
    if (to_date && from_date > to_date) {
      $("#to_date").val("");
    }

    // Set minimum selectable date for to_date
    $("#to_date").attr("min", from_date);
  });

  $("#search_location").on("click", function () {
    let company_id = $("#company_name").val();
    let branch_id = $("#branch_name_one").val();
    let department_id = $("#department_name_one").val();
    if (company_id === "" && branch_id === "" && department_id === "") {
      swalError("Warning", "Please select at least one fields.");
      return;
    }
    getLocationAccessTable(company_id, branch_id, department_id);
  });

  /* --- Edit Location Access --- */
  $(document).on("click", ".locationActionBtn", function () {
    let id = $(this).data("id"); // Get value attribute
    let staff_profile_id = $(this).data("staff-profile-id");

    // store in hidden field
    $("#staff_profile_id").val(staff_profile_id);

    $.post(
      "api/location_creation_files/location_access_data.php",
      { id: id },
      function (response) {
        swapTableAndCreation();
        $("#staff_info_id").val(id);
        $("#branch_name_two").val(response[0].branch_name);
        $("#department_name_two").val(response[0].department_name);
        $("#staff_name").val(response[0].staff_name);
        $("#staff_id").val(response[0].staff_id);
        getLocationMappingTable(staff_profile_id);
        // Load branches and exclude current branch
        getBranchName(response[0].company_id, response[0].branch_name);
        clearFields();
      },
      "json",
    );
  });

  /* --- Submit Location Access --- */
  $("#submit_location_access").click(function () {
    event.preventDefault();
    //Validation
    let staff_profile_id = $("#staff_profile_id").val();
    let staff_id = $("#staff_id").val();
    let from_date = $("#from_date").val();
    let to_date = $("#to_date").val();
    let branch_name_three = $("#branch_name_three").val();
    let branch_location = $("#branch_location").val();
    let reason = $("#reason").val();
    let location_access_id = $("#location_access_id").val();
    var data = ["from_date", "to_date", "branch_name_three", "branch_location"];
    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });
    if (isValid) {
      $.post(
        "api/location_creation_files/submit_location_creation.php",
        {
          staff_id,
          from_date,
          to_date,
          branch_name_three,
          branch_location,
          location_access_id,
          reason,
          staff_profile_id,
        },
        function (response) {
          if (response == "2") {
            swalSuccess("Success", "Location Access Added Successfully!");
          } else if (response == "1") {
            swalSuccess("Success", "Location Access Updated Successfully!");
          } else if (response == "3") {
            swalError("Warning", "Date range already exists or overlaps!");
          } else {
            swalError("Error", "Error Occurs!");
          }
          getLocationMappingTable(staff_profile_id);
          clearFields();
        },
      );
    }
  });

  /* --- Edit Location Mapping --- */
  $(document).on("click", ".locationMappingActionBtn", async function () {
    var id = $(this).attr("value"); // Get value attribute

    try {
      const response = await $.ajax({
        url: "api/location_creation_files/location_mapping_data.php",
        type: "POST",
        data: { id },
        dataType: "json",
      });

      $("#location_access_id").val(id);
      $("#from_date").val(response[0].from_date);
      $("#to_date").val(response[0].to_date);
      await getBranchName(response[0].company_id, $("#branch_name_two").val());
      $("#branch_name_three").val(response[0].assigned_branch);
      $("#branch_location").val(response[0].lattitude_longitude);
      $("#reason").val(response[0].reason);
    } catch (error) {
      console.error("Failed to fetch branch data:", error);
    }
  });

  /* --- Delete Location Mapping --- */
  $(document).on("click", ".locationMappingDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the Location Mapping?",
      getLocationMappingDelete,
      id,
    );
    return;
  });
});

/* --- On Load --- */
$(function () {
  getCompanyName();
});

/* --- Swap Table and hide/show --- */
function swapTableAndCreation() {
  if ($(".location_table_content").is(":visible")) {
    $(".location_table_content").hide();
    $(".location_search").hide();
    $(".staff_information").show();
    $(".backBtnContainer").show();
  } else {
    $(".location_table_content").show();
    $(".location_search").show();
    $(".staff_information").hide();
    $(".backBtnContainer").hide();
  }
}

/* --- Get Company Name --- */
function getCompanyName() {
  $.ajax({
    url: "api/branch_creation/getCompanyName.php",
    type: "POST",
    data: {},
    dataType: "json",
    cache: false,
    success: function (response) {
      let dropdown = $("#company_name");

      dropdown.empty(); // clear existing

      dropdown.append('<option value="">Select Company Name</option>');

      // assuming response is array of objects
      $.each(response, function (index, item) {
        dropdown.append(
          `<option value="${item.id}">${item.company_name}</option>`,
        );
      });
    },
    error: function (xhr, status, error) {
      swalError("Error", status + error);
    },
  });
}

/* --- Get Branch Name --- */
async function getBranchName(compnay_id, excludeBranch = "") {
  try {
    const response = await $.ajax({
      url: "api/location_creation_files/getBranchName.php",
      type: "POST",
      data: { compnay_id },
      dataType: "json",
      cache: false,
    });

    $("#branch_name_one, #branch_name_three")
      .empty()
      .append('<option value="">Select Branch Name</option>');

    $.each(response, function (index, item) {
      // Search dropdown - show all branches
      $("#branch_name_one").append(
        `<option value="${item.id}">${item.branch_name}</option>`,
      );

      // Assignment dropdown - exclude current branch
      if (item.branch_name !== excludeBranch) {
        $("#branch_name_three").append(
          `<option value="${item.id}">${item.branch_name}</option>`,
        );
      }
    });
  } catch (error) {
    swalError("Error", error.statusText || error);
  }
}

/* --- Get Department Name --- */
async function getDepartmentName(company_name) {
  return new Promise((resolve, reject) => {
    $.post(
      "api/team_creation_files/getDepartmentName.php",
      { company_name: company_name },

      function (response) {
        let dropdown = $("#department_name_one");
        dropdown.empty();
        dropdown.append('<option value="">Select Department Name</option>');
        $.each(response, function (index, item) {
          dropdown.append(
            `<option value="${item.id}">${item.department_name}</option>`,
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

/* --- Get Location Access Outer List Table --- */
function getLocationAccessTable(company_id, branch_id, department_id) {
  serverSideTable(
    "#location_creation_table",
    {
      company_id: company_id,
      branch_id: branch_id,
      department_id: department_id,
    },
    "api/location_creation_files/location_creation_list.php",
    "Location Access List",
  );
}

/* --- Get Branch Location --- */
function getBranchLocation(branch_id) {
  $.post(
    "api/location_creation_files/getBranchLocation.php",
    { branch_id },
    function (response) {
      $("#branch_location").val(response[0].location);
    },
    "json",
  );
}

/* --- Get Location Mapping Table --- */
function getLocationMappingTable(staff_profile_id) {
  $.post(
    "api/location_creation_files/location_mapping_list.php",
    { staff_profile_id },
    function (response) {
      var columnMapping = [
        "sno",
        "branch_name",
        "assigned_branch_name",
        "from_date",
        "to_date",
        "lattitude_longitude",
        "action",
      ];
      appendDataToTable("#location_mapping_table", response, columnMapping);
      setdtable("#location_mapping_table", "Location Mapping List");
    },
    "json",
  );
}

/* --- Delete Location Mapping Table --- */
function getLocationMappingDelete(id) {
  let staff_profile_id = $("#staff_profile_id").val();
  let company_id = $("#company_name").val();
  let branch_id = $("#branch_name_one").val();
  let department_id = $("#department_name_one").val();

  $.post(
    "api/location_creation_files/delete_location_mapping.php",
    { id },
    function (response) {
      if (response == "1") {
        swalSuccess("Success", "Location Mapping Deleted Successfully!");
        getLocationMappingTable(staff_profile_id);
        getLocationAccessTable(company_id, branch_id, department_id);
        clearFields();
      } else {
        swalError("Warning", "Error occur While Delete Location Mapping.");
      }
    },
    "json",
  );
}

/* --- Clear Fields --- */
function clearFields() {
  $("#from_date").val("");
  $("#to_date").val("");
  $("#branch_name_three").val("");
  $("#branch_location").val("");
  $("#reason").val("");
  $("#location_access_id").val("");
  $("#location_form input").css("border", "1px solid #cecece");
  $("#location_form select").css("border", "1px solid #cecece");
}
