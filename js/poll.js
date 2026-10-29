$(document).ready(function () {
  /* --- Back Button Click --- */
  $(document).on("click", ".backBtn", async function () {
    swapTableAndCreation();
    clearFields();
  });

  /* --- Answer Poll Question --- */
  $(document).on("click", ".pollAnswerBtn", function () {
    var id = $(this).attr("value");

    $.post(
        "api/poll_answer_files/poll_answer_data.php",
        { id: id },
        function (response) {
            swapTableAndCreation();

            $("#poll_titles_id").val(id);
            $("#selection_type").val(response[0].selection_type);

            $("#poll_question").text("1. " + response[0].poll_title);

            polloptions();
        },
        "json"
    );
});


  /* --- Submit poll answer --- */
 // Enforce single-selection behavior if selection_type is 1
$(document).on("change", "input[name='poll_option']", function () {
    if ($(this).data("selection-type") == "1") {
        $("input[name='poll_option']").not(this).prop("checked", false);
    }
});

// Submit Click Handler
$("#submit_poll_question").click(function (event) {
    event.preventDefault();

    let poll_titles_id = $("#poll_titles_id").val();
    let selection_type = $("#selection_type").val();
    let reason = $("#reason").val().trim();

    let poll_value;
    if (selection_type == "1") {
        poll_value = $("input[name='poll_option']:checked").val();
    } else {
        // Gather all checked checkboxes into an array and join them as a comma-separated string
        let selectedOptions = [];
        $("input[name='poll_option[]']:checked").each(function () {
            selectedOptions.push($(this).val());
        });
        poll_value = selectedOptions.length > 0 ? selectedOptions.join(",") : undefined;
    }

    // Validation
    if (!poll_value) {
        swalError("Warning", "Please select at least one poll option.");
        return;
    }

    swalConfirm(
        "Are you sure?",
        "Do you want to submit this poll?",
        function () {
            $.post(
                "api/poll_answer_files/submit_poll_answer.php",
                {
                    poll_titles_id: poll_titles_id,
                    poll_value: poll_value,
                    reason: reason,
                },
                function (response) {
                    if (response == "1") {
                        swalSuccess("Success", "Poll Submitted Successfully!");
                    } else {
                        swalError("Error", "Something went wrong!");
                    }

                    pollTable();
                    swapTableAndCreation();
                    clearFields();
                }
            );
        }
    );
});

//DOcument End
});

/* --- On Load --- */
$(function () {
  pollTable();
});

/* --- Swap Table and hide/show --- */
function swapTableAndCreation() {
  if ($(".poll_table_content").is(":visible")) {
    $(".poll_table_content").hide();
    $("#poll_question_content").show();
    $(".backBtn").show();
  } else {
    $(".poll_table_content").show();
    $("#poll_question_content").hide();
    $(".backBtn").hide();
  }
}

/* --- Get poll table data --- */
function pollTable() {
  $.post(
    "api/poll_answer_files/poll_answer_list.php",
    function (response) {
      var columnMapping = ["sno", "poll_title","selection_type", "status", "action"];
      appendDataToTable("#poll_table", response, columnMapping);
      setdtable("#poll_table", "Poll List");
    },
    "json",
  );
}

/* --- Get poll options --- */
function polloptions() {
    let poll_titles_id = $("#poll_titles_id").val();
    let selection_type = $("#selection_type").val();

    $.post(
        "api/poll_answer_files/poll_options_list.php",
        { poll_titles_id: poll_titles_id },
        function (response) {
            let optionHTML = "";
            // Always use checkbox as inputType so the design stays uniform
            let inputType = "checkbox"; 
            let inputName = selection_type == "1" ? "poll_option" : "poll_option[]";

            response.forEach(function (option, index) {
                let optionNumber = index + 1;

                optionHTML += `
                    <div class="poll-option-wrapper">
                        <input
                            type="${inputType}"
                            id="opt${option.id}"
                            name="${inputName}"
                            value="${option.id}"
                            data-selection-type="${selection_type}">

                        <label
                            for="opt${option.id}"
                            class="poll-option-label">

                            <span class="opt-num">${optionNumber}</span>
                            <div class="checkbox-square"></div>
                            <span class="opt-text">${option.poll_options}</span>
                        </label>
                    </div>
                `;
            });

            $("#poll_options_container").html(optionHTML);
        },
        "json"
    );
}


/* --- Poll Input Clear Fields --- */
function clearFields() {
  $("input[name='poll_option']").prop("checked", false);
  $("#reason").val("");
  $("#poll_titles_id").val("");
}
