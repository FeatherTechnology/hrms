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
      "#req_type,#leave_type,#balance_req,#from_date,#to_date,#reason,#hidden_id,#leave_type_id,#leave_period",
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
    $("#reason").attr("readonly", false);

    $("#back_btn,#balance_req,.staff_info_div").show();

    $(
      ".add_reg,.regularization_list,.approval_div,.leveType,.bal_req,.ot_req,.Lev_per",
    ).hide();

    getuserdetails("");
  });

  // back button hide and show
  $("#back_btn").click(function () {
    userTypeIdentification();
    // if (approval_required == 1) {
    //   $('input[name="regularization_type"][value="Approval"]').prop(
    //     "checked",
    //     true,
    //   );

    //   getregularizationlist("Approval");
    // } else {
    //   $('input[name="regularization_type"][value="Request"]').prop(
    //     "checked",
    //     true,
    //   );

    //   getregularizationlist("Request");
    // }

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
    console.log("ff");
    let reqType = $("#req_type").val();

    if (!$("#from_date").val() || !$("#to_date").val()) {
      return;
    }
    await getbalancerequest();
    let fromDate = new Date($("#from_date").val());

    let toDate = new Date($("#to_date").val());

    let shiftDate = $("#from_date").val().split("T")[0];

    let shiftStartDateTime = new Date(shiftDate + "T" + shiftStart);
    let shiftEndDateTime = new Date(shiftDate + "T" + shiftEnd);

    let formattedStart = formatTime(shiftStart);
    let formattedEnd = formatTime(shiftEnd);

    // OT Validation
    if (reqType == "4") {
      let isBeforeShift = toDate <= shiftStartDateTime;
      let isAfterShift = fromDate >= shiftEndDateTime;

      if (!(isBeforeShift || isAfterShift)) {
        swalError(
          "Warning",
          `OT can only be applied before the shift starts (${formattedStart}) or after the shift ends (${formattedEnd}).`,
        );

        $("#from_date, #to_date").val("");
      }
    }

    // Permission Validation
    if (reqType == "2") {
      let isWithinShift =
        fromDate >= shiftStartDateTime && toDate <= shiftEndDateTime;

      if (!isWithinShift) {
        swalError(
          "Warning",
          `Permission can only be applied within the shift timings (${formattedStart} to ${formattedEnd}).`,
        );

        $("#from_date, #to_date").val("");
      }
    }
    calculateDateDiff("#from_date", "#to_date", "#total_min", "#total_days");
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
      getedituserdetails(id, staff_id);
      // } else {
      //   swalError("Error", "This request has already Approved.");
      // }
    } else if (status == "2") {
      swalError("Warning", "This request has already been cancelled");
    } else {
      $("#back_btn,.staff_info_div,.approval_div").show();

      $(".add_reg,.regularization_list").hide();

      $("#hidden_id").val(id);

      getedituserdetails(id, staff_id);
    }
  });

  // request type change
  $("#req_type").change(function () {
    let cmpy_id = $("#cmpy_id").val();
    let value = $(this).val();
    $("#total_days").empty();
    $(
      ".leveType,#from_date,#to_date,#leave_type_id,#balance_req,#leave_period",
    ).val("");
    if (value == "1") {
      $(".leveType").show();
      $(".Lev_per").show();
      $(".bal_req").show();
      $(".ot_req").hide();
      $("#from_date,#to_date").attr("type", "date");
      getcmpyleavelist(cmpy_id);
    } else if (value == "2") {
      $(".Lev_per").hide();
      $(".bal_req").show();
      $(".leveType").hide();
      $(".ot_req").hide();
      $("#from_date,#to_date").attr("type", "datetime-local");
      // getbalancerequest("2", cmpy_id);
    } else if (value == "3") {
      $(".Lev_per").show();
      $(".bal_req").show();
      $(".leveType").hide();
      $(".ot_req").hide();
      $("#from_date,#to_date").attr("type", "date");
      // getbalancerequest("3", cmpy_id);
    } else if (value == "4") {
      $(".Lev_per").hide();
      $(".bal_req").hide();
      $(".leveType").hide();
      $(".ot_req").show();
      $("#leave_type,#leave_type_id,#balance_req,#leave_period").val("");
      $("#from_date,#to_date").attr("type", "datetime-local");

      // getbalancerequest("4", cmpy_id);
    }

    $("#request_form input").css("border", "1px solid #cecece");
    $("#request_form select").css("border", "1px solid #cecece");
    $("#request_form textarea").css("border", "1px solid #cecece");

    setCurrentMonthRestriction("#from_date", "#to_date");
  });

  // leave type change
  $("#leave_type").change(function () {
    let leave_type = $("#leave_type").val();
    $("#to_date").val("");
    $("#to_date,#total_days,#from_date,#balance_req").val("");
    // getbalancerequest("1", leave_type);
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

    // Recalculate total days after changing leave period
    // if ($("#from_date").val() && $("#to_date").val()) {
    //   calculateDateDiff("#from_date", "#to_date", "#total_min", "#total_days");
    // }
  });

  // delete regularization
  $(document).on("click", ".delete_reg", function () {
    let id = $(this).data("id");
    let status = $(this).data("status");
    let from_date = $(this).data("from_date");
    let currentdate = new Date();

    if (status == "1") {
      // // swalError("Error", "This request has already been approved");
      // let appFromDate = new Date(from_date);

      // if (appFromDate > currentdate) {
      //   // app_from date is greater than current date
      //   deleteregularization(id);
      // } else {
      swalError("Error", "This request has already Approved.");
      // }
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
      leave_period: $("#leave_period").val(),
      balance_req: $("#balance_req").val(),
      current_month_ot_count: $("#current_month_ot_count").val(),
      req_date: $("#req_date").val(),
      from_date: $("#from_date").val(),
      to_date: $("#to_date").val(),
      total_min: $("#total_min").val(),
      reason: $("#reason").val(),
      approval_type: $("#approval_type").val(),
      remarks: $("#remarks").val(),
      hidden_id: $("#hidden_id").val(),
    };
    let isValid = true;

    let validationResults = [
      validateField(collData["req_type"], "req_type"),
      validateField(collData["req_date"], "req_date"),
      validateField(collData["from_date"], "from_date"),
      validateField(collData["to_date"], "to_date"),
      validateField(collData["reason"], "reason"),
    ];

    if (collData["req_type"] == 1) {
      validationResults.push(
        validateField($("#leave_type").val(), "leave_type"),
        validateField($("#leave_period").val(), "leave_period"),
      );
    }
    if (collData["req_type"] == 3) {
      validationResults.push(
        validateField($("#leave_period").val(), "leave_period"),
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
          $.post(
            "api/regularization_files/submit_regularization.php",
            collData,
            function (response) {
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
                swalError(
                  "Error",
                  "Already regularization request exists for this date",
                );
              }
              // $("#pending").prop("checked", true).trigger("click");
            },
            "json",
          );
        },
      );
    }
  }); //submit END.

  // First section
  $("#from_date, #to_date").on("change", function () {
    // calculateDateDiff("#from_date", "#to_date", "#total_min", "#total_days");
  });

  setDateValidation("#from_date", "#to_date");
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

// to get the regularization list
function getregularizationlist(type) {
  if ($.fn.DataTable.isDataTable("#regularization_table")) {
    $("#regularization_table").DataTable().destroy();
  }
  getUserAccess(function (downloadAccess) {
    let buttons = [];

    // Add Excel button only if download access is granted
    if (downloadAccess === 1) {
      excelTitle = "Regularization Report List";
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
    });
  });
}

// to get the user deatils
function getuserdetails(userid) {
  let status = 0;
  let id = "";
  $.post(
    "api/regularization_files/get_user_details.php",
    { id, userid, status },
    function (response) {
      $("#staff_id").val(response.staff_id);
      $("#staff_type").val(response.staff_type);
      $("#stf_prf_id").val(response.id);
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
      $("#req_date").val(
        new Date().toLocaleDateString("en-GB").replace(/\//g, "-"),
      );
    },
    "json",
  );
}

// to get the user details using edit
function getedituserdetails(id, userid) {
  let status = 1;

  $.post(
    "api/regularization_files/get_user_details.php",
    { id, userid, status },
    function (response) {
      $("#to_date").attr("readonly", true);
      $("#req_type").prop("disabled", true);
      $("#leave_type").prop("disabled", true);
      $("#leave_period").prop("disabled", true);
      $("#from_date").attr("readonly", true);
      $("#reason").attr("readonly", true);
      $("#approval_type,#remarks").val("");
      if ($("#req_type option").length <= 1) {
        $("#req_type").append(`
    <option value="1">Leave</option>
    <option value="2">Permission</option>
    <option value="3">Week Off</option>
    <option value="4">OT</option>
  `);
      }

      // select response request type
      $("#req_type").val(response.req_type);

      $("#staff_id").val(response.staff_id);
      $("#staff_type").val(response.staff_type);
      $("#stf_prf_id").val(response.id);
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
      // $("#req_type").val(response.req_type);
      $("#req_date").val(
        response.req_date.split(" ")[0].split("-").reverse().join("-"),
      );
      $("#leave_type_id").val(response.leave_type);
      $("#reason").val(response.reason);
      $("#from_date").val(response.from_date.replace(" ", "T").slice(0, 16));
      $("#to_date").val(response.to_date.replace(" ", "T").slice(0, 16));

      $("#approval_type").val(response.status || 0);
      $("#remarks").val(response.remarks);

      if (response.req_type == 1) {
        $(".leveType").show();
        getcmpyleavelist(response.cmpy_id);
      } else {
        $(".leveType").hide();
      }
      if (response.req_type == 1 || response.req_type == 3) {
        $(".Lev_per").show();
        $("#leave_period").val(response.leave_period);
      } else {
        $(".Lev_per").hide();
        $("#leave_period").val("");
      }

      if (response.req_type != "4") {
        $(".bal_req").show();
        $("#balance_req").val(response.balance_req);
      } else {
        $(".bal_req").hide();
      }

      if (response.req_type == "4") {
        $(".ot_req").show();
        $("#current_month_ot_count").val(response.current_month_ot_count || 0);
      } else {
        $(".ot_req").hide();
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

    $("#balance_req").val(response.balance);
    $("#current_month_ot_count").val(response.current_month_ot_count);

    shiftStart = response.start_time;
    shiftEnd = response.end_time;
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
    // Convert HH:MM:SS to Date objects
    let start = new Date(`1970-01-01T${shiftStart}`);
    let end = new Date(`1970-01-01T${shiftEnd}`);

    console.log("hhh", start);
    console.log("ddd", end);
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
    console.log("hrs", hours);

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
      } = JSON.parse(response);

      const hasRequestPermission = allowed_request_type
        .split(",")
        .includes("1");

      // Request UI
      $("#request_div, .add_reg").toggle(hasRequestPermission);

      // Approval UI
      $("#approval_div").toggle(approval_required == "1");

      // Default selected radio
      if (user_type == "1") {
        $('input[name="regularization_type"][value="Approval"]').prop(
          "checked",
          true,
        );
        getregularizationlist("Approval");
      } else if (hasRequestPermission) {
        $('input[name="regularization_type"][value="Request"]').prop(
          "checked",
          true,
        );
        getregularizationlist("Request");
      } else {
        $('input[name="regularization_type"][value="Approval"]').prop(
          "checked",
          true,
        );
        getregularizationlist("Approval");
      }
    },
  );
}
