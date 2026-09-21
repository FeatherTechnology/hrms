$(document).ready(function () {

  $(".add_advance_salary").click(function () {
    getCompanyName("#company_name");
    $(".add_advance_salary,.staff_salary_advance_table_content").hide();
    $("#advance_salary_content,.back_to_list").show();
     $(".user_info_card,.salary_advance_card").find("input, select").val("");

  });

  $(".back_to_list").click(function () {
     getStaffAdvanceSalaryList();
    $(".add_advance_salary,.staff_salary_advance_table_content").show();
    $("#advance_salary_content,.back_to_list").hide();

  });
  
  $(document).on("click", ".staffSalaryAdvanceActionBtn", function () {
      $(".add_advance_salary,.staff_salary_advance_table_content").hide();
     $("#advance_salary_content,.back_to_list").show();
     getStaffSalaryAdvanceDetails($(this).attr("value"));

  });

$(document).on("click", ".staffSalaryAdvanceDeleteBtn", function () {

  let id = $(this).attr("value");
   swalConfirm(
        "Are you sure?",
        "Do you want to delete this Salary Advance ?",
        function () {
            $.post(
                "api/staff_loan_&_advance/delete_staff_salary_advance.php",
                {
                    id: id
                },
                function (response) {
                    if (response == 1) {
                        swalSuccess(
                            "Success",
                            "Staff Salary Advance Deleted Successfully!"
                        );
                        getStaffAdvanceSalaryList();
                    } else {
                        swalError(
                            "Error",
                            "Unable to Delete Staff Salary Advance!"
                        );
                    }
                },
                "json"
            );
        }
    );

});

  $("#submit_staff_salary_advance").click(function (event) {
    event.preventDefault();
   var company_name = $('#company_name').val();
   var advance_salary_id = $('#advance_salary_id').val();
   var staff_profile_id= $('#staff_name').val();
   var advance_amount= $('#advance_amount').val();
   var dedection_month= $('#dedection_month').val();

   var data = ["company_name", "staff_name","advance_amount","dedection_month"];
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
        "Do you want to submit Salary Advance... ?",
        function () {
          $.post(
            "api/staff_loan_&_advance/submit_salary_advance.php",
            {
              company_name,
              staff_profile_id,
              advance_amount,
              dedection_month,
              advance_salary_id
            },
            function (response) {
              if (response == "1") {
                swalSuccess("Success", "Staff Loan Updated Successfully!");
              } else if (response == "2") {
                swalSuccess("Success", "Staff Loan Added Successfully!");
              } else {
                swalError("Error", "Error Occurred!");
              }
              $(".add_advance_salary,.staff_salary_advance_table_content").show();
              $("#advance_salary_content,.back_to_list").hide();
              getStaffAdvanceSalaryList();
            },
             "json"
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

});
// document end


$(function () {
  getStaffAdvanceSalaryList();
});

// function start
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
function getStaffAdvanceSalaryList() {
  $.post(
    "api/staff_loan_&_advance/get_staff_advance_salary_list.php",
    function (response) {
      var columnMapping = [
        "sno",
        "company_name",
        "staff_name",
        "advance_amount",
        "dedection_month",
        "action",
      ];
      appendDataToTable("#staff_salary_advance_table", response, columnMapping);
      setdtable("#staff_salary_advance_table", "Staff Advance Salary List");
    },
    "json",
  );
}


function getStaffSalaryAdvanceDetails(id) {

    $.post(
        "api/staff_loan_&_advance/getStaffAdvanceDetails.php",
        { id: id },
        function (response) {
            if (response && response.length > 0) {
                let data = response[0];
                // Staff Loan ID
                $("#advance_salary_id").val(data.id);
                // Show Edit Form
                $(".add_advance_salary,.staff_salary_advance_table_content").hide();
                $("#advance_salary_content,.back_to_list").show();
                // Load company dropdown
                getCompanyName("#company_name").then(function () {
                    // Set company
                    $("#company_name").val(String(data.company_id));
                    // Load staff dropdown
                    return getStaffName(data.company_id);
                }).then(function () {
                    // Set staff
                    $("#staff_name").val(String(data.staff_id));
                    // Load staff information
                    getStaffInfo();
                });
                // Loan Details
                $("#advance_amount").val(data.advance_amount);
                $("#dedection_month").val(data.dedection_month.substring(0, 7));
            } else {
                swalError("Warning", "Staff Loan Details Not Found");
            }
        },
        "json"
    );
}

