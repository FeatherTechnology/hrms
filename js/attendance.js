$(document).ready(function () {
  // company Name change to get branch list
  $("#cmpy_name").change(function () {
    let cmpy_id = $(this).val();
    getBranchList(cmpy_id);
  });

  // toget the attendance list
  $("#submit_search").click(function () {
    let company_id = $("#cmpy_name").val();
    let branch_id = $("#branch_name").val();
    let date = $("#date").val();
    if (company_id == "" || branch_id == "" || date == "") {
      swalError("Error", "Please Filled The Manditaory Feild");
    } else {
      getAttendanceList(company_id, branch_id, date);
    }
  });

  // go to list page
  $("#back_btn").click(function () {
    $(".attendance_details").hide();
    $(".search_details").show();
    let company_id = $("#cmpy_name").val();
    let branch_id = $("#branch_name").val();
    let date = $("#date").val();
    getAttendanceList(company_id, branch_id, date);
    // getCompanyList();
  });

  //  submit attendance
  $("#submit_attendance").click(function () {
    event.preventDefault();
    let collData = {
      att_id: $("#att_id").val(),
      stf_prf_id: $("#stf_prf_id").val(),
      cmpy_id: $("#cmpy_id").val(),
      branch_id: $("#branch_id").val(),
      dep_id: $("#dep_id").val(),
      des_id: $("#des_id").val(),
      team_id: $("#team_id").val(),
      staff_type: $("#staff_type").val(),
      entry_time: $("#entry_time").val(),
      reason: $("#reason").val(),
    };
    let cmy = $("#cmpy_name").val();
    let brnh = $("#branch_name").val();
    let date = $("#date").val();

    let isValid = true;
    let entry_time = collData["entry_time"];
    let reason = collData["reason"];
    let validationResults = [
      validateField(collData["entry_time"], "entry_time"),
      validateField( collData["reason"], "reason"),
    ];

    if (!validationResults.every((result) => result)) {
      isValid = false;
    }

    if (isValid) {
      swalConfirm(
        "Are you sure?",
        "Do you want to submit this Attendance ?",
        function () {
          submitAttendance(collData, cmy, brnh, date);
        },
      );
    }
  });
});
// Document End

// initial load
$(function () {
  getCompanyList();
});

// to get the attendamce Details
$(document).on("click", ".edit_add", function () {
  let staff_id = $(this).data("id");
  let att_id = $(this).data("att_id");
  let date = $("#date").val();
  $(".attendance_details").show();
  $(".search_details").hide();
  // attendance_details
  $("#update_attendance_div input").css("border", "1px solid #cecece");
  $("#update_attendance_div textarea").css("border", "1px solid #cecece");

  getStaffDetails(staff_id, att_id, date);
});

// Function Start

// to get company list
function getCompanyList() {
  $.post(
    "api/attendance_files/get_company_list.php",
    {},
    function (response) {
      $("#cmpy_name").empty();
      $("#cmpy_name").append("<option value=''>Select Company Name</option>");

      $.each(response, function (index, val) {
        $("#cmpy_name").append(
          "<option value='" +
            val["id"] +
            "'>" +
            val["company_name"] +
            "</option>",
        );
      });
    },
    "json",
  );
}

// to get the branch list
function getBranchList(cmpy_id) {
  $.post(
    "api/attendance_files/get_branch_list.php",
    { cmpy_id },
    function (response) {
      $("#branch_name").empty();
      $("#branch_name").append("<option value=''>Select Branch Name</option>");

      $.each(response, function (index, val) {
        $("#branch_name").append(
          "<option value='" +
            val["id"] +
            "'>" +
            val["branch_name"] +
            "</option>",
        );
      });
    },
    "json",
  );
}

// to get the attendance list
function getAttendanceList(company_id, branch_id, date) {
  $("#attendance_table").DataTable().destroy();
  getUserAccess(function (downloadAccess) {
    let buttons = [];

    // Add Excel button only if download access is granted
    if (downloadAccess === 1) {
      excelTitle = "Attendance List";
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

    $("#attendance_table").DataTable({
      order: [[0, "desc"]],
      processing: true,
      serverSide: true,
      serverMethod: "post",
      ajax: {
        url: "api/attendance_files/get_attendance_list.php",
        data: function (data) {
          var search = $("input[type=search]").val();
          data.search = search;
          data.company_id = company_id;
          data.branch_id = branch_id;
          data.date = date;
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

// to get the staff deatails
function getStaffDetails(staff_id, att_id, date) {
  $.post(
    "api/attendance_files/get_staff_details.php",
    { staff_id, att_id },
    function (response) {
      let staffType = { 1: "Employer", 2: "Employee" };
      $("#staff_id").val(response.staff_id);
      $("#stf_prf_id").val(response.stf_id);
      $("#staff_name").val(response.staff_name);
      $("#cmpy_id").val(response.cmpy_id);
      $("#company_name").val(response.company_name);
      $("#branch_id").val(response.brch_id);
      $("#brch_name").val(response.branch_name);
      $("#dep_id").val(response.dep_id);
      $("#department").val(response.department_name);
      $("#des_id").val(response.des_id);
      $("#designation").val(response.designation);
      $("#team_id").val(response.team_id);
      $("#team").val(response.team_name);
      $("#staff_type_id").val(response.staff_type);
      $("#staff_type").val(staffType[response.staff_type] || "");
      $("#att_id").val(response.att_id);
      $("#reason").val(response.reason ? response.reason : "");
      if (response.entry_time) {
        $("#entry_time").val(
          response.entry_time.replace(" ", "T").slice(0, 16),
        );
      } else {
        $("#entry_time").val(date + "T00:00");
      }
    },
    "json",
  );
}

// submit modified attendance
function submitAttendance(collData, cmy, brnh, date) {
  $.post(
    "api/attendance_files/submit_attendance.php",
    collData,
    function (response) {
      if (response.result == "1") {
        swalSuccess("Success", "Attendance Added Successfully.");
        $(".attendance_details").hide();
        $(".search_details").show();
        getAttendanceList(cmy, brnh, date);
      } else if (response.result == "2") {
        swalError("Error", "Failed to Add Attendance");
      } else if (response.result == "3") {
        swalSuccess("Success", "Attendance Updated Successfully.");
        $(".attendance_details").hide();
        $(".search_details").show();
        getAttendanceList(cmy, brnh, date);
      } else if (response.result == "4") {
        swalError("Error", "Failed to Update Attendance");
      }
    },
    "json",
  );
}
