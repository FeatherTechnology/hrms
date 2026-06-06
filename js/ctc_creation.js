$(document).ready(function () {
  getCompanyName();

  /* --- CTC Creation On Change & Click Events --- */
  $("#company_name").on("change", function () {
    // Get selected option text
    let companyName = $("#company_name option:selected").text();

    // If default option selected
    if (companyName == "Select Company Name") {
      companyName = "";
    }

    // Set value to input field
    $("#company_names").val(companyName);
    getctcTable();
    clearFields();
    $("#ctc_settings").hide();
  });

  $("#search_ctc").on("click", function () {
    let company_name = $("#company_name").val();
    if (company_name === "") {
      swalError("Warning", "Please select a company name.");
      return;
    }
    $("#ctc_settings").show();
  });

  /* --- Submit CTC Creation --- */
  $("#submit_ctc_settings_info").click(function (event) {
    event.preventDefault();
    // Validation
    let company_id = $("#company_name").val();
    let ctc_id = $("#ctc_id").val();
    let salary_component = $("#salary_component").val();
    let component_classification = $("#component_classification").val();
    let component_category = $("#component_category").val();
    let pay_frequency = $("#pay_frequency").val();

    var data = [
      "salary_component",
      "component_classification",
      "component_category",
      "pay_frequency",
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
        "Do you want to submit this CTC info?",
        function () {
          $.post(
            "api/ctc_creation_files/submit_ctc_info.php",
            {
              company_id,
              salary_component,
              component_classification,
              component_category,
              pay_frequency,
              ctc_id,
            },
            function (response) {
              if (response === "3") {
                swalError("Warning", "CTC Info already exists!");
              } else if (response === "2") {
                swalSuccess("Success", "CTC Info Added Successfully!");
              } else if (response === "1") {
                swalSuccess("Success", "CTC Info Updated Successfully!");
              } else {
                swalError("Error", "Error Occurred!");
              }

              // IMPORTANT
              // Destroy old datatable before reload
              $("#ctc_creation_table").DataTable().destroy();
              getctcTable();
              clearFields();
            },
          );
        },
      );
    }
  });

  /* --- Edit CTC Creation --- */
  $(document).on("click", ".ctcActionBtn", function () {
    var id = $(this).attr("value"); // Get value attribute
    $.post(
      "api/ctc_creation_files/ctc_creation_data.php",
      { id: id },
      function (response) {
        $("#ctc_id").val(id);
        $("#salary_component").val(response[0].salary_component);
        $("#component_classification").val(
          response[0].component_classification,
        );
        $("#component_category").val(response[0].component_category);
        $("#pay_frequency").val(response[0].pay_frequency);
      },
      "json",
    );
  });

  /* --- Delete CTC Creation --- */
  $(document).on("click", ".ctcDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the CTC Details?",
      getctcDelete,
      id,
    );
    return;
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

/* --- Get CTC Table --- */
function getctcTable() {
  let company_id = $("#company_name").val();

  $.post(
    "api/ctc_creation_files/ctc_creation_list.php",
    { company_id },
    function (response) {
      var columnMapping = [
        "sno",
        "salary_component",
        "component_classification",
        "component_category",
        "pay_frequency",
        "action",
      ];
      appendDataToTable("#ctc_creation_table", response, columnMapping);
      setdtable("#ctc_creation_table", "CTC Creation List");
    },
    "json",
  );
}

/* --- Get CTC Delete --- */
function getctcDelete(id) {
  $.post(
    "api/ctc_creation_files/delete_ctc_info.php",
    { id },
    function (response) {
      if (response == "1") {
        swalSuccess("Success", "CTC Info Deleted Successfully!");
        getctcTable();
        clearFields();
      } else if (response == "2") {
        swalError("Warning", "CTC Info is already used in Staff Creation!");
      } else {
        swalError("Warning", "Error occur While Delete CTC Info.");
      }
    },
    "json",
  );
}

/* --- Clear CTC Input Fields --- */
function clearFields() {
  $("#salary_component").val("");
  $("#component_classification").val("");
  $("#component_category").val("");
  $("#pay_frequency").val("");
  $("#ctc_id").val("");
  $("#ctc_settings_form input").css("border", "1px solid #cecece");
  $("#ctc_settings_form select").css("border", "1px solid #cecece");
}
