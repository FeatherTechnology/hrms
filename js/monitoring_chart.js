$(document).ready(function () {
  getCompanyName();

  $("#company_name").on("change", function () {
    let company_id = $(this).val();
    getShiftName(company_id);
  });

  $("#shift_name").on("change", function () {
    let company_id = $("#company_name").val();
    let shift_id = $(this).val();
    getStaffName(company_id, shift_id);
  });

  $("#search_attendance_ot_monitor").on("click", function () {
    let company_id = $("#company_name").val();
    let shift_id = $("#shift_name").val();
    let staff_id = $("#staff_name").val();
    let date = $("#date").val();
    if (
      company_id === "" ||
      shift_id === "" ||
      staff_id === "" ||
      date === ""
    ) {
      swalError("Warning", "Please select all fields.");
      return;
    }
    loadChart(company_id, shift_id, staff_id, date);
  });
});

/* --- Get Company Name --- */
function getCompanyName() {
  $.ajax({
    url: "api/branch_creation/getCompanyName.php",
    type: "POST",
    data: {},
    dataType: "json",
    cache: false,
    success: function (response) {
      let dropdown = $("#company_name");

      dropdown.empty(); // clear existing

      dropdown.append('<option value="">Select Company Name</option>');

      // assuming response is array of objects
      $.each(response, function (index, item) {
        dropdown.append(
          `<option value="${item.id}">${item.company_name}</option>`,
        );
      });
    },
    error: function (xhr, status, error) {
      swalError("Error", status + error);
    },
  });
}

/* --- Get Shift Name --- */
function getShiftName(company_id) {
  $.ajax({
    url: "api/attendance_ot_monitor_chart_files/getShiftName.php",
    type: "POST",
    data: { company_id },
    dataType: "json",
    cache: false,
    success: function (response) {
      let dropdown = $("#shift_name");

      dropdown.empty(); // clear existing

      dropdown.append('<option value="">Select Shift Name</option>');

      // assuming response is array of objects
      $.each(response, function (index, item) {
        dropdown.append(
          `<option value="${item.id}">${item.shift_name}</option>`,
        );
      });
    },
    error: function (xhr, status, error) {
      swalError("Error", status + error);
    },
  });
}

/* --- Get Staff Name --- */
function getStaffName(company_id, shift_id) {
  $.ajax({
    url: "api/attendance_ot_monitor_chart_files/get_staff_name.php",
    type: "POST",
    data: { company_id, shift_id },
    dataType: "json",
    cache: false,
    success: function (response) {
      let dropdown = $("#staff_name");

      dropdown.empty();

      // Default option
      dropdown.append('<option value="">Select Staff Name</option>');

      // All option
      dropdown.append('<option value="all">All</option>');

      $.each(response, function (index, item) {
        dropdown.append(`
                    <option value="${item.id}">
                        ${item.staff_name}
                    </option>
                `);
      });
    },
    error: function (error) {
      console.error(error);

      swalError("Error", "Unable to Fetch Staff Name");
    },
  });
}

/* --- Load Chart --- */
function loadChart(company_id, shift_id, staff_id, date) {
  $.post(
    "api/attendance_ot_monitor_chart_files/get_staff_info.php",
    { company_id, shift_id, staff_id, date },
    function (response) {
      drawChart(response);
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
