$(document).ready(function () {
  getCompanyName();

  /* --- Leave Master On Change & Click Events --- */
  $("#company_name").on("change", function () {
    // Reset first
    $("#week_off_table_body").empty();
    $("#max_permission").val("");
    $("#leave_master_settings").hide();

    getLeaveCriteriaTable();
    getShiftTable();

    let id = $(this).val();

    getLeaveMaster(id);
  });

  $("#search_ctc").on("click", function () {
    let company_name = $("#company_name").val();
    if (company_name === "") {
      swalError("Warning", "Please select a company name.");
      return;
    }
    $("#leave_master_settings").show();
  });

  $("#start_time, #end_time").on("change", function () {
    calculateShiftTime();
  });

  /* --- Submit Leave Creation --- */
  $("#submit_leave_criteria").click(function (event) {
    event.preventDefault();
    // Validation
    let company_name = $("#company_name").val();
    let leave_type = $("#leave_type").val();
    let no_of_days = $("#no_of_days").val();
    let leave_criteria_id = $("#leave_criteria_id").val();

    var data = ["leave_type", "no_of_days"];

    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });

    if (isValid) {
      swalConfirm(
        "Are you sure?",
        "Do you want to submit this Leave Criteria info?",
        function () {
          $.post(
            "api/leave_master_files/submit_leave_criteria_info.php",
            {
              company_name,
              leave_type,
              no_of_days,
              leave_criteria_id,
            },
            function (response) {
              if (response === "3") {
                swalError("Warning", "Leave Type already exists!");
              } else if (response === "2") {
                swalSuccess("Success", "Leave Criteria Added Successfully!");
              } else if (response === "1") {
                swalSuccess("Success", "Leave Criteria Updated Successfully!");
              } else {
                swalError("Error", "Error Occurred!");
              }

              // Refresh the leave info table
              getLeaveInfoTable();
            },
          );
        },
      );
    }
  });

  /* --- Edit Leave Creation --- */
  $(document).on("click", ".leaveInfoActionBtn", function () {
    var id = $(this).attr("value"); // Get value attribute
    $.post(
      "api/leave_master_files/leave_criteria_data.php",
      { id: id },
      function (response) {
        $("#leave_criteria_id").val(id);
        $("#leave_type").val(response[0].leave_type);
        $("#no_of_days").val(response[0].no_of_days);
      },
      "json",
    );
  });

  /* --- Delete Leave Creation --- */
  $(document).on("click", ".leaveInfoDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the Leave Details?",
      getLeaveInfoDelete,
      id,
    );
    return;
  });

  /* --- Submit Shift Creation --- */
  $("#submit_shift_info").click(function (event) {
    event.preventDefault();
    // Validation
    let company_name = $("#company_name").val();
    let shift_name = $("#shift_name").val();
    let start_time = $("#start_time").val();
    let end_time = $("#end_time").val();
    let shift_time = $("#shift_time").val();
    let grace_time = $("#grace_time").val();
    let shift_id = $("#shift_id").val();

    var data = [
      "shift_name",
      "start_time",
      "end_time",
      "shift_time",
      "grace_time",
    ];

    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });

    if (isValid) {
      swalConfirm(
        "Are you sure?",
        "Do you want to submit this shift info?",
        function () {
          $.post(
            "api/leave_master_files/submit_shift_info.php",
            {
              company_name,
              shift_name,
              start_time,
              end_time,
              shift_time,
              grace_time,
              shift_id,
            },
            function (response) {
              if (response === "2") {
                swalSuccess("Success", "Shift Info Added Successfully!");
              } else if (response === "1") {
                swalSuccess("Success", "Shift Info Updated Successfully!");
              } else {
                swalError("Error", "Error Occurred!");
              }

              // Refresh the shift info table
              getShiftInfoTable();
            },
          );
        },
      );
    }
  });

  /* --- Edit Shift Creation --- */
  $(document).on("click", ".shiftInfoActionBtn", function () {
    var id = $(this).attr("value"); // Get value attribute
    $.post(
      "api/leave_master_files/shift_info_data.php",
      { id: id },
      function (response) {
        $("#shift_id").val(id);
        $("#shift_name").val(response[0].shift_name);
        $("#start_time").val(response[0].start_time);
        $("#end_time").val(response[0].end_time);
        $("#shift_time").val(response[0].shift_time);
        $("#grace_time").val(response[0].grace_time);
      },
      "json",
    );
  });

  /* --- Delete Shift Creation --- */
  $(document).on("click", ".shiftInfoDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the Shift Details?",
      getShiftInfoDelete,
      id,
    );
    return;
  });

  /* --- Submit Leave Master --- */
  $("#submit_leave_master").click(function (event) {
    event.preventDefault();

    let company_name = $("#company_name").val();
    let max_permission = $("#max_permission").val();
    let shiftCreationRowCount = $("#shift_info_table")
      .DataTable()
      .rows()
      .count();

    let week_off = {};

    $("select[name^='week_off']").each(function () {
      let name = $(this).attr("name");

      // Extract sunday from week_off[sunday]
      let day = name.match(/\[(.*?)\]/)[1];

      let value = $(this).val();

      week_off[day] = value;
    });

    var data = ["company_name"];

    var isValid = true;

    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);

      if (!fieldIsValid) {
        isValid = false;
      }
    });

    if (shiftCreationRowCount === 0) {
      swalError("Warning", "Please fill out Shift Timimgs Info!");
      isValid = false;
    }

    if (isValid) {
      swalConfirm(
        "Are you sure?",
        "Do you want to submit this Leave Master?",
        function () {
          $.post(
            "api/leave_master_files/submit_leave_master.php",
            {
              company_name,
              max_permission,
              week_off,
            },

            function (response) {
              if (response === "2") {
                swalSuccess("Success", "Leave Master Added Successfully!");
              } else if (response === "1") {
                swalSuccess("Success", "Leave Master Updated Successfully!");
              } else {
                swalError("Error", "Error Occurred!");
              }
            },
          );
        },
      );
    }
  });
});

/* --- Get Company Name --- */
function getCompanyName() {
  $.ajax({
    url: "api/attendance_files/get_company_list.php",
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

/* --- Get Leave Creation Outer List Table --- */
function getLeaveCriteriaTable() {
  return new Promise((resolve, reject) => {
    let company_id = $("#company_name").val();
    $.post(
      "api/leave_master_files/leave_criteria_list.php",
      { company_id },
      function (response) {
        var columnMapping = ["sno", "leave_type", "no_of_days"];

        appendDataToTable("#leave_info_table", response, columnMapping);
        setdtable("#leave_info_table", "Leave Criteria Info List");
        resolve();
      },
      "json",
    ).fail(reject);
  });
}

/* --- Get Leave Creation Inner List Table --- */
function getLeaveInfoTable() {
  let company_id = $("#company_name").val();
  $.post(
    "api/leave_master_files/leave_criteria_list.php",
    { company_id: company_id },
    function (response) {
      var columnMapping = ["sno", "leave_type", "no_of_days", "action"];

      appendDataToTable("#leave_creation_table", response, columnMapping);
      setdtable("#leave_creation_table", "Family Creation List");
      $("#leave_info_form input").val("");
      $("#leave_info_form input").css("border", "1px solid #cecece");
      $("#leave_info_form select").css("border", "1px solid #cecece");
    },
    "json",
  );
}

/* --- Delete Leave Creation --- */
function getLeaveInfoDelete(id) {
  $.post(
    "api/leave_master_files/delete_leave_criteria.php",
    { id },
    function (response) {
      if (response == "1") {
        swalSuccess("Success", "Leave Type Deleted Successfully!");
        getLeaveInfoTable();
      } else if (response == "2") {
        swalError("Warning", "Leave Type is already used in Regularization!");
      } else {
        swalError("Warning", "Error occur While Delete Leave Type.");
      }
    },
    "json",
  );
}

/* --- Calculate Shift Time --- */
function calculateShiftTime() {
  let startTime = $("#start_time").val();
  let endTime = $("#end_time").val();

  if (startTime && endTime) {
    // Validation: same start and end time
    if (startTime === endTime) {
      swalError("Warning", "Start Time and End Time cannot be the same.");
      $("#end_time").val("");
      $("#shift_time").val("");
      return;
    }

    let start = new Date("2000-01-01 " + startTime);
    let end = new Date("2000-01-01 " + endTime);

    // Handle night shift
    if (end < start) {
      end.setDate(end.getDate() + 1);
    }

    let diffMs = end - start;

    let hours = Math.floor(diffMs / (1000 * 60 * 60));
    let minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

    let result = `${hours} Hrs`;

    if (minutes > 0) {
      result += ` ${minutes} Mins`;
    }

    $("#shift_time").val(result);
  }
}

/* --- Get Shift Creation Outer List Table --- */
function getShiftTable() {
  return new Promise((resolve, reject) => {
    let company_id = $("#company_name").val();
    $.post(
      "api/leave_master_files/shift_info_list.php",
      { company_id },
      function (response) {
        var columnMapping = [
          "sno",
          "shift_name",
          "start_time",
          "end_time",
          "shift_time",
          "grace_time",
        ];

        appendDataToTable("#shift_info_table", response, columnMapping);
        setdtable("#shift_info_table", "Shift Info List");
        resolve();
      },
      "json",
    ).fail(reject);
  });
}

/* --- Get Shift Creation Inner List Table --- */
function getShiftInfoTable() {
  let company_id = $("#company_name").val();
  $.post(
    "api/leave_master_files/shift_info_list.php",
    { company_id: company_id },
    function (response) {
      var columnMapping = [
        "sno",
        "shift_name",
        "start_time",
        "end_time",
        "shift_time",
        "grace_time",
        "action",
      ];

      appendDataToTable("#shift_creation_table", response, columnMapping);
      setdtable("#shift_creation_table", "Shift Creation List");
      $("#shift_info_form input").val("");
      $("#shift_info_form input").css("border", "1px solid #cecece");
      $("#shift_info_form select").css("border", "1px solid #cecece");
    },
    "json",
  );
}

/* --- Delete Shift Info --- */
function getShiftInfoDelete(id) {
  $.post(
    "api/leave_master_files/delete_shift_info.php",
    { id },
    function (response) {
      if (response == "1") {
        swalSuccess("Success", "Shift Info Deleted Successfully!");
        getShiftInfoTable();
      } else if (response == "2") {
        swalError("Warning", "Shift is already used in Staff Creation!");
      } else {
        swalError("Warning", "Error occur While Delete Shift Info.");
      }
    },
    "json",
  );
}

/* --- Get Week Off Table --- */
const weekDays = [
  "Sunday",
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday",
];

const weekOptions = `
        <option value="">Select</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">All</option>
    `;

let tableRows = "";

weekDays.forEach((day, index) => {
  tableRows += `
            <tr>
                <td>${index + 1}</td>

                <td>${day}</td>

                <td>
                    <select 
                        class="form-control"
                        name="week_off[${day.toLowerCase()}]"
                    >
                        ${weekOptions}
                    </select>
                </td>
            </tr>
        `;
});

document.getElementById("week_off_table_body").innerHTML = tableRows;

/* --- Get Company Wise Week Off --- */
function getLeaveMaster(id) {
  $.post(
    "api/leave_master_files/leave_master_data.php",
    { id },
    function (response) {
      $("#max_permission").val(response[0]?.max_permission || "");

      $("#week_off_table_body").empty();

      const weekDays = [
        "Sunday",
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
      ];

      // Convert response into object
      let weekOffData = {};

      $.each(response, function (index, item) {
        if (item.week_day) {
          weekOffData[item.week_day.toLowerCase()] = item.week_off;
        }
      });

      // Build all 7 rows always
      $.each(weekDays, function (index, day) {
        let selectedValue = weekOffData[day.toLowerCase()] ?? "";

        let row = `
          <tr>
            <td>${index + 1}</td>

            <td>${day}</td>

            <td>
              <select class="form-control week_off_dropdown" name="week_off[${day.toLowerCase()}]">
                <option value="" ${selectedValue === "" ? "selected" : ""}> Select </option>
                <option value="1" ${selectedValue == 1 ? "selected" : ""}> 1 </option>
                <option value="2" ${selectedValue == 2 ? "selected" : ""}> 2 </option>
                <option value="3" ${selectedValue == 3 ? "selected" : ""}> 3 </option>
                <option value="4" ${selectedValue == 4 ? "selected" : ""}> 4 </option>
                <option value="5" ${selectedValue == 5 ? "selected" : ""}> All </option>
              </select>
            </td>
          </tr>
        `;

        $("#week_off_table_body").append(row);
      });
    },
    "json",
  );
}
