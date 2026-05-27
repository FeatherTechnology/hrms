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
    $("#req_type").val("");
    $("#leave_type").val("");
    $("#balance_req").val("");
    $("#from_date").val("");
    $("#to_date").val("");
    $("#total_days").val("");
    $("#reason").val("");
    $("#hidden_id").val("");

    $("#to_date").attr("readonly", false);
    $("#req_type").attr("readonly", false);
    $("#leave_type").attr("readonly", false);
    $("#from_date").attr("readonly", false);
    $("#reason").attr("readonly", false);

    $("#back_btn").show();
    $("#balance_req").show();
    $(".staff_info_div").show();
    $(".add_reg").hide();
    $(".regularization_list").hide();
    $(".approval_div").hide();
    $("#leveType").hide();
    getuserdetails("");
  });

  // back button hide and show
  $("#back_btn").click(function () {
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

    if (status == "1") {
      swalError("Error", "This request has already been approved");
    } else if (status == "2") {
      swalError("Error", "This request has already been cancelled");
    } else {
      $("#back_btn").show();
      $(".staff_info_div").show();
      $(".approval_div").show();
      $(".add_reg").hide();
      $(".regularization_list").hide();
      $("#hidden_id").val(id);
      getedituserdetails(id, staff_id);
    }
  });

  // request type change
  $("#req_type").change(function () {
    let cmpy_id = $("#cmpy_id").val();
    let value = $(this).val();
    if (value == "1") {
      $(".leveType").show();
      $(".bal_req").show();
      $("#from_date").attr("type", "date");
      $("#to_date").attr("type", "date");

      getcmpyleavelist(cmpy_id);
    } else if (value == "2") {
      $(".bal_req").show();
      $(".leveType").hide();
      $("#from_date").attr("type", "datetime-local");
      $("#to_date").attr("type", "datetime-local");
      getbalancerequest("2", cmpy_id);
    } else if (value == "3") {
      $(".bal_req").show();
      $(".leveType").hide();
      $("#from_date").attr("type", "date");
      $("#to_date").attr("type", "date");
      getbalancerequest("3", cmpy_id);
    } else if (value == "4") {
      $(".bal_req").hide();
      $(".leveType").hide();
      $("#balance_req").val("");
      $("#leave_type").val("");
      $("#from_date").attr("type", "datetime-local");
      $("#to_date").attr("type", "datetime-local");
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

    if (status == "1") {
      swalError("Error", "This request has already been approved");
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
    let req_type = collData["req_type"];
    let req_date = collData["req_date"];
    let from_date = collData["from_date"];
    let to_date = collData["to_date"];
    let total_days = collData["total_days"];
    let reason = collData["reason"];
    let approval_type = collData["approval_type"];
    let app_from_date = collData["app_from_date"];
    let app_to_date = collData["app_to_date"];
    let remarks = collData["remarks"];
    let validationResults = [
      validateField(req_type, "req_type"),
      validateField(req_date, "req_date"),
      validateField(from_date, "from_date"),
      validateField(to_date, "to_date"),
      validateField(total_days, "total_days"),
      validateField(reason, "reason"),
    ];
    if (!validationResults.every((result) => result)) {
      isValid = false;
    }

    if ($(".approval_div").is(":visible")) {
      let approvalValidation = [
        validateField(approval_type, "approval_type"),
        validateField(app_from_date, "app_from_date"),
        validateField(app_to_date, "app_to_date"),
        validateField(remarks, "remarks"),
      ];

      if (!approvalValidation.every((r) => r)) {
        isValid = false;
      }

      // optional date check
      if (app_from_date && app_to_date && app_to_date < app_from_date) {
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
          $("#pending").prop("checked", true).trigger("click");
        },
        "json",
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

  // $("#from_date, #to_date").on("change", function () {
  //   let from = new Date($("#from_date").val());
  //   let to = new Date($("#to_date").val());

  //   let diffMs = to - from;

  //   if (diffMs <= 0) {
  //     $("#total_days").val("");
  //     $("#total_min").val("");
  //     return;
  //   }

  //   // total minutes (for DB)
  //   let totalMinutes = Math.floor(diffMs / (1000 * 60));
  //   $("#total_min").val(totalMinutes);

  //   // breakdown
  //   let days = Math.floor(totalMinutes / (24 * 60));
  //   let hours = Math.floor((totalMinutes % (24 * 60)) / 60);
  //   let minutes = totalMinutes % 60;

  //   // show in UI
  //   $("#total_days").val(
  //     days + " Days " + hours + " Hours " + minutes + " Minutes",
  //   );
  // });

  // $("#app_from_date, #app_to_date").on("change", function () {
  //   let from = new Date($("#app_from_date").val());
  //   let to = new Date($("#app_to_date").val());

  //   let diffMs = to - from;

  //   if (diffMs < 0) {
  //     $("#app_total_min").val("");
  //     $("#app_total_days").val("");
  //     return;
  //   }

  //   // total minutes (for DB)
  //   let totalMinutes = Math.floor(diffMs / (1000 * 60));
  //   $("#app_total_min").val(totalMinutes);

  //   // breakdown
  //   let days = Math.floor(totalMinutes / (24 * 60));
  //   let hours = Math.floor((totalMinutes % (24 * 60)) / 60);
  //   let minutes = totalMinutes % 60;

  //   // show in UI
  //   $("#app_total_days").val(
  //     days + " Days " + hours + " Hours " + minutes + " Minutes",
  //   );
  // });

  // from date change
  // $("#from_date").on("change", function () {
  //   let fromDateTime = $(this).val();
  //   $("#to_date").attr("min", fromDateTime);

  //   $("#to_date").on("change", function () {
  //     let toDateTime = $(this).val();

  //     if (toDateTime < fromDateTime) {
  //       alert("To Date/Time cannot be less than From Date/Time");
  //       $(this).val("");
  //     }
  //   });
  // });

  // $("#app_from_date").on("change", function () {
  //   let fromDateTime = $(this).val();
  //   $("#app_to_date").attr("min", fromDateTime);

  //   $("#app_to_date").on("change", function () {
  //     let toDateTime = $(this).val();

  //     if (toDateTime < fromDateTime) {
  //       alert("To Date/Time cannot be less than From Date/Time");
  //       $(this).val("");
  //     }
  //   });
  // });
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
  $("#regularization_table").DataTable().destroy();
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
          var search = $("input[type=search]").val();
          data.search = search;
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
      console.log("branch_id", response.branch_id);
      $("#staff_id").val(response.staff_id);
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

      $("#staff_id").val(response.staff_id);
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
      $("#req_date").val(response.req_date);
      $("#leave_type_id").val(response.leave_type);
      $("#reason").val(response.reason);
      $("#from_date").val(response.from_date.replace(" ", "T").slice(0, 16));
      $("#to_date").val(response.to_date.replace(" ", "T").slice(0, 16));
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

      let days = Math.floor(response.total_min / (24 * 60));
      let hours = Math.floor((response.total_min % (24 * 60)) / 60);
      let minutes = response.total_min % 60;
      $("#total_days").val(
        days + " Days " + hours + " Hours " + minutes + " Minutes",
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

        $("#leave_type").append( "<option value='" + val["id"] + "' " + selected + ">" +  val["leave_type"] + "</option>", );
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
function calculateDateDiff( fromSelector,  toSelector, totalMinSelector,  totalDaysSelector,) {
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

  // invalid or negative
  if (diffMs <= 0) {
    $(totalMinSelector).val(0);
    $(totalDaysSelector).val("0 Days 0 Hours 0 Minutes");
    return;
  }

  // total minutes
  let totalMinutes = Math.floor(diffMs / (1000 * 60));
  $(totalMinSelector).val(totalMinutes);

  // breakdown
  let days = Math.floor(totalMinutes / (24 * 60));
  let hours = Math.floor((totalMinutes % (24 * 60)) / 60);
  let minutes = totalMinutes % 60;

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

  $(toSelector).on("change", function () {
    let fromDateTime = $(fromSelector).val();
    let toDateTime = $(this).val();

    if (toDateTime < fromDateTime) {
      alert("To Date/Time cannot be less than From Date/Time");
      $(this).val("");
    }
  });
}
