$(document).ready(function () {

    $("#company_name").on("change", function () {
        let company_id = $(this).val();
        getPerformanceTable(company_id)

    });
    $("#add_performance").click(function (event) {
        event.preventDefault();
        // Validation

        let company_name = $("#company_name").val();
        let criteria = $("#criteria").val();
        let target_performance = $("#target_performance").val();
        let weightage = $("#weightage").val();
        let effective_from = $("#effective_from").val();
        let performance_id = $("#performance_id").val();


        var data = ["company_name", "criteria", "target_performance", "weightage", "effective_from"];

        var isValid = true;
        data.forEach(function (entry) {
            var fieldIsValid = validateField($("#" + entry).val(), entry);
            if (!fieldIsValid) {
                isValid = false;
            }
        });

        if (isValid) {
            $.post(
                "api/performance_analysis/submit_performance.php",
                {
                    company_name,
                    criteria,
                    target_performance,
                    weightage,
                    effective_from,
                    performance_id

                },
                function (response) {
                    if (response == "3") {
                        // Catching the 100% weightage validation rule limit error
                        swalError("Warning", "Total weightage for this company cannot exceed 100%!");
                        $('#weightage').val('')
                    }
                    else if (response == "2") {
                        swalSuccess("Success", "Performance Analysis Added Successfully!");
                        getPerformanceTable();
                    } else if (response == "1") {
                        swalSuccess("Success", "Performance Analysis Updated Successfully!");
                        getPerformanceTable();
                    } else {
                        swalError("Error", "Error in Performance Analysis Table");
                    }
                  
                },
            );
        }
    });

    $(document).on("click", ".performanceActionBtn", function () {
        var id = $(this).attr("value"); // Get value attribute
        $.post(
            "api/performance_analysis/performance_analysis_data.php",
            { id: id },
            function (response) {
                $("#performance_id").val(id);
                $("#company_name").val(response[0].company_id);
                $("#criteria").val(response[0].criteria);
                $("#target_performance").val(response[0].target_perform);
                $("#weightage").val(response[0].weightage);
                $("#effective_from").val(response[0].effective_from);
            },
            "json",
        );
    });

    $(document).on("click", ".performanceDeleteBtn", function () {
        var id = $(this).attr("value");
        swalConfirm("Delete", "Do you want to Delete the Performance Details?", getPerformanceDelete, id,);
        return;
    });

})

$(function () {
    getCompanyName()
})
/* --- Get Company Name --- */
function getCompanyName() {
    $.ajax({
        url: "api/attendance_files/get_company_list.php",
        type: "POST",
        data: {},
        dataType: "json",
        cache: false,
        success: function (response) {
            let dropdown = $("#company_name");

            dropdown.empty(); // clear existing

            dropdown.append('<option value="">Select Company Name</option>');

            // assuming response is array of objects
            $.each(response, function (index, item) {
                dropdown.append(
                    `<option value="${item.id}">${item.company_name}</option>`,
                );
            });
        },
        error: function (xhr, status, error) {
            swalError("Error", status + error);
        },
    });
}

function getPerformanceTable() {
    let company_id = $("#company_name").val();

    $.post(
        "api/performance_analysis/get_performance_list.php",
        { company_id: company_id },
        function (response) {

            var columnMapping = [
                "sno",
                "criteria",
                "target_perform",
                "weightage",
                "effective_from",
                "action",
            ];

            appendDataToTable("#performance_table", response, columnMapping);

            // Calculate Total Weightage
            let totalWeightage = 0;

            response.forEach(function (row) {
                totalWeightage += parseFloat(row.weightage) || 0;
            });

            $("#total_weightage").text(totalWeightage + "%");

            setdtable("#performance_table", "Performance Analysis List");

            $("#performance_analysis input").val("");
            $("#performance_analysis input").css("border", "1px solid #cecece");
            $("#performance_analysis select").css("border", "1px solid #cecece");
        },
        "json"
    );
}


function getPerformanceDelete(id) {
    $.post(
        "api/performance_analysis/delete_performance_analysis.php",
        { id },
        function (response) {
            if (response == "1") {
                swalSuccess("Success", "Performance Data Deleted Successfully!");
                getPerformanceTable();
            } else if (response == "2") {
                swalError("Warning", "Performance Data Not Delete");
            } else {
                swalError("Warning", "Error occur While Delete Performance data.");
            }
        },
        "json",
    );
}