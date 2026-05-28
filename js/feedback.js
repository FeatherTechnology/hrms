$(document).ready(function () {
  $("input[name=feedback_type]").click(function () {
    let feed_type = $(this).val();
    if (feed_type == "0") {
      $("#gendral_feedback_div").show();
      $(".scheduled_feedback_div").hide();
      $(".scheduled_feedback_ans_div").hide();
      getfeedbackname();
    } else if (feed_type == "1") {
      $("#gendral_feedback_div").hide();
      $(".scheduled_feedback_div").show();
      $(".scheduled_feedback_ans_div").hide();
      getSchFeedbackList();
    }
  });

  $("#submit_gen_feedback").click(function (event) {
    event.preventDefault();
    //Validation
    let feedback_name = $("#feedback_name").val();
    let commants = $("#commants").val();
    let attachment = $("#attachment")[0].files[0];

    var data = ["feedback_name", "commants"];
    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });
    if (isValid) {
      let kycDetail = new FormData();

      kycDetail.append("feedback_name", feedback_name);
      kycDetail.append("commants", commants);
      kycDetail.append("attachment", attachment);

      $.ajax({
        url: "api/feedback/submit_gen_feedback.php",
        type: "post",
        data: kycDetail,
        contentType: false,
        processData: false,
        cache: false,
        success: function (response) {
          if ((response = "1")) {
            swalSuccess("Success", "Feedback Added Successfully!");
            $("#feedback_name").val("");
            $("#commants").val("");
            $("#attachment").val("");
            $('input[name="feedback_type"][value="0"]')
              .prop("checked", true)
              .trigger("click");
          } else {
            swalError("Error", "Error in table");
          }
          getKycTable();
        },
      });
    }
  });

  $("#back_btn").click(function () {
    $('input[name="feedback_type"][value="1"]')
      .prop("checked", true)
      .trigger("click");
  });

  $(document).on("click", ".ratingsAnswerBtn", function () {
    $(".scheduled_feedback_div").hide();
    $(".scheduled_feedback_ans_div").show();

    let feedback_configuration_id = $(this).val();

    $.post(
      "api/feedback/get_feedback_questions.php",
      {
        feedback_configuration_id: feedback_configuration_id,
      },
      function (response) {
        let html = "";

        //////////////////////////////////////////////////////
        // TITLE
        //////////////////////////////////////////////////////

        html += `
                <div class="mb-4">
                    <h3 class="">
                        1 . ${response.feedback_title}
                    </h3>
                </div>
            `;

        //////////////////////////////////////////////////////
        // QUESTIONS
        //////////////////////////////////////////////////////

        response.questions.forEach(function (row) {
          html += `

                    <div class="border rounded p-3 mb-3" style="background-color: white;">

                        <div class="row align-items-center">

                            <div class="col-md-1" style="display:flex;justify-content:center;align-items: center;">
                                <b>${row.sno}</b>
                            </div>

                            <div class="col-md-5" style="display:flex;justify-content:center;align-items: center;">
                                ${row.feedback_questions}
                            </div>

                            <div class="col-md-6" style="display:flex;justify-content:center;align-items: center;">

                              <textarea class="form-control feedback_answer" data-question-id="${row.id}" rows="3"  placeholder="Enter your answer" style="resize:none;"></textarea>

                            </div>

                        </div>

                    </div>

                `;
        });

        //////////////////////////////////////////////////////
        // SUBMIT BUTTON
        //////////////////////////////////////////////////////

        html += `

                <div class="text-end" style="display: flex;justify-content: right;">

                    <button 
                        class="btn btn-primary submitFeedbackBtn"
                        value="${feedback_configuration_id}">
                        Submit
                    </button>

                </div>

            `;

        $(".feedbackQuestionDiv").html(html);
      },
      "json",
    );
  });

  $(document).on("click", ".submitFeedbackBtn", function () {
    let feedback_configuration_id = $(this).val();

    let answerArr = [];

    //////////////////////////////////////////////////////
    // GET ALL ANSWERS
    //////////////////////////////////////////////////////

    $(".feedback_answer").each(function () {
      let question_id = $(this).attr("data-question-id");

      let answer = $(this).val();

      answerArr.push({
        question_id: question_id,
        answer: answer,
      });
    });

    //////////////////////////////////////////////////////
    // INSERT
    //////////////////////////////////////////////////////

    $.post(
      "api/feedback/submit_sch_feedback.php",
      {
        feedback_configuration_id: feedback_configuration_id,
        answerArr: answerArr,
      },
      function (response) {
        if (response == 1) {
          swalSuccess("Success", "Feedback Answer Submited Successfully.");
          $('input[name="feedback_type"][value="1"]')
            .prop("checked", true)
            .trigger("click");

          $(".feedbackQuestionDiv").html("");
        } else {
          alert("Failed");
        }
      },
    );
  });
});

$(function () {
  $('input[name="feedback_type"][value="0"]')
    .prop("checked", true)
    .trigger("click");
  // getfeedbackname();
});

function getfeedbackname() {
  $.post(
    "api/feedback/get_gen_feedback_name.php",
    {},
    function (response) {
      $("#feedback_name").empty();
      $("#feedback_name").append(
        "<option value=''>Select Feed Back Tittle</option>",
      );

      $.each(response, function (index, val) {
        $("#feedback_name").append(
          "<option value='" +
            val["id"] +
            "'>" +
            val["feedback_name"] +
            "</option>",
        );
      });
    },
    "json",
  );
}

function getSchFeedbackList() {
  $.post(
    "api/feedback/get_scheduled_feedback_list.php",
    function (response) {
      var columnMapping = ["sno", "feedback_title", "status", "action"];
      appendDataToTable("#scheduled_feedback_table", response, columnMapping);
      setdtable("#scheduled_feedback_table", "Scheduled Feedback List");
    },
    "json",
  );
}
