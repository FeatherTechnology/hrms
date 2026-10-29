
$(document).ready(function () {

  // Company name on change
  $("#cmpy_name").change(function () {
    let cmpy_id = $(this).val();
    getBranchList(cmpy_id);
    getDepartmentName(cmpy_id);
  });

  // Generate payroll
  $("#gen_pay_roll").click(function () {

    let company_id = $("#cmpy_name").val();
    let branch_id = $("#branch_name").val();
    let dept_id = $("#department_name").val();
    let month = $("#date").val();

    if ( company_id == "" ||  month == "" ) {
      swalError("Warning", "Please Filled The Manditaory Feild");
    } else {
      getPayRoll(company_id, branch_id,dept_id, month);
    }
  });

});

$(function () {
  getCompanyList();
});


// ==========================================================
// GET COMPANY LIST
// ==========================================================

function getCompanyList() {

  $.post(
    "api/attendance_files/get_company_list.php",
    {},
    function (response) {

      $("#cmpy_name").empty();

      $("#cmpy_name").append(
        "<option value=''>Select Company Name</option>"
      );

      $.each(response, function (index, val) {
        $("#cmpy_name").append(
          "<option value='" + val["id"] + "'>" +
          val["company_name"] +
          "</option>"
        );
      });

    },
    "json"
  );
}


// ==========================================================
// GET BRANCH LIST
// ==========================================================

function getBranchList(cmpy_id) {

  $.post(
    "api/attendance_files/get_branch_list.php",
    { cmpy_id: cmpy_id },

    function (response) {

      $("#branch_name").empty();

      $("#branch_name").append(
        "<option value=''>Select Branch Name</option>"
      );

      $.each(response, function (index, val) {
        $("#branch_name").append(
          "<option value='" + val["id"] + "'>" +
          val["branch_name"] +
          "</option>"
        );
      });

    },
    "json"
  );
}

/* --- Get Department Name --- */
async function getDepartmentName(company_name) {
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

// ==========================================================
// GET PAYROLL
// ==========================================================

function getPayRoll(company_id, branch_id, dept_id, month) {

  $.post(
    "api/payroll_files/get_payroll.php",
    {
      company_id: company_id,
      branch_id: branch_id,
      dept_id: dept_id,
      month: month
    },

    function (response) {

      // ====================================================
      // CHECK PENDING REGULARIZATION
      // ====================================================

      if (response.status == 0) {

        $(".payroll_list").hide();

        swalError("Warning", response.message);

        return false;
      }

      $(".payroll_list").show();

      let components = response.components || [];


      // ====================================================
      // BUILD TABLE HEADER
      // ====================================================

      let table = `
        <table class="table table-bordered" id="payroll_table">

          <thead>
            <tr>

              <th rowspan="2">SNO</th>
              <th rowspan="2">Staff ID</th>
              <th rowspan="2">Staff Name</th>
              <th rowspan="2">Company Name</th>
              <th rowspan="2">Branch Name</th>
              <th rowspan="2">Department</th>
              <th rowspan="2">Team</th>
              <th rowspan="2">Designation</th>

              <th rowspan="2">Total CTC</th>
              <th rowspan="2">Per Day Salary</th>
              <th rowspan="2">Working Days</th>

              <th rowspan="2">LOP Days</th>
              <th rowspan="2">LOP Amount</th>

              <th rowspan="2">Earned</th>

              <!-- SALARY COMPONENTS -->
              <th colspan="${components.length}">
                Salary Components
              </th>

              <!-- NEW PAYROLL CALCULATIONS -->
              <th rowspan="2">PF Percentage</th>
              <th rowspan="2">Pay</th>
              <th rowspan="2">Others</th>
              <th rowspan="2">nonctcamount</th>

              <th rowspan="2">Extra Working Days</th>
              <th rowspan="2">Extra Working Amount</th>

              <th rowspan="2">OT Amount</th>

              <th rowspan="2">Employer PF</th>
              <th rowspan="2">Employer ESI</th>

              <th rowspan="2">Gross Salary</th>

              <!-- DEDUCTIONS -->
              <th colspan="5">Deductions</th>

              <th rowspan="2">Total Deduction</th>
              <th rowspan="2">Net Salary</th>

              <th rowspan="2">Loan Due</th>
              <th rowspan="2">Salary Advance</th>
              <th rowspan="2">Attendance Deduction</th>

              <th rowspan="2">Take Home</th>

            </tr>

            <tr>
      `;


      // ====================================================
      // SALARY COMPONENT HEADERS
      // ====================================================

      components.forEach(function (component) {

        table += `
          <th>${component.name}</th>
        `;

      });


      // ====================================================
      // DEDUCTION HEADERS
      // ====================================================

      table += `
              <th>PF</th>
              <th>ESI</th>
              <th>PT</th>
              <th>Employer PF Contribution</th>
              <th>Employer ESI Contribution</th>
            </tr>

          </thead>

          <tbody></tbody>

        </table>
      `;


      $("#payroll_table_div").html(table);


      // ====================================================
      // BASE COLUMN MAPPING
      // ====================================================

      let columnMapping = [

        "sno",
        "staff_id",
        "staff_name",
        "company_name",
        "branch_name",
        "department",
        "team",
        "designation",

        "total_ctc",
        "one_day_salary",
        "working_days",

        "lop_days",
        "lop_amount",

        "earned_for_month"

      ];


      // ====================================================
      // ADD SALARY COMPONENT VALUES
      // ====================================================

      $.each(response.data || [], function (index, val) {

        components.forEach(function (component) {

          val[`comp_${component.id}`] =
            val.components &&
            val.components[component.id] !== undefined
              ? val.components[component.id]
              : 0;

        });

      });


      // ====================================================
      // SALARY COMPONENT COLUMN MAPPING
      // ====================================================

      components.forEach(function (component) {

        columnMapping.push(
          `comp_${component.id}`
        );

      });


      // ====================================================
      // REMAINING COLUMN MAPPING
      // MUST EXACTLY MATCH HEADER ORDER
      // ====================================================

      columnMapping.push(

        // ------------------------------------
        // NEW PAYROLL CALCULATIONS
        // ------------------------------------

        "pf_percentage",
        "monthly_earned",
        "others",
        "nonctcamount",

        // ------------------------------------
        // EXTRA WORKING
        // ------------------------------------

        "extra_working_days",
        "extra_working_amount",

        // ------------------------------------
        // OT
        // ------------------------------------

        "ot_amount",

        // ------------------------------------
        // EMPLOYER CONTRIBUTIONS
        // ------------------------------------

        "employer_pf",
        "employer_esi",

        // ------------------------------------
        // GROSS
        // ------------------------------------

        "gross_total",

        // ------------------------------------
        // DEDUCTIONS
        // 7 columns
        // ------------------------------------

        "pf",
        "esi",
        "pt",
        "employer_pf_deduction",
        "employer_esi_deduction",
        // ------------------------------------
        // TOTALS
        // ------------------------------------
        "deduction_total",
        "net_salary",

        // ------------------------------------
        // OTHER DEDUCTIONS
        // ------------------------------------
        "loan_due",
        "salary_advance",
        "attendance_deduction",

        // ------------------------------------
        // TAKE HOME
        // ------------------------------------
        "take_home"
      );


      // ====================================================
      // CHECK EMPTY RESPONSE DATA
      // ====================================================

      let payrollData = response.data || [];

      if (payrollData.length === 0) {

        $("#payroll_table tbody").html(`
          <tr>
            <td colspan="${columnMapping.length}"
                class="text-center">
              No payroll records found
            </td>
          </tr>
        `);

        return;
      }

      // ====================================================
      // APPEND DATA
      // ====================================================

      appendDataToTable(
        "#payroll_table",
        payrollData,
        columnMapping
      );

      // ====================================================
      // DATATABLE
      // ====================================================

      setdtable(
        "#payroll_table",
        "Payroll Report"
      );

    },
    "json"
  );
}
