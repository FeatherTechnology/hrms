$(document).ready(function () {
  // when i click the back button
  $(".add_staff_loan").click(function () {
    getCompanyName("#company_name");
    $(".add_staff_loan,.staff_loan_table_content").hide();
    $("#staff_loan_content,.back_to_list").show();
    $(".user_info_card,.loan_calculation_card").find("input, select").val("");
  });

  $(".back_to_list").click(function () {
    getStaffLoanList();
    $(".add_staff_loan,.staff_loan_table_content").show();
    $("#staff_loan_content,.back_to_list").hide();
  });

  $(document).on("click", ".staffLoanActionBtn", function () {
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
      $(".add_staff_loan,.staff_loan_table_content").hide();
      $("#staff_loan_content,.back_to_list").show();
      getStaffLoanDetails($(this).attr("value"));
    } else {
      // Date mismatched
      swalError("Warning", "Loan due started. Edit not allowed...");
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
    var staff_loan_id = $("#staff_loan_id").val();
    var staff_profile_id = $("#staff_name").val();
    var loan_amount = $("#loan_amount").val();
    var int_rate = $("#int_rate").val();
    var due_period = $("#due_period").val();
    var total_intrest = $("#total_intrest").val();
    var due_amount = $("#due_amount").val();
    var due_start_date = $("#due_start_date").val();
    var due_end_date = $("#due_end_date").val();

    var data = [
      "company_name",
      "staff_name",
      "loan_amount",
      "int_rate",
      "due_period",
      "due_start_date",
    ];
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
        "Do you want to submit Staff Loan?",
        function () {
          $.post(
            "api/staff_loan_&_advance/submit_staff_loan.php",
            {
              company_name,
              staff_profile_id,
              loan_amount,
              int_rate,
              due_period,
              total_intrest,
              due_amount,
              due_start_date,
              due_end_date,
              staff_loan_id,
            },
            function (response) {
              if (response == "1") {
                swalSuccess("Success", "Staff Loan Updated Successfully!");
              } else if (response == "2") {
                swalSuccess("Success", "Staff Loan Added Successfully!");
              } else {
                swalError("Error", "Error Occurred!");
              }
              $(".add_staff_loan,.staff_loan_table_content").show();
              $("#staff_loan_content,.back_to_list").hide();
              getStaffLoanList();
            },
            "json",
          );
        },
      );
    }
  });

  $("#company_name").on("change", function () {
    var cmpy_id = $(this).val();
    getStaffName(cmpy_id);
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
      let date = new Date(startDate);

      date.setMonth(date.getMonth() + duePeriod);

      let year = date.getFullYear();
      let month = String(date.getMonth() + 1).padStart(2, "0");
      let day = String(date.getDate()).padStart(2, "0");

      $("#due_end_date").val(year + "-" + month + "-" + day);
    } else {
      $("#due_end_date").val("");
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

function getStaffLoanDetails(id) {
  $.post(
    "api/staff_loan_&_advance/getStaffDetails.php",
    { id: id },

    function (response) {
      if (response && response.length > 0) {
        let data = response[0];

        // Staff Loan ID
        $("#staff_loan_id").val(data.id);
        // Show Edit Form
        $(".add_staff_loan,.staff_loan_table_content").hide();
        $("#staff_loan_content,.back_to_list").show();
        // Load company dropdown
        getCompanyName("#company_name")
          .then(function () {
            // Set company
            $("#company_name").val(String(data.company_id));
            // Load staff dropdown
            return getStaffName(data.company_id);
          })
          .then(function () {
            // Set staff
            $("#staff_name").val(String(data.staff_id));
            // Load staff information
            getStaffInfo();
          });
        // Loan Details
        $("#loan_amount").val(data.loan_amount);
        $("#int_rate").val(data.intrest_rate);
        $("#due_period").val(data.due_period);
        $("#total_intrest").val(data.total_intrest);
        $("#due_amount").val(data.due_amount);

        // Dates
        $("#due_start_date").val(data.due_start_date);
        $("#due_end_date").val(data.due_end_date);
      } else {
        swalError("Warning", "Staff Loan Details Not Found");
      }
    },
    "json",
  );
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
