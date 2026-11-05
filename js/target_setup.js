
const BranchChoices = new Choices('#branch_name', {
    removeItemButton: true,
    noChoicesText: 'No branches available',
    allowHTML: true,
    placeholder: true,
    placeholderValue: 'Select Branch Name',
    shouldSort: false
});

const DepartmentChoices = new Choices('#department_name', {
    removeItemButton: true,
    noChoicesText: 'No departments available',
    allowHTML: true,
    placeholder: true,
    placeholderValue: 'Select Department',
    shouldSort: false
});

$(document).ready(function () {

    $(document).on("click", "#add_target, #back_btn", function () {
        $("#submit_target_setup").prop("disabled", false);
        swapTableAndCreation();
        clearFields();
    });

    $("#company_name").on("change", async function () {
        let company_id = $(this).val();

        $("#staff_info_table tbody").empty();
        targetStaffList = [];

        if (company_id) {
            await Promise.all([
                getBranchNameDropdown({ company_id }),
                getDepartmentNameDropdown({ company_id })
            ]);
        } else {
            BranchChoices.clearStore();
            DepartmentChoices.clearStore();
        }
    });

    $("#branch_name, #department_name").on("change", function () {
        loadTargetStaff();
    });

    $(document).on("change", ".payout_type", function () {
        let row = $(this).closest("tr");
        let payout = $(this).val();

        if (payout === "CTC") {
            row.find(".ctc_percentage").prop("readonly", false);
            row.find(".fixed_amount").prop("readonly", true).val("");
        } else if (payout === "Fixed") {
            row.find(".ctc_percentage").prop("readonly", true).val("");
            row.find(".fixed_amount").prop("readonly", false);
        } else {
            row.find(".ctc_percentage, .fixed_amount")
                .prop("readonly", true).val("");
        }
    });
    // Add new row from the clicked row
    $(document).on("click", ".add_staff_row", function () {
        addStaffRow();
    });

    // Delete selected row
    $(document).on("click", ".delete_staff_row", function () {
        $(this).closest("tr").remove();
        updateStaffSerialNo();
    });

    $(document).on("input change", 'input[name="target_count[]"], input[name="target_amount[]"], input[name="minimum_count[]"], input[name="minimum_amount[]"]', function () {

        let row = $(this).closest("tr");

        let targetCount = row.find('[name="target_count[]"]');
        let minimumCount = row.find('[name="minimum_count[]"]');
        let targetAmount = row.find('[name="target_amount[]"]');
        let minimumAmount = row.find('[name="minimum_amount[]"]');

        let hasCount = targetCount.val().trim() !== "";
        let hasAmount = targetAmount.val().trim() !== "";

        if (this.name === "target_count[]") {
            if (hasCount) {
                targetAmount.val("").prop("readonly", true);
                minimumAmount.val("").prop("readonly", true);
            } else {
                targetAmount.prop("readonly", false);
                minimumAmount.prop("readonly", false);
            }
        }

        if (this.name === "target_amount[]") {
            if (hasAmount) {
                targetCount.val("").prop("readonly", true);
                minimumCount.val("").prop("readonly", true);
            } else {
                targetCount.prop("readonly", false);
                minimumCount.prop("readonly", false);
            }
        }

        // Minimum Count Validation
        let targetCountValue = parseFloat(targetCount.val()) || 0;
        let minimumCountValue = parseFloat(minimumCount.val()) || 0;

        if (minimumCountValue > targetCountValue && hasCount) {
            minimumCount.val("");
            Swal.fire("Warning", "Minimum Count should not be greater than Target Count.", "warning");
        }

        // Minimum Amount Validation
        let targetAmountValue = parseFloat(targetAmount.val()) || 0;
        let minimumAmountValue = parseFloat(minimumAmount.val()) || 0;

        if (minimumAmountValue > targetAmountValue && hasAmount) {
            minimumAmount.val("");
            Swal.fire("Warning", "Minimum Amount should not be greater than Target Amount.", "warning");
        }
    });


    $("#target_setup").on("submit", function (event) {
        event.preventDefault();

        let company_id = $("#company_name").val();
        let branch_id = $("#branch_name").val() || [];
        let department_id = $("#department_name").val() || [];
        let effective_month = $("#effective_month").val();
        let target_id = $("#target_id").val();

        if (!company_id) {
            Swal.fire("Warning", "Please select Company Name.", "warning");
            return false;
        }

        if (branch_id.length === 0 && department_id.length === 0) {
            Swal.fire("Warning", "Please select at least one Branch or Department.", "warning");
            return false;
        }

        if (!effective_month) {
            Swal.fire("Warning", "Please select Effective Month.", "warning");
            return false;
        }

        let rows = $("#staff_info_table tbody tr").filter(function () {
            return $(this).find(".staff_id").length > 0;
        });

        if (rows.length === 0) {
            Swal.fire("Warning", "Please add at least one staff row.", "warning");
            return false;
        }

        let isValid = true;
        let errorMessage = "";

        rows.each(function (index) {

            let row = $(this);

            let staff_id = row.find(".staff_id").val();
            let incentive_title = row.find('[name="incentive_title[]"]').val().trim();
            let target_count = row.find('[name="target_count[]"]').val();
            let target_amount = row.find('[name="target_amount[]"]').val();
            let minimum_count = row.find('[name="minimum_count[]"]').val();
            let minimum_amount = row.find('[name="minimum_amount[]"]').val();
            let payout_type = row.find(".payout_type").val();
            let ctc_percentage = row.find(".ctc_percentage").val();
            let fixed_amount = row.find(".fixed_amount").val();

            let rowNumber = index + 1;

            if (!staff_id) {
                errorMessage = "Please select Staff Name in row " + rowNumber + ".";
                isValid = false;
                return false;
            }

            if (!incentive_title) {
                errorMessage = "Please enter Incentive Title in row " + rowNumber + ".";
                isValid = false;
                return false;
            }

            if (!target_count && !target_amount) {
                errorMessage = "Please enter either Target Count or Target Amount in row " + rowNumber + ".";
                isValid = false;
                return false;
            }

            if (!minimum_count && !minimum_amount) {
                errorMessage = "Please enter either Minimum Count or Minimum Amount in row " + rowNumber + ".";
                isValid = false;
                return false;
            }

            if (!payout_type) {
                errorMessage = "Please select Incentive Payout in row " + rowNumber + ".";
                isValid = false;
                return false;
            }

            if (payout_type === "CTC" && (!ctc_percentage || parseFloat(ctc_percentage) <= 0 || parseFloat(ctc_percentage) > 100)) {
                errorMessage = "Please enter a valid CTC Percentage in row " + rowNumber + ".";
                isValid = false;
                return false;
            }

            if (payout_type === "Fixed" && (!fixed_amount || parseFloat(fixed_amount) <= 0)) {
                errorMessage = "Please enter a valid Fixed Amount in row " + rowNumber + ".";
                isValid = false;
                return false;
            }

        });

        if (!isValid) {
            Swal.fire("Warning", errorMessage, "warning");
            return false;
        }

        swalConfirm(
            "Are you sure?",
            "Do you want to submit this Target Setup?",
            function () {

                let formData = new FormData($("#target_setup")[0]);

                formData.append("company_id", company_id);
                formData.append("effective_month", effective_month);
                formData.append("target_id", target_id);
                formData.append("branch_id", branch_id.join(","));
                formData.append("department_id", department_id.join(","));

                $("#submit_target_setup").prop("disabled", true);

                $.ajax({
                    url: "api/target_setup/submit_target_info.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    cache: false,
                    dataType: "json",

                    success: function (response) {

                        if (response.result == 0) {
                            swalError("Error", response.message || "Target Info Not Added!");
                        } else if (response.result == 1) {
                            swalSuccess("Success", "Target Info Added Successfully!");
                            $("#target_id").val('');
                            $("#target_setup")[0].reset();

                            BranchChoices.removeActiveItems();
                            DepartmentChoices.removeActiveItems();

                            $("#staff_info_table tbody").empty();

                            swapTableAndCreation();
                            getTargetTable();
                        } else {
                            swalError("Error", response.message || "Something went wrong!");
                        }

                        $("#submit_target_setup").prop("disabled", false);
                    },

                    error: function (xhr) {
                        console.log(xhr.responseText);
                        swalError("Error", "Something went wrong!");
                        $("#submit_target_setup").prop("disabled", false);
                    }
                });
            }
        );
    });


    $(document).on("click", ".targetSetupActionBtn", async function () {
        let id = $(this).attr("value");

        try {
            const response = await $.ajax({
                url: "api/target_setup/target_setup_creation_data.php",
                type: "POST",
                data: { id: id },
                dataType: "json"
            });

            if (!response || !response.id) {
                swalError("Target setup details not found.");
                return;
            }

            swapTableAndCreation();

            // Clear existing data
            $("#target_setup")[0].reset();
            $("#staff_info_table tbody").empty();

            // Master Data
            await getCompanyName();

            $("#target_id").val(response.id);
            $("#company_name").val(response.company_id).trigger("change");
            $("#effective_month").val(response.effective_month);

            // Restore Branch and Department selections
            let branchIds = (response.branch_ids || []).map(String);
            let departmentIds = (response.department_ids || []).map(String);

            $("#branch_name2").val(branchIds.join(","));
            $("#department_name2").val(departmentIds.join(","));

            // Load dropdowns with selected IDs
            await getDepartmentNameDropdown({
                company_id: response.company_id,
                selected_ids: departmentIds
            });

            await getBranchNameDropdown({
                company_id: response.company_id,
                selected_ids: branchIds
            });

            // Load Staff based on selected filters
            await loadTargetStaff(false);

            // Populate saved staff details
            $("#staff_info_table tbody").empty();

            if (response.staff_details && response.staff_details.length > 0) {

                response.staff_details.forEach(function (staff) {

                    addStaffRow();

                    let row = $("#staff_info_table tbody tr:last");

                    row.find(".staff_id")
                        .val(String(staff.staff_id))
                        .trigger("change");

                    row.find('[name="incentive_title[]"]').val(staff.incentive_titles);
                    row.find('[name="target_count[]"]').val(staff.target_counts);
                    row.find('[name="target_amount[]"]').val(staff.target_amounts);
                    row.find('[name="minimum_count[]"]').val(staff.minimum_counts);
                    row.find('[name="minimum_amount[]"]').val(staff.minimum_amounts);
                    // Restore Count / Amount disabling condition
                    let targetCount = row.find('[name="target_count[]"]');
                    let minimumCount = row.find('[name="minimum_count[]"]');
                    let targetAmount = row.find('[name="target_amount[]"]');
                    let minimumAmount = row.find('[name="minimum_amount[]"]');

                    let countValue = (targetCount.val() || "").toString().trim();
                    let amountValue = (targetAmount.val() || "").toString().trim();

                    if (countValue !== "") {
                        targetAmount.val("").prop("readonly", true);
                        minimumAmount.prop("readonly", true);
                        targetCount.prop("readonly", false);
                        minimumCount.prop("readonly", false);

                    } else if (amountValue !== "") {
                        targetCount.val("").prop("readonly", true);
                        minimumCount.prop("readonly", true);
                        targetAmount.prop("readonly", false);
                        minimumAmount.prop("readonly", false);

                    } else {
                        targetCount.prop("readonly", false);
                        minimumCount.prop("readonly", false);
                        targetAmount.prop("readonly", false);
                        minimumAmount.prop("readonly", false);
                    }

                    row.find('[name="payout_type[]"]')
                        .val(staff.payout_types)
                        .trigger("change");

                    if (staff.payout_types === "CTC") {
                        row.find('[name="ctc_percentage[]"]')
                            .val(staff.ctc_percentages)
                            .prop("readonly", false);

                        row.find('[name="fixed_amount[]"]')
                            .val("")
                            .prop("readonly", true);

                    } else if (staff.payout_types === "Fixed") {
                        row.find('[name="fixed_amount[]"]')
                            .val(staff.fixed_amounts)
                            .prop("readonly", false);

                        row.find('[name="ctc_percentage[]"]')
                            .val("")
                            .prop("readonly", true);
                    }
                });

                updateStaffSerialNo();

            } else {
                addStaffRow();
            }
            // Check whether any staff evaluation has started
            let isEvaluated = (response.staff_details || []).some(function (staff) {
                return staff.achieved_value !== null &&
                    staff.achieved_value !== undefined &&
                    String(staff.achieved_value).trim() !== "";
            });

            // Disable Submit if any staff evaluation has started
            $("#submit_target_setup")
                .prop("disabled", isEvaluated);

        } catch (error) {
            console.error("Failed to fetch target setup data:", error);
            swalError("Failed to load target setup details.");
        }
    });
    /* --- Delete Target Setup --- */
    $(document).on("click", ".targetSetupDeleteBtn", function () {
        var id = $(this).attr("value");
        swalConfirm(
            "Delete",
            "Do you want to Delete the Target Setup?",
            getTargetSetupDelete,
            id,
        );
        return;
    });




});

$(function () {
    getTargetTable()
});

function getTargetTable() {
    serverSideTable("#target_setup_table", " ", "api/target_setup/get_target_setup_list.php", " Target Setup Staff List")
}
function swapTableAndCreation() {
    if ($(".target_setup_table_content").is(":visible")) {
        $(".target_setup_table_content").hide();
        $(".addtargetBtn").hide();
        $("#target_setup_content").show();
        $(".backBtn").show();

        getCompanyName();
    } else {
        $(".target_setup_table_content").show();
        $(".addtargetBtn").show();
        $("#target_setup_content").hide();
        $(".backBtn").hide();
    }
}

async function getCompanyName() {
    try {
        const response = await $.ajax({
            url: "api/attendance_files/get_company_list.php",
            type: "POST",
            dataType: "json"
        });

        let dropdown = $("#company_name");

        dropdown.empty();
        dropdown.append('<option value="">Select Company Name</option>');

        $.each(response, function (index, item) {
            dropdown.append(
                `<option value="${item.id}">${item.company_name}</option>`
            );
        });

    } catch (err) {
        console.error("Error loading company dropdown:", err);
    }
}

async function getDepartmentNameDropdown({ company_id }) {
    try {
        const selectedValue = $('#department_name2').val();

        const response = await $.ajax({
            url: "api/company_creation_files/get_department_name_dropdown.php",
            type: "POST",
            data: {
                company_id: company_id,
                screen: "feedback_screen"
            },
            dataType: "json"
        });

        DepartmentChoices.clearStore();

        const selectedIds = selectedValue ? selectedValue.split(",") : [];

        const items = response.map((val) => ({
            value: val.id.toString(),
            label: val.department_name,
            selected: selectedIds.includes(val.id.toString()),
            disabled: val.disabled && !selectedIds.includes(val.id.toString())
        }));

        DepartmentChoices.setChoices(items, "value", "label", true);

    } catch (err) {
        console.error("Error loading department dropdown:", err);
    }
}

async function getBranchNameDropdown({ company_id }) {
    try {
        const selectedValue = $('#branch_name2').val();

        const response = await $.ajax({
            url: "api/staff_creation/company_mapped_branches.php",
            type: "POST",
            data: {
                company_id: company_id
            },
            dataType: "json"
        });

        BranchChoices.clearStore();

        const selectedIds = selectedValue ? selectedValue.split(",") : [];

        const items = response.map((val) => ({
            value: val.id.toString(),
            label: val.branch_name,
            selected: selectedIds.includes(val.id.toString()),
            disabled: val.disabled && !selectedIds.includes(val.id.toString())
        }));

        BranchChoices.setChoices(items, "value", "label", true);

    } catch (err) {
        console.error("Error loading branch dropdown:", err);
    }
}
async function loadTargetStaff(addDefaultRow = true) {

    let company_id = $("#company_name").val();

    let branch_id = $("#branch_name").val() || [];
    let dept_id = $("#department_name").val() || [];

    $("#staff_info_table tbody").empty();
    targetStaffList = [];

    if (!company_id) {
        return;
    }

    try {
        const response = await $.ajax({
            url: "api/target_setup/get_target_staff.php",
            type: "POST",
            dataType: "json",
            data: {
                company_id: company_id,
                branch_id: branch_id,
                dept_id: dept_id
            }
        });

        targetStaffList = response || [];

        if (targetStaffList.length > 0) {

            if (addDefaultRow) {
                addStaffRow();
            }

        } else {
            $("#staff_info_table tbody").html(
                '<tr class="no-staff-row"><td colspan="11" class="text-center">No staff found</td></tr>'
            );
        }

    } catch (error) {
        console.error("Error loading staff:", error);
    }
}


function addStaffRow() {

    if (targetStaffList.length === 0) {
        return;
    }

    let tbody = $("#staff_info_table tbody");
    tbody.find(".no-staff-row").remove();

    let row = $("<tr>");

    let staffSelect = $('<select class="form-control staff_id" name="staff_id[]" required></select>');
    staffSelect.append('<option value="">Select Staff</option>');

    $.each(targetStaffList, function (index, staff) {
        staffSelect.append(
            $("<option>", {
                value: staff.id,
                text: staff.staff_name
            })
        );
    });

    row.append('<td class="sno"></td>');
    row.append($("<td>").append(staffSelect));

    row.append(`
        <td><input type="text" class="form-control" name="incentive_title[]" placeholder="Incentive Title" required></td>
        <td><input type="number" class="form-control" name="target_count[]" min="0" placeholder="Target Count"></td>
        <td><input type="number" class="form-control" name="target_amount[]" min="0" step="0.01" placeholder="Target Amount"></td>
        <td><input type="number" class="form-control" name="minimum_count[]" min="0" placeholder="Minimum Count"></td>
        <td><input type="number" class="form-control" name="minimum_amount[]" min="0" step="0.01" placeholder="Minimum Amount"></td>
        <td>
            <select class="form-control payout_type" name="payout_type[]" required>
                <option value="">Select Payout</option>
                <option value="CTC">CTC</option>
                <option value="Fixed">Fixed</option>
            </select>
        </td>
        <td><input type="number" class="form-control ctc_percentage" name="ctc_percentage[]" min="0" max="100" step="0.01" placeholder="CTC %" readonly></td>
        <td><input type="number" class="form-control fixed_amount" name="fixed_amount[]" min="0" step="0.01" placeholder="Fixed Amount" readonly></td>
        <td>
            <div class="d-flex" style="gap:5px;">
                <button type="button" class="btn btn-success btn-sm add_staff_row" title="Add Row">
                    <i class="fa fa-plus"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm delete_staff_row" title="Delete Row">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </td>
    `);

    tbody.append(row);
    updateStaffSerialNo();
}

// Update serial numbers
function updateStaffSerialNo() {
    $("#staff_info_table tbody tr").each(function (index) {
        $(this).find(".sno").text(index + 1);
    });
}

function clearFields() {
    $("#target_setup")[0].reset();
    $("#target_id").val('');
    $("#branch_name2").val('');
    $("#department_name2").val('');
    BranchChoices.removeActiveItems();
    BranchChoices.clearStore();
    DepartmentChoices.removeActiveItems();
    DepartmentChoices.clearStore();
    $("#staff_info_table tbody").empty();
}

function getTargetSetupDelete(id) {
    $.post(
        "api/target_setup/delete_target_setup.php",
        { id: id },
        function (response) {
            if (response == "1") {
                swalSuccess("Success", "Target Setup Deleted Successfully!");
                getTargetTable();
            } else {
                swalError("Warning", "Unable to delete Target Setup. Evaluation may have already started.");
            }
        },
        "json"
    );
}