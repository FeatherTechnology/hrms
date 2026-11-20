$(document).ready(function () {
  $("#company_id").on("change", function () {
    let company_id = $(this).val();
    if (company_id) {
      getBranchList(company_id, "#branch_id", "");
    } else {
      $("#branch_id").empty().append('<option value="">Select Branch</option>');
    }
  });

  $("#staff_btn").click(function (event) {
    event.preventDefault();
    let company_id = $("#company_id").val();
    let branch_id = $("#branch_id").val();
    let status = $("#status").val();

    if ((company_id != "" || branch_id != "") && status !="") {
      let data = {
        company_id: company_id,
        branch_id: branch_id,
        status: status,
      };

      getStaffLoanList(company_id,branch_id,status);
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
      "api/attendance_files/get_company_list.php",
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

function getStaffLoanList(company_id,branch_id,status) {
  $.post(
    "api/report_files/get_staff_loan_advance_report.php",{company_id,branch_id,status},
    function (response) {
      var columnMapping = [
        "sno",
        "company_name",
        "branch_name",
        "staff_name",
        "loan_amount",
        "intrest_rate",
        "due_period",
        "total_intrest",
        "due_amount",
        "due_start_date",
        "due_end_date",
        "balance_amount",
      ];
      appendDataToTable("#staff_loan_table", response, columnMapping);
      setdtable("#staff_loan_table", "Staff Loan Report");
    },
    "json",
  );
}