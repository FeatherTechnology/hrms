$(document).ready(function () {
  getActiveCount("feedback", ".active_feedback");
  getActiveCount("rating", ".active_rating");
  getActiveCount("poll", ".active_poll");


  getAverageRating();

  getDashboardList("feedback", "#feedback_table", "feedback-hidden");
  getDashboardList("rating", "#ratings_table", "rating-hidden");
  getDashboardList("poll", "#polls_table", "poll-hidden");

  // View All / View Less Events
  toggleRows("#feedbackViewAll", "feedback-hidden", "#feedback_table_div");

  toggleRows("#ratingViewAll", "rating-hidden", "#ratings_table_div");

  toggleRows("#pollViewAll", "poll-hidden", "#polls_table_div");
});

//////////////////////////////////////////////////////
// COMMON VIEW ALL / VIEW LESS
//////////////////////////////////////////////////////

function toggleRows(buttonId, hiddenClass, scrollDiv) {
  $(document).on("click", buttonId, function () {
    if ($(this).data("expanded") == 1) {
      $("." + hiddenClass).addClass("d-none");

      $(this).text("View All >>>>");

      $(this).data("expanded", 0);
    } else {
      $("." + hiddenClass).removeClass("d-none");

      $(this).text("View Less <<<<");

      $(this).data("expanded", 1);

      $("html, body").animate(
        {
          scrollTop: $(scrollDiv).offset().top - 20,
        },
        500,
      );
    }
  });
}

function getAverageRating() {
  $.ajax({
    url: "api/analytics_dashboard_filer/get_average_rating.php",
    type: "POST",
    success: function (response) {
      $(".average_rating").text(response);
    },
  });
}


function getDashboardList(type, tableId, hiddenClass) {
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
        let hideClass = index >= 5 ? hiddenClass + " d-none" : "";

        html += `
                    <tr class="${hideClass}">
                        <td>${row.sno}</td>
                        <td>${row.title}</td>
                        <td>${row.start_date}</td>
                        <td>${row.end_date}</td>
                        <td>${row.total_response}</td>
                    </tr>
                `;
      });

      $(tableId + " tbody").html(html);
    },
  });
}

function getActiveCount(type, element) {

    $.ajax({

        url: "api/analytics_dashboard_files/get_active_counts.php",

        type: "POST",

        data: {
            type: type
        },

        success: function (response) {

            $(element).text(response);

        }

    });

}
