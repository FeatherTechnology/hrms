$(document).ready(function () {
  // to get regularization list
  $("input[name=regularization_type]").click(function () {
    let regType = $(this).val();
    if (regType == "0") {
      getregularizationlist("0");
    } else if (regType == "1") {
      getregularizationlist("1");
    } else if (regType == "2") {
      getregularizationlist("2");
    }
  });

  // to add the new regularization
  $(".add_reg").click(function () {
    setCurrentMonthRestriction("#from_date", "#to_date");
    $(
      "#req_type,#leave_type,#balance_req,#from_date,#to_date,#total_days,#reason,#hidden_id,#leave_type_id",
    ).val("");

    $(
      ".req_div input, .req_div select,.req_div textarea, .approval_div input, .approval_div select,.approval_div textarea",
    ).css("border", "1px solid #cecece");

    $("#to_date").attr("readonly", false);
    $("#req_type").attr("readonly", false);
    $("#leave_type").attr("readonly", false);
    $("#from_date").attr("readonly", false);
    $("#reason").attr("readonly", false);

    $("#back_btn,#balance_req,.staff_info_div").show();

    $(".add_reg,.regularization_list,.approval_div,#leveType").hide();

    getuserdetails("");
  });

  // back button hide and show
  $("#back_btn").click(function () {
    $('input[name="regularization_type"][value="0"]')
      .prop("checked", true)
      .trigger("click");
    $("#back_btn").hide();
    $(".staff_info_div").hide();
    $(".approval_div").hide();
    $(".add_reg").show();
    $(".regularization_list").show();
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

      if (appFromDate > currentdate) {
        // app_from date is greater than current date
        $("#back_btn,.staff_info_div,.approval_div").show();

        $(".add_reg,.regularization_list").hide();

        $("#hidden_id").val(id);
        getedituserdetails(id, staff_id);
      } else {
        swalError("Error", "This request has already Approved.");
      }
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
    $(".leveType,#from_date,#to_date,#leave_type_id,#balance_req").val("");
    if (value == "1") {
      $(".leveType").show();
      $(".bal_req").show();
      $("#from_date,#to_date").attr("type", "date");

      getcmpyleavelist(cmpy_id);
    } else if (value == "2") {
      $(".bal_req").show();
      $(".leveType").hide();
      $("#from_date,#to_date").attr("type", "datetime-local");
      getbalancerequest("2", cmpy_id);
    } else if (value == "3") {
      $(".bal_req").show();
      $(".leveType").hide();
      $("#from_date,#to_date").attr("type", "date");
      getbalancerequest("3", cmpy_id);
    } else if (value == "4") {
      $(".bal_req").hide();
      $(".leveType").hide();
      $("#leave_type,#leave_type_id,#balance_req").val("");
      $("#from_date,#to_date").attr("type", "datetime-local");
    }
  });

  // leave type change
  $("#leave_type").change(function () {
    let leave_type = $("#leave_type").val();
    getbalancerequest("1", leave_type);
  });

  // delete regularization
  $(document).on("click", ".delete_reg", function () {
    let id = $(this).data("id");
    let status = $(this).data("status");
    let appfrom = $(this).data("appfrom");
    let currentdate = new Date();

    if (status == "1") {
      // swalError("Error", "This request has already been approved");
      let appFromDate = new Date(appfrom);

      if (appFromDate > currentdate) {
        // app_from date is greater than current date
        deleteregularization(id);
      } else {
        swalError("Error", "This request has already Approved.");
      }
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
      balance_req: $("#balance_req").val(),
      req_date: $("#req_date").val(),
      from_date: $("#from_date").val(),
      to_date: $("#to_date").val(),
      total_min: $("#total_min").val(),
      reason: $("#reason").val(),
      total_days: $("#total_days").val(),
      approval_type: $("#approval_type").val(),
      app_from_date: $("#app_from_date").val(),
      app_to_date: $("#app_to_date").val(),
      remarks: $("#remarks").val(),
      app_total_min: $("#app_total_min").val(),
      hidden_id: $("#hidden_id").val(),
    };
    let isValid = true;
    // let req_type = collData["req_type"];
    // let req_date = collData["req_date"];
    // let from_date = collData["from_date"];
    // let to_date = collData["to_date"];
    // let total_days = collData["total_days"];
    // let reason = collData["reason"];
    // let approval_type = collData["approval_type"];
    // let app_from_date = collData["app_from_date"];
    // let app_to_date = collData["app_to_date"];
    // let remarks = collData["remarks"];
    let validationResults = [
      validateField(collData["req_type"], "req_type"),
      validateField(collData["req_date"], "req_date"),
      validateField(collData["from_date"], "from_date"),
      validateField(collData["to_date"], "to_date"),
      validateField(collData["total_days"], "total_days"),
      validateField(collData["reason"], "reason"),
    ];
    if (!validationResults.every((result) => result)) {
      isValid = false;
    }
    if ($(".approval_div").is(":visible")) {
      let approvalValidation = [
        validateField(collData["approval_type"], "approval_type"),
        validateField(collData["remarks"], "remarks"),
      ];

      if (approval_type == "1") {
        approvalValidation.push(
          validateField(collData["app_from_date"], "app_from_date"),
          validateField(collData["app_to_date"], "app_to_date"),
        );
      }

      if (!approvalValidation.every((r) => r)) {
        isValid = false;
      }

      if (
        approval_type == "1" &&
        app_from_date &&
        app_to_date &&
        app_to_date < app_from_date
      ) {
        swalError("Error", "Approved To Date cannot be less than From Date");
        isValid = false;
      }
    }

    req_type = parseInt($("#req_type").val());
    let balance_req = parseInt($("#balance_req").val());

    if (req_type < 3 && balance_req <= 0) {
      swalError("Error", "No Balance Request Available");

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
    calculateDateDiff("#from_date", "#to_date", "#total_min", "#total_days");
  });

  // Second section
  $("#app_from_date, #app_to_date").on("change", function () {
    calculateDateDiff(
      "#app_from_date",
      "#app_to_date",
      "#app_total_min",
      "#app_total_days",
    );
  });

  $("#approval_type").on("change", function () {
    let value = $(this).val();

    if (value == "2") {
      $("#app_from_date, #app_to_date").prop("readonly", true);
      $("#app_from_date,#app_to_date,#app_total_days,#app_total_min").val("");
    } else {
      $("#app_from_date, #app_to_date").prop("readonly", false);
    }
  });

  setDateValidation("#from_date", "#to_date");
  setDateValidation("#app_from_date", "#app_to_date");
});
// document end
$(function () {
  $('input[name="regularization_type"][value="0"]')
    .prop("checked", true)
    .trigger("click");
});

// function start

// to get the regularization list
function getregularizationlist(sts) {
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
          data.sts = sts;
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
      $("#req_type").attr("readonly", true);
      $("#leave_type").attr("readonly", true);
      $("#from_date").attr("readonly", true);
      $("#reason").attr("readonly", true);
      $(
        "#approval_type,#app_from_date,#app_to_date,#app_total_days,#app_total_min,#remarks",
      ).val("");

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
      $("#req_type").val(response.req_type);
      $("#req_date").val(
        response.req_date.split(" ")[0].split("-").reverse().join("-"),
      );
      $("#leave_type_id").val(response.leave_type);
      $("#reason").val(response.reason);
      $("#from_date").val(response.from_date.replace(" ", "T").slice(0, 16));
      $("#to_date").val(response.to_date.replace(" ", "T").slice(0, 16));

      if (response.req_type == 1 || response.req_type == 3) {
        $("#app_from_date,#app_to_date").attr("type", "date");
      } else {
        $("#app_from_date,#app_to_date").attr("type", "datetime-local");
      }

      setCurrentMonthRestriction("#app_from_date", "#app_to_date");
      setApprovalDate("#app_from_date", response.approved_from_date);
      setApprovalDate("#app_to_date", response.approved_to_date);
      
      $("#app_total_min").val(response.approved_total_min);
      $("#approval_type").val(response.status || 0);
      $("#remarks").val(response.remarks);

      if (response.req_type == 1) {
        $(".leveType").show();
        getcmpyleavelist(response.cmpy_id);
      } else {
        $(".leveType").hide();
      }

      if (response.req_type != "4") {
        $("#balance_req").show();
        $("#balance_req").val(response.balance_req);
      }
      let totalMin = parseInt(response.total_min, 10);
      let days = Math.floor(totalMin / (24 * 60));
      let hours = Math.floor((totalMin % (24 * 60)) / 60);
      let minutes = totalMin % 60;

      $("#total_days").val(
        days + " Days " + hours + " Hours " + minutes + " Minutes",
      );

      let app_totalMin = parseInt(response.approved_total_min, 10);
      let app_days = Math.floor(app_totalMin / (24 * 60));
      let app_hours = Math.floor((app_totalMin % (24 * 60)) / 60);
      let app_minutes = app_totalMin % 60;

      // $("#total_days").val(
      //   days + " Days " + hours + " Hours " + minutes + " Minutes",
      // );

      // let app_days = Math.floor(response.approved_total_min / (24 * 60));
      // let app_hours = Math.floor((response.total_min % (24 * 60)) / 60);
      // let app_minutes = response.total_min % 60;
      $("#app_total_days").val(
        app_days + " Days " + app_hours + " Hours " + app_minutes + " Minutes",
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

// to get the balance request
function getbalancerequest(req_type, cmpy_id) {
  let staff_id = $("#stf_prf_id").val();

  $.post(
    "api/regularization_files/get_balance_request.php",
    { req_type, cmpy_id, staff_id },
    function (response) {
      $("#balance_req").val(response.balance);
    },
    "json",
  );
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
    $(totalDaysSelector).val("0 Days 0 Hours 0 Minutes");
    return;
  }

  let from = new Date(fromVal);
  let to = new Date(toVal);

  let diffMs = to - from;

  // invalid date
  let reqType = $("#req_type").val();

  // Permission type
  if (reqType == "2") {
    // less than 1 minute
    if (diffMs < 1000 * 60) {
      alert("Permission must have at least 1 minute difference");

      $(toSelector).val("");

      $(totalMinSelector).val(0);

      $(totalDaysSelector).val("0 Days 0 Hours 0 Minutes");

      return;
    }
  } else {
    // other request types
    if (diffMs < 0) {
      alert("To Date must be greater than From Date");

      $(toSelector).val("");

      $(totalMinSelector).val(0);

      $(totalDaysSelector).val("0 Days 0 Hours 0 Minutes");

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
  let balanceDays = parseInt($("#balance_req").val());

  if (days > balanceDays) {
    alert("You only have " + balanceDays + " leave days balance");

    $(toSelector).val("");

    $(totalMinSelector).val(0);

    $(totalDaysSelector).val("0 Days 0 Hours 0 Minutes");

    return;
  }

  // display
  $(totalDaysSelector).val(
    days + " Days " + hours + " Hours " + minutes + " Minutes",
  );
}

// to set the validation for the from and to date
function setDateValidation(fromSelector, toSelector) {
  $(fromSelector).on("change", function () {
    let fromDateTime = $(this).val();

    // set min date
    $(toSelector).attr("min", fromDateTime);

    // clear invalid old value
    let toDateTime = $(toSelector).val();

    if (toDateTime && toDateTime < fromDateTime) {
      alert("To Date/Time cannot be less than From Date/Time");
      $(toSelector).val("");
    }
  });
}

function setCurrentMonthRestriction(fromSelector, toSelector) {
  let today = new Date();

  // First day of previous month
  let minMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);

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

function setApprovalDate(id, value) {
  if (!value) {
    $(id).val("");
    return;
  }

  let type = $(id).attr("type");
  let formattedDate = value.replace(" ", "T");

  if (type === "date") {
    $(id).val(formattedDate.slice(0, 10));
  } else if (type === "datetime-local") {
    $(id).val(formattedDate.slice(0, 16));
  }
}
