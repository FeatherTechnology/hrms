$(document).ready(function () {
  // when i click the back button
  $(".add_staff_loan").click(function () {
    getCompanyName("#company_name");
    $(".add_staff_loan,.staff_loan_table_content").hide();
    $("#staff_loan_content,.back_to_list").show();
    $(".deduction_card").hide();
    $(".user_info_card,.loan_calculation_card").find("input, select").val("");
    $("#loan_amount,#int_rate,#due_period,#due_start_date").prop("readonly", false)
    $("#company_name,#branch_name,#staff_name,#type").prop("disabled", false);
  });

  $(".back_to_list").click(function () {
    getStaffLoanList();
    $(".add_staff_loan,.staff_loan_table_content").show();
    $("#staff_loan_content,.back_to_list").hide();
  });

  $(document).on("click", ".staffLoanActionBtn", function () {
    let dueStartDate = $(this).attr("data-id");

    $(".add_staff_loan,.staff_loan_table_content").hide();
    $("#staff_loan_content,.back_to_list").show();
    getStaffLoanDetails($(this).attr("value"));

  });

  $("#deduction_amount").on("input change", function () {
    var deduction_amount = Number($(this).val());
    var bal_amount = Number($("#bal_amount").val());

    if ($(this).val() !== "" && deduction_amount > bal_amount) {
      swalError("Validation", "Deduction amount cannot exceed the balance.");
      $(this).val("");
    }
  });

  $(document).on("click", ".staffLoanDeleteBtn", function () {
    let dueStartDate = $(this).attr("data-id");
    // Current month and year
    let currentDate = new Date();

    let currentMonth = currentDate.getMonth() + 1;
    let currentYear = currentDate.getFullYear();

    // Date format: dd-mm-yyyy
    let dateParts = dueStartDate.split("-");

    let dueMonth = parseInt(dateParts[1]);
    let dueYear = parseInt(dateParts[2]);

    if (dueMonth >= currentMonth && dueYear >= currentYear) {
      deleteStaffLoan($(this).attr("value"));
    } else {
      // Date mismatched
      swalError("Warning", "Loan due started. Deletion not allowed...");
    }
  });

  $("#submit_staff_loan").click(function (event) {
    event.preventDefault();

    var company_name = $("#company_name").val();
    var branch_name = $("#branch_name").val();
    var staff_loan_id = $("#staff_loan_id").val();
    var staff_profile_id = $("#staff_name").val();
    var loan_amount = $("#loan_amount").val();
    var int_rate = $("#int_rate").val();
    var due_period = $("#due_period").val();
    var total_intrest = $("#total_intrest").val();
    var due_amount = $("#due_amount").val();
    var due_start_date = $("#due_start_date").val();
    var deduction_month = $("#deduction_month").val();
    var deduction_amount = $("#deduction_amount").val();
    var bal_amount = $("#bal_amount").val();
    var type = $("#type").val();
    var due_end_date = "";

    if (due_start_date) {
      due_start_date += "-01";
    }

    if ($("#due_end_date").val()) {
      var endDate = new Date($("#due_end_date").val() + " 1");
      due_end_date =
        endDate.getFullYear() + "-" +
        String(endDate.getMonth() + 1).padStart(2, "0") + "-01";
    }

    if (staff_loan_id !== "") {
      if (!deduction_month) {
        swalError("Validation", "Please select the deduction month.");
        return;
      }

      if (
        deduction_amount === "" ||
        isNaN(deduction_amount) ||
        Number(deduction_amount) < 0
      ) {
        swalError("Validation", "Please enter a valid deduction amount.");
        return;
      }
    } else {
      var fields = [
        "company_name",
        "branch_name",
        "staff_name",
        "loan_amount",
        "int_rate",
        "due_period",
        "due_start_date",
        "type"
      ];

      var isValid = true;

      fields.forEach(function (field) {
        if (!validateField($("#" + field).val(), field)) {
          isValid = false;
        }
      });

      if (!isValid) {
        return;
      }
    }

    swalConfirm(
      "Are you sure?",
      staff_loan_id !== ""
        ? "Do you want to submit this deduction?"
        : type == "1"
          ? "Do you want to submit Staff Loan?"
          : "Do you want to submit Salary Advance?",
      function () {
        $.post(
          "api/staff_loan_&_advance/submit_staff_loan.php",
          {
            company_name: company_name,
            branch_name: branch_name,
            staff_profile_id: staff_profile_id,
            loan_amount: loan_amount,
            int_rate: int_rate,
            due_period: due_period,
            total_intrest: total_intrest,
            due_amount: due_amount,
            due_start_date: due_start_date,
            due_end_date: due_end_date,
            staff_loan_id: staff_loan_id,
            type: type,
            deduction_month: deduction_month,
            deduction_amount: deduction_amount,
            bal_amount: bal_amount
          },
          function (response) {
            if (response == "1") {
              swalSuccess(
                "Success",
                staff_loan_id !== ""
                  ? "Deduction Added Successfully!"
                  : "Staff Loan Updated Successfully!"
              );
            } else if (response == "2") {
              swalSuccess("Success", "Staff Loan Added Successfully!");
            } else if (response == "3") {
              swalError("Validation", "Deduction already exists for this month.");
              return;
            } else {
              swalError("Error", "Error Occurred!");
              return;
            }

            $(".add_staff_loan,.staff_loan_table_content").show();
            $("#staff_loan_content,.back_to_list").hide();
            getStaffLoanList();
          },
          "json"
        );
      }
    );
  });

  $("#company_name").on("change", function () {
    var cmpy_id = $(this).val();
    getBranchList(cmpy_id)

  });

  $("#branch_name").on("change", function () {
    var company_id = $('#company_name').val();
    var branch_id = $(this).val();
    getStaffName(company_id, branch_id)

  });

  $("#staff_name").on("change", function () {
    getStaffInfo();
  });

  // Loan Amount change
  $("#loan_amount").on("input", function () {
    let loanAmount = $(this).val();
    let intRate = $("#int_rate").val();
    let duePeriod = $("#due_period").val();

    if (loanAmount !== "" && intRate !== "" && duePeriod !== "") {
      calculateDueAmount();
    } else {
      $("#due_amount").val("");
    }
  });

  // Interest Rate change
  $("#int_rate").on("input", function () {
    let intRate = $(this).val();
    let loanAmount = $("#loan_amount").val();

    if (intRate !== "" && loanAmount === "") {
      swalError("Warning", "Loan Amount Required");

      $(this).val("");
      $("#due_amount").val("");
      $("#loan_amount").focus();
      return;
    }

    let duePeriod = $("#due_period").val();

    if (loanAmount !== "" && intRate !== "" && duePeriod !== "") {
      calculateDueAmount();
    }
  });

  // Due Period change
  $("#due_period").on("input", function () {
    let duePeriod = $(this).val();
    let loanAmount = $("#loan_amount").val();
    let intRate = $("#int_rate").val();

    if (duePeriod !== "" && loanAmount === "") {
      swalError("Warning", "Loan Amount Required");

      $(this).val("");
      $("#due_amount").val("");
      $("#loan_amount").focus();

      return;
    }

    if (duePeriod !== "" && intRate === "") {
      swalError("Warning", "Interest Rate Required");

      $(this).val("");
      $("#due_amount").val("");
      $("#int_rate").focus();

      return;
    }

    if (loanAmount !== "" && intRate !== "" && duePeriod !== "") {
      calculateDueAmount();
    }
  });

  $("#due_start_date, #due_period").on("change input", function () {

    let startDate = $("#due_start_date").val();
    let duePeriod = parseInt($("#due_period").val());

    if (startDate && duePeriod > 0) {

      let date = new Date(startDate + "-01");

      // Starting month is counted as month 1
      date.setMonth(date.getMonth() + duePeriod - 1);

      $("#due_end_date").val(
        date.toLocaleDateString("en-US", {
          month: "long",
          year: "numeric"
        })
      );

    } else {
      $("#due_end_date").val("");
    }
  });

  $("#type").on("change", function () {
    let type = $(this).val();

    if (type == "1") {
      $("#amount_label").text("Loan Amount");
    } else if (type == "2") {
      $("#amount_label").text("Advance Amount");
    }
  });

});
// document end

$(function () {
  getStaffLoanList();
});

// Function Start
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

/* --- Get Staff Name --- */
async function getStaffName(company_id, branch_id) {
  try {
    const response = await $.ajax({
      url: "api/attendance_ot_monitor_chart_files/get_staff_name.php",
      type: "POST",
      data: { company_id, branch_id },
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


function getStaffInfo() {
  let id = $("#staff_name").val();

  $.post(
    "api/user_creation_files/getStaffInfo.php",
    { id: id },

    function (response) {
      if (response.length > 0) {
        $("#staff_id").val(response[0].staff_id);
        $("#department").val(response[0].department_name);
        $("#team").val(response[0].team_name);
        $("#designation").val(response[0].designation);
      } else {
        $("#staff_id").val("");
        $("#department").val("");
        $("#team").val("");
        $("#designation").val("");

        swalError("Warning", "No Staff Info Found");
      }
    },
    "json",
  );
}
function getStaffLoanList() {
  $.post(
    "api/staff_loan_&_advance/get_staff_loan_list.php",
    function (response) {
      var columnMapping = [
        "sno",
        "company_name",
        "staff_name",
        "loan_amount",
        "intrest_rate",
        "due_period",
        "total_intrest",
        "due_amount",
        "due_start_date",
        "due_end_date",
        "action",
      ];
      appendDataToTable("#staff_loan_table", response, columnMapping);
      setdtable("#staff_loan_table", "Staff Loan List");
    },
    "json",
  );
}

function calculateDueAmount() {
  let loanAmount = parseFloat($("#loan_amount").val()) || 0;
  let intRate = parseFloat($("#int_rate").val()) || 0;
  let duePeriod = parseInt($("#due_period").val()) || 0;

  if (loanAmount > 0 && intRate >= 0 && duePeriod > 0) {
    let interest = loanAmount * (intRate / 100);
    let dueAmount = (loanAmount + interest) / duePeriod;
    dueAmount = Math.ceil(dueAmount - 0.5);

    $("#due_amount").val(dueAmount.toFixed(2));
    $("#total_intrest").val(interest.toFixed(2));
  } else {
    $("#due_amount").val("");
    $("#total_intrest").val("");
  }
}

async function getStaffLoanDetails(id) {
  try {
    const response = await $.ajax({
      url: "api/staff_loan_&_advance/getStaffDetails.php",
      data: { id: id },
      type: "POST",
      dataType: "json"
    });

    if (response && response.id) {
      let data = response;
      $("#staff_loan_id").val(data.id);
      $(".add_staff_loan,.staff_loan_table_content").hide();
      $("#staff_loan_content,.back_to_list,.deduction_card").show();
      
      // 1. Load companies, then set the value and disable
      await getCompanyName("#company_name");
      $("#company_name").val(String(data.company_id)).prop("disabled", true);
      
      // 2. Load branches using company_id, then set the value and disable
      await getBranchList(data.company_id);
      $("#branch_name").val(String(data.branch_id)).prop("disabled", true);
      
      // 3. Load staff names, then set the value and call info
      if (typeof getStaffName === 'function') {
        await getStaffName(data.company_id, data.branch_id);
        $("#staff_name").val(String(data.staff_id)).prop("disabled", true);
        getStaffInfo();
      }

      $("#type").val(data.type).trigger("change").prop("disabled", true);
      $("#loan_amount").val(data.loan_amount).prop("readonly", true);
      $("#int_rate").val(data.intrest_rate).prop("readonly", true);
      $("#due_period").val(data.due_period).prop("readonly", true);
      $("#total_intrest").val(data.total_intrest).prop("readonly", true);
      $("#due_amount").val(data.due_amount).prop("readonly", true);
      $("#deduction_amount").val(data.due_amount);
      $("#bal_amount").val(data.balance_amount);
      $("#due_start_date")
        .val(data.due_start_date ? data.due_start_date.substring(0, 7) : "")
        .prop("readonly", true);
        
      if (data.due_end_date) {
        let endDate = new Date(data.due_end_date);
        $("#due_end_date").val(
          endDate.toLocaleDateString("en-US", {
            month: "long",
            year: "numeric"
          })
        ).prop("readonly", true);
      } else {
        $("#due_end_date").val("").prop("readonly", true);
      }
      
      let currentMonth = new Date().toISOString().slice(0, 7);
      let dueStartMonth = data.due_start_date
        ? data.due_start_date.substring(0, 7)
        : currentMonth;
      let deductionMonth =
        dueStartMonth > currentMonth ? dueStartMonth : currentMonth;
      $("#deduction_month")
        .val(deductionMonth)
        .attr("min", currentMonth)
        .attr("max", currentMonth);
    } else {
      swalError("Warning", "Staff Loan Details Not Found");
    }
  } catch (error) {
    console.error("Error fetching staff loan details:", error);
  }
}

function deleteStaffLoan(id) {
  swalConfirm(
    "Are you sure?",
    "Do you want to delete this Staff Loan?",
    function () {
      $.post(
        "api/staff_loan_&_advance/delete_staff_loan.php",
        {
          id: id,
        },
        function (response) {
          if (response == 1) {
            swalSuccess("Success", "Staff Loan Deleted Successfully!");
            getStaffLoanList();
          } else {
            swalError("Error", "Unable to Delete Staff Loan!");
          }
        },
        "json",
      );
    },
  );
}

async function getBranchList(company_id) {
  try {
    const response = await $.ajax({
      url: "api/attendance_files/get_branch_list.php",
      data: { cmpy_id: company_id },
      type: "POST",
      dataType: "json",
    });

    let appendBranchOption = '<option value="">Select Branch Name</option>';

    $.each(response, function (index, val) {
      appendBranchOption += `
                <option value="${val.id}">
                    ${val.branch_name}
                </option>
            `;
    });

    $('#branch_name').empty().append(appendBranchOption);
  } catch (error) {
    console.error("Error loading branch list:", error);
  }
}