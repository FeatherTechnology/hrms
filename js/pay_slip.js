$(document).ready(function () {
  // to gendrate the pay slip
  $("#gen_pay_slip").click(function () {
    let company_id = $("#cmpy_id").val();
    let branch_id = $("#branch_id").val();
    let stf_prf_id = $("#stf_prf_id").val();
    let month = $("#date").val();

    getPayslip(company_id, branch_id, stf_prf_id, month);
  });

  // to downlode the pay slip
  $("#download_payslip").click(function () {
    let payslip = document.querySelector(".payslip-container");

    html2canvas(payslip, {
      scale: 2,
    }).then(function (canvas) {
      const imgData = canvas.toDataURL("image/png");

      const { jsPDF } = window.jspdf;

      let pdf = new jsPDF("p", "mm", "a4");

      let pdfWidth = pdf.internal.pageSize.getWidth();

      let pdfHeight = pdf.internal.pageSize.getHeight();

      let imgWidth = pdfWidth - 10;

      let imgHeight = (canvas.height * imgWidth) / canvas.width;

      let position = 5;

      pdf.addImage(imgData, "PNG", 5, position, imgWidth, imgHeight);

      pdf.save("Payslip.pdf");
    });
  });

  // document end
});

$(function () {
  getstaffdetails();
});

// to get the staff details
function getstaffdetails() {
  $.post(
    "api/attendance_files/get_staff_details.php",
    function (response) {
      $("#stf_prf_id").val(response.stf_id);
      $("#cmpy_id").val(response.cmpy_id);
      $("#branch_id").val(response.brch_id);
    },
    "json",
  );
}

// to get the pay slip
function getPayslip(company_id, branch_id, stf_prf_id, month) {
  $.post(
    "api/payroll_files/get_payroll.php",
    { company_id,  branch_id, month, stf_prf_id},
    function (response) {
      $(".pay_slip_details").show();

      if (response.data.length > 0) {
        let row = response.data[0];
        let dateObj = new Date(month + "-01");

        let formattedMonth = dateObj.toLocaleString("en-US", {
          month: "long",
          year: "numeric",
        });

        //////////////////////////////////////////////////////
        // HEADER
        //////////////////////////////////////////////////////

        $("#ps_company_name").text(row.company_name);
        $("#ps_month").text("Pay Slip For " + formattedMonth);

        //////////////////////////////////////////////////////
        // EMPLOYEE DETAILS
        //////////////////////////////////////////////////////

        $("#ps_staff_id").text(row.staff_id);
        $("#ps_staff_name").text(row.staff_name);

        $("#ps_department").text(row.department);
        $("#ps_designation").text(row.designation);
        $("#team_name").text(row.team_name);

        $("#ps_total_days").text(row.total_days);
        $("#ps_working_days").text(row.working_days);

        $("#ps_present_days").text(row.present_days);
        $("#ps_approved_leave").text(row.approved_leave);

        $("#ps_lop_days").text(row.lop_days);
        $("#ps_extra_working").text(row.extra_working);

        $("#otamount").text(row.ot_amount);

        //////////////////////////////////////////////////////
        // TOTALS
        //////////////////////////////////////////////////////

        $("#ps_gross_total").text("₹" + row.gross_total);
        $("#ps_deduction_total").text("₹" + row.deduction_total);
        $("#ps_net_salary").text("₹" + row.net_salary);

        //////////////////////////////////////////////////////
        // COMPONENTS
        //////////////////////////////////////////////////////

        let earnings = row.components;

        let deductions = [];

        deductions.push({
          name: "PF",
          amount: row.pf,
        });
        deductions.push({
          name: "Admin Charge",
          amount: row.admin_charge,
        });
        deductions.push({
          name: "Pension",
          amount: row.pension,
        });

        deductions.push({
          name: "ESI",
          amount: row.esi,
        });

        deductions.push({
          name: "PT",
          amount: row.pt,
        });

        let earningKeys = Object.keys(earnings);

        let totalRows = Math.max(earningKeys.length, deductions.length);

        let html = "";

        for (let i = 0; i < totalRows; i++) {
          let earnName = "";
          let earnAmount = "";

          let dedName = "";
          let dedAmount = "";

          ////////////////////////////////////////////////////
          // EARNING
          ////////////////////////////////////////////////////

          if (earningKeys[i]) {
            earnName = earningKeys[i];
            earnAmount = earnings[earningKeys[i]];
          }

          ////////////////////////////////////////////////////
          // DEDUCTION
          ////////////////////////////////////////////////////

          if (deductions[i]) {
            dedName = deductions[i].name;
            dedAmount = deductions[i].amount;
          }

          html += `
            <tr>

              <td>${earnName}</td>
              <td>
                ${earnAmount != "" ? "₹" + earnAmount : ""}
              </td>

              <td>${dedName}</td>
              <td>
                ${dedAmount != "" ? "₹" + dedAmount : ""}
              </td>

            </tr>
          `;
        }

        $("#salary_component_body").html(html);
      }
    },
    "json",
  );
}
