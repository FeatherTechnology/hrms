$(document).ready(function () {
  $("#from_date").change(function () {
    let from_date = $("#from_date").val();
    let to_date = $("#to_date").val();
    if (from_date > to_date) {
      $("#to_date").val("");
    }
    $("#to_date").attr("min", from_date);
  });

  $("#feedback_type").on("change", function () {
    let feedback_type = $(this).val();

    if (feedback_type == "1") {
      $("#department_id").hide();
    } else {
      $("#department_id").show();
    }

    if (feedback_type == "2") {
      $("#question").show();
    } else {
      $("#question").hide();
    }

    getCompanyName();
  });

  $("#company_id").on("change", function () {
    let company_id = $("#company_id").val();
    getDepartmentName(company_id);
    let feedback_type = $("#feedback_type").val();
    getTitles(feedback_type, company_id);
  });

  $("#department_id").on("change", function () {
    let company_id = $("#company_id").val();
    let department_id = $("#department_id").val();
    let feedback_type = $("#feedback_type").val();
    getTitles(feedback_type, company_id, department_id);
  });

  $("#company_id, #department_id, #feedback_type").on("change", function () {
    $("#title").empty().append("<option value=''>Select Title</option>");
    $("#question").empty().append("<option value=''>Select Question</option>");
  });

  $("#title").on("change", function () {
    let title = $(this).val();
    getQuestions(title);
  });

  $("#feedback_btn").click(function () {
    let from_date = $("#from_date").val();
    let toDate = $("#to_date").val();
    let company_id = $("#company_id").val();
    let department_id = $("#department_id").val();
    let feedback_type = $("#feedback_type").val();
    let title = $("#title").val();
    let question = $("#question").val();

    if (
      from_date == "" ||
      toDate == "" ||
      company_id == "" ||
      feedback_type == "" ||
      title == "" ||
      (feedback_type != "1" && department_id == "") ||
      (feedback_type == "2" && question == "")
    ) {
      swalError("Warning", "Kindly fill the required fields.");
      return false;
    }

    let data = {
      from_date: from_date,
      to_date: toDate,
      company_id: company_id,
      department_id: department_id,
      feedback_type: feedback_type,
      title: title,
      question: question,
    };

    // Destroy existing DataTable instances
    [
      "#general_feedback_table",
      "#feedback_configuration_table",
      "#rating_table",
      "#poll_table",
    ].forEach(function (tableId) {
      if ($.fn.DataTable.isDataTable(tableId)) {
        $(tableId).DataTable().clear().destroy();
      }
    });

    const reportConfig = {
      1: {
        table: "#general_feedback_table",
        url: "api/report_files/get_general_feedback_report.php",
        header: "General Feedback Report",
        excelTitle: "General Feedback Report",
      },
      2: {
        table: "#feedback_configuration_table",
        url: "api/report_files/get_feedback_configuration_report.php",
        header: "Feedback Configuration Report",
        excelTitle: "Feedback Configuration Report",
      },
      3: {
        table: "#rating_table",
        url: "api/report_files/get_rating_report.php",
        header: "Rating Report",
        excelTitle: "Rating Report",
      },
      4: {
        table: "#poll_table",
        url: "api/report_files/get_poll_report.php",
        header: "Poll Report",
        excelTitle: "Poll Report",
      },
    };

    const config = reportConfig[feedback_type];

    if (config) {
      // Hide all tables
      $(
        "#general_feedback_table, #feedback_configuration_table, #rating_table, #poll_table",
      ).hide();

      // Show selected table
      $(config.table).show();

      // Change card header
      $("#report_header").text(config.header);

      // Load DataTable
      serverSideTable(config.table, data, config.url, config.excelTitle);
    }
  });
});

/* --- Get Company Name --- */
async function getCompanyName() {
  return new Promise((resolve, reject) => {
    $.post(
      "api/branch_creation/getCompanyName.php",
      {},

      function (response) {
        let dropdown = $("#company_id");
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
async function getDepartmentName(company_name) {
  return new Promise((resolve, reject) => {
    $.post(
      "api/team_creation_files/getDepartmentName.php",
      { company_name: company_name },

      function (response) {
        let dropdown = $("#department_id");
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

/* --- Get Title Name --- */
function getTitles(feedback_type, company_id, department_id) {
  $.post(
    "api/report_files/get_feedback_titles.php",
    {
      feedback_type: feedback_type,
      company_id: company_id,
      department_id: department_id,
    },
    function (response) {
      $("#title").empty().append("<option value=''>Select Title</option>");

      $.each(response, function (index, val) {
        $("#title").append(
          "<option value='" + val.id + "'>" + val.title + "</option>",
        );
      });
    },
    "json",
  );
}

/* --- Get Question --- */
function getQuestions(title_id) {
  $("#question").empty().append("<option value=''>Select Question</option>");

  if (!title_id) {
    return;
  }

  $.post(
    "api/report_files/get_questions.php",
    { title_id: title_id },
    function (response) {
      $.each(response, function (index, val) {
        $("#question").append(
          "<option value='" +
            val.id +
            "'>" +
            val.feedback_questions +
            "</option>",
        );
      });
    },
    "json",
  );
}
