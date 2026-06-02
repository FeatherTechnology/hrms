/* --- Team Multi Select --- */
const teamInstance = new Choices("#team_name", {
  removeItemButton: true,
  placeholder: true,
  placeholderValue: "Select Team Name",
  searchPlaceholderValue: "Search Team",
  itemSelectText: "",
  allowHTML: false,
});

$(document).ready(function () {
  /* --- Add Team & BackButton Click --- */
  $(document).on(
    "click",
    ".add_team_btn, .back_to_team_btn",
    async function () {
      swapTableAndCreation();
    },
  );

  /* --- Team Creation On Change Events --- */
  $("#company_name").change(function () {
    getDepartmentName();
  });

  /* --- Submit Team Modal --- */
  $("#submit_team_creation").click(function () {
    event.preventDefault();
    //Validation
    let company_name = $("#company_name").val();
    let department_name = $("#department_name").val();
    let team_name = $("#team_name").val();
    let team_name2 = $("#team_name2").val();
    let team_creation_id = $("#team_creation_id").val();

    var data = ["company_name", "department_name"];

    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });

    let teamValid = validateMultiSelectField("team_name", teamInstance);

    if (isValid && teamValid) {
      $.post(
        "api/team_creation_files/submit_team_creation.php",
        {
          company_name,
          department_name,
          team_name,
          team_name2,
          team_creation_id,
        },
        function (response) {
          if (response == "1") {
            swalSuccess("Success", "Team Added Successfully!");
          } else {
            swalSuccess("Success", "Team Updated Successfully!");
          }

          $("#team_creation_id").val("");
          $("#team_name2").val("");
          $("#team_creation").trigger("reset");
          getTeamTable();
          swapTableAndCreation(); //to change to div to table content.
        },
      );
    }
  });

  /* --- Edit Team Modal --- */
  $(document).on("click", ".teamCreationActionBtn", async function () {
    var id = $(this).attr("value"); // Get value attribute

    try {
      const response = await $.ajax({
        url: "api/team_creation_files/get_team_creation_data.php",
        type: "POST",
        data: { id },
        dataType: "json",
      });

      swapTableAndCreation();
      await getCompanyName();
      $("#team_creation_id").val(id);
      $("#company_name").val(response[0].company_id);

      await getDepartmentName();
      $("#department_name").val(response[0].department_id);

      $("#team_name2").val(response[0].team_ids);
      await getTeamNameDropdown();
    } catch (error) {
      console.error("Failed to fetch company data:", error);
    }
  });

  /* --- Delete Team Modal --- */
  $(document).on("click", ".teamCreationDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the Team Creation?",
      getTeamDelete,
      id,
    );
    return;
  });

  /* --- Submit Team Creation --- */
  $("#submit_team").click(function (event) {
    event.preventDefault();
    // Validation
    let team_code = $("#modal_team_code").val(); // Remove spaces from department_code
    let team_name = $("#modal_team_name").val();
    let team_id = $("#team_id").val();

    var data = ["modal_team_code", "modal_team_name"];

    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });

    if (isValid) {
      swalConfirm(
        "Are you sure?",
        "Do you want to submit this Team info?",
        function () {
          $.post(
            "api/team_creation_files/submit_team_info.php",
            {
              team_code,
              team_name,
              team_id,
            },
            function (response) {
              if (response === "3") {
                swalError("Warning", "Team already exists!");
              } else if (response === "2") {
                swalSuccess("Success", "Team Added Successfully!");
              } else if (response === "1") {
                swalSuccess("Success", "Team Updated Successfully!");
              } else {
                swalError("Error", "Error Occurred!");
              }

              // Refresh the department table
              getTeamNameTable();
            },
          );
        },
      );
    }
  });

  /* --- Edit Team Creation --- */
  $(document).on("click", ".teamActionBtn", function () {
    var id = $(this).attr("value"); // Get value attribute
    $.post(
      "api/team_creation_files/team_modal_data.php",
      { id: id },
      function (response) {
        $("#team_id").val(id);
        $("#modal_team_code").val(response[0].team_code);
        $("#modal_team_name").val(response[0].team_name);
      },
      "json",
    );
  });

  /* --- Delete Team Creation --- */
  $(document).on("click", ".teamDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the Team Details?",
      getTeamModalDelete,
      id,
    );
    return;
  });

  /* --- Team Creation Reset --- */
  $('button[type="reset"], .back_to_team_btn').click(function (event) {
    event.preventDefault();

    $("input").val("");

    $("select").each(function () {
      $(this).val("");
    });

    teamInstance.removeActiveItems();

    $("input").css("border", "1px solid #cecece");
    $("select").css("border", "1px solid #cecece");
    $("#team_name")
      .closest(".choices")
      .find(".choices__inner")
      .css("border", "1px solid #cecece");
  });
});

/* --- On Load --- */
$(function () {
  getTeamTable();
});

/* --- Swap Table and hide/show --- */
function swapTableAndCreation() {
  if ($(".team_table_content").is(":visible")) {
    $(".team_table_content").hide();
    $(".add_team_btn").hide();
    $("#team_creation_content").show();
    $(".back_to_team_btn").show();
    getCompanyName();
    getTeamNameDropdown();
  } else {
    $(".add_team_btn").show();
    $(".team_table_content").show();
    $("#team_creation_content").hide();
    $(".back_to_team_btn").hide();
  }
}

/* --- Get Company Name --- */
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
            `<option value="${item.id}">${item.company_name}</option>`,
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

/* --- Get Department Name --- */
async function getDepartmentName() {
  let company_name = $("#company_name").val();
  return new Promise((resolve, reject) => {
    $.post(
      "api/team_creation_files/getDepartmentName.php",
      { company_name: company_name },

      function (response) {
        let dropdown = $("#department_name");
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

/* --- Team Creation Outer List Table --- */
function getTeamTable() {
  $.post(
    "api/team_creation_files/team_creation_list.php",
    function (response) {
      var columnMapping = [
        "sno",
        "company_name",
        "department_name",
        "team_name",
        "action",
      ];
      appendDataToTable("#team_creation_table", response, columnMapping);
      setdtable("#team_creation_table", "Team Creation List");
    },
    "json",
  );
}

/* --- Get Auto Generated Team Id --- */
function getAutoGenTeamId(id) {
  $.post(
    "api/team_creation_files/get_autoGen_team_id.php",
    { id },
    function (response) {
      $("#modal_team_code").val(response);
    },
    "json",
  ).fail(function (error) {
    console.log(error);
  });
}

/* --- Get Team Name Modal Table --- */
function getTeamNameTable() {
  $.post(
    "api/team_creation_files/team_modal_list.php",
    {},
    function (response) {
      var columnMapping = ["sno", "team_code", "team_name", "action"];

      appendDataToTable("#team_modal_table", response, columnMapping);
      setdtable("#team_modal_table", "Team Creation List");

      $("#team_form input").not("#modal_team_code").val("");
      $("#team_form select").each(function () {
        $(this).val($(this).find("option:first").val());
      });
      $("#team_form input").css("border", "1px solid #cecece");
      $("#team_form select").css("border", "1px solid #cecece");

      getAutoGenTeamId(0);
    },
    "json",
  );
}

/* --- Get Team Modal Delete --- */
function getTeamModalDelete(id) {
  $.post(
    "api/team_creation_files/delete_team_modal.php",
    { id },
    function (response) {
      if (response == "1") {
        swalSuccess("Success", "Team Info Delete Successfully!");
        getTeamNameTable();
      } else if (response == "2") {
        swalError("Warning", "Team is already used in Staff Creation!");
      } else {
        swalError("Warning", "Error occur While Delete Team Info.");
      }
    },
    "json",
  );
}

/* --- Get Team Name Dropdown --- */
async function getTeamNameDropdown() {
  const team_name2 = $("#team_name2").val();

  try {
    const response = await $.ajax({
      url: "api/team_creation_files/get_team_name_dropdown.php",
      type: "POST",
      dataType: "json",
    });

    teamInstance.clearStore();

    const selectedIds = team_name2 ? team_name2.split(",") : [];

    const items = response.map((val) => ({
      value: val.id,
      label: val.team_name,
      selected: selectedIds.includes(val.id.toString()),
      disabled: val.disabled && !selectedIds.includes(val.id.toString()),
    }));

    teamInstance.setChoices(items, "value", "label", true);
  } catch (err) {
    console.error("Error loading team dropdown:", err);
  }
}

/* --- Get Team Creation Delete --- */
function getTeamDelete(id) {
  $.post(
    "api/team_creation_files/delete_team_creation.php",
    { id },
    function (response) {
      if (response == "1") {
        swalSuccess("Success", "Team Info Delete Successfully!");
        getTeamTable();
      } else {
        swalError("Warning", "Error occur While Delete Team Info.");
      }
    },
    "json",
  );
}
