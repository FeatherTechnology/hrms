// Department Multi Select
const departmentInstances = {};

function initializeDepartmentChoices(selector) {
  departmentInstances[selector] = new Choices(selector, {
    removeItemButton: true,
    placeholder: true,
    placeholderValue: "Select Department Name",
    itemSelectText: "",
    allowHTML: false,
    searchEnabled: false,
  });
}

// Initialize all dropdowns
initializeDepartmentChoices("#feedback_config_department_name");
initializeDepartmentChoices("#rating_department_name");
initializeDepartmentChoices("#poll_department_name");

$(document).ready(function () {
  getCompanyName();
  $("input[name=general_feedback_type]").click(function () {
    let general_feedback_type = $(this).val();
    if (general_feedback_type == "general") {
      $("#general_feedback_content").show();
      $("#scheduled_feedback_content").hide();
      $("#scheduled_feedback_configuration").hide();
      $("#scheduled_rating").hide();
      $("#scheduled_poll").hide();
      $("#general_feedback").trigger("reset");
    } else if (general_feedback_type == "scheduled") {
      $("#general_feedback_content").hide();
      $("#general_feedback_table_content").hide();
      $("#scheduled_feedback_content").show();
      $("#scheduled_feedback_creation").trigger("reset");
    }
  });

  function initializeDateTimeValidation({ startSelector, endSelector }) {
    // Current date & time
    let now = new Date();

    // Format => YYYY-MM-DDTHH:MM
    let formattedNow =
      now.getFullYear() +
      "-" +
      String(now.getMonth() + 1).padStart(2, "0") +
      "-" +
      String(now.getDate()).padStart(2, "0") +
      "T" +
      String(now.getHours()).padStart(2, "0") +
      ":" +
      String(now.getMinutes()).padStart(2, "0");

    // Set minimum start datetime
    $(startSelector).attr("min", formattedNow);

    // Start date change event
    $(document).on("change", startSelector, function () {
      let startDate = $(this).val();

      let endDate = $(endSelector).val();

      // Clear invalid end date
      if (endDate && endDate < startDate) {
        $(endSelector).val("");
      }

      // Set min end date
      $(endSelector).attr("min", startDate);
    });
  }

  initializeDateTimeValidation({
    startSelector: "#feedback_config_start_date",
    endSelector: "#feedback_config_end_date",
  });

  initializeDateTimeValidation({
    startSelector: "#rating_start_date",
    endSelector: "#rating_end_date",
  });

  initializeDateTimeValidation({
    startSelector: "#poll_start_date",
    endSelector: "#poll_end_date",
  });

  $("#general_company_name").on("change", function () {
    let company_id = $("#general_company_name").val();

    getGeneralFeedbackTable(company_id);
    $("#general_feedback_table_content").show();

    $("#feedback_name").val("");
    $("#status").val("");
  });

  $("#feedback_config_company_name").on("change", function () {
    let company_id = $("#feedback_config_company_name").val();

    $("#feedback_config_department_name2").val("");

    getDepartmentNameDropdown({
      company_id: company_id,
      dropdownSelector: "#feedback_config_department_name",
      hiddenInputSelector: "#feedback_config_department_name2",
    });

    resetQuestionTable({
      tbodySelector: "#feedback_question_body",
      inputClass: "feedback_question",
      inputName: "feedback_question",
      placeholder: "Enter Feedback Question",
    });
    clearFeedbackConfigurationFields();
    getFeedbackConfigurationTable(company_id);
  });

  $("#rating_company_name").on("change", function () {
    let company_id = $("#rating_company_name").val();

    $("#rating_department_name2").val("");

    getDepartmentNameDropdown({
      company_id: company_id,
      dropdownSelector: "#rating_department_name",
      hiddenInputSelector: "#rating_department_name2",
    });

    clearRatingFields();
    getRatingTable(company_id);
  });

  $("#poll_company_name").on("change", function () {
    let company_id = $("#poll_company_name").val();

    $("#poll_department_name").val("");

    getDepartmentNameDropdown({
      company_id: company_id,
      dropdownSelector: "#poll_department_name",
      hiddenInputSelector: "#poll_department_name2",
    });

    resetQuestionTable({
      tbodySelector: "#poll_question_body",
      inputClass: "poll_option",
      inputName: "poll_option",
      placeholder: "Enter Poll Option",
    });
    clearPollFields();
    getPollTable(company_id);
  });

  $("#scheduled_feedback_search").on("click", function () {
    let scheduled_feedback_type = $("#scheduled_feedback_type").val();

    if (scheduled_feedback_type == "") {
      swalError("Warning", "Please select Scheduled Feedback Type.");
      return;
    }

    $("#feedback_config_company_name").val("");
    clearFeedbackConfigurationFields();
    $("#rating_company_name").val("");
    clearRatingFields();
    $("#poll_company_name").val("");
    clearPollFields();

    if ($.fn.DataTable.isDataTable("#feedback_configuration_table")) {
      $("#feedback_configuration_table").DataTable().clear().destroy();
    }

    if ($.fn.DataTable.isDataTable("#rating_table")) {
      $("#rating_table").DataTable().clear().destroy();
    }

    if ($.fn.DataTable.isDataTable("#poll_table")) {
      $("#poll_table").DataTable().clear().destroy();
    }

    resetQuestionTable({
      tbodySelector: "#feedback_question_body",
      inputClass: "feedback_question",
      inputName: "feedback_question",
      placeholder: "Enter Feedback Question",
    });

    resetQuestionTable({
      tbodySelector: "#poll_question_body",
      inputClass: "poll_option",
      inputName: "poll_option",
      placeholder: "Enter Poll Option",
    });

    if (scheduled_feedback_type == "1") {
      $("#scheduled_feedback_configuration").show();
      $("#scheduled_rating").hide();
      $("#scheduled_poll").hide();
    } else if (scheduled_feedback_type == "2") {
      $("#scheduled_feedback_configuration").hide();
      $("#scheduled_rating").show();
      $("#scheduled_poll").hide();
    } else if (scheduled_feedback_type == "3") {
      $("#scheduled_feedback_configuration").hide();
      $("#scheduled_rating").hide();
      $("#scheduled_poll").show();
    }
  });

  // <---------------------------------------------------------------------- Submit General Feedback Start -------------------------------------------------------------->

  $("#general_feedback_submit").click(function (event) {
    event.preventDefault();
    // Validation
    let general_feedback_id = $("#general_feedback_id").val();
    let company_id = $("#general_company_name").val();
    let feedback_name = $("#feedback_name").val();
    let status = $("#status").val();

    var data = ["general_company_name", "feedback_name", "status"];

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
        "Do you want to submit this General Feedback?",
        function () {
          $.post(
            "api/general_feedback_files/submit_general_feedback.php",
            {
              general_feedback_id,
              company_id,
              feedback_name,
              status,
            },
            function (response) {
              if (response === "2") {
                swalSuccess("Success", "General Feedback Added Successfully!");
              } else if (response === "1") {
                swalSuccess(
                  "Success",
                  "General Feedback Updated Successfully!",
                );
              } else {
                swalError("Error", "Error Occurred!");
              }

              // IMPORTANT
              // Destroy old datatable before reload
              $("#general_feedback_table").DataTable().destroy();
              getGeneralFeedbackTable(company_id);
              clearFields();
            },
          );
        },
      );
    }
  });

  // <------------------------------------------------------------------------------ Submit General Feedback End ----------------------------------------------------------->

  // <------------------------------------------------------------------------------ Edit General Feedback Start ----------------------------------------------------------->

  $(document).on("click", ".generalFeedbackActionBtn", function () {
    var id = $(this).attr("value"); // Get value attribute
    $.post(
      "api/general_feedback_files/general_feedback_creation_data.php",
      { id: id },
      function (response) {
        $("#general_feedback_id").val(id);
        $("#feedback_name").val(response[0].feedback_name);
        $("#status").val(response[0].status);
      },
      "json",
    );
  });

  // <---------------------------------------------------------------------- Edit General Feedback End ------------------------------------------------------------------->

  // <---------------------------------------------------------------------- Delete General Feedback Start --------------------------------------------------------------->

  $(document).on("click", ".generalFeedbackDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the General Feedback?",
      getgeneralFeedbackDelete,
      id,
    );
    return;
  });

  // <---------------------------------------------------------------------------- Delete General Feedback End ---------------------------------------------------------->

  // <------------------------------------------------------------------ Feedback Question Table Start ------------------------------------------------------------------->

  initializeQuestionTable({
    tbodySelector: "#feedback_question_body",
    inputClass: "feedback_question",
    inputName: "feedback_question",
    placeholder: "Enter Feedback Question",
    validationMessage: "Please enter feedback question",
  });

  initializeQuestionTable({
    tbodySelector: "#poll_question_body",
    inputClass: "poll_option",
    inputName: "poll_option",
    placeholder: "Enter Poll Question",
    validationMessage: "Please enter poll Option",
  });

  function initializeQuestionTable({
    tbodySelector,
    inputClass,
    inputName,
    placeholder,
    validationMessage,
  }) {
    // Add Row
    $(document).on("click", `${tbodySelector} .add-question-row`, function () {
      let isValid = true;

      $(`${tbodySelector} .${inputClass}`).each(function () {
        if ($(this).val().trim() === "") {
          $(this).focus();

          swalError("Warning", validationMessage);

          isValid = false;

          return false;
        }
      });

      if (!isValid) {
        return;
      }

      let rowCount = $(`${tbodySelector} tr`).length + 1;

      let newRow = `
        <tr>

          <td>${rowCount}</td>

          <td>
            <input
              type="text"
              class="form-control ${inputClass}"
              name="${inputName}[]"
              placeholder="${placeholder}">
          </td>

          <td>

            <button
              type="button"
              class="btn btn-success add-question-row">
              Add
            </button>

            <button
              type="button"
              class="btn btn-danger remove-question-row">
              Delete
            </button>

          </td>

        </tr>
      `;

      $(tbodySelector).append(newRow);
    });

    // Remove Row
    $(document).on(
      "click",
      `${tbodySelector} .remove-question-row`,
      function () {
        if ($(`${tbodySelector} tr`).length > 1) {
          $(this).closest("tr").remove();

          $(`${tbodySelector} tr`).each(function (index) {
            $(this)
              .find("td:first")
              .text(index + 1);
          });
        }
      },
    );
  }

  // <------------------------------------------------------------------ Feedback Question Table End -------------------------------------------------------------->

  // <------------------------------------------------------------------ Submit Feedback Configuration Start ------------------------------------------------------>

  $("#submit_feedback_configuration").click(function (event) {
    event.preventDefault();

    // Form Values
    let feedback_config_company_name = $("#feedback_config_company_name").val();
    let feedback_config_department_name = $(
      "#feedback_config_department_name",
    ).val();
    let feedback_config_department_name2 = $(
      "#feedback_config_department_name2",
    ).val();
    let feedback_config_start_date = $("#feedback_config_start_date").val();
    let feedback_config_end_date = $("#feedback_config_end_date").val();
    let feedback_title = $("#feedback_title").val();
    let feedback_status = $("#feedback_status").val();
    let scheduled_feedback_type = $("#scheduled_feedback_type").val();
    let feedback_titles_id = $("#feedback_titles_id").val();

    // Get Dynamic Questions
    let feedback_questions = [];

    $(".feedback_question").each(function () {
      let question = $(this).val().trim();

      if (question != "") {
        feedback_questions.push(question);
      }
    });

    // Validation
    var data = [
      "feedback_config_company_name",
      "feedback_config_start_date",
      "feedback_config_end_date",
      "scheduled_feedback_type",
      "feedback_title",
      "feedback_status",
    ];

    var isValid = true;

    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);

      if (!fieldIsValid) {
        isValid = false;
      }
    });

    // Question Validation
    if (feedback_questions.length == 0) {
      swalError("Warning", "Please enter at least one feedback question");

      return false;
    }

    let departmentValid = validateMultiSelectField(
      "feedback_config_department_name",
      departmentInstances["#feedback_config_department_name"],
    );

    if (isValid && departmentValid) {
      swalConfirm(
        "Are you sure?",
        "Do you want to submit this Feedback Configuration?",
        function () {
          $.post(
            "api/scheduled_feedback_files/submit_feedback_configuration.php",
            {
              feedback_config_company_name,
              feedback_config_department_name,
              feedback_config_department_name2,
              feedback_config_start_date,
              feedback_config_end_date,
              feedback_title,
              feedback_status,
              feedback_titles_id,

              // Pass Array
              feedback_questions: feedback_questions,
            },

            function (response) {
              if (response === "2") {
                swalSuccess(
                  "Success",
                  "Feedback Configuration Added Successfully!",
                );
              } else if (response === "1") {
                swalSuccess(
                  "Success",
                  "Feedback Configuration Updated Successfully!",
                );
              } else {
                swalError("Error", "Error Occurred!");
              }

              $("#feedback_config_department_name2").val("");

              // Reset Dynamic Rows
              getFeedbackConfigurationTable(feedback_config_company_name);
              resetQuestionTable({
                tbodySelector: "#feedback_question_body",
                inputClass: "feedback_question",
                inputName: "feedback_question",
                placeholder: "Enter Feedback Question",
              });
              clearFeedbackConfigurationFields();
              getDepartmentNameDropdown({
                company_id: feedback_config_company_name,
                dropdownSelector: "#feedback_config_department_name",
                hiddenInputSelector: "#feedback_config_department_name2",
              });
            },
          );
        },
      );
    }
  });

  // <--------------------------------------------------------------------- Submit Feedback Configuration End ------------------------------------------------------------->

  // <------------------------------------------------------------------- Edit Feedback Configuration Start --------------------------------------------------------------->

  $(document).on("click", ".FeedbackConfigurationActionBtn", async function () {
    let id = $(this).attr("value");

    try {
      const response = await $.ajax({
        url: "api/scheduled_feedback_files/feedback_configuration_creation_data.php",
        type: "POST",
        data: { id: id },
        dataType: "json",
      });

      await getCompanyName();

      // Master Data
      $("#feedback_titles_id").val(id);
      $("#feedback_config_company_name").val(response.company_id);
      $("#feedback_config_department_name2").val(response.department_ids);
      await getDepartmentNameDropdown({
        company_id: response.company_id,
        dropdownSelector: "#feedback_config_department_name",
        hiddenInputSelector: "#feedback_config_department_name2",
      });
      $("#feedback_config_start_date").val(response.start_date_time);
      $("#feedback_config_end_date").val(response.end_date_time);
      $("#feedback_title").val(response.feedback_title);
      $("#feedback_status").val(response.feedback_status);

      // Clear Old Rows
      $("#feedback_question_body").empty();

      // Append Questions
      $.each(response.feedback_questions, function (index, question) {
        let row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <input type="text" class="form-control feedback_question" name="feedback_question[]" value="${question}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-success add-question-row"> Add </button>
                        <button type="button" class="btn btn-danger remove-question-row"> Delete </button>
                    </td>
                </tr>
            `;

        $("#feedback_question_body").append(row);
      });
    } catch (error) {
      console.error("Failed to fetch feedback configuration data:", error);
    }
  });

  // <------------------------------------------------------------------- Edit Feedback Configuration End -------------------------------------------------------------------->

  // <------------------------------------------------------------------- Delete Feedback Configuration Start ----------------------------------------------------------------->

  $(document).on("click", ".FeedbackConfigurationDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the Feedback Configuration?",
      getFeedbackConfigurationDelete,
      id,
    );
    return;
  });

  // <------------------------------------------------------------------- Delete Feedback Configuration End ----------------------------------------------------------------->

  // <---------------------------------------------------------------------- Submit Rating Start --------------------------------------------------------------------------->

  $("#submit_rating").click(function (event) {
    event.preventDefault();
    // Validation
    let rating_titles_id = $("#rating_titles_id").val();
    let rating_company_name = $("#rating_company_name").val();
    let rating_department_name = $("#rating_department_name").val();
    let rating_department_name2 = $("#rating_department_name2").val();
    let rating_start_date = $("#rating_start_date").val();
    let rating_end_date = $("#rating_end_date").val();
    let rating_title = $("#rating_title").val();
    let rating_description = $("#rating_description").val();
    let rating_status = $("#rating_status").val();
    let scheduled_feedback_type = $("#scheduled_feedback_type").val();

    var data = [
      "rating_company_name",
      "rating_start_date",
      "rating_end_date",
      "rating_title",
      "rating_description",
      "rating_status",
      "scheduled_feedback_type",
    ];

    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });

    let departmentValid = validateMultiSelectField(
      "rating_department_name",
      departmentInstances["#rating_department_name"],
    );

    if (isValid && departmentValid) {
      swalConfirm(
        "Are you sure?",
        "Do you want to submit this Rating?",
        function () {
          $.post(
            "api/rating_files/submit_rating.php",
            {
              rating_titles_id,
              rating_company_name,
              rating_department_name,
              rating_department_name2,
              rating_start_date,
              rating_end_date,
              rating_title,
              rating_description,
              rating_status,
            },
            function (response) {
              if (response === "2") {
                swalSuccess("Success", "Rating Added Successfully!");
              } else if (response === "1") {
                swalSuccess("Success", "Rating Updated Successfully!");
              } else {
                swalError("Error", "Error Occurred!");
              }

              // IMPORTANT
              // Destroy old datatable before reload
              $("#rating_table").DataTable().destroy();
              getRatingTable(rating_company_name);
              clearRatingFields();
              getDepartmentNameDropdown({
                company_id: rating_company_name,
                dropdownSelector: "#feedback_config_department_name",
                hiddenInputSelector: "#feedback_config_department_name2",
              });
            },
          );
        },
      );
    }
  });

  // <------------------------------------------------------------------------------ Submit Rating End ------------------------------------------------------------------->

  // <----------------------------------------------------------------------- Edit Rating Start ------------------------------------------------------------------------->

  $(document).on("click", ".ratingActionBtn", async function () {
    let id = $(this).attr("value");

    try {
      const response = await $.ajax({
        url: "api/rating_files/rating_creation_data.php",
        type: "POST",
        data: { id: id },
        dataType: "json",
      });

      await getCompanyName();

      // Master Data
      $("#rating_titles_id").val(id);
      $("#rating_company_name").val(response.company_id);
      $("#rating_department_name2").val(response.department_ids);
      await getDepartmentNameDropdown({
        company_id: response.company_id,
        dropdownSelector: "#rating_department_name",
        hiddenInputSelector: "#rating_department_name2",
      });
      $("#rating_start_date").val(response.start_date_time);
      $("#rating_end_date").val(response.end_date_time);
      $("#rating_title").val(response.rating_title);
      $("#rating_description").val(response.rating_description);
      $("#rating_status").val(response.rating_status);
    } catch (error) {
      console.error("Failed to fetch feedback configuration data:", error);
    }
  });

  // <---------------------------------------------------------------------------- Edit Rating End ------------------------------------------------------------------------>

  // <------------------------------------------------------------------------ Delete Rating Start ----------------------------------------------------------------------->

  $(document).on("click", ".ratingDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the Rating?",
      getRatingDelete,
      id,
    );
    return;
  });

  // <-------------------------------------------------------------------------- Delete Rating End ------------------------------------------------------------------------>

  // <----------------------------------------------------------------------- Submit Poll Start --------------------------------------------------------------------------->

  $("#submit_poll").click(function (event) {
    event.preventDefault();

    // Form Values
    let poll_company_name = $("#poll_company_name").val();
    let poll_department_name = $("#poll_department_name").val();
    let poll_department_name2 = $("#poll_department_name2").val();
    let poll_start_date = $("#poll_start_date").val();
    let poll_end_date = $("#poll_end_date").val();
    let poll_title = $("#poll_title").val();
    let poll_description = $("#poll_description").val();
    let poll_status = $("#poll_status").val();
    let scheduled_feedback_type = $("#scheduled_feedback_type").val();
    let poll_titles_id = $("#poll_titles_id").val();

    // Get Dynamic Questions
    let poll_options = [];

    $(".poll_option").each(function () {
      let option = $(this).val().trim();

      if (option != "") {
        poll_options.push(option);
      }
    });

    // Validation
    var data = [
      "poll_company_name",
      "poll_start_date",
      "poll_end_date",
      "poll_title",
      "poll_description",
      "poll_status",
      "scheduled_feedback_type",
    ];

    var isValid = true;

    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);

      if (!fieldIsValid) {
        isValid = false;
      }
    });

    // Question Validation
    if (poll_options.length == 0) {
      swalError("Warning", "Please enter at least one pole option!");

      return false;
    }

    let departmentValid = validateMultiSelectField(
      "poll_department_name",
      departmentInstances["#poll_department_name"],
    );

    if (isValid && departmentValid) {
      swalConfirm(
        "Are you sure?",
        "Do you want to submit this Poll?",
        function () {
          $.post(
            "api/poll_files/submit_poll.php",
            {
              poll_company_name,
              poll_department_name,
              poll_department_name2,
              poll_start_date,
              poll_end_date,
              poll_title,
              poll_description,
              poll_status,
              poll_titles_id,

              // Pass Array
              poll_options: poll_options,
            },

            function (response) {
              if (response === "2") {
                swalSuccess("Success", "Poll Added Successfully!");
              } else if (response === "1") {
                swalSuccess("Success", "Poll Updated Successfully!");
              } else {
                swalError("Error", "Error Occurred!");
              }

              $("#poll_department_name2").val("");

              // Reset Dynamic Rows
              getPollTable(poll_company_name);
              resetQuestionTable({
                tbodySelector: "#poll_question_body",
                inputClass: "poll_option",
                inputName: "poll_option",
                placeholder: "Enter Poll Option",
              });
              clearPollFields();
              getDepartmentNameDropdown({
                company_id: poll_company_name,
                dropdownSelector: "#feedback_config_department_name",
                hiddenInputSelector: "#feedback_config_department_name2",
              });
            },
          );
        },
      );
    }
  });

  // <-------------------------------------------------------------------------- Submit Poll End -------------------------------------------------------------------------->

  // <------------------------------------------------------------------- Edit Poll Start -------------------------------------------------------------------------------->

  $(document).on("click", ".pollActionBtn", async function () {
    let id = $(this).attr("value");

    try {
      const response = await $.ajax({
        url: "api/poll_files/poll_creation_data.php",
        type: "POST",
        data: { id: id },
        dataType: "json",
      });

      await getCompanyName();

      // Master Data
      $("#poll_titles_id").val(id);
      $("#poll_company_name").val(response.company_id);
      $("#poll_department_name2").val(response.department_ids);
      await getDepartmentNameDropdown({
        company_id: response.company_id,
        dropdownSelector: "#poll_department_name",
        hiddenInputSelector: "#poll_department_name2",
      });
      $("#poll_start_date").val(response.start_date_time);
      $("#poll_end_date").val(response.end_date_time);
      $("#poll_title").val(response.poll_title);
      $("#poll_description").val(response.poll_description);
      $("#poll_status").val(response.poll_status);

      // Clear Old Rows
      $("#poll_question_body").empty();

      // Append Questions
      $.each(response.poll_options, function (index, option) {
        let row = `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <input type="text" class="form-control poll_option" name="poll_option[]" value="${option}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-success add-question-row"> Add </button>
                        <button type="button" class="btn btn-danger remove-question-row"> Delete </button>
                    </td>
                </tr>
            `;

        $("#poll_question_body").append(row);
      });
    } catch (error) {
      console.error("Failed to fetch feedback configuration data:", error);
    }
  });

  // <--------------------------------------------------------------------- Edit Poll End --------------------------------------------------------------------------------->

  // <------------------------------------------------------------------- Delete Feedback Configuration Start ----------------------------------------------------------------->

  $(document).on("click", ".pollDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm("Delete", "Do you want to Delete the Poll?", getPollDelete, id);
    return;
  });

  // <------------------------------------------------------------------- Delete Feedback Configuration End ----------------------------------------------------------------->
});

// <------------------------------------------------------------------------------------ Get Company Name Start ------------------------------------------------------------>

async function getCompanyName() {
  return new Promise((resolve, reject) => {
    $.post(
      "api/branch_creation/getCompanyName.php",
      {},

      function (response) {
        let dropdown = $(
          "#general_company_name, #feedback_config_company_name, #rating_company_name, #poll_company_name",
        );
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

// <-------------------------------------------------------------------- Get Company Name End --------------------------------------------------------------------------->

// <------------------------------------------------------------------ Get General Feedback Table Start ------------------------------------------------------------------>

function getGeneralFeedbackTable(company_id) {
  $.post(
    "api/general_feedback_files/general_feedback_creation_list.php",
    { company_id },
    function (response) {
      var columnMapping = ["sno", "feedback_name", "status", "action"];
      appendDataToTable("#general_feedback_table", response, columnMapping);
      setdtable("#general_feedback_table", "General Feedback Creation List");
    },
    "json",
  );
}

// <-------------------------------------------------------------------- Get General Feedback Table End ------------------------------------------------------------------>

// <-------------------------------------------------------------------- Get General Feedback Table Delete Start --------------------------------------------------------->

function getgeneralFeedbackDelete(id) {
  let company_id = $("#general_company_name").val();

  $.post(
    "api/general_feedback_files/delete_general_feedback.php",
    { id },
    function (response) {
      if (response == "1") {
        swalSuccess("Success", "General Feedback Deleted Successfully!");
        getGeneralFeedbackTable(company_id);
        clearFields();
      } else if (response == "2") {
        swalError("Warning", "General Feedback already answered by Employee!");
      } else {
        swalError("Warning", "Error occur While Delete CTC Info.");
      }
    },
    "json",
  );
}

// <----------------------------------------------------------------------- Get General Feedback Table Delete End ------------------------------------------------------->

// <-------------------------------------------------------------------- General Feedback Input Clear Fields Start ------------------------------------------------------->

function clearFields() {
  $("#feedback_name").val("");
  $("#status").val("");
  $("#general_feedback_id").val("");
}

// <--------------------------------------------------------------------- General Feedback Input Clear Fields End -------------------------------------------------------->

// <--------------------------------------------------------------------- Get Department Name Dropdown Start ------------------------------------------------------------->

async function getDepartmentNameDropdown({
  company_id,
  dropdownSelector,
  hiddenInputSelector,
}) {
  try {
    const selectedValue = $(hiddenInputSelector).val();

    const response = await $.ajax({
      url: "api/company_creation_files/get_department_name_dropdown.php",
      type: "POST",
      data: {
        company_id,
        screen: "feedback_screen",
      },
      dataType: "json",
    });

    const instance = departmentInstances[dropdownSelector];

    instance.clearStore();
    instance.removeActiveItems();

    const selectedIds = selectedValue ? selectedValue.split(",") : [];

    const items = response.map((val) => ({
      value: val.id,
      label: val.department_name,
      selected: selectedIds.includes(val.id.toString()),
      disabled: val.disabled && !selectedIds.includes(val.id.toString()),
    }));

    instance.setChoices(items, "value", "label", true);
  } catch (err) {
    console.error("Error loading department dropdown:", err);
  }
}

// <--------------------------------------------------------------------- Get Department Name Dropdown End ------------------------------------------------------------>

// <------------------------------------------------------------------ Get Feedback Configuration Table Start --------------------------------------------------------->

function getFeedbackConfigurationTable(company_id) {
  $.post(
    "api/scheduled_feedback_files/feedback_configuration_creation_list.php",
    { company_id },
    function (response) {
      var columnMapping = [
        "sno",
        "feedback_title",
        "start_date_time",
        "end_date_time",
        "department_name",
        "feedback_status",
        "action",
      ];
      appendDataToTable(
        "#feedback_configuration_table",
        response,
        columnMapping,
      );
      setdtable(
        "#feedback_configuration_table",
        "Feedback Configuration Creation List",
      );
    },
    "json",
  );
}

// <-------------------------------------------------------------------- Get Feedback Configuration Table End ---------------------------------------------------------->

// <-------------------------------------------------------------------- Delete Feedback Configuration Table Start ------------------------------------------------------>

function getFeedbackConfigurationDelete(id) {
  let company_id = $("#feedback_config_company_name").val();
  $.post(
    "api/scheduled_feedback_files/delete_feedback_configuration.php",
    { id },
    function (response) {
      if (response == "1") {
        swalSuccess("Success", "Feedback Configuration Delete Successfully!");
        getFeedbackConfigurationTable(company_id);
        clearFeedbackConfigurationFields();
      } else if (response == "2") {
        swalError(
          "Warning",
          "Scheduled Feedback already answered by Employee!",
        );
      } else {
        swalError("Warning", "Error occur While Delete Team Info.");
      }
    },
    "json",
  );
}

// <-------------------------------------------------------------------- Delete Feedback Configuration Table End -------------------------------------------------------->

// <-------------------------------------------------------------- Feedback Configuration Input Clear Fields Start --------------------------------------------------->

function clearFeedbackConfigurationFields() {
  const instance = departmentInstances["#feedback_config_department_name"];

  instance.clearChoices();
  instance.removeActiveItems();

  $("#feedback_config_start_date").val("");
  $("#feedback_config_end_date").val("");
  $("#feedback_title").val("");
  $("#feedback_status").val("");
  $("#feedback_titles_id").val("");
}

// <------------------------------------------------------------ Feedback Configuration Input Clear Fields End -------------------------------------------------------->

// <------------------------------------------------------------------------- Get Rating Table Start ------------------------------------------------------------------>

function getRatingTable(company_id) {
  $.post(
    "api/rating_files/rating_creation_list.php",
    { company_id },
    function (response) {
      var columnMapping = [
        "sno",
        "rating_title",
        "start_date_time",
        "end_date_time",
        "department_name",
        "rating_status",
        "action",
      ];
      appendDataToTable("#rating_table", response, columnMapping);
      setdtable("#rating_table", "Rating Creation List");
    },
    "json",
  );
}

// <------------------------------------------------------------------------------ Get Rating Table End --------------------------------------------------------------->

// <--------------------------------------------------------------------------- Delete Rating Table Start ------------------------------------------------------------->

function getRatingDelete(id) {
  let company_id = $("#rating_company_name").val();
  $.post(
    "api/rating_files/delete_rating.php",
    { id },
    function (response) {
      if (response == "1") {
        swalSuccess("Success", "Rating Delete Successfully!");
        getRatingTable(company_id);
        clearRatingFields();
      } else if (response == "2") {
        swalError("Warning", "Rating already answered by Employee!");
      } else {
        swalError("Warning", "Error occur While Delete Team Info.");
      }
    },
    "json",
  );
}

// <-------------------------------------------------------------------------- Delete Rating Table End --------------------------------------------------------------->

// <-------------------------------------------------------------------------- Rating Input Clear Fields Start ------------------------------------------------------->

function clearRatingFields() {
  const instance = departmentInstances["#rating_department_name"];

  instance.clearChoices();
  instance.removeActiveItems();

  $("#rating_start_date").val("");
  $("#rating_end_date").val("");
  $("#rating_title").val("");
  $("#rating_description").val("");
  $("#rating_status").val("");
  $("#rating_titles_id").val("");
}

// <----------------------------------------------------------------------- Rating Input Clear Fields End --------------------------------------------------------------->

// <-------------------------------------------------------------------------- Get Poll Table Start --------------------------------------------------------------------->

function getPollTable(company_id) {
  $.post(
    "api/poll_files/poll_creation_list.php",
    { company_id },
    function (response) {
      var columnMapping = [
        "sno",
        "poll_title",
        "start_date_time",
        "end_date_time",
        "department_name",
        "poll_status",
        "action",
      ];
      appendDataToTable("#poll_table", response, columnMapping);
      setdtable("#poll_table", "Poll Creation List");
    },
    "json",
  );
}

// <-------------------------------------------------------------------------- Get Poll Table End ----------------------------------------------------------------------->

// <----------------------------------------------------------------------- Delete Poll Table Start --------------------------------------------------------------------->

function getPollDelete(id) {
  let company_id = $("#poll_company_name").val();
  $.post(
    "api/poll_files/delete_poll.php",
    { id },
    function (response) {
      if (response == "1") {
        swalSuccess("Success", "Poll Delete Successfully!");
        getPollTable(company_id);
        clearPollFields();
      } else if (response == "2") {
        swalError("Warning", "Poll already answered by Employee!");
      } else {
        swalError("Warning", "Error occur While Delete Team Info.");
      }
    },
    "json",
  );
}

// <---------------------------------------------------------------------------- Delete Poll Table End ---------------------------------------------------------------->

// <------------------------------------------------------------------------------ Poll Input Clear Fields Start ------------------------------------------------------->

function clearPollFields() {
  const instance = departmentInstances["#poll_department_name"];

  instance.clearChoices();
  instance.removeActiveItems();

  $("#poll_start_date").val("");
  $("#poll_end_date").val("");
  $("#poll_title").val("");
  $("#poll_description").val("");
  $("#poll_status").val("");
  $("#poll_titles_id").val("");
}

// <----------------------------------------------------------------------- Poll Input Clear Fields End ----------------------------------------------------------------->

// <----------------------------------------------------------------------- Reset Questions Start ----------------------------------------------------------------------->

resetQuestionTable({
  tbodySelector: "#feedback_question_body",
  inputClass: "feedback_question",
  inputName: "feedback_question",
  placeholder: "Enter Feedback Question",
});

resetQuestionTable({
  tbodySelector: "#poll_question_body",
  inputClass: "poll_option",
  inputName: "poll_option",
  placeholder: "Enter Poll Option",
});

function resetQuestionTable({
  tbodySelector,
  inputClass,
  inputName,
  placeholder,
}) {
  $(tbodySelector).html(`
    <tr>

      <td>1</td>

      <td>
        <input
          type="text"
          class="form-control ${inputClass}"
          name="${inputName}[]"
          placeholder="${placeholder}">
      </td>

      <td>
        <button
          type="button"
          class="btn btn-success add-question-row">
          Add
        </button>

        <button
          type="button"
          class="btn btn-danger remove-question-row">
          Delete
        </button>
      </td>

    </tr>
  `);
}

// <------------------------------------------------------------------------- Reset Questions End ------------------------------------------------------------------->
