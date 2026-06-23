/* --- Department Multi Select --- */
const departmentInstance = new Choices("#department_name", {
  removeItemButton: true,
  placeholder: true,
  placeholderValue: "Select Department Name",
  itemSelectText: "",
  allowHTML: false,
  searchEnabled: false,
});

/* --- Designation Multi Select --- */
const designationInstance = new Choices("#designation_name", {
  removeItemButton: true,
  placeholder: true,
  placeholderValue: "Select Designation Name",
  itemSelectText: "",
  allowHTML: false,
  searchEnabled: false,
});

$(document).ready(function () {
  /* --- Add Company Button Click --- */
  $(document).on("click", ".addcompanyBtn", async function () {
    $("#reset_btn").show();
    await swapTableAndCreation();
    getDepartmentNameDropdown();
    getDesignationNameDropdown();
  });

  /* --- Company Creation On Change & Click Events --- */
  $("#state").change(function () {
    getDistrictList($(this).val());
  });

  $("#mobile, #whatsapp").change(function () {
    checkMobileNo($(this).val(), $(this).attr("id"));
  });

  $("#landline").change(function () {
    checkLandlineFormat($(this).val(), $(this).attr("id"));
  });

  $("#mailid").on("change", function () {
    validateEmail($(this).val(), $(this).attr("id"));
  });

  /* --- Submit Department --- */
  $("#submit_department").click(function (event) {
    event.preventDefault();
    // Validation
    let department_code = $("#modal_department_code").val(); // Remove spaces from department_code
    let department_name = $("#modal_department_name").val();
    let department_id = $("#department_id").val();

    var data = ["modal_department_code", "modal_department_name"];

    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });

    if (isValid) {
      $.post(
        "api/company_creation_files/submit_department_info.php",
        {
          department_code,
          department_name,
          department_id,
        },
        function (response) {
          if (response === "3") {
            swalError("Warning", "Department Name already exists!");
          } else if (response === "2") {
            swalSuccess("Success", "Department Added Successfully!");
          } else if (response === "1") {
            swalSuccess("Success", "Department Updated Successfully!");
          } else {
            swalError("Error", "Error Occurred!");
          }

          // Refresh the department table
          getDepartmentNameTable();
        },
      );
    }
  });

  /* --- Edit Department --- */
  $(document).on("click", ".departmentActionBtn", function () {
    var id = $(this).attr("value"); // Get value attribute
    $.post(
      "api/company_creation_files/department_creation_data.php",
      { id: id },
      function (response) {
        $("#department_id").val(id);
        $("#modal_department_code").val(response[0].department_code);
        $("#modal_department_name").val(response[0].department_name);
      },
      "json",
    );
  });

  /* --- Delete Department --- */
  $(document).on("click", ".departmentDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the Department Details?",
      getDepartmentDelete,
      id,
    );
    return;
  });

  /* --- Submit Designation --- */
  $("#submit_designation").click(function (event) {
    event.preventDefault();
    // Validation
    let designation = $("#modal_designation").val(); // Remove spaces from department_code
    let designation_level = $("#modal_designation_level").val();
    let designation_id = $("#designation_id").val();

    var data = ["modal_designation", "modal_designation_level"];

    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });

    if (isValid) {
      $.post(
        "api/company_creation_files/submit_designation_info.php",
        {
          designation,
          designation_level,
          designation_id,
        },
        function (response) {
          if (response === "2") {
            swalSuccess("Success", "Designation Added Successfully!");
          } else if (response === "1") {
            swalSuccess("Success", "Designation Updated Successfully!");
          } else {
            swalError("Error", "Error Occurred!");
          }

          // Refresh the designation table
          getDesignationNameTable();
        },
      );
    }
  });

  /* --- Edit Designation --- */
  $(document).on("click", ".designationActionBtn", function () {
    var id = $(this).attr("value"); // Get value attribute
    $.post(
      "api/company_creation_files/designation_creation_data.php",
      { id: id },
      function (response) {
        $("#designation_id").val(id);
        $("#modal_designation").val(response[0].designation);
        $("#modal_designation_level").val(response[0].designation_level);
      },
      "json",
    );
  });

  /* --- Delete Designation --- */
  $(document).on("click", ".designationDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the Designation Details?",
      getDesignationDelete,
      id,
    );
    return;
  });

  /* --- Submit Company Creation --- */
  $("#submit_company_creation").click(function () {
    event.preventDefault();
    //Validation
    let company_name = $("#company_name").val();
    let gst_number = $("#gst_number").val();
    let cin_number = $("#cin_number").val();
    let address = $("#address").val();
    let state = $("#state").val();
    let district = $("#district").val();
    let place = $("#place").val();
    let pincode = $("#pincode").val();
    let mobile = $("#mobile").val();
    let whatsapp = $("#whatsapp").val();
    let landline_code = $("#landline_code").val();
    let landline = $("#landline").val();
    let department_name = $("#department_name").val();
    let department_name2 = $("#department_name2").val();
    let designation_name = $("#designation_name").val();
    let designation_name2 = $("#designation_name2").val();
    let website = $("#website").val();
    let mailid = $("#mailid").val();
    let instagram = $("#instagram").val();
    let youtube_link = $("#youtube_link").val();
    let facebook = $("#facebook").val();
    let twitter = $("#twitter").val();
    let companyid = $("#companyid").val();

    var data = [
      "company_name",
      "address",
      "state",
      "place",
      "district",
      "pincode",
      "mobile",
      "mailid",
    ];

    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });

    let departmentValid = validateMultiSelectField(
      "department_name",
      departmentInstance,
    );

    let designationValid = validateMultiSelectField(
      "designation_name",
      designationInstance,
    );

    if (isValid && departmentValid && designationValid) {
      swalConfirm(
        "Are you sure?",
        "Do you want to submit this Company Creation?",
        function () {
          $.post(
            "api/company_creation_files/submit_company_creation.php",
            {
              company_name,
              gst_number,
              cin_number,
              address,
              state,
              district,
              place,
              pincode,
              mobile,
              whatsapp,
              landline_code,
              landline,
              department_name,
              department_name2,
              designation_name,
              designation_name2,
              website,
              mailid,
              instagram,
              youtube_link,
              facebook,
              twitter,
              companyid,
            },
            function (response) {
              if (response == "1") {
                swalSuccess("Success", "Company Added Successfully!");
              } else {
                swalSuccess("Success", "Company Updated Successfully!");
              }

              $("#companyid").val("");
              $("#department_name2").val("");
              $("#designation_name2").val("");
              $("#company_creation").trigger("reset");
              getCompanyTable();
              swapTableAndCreation(); //to change to div to table content.
            },
          );
        },
      );
    }
  });

  /* --- Edit Company Creation --- */
  $(document).on("click", ".companyActionBtn", async function () {
    $("#reset_btn").hide();
    var id = $(this).attr("value"); // Get value attribute

    try {
      const response = await $.ajax({
        url: "api/company_creation_files/get_company_creation_data.php",
        type: "POST",
        data: { id },
        dataType: "json",
      });

      await swapTableAndCreation();
      $("#companyid").val(id);
      $("#company_name").val(response[0].company_name);
      $("#gst_number").val(response[0].gst_num);
      $("#cin_number").val(response[0].cin_number);
      $("#address").val(response[0].address);

      await getDistrictList(response[0].state);

      $("#state").val(response[0].state);
      $("#district").val(response[0].district);
      $("#place").val(response[0].place);
      $("#pincode").val(response[0].pincode);
      $("#mobile").val(response[0].mobile);
      $("#whatsapp").val(response[0].whatsapp);
      $("#landline_code").val(response[0].landline_code);
      $("#landline").val(response[0].landline);
      $("#department_name2").val(response[0].department_ids);
      $("#designation_name2").val(response[0].designation_ids);

      await getDepartmentNameDropdown();
      await getDesignationNameDropdown();

      $("#website").val(response[0].website);
      $("#mailid").val(response[0].mailid);
      $("#instagram").val(response[0].instagram);
      $("#youtube_link").val(response[0].youtube_link);
      $("#facebook").val(response[0].facebook);
      $("#twitter").val(response[0].twitter);
    } catch (error) {
      console.error("Failed to fetch company data:", error);
    }
  });

  /* --- Company Creation Reset --- */
  $(".backBtn").click(function (event) {
    event.preventDefault();

    swapTableAndCreation();

    $("#pageHeaderName").text(" - Company Creation");
    $("input").val("");

    $("select").each(function () {
      $(this).val("");
    });

    // Reset Choices.js properly
    departmentInstance.removeActiveItems();
    designationInstance.removeActiveItems();

    $("input").css("border", "1px solid #cecece");
    $("select").css("border", "1px solid #cecece");
    $("#department_name")
      .closest(".choices")
      .find(".choices__inner")
      .css("border", "1px solid #cecece");
    $("#designation_name")
      .closest(".choices")
      .find(".choices__inner")
      .css("border", "1px solid #cecece");
  });
});

/* --- On Load --- */
$(function () {
  getCompanyTable();
});

/* --- Swap Table and hide/show --- */
async function swapTableAndCreation() {
  if ($(".company_table_content").is(":visible")) {
    $(".company_table_content").hide();
    $(".addcompanyBtn").hide();
    $("#company_creation_content").show();
    $(".backBtn").show();

    await getStateList();
  } else {
    await getCompanyLimit(); // Check limit and show/hide button properly

    $(".company_table_content").show();
    $("#company_creation_content").hide();
    $(".backBtn").hide();
  }
}

/* --- Company Creation Outer List Table --- */
function getCompanyTable() {
  $.post(
    "api/company_creation_files/company_creation_list.php",
    function (response) {
      var columnMapping = [
        "sno",
        "company_name",
        "place",
        "district_name",
        "mobile",
        "action",
      ];
      appendDataToTable("#company_creation_table", response, columnMapping);
      setdtable("#company_creation_table", "Company Creation List");
      getCompanyLimit();
    },
    "json",
  );
}

/* --- Get Company Limit --- */
async function getCompanyLimit() {
  try {
    const response = await $.ajax({
      url: "api/common_files/get_limit.php",
      type: "GET",
      dataType: "json",
    });

    let companyLimit = parseInt(response.company_limit);

    $("#pageHeaderName").html(`
            - Company Creation
            <span style="padding-left:1000px;">
                Company Limit: ${companyLimit}
            </span>
        `);

    if ($.fn.DataTable.isDataTable("#company_creation_table")) {
      let table = $("#company_creation_table").DataTable();
      let rowCount = table.rows().count();

      if (rowCount >= companyLimit) {
        $(".addcompanyBtn").hide();
      } else {
        $(".addcompanyBtn").show();
      }
    }

    return companyLimit;
  } catch (error) {
    console.error("Error loading company limit:", error);
    return 0;
  }
}

/* --- Get State List --- */
async function getStateList() {
  try {
    const response = await $.ajax({
      url: "api/common_files/get_state_list.php",
      type: "POST",
      dataType: "json",
    });

    let appendStateOption = "<option value=''>Select State</option>";

    $.each(response, function (index, val) {
      appendStateOption += `<option value="${val.id}">${val.state_name}</option>`;
    });

    $("#state").empty().append(appendStateOption);
  } catch (error) {
    console.error("Error loading states:", error);
  }
}

/* --- Get District List --- */
async function getDistrictList(state_id) {
  return new Promise((resolve, reject) => {
    $.post(
      "api/common_files/get_district_list.php",
      { state_id },
      function (response) {
        let appendDistrictOption = "";
        appendDistrictOption += "<option value=''>Select District</option>";
        $.each(response, function (index, val) {
          appendDistrictOption +=
            "<option value='" + val.id + "'>" + val.district_name + "</option>";
        });
        $("#district").empty().append(appendDistrictOption);
        resolve();
      },
      "json",
    );
  });
}

/* --- Get Auto Generated Department Id --- */
function getAutoGenDepartmentId(id) {
  $.post(
    "api/company_creation_files/get_autoGen_department_id.php",
    { id },
    function (response) {
      $("#modal_department_code").val(response);
    },
    "json",
  ).fail(function (error) {
    console.log(error);
  });
}

/* --- Get Department Table --- */
function getDepartmentNameTable() {
  $.post(
    "api/company_creation_files/department_creation_list.php",
    {},
    function (response) {
      var columnMapping = [
        "sno",
        "department_code",
        "department_name",
        "action",
      ];

      appendDataToTable("#department_creation_table", response, columnMapping);
      setdtable("#department_creation_table", "Department Creation List");
      $("#department_form input").not("#modal_department_code").val("");
      $("#department_form select").each(function () {
        $(this).val($(this).find("option:first").val());
      });
      $("#department_form input").css("border", "1px solid #cecece");
      $("#department_form select").css("border", "1px solid #cecece");
      getAutoGenDepartmentId(0);
    },
    "json",
  );
}

/* --- Get Department Name Dropdown --- */
async function getDepartmentNameDropdown() {
  const department_name2 = $("#department_name2").val();

  try {
    const response = await $.ajax({
      url: "api/company_creation_files/get_department_name_dropdown.php",
      type: "POST",
      data: { screen: "company_creation" },
      dataType: "json",
    });

    departmentInstance.clearChoices();
    departmentInstance.removeActiveItems();

    const selectedIds = department_name2 ? department_name2.split(",") : [];

    const items = response.map((val) => ({
      value: val.id,
      label: val.department_name,
      selected: selectedIds.includes(val.id.toString()),
      disabled: val.disabled && !selectedIds.includes(val.id.toString()),
    }));

    departmentInstance.setChoices(items, "value", "label", true);
  } catch (err) {
    console.error("Error loading department dropdown:", err);
  }
}

/* --- Get Department Delete --- */
function getDepartmentDelete(id) {
  $.post(
    "api/company_creation_files/delete_department_creation.php",
    { id },
    function (response) {
      if (response == "1") {
        swalSuccess("Success", "Department Info Delete Successfully!");
        getDepartmentNameTable();
      } else if (response == "2") {
        swalError("Warning", "Department already used in Team Creation!");
      } else {
        swalError("Warning", "Error occur While Delete Department Info.");
      }
    },
    "json",
  );
}

/* --- Get Designation Table --- */
function getDesignationNameTable() {
     let company_id = $("#companyid").val();
  $.post(
    "api/company_creation_files/designation_creation_list.php",
    {company_id},
    function (response) {
      var columnMapping = ["sno", "designation", "designation_level", "action"];

      appendDataToTable("#designation_creation_table", response, columnMapping);
      setdtable("#designation_creation_table", "Designation Creation List");
      $("#designation_form input").val("");
      $("#designation_form select").each(function () {
        $(this).val($(this).find("option:first").val());
      });
      $("#designation_form input").css("border", "1px solid #cecece");
      $("#designation_form select").css("border", "1px solid #cecece");
    },
    "json",
  );
}

/* --- Get Designation Name Dropdown --- */
async function getDesignationNameDropdown() {
  const designation_name2 = $("#designation_name2").val();
  const company_id = $("#companyid").val();

  try {
    const response = await $.ajax({
      url: "api/company_creation_files/get_designation_name_dropdown.php",
      data: { company_id },
      type: "POST",
      dataType: "json",
    });

    designationInstance.clearChoices();
    designationInstance.removeActiveItems();

    const selectedIds = designation_name2 ? designation_name2.split(",") : [];

    const items = response.map((val) => ({
      value: val.id,
      label: val.designation,
      selected: selectedIds.includes(val.id.toString()),
      disabled: val.disabled && !selectedIds.includes(val.id.toString()),
    }));

    designationInstance.setChoices(items, "value", "label", true);
  } catch (err) {
    console.error("Error loading designation dropdown:", err);
  }
}

/* --- Get Designation Delete --- */
function getDesignationDelete(id) {
  $.post(
    "api/company_creation_files/delete_designation_creation.php",
    { id },
    function (response) {
      if (response == "1") {
        swalSuccess("Success", "Designation Info Delete Successfully!");
        getDesignationNameTable();
      } else if (response == "2") {
        swalError("Warning", "Designation already used in Staff Creation!");
      } else {
        swalError("Warning", "Error occur While Delete Designation Info.");
      }
    },
    "json",
  );
}
