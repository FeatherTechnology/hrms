$(document).ready(function () {
  $("#from_date").change(function () {
    let from_date = $("#from_date").val();
    let to_date = $("#to_date").val();
    if (from_date > to_date) {
      $("#to_date").val("");
    }
    $("#to_date").attr("min", from_date);
  });

  $("#company_id").on("change", function () {
    let company_id = $("#company_id").val();
    getBranchName(company_id);
    getDepartmentName(company_id);
  });

  $("#location_access_btn").click(function (event) {
    event.preventDefault();
    let from_date = $("#from_date").val();
    let to_date = $("#to_date").val();
    let company_id = $("#company_id").val();
    let branch_id = $("#branch_id").val();
    let department_id = $("#department_id").val();

    if (
      from_date != "" &&
      to_date != "" &&
      company_id != "" &&
      branch_id != "" &&
      department_id != ""
    ) {
      let data = {
        from_date: from_date,
        to_date: to_date,
        company_id: company_id,
        branch_id: branch_id,
        department_id: department_id,
      };

      serverSideTable(
        "#location_access_report_table",
        data,
        "api/report_files/get_location_access_report.php",
      );
    } else {
      swalError("Warning", "Please Fill The Required Fields!");
    }
  });
});

$(function () {
  getCompanyName();
});

/* --- Get Company Name --- */
async function getCompanyName() {
  return new Promise((resolve, reject) => {
    $.post(
      "api/attendance_files/get_company_list.php",
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

/* --- Get Branch Name --- */
async function getBranchName(company_id) {
  return new Promise((resolve, reject) => {
    $.post(
      "api/location_creation_files/getBranchName.php",
      { company_id: company_id },

      function (response) {
        let dropdown = $("#branch_id");
        dropdown.empty();
        dropdown.append('<option value="">Select Branch Name</option>');
        $.each(response, function (index, item) {
          dropdown.append(
            `<option value="${item.id}">${item.branch_name}</option>`,
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
