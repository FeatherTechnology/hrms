
$(document).ready(function () {

    $(document).on("click", "#back_btn", function () {
        swapTableAndCreation();
        clearFields();
    });



    $("#submit_target_incentive").on("click", function (event) {
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

            // Skip staff whose final incentive is already submitted
            if (row.attr("data-finalized") == "1") {
                return true;
            }

            // Skip staff who have not completed target evaluation
            if (row.attr("data-has-achieved") != "1") {
                return true;
            }

            let input = row.find(".final_incentive");

            if (input.length === 0) {
                return true;
            }

            let finalIncentive = input.val().trim();

            if (finalIncentive === "") {
                return true;
            }

            if (!Number.isFinite(Number(finalIncentive)) || Number(finalIncentive) < 0) {
                isValid = false;
                errorMessage = "Please enter a valid Final Incentive for Staff Row " + (index + 1) + ".";
                return false;
            }

            completedRows.push(row);
        });

        if (!isValid) {
            Swal.fire("Warning", errorMessage, "warning");
            return false;
        }

        if (completedRows.length === 0) {
            Swal.fire("Warning", "Please enter Final Incentive for at least one eligible staff member.", "warning");
            return false;
        }

        swalConfirm(
            "Are you sure?",
            "Do you want to submit the Final Incentive for " + completedRows.length + " staff member(s)?",
            function () {

                let formData = new FormData();
                formData.append("target_id", target_id);

                completedRows.forEach(function (row) {
                    formData.append("target_detail_id[]", row.find('[name="target_detail_id[]"]').val());
                    formData.append("staff_id[]", row.find(".staff_id").val());
                    formData.append("final_incentive[]", row.find(".final_incentive").val());
                });

                $("#submit_target_incentive").prop("disabled", true);

                $.ajax({
                    url: "api/target_incentive/submit_target_incentive.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    cache: false,
                    dataType: "json",

                    success: function (response) {
                        if (response.result == 0) {
                            swalError("Error", response.message || "Target Incentive Not Added!");
                        } else if (response.result == 1) {
                            swalSuccess("Success", "Target Incentive Submitted Successfully!");

                            $("#target_id").val("");
                            $("#staff_info_table tbody").empty();

                            swapTableAndCreation();
                            getTargetTable();
                        } else {
                            swalError("Error", response.message || "Something went wrong!");
                        }

                        $("#submit_target_incentive").prop("disabled", false);
                    },

                    error: function (xhr) {
                        console.log(xhr.responseText);
                        swalError("Error", "Something went wrong!");
                        $("#submit_target_incentive").prop("disabled", false);
                    }
                });
            }
        );
    });
    $(document).on("click", ".reviewBtn", async function () {
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
            let form = $("#target_incentive")[0];
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
            console.error("Target Incentive Error:", error);
            console.error("Error Message:", error.message);

            swalError("Failed to load target incentive details. Check console for details.");
        }
    });


});

$(function () {
    getTargetTable()
});

function getTargetTable() {
    serverSideTable("#target_incentive_table", " ", "api/target_incentive/get_target_incentive_list.php", " Target Incentive List")
}
function swapTableAndCreation() {
    if ($(".target_incentive_table_content").is(":visible")) {
        $(".target_incentive_table_content").hide();
        $("#target_incentive_content").show();
        $(".backBtn").show();
    } else {
        $(".target_incentive_table_content").show();
        $("#target_incentive_content").hide();
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
    let serialNo = $("#staff_info_table tbody tr").length + 1;

    let hasAchievedValue =
        staff.achieved_value !== null &&
        staff.achieved_value !== undefined &&
        String(staff.achieved_value).trim() !== "";

    let hasFinalIncentive =
        staff.final_incentive !== null &&
        staff.final_incentive !== undefined &&
        String(staff.final_incentive).trim() !== "";

    let finalIncentiveInput = "";

    if (!hasAchievedValue) {
        finalIncentiveInput = `<span class="text-danger">In Target Evaluation</span>`;
    } else if (hasFinalIncentive) {
        finalIncentiveInput = `
            <input type="number" class="form-control final_incentive"
                value="${staff.final_incentive}" readonly>
        `;
    } else {
        finalIncentiveInput = `
            <input type="number" class="form-control final_incentive"
                name="final_incentive[]" min="0" step="0.01"
                value="">
        `;
    }

    let row = `
        <tr data-staff-id="${staff.staff_id}"
            data-finalized="${hasFinalIncentive ? 1 : 0}"
            data-has-achieved="${hasAchievedValue ? 1 : 0}">
            <td class="serial-no">${serialNo}</td>
            <td>
                <input type="text" class="form-control"
                    value="${staff.staff_name || ''}" readonly>
                <input type="hidden" class="staff_id"
                    value="${staff.staff_id}">
                <input type="hidden" name="target_detail_id[]"
                    value="${staff.id}">
            </td>
            <td><input type="text" class="form-control" value="${staff.incentive_titles || ''}" readonly></td>
            <td><input type="text" class="form-control" value="${staff.target_counts ?? ''}" readonly></td>
            <td><input type="text" class="form-control" value="${staff.target_amounts ?? ''}" readonly></td>
            <td><input type="text" class="form-control" value="${staff.minimum_counts ?? ''}" readonly></td>
            <td><input type="text" class="form-control" value="${staff.minimum_amounts ?? ''}" readonly></td>
            <td><input type="text" class="form-control" value="${staff.payout_types || ''}" readonly></td>
            <td><input type="text" class="form-control" value="${staff.ctc_percentages ?? ''}" readonly></td>
            <td><input type="text" class="form-control" value="${staff.fixed_amounts ?? ''}" readonly></td>
            <td><input type="text" class="form-control" value="${staff.achievement_basis || ''}" readonly></td>
            <td><input type="text" class="form-control" value="${staff.achieved_value ?? ''}" readonly></td>
            <td><input type="text" class="form-control" value="${staff.incentive_amount ?? ''}" readonly></td>
            <td>${finalIncentiveInput}</td>
        </tr>
    `;

    $("#staff_info_table tbody").append(row);
}



