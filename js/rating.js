$(document).ready(function () {
  /* --- Back Button Click --- */
  $(document).on("click", ".backBtn", async function () {
    swapTableAndCreation();
    clearFields();
  });

  /* --- Answer Ratings Question Start --- */
  $(document).on("click", ".ratingsAnswerBtn", function () {
    var id = $(this).attr("value");

    $.post(
      "api/ratings_answer_files/ratings_data.php",
      { id: id },

      function (response) {
        swapTableAndCreation();

        $("#rating_titles_id").val(id);

        /* --- Set text inside <p> --- */
        $("#rating_question").text("1. " + response[0].rating_title);
      },
      "json",
    );
  });

  /* --- On Change For Rating Button Text --- */
$(document).on("change", "input[name='ratingOption']", function () {
    let rating = parseInt($(this).val(), 10);

    $("#ratings").val(rating);

    $("#ratings_badge")
        .removeClass(
            "rating-1 rating-2 rating-3 rating-4 rating-5 " +
            "rating-6 rating-7 rating-8 rating-9 rating-10"
        )
        .addClass("rating-" + rating)
        .text(rating);
});

  /* --- Submit ratings answer --- */
  $("#submit_rating_question").click(function (event) {
    event.preventDefault();

    /* --- Values --- */
    let rating_titles_id = $("#rating_titles_id").val();
    let rating_value = $("input[name='ratingOption']:checked").val();
    let reason = $("#reason").val().trim();

    /* --- Validation --- */
    if (!rating_value) {
      swalError("Warning", "Please select a rating.");
      return;
    }

    /* --- Confirm --- */
    swalConfirm(
      "Are you sure?",
      "Do you want to submit this rating?",
      function () {
        $.post(
          "api/ratings_answer_files/submit_rating_answer.php",
          {
            rating_titles_id: rating_titles_id,
            rating_value: rating_value,
            reason: reason,
          },

          function (response) {
            if (response == "1") {
              swalSuccess("Success", "Rating Submitted Successfully!");
            } else {
              swalError("Error", "Something went wrong!");
            }

            getRatingsTable();
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
  getRatingsTable();
});

/* --- Swap Table and hide/show --- */
function swapTableAndCreation() {
  if ($(".ratings_table_content").is(":visible")) {
    $(".ratings_table_content").hide();
    $("#ratings_question_content").show();
    $(".backBtn").show();
  } else {
    $(".ratings_table_content").show();
    $("#ratings_question_content").hide();
    $(".backBtn").hide();
  }
}

/* --- Get Ratings Table --- */
function getRatingsTable() {
  $.post(
    "api/ratings_answer_files/ratings_list.php",
    function (response) {
      var columnMapping = ["sno", "rating_title", "status", "action"];
      appendDataToTable("#ratings_table", response, columnMapping);
      setdtable("#ratings_table", "Ratings List");
    },
    "json",
  );
}

/* --- Ratings Input Clear Fields --- */

function clearFields() {
    // 1. Reset text and remove ONLY the numeric rating classes (leaves 'rating-badge' intact)
    $("#ratings_badge")
        .text("Please select a rating")
        .removeClass(function (index, className) {
            return (className.match(/(^|\s)rating-\d+/g) || []).join(" ");
        });

    // 2. Clear any inline styles
    $("#ratings_badge").removeAttr("style");

    // 3. Reset all selected stars & inputs
    $("input[name='ratingOption']").prop("checked", false);
    $("#reason").val("");
    $("#rating_titles_id").val("");
}
