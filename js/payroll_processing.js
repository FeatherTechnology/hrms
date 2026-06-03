$(document).ready(function () {
  // company name on change
  $("#cmpy_name").change(function () {
    let cmpy_id = $(this).val();
    getBranchList(cmpy_id);
  });

  // gendrate pay roll
  $("#gen_pay_roll").click(function () {
    let company_id = $("#cmpy_name").val();
    let branch_id = $("#branch_name").val();
    let month = $("#date").val();

    if (company_id == "" || branch_id == "" || month == "") {
      swalError("Error", "Please Filled The Manditaory Feild");
    } else {
      getPayRoll(company_id, branch_id, month);
    }
  });

  // document end
});

$(function () {
  getCompanyList();
});

// to get the company list
function getCompanyList() {
  $.post(
    "api/attendance_files/get_company_list.php",
    {},
    function (response) {
      $("#cmpy_name").empty();
      $("#cmpy_name").append("<option value=''>Select Company Name</option>");

      $.each(response, function (index, val) {
        $("#cmpy_name").append(
          "<option value='" +
            val["id"] +
            "'>" +
            val["company_name"] +
            "</option>",
        );
      });
    },
    "json",
  );
}

// to get the branch list
function getBranchList(cmpy_id) {
  $.post(
    "api/attendance_files/get_branch_list.php",
    { cmpy_id },
    function (response) {
      $("#branch_name").empty();
      $("#branch_name").append("<option value=''>Select Branch Name</option>");

      $.each(response, function (index, val) {
        $("#branch_name").append(
          "<option value='" +
            val["id"] +
            "'>" +
            val["branch_name"] +
            "</option>",
        );
      });
    },
    "json",
  );
}

// to get the pay roll
function getPayRoll(company_id, branch_id, month) {
  $.post(
    "api/payroll_files/get_payroll.php",
    {
      company_id,
      branch_id,
      month,
    },
    function (response) {
      $(".payroll_list").show();

      let components = response.components;

      //////////////////////////////////////////////////////
      // BUILD TABLE HEADER
      //////////////////////////////////////////////////////

      let table = `
        <table class="table table-bordered" id="payroll_table">
          <thead>

            <tr>
              <th rowspan="2">SNO</th>
              <th rowspan="2">Staff ID</th>
              <th rowspan="2">Staff Name</th>
              <th rowspan="2">Company Name</th>
              <th rowspan="2">Department</th>
              <th rowspan="2">Designation</th>
              <th rowspan="2">Team</th>
              <th rowspan="2">CTC</th>

              <th colspan="${components.length +2}">Gross Salary</th>

              <th rowspan="2">Gross Total</th>

              <th colspan="5">Total Deductions</th>

              <th rowspan="2">Deduction</th>
              <th rowspan="2">Net Salary</th>
            </tr>

            <tr>
      `;

      //////////////////////////////////////////////////////
      // DYNAMIC COMPONENT HEADERS
      //////////////////////////////////////////////////////

      components.forEach(function (component) {
        table += `<th>${component}</th>`;
      });

      table += `<th>OT Amount</th> 
      <th>Extra Working Days</th>`;
      //////////////////////////////////////////////////////
      // FINAL HEADERS
      //////////////////////////////////////////////////////

      table += `
              <th>PF</th>
              <th>Admin Charge</th>
              <th>Pension</th>
              <th>ESI</th>
              <th>PT</th>
            </tr>

          </thead>

          <tbody></tbody>

        </table>
      `;

      //////////////////////////////////////////////////////
      // APPEND TABLE
      //////////////////////////////////////////////////////

      $("#payroll_table_div").html(table);

      //////////////////////////////////////////////////////
      // COLUMN MAPPING
      //////////////////////////////////////////////////////

      let columnMapping = [
        "sno",
        "staff_id",
        "staff_name",
        "company_name",
        "department",
        "designation",
        "team",
        "total_ctc",
      ];

      //////////////////////////////////////////////////////
      // FLATTEN DYNAMIC COMPONENTS
      //////////////////////////////////////////////////////

      $.each(response.data, function (index, val) {
        components.forEach(function (component) {
          val[`comp_${component}`] = val.components?.[component] || 0;
        });
      });

      //////////////////////////////////////////////////////
      // ADD DYNAMIC COMPONENT KEYS
      //////////////////////////////////////////////////////

      components.forEach(function (component) {
        columnMapping.push(`comp_${component}`);
      });

      //////////////////////////////////////////////////////
      // FINAL COLUMNS
      //////////////////////////////////////////////////////

      columnMapping.push(
        "ot_amount",
        "extra_working",
        "gross_total",
        "pf",
        "admin_charge",
        "pension",
        "esi",
        "pt",
        "deduction_total",
        "net_salary",
      );

      //////////////////////////////////////////////////////
      // APPEND DATA TO TABLE
      //////////////////////////////////////////////////////

      appendDataToTable("#payroll_table", response.data, columnMapping);

      //////////////////////////////////////////////////////
      // INIT DATATABLE
      //////////////////////////////////////////////////////

      setdtable("#payroll_table", "Payroll Report");
    },
    "json",
  );
}
