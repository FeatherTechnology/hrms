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
             <td>${getAttachmentHtml(row.attachment)}</td>
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
        // FEEDBACK ATTACHMENT VIEW BUTTON
        else if (type == "feedback") {
          extraColumn = `
            <td>
              <button class="btn btn-sm btn-info viewFeedbackAttachments" value="${row.id}"> View
              </button>
            </td>`;
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
      if (type == "feedback" && $(tableId + " thead th.feedback-attachment").length == 0) {
        $(tableId + " thead tr").append(`<th class="feedback-attachment">Attachments</th>`);
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


function getAttachmentHtml(attachment) {
    if (!attachment || !attachment.trim()) {
        return "-";
    }

    const fileName = attachment.split(/[\\/]/).pop();
    const extension = fileName.split(".").pop().toLowerCase();
    const fileUrl = "/hrms/uploads/general_feedback/" +
        attachment.split(/[\\/]/).map(encodeURIComponent).join("/");

    const safeUrl = $('<a>').attr('href', fileUrl).prop('href');
    const safeName = $('<span>').text(fileName).html();

    if (["jpg", "jpeg", "png", "gif", "webp", "bmp"].includes(extension)) {
        return `
            <a href="${safeUrl}" target="_blank" rel="noopener">
                ${safeName}
            </a>`;
    }

    if (["mp3", "wav", "ogg", "m4a", "aac"].includes(extension)) {
        return `
            <div>
                <div>${safeName}</div>
                <audio controls preload="none" style="width:220px;max-width:100%;">
                    <source src="${safeUrl}">
                    Your browser does not support audio.
                </audio>
            </div>`;
    }

    if (["mp4", "webm", "ogv", "mov", "m4v"].includes(extension)) {
        return `
            <div>
                <div>${safeName}</div>
                <video controls preload="metadata"
                       style="width:220px;max-width:100%;max-height:150px;">
                    <source src="${safeUrl}">
                    Your browser does not support video.
                </video>
            </div>`;
    }

    if (extension === "pdf") {
        return `
            <a href="${safeUrl}" target="_blank" rel="noopener">
                ${safeName}
            </a>`;
    }

    return `
        <a href="${safeUrl}" target="_blank" rel="noopener" download="${safeName}">
            ${safeName}
        </a>`;
}

// Click event for View Attachments button
$(document).on("click", ".viewFeedbackAttachments", function () {
    let feedback_titles_id = $(this).val();

    $.post(
        "api/analytics_dashboard_files/get_feedback_attachments_matrix.php",
        { feedback_titles_id: feedback_titles_id },
        function (response) {
            let theadHtml = `<th>S.No</th><th>Staff Name</th>`;
            let tbodyHtml = "";

            // Build dynamic question columns
            response.questions.forEach(function (q, idx) {
                theadHtml += `<th>Q${idx + 1}: ${q.feedback_questions}</th>`;
            });

            // Build staff attachment rows
            if (response.data.length > 0) {
                response.data.forEach(function (staffRow, index) {
                    tbodyHtml += `<tr>`;
                    tbodyHtml += `<td>${index + 1}</td>`;
                    tbodyHtml += `<td><b>${staffRow.staff_name}</b></td>`;

                    response.questions.forEach(function (q) {
                        let file = staffRow.attachments[q.id];
                        if (file && file.trim() !== "") {
                            let fileUrl = "/hrms/uploads/schedule_feedback/" + encodeURIComponent(file);
                            let fileName = file.split(/[\\/]/).pop();
                            tbodyHtml += `<td><a href="${fileUrl}" target="_blank" rel="noopener">${fileName}</a></td>`;
                        } else {
                            tbodyHtml += `<td>-</td>`;
                        }
                    });

                    tbodyHtml += `</tr>`;
                });
            } else {
                tbodyHtml += `<tr><td colspan="${response.questions.length + 2}" class="text-center">No attachments found.</td></tr>`;
            }

            $("#attachmentTableHead").html(theadHtml);
            $("#attachmentTableBody").html(tbodyHtml);
            $("#attachmentModal").modal("show");
        },
        "json"
    );
});