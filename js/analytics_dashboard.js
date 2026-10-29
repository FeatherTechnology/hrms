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
let feedbackData = []; // Store the fetched feedback globally
// Click handler to display feedback details & attachments in the modal
$(document).on("click", ".view-attachments-btn", function () {
  let id = $(this).data("id");

  // Find the specific row data from our global feedbackData array
  let row = feedbackData.find(item => Number(item.id) === Number(id));
  if (!row) {
    return;
  }

  // 1. Populate Feedback Details into the INPUT fields using .val()
  $("#modal_feedback_title").val(row.feedback_name || "-");
  $("#modal_submitted_by").val(row.submitted_by || "-");
  $("#modal_visible_to").val(row.visible_to || "-");
  $("#modal_company_name").val(row.company_name || "-");

  // Format and show created date
  let formattedDate = row.created_date
    ? (() => {
      const date = new Date(row.created_date.replace(" ", "T"));
      const day = String(date.getDate()).padStart(2, "0");
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const year = date.getFullYear();
      return `${day}-${month}-${year}`;
    })()
    : "-";

  $("#modal_created_date").val(formattedDate);

  // 2. Process and Render Attachments
  let attachmentValue = row.attachment ? String(row.attachment).trim() : "";
  let attachments = [];

  if (attachmentValue) {
    try {
      let decoded = JSON.parse(attachmentValue);
      attachments = Array.isArray(decoded) ? decoded : [attachmentValue];
    } catch (e) {
      attachments = attachmentValue.split(",");
    }
  }

  let html = "";
  attachments.forEach(function (file) {
    file = String(file).trim();

    if (!file || file.includes("/") || file.includes("\\") || file.includes("..")) {
      return;
    }

    let fileUrl = "/hrms/uploads/general_feedback/" + encodeURIComponent(file);

    html += `
        <a href="${fileUrl}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
            <div class="border rounded p-3 mb-2 bg-light d-flex align-items-center text-dark attachment-item-hover transition">
                <i class="fa fa-file-alt text-primary fs-5 me-3"></i>
                <span class="text-truncate fw-semibold">${escapeHtml(file)}</span>
            </div>
        </a>
    `;
  });

  $("#attachmentList").html(
    html || '<p class="text-muted mb-0 small fst-italic">No attachments available for this feedback.</p>'
  );

  showAttachmentModal();
});

$(document).on("click", ".remove-feedback-btn", function () {
  let id = $(this).data("id");

  swalConfirm(
    "Are you sure?",
    "Do you want to remove this feedback record?",
    function () {
      $.ajax({
        url: "api/analytics_dashboard_files/remove_general_feedback.php",
        type: "POST",
        data: { id: id },
        success: function (response) {
          if (String(response).trim() === "1") {
            swalSuccess("Success", "Feedback removed successfully.");
            getGeneralFeedback();
          } else if (String(response).trim() === "3") {
            swalError("Access denied", "You are not allowed to remove this feedback.");
          } else {
            swalError("Error", "Failed to remove feedback.");
          }
        },
        error: function () {
          swalError("Error", "Something went wrong while removing feedback.");
        }
      });
    }
  );
});


$(document).on("click", ".viewPollRating", function () {
  const id = $(this).val();
  const type = $(this).data("type");

  $.ajax({
    url: "api/analytics_dashboard_files/get_poll_rating_matrix.php",
    type: "POST",
    data: { id: id, type: type },
    dataType: "json",
    success: function (response) {
      const responseTitle = response.title || "";
      const modalTitle = type === "poll" ? "Poll Responses" : "Rating Responses";

      $("#feedbackAttachmentModalLabel").text(
        responseTitle ? `${modalTitle} - ${responseTitle}` : modalTitle
      );
      let theadHtml = "";
      let tbodyHtml = "";
      const rows = response.data || [];

      if (type === "poll") {
        theadHtml = `
          <th>S.No</th>
          <th>Staff Name</th>
        `;

        (response.options || []).forEach(function (option) {
          theadHtml += `
            <th class="text-center">
              ${escapeFeedbackHtml(option.poll_options)}
            </th>
          `;
        });

        if (rows.length > 0) {
          rows.forEach(function (row, index) {
            const selectedOptions = (row.selected_options || []).map(String);

            tbodyHtml += `
              <tr>
                <td>${index + 1}</td>
                <td>${escapeFeedbackHtml(row.staff_name || "-")}</td>
            `;

            (response.options || []).forEach(function (option) {
              const selected = selectedOptions.includes(String(option.id));

              tbodyHtml += `
                <td class="text-center">
                  ${selected
                  ? '<span style="color:#198754;font-size:20px;font-weight:bold;" title="Selected">&#10003;</span>'
                  : '<span style="color:#adb5bd;">-</span>'
                }
                </td>
              `;
            });

            tbodyHtml += `</tr>`;
          });
        } else {
          tbodyHtml = `
            <tr>
              <td colspan="${(response.options || []).length + 2}"
                  class="text-center">No staff found.</td>
            </tr>
          `;
        }
      } else {
        theadHtml = `
          <th>S.No</th>
          <th>Staff Name</th>
          <th>Rating Answer</th>
        `;

        if (rows.length > 0) {
          rows.forEach(function (row, index) {
            tbodyHtml += `
              <tr>
                <td>${index + 1}</td>
                <td>${escapeFeedbackHtml(row.staff_name || "-")}</td>
                <td>${escapeFeedbackHtml(
              row.answer !== null && String(row.answer).trim() !== ""
                ? row.answer
                : "-"
            )}</td>
              </tr>
            `;
          });
        } else {
          tbodyHtml = `
            <tr>
              <td colspan="3" class="text-center">No staff found.</td>
            </tr>
          `;
        }
      }

      $("#attachmentTableHead").html(theadHtml);
      $("#attachmentTableBody").html(tbodyHtml);
      $("#feedbackAttachmentModal").modal("show");
    },
    error: function (xhr) {
      console.error(xhr.responseText);
      alert("Failed to load staff responses.");
    }
  });
});

$(document).on("click", ".viewFeedbackAttachments", function () {
  let feedback_titles_id = $(this).val();
  $("#feedbackAttachmentModalLabel").text('Feedback Responses');

  $.ajax({
    url: "api/analytics_dashboard_files/get_feedback_attachments_matrix.php",
    type: "POST",
    data: {
      feedback_titles_id: feedback_titles_id
    },
    dataType: "json",
    success: function (response) {
      let theadHtml = `
                <th>S.No</th>
                <th>Staff Name</th>
            `;

      let tbodyHtml = "";

      // Question columns: Answer and Attachment
      response.questions.forEach(function (q, idx) {
        let question = escapeFeedbackHtml(q.feedback_questions);

        theadHtml += `
                    <th>Q${idx + 1}: ${question}</th>
                    <th>Q${idx + 1}: Attachments</th>
                `;
      });

      if (response.data && response.data.length > 0) {
        response.data.forEach(function (staffRow, index) {
          tbodyHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td><b>${escapeFeedbackHtml(staffRow.staff_name || "-")}</b></td>
                    `;

          response.questions.forEach(function (q) {
            let answer = staffRow.answers?.[q.id];
            let attachment = staffRow.attachments?.[q.id];

            // Answer column
            tbodyHtml += `
                            <td>${answer && String(answer).trim() !== ""
                ? escapeFeedbackHtml(answer)
                : "-"}</td>
                        `;

            // Attachment column
            let files = [];

            if (attachment && String(attachment).trim() !== "") {
              try {
                let parsed = JSON.parse(attachment);
                files = Array.isArray(parsed)
                  ? parsed
                  : String(attachment).split(",");
              } catch (e) {
                files = String(attachment).split(",");
              }
            }

            let attachmentHtml = "";

            files.forEach(function (file) {
              file = String(file).trim();

              if (!file || file.includes("/") ||
                file.includes("\\") || file.includes("..")) {
                return;
              }

              let fileUrl = "uploads/schedule_feedback/" +
                encodeURIComponent(file);

              attachmentHtml += `
                                <div class="mb-2">
                                    <a href="${fileUrl}"
                                       target="_blank"
                                       rel="noopener noreferrer">
                                        ${escapeFeedbackHtml(file)}
                                    </a>
                                </div>
                            `;
            });

            tbodyHtml += `
                            <td>${attachmentHtml || "-"}</td>
                        `;
          });

          tbodyHtml += `</tr>`;
        });
      } else {
        tbodyHtml = `
                    <tr>
                        <td colspan="${response.questions.length * 2 + 2}"
                            class="text-center">
                            No staff found.
                        </td>
                    </tr>
                `;
      }

      $("#attachmentTableHead").html(theadHtml);
      $("#attachmentTableBody").html(tbodyHtml);
      $("#feedbackAttachmentModal").modal("show");
    },
    error: function () {
      alert("Failed to load feedback details. Please try again.");
    }
  });
});

function escapeHtml(value) {
  return String(value ?? "").replace(/[&<>"']/g, function (char) {
    return {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;"
    }[char];
  });
}

function getAttachmentHtmlButton(attachment, id) {
  if (!attachment || !String(attachment).trim()) {
    return "-";
  }

  // Only pass the ID; we will look up the attachment data from our global array
  return `<button type="button"
        class="btn btn-sm btn-info view-attachments-btn"
        data-id="${Number(id)}">
        View
    </button>`;
}

function showAttachmentModal() {
  $("#attachmentModal").modal("show");
}

function getGeneralFeedback() {
  $.ajax({
    url: "api/analytics_dashboard_files/get_general_feedback.php",
    type: "POST",
    dataType: "json",
    success: function (response) {
      feedbackData = response; // Save the response globally for lookup
      let html = "";

      $.each(response, function (index, row) {
        html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${escapeHtml(row.company_name || "-")}</td>
                        <td>${escapeHtml(row.feedback_name || "-")}</td>
                        <td>${escapeHtml(row.commants || "-")}</td>
                        <td>${escapeHtml(row.submitted_by || "-")}</td>
                        <td>${escapeHtml(row.visible_to || "-")}</td>
                        <td>${getAttachmentHtmlButton(row.attachment, row.id)}</td>
                        <td>
                            <button type="button"
                                class="btn btn-sm btn-danger remove-feedback-btn"
                                data-id="${Number(row.id)}">
                                Remove
                            </button>
                        </td>
                    </tr>
                `;
      });

      $("#general_feedback_table tbody").html(
        html || '<tr><td colspan="7" class="text-center">No feedback found</td></tr>'
      );
    },
    error: function () {
      $("#general_feedback_table tbody").html(
        '<tr><td colspan="7" class="text-center text-danger">Failed to load feedback</td></tr>'
      );
    }
  });
}

//==================================================
// Dashboard List
//==================================================
function getDashboardList(type, tableId) {
  $.ajax({
    url: "api/analytics_dashboard_files/get_active_list.php",
    type: "POST",
    data: { type: type },
    dataType: "json",
    success: function (response) {
      let html = "";

      $.each(response, function (index, row) {
        let extraColumn = "";

        if (type === "poll") {
          extraColumn = `
            <td>
              <button class="btn btn-sm btn-info viewPollRating"
                data-type="poll" value="${row.id}">
                View
              </button>
            </td>
            <td>${row.top_answer ? row.top_answer : "-"}</td>
          `;
        } else if (type === "rating") {
          extraColumn = `
            <td>
              <button class="btn btn-sm btn-info viewPollRating"
                data-type="rating" value="${row.id}">
                View
              </button>
            </td>
            <td>${row.average_rating ? row.average_rating : "-"}</td>
          `;
        } else if (type === "feedback") {
          extraColumn = `
            <td>
              <button class="btn btn-sm btn-info viewFeedbackAttachments"
                value="${row.id}">
                View
              </button>
            </td>
          `;
        }

        const selectionTypeCell = type === "poll"
          ? `<td>${escapeHtml(row.selection_type || "-")}</td>`
          : "";

        html += `
  <tr>
    <td>${row.sno}</td>
    <td>${escapeHtml(row.company_name || "-")}</td>
    <td>${escapeHtml(row.branch_name || "-")}</td>
    <td>${escapeHtml(row.department_name || "-")}</td>
    <td>${escapeHtml(row.title || "-")}</td>
    ${selectionTypeCell}
    <td>${row.start_date}</td>
    <td>${row.end_date}</td>
    <td>${row.total_response}/${row.total_staff}</td>
    ${extraColumn}
  </tr>
`;
      });

      $(tableId + " tbody").html(html);

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
    }
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
  const fileUrl = "uploads/general_feedback/" +
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
function escapeFeedbackHtml(value) {
  return String(value ?? "").replace(/[&<>"']/g, function (char) {
    return {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;"
    }[char];
  });
}

