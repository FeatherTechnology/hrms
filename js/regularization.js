$(document).ready(function () {
  // to get regularization list
  $("input[name=regularization_type]").click(function () {
    let regType = $(this).val();
    if (regType == "Request") {
      getregularizationlist("Request");
    } else if (regType == "Approval") {
      getregularizationlist("Approval");
    }
  });

  // to add the new regularization
  $(".add_reg").click(function () {
    getRequestType();
    $(
      "#req_type,#leave_type,#balance_req,#from_date,#to_date,#purpose,#hidden_id,#leave_type_id,#leave_period,#ave_balance,#shift_start,#shift_end,#leave_days,#upload,#doc_upload,#cur_month_lop_count",
    ).val("");

    $(".req_div input, .req_div select,.req_div textarea").css(
      "border",
      "1px solid #cecece",
    );

    $("#to_date").attr("readonly", false);
    $("#req_type").attr("disabled", false);
    $("#leave_type").attr("disabled", false);
    $("#leave_period").attr("disabled", false);
    $("#from_date").attr("readonly", false);
    $("#purpose").attr("readonly", false);

    $("#back_btn,#balance_req,.staff_info_div").show();

    $(
      ".add_reg,.regularization_list,.approval_div,.leveType,.bal_req,.ot_req,.Lev_per.leave_day_div,.doc_upload_div.leave_count_div",
    ).hide();

    getuserdetails();
  });

  // back button hide and show
  $("#back_btn").click(function () {
    userTypeIdentification();
    $("#back_btn").hide();
    $(".staff_info_div").hide();
    $(".approval_div").hide();

    if (approval_required != 1) {
      $(".add_reg").show();
    }

    $(".regularization_list").show();
    $("#total_days").empty();

    $(".approval_div select,.approval_div textarea").css(
      "border",
      "1px solid #cecece",
    );
  });

  $("#from_date, #to_date").on("change", async function () {

    let reqType = $("#req_type").val();

    if (!$("#from_date").val() || !$("#to_date").val()) {
      return;
    }

    const response = await getbalancerequest();

    if (!response) {
      return;
    }

    // Selected From and To time
    let fromValue = $("#from_date").val();
    let toValue = $("#to_date").val();



  // Get time whether separator is T or space
   let fromTime = fromValue.includes("T")
        ? fromValue.split("T")[1].substring(0, 5)
        : "";

    let toTime = toValue.includes("T")
        ? toValue.split("T")[1].substring(0, 5)
        : "";

    // Shift time
    let shiftStartTime = response.start_time.substring(0, 5);
    let shiftEndTime = response.end_time.substring(0, 5);

    let formattedStart = formatTime(response.start_time);
    let formattedEnd = formatTime(response.end_time);
     let fromMinutes = Number(fromTime.split(":")[0]) * 60 +
                      Number(fromTime.split(":")[1]);

    let toMinutes = Number(toTime.split(":")[0]) * 60 +
                    Number(toTime.split(":")[1]);

    let shiftStartMinutes = Number(shiftStartTime.split(":")[0]) * 60 +
                            Number(shiftStartTime.split(":")[1]);

    let shiftEndMinutes = Number(shiftEndTime.split(":")[0]) * 60 +
                          Number(shiftEndTime.split(":")[1]);

    // =========================
    // OT Validation
    // =========================
    if (reqType == "4") {

      let isBeforeShift = toTime <= shiftStartTime;
      let isAfterShift = fromTime >= shiftEndTime;

      if (!(isBeforeShift || isAfterShift)) {

        swalError(
          "Warning",
          `OT can only be applied before the shift starts (${formattedStart}) or after the shift ends (${formattedEnd}).`
        );

        $("#from_date, #to_date").val("");

        return;
      }
    }

    // =========================
    // Permission Validation
    // =========================
    if (reqType == "2") {
      
    if (
        fromMinutes < shiftStartMinutes ||
        toMinutes > shiftEndMinutes ||
        toMinutes <= fromMinutes
    ) {
        swalError(
            "Warning",
            `Permission can only be applied within the shift timings (${formattedStart} to ${formattedEnd}).`
        );

        $("#from_date, #to_date").val("");
        return;
    }
    }

    // Calculate difference
    calculateDateDiff(
      "#from_date",
      "#to_date",
      "#total_min",
      "#total_days"
    );
    getLeaveDays()
  });

  // edit regularization
  $(document).on("click", ".edit_reg", function () {
    let id = $(this).data("id");
    let staff_id = $(this).data("staff_id");
    let status = $(this).data("status");
    let appfrom = $(this).data("appfrom");
    let currentdate = new Date();

    if (status == "1") {
      // swalError("Error", "This request has already been approved");
      let appFromDate = new Date(appfrom);

      // if (appFromDate > currentdate) {
      // app_from date is greater than current date
      $("#back_btn,.staff_info_div,.approval_div").show();

      $(".add_reg,.regularization_list").hide();

      $("#hidden_id").val(id);
      getedituserdetails(id);
      // } else {
      //   swalError("Error", "This request has already Approved.");
      // }
    } else if (status == "2") {
      swalError("Warning", "This request has already been cancelled");
    } else {
      $("#back_btn,.staff_info_div,.approval_div").show();

      $(".add_reg,.regularization_list").hide();

      $("#hidden_id").val(id);

      getedituserdetails(id);
    }
  });

  // request type change
  $("#req_type").change(function () {
    let cmpy_id = $("#cmpy_id").val();
    let value = $(this).val();
    $("#total_days").empty();
    $(
      ".leveType,#from_date,#to_date,#leave_type_id,#balance_req,#leave_period,#ave_balance,#shift_start,#shift_end",
    ).val("");
    if (value == "1") {
      $(".leveType").show();
      $(".Lev_per").show();
      $(".bal_req").show();
      $(".leave_day_div").show();
      $(".doc_upload_div").show();
      $(".ot_req").hide();
      $(".shift_time").hide();
      $("#from_date,#to_date").attr("type", "date");
      getcmpyleavelist(cmpy_id);
    } else if (value == "2") {
      $(".Lev_per").hide();
      $(".bal_req").show();
      $(".shift_time").show();
      $(".leveType").hide();
      $(".ot_req").hide();
      $(".leave_day_div").hide();
      $(".leave_count_div").hide();
      $(".doc_upload_div").hide();
      $("#from_date,#to_date").attr("type", "datetime-local");
      getbalancerequest();
    } else if (value == "3") {
      $(".Lev_per").show();
      $(".bal_req").show();
      $(".leveType").hide();
      $(".leave_day_div").show();
      $(".doc_upload_div").hide();
      $(".leave_count_div").hide();
      $(".ot_req").hide();
      $(".shift_time").hide();
      $("#from_date,#to_date").attr("type", "date");
      getbalancerequest();
    } else if (value == "4") {
      $(".Lev_per").hide();
      $(".bal_req").hide();
      $(".leveType").hide();
      $(".ot_req").show();
      $(".shift_time").show();
      $(".doc_upload_div").hide();
      $(".leave_day_div").hide();
      $(".leave_count_div").hide();
      $(
        "#leave_type,#leave_type_id,#balance_req,#leave_period,#ave_balance,#shift_start,#shift_end",
      ).val("");
      $("#from_date,#to_date").attr("type", "datetime-local");
      getbalancerequest();
    }

    $("#request_form input").css("border", "1px solid #cecece");
    $("#request_form select").css("border", "1px solid #cecece");
    $("#request_form textarea").css("border", "1px solid #cecece");

    setCurrentMonthRestriction("#from_date", "#to_date");
  });

  // Leave Day change
  // Leave Day change
  $("#leave_period").on("change", function () {
    // Empty fields on leave period change
    if ($(this).attr("id") === "leave_period") {
      $("#from_date").val("");
      $("#to_date").val("");
      $("#total_days").text("");
      $("#balance_req").val("");
    }

    // getbalancerequest();
    let leaveDay = $("#leave_period").val();
    let fromDate = $("#from_date").val();
    let req_type = $("#req_type").val();

    if (!fromDate) return;

    if (leaveDay == "1" || leaveDay == "2") {
      $("#to_date").val(fromDate).attr("min", fromDate).attr("max", fromDate);
    } else if (leaveDay == "3") {
      let from = new Date(fromDate);

      let lastDay = new Date(from.getFullYear(), from.getMonth() + 1, 0);

      let yyyy = lastDay.getFullYear();
      let mm = String(lastDay.getMonth() + 1).padStart(2, "0");
      let dd = String(lastDay.getDate()).padStart(2, "0");

      let maxDate = `${yyyy}-${mm}-${dd}`;

      $("#to_date").val("").attr("min", fromDate).attr("max", maxDate);
    }
  });

  // delete regularization
  $(document).on("click", ".delete_reg", function () {
    let id = $(this).data("id");
    let status = $(this).data("status");
    let from_date = $(this).data("from_date");
    let currentdate = new Date();

    if (status == "1") {
      swalError("Error", "This request has already Approved.");
    } else if (status == "2") {
      swalError("Error", "This request has already been cancelled");
    } else {
      if (
        confirm("Are you sure you want to delete this regularization request?")
      ) {
        deleteregularization(id);
      }
    }
  });

  // submit regularization
  $("#submit_regularization").click(function () {
    event.preventDefault();
    let collData = {
      stf_prf_id: $("#stf_prf_id").val(),
      staff_id: $("#staff_id").val(),
      staff_type: $("#staff_type").val(),
      cmpy_id: $("#cmpy_id").val(),
      branch_id: $("#branch_id").val(),
      dep_id: $("#dep_id").val(),
      des_id: $("#des_id").val(),
      team_id: $("#team_id").val(),
      req_type: $("#req_type").val(),
      leave_type: $("#leave_type").val(),
      ave_balance: $("#req_type").val() == "2" ? availableBalance : $("#ave_balance").val(),
      balance_req: $("#req_type").val() == "2" ? remainingBalance : $("#balance_req").val(),
      shift_end: $("#shift_end").val(),
      shift_start: $("#shift_start").val(),
      leave_period: $("#leave_period").val(),
      current_month_ot_count: $("#current_month_ot_count").val(),
      cur_month_lop_count: $("#cur_month_lop_count").val(),
      req_date: $("#req_date").val(),
      from_date: $("#from_date").val(),
      to_date: $("#to_date").val(),
      leave_days: $("#leave_days").val(),
      ot_payment: $("#ot_payment").val(),
      total_min: $("#total_min").val(),
      purpose: $("#purpose").val(),
      approval_type: $("#approval_type").val(),
      permission_type: $("#permission_type").val(),
      remarks: $("#remarks").val(),
      doc_upload: $("#doc_upload").val(),
      hidden_id: $("#hidden_id").val(),
    };
    let isValid = true;

    let validationResults = [
      validateField(collData["req_type"], "req_type"),
      validateField(collData["req_date"], "req_date"),
      validateField(collData["from_date"], "from_date"),
      validateField(collData["to_date"], "to_date"),
      validateField(collData["purpose"], "purpose"),
    ];

    if (collData["req_type"] == 1) {
      validationResults.push(
        validateField($("#leave_type").val(), "leave_type"),
        validateField($("#leave_period").val(), "leave_period"),
        validateField($("#leave_days").val(), "leave_days"),
      );
    }
    if (collData["req_type"] == 2) {
      validationResults.push(
        validateField($("#ave_balance").val(), "ave_balance"),
        validateField($("#balance_req").val(), "balance_req"),
      );
    }
    if (collData["req_type"] == 3) {
      validationResults.push(
        validateField($("#leave_period").val(), "leave_period"),
        validateField($("#ave_balance").val(), "ave_balance"),
        validateField($("#balance_req").val(), "balance_req"),
        validateField($("#leave_days").val(), "leave_days"),
      );
    }

    if (!validationResults.every((result) => result)) {
      isValid = false;
    }

    if ($(".approval_div").is(":visible")) {
      let approvalValid = true;

      if ($("#approval_type").val() == "0") {
        $("#approval_type").css("border", "1px solid red");
        approvalValid = false;
      } else {
        $("#approval_type").css("border", "1px solid #cecece");
      }

      if ($("#remarks").val().trim() == "") {
        $("#remarks").css("border", "1px solid red");
        approvalValid = false;
      } else {
        $("#remarks").css("border", "1px solid #cecece");
      }

      if (!approvalValid) {
        swalError("Warning", "Please fill out all mandatory fields.");
        isValid = false;
        return false;
      }
    }

    const req_type = parseInt($("#req_type").val(), 10);
    const balance_req = parseFloat($("#balance_req").val()) || 0;

    // Check balance only for req_type 1,2,3
    if ([1, 2, 3].includes(req_type) && balance_req < 0) {
      let excessDays = Math.abs(balance_req);

      swalError(
        "Error",
        `You don't have enough balance. You have applied ${excessDays} more day(s) than your available balance.`,
      );

      return false;
    }

    if (isValid) {
      swalConfirm(
        "Are you sure?",
        "Do you want to submit this Regularization?",
        function () {
          let formData = new FormData();

          $.each(collData, function (key, value) {
            formData.append(key, value ?? "");
          });

          let uploadFile = $("#upload")[0].files[0];

          if (uploadFile) {
            formData.append("upload", uploadFile);
          }
          $.ajax({
            url: "api/regularization_files/submit_regularization.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
              if (response.result == "1") {
                swalSuccess("Success", "Regularization Updated Successfully.");
                $("#back_btn").trigger("click");
              } else if (response.result == "2") {
                swalError("Error", "Failed to Update Regularization");
              } else if (response.result == "3") {
                swalSuccess("Success", "Regularization Inserted Successfully.");
                $("#back_btn").trigger("click");
              } else if (response.result == "4") {
                swalError("Error", "Failed to Insert Regularization");
              } else if (response.result == "5") {
                swalError("Error", "Already regularization request exists for this date");
              }
            },
            error: function (xhr, status, error) {
              console.error(xhr.responseText);
              console.error(error);
              swalError("Error", "Failed to submit Regularization.");
            }
          });
        },
      );
    }
  }); //submit END.

  setDateValidation("#from_date", "#to_date");

  $("#leave_type").on("change", async function () {
    let leave_type = $("#leave_type").val();
    $("#to_date,#total_days,#from_date,#balance_req").val("");
    if (leave_type == 0) {
      $(".bal_req").hide();
      $(".leave_count_div").show();
    } else {
      $(".bal_req").show();
      $(".leave_count_div").hide();
    }
    await getbalancerequest();
  });

  $(document).on("click", ".sts_remarks", function (e) {
    e.preventDefault();
    let remarks = $(this).data("remarks") || "";
    $("#sts_remarks").val(remarks);
  });

  $(document).on("click", ".attendance_chart", function () {
    $("#attendanceChartModal")
      .data("company_id", $(this).data("company_id"))
      .data("shift_id", $(this).data("shift_id"))
      .data("staff_id", $(this).data("staff_id"))
      .data("att_date", $(this).data("date"))
      .modal("show");
  });
  $("#attendanceChartModal").on("shown.bs.modal", function () {
    let modal = $(this);

    loadChart(
      modal.data("company_id"),
      modal.data("shift_id"),
      modal.data("staff_id"),
      modal.data("att_date"),
    );
  });

  $("#attendanceChartModal").on("hidden.bs.modal", function () {
    $("#timeline_chart").empty();
  });
});
// document end
$(function () {
  userTypeIdentification();
});

// function start

/* --- Get Request Type --- */
async function getRequestType() {
  return new Promise((resolve, reject) => {
    $.post(
      "api/regularization_files/get_request_type.php",
      {},
      function (response) {
        let dropdown = $("#req_type");
        dropdown.empty();
        dropdown.append('<option value="">Select Request Type</option>');

        $.each(response, function (index, item) {
          dropdown.append(
            `<option value="${item.id}">${item.request_type}</option>`,
          );
        });

        resolve();
      },
      "json",
    ).fail(function (xhr, status, error) {
      reject(error);
    });
  });
}
let availableBalance = 0;
let remainingBalance = 0;
// to get the regularization list
function getregularizationlist(type) {
  if ($.fn.DataTable.isDataTable("#regularization_table")) {
    $("#regularization_table").DataTable().destroy();
  }
  getUserAccess(function (downloadAccess) {
    let buttons = [];

    // Add Excel button only if download access is granted
    if (downloadAccess === 1) {
      let excelTitle = "Regularization Report List";
      buttons.push({
        extend: "excelHtml5",
        action: function (e, dt, button, config) {
          excelExportAction(e, dt, button, config, excelTitle);
        },
      });
    }

    // Add column visibility button
    buttons.push({
      extend: "colvis",
      collectionLayout: "fixed four-column",
    });

    $("#regularization_table").DataTable({
      order: [[0, "desc"]],
      processing: true,
      serverSide: true,
      serverMethod: "post",
      ajax: {
        url: "api/regularization_files/get_regularization_list.php",
        data: function (data) {
          // var search = $("input[type=search]").val();
          // data.search = search;
          data.type = type;
        },
      },
      dom: "lBfrtip",
      buttons: buttons, // Use the dynamically constructed buttons array
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, "All"],
      ],

      // Call searchFunction after DataTable is drawn
      drawCallback: function () {
        searchFunction("regularization_table");
      },
    });
  });
}

// to get the user deatils
function getuserdetails() {
  let status = 0;
  let id = "";
  $.post(
    "api/regularization_files/get_user_details.php",
    { id, status },
    function (response) {
      $("#staff_id").val(response.staff_id);
      $("#staff_type").val(response.staff_type);
      $("#stf_prf_id").val(response.stf_prf_id);
      $("#staff_name").val(response.staff_name);
      $("#cmpy_id").val(response.cmpy_id);
      $("#cmpy_name").val(response.company_name);
      $("#branch_id").val(response.branch_id);
      $("#branch_name").val(response.branch_name);
      $("#dep_id").val(response.dep_id);
      $("#department").val(response.department_name);
      $("#des_id").val(response.des_id);
      $("#designation").val(response.designation);
      $("#team_id").val(response.team_id);
      $("#team").val(response.team_name);
      $("#ot_payment").val(response.ot_payment);
      $("#req_date").val(
        new Date().toLocaleDateString("en-GB").replace(/\//g, "-"),
      );
    },
    "json",
  );
}

// to get the user details using edit
function getedituserdetails(id) {
  let status = 1;

  $.post(
    "api/regularization_files/get_user_details.php",
    { id, status },
    function (response) {
      $("#req_type").prop("disabled", true);
      $("#leave_type").prop("disabled", true);
      $("#leave_period").prop("disabled", true);
      $("#purpose").attr("readonly", true);
      $("#approval_type,#remarks").val("");
      if (response.req_type == "2" || response.req_type == "4") {
        $("#from_date, #to_date").prop("readonly", false);
      } else {
        $("#from_date, #to_date").prop("readonly", true);
      }
      if ($("#req_type option").length <= 1) {
        $("#req_type").html(`
    <option value="4">OT</option>
    <option value="1">Leave</option>
    <option value="3">Week Off</option>
    <option value="2">Permission</option>
`);
      }
      // Show Attendance Chart only for Permission (2) and OT (4)
      if (response.req_type == "2" || response.req_type == "4") {
        $("#approval_attendance_chart")
          .show()
          .attr("data-company", response.company_name || "")
          .attr("data-company_id", response.cmpy_id || "")
          .attr("data-shift", response.shift || "")
          .attr("data-staff_id", response.stf_prf_id || "")
          .attr("data-staff_name", response.staff_name || "")
          .attr("data-date", (response.att_date || response.from_date || "").split(/[ T]/)[0]);
      } else {
        $("#approval_attendance_chart").hide();
      }
      // select response request type
      $("#req_type").val(response.req_type);

      $("#staff_id").val(response.staff_id);
      $("#staff_type").val(response.staff_type);
      $("#stf_prf_id").val(response.stf_prf_id);
      $("#staff_name").val(response.staff_name);
      $("#cmpy_id").val(response.cmpy_id);
      $("#cmpy_name").val(response.company_name);
      $("#branch_id").val(response.branch_id);
      $("#branch_name").val(response.branch_name);
      $("#dep_id").val(response.dep_id);
      $("#department").val(response.department_name);
      $("#des_id").val(response.des_id);
      $("#designation").val(response.designation);
      $("#team_id").val(response.team_id);
      $("#team").val(response.team_name);
      $("#shift_start").val(response.shift_start);
      $("#shift_end").val(response.shift_end);
      $("#leave_days").val(response.leave_days);
      $("#ot_payment").val(response.ot_payment);
      $("#req_date").val(
        response.req_date.split(" ")[0].split("-").reverse().join("-"),
      );
      $("#leave_type_id").val(response.leave_type);
      $("#purpose").val(response.purpose);
      if (response.from_date.endsWith("00:00:00")) {
        $("#from_date")
          .attr("type", "date")
          .val(response.from_date.substring(0, 10));
      } else {
        $("#from_date")
          .attr("type", "datetime-local")
          .val(response.from_date.replace(" ", "T").slice(0, 16));
      }

      if (response.to_date.endsWith("00:00:00")) {
        $("#to_date")
          .attr("type", "date")
          .val(response.to_date.substring(0, 10));
      } else {
        $("#to_date")
          .attr("type", "datetime-local")
          .val(response.to_date.replace(" ", "T").slice(0, 16));
      }

      $("#approval_type").val(response.status || 0);
      $("#remarks").val(response.remarks);
        if (response.req_type != "2") {
        availableBalance = Number(response.ave_balance) || 0;
        remainingBalance = Number(response.balance_req) || 0;

        $("#ave_balance").val(availableBalance);
        $("#balance_req").val(remainingBalance);
      } else {
        const permissionType = response.permission_type;

        availableBalance = parseFloat(response.ave_balance) || 0;
        remainingBalance = parseFloat(response.balance_req) || 0;

        $("#permission_type").val(permissionType);

        $("#ave_balance").val(
          formatPermissionBalance(response.ave_balance, permissionType)
        );

        $("#balance_req").val(
          formatPermissionBalance(response.balance_req, permissionType)
        );
      }
      if (response.req_type == 1) {
        $(".leveType").show();
        $(".leave_day_div").show();
        $(".doc_upload_div").show();

        getcmpyleavelist(response.cmpy_id);
      } else {
        $(".leveType").hide();
        $(".leave_day_div").hide();
        $(".doc_upload_div").hide();
      }
      if (response.req_type == 1 || response.req_type == 3) {
        $(".Lev_per").show();
        $(".leave_day_div").show();
        $("#leave_period").val(response.leave_period);
      } else {
        $(".Lev_per").hide();
        $(".leave_day_div").hide();
        $(".doc_upload_div").hide();
        $("#leave_period").val("");
        $("#leave_days").val("");
        $("#upload").val("");
        $("#doc_upload").val("");
      }

      if (response.req_type != 4) {
        $(".bal_req").show();
      } else {
        $(".bal_req").hide();
      }

      if (response.req_type == 4) {
        $(".ot_req").show();
        $("#current_month_ot_count").val(response.current_month_ot_count || 0);
      } else {
        $(".ot_req").hide();
      }

      $("#upload").val("");
      $("#doc_upload").val(response.upload || "");

      if (response.upload) {
        let fileName = response.upload.split("/").pop();

        $("#uploaded_document_link").html(
          `<a href="uploads/leave/document/${encodeURIComponent(fileName)}"
            target="_blank"
            rel="noopener noreferrer">
            <i class="fa fa-file"></i> View Uploaded Document
        </a>`
        );

        $("#upload").prop("disabled", true);
      } else {
        $("#uploaded_document_link").empty();
        $("#upload").prop("disabled", true);
      }

      let totalMin = parseInt(response.total_min, 10);
      let days = Math.floor(totalMin / (24 * 60));
      let hours = Math.floor((totalMin % (24 * 60)) / 60);
      let minutes = totalMin % 60;

      $("#total_days").html(
        `<div style="display:flex; gap:15px;">
          <span><span style="color:#f26b35">${days}</span> D</span>
          <span><span style="color:#f26b35">${hours}</span> H</span>
          <span><span style="color:#f26b35">${minutes}</span> M</span>
        </div>`,
      );

      if (response.req_type == "2" || response.req_type == "4") {
        $(".shift_time").show();
      } else {
        $(".shift_time").hide();
      }
      if (response.req_type == "1" && response.leave_type == "0") {
        $(".bal_req").hide();
        $(".leave_count_div").show();
        $("#cur_month_lop_count").val(response.cur_month_lop_count || 0);
      } else {
        $(".leave_count_div").hide();
      }
    },
    "json",
  );
}

// to get the company leave list
function getcmpyleavelist(cmpy_id) {
  let leave_type_id = $("#leave_type_id").val();

  $.post(
    "api/regularization_files/get_cmpy_leave_list.php",
    { cmpy_id },
    function (response) {
      $("#leave_type").empty();
      $("#leave_type").append("<option value=''>Select Leave Type</option>");

      // LOP
      let lopSelected = leave_type_id == "0" ? "selected" : "";

      $("#leave_type").append(
        "<option value='0' " + lopSelected + ">LOP</option>",
      );

      $.each(response, function (index, val) {
        let selected = "";

        if (leave_type_id == val["id"]) {
          selected = "selected";
        }

        $("#leave_type").append(
          "<option value='" +
          val["id"] +
          "' " +
          selected +
          ">" +
          val["leave_type"] +
          "</option>",
        );
      });
    },
    "json",
  );
}

// to get the balance request && to get the Current Month OT Count
let shiftStart = "";
let shiftEnd = "";

async function getbalancerequest() {
  try {
    const response = await $.post(
      "api/regularization_files/get_balance_request.php",
      {
        req_type: $("#req_type").val(),
        dep_id: $("#dep_id").val(),
        cmpy_id: $("#cmpy_id").val(),
        staff_id: $("#stf_prf_id").val(),
        from_date: $("#from_date").val(),
        to_date: $("#to_date").val(),
        leave_period: $("#leave_period").val(),
        leave_type: $("#leave_type").val(),
      },
      null,
      "json",
    );


    const reqType = $("#req_type").val();

    if (reqType != "2") {
      availableBalance = Number(response.ave_balance) || 0;
      remainingBalance = Number(response.balance) || 0;

      $("#ave_balance").val(availableBalance);
      $("#balance_req").val(remainingBalance);
    } else {
      const permissionType = response.permission_type;

      availableBalance = parseFloat(response.ave_balance) || 0;
      remainingBalance = parseFloat(response.balance) || 0;

      $("#permission_type").val(permissionType);

      $("#ave_balance").val(
        formatPermissionBalance(response.ave_balance, permissionType)
      );

      $("#balance_req").val(
        formatPermissionBalance(response.balance, permissionType)
      );
    }

    $("#current_month_ot_count").val(response.current_month_ot_count);
    $("#cur_month_lop_count").val(response.cur_month_lop_count);

    const startTime = formatTime(response.start_time);
    const endTime = formatTime(response.end_time);

    $("#shift_start").val(startTime);
    $("#shift_end").val(endTime);

    shiftStart = response.start_time;
    shiftEnd = response.end_time;


    /* =====================================================
       PERMISSION LATE ENTRY
       ===================================================== */

    if (
      $("#req_type").val() == "2" &&
      response.late_entry == 1
    ) {



      const requiredFrom = response.permission_from
        ? response.permission_from.substring(0, 5)
        : "";

      const requiredTo = response.permission_to
        ? response.permission_to.substring(0, 5)
        : "";


      const selectedFrom = $("#from_date").val();
      const selectedTo = $("#to_date").val();

      let selectedFromTime = "";
      let selectedToTime = "";

      if (selectedFrom) {
        selectedFromTime =
          selectedFrom.split("T")[1]?.substring(0, 5) || "";
      }

      if (selectedTo) {
        selectedToTime =
          selectedTo.split("T")[1]?.substring(0, 5) || "";
      }


      /*
        EXACT MATCH CHECK

        Required:
        09:00 -> 10:00

        If employee selects anything else,
        show warning.
      */

      if (
        selectedFromTime !== requiredFrom ||
        selectedToTime !== requiredTo
      ) {

        const requiredFromDisplay =
          formatTime(response.permission_from);

        const requiredToDisplay =
          formatTime(response.permission_to);

        swalError(
          "Warning",
          `You have Later Entry on that day. Please take the permission from "${requiredFromDisplay}" to "${requiredToDisplay}".`
        );

        $("#from_date,#to_date,#total_days,#balance_req").val('');
        return null;
      }
    }


    /* =====================================================
       PERMISSION BALANCE CHECK
       ===================================================== */

    if (
      $("#req_type").val() == "2" &&
      response.permission_exceeded == 1
    ) {

      swalError(
        "Warning",
        "You have exceeded the available permission balance."
      );

      $("#from_date, #to_date,#total_days,#balance_req").val("");

      return null;
    }
    return response;

  } catch (error) {

    console.error(error);

  }
}


// to delete the applied regularization
function deleteregularization(id) {
  $.post(
    "api/regularization_files/delete_regularization.php",
    { id: id },
    function (response) {
      if (response == 1) {
        swalSuccess("Success", "Deleted Successfully");
        $("#pending").prop("checked", true).trigger("click");
      } else {
        swalError("Error", "Delete Failed");
      }
    },
  );
}

// to calculate the date difference
function calculateDateDiff(
  fromSelector,
  toSelector,
  totalMinSelector,
  totalDaysSelector,
) {
  let fromVal = $(fromSelector).val();
  let toVal = $(toSelector).val();
  // empty check
  if (fromVal == "" || toVal == "") {
    $(totalMinSelector).val(0);

    $(totalDaysSelector).html(
      `<div style="display:flex; gap:15px;">
        <span><span style="color:#f26b35">0</span> D</span>
        <span><span style="color:#f26b35">0</span> H</span>
        <span><span style="color:#f26b35">0</span> M</span>
      </div>`,
    );

    return;
  }

  let from = new Date(fromVal);
  let to = new Date(toVal);

  let leaveDay = $("#leave_period").val();

  if (
    (leaveDay == "1" || leaveDay == "2") &&
    from.toDateString() !== to.toDateString()
  ) {
    swalError(
      "Warning",
      "For Half Day leave, From Date and To Date must be the same.",
    );

    $(toSelector).val("");

    return;
  }

  let diffMs = to - from;

  // invalid date
  let reqType = $("#req_type").val();

  let leave_period = $("#leave_period").val();
  if (
    (reqType == "1" || reqType == "3") &&
    (leave_period == "1" || leave_period == "2")
  ) {
    let start = new Date(`1970-01-01T${shiftStart}`);
    let end = new Date(`1970-01-01T${shiftEnd}`);

    if (end <= start) {
      end.setDate(end.getDate() + 1);
    }

    let totalMinutes = (end - start) / (1000 * 60);
    let halfDayMinutes = totalMinutes / 2;

    $(totalMinSelector).val(halfDayMinutes);

    let hours = Math.floor(halfDayMinutes / 60);
    let minutes = halfDayMinutes % 60;

    $(totalDaysSelector).html(`
      <div style="display:flex; gap:15px;">
        <span><span style="color:#f26b35">0</span> D</span>
        <span><span style="color:#f26b35">${hours}</span> H</span>
        <span><span style="color:#f26b35">${minutes}</span> M</span>
      </div>
    `);

    return;
  }

  // LOP Full Day
  if (reqType == "1" && $("#leave_type").val() == "0" && leave_period == "3") {
    let totalMinutes = Math.floor(diffMs / 60000) + 1440;

    $(totalMinSelector).val(totalMinutes);

    let days = Math.floor(totalMinutes / 1440);

    $(totalDaysSelector).html(`
      <div style="display:flex; gap:15px;">
        <span><span style="color:#f26b35">${days}</span> D</span>
        <span><span style="color:#f26b35">0</span> H</span>
        <span><span style="color:#f26b35">0</span> M</span>
      </div>
    `);

    return;
  }

  if (
    (reqType == "1" || reqType == "3") &&
    (leave_period == "1" || leave_period == "2")
  ) {
    // Convert HH:MM:SS to Date objects
    let start = new Date(`1970-01-01T${shiftStart}`);
    let end = new Date(`1970-01-01T${shiftEnd}`);

    // Handle overnight shifts (e.g. 10 PM to 6 AM)
    if (end <= start) {
      end.setDate(end.getDate() + 1);
    }

    // Total shift minutes
    let totalMinutes = (end - start) / (1000 * 60);

    // Half-day minutes
    let halfDayMinutes = totalMinutes / 2;

    $(totalMinSelector).val(halfDayMinutes);

    let hours = halfDayMinutes / 60;

    $(totalDaysSelector).html(`
    <div style="display:flex; gap:15px;">
      <span><span style="color:#f26b35">0</span> D</span>
      <span><span style="color:#f26b35">${hours}</span> H</span>
      <span><span style="color:#f26b35">0</span> M</span>
    </div>
  `);

    return;
  }

  // Permission type
  if (reqType == "2") {
    // less than 1 minute
    if (diffMs < 1000 * 60) {
      alert("Permission must have at least 1 minute difference");

      $(toSelector).val("");

      $(totalMinSelector).val(0);

      $(totalDaysSelector).html(
        `<div style="display:flex; gap:15px;">
          <span><span style="color:#f26b35">0</span> D</span>
          <span><span style="color:#f26b35">0</span> H</span>
          <span><span style="color:#f26b35">0</span> M</span>
        </div>`,
      );

      return;
    }
  } else {
    // other request types
    if (diffMs < 0) {
      alert("To Date must be greater than From Date");

      $(toSelector).val("");

      $(totalMinSelector).val(0);

      $(totalDaysSelector).html(
        `<div style="display:flex; gap:15px;">
          <span><span style="color:#f26b35">0</span> D</span>
          <span><span style="color:#f26b35">0</span> H</span>
          <span><span style="color:#f26b35">0</span> M</span>
        </div>`,
      );

      return;
    }
  }

  // detect datetime or date only
  let hasTime = fromVal.includes(":") || toVal.includes(":");

  // total minutes
  let totalMinutes = Math.floor(diffMs / (1000 * 60));

  // DATE ONLY → inclusive calculation
  if (!hasTime) {
    totalMinutes += 24 * 60;
  }

  $(totalMinSelector).val(totalMinutes);

  // breakdown
  let days = Math.floor(totalMinutes / (24 * 60));

  let remainingMinutes = totalMinutes % (24 * 60);

  let hours = Math.floor(remainingMinutes / 60);

  let minutes = remainingMinutes % 60;

  // balance check
  // let balanceDays = parseInt($("#balance_req").val());

  // if (days > balanceDays) {
  //   alert("You only have " + balanceDays + " leave days balance");

  //   $(toSelector).val("");

  //   $(totalMinSelector).val(0);

  //   $(totalDaysSelector).html(
  //     `<div style="display:flex; gap:15px;">
  //       <span><span style="color:#f26b35">0</span> D</span>
  //       <span><span style="color:#f26b35">0</span> H</span>
  //       <span><span style="color:#f26b35">0</span> M</span>
  //     </div>`,
  //   );

  //   return;
  // }

  // display
  $(totalDaysSelector).html(
    `<div style="display:flex; gap:15px;">
        <span><span style="color:#f26b35">${days}</span> D</span>
        <span><span style="color:#f26b35">${hours}</span> H</span>
        <span><span style="color:#f26b35">${minutes}</span> M</span>
      </div>`,
  );
}

// to set the validation for the from and to date
function setDateValidation(fromSelector, toSelector) {
  $(fromSelector).on("change", function () {
    let fromDateTime = $(this).val();
    let req_typ = $("#req_type").val();

    // set min date
    let leaveDay = $("#leave_period").val();

    // Selected From Date
    let from = new Date(fromDateTime);

    // Last day of the selected month
    let lastDay = new Date(from.getFullYear(), from.getMonth() + 1, 0);

    let yyyy = lastDay.getFullYear();
    let mm = String(lastDay.getMonth() + 1).padStart(2, "0");
    let dd = String(lastDay.getDate()).padStart(2, "0");

    let maxDate =
      $(toSelector).attr("type") === "datetime-local"
        ? `${yyyy}-${mm}-${dd}T23:59`
        : `${yyyy}-${mm}-${dd}`;

    // Set minimum = selected From Date
    $(toSelector).attr("min", fromDateTime);

    // Half-day leave
    if (leaveDay == "1" || leaveDay == "2") {
      $(toSelector).attr("min", fromDateTime).attr("max", fromDateTime);
    } else {
      // Full-day leave / Week Off
      $(toSelector).attr("max", maxDate);
    }

    // clear invalid old value
    let toDateTime = $(toSelector).val();

    // let toDateTime = $(toSelector).val();

    if (toDateTime) {
      if (toDateTime < fromDateTime) {
        alert("To Date/Time cannot be less than From Date/Time");
        $(toSelector).val("");
      } else if (leaveDay != "1" && leaveDay != "2" && toDateTime > maxDate) {
        alert(
          "To Date/Time cannot be greater than the last day of the selected month",
        );
        $(toSelector).val("");
      }
    }
  });
}

function setCurrentMonthRestriction(fromSelector, toSelector) {
  let today = new Date();

  // First day of current month
  let minMonth = new Date(today.getFullYear(), today.getMonth(), 1);

  let yyyy = minMonth.getFullYear();
  let mm = String(minMonth.getMonth() + 1).padStart(2, "0");
  let dd = String(minMonth.getDate()).padStart(2, "0");

  let minDateTime = `${yyyy}-${mm}-${dd}T00:00`;
  let minDate = `${yyyy}-${mm}-${dd}`;

  if ($(fromSelector).attr("type") === "datetime-local") {
    $(fromSelector).attr("min", minDateTime);
  } else {
    $(fromSelector).attr("min", minDate);
  }
}

// For OT validation and display time format
function formatTime(time) {
  if (!time || typeof time !== "string") {
    return "";
  }

  let parts = time.split(":");

  if (parts.length < 2) {
    return time;
  }

  let hours = parseInt(parts[0], 10);
  let minutes = parts[1];

  let ampm = hours >= 12 ? "PM" : "AM";
  hours = hours % 12 || 12;

  return `${hours}:${minutes} ${ampm}`;
}

let approval_required = "";

function userTypeIdentification() {
  $.post(
    "api/regularization_files/user_type_identification.php",
    {},
    function (response) {
      const {
        approval_required,
        user_type,
        allowed_request_type = "",
        approval_view,
      } = JSON.parse(response);

      const hasRequestPermission = allowed_request_type
        .split(",")
        .includes("1");

      // Request UI
      $("#request_div, .add_reg").toggle(hasRequestPermission);

      // Approval UI
      const showMyApproval =
        approval_required == "1" ||
        (approval_required == "2" && approval_view == "1");

      $("#approval_div").toggle(showMyApproval);

      // Default selected radio
      if (user_type == "1") {
        $('input[name="regularization_type"][value="Approval"]').prop(
          "checked",
          true
        );
        getregularizationlist("Approval");
      } else if (hasRequestPermission) {
        $('input[name="regularization_type"][value="Request"]').prop(
          "checked",
          true
        );
        getregularizationlist("Request");
      } else {
        $('input[name="regularization_type"][value="Approval"]').prop(
          "checked",
          true
        );
        getregularizationlist("Approval");
      }
    }
  );
}

function getLeaveDays() {
  let fromDate = $("#from_date").val();
  let toDate = $("#to_date").val();

  $("#leave_days").val("");

  if (!fromDate || !toDate) {
    return;
  }

  let startDate = new Date(fromDate);
  let endDate = new Date(toDate);

  if (endDate < startDate) {
    $("#leave_days").val("");
    return;
  }

  let days = [];

  while (startDate <= endDate) {
    days.push(
      startDate.toLocaleDateString("en-US", {
        weekday: "long",
      }),
    );

    startDate.setDate(startDate.getDate() + 1);
  }

  $("#leave_days").val(days.join(", "));
}



function loadChart(company_id, shift_id, staff_id, date) {
  $.post(
    "api/attendance_ot_monitor_chart_files/get_staff_info.php",
    { company_id, shift_id, staff_id, date, type: 'attendance' },
    function (response) {
      drawChart(response, date);
    },
    "json",
  );
}

google.charts.load("current", {
  packages: ["timeline"],
});

// ========================================= CONVERT DATETIME =========================================

function convertDateTime(datetime) {
  let parts = datetime.split(/[- :]/);

  return new Date(
    parts[0], // year
    parts[1] - 1, // month
    parts[2], // day
    parts[3], // hour
    parts[4], // minute
    parts[5], // second
  );
}

// ========================================= DRAW CHART =========================================
function drawChart(chartData, selectedDate) {
  // ========================================= VALIDATE RESPONSE =========================================

  let container = document.getElementById("timeline_chart");
  container.innerHTML = ""; // Clear existing chart on reload

  if (!Array.isArray(chartData) || chartData.length === 0) {
    container.innerHTML = `
        <div style="height:150px; display:flex; align-items:center; justify-content:center; color:red; font-size:18px; font-weight:bold;">
            No Attendance Data Found
        </div>`;
    return;
  }

  // ========================================= CREATE GROUPS (STAFF NAMES / Y-AXIS) =========================================

  let uniqueStaff = [...new Set(chartData.map((item) => item.staff_name))];

  let groups = new vis.DataSet();
  uniqueStaff.forEach((name) => {
    groups.add({ id: name, content: `<b>${name}</b>` });
  });

  // =========================================  CREATE ITEMS (COLORED BLOCKS WITH TOOLTIP) =========================================

  let items = new vis.DataSet();

  chartData.forEach(function (row, index) {
    if (!row.start || !row.end || !row.staff_name) return;

    let startDate = convertDateTime(row.start);
    let endDate = convertDateTime(row.end);

    if (startDate >= endDate) return;

    // Create the HTML tooltip for the hover effect
    let hoverDetails = `
        <div style="padding: 5px;">
            <strong>${row.type}</strong><br>
            Start: ${startDate.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" })}<br>
            End: ${endDate.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" })}
        </div>
    `;

    items.add({
      id: index,
      group: row.staff_name,
      content: "", // This keeps the bar blank
      title: hoverDetails, // This adds the hover tooltip
      start: startDate,
      end: endDate,
      style: `background-color: ${row.color}; color: white; border: none; border-radius: 0px; font-size: 14px; height: 25px;`,
    });
  });

  // ========================================= DYNAMIC DATE RANGE & OPTIONS =========================================

  let dateParts = selectedDate.split("-");
  let year = parseInt(dateParts[0]);
  let month = parseInt(dateParts[1]) - 1;
  let day = parseInt(dateParts[2]);

  let minDate = new Date(year, month, day, 5, 0, 0); // 5:00 AM
  let maxDate = new Date(year, month, day, 23, 59, 59); // 11:59 PM

  let options = {
    orientation: "bottom",
    min: minDate,
    max: maxDate,
    start: minDate,
    end: maxDate,
    moveable: false,
    zoomable: false,
    stack: false,
    margin: {
      item: { horizontal: 0, vertical: 40 },
      axis: 20,
    },

    // 1. CHANGE STEP BACK TO 1 HOUR
    timeAxis: {
      scale: "hour",
      step: 1,
    },

    // 2. USE A CUSTOM FUNCTION TO ONLY SHOW ODD HOURS
    format: {
      minorLabels: function (date, scale, step) {
        // Safely get the current hour being drawn
        let d = new Date(date);
        let hours = d.getHours();

        // Check if the hour is an ODD number (5, 7, 9, 11, etc.)
        if (hours % 2 !== 0) {
          let ampm = hours >= 12 ? "PM" : "AM";
          let displayH = hours % 12;

          if (displayH === 0) displayH = 12;

          // Returns exactly what you asked for: "5.00 AM", "7.00 AM", etc.
          return displayH + ".00 " + ampm;
        }

        // If it is an EVEN hour (6, 8, 10), return nothing so it stays blank
        return "";
      },
    },
  };

  // ========================================= DRAW CHART =========================================

  let timeline = new vis.Timeline(container, items, groups, options);
}


function formatPermissionBalance(value, permissionType) {
  const balance = Math.max(0, parseFloat(value) || 0);

  if (Number(permissionType) === 1) {
    return balance;
  }

  const totalMinutes = Math.floor(balance);
  const days = Math.floor(totalMinutes / 1440);
  const hours = Math.floor((totalMinutes % 1440) / 60);
  const minutes = totalMinutes % 60;

  return `${days} D ${hours} H ${minutes} M`;
}
