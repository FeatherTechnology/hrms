$(document).ready(function () {
  // Dashboard Counts
  getActiveCount("general_feedback", ".general_feedback");
  getActiveCount("feedback", ".active_feedback");
  getActiveCount("rating", ".active_rating");
  getActiveCount("poll", ".active_poll");

  // Average Rating
  // getAverageRating();

  // Hide all sections initially
  $("#general_feedback_section").hide();
  $("#feedback_section").hide();
  $("#rating_section").hide();
  $("#poll_section").hide();

  // Button Click Events
  $("#general_feedbackViewAll").click(function () {
    toggleSection("general_feedback");
  });

  $("#feedbackViewAll").click(function () {
    toggleSection("feedback");
  });

  $("#ratingViewAll").click(function () {
    toggleSection("rating");
  });

  $("#pollViewAll").click(function () {
    toggleSection("poll");
  });
});

//==================================================
// Show / Hide Section
//==================================================
function toggleSection(type) {
  var section = $("#" + type + "_section");
  var button = $("#" + type + "ViewAll");

  // If already visible, hide it
  if (section.is(":visible")) {
    section.hide();

    button.text("View All >>>>");

    return;
  }

  // Hide all sections
  $("#general_feedback_section").hide();
  $("#feedback_section").hide();
  $("#rating_section").hide();
  $("#poll_section").hide();

  // Reset all buttons
  $("#general_feedbackViewAll").text("View All >>>>");
  $("#feedbackViewAll").text("View All >>>>");
  $("#ratingViewAll").text("View All >>>>");
  $("#pollViewAll").text("View All >>>>");

  // Show selected section
  section.show();

  // Change selected button
  button.text("View Less <<<<");

  // Load data
  switch (type) {

    case "general_feedback":
      getGeneralFeedback();
      break;

    case "feedback":
      getDashboardList("feedback", "#feedback_table");
      break;

    case "rating":
      getDashboardList("rating", "#ratings_table");
      break;

    case "poll":
      getDashboardList("poll", "#polls_table");
      break;
  }

  // Scroll to section
  $("html, body").animate(
    {
      scrollTop: section.offset().top - 20,
    },
    500,
  );
}

//==================================================
// Average Rating
//==================================================
// function getAverageRating() {
//   $.ajax({
//     url: "api/analytics_dashboard_files/get_average_rating.php",
//     type: "POST",
//     success: function (response) {
//       $(".average_rating").text(response);
//     },
//   });
// }

function getGeneralFeedback() {
  $.ajax({
    url: "api/analytics_dashboard_files/get_general_feedback.php",
    type: "POST",
    dataType: "json",
    success: function (response) {
      let html = "";

      $.each(response, function (index, row) {
        html += `
          <tr>
            <td>${index + 1}</td>
            <td>${row.feedback_name ?? "-"}</td>
            <td>${row.commants ?? "-"}</td>
            <td>${row.submitted_by ?? "-"}</td>
            <td>${row.visible_to ?? "-"}</td>
          </tr>
        `;
      });

      $("#general_feedback_table tbody").html(html);
    },
  });
}

//==================================================
// Dashboard List
//==================================================
function getDashboardList(type, tableId) {
  $.ajax({
    url: "api/analytics_dashboard_files/get_active_list.php",
    type: "POST",
    data: {
      type: type,
    },
    dataType: "json",
    success: function (response) {
      let html = "";
      $.each(response, function (index, row) {
        let extraColumn = "";
        // POLL TOP ANSWER
        if (type == "poll") {
          extraColumn = `
                        <td>
                            ${row.top_answer ? row.top_answer : "-"}
                        </td>
                    `;
        }
        // RATING AVERAGE
        else if (type == "rating") {
          extraColumn = `
                        <td>
                            ${row.average_rating ? row.average_rating : "-"}
                        </td>
                    `;
        }
        html += `
                    <tr>
                        <td>${row.sno}</td>
                        <td>${row.title}</td>
                        <td>${row.start_date}</td>
                        <td>${row.end_date}</td>
                        <td>${row.total_response}/${row.total_staff}</td>
                        ${extraColumn}
                    </tr>
                `;
      });
      $(tableId + " tbody").html(html);
      // add extra column heading

      if (type == "poll") {
        if ($(tableId + " thead th.top-answer").length == 0) {
          $(tableId + " thead tr").append(
            `<th class="top-answer">Top Voted Option</th>`,
          );
        }
      }

      if (type == "rating") {
        if ($(tableId + " thead th.average-rating").length == 0) {
          $(tableId + " thead tr").append(
            `<th class="average-rating">AVERAGE RATING</th>`,
          );
        }
      }
    },
  });
}

//==================================================
// Active Counts
//==================================================
function getActiveCount(type, element) {
  $.ajax({
    url: "api/analytics_dashboard_files/get_active_counts.php",

    type: "POST",

    data: {
      type: type,
    },

    success: function (response) {
      $(element).text(response);
    },
  });
}
