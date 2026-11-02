$(document).ready(function () {
  $("input[name=feedback_type]").click(function () {
    let feed_type = $(this).val();
    if (feed_type == "0") {
      $("#general_feedback_div").show();
      $(".scheduled_feedback_div").hide();
      $(".scheduled_feedback_ans_div").hide();
      getfeedbackname();
      getUserNames();
    } else if (feed_type == "1") {
      $("#general_feedback_div").hide();
      $(".scheduled_feedback_div").show();
      $(".scheduled_feedback_ans_div").hide();
      getSchFeedbackList();
    }
  });

  
$("#submit_gen_feedback").click(function (event) {
    event.preventDefault();

    let feedback_name = $("#feedback_name").val();
    let commants = $("#commants").val();
    let attachments = $("#attachment")[0].files;
    let user_name = $("#user_name").val();

    var data = ["feedback_name", "commants", "user_name"];
    var isValid = true;

    data.forEach(function (entry) {
        var fieldIsValid = validateField($("#" + entry).val(), entry);
        if (!fieldIsValid) {
            isValid = false;
        }
    });

    if (isValid) {
        swalConfirm(
            "Are you sure?",
            "Do you want to submit this Feedback?",
            function () {
                let kycDetail = new FormData();

                kycDetail.append("feedback_name", feedback_name);
                kycDetail.append("commants", commants);
                kycDetail.append("user_name", user_name);

                for (let i = 0; i < attachments.length; i++) {
                    kycDetail.append("attachment[]", attachments[i]);
                }

                $.ajax({
                    url: "api/feedback/submit_gen_feedback.php",
                    type: "POST",
                    data: kycDetail,
                    contentType: false,
                    processData: false,
                    cache: false,
                    success: function (response) {
                        let result = response;

                        try {
                            result = JSON.parse(response);
                        } catch (e) {}

                        if (result == 1 || result == "1") {
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
                    },
                    error: function () {
                        swalError("Error", "Unable to submit feedback.");
                    }
                });
            }
        );
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
        // TITLE
        html += `
                <div class="mb-4">
                    <h3 class="">
                        1 . ${response.feedback_title}
                    </h3>
                </div>
            `;
        // QUESTIONS
        response.questions.forEach(function (row) {
          html += `
                    <div class="border rounded p-3 mb-3 question-row" style="background-color: white;" data-question-id="${row.id}">
                        <div class="row align-items-center">

                            <div class="col-md-1" style="display:flex;justify-content:center;align-items: center;">
                                <b>${row.sno}</b>
                            </div>

                            <div class="col-md-4" style="display:flex;justify-content:center;align-items: center;">
                                ${row.feedback_questions}
                            </div>

                            <div class="col-md-4" style="display:flex;justify-content:center;align-items: center;">
                              <textarea class="form-control feedback_answer" rows="3" placeholder="Enter your answer" style="resize:none;"></textarea>
                            </div>

                            <div class="col-md-3" style="display:flex;flex-direction:column;justify-content:center;">
                             <label class="form-label mb-1" style="font-size:12px;color:#6c757d;">Attachments</label>
                              <input type="file" class="form-control form-control-sm feedback_attachment" name="attachment[]" multiple
                               accept="image/*,.pdf,.doc,.docx,audio/*">
                            </div>

                        </div>
                    </div>
                `;
        });
        // SUBMIT BUTTON
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
    let formData = new FormData();

    formData.append("feedback_configuration_id", feedback_configuration_id);

    let isValid = true;

    $(".question-row").each(function (index) {
        let question_id = $(this).attr("data-question-id");
        let answer = $(this).find(".feedback_answer").val().trim();
        let files = $(this).find(".feedback_attachment")[0].files;

        if (answer === "") {
            isValid = false;
            $(this).find(".feedback_answer").addClass("is-invalid");
        } else {
            $(this).find(".feedback_answer").removeClass("is-invalid");
        }

        formData.append(`answerArr[${index}][question_id]`, question_id);
        formData.append(`answerArr[${index}][answer]`, answer);

        for (let i = 0; i < files.length; i++) {
            formData.append(
                `answerArr[${index}][attachment][]`,
                files[i]
            );
        }
    });

    if (!isValid) {
        swalError("Validation", "Please answer all questions before submitting.");
        return false;
    }

    swalConfirm(
        "Are you sure?",
        "Do you want to submit this Feedback?",
        function () {
            $.ajax({
                url: "api/feedback/submit_sch_feedback.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    response = String(response).trim();

                    if (response === "1") {
                        swalSuccess(
                            "Success",
                            "Feedback Answer Submitted Successfully."
                        );

                        $('input[name="feedback_type"][value="1"]')
                            .prop("checked", true)
                            .trigger("click");

                        $(".feedbackQuestionDiv").html("");
                    } else {
                        alert("Failed to submit feedback.");
                    }
                },
                error: function () {
                    alert("Something went wrong while submitting feedback.");
                }
            });
        }
    );
});

//Document End
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
        "<option value=''>Select Feedback Tittle</option>",
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

// <--- Get User Name List Based on feedback access yes --->
function getUserNames() {
  $.post(
    "api/feedback/get_user_names.php",
    {},
    function (response) {
      $("#user_name").empty();
      $("#user_name").append("<option value=''>Select User Name</option>");

      $.each(response, function (index, val) {
        $("#user_name").append(
          "<option value='" + val["id"] + "'>" + val["user_name"] + "</option>",
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
