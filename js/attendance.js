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

    let entryDate = $("#entry_date").val();
    let entryTime = $("#entry_time").val();

    let entryDateTime = "";
    if (entryDate && entryTime) {
      entryDateTime = entryDate + " " + entryTime + ":00";
    }

    let collData = {
      att_id: $("#att_id").val(),
      stf_prf_id: $("#stf_prf_id").val(),
      cmpy_id: $("#cmpy_id").val(),
      branch_id: $("#branch_id").val(),
      dep_id: $("#dep_id").val(),
      des_id: $("#des_id").val(),
      team_id: $("#team_id").val(),
      staff_type: $("#staff_type").val(),
      entry_time: entryDateTime, // Combined Date + Time
      reason: $("#reason").val(),
    };
    let cmy = $("#cmpy_name").val();
    let brnh = $("#branch_name").val();
    let date = $("#date").val();

    let isValid = true;
    let entry_date = collData["entry_date"];
    let entry_time = collData["entry_time"];
    let reason = collData["reason"];
    let validationResults = [
      validateField($("#entry_date").val(), "entry_date"),
      validateField($("#entry_time").val(), "entry_time"),
      validateField(collData["reason"], "reason"),
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

  let selectedDate = $("#date").val(); // dd-mm-yyyy

  if (selectedDate) {
    let dateParts = selectedDate.split("-");
    let selectedMonth = parseInt(dateParts[1], 10);

    let currentMonth = new Date().getMonth() + 1; // 1-12

    if (selectedMonth !== currentMonth) {
      swalError(
        "Warning",
        "Attendance can be edited only for the current month.",
      );
      return false;
    }
  }

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
        $("#entry_date").val(response.entry_time.slice(0, 10));
      } else {
        $("#entry_date").val(date);
      }
      $("#entry_time").val(
        response.entry_time ? response.entry_time.slice(11) : "",
      );
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

/* --- Load Chart --- */
function loadChart(company_id, shift_id, staff_id, date) {
  $.post(
    "api/attendance_ot_monitor_chart_files/get_staff_info.php",
    { company_id, shift_id, staff_id, date },
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
