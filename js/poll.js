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

        // Set text inside <p>
        $("#poll_question").text("1. " + response[0].poll_title);
        polloptions();
      },
      "json",
    );
  });

  /* --- Submit poll answer --- */
  $("#submit_poll_question").click(function (event) {
    event.preventDefault();

    let poll_titles_id = $("#poll_titles_id").val();
    let poll_value = $("input[name='poll_option']:checked").val();
    let reason = $("#reason").val().trim();

    // Validation
    if (!poll_value) {
      swalError("Warning", "Please select a poll option.");
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
          },
        );
      },
    );
  });
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
      var columnMapping = ["sno", "poll_title", "status", "action"];
      appendDataToTable("#poll_table", response, columnMapping);
      setdtable("#poll_table", "Poll List");
    },
    "json",
  );
}

/* --- Get poll options --- */
function polloptions() {
  let poll_titles_id = $("#poll_titles_id").val();

  $.post(
    "api/poll_answer_files/poll_options_list.php",
    { poll_titles_id: poll_titles_id },

    function (response) {
      let optionHTML = "";

      response.forEach(function (option, index) {
        let optionNumber = index + 1;

        optionHTML += `

                    <div class="poll-option-wrapper">

                        <input 
                            type="radio"
                            id="opt${option.id}"
                            name="poll_option"
                            value="${option.id}">

                        <label 
                            for="opt${option.id}"
                            class="poll-option-label">

                            <span class="opt-num">
                                ${optionNumber}
                            </span>

                            <div class="radio-circle"></div>

                            <span class="opt-text">
                                ${option.poll_options}
                            </span>

                        </label>

                    </div>

                `;
      });

      $("#poll_options_container").html(optionHTML);
    },
    "json",
  );
}

/* --- Poll Input Clear Fields --- */
function clearFields() {
  $("input[name='poll_option']").prop("checked", false);
  $("#reason").val("");
  $("#poll_titles_id").val("");
}
