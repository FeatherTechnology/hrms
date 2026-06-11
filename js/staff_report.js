$(document).ready(function () {
  $("#company_id").on("change", function () {
    let company_id = $(this).val();
    if (company_id) {
      getDepartmentList(company_id, "#department_id", "");
      getBranchList(company_id, "#branch_id", "");
    } else {
      $("#department_id")
        .empty()
        .append('<option value="">Select Department</option>');
      $("#branch_id").empty().append('<option value="">Select Branch</option>');
    }
  });

  $("#staff_btn").click(function (event) {
    event.preventDefault();
    let company_id = $("#company_id").val();
    let branch_id = $("#branch_id").val();
    let department_id = $("#department_id").val();

    if (company_id != "" || branch_id != "" || department_id != "") {
      let data = {
        company_id: company_id,
        branch_id: branch_id,
        department_id: department_id,
      };

      serverSideTable(
        "#staff_report_table",
        data,
        "api/report_files/get_staff_report.php",
      );
    } else {
      swalError("Please Fill Any One Field!", "Select at least one filter.");
    }
  });
});

$(function () {
  getCompanyName("#company_id");
});

async function getCompanyName(selector) {
  return new Promise((resolve, reject) => {
    $.post(
      "api/branch_creation/getCompanyName.php",
      {},

      function (response) {
        let dropdown = $(selector);
        dropdown.empty();
        dropdown.append('<option value="">Select Company</option>');
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

async function getDepartmentList(company_id, selector, selected_dept = "") {
  try {
    const response = await $.ajax({
      url: "api/staff_creation/company_mapped_department.php",
      type: "POST",
      dataType: "json",
      data: {
        company_id: company_id,
        selected_dept: selected_dept,
      },
    });

    let deptOption = '<option value="">Select Department</option>';

    $.each(response, function (index, val) {
      deptOption += `<option value="${val.id}">${val.department_name}</option>`;
    });

    $(selector).empty().append(deptOption);
  } catch (error) {
    console.error(error);
  }
}

async function getBranchList(company_id, selector) {
  try {
    const response = await $.ajax({
      url: "api/staff_creation/company_mapped_branches.php",
      data: { company_id: company_id },
      type: "POST",
      dataType: "json",
    });

    let appendBranchOption = '<option value="">Select Branch</option>';

    $.each(response, function (index, val) {
      appendBranchOption += `
                <option value="${val.id}">
                    ${val.branch_name}
                </option>
            `;
    });

    $(selector).empty().append(appendBranchOption);
  } catch (error) {
    console.error("Error loading branch list:", error);
  }
}
