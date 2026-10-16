
$(document).ready(function () {

    // Generate payslip
    $("#gen_pay_slip").on("click", function (e) {
        e.preventDefault();

        let company_id = $("#cmpy_id").val();
        let branch_id = $("#branch_id").val();
        let stf_prf_id = $("#stf_prf_id").val();
        let month = $("#date").val();

        if (!month) {
            swalError("Warning", "Please select Month & Year.");
            return;
        }

        getPayslip(company_id, branch_id, stf_prf_id, month);
    });

    // Download payslip as PDF
    $("#download_payslip").on("click", function () {

        let payslip = document.querySelector(".payslip-container");

        if (!payslip) {
            swalError("Warning", "Payslip is not available.");
            return;
        }

        html2canvas(payslip, {
            scale: 2,
            useCORS: true,
            backgroundColor: "#ffffff"
        }).then(function (canvas) {

            const imgData = canvas.toDataURL("image/png");
            const { jsPDF } = window.jspdf;

            let pdf = new jsPDF("p", "mm", "a4");

            let pdfWidth = pdf.internal.pageSize.getWidth();
            let pdfHeight = pdf.internal.pageSize.getHeight();

            let margin = 5;
            let imgWidth = pdfWidth - (margin * 2);
            let imgHeight = (canvas.height * imgWidth) / canvas.width;

            // Support multiple PDF pages if the payslip is tall.
            let pageContentHeight = pdfHeight - (margin * 2);
            let heightLeft = imgHeight;
            let position = margin;

            pdf.addImage(
                imgData,
                "PNG",
                margin,
                position,
                imgWidth,
                imgHeight
            );

            heightLeft -= pageContentHeight;

            while (heightLeft > 0) {
                position = margin - (imgHeight - heightLeft);
                pdf.addPage();

                pdf.addImage(
                    imgData,
                    "PNG",
                    margin,
                    position,
                    imgWidth,
                    imgHeight
                );

                heightLeft -= pageContentHeight;
            }

            let month = $("#date").val() || "Payslip";
            pdf.save("Payslip_" + month + ".pdf");

        }).catch(function (error) {
            console.error("Payslip PDF error:", error);
            swalError("Error", "Unable to download the payslip.");
        });
    });

});


// Get logged-in staff details
$(document).ready(function () {
    getstaffdetails();
});


function getstaffdetails() {

    $.ajax({
        url: "api/attendance_files/get_staff_details.php",
        type: "POST",
        dataType: "json",
        cache: false,

        success: function (response) {

            $("#stf_prf_id").val(response.stf_id || "");
            $("#cmpy_id").val(response.cmpy_id || "");
            $("#branch_id").val(response.brch_id || "");

        },

        error: function (xhr, status, error) {
            console.error("Staff details error:", error);
        }
    });
}


// Format currency safely, including values returned with commas.
function formatAmount(amount) {

    let value = String(amount ?? "0")
        .replace(/,/g, "")
        .replace(/[₹\s]/g, "");

    let number = Number(value);

    if (!Number.isFinite(number)) {
        number = 0;
    }

    return "₹" + number.toLocaleString("en-IN", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}


// Format a numeric or text value.
function displayValue(value) {
    return value === null || value === undefined || value === ""
        ? "-"
        : value;
}


// Prevent HTML from being interpreted as markup.
function escapeHtml(value) {
    return String(value ?? "").replace(/[&<>"']/g, function (char) {
        return {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        }[char];
    });
}


// Get payslip using the existing payroll AJAX page.
function getPayslip(company_id, branch_id, stf_prf_id, month) {

    $.ajax({
        url: "api/payroll_files/get_payroll.php",
        type: "POST",
        dataType: "json",
        cache: false,

        data: {
            company_id: company_id,
            branch_id: branch_id,
            stf_prf_id: stf_prf_id,
            month: month
        },

        success: function (response) {

            if (
                !response ||
                !Array.isArray(response.data) ||
                response.data.length === 0
            ) {
                $(".pay_slip_details").hide();
                $("#salary_component_body").empty();

                swalError(
                    "Warning",
                    "Payslip has not been generated for the selected month!"
                );

                return;
            }

            let row = response.data[0];

            // Header
            let dateObj = new Date(month + "-01T00:00:00");

            let formattedMonth = dateObj.toLocaleString("en-US", {
                month: "long",
                year: "numeric"
            });

            $("#ps_company_name").text(displayValue(row.company_name));
            $("#ps_month").text(formattedMonth);

            // Employee details
            $("#ps_staff_id").text(displayValue(row.staff_id));
            $("#ps_staff_name").text(displayValue(row.staff_name));
            $("#ps_department").text(displayValue(row.department));
            $("#ps_designation").text(displayValue(row.designation));
            $("#team_name").text(displayValue(row.team));

            $("#ps_total_days").text(displayValue(row.total_days));
            $("#ps_working_days").text(displayValue(row.working_days));
            $("#ps_present_days").text(displayValue(row.present_days));
            $("#ps_approved_leave").text(displayValue(row.approved_leave));
            $("#ps_lop_days").text(displayValue(row.lop_days));
            $("#ps_extra_working").text(displayValue(row.extra_working));

            $("#ps_holidays").text(displayValue(row.holidays));
            $("#ps_weekoff").text(displayValue(row.weekoff));
            $("#ps_ot_hours").text(displayValue(row.ot_hours));
            $("#ps_ot_days").text(displayValue(row.ot_days));

            // Salary information
            $("#ps_total_ctc").text(formatAmount(row.total_ctc));
            $("#ps_earned_salary").text(formatAmount(row.earned_for_month));
            $("#ps_one_day_salary").text(formatAmount(row.one_day_salary));
            $("#ps_lop_amount").text(formatAmount(row.lop_amount));
            $("#ps_pt_eligible_earnings").text(
                formatAmount(row.pt_eligible_earnings)
            );

            // Totals returned by PHP; do not recalculate them in JavaScript.
            $("#ps_gross_total").text(formatAmount(row.gross_total));
            $("#ps_deduction_total").text(formatAmount(row.deduction_total));
            $("#ps_net_salary").text(formatAmount(row.net_salary));
            $("#ps_take_home").text(formatAmount(row.take_home));

            // Build earning rows.
            let earnings = [];

            $.each(row.components || {}, function (id, amount) {

                let component = (response.components || []).find(function (c) {
                    return String(c.id) === String(id);
                });

                if (component) {
                    earnings.push({
                        name: component.name,
                        amount: amount
                    });
                }
            });

            // Extra working earnings.
            earnings.push({
                name: "Extra Working Amount",
                amount: row.extra_working_amount
            });

            // Employer contributions included in the payroll gross, as
            // indicated by the supplied payroll response.
            earnings.push({
                name: "Employer PF Contribution",
                amount: row.employer_pf_earning
            });

            earnings.push({
                name: "Employer ESI Contribution",
                amount: row.employer_esi_earning
            });

            // Keep OT Amount as the last earning row.
            earnings.push({
                name: "OT Amount",
                amount: row.ot_amount
            });


            // Build deduction rows.
            let deductions = [
                {
                    name: "PF",
                    amount: row.pf
                },
                {
                    name: "Admin Charge",
                    amount: row.admin_charge
                },
                {
                    name: "Pension",
                    amount: row.pension
                },
                {
                    name: "ESI",
                    amount: row.esi
                },
                {
                    name: "PT",
                    amount: row.pt
                },
                {
                    name: "Loan Due",
                    amount: row.loan_due
                },
                {
                    name: "Salary Advance",
                    amount: row.salary_advance
                },
                {
                    name: "Attendance Deduction",
                    amount: row.attendance_deduction
                },
                {
                    name: "Employer PF Contribution",
                    amount: row.employer_pf_deduction
                },
                {
                    name: "Employer ESI Contribution",
                    amount: row.employer_esi_deduction
                }
            ];


            // Render both sides in the same table.
            let totalRows = Math.max(earnings.length, deductions.length);
            let html = "";

            for (let i = 0; i < totalRows; i++) {

                let earning = earnings[i];
                let deduction = deductions[i];

                html += `
                    <tr>
                        <td>${earning ? escapeHtml(earning.name) : ""}</td>
                        <td class="amount-cell">
                            ${earning ? formatAmount(earning.amount) : ""}
                        </td>

                        <td>${deduction ? escapeHtml(deduction.name) : ""}</td>
                        <td class="amount-cell">
                            ${deduction ? formatAmount(deduction.amount) : ""}
                        </td>
                    </tr>
                `;
            }

            $("#salary_component_body").html(html);

            // Show the payslip only after successfully populating it.
            $(".pay_slip_details").show();

            console.log("Payslip payroll response:", row);
        },

        error: function (xhr, status, error) {

            console.error("Payslip AJAX error:", error);
            console.error("Server response:", xhr.responseText);

            $(".pay_slip_details").hide();

            swalError(
                "Error",
                "Unable to fetch payslip. Please check the payroll response."
            );
        }
    });
}
