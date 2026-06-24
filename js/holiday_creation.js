$(document).ready(function () {
  getCompanyName();

  /* --- Holiday Creation On Change & Click Events --- */
  $("#from_date, #to_date").on("change", function () {
    let from_date = $("#from_date").val();
    let to_date = $("#to_date").val();

    if (from_date != "" && to_date != "") {
      let from = new Date(from_date);
      let to = new Date(to_date);

      // Calculate difference in milliseconds
      let diffTime = to - from;

      // Convert to days
      let diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;

      // Prevent negative values
      if (diffDays > 0) {
        $("#no_of_days").val(diffDays);
      } else {
        $("#no_of_days").val("");
      }
    }
  });

  $("#company_name").on("change", function () {
    getHolidayTable();
    clearFields();
    $("#holiday_setup").hide();
  });

  $("#search_holiday").on("click", function () {
    let company_name = $("#company_name").val();
    if (company_name === "") {
      swalError("Warning", "Please select a company name.");
      return;
    }
    $("#holiday_setup").show();
    $("#holiday_setup input").css("border", "1px solid #cecece");
    $("#holiday_setup select").css("border", "1px solid #cecece");
  });

  /* --- Submit Holiday Creation --- */
  $("#submit_holiday_creation").click(function (event) {
    event.preventDefault();
    // Validation
    let company_id = $("#company_name").val();
    let holiday_id = $("#holiday_id").val();
    let from_date = $("#from_date").val();
    let to_date = $("#to_date").val();
    let no_of_days = $("#no_of_days").val();
    let holiday_name = $("#holiday_name").val();

    var data = ["from_date", "to_date", "no_of_days", "holiday_name"];

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
        "Do you want to submit this Holiday Creation?",
        function () {
          $.post(
            "api/holiday_creation_files/submit_holiday_creation.php",
            {
              company_id,
              holiday_id,
              from_date,
              to_date,
              no_of_days,
              holiday_name,
            },
            function (response) {
              if (response === "2") {
                swalSuccess("Success", "Holiday Added Successfully!");
              } else if (response === "1") {
                swalSuccess("Success", "Holiday Updated Successfully!");
              } else {
                swalError("Error", "Error Occurred!");
              }

              getHolidayTable();
              clearFields();
              $("#holiday_id").val("");
            },
          );
        },
      );
    }
  });

  /* --- Edit Holiday Creation --- */
  $(document).on("click", ".holidayActionBtn", function () {
    var id = $(this).attr("value"); // Get value attribute
    $.post(
      "api/holiday_creation_files/holiday_creation_data.php",
      { id: id },
      function (response) {
        $("#holiday_id").val(id);
        $("#from_date").val(response[0].from_date);
        $("#to_date").val(response[0].to_date);
        $("#no_of_days").val(response[0].no_of_days);
        $("#holiday_name").val(response[0].holiday_name);
      },
      "json",
    );
  });

  /* --- Delete Holiday Creation --- */
  $(document).on("click", ".holidayDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the Holiday Details?",
      getHolidayDelete,
      id,
    );
    return;
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

/* --- Get Holiday Creation Table --- */
function getHolidayTable() {
  let company_id = $("#company_name").val();

  $.post(
    "api/holiday_creation_files/holiday_creation_list.php",
    { company_id },
    function (response) {
      var columnMapping = [
        "sno",
        "from_date",
        "to_date",
        "no_of_days",
        "holiday_name",
        "action",
      ];
      appendDataToTable("#holiday_creation_table", response, columnMapping);
      setdtable("#holiday_creation_table", "Holiday Creation List");
    },
    "json",
  );
}

/* --- Delete Holiday Creation Table --- */
function getHolidayDelete(id) {
  $.post(
    "api/holiday_creation_files/delete_holiday_creation.php",
    { id },
    function (response) {
      if (response == "1") {
        swalSuccess("Success", "Holiday Deleted Successfully!");
        getHolidayTable();
        clearFields();
      } else {
        swalError("Warning", "Error occur While Delete Holiday Info.");
      }
    },
    "json",
  );
}

/* --- Clear Holiday Creation Fields --- */
function clearFields() {
  $("#from_date").val("");
  $("#to_date").val("");
  $("#no_of_days").val("");
  $("#holiday_name").val("");
  $("#holiday_creation").val("");
}
