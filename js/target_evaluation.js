
$(document).ready(function () {

    $(document).on("click", "#back_btn", function () {
        swapTableAndCreation();
        clearFields();
    });


    $("#submit_target_evaluation").on("click", function (event) {
        event.preventDefault();

        let target_id = $("#target_id").val();

        let rows = $("#staff_info_table tbody tr").filter(function () {
            return $(this).find(".staff_id").length > 0;
        });

        if (!target_id) {
            Swal.fire("Warning", "Target setup ID is missing.", "warning");
            return false;
        }

        if (rows.length === 0) {
            Swal.fire("Warning", "No staff details found.", "warning");
            return false;
        }

        let isValid = true;
        let errorMessage = "";
        let completedRows = [];

        rows.each(function (index) {
            let row = $(this);

            // Skip staff who have already been evaluated
            if (row.attr("data-evaluated") == "1") {
                return true;
            }

            let achievedValue = row.find(".achieved_value").val().trim();
            let incentiveAmount = row.find(".incentive_amount").val().trim();

            // Skip rows where both values are empty
            if (achievedValue === "" && incentiveAmount === "") {
                return true;
            }

            if (achievedValue === "" || incentiveAmount === "") {
                isValid = false;
                errorMessage = "Please enter both Achieved Value and Incentive Amount for Staff Row " + (index + 1) + ".";
                return false;
            }

            if (!Number.isFinite(Number(achievedValue)) || Number(achievedValue) < 0) {
                isValid = false;
                errorMessage = "Please enter a valid Achieved Value for Staff Row " + (index + 1) + ".";
                return false;
            }

            if (!Number.isFinite(Number(incentiveAmount)) || Number(incentiveAmount) < 0) {
                isValid = false;
                errorMessage = "Please enter a valid Incentive Amount for Staff Row " + (index + 1) + ".";
                return false;
            }

            completedRows.push(row);
        });

        if (!isValid) {
            Swal.fire("Warning", errorMessage, "warning");
            return false;
        }

        // At least one staff evaluation must be entered
        if (completedRows.length === 0) {
            Swal.fire("Warning", "Please enter Achieved Value and Incentive Amount for at least one staff member.", "warning");
            return false;
        }

        swalConfirm(
            "Are you sure?",
            "Do you want to submit the Target Evaluation for " + completedRows.length + " staff member(s)?",
            function () {

                let formData = new FormData();
                formData.append("target_id", target_id);

                completedRows.forEach(function (row) {
                    formData.append("target_detail_id[]", row.find('[name="target_detail_id[]"]').val());
                    formData.append("staff_id[]", row.find(".staff_id").val());
                    formData.append("achievement_basis[]", row.find('[name="achievement_basis[]"]').val());
                    formData.append("achieved_value[]", row.find(".achieved_value").val());
                    formData.append("incentive_amount[]", row.find(".incentive_amount").val());
                });

                $("#submit_target_evaluation").prop("disabled", true);

                $.ajax({
                    url: "api/target_evaluation/submit_target_evaluation.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    cache: false,
                    dataType: "json",

                    success: function (response) {
                        if (response.result == 0) {
                            swalError("Error", response.message || "Target Evaluation Not Added!");
                        } else if (response.result == 1) {
                            swalSuccess("Success", "Target Evaluation Submitted Successfully!");

                            $("#target_id").val("");
                            $("#staff_info_table tbody").empty();

                            swapTableAndCreation();
                            getTargetTable();
                        } else {
                            swalError("Error", response.message || "Something went wrong!");
                        }

                        $("#submit_target_evaluation").prop("disabled", false);
                    },

                    error: function (xhr) {
                        console.log(xhr.responseText);
                        swalError("Error", "Something went wrong!");
                        $("#submit_target_evaluation").prop("disabled", false);
                    }
                });
            }
        );
    });

    $(document).on("click", ".evaluateBtn", async function () {
        let id = $(this).attr("value");

        try {
            const response = await $.ajax({
                url: "api/target_evaluation/target_evaluation_data.php",
                type: "POST",
                data: { id: id },
                dataType: "json"
            });


            if (!response || !response.id) {
                swalError("Target setup details not found.");
                return;
            }

            swapTableAndCreation();

            // Reset form only if it exists
            let form = $("#target_evaluation")[0];
            if (form) {
                form.reset();
            }

            $("#staff_info_table tbody").empty();
            $("#target_id").val(response.id);

            if (Array.isArray(response.staff_details) && response.staff_details.length > 0) {
                response.staff_details.forEach(function (staff) {
                    addEvaluationRow(staff);
                });

                if (typeof updateStaffSerialNo === "function") {
                    updateStaffSerialNo();
                }
            } else {
                $("#staff_info_table tbody").html(
                    '<tr><td colspan="12" class="text-center">No staff details found.</td></tr>'
                );
            }

        } catch (error) {
            console.error("Target Evaluation Error:", error);
            console.error("Error Message:", error.message);

            swalError("Failed to load target evaluation details. Check console for details.");
        }
    });



    $(document).on("change", ".achieved_value", function () {
        let input = $(this);
        let row = input.closest("tr");

        let achievedValue = parseFloat(input.val());
        let basis = input.data("basis");

        let targetValue = basis === "Count"
            ? parseFloat(input.data("target-count"))
            : parseFloat(input.data("target-amount"));

        let minimumValue = basis === "Count"
            ? parseFloat(input.data("minimum-count"))
            : parseFloat(input.data("minimum-amount"));

        let incentiveInput = row.find(".incentive_amount");

        if (input.val().trim() === "" || !Number.isFinite(achievedValue)) {
            incentiveInput.val("");
            return;
        }

        let payoutType = (row.find(".payout_types").val() || "").trim().toLowerCase();
        let ctcPercentage = parseFloat(row.find(".ctc_percentages").val()) || 0;
        let fixedAmount = parseFloat(row.find(".fixed_amount").val()) || 0;
        let totalCtc = parseFloat(row.find('[name="target_ctc_amount[]"]').val()) || 0;

        if (
            !Number.isFinite(targetValue) ||
            !Number.isFinite(minimumValue) ||
            targetValue <= minimumValue
        ) {
            incentiveInput.val("");
            return;
        }

        let achievementRatio = (achievedValue - minimumValue) / (targetValue - minimumValue);

        if (achievementRatio <= 0) {
            incentiveInput.val("0.00");
            return;
        }

        let incentiveAmount = 0;

        if (payoutType.includes("ctc") || payoutType.includes("percentage")) {
            let incentivePercentage = achievementRatio * ctcPercentage;
            incentiveAmount = totalCtc * (incentivePercentage / 100);
        } else if (payoutType.includes("fixed")) {
            incentiveAmount = achievementRatio * fixedAmount;
        } else {
            incentiveInput.val("");
            return;
        }

        incentiveInput.val(incentiveAmount.toFixed(2));
    });


});

$(function () {
    getTargetTable()
});

function getTargetTable() {
    serverSideTable("#target_evaluation_table", " ", "api/target_evaluation/get_target_evaluation_list.php", " Target Evaluation List")
}
function swapTableAndCreation() {
    if ($(".target_evaluation_table_content").is(":visible")) {
        $(".target_evaluation_table_content").hide();
        $("#target_evaluation_content").show();
        $(".backBtn").show();
    } else {
        $(".target_evaluation_table_content").show();
        $("#target_evaluation_content").hide();
        $(".backBtn").hide();
    }
}

// Update serial numbers
function updateStaffSerialNo() {
    $("#staff_info_table tbody tr").each(function (index) {
        $(this).find(".sno").text(index + 1);
    });
}

function clearFields() {
    $("#target_id").val('');
    $("#staff_info_table tbody").empty();
}

function addEvaluationRow(staff) {
    let achievementBasis = "";

    if (staff.target_counts !== null && staff.target_counts !== "") {
        achievementBasis = "Count";
    } else if (staff.target_amounts !== null && staff.target_amounts !== "") {
        achievementBasis = "Amount";
    }

    let hasAchievedValue =
        staff.achieved_value !== null &&
        staff.achieved_value !== undefined &&
        String(staff.achieved_value).trim() !== "";

    let serialNo = $("#staff_info_table tbody tr").length + 1;
    let achievedReadonly = hasAchievedValue ? "readonly" : "";

    let row = `
        <tr data-staff-id="${staff.staff_id}" data-evaluated="${hasAchievedValue ? 1 : 0}">
            <td class="serial-no">${serialNo}</td>
            <td>
                <input type="text" class="form-control" value="${staff.staff_name || ''}" readonly>
                <input type="hidden" class="staff_id" name="staff_id[]" value="${staff.staff_id}">
                <input type="hidden" name="target_detail_id[]" value="${staff.id}">
                <input type="hidden" name="target_ctc_amount[]" value="${staff.total_ctc ?? ''}">
            </td>
            <td><input type="text" class="form-control" value="${staff.incentive_titles || ''}" readonly></td>
            <td><input type="text" class="form-control target_counts" value="${staff.target_counts ?? ''}" readonly></td>
            <td><input type="text" class="form-control" value="${staff.target_amounts ?? ''}" readonly></td>
            <td><input type="text" class="form-control minimum_counts" value="${staff.minimum_counts ?? ''}" readonly></td>
            <td><input type="text" class="form-control" value="${staff.minimum_amounts ?? ''}" readonly></td>
            <td>
                <input type="text" class="form-control payout_types" value="${staff.payout_types || ''}" readonly>
            </td>
            <td><input type="text" class="form-control ctc_percentages" value="${staff.ctc_percentages ?? ''}" readonly></td>
            <td><input type="text" class="form-control fixed_amount" value="${staff.fixed_amounts ?? ''}" readonly></td>
            <td>
                <input type="text" class="form-control" name="achievement_basis[]" value="${achievementBasis}" readonly>
            </td>
            <td>
                <input type="number" class="form-control achieved_value"
                    name="achieved_value[]" min="0" step="0.01"
                    data-target-count="${staff.target_counts ?? ''}"
                    data-minimum-count="${staff.minimum_counts ?? ''}"
                    data-target-amount="${staff.target_amounts ?? ''}"
                    data-minimum-amount="${staff.minimum_amounts ?? ''}"
                    data-basis="${achievementBasis}"
                    value="${staff.achieved_value ?? ''}" ${achievedReadonly}>
            </td>
            <td>
                <input type="number" class="form-control incentive_amount"
                    name="incentive_amount[]" min="0" step="0.01"
                    value="${staff.incentive_amount ?? ''}" readonly>
            </td>
        </tr>
    `;

    $("#staff_info_table tbody").append(row);
}




