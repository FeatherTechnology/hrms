$(document).ready(function () {
  /* --- Add Company Button & Back Button Click --- */
  $(document).on("click", "#add_branch, #back_btn", function () {
    swapTableAndCreation();
  });

  /* --- Branch Creation On Change Events --- */
  $("#company_name").on("change", function () {
    getBranchCode();
  });

  $("#state").change(function () {
    getDistrictList($(this).val());
  });

  $("#mobile_number, #whatsapp").change(function () {
    checkMobileNo($(this).val(), $(this).attr("id"));
  });

  $("#landline").change(function () {
    checkLandlineFormat($(this).val(), $(this).attr("id"));
  });

  $("#email_id").on("change", function () {
    validateEmail($(this).val(), $(this).attr("id"));
  });

  /* --- Submit Branch Creation --- */
  $("#submit_branch_creation").click(function () {
    event.preventDefault();
    //Validation
    let company_name = $("#company_name").val();
    let branch_code = $("#branch_code").val();
    let branch_name = $("#branch_name").val();
    let address = $("#address").val();
    let state = $("#state").val();
    let district = $("#district").val();
    let place = $("#place").val();
    let pincode = $("#pincode").val();
    let email_id = $("#email_id").val();
    let mobile_number = $("#mobile_number").val();
    let whatsapp = $("#whatsapp").val();
    let landline = $("#landline").val();
    let landline_code = $("#landline_code").val();
    let location = $("#location").val();
    let branchid = $("#branchid").val();

    var data = [
      "company_name",
      "branch_code",
      "branch_name",
      "place",
      "state",
      "district",
      "pincode",
      "location",
    ];

    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });
    if (isValid) {
      $.post(
        "api/branch_creation/submit_branch_creation.php",
        {
          company_name,
          branch_code,
          branch_name,
          address,
          state,
          district,
          place,
          pincode,
          email_id,
          mobile_number,
          whatsapp,
          landline,
          landline_code,
          branchid,
          location,
        },
        function (response) {
          if (response == "2") {
            swalSuccess("Success", "Branch Added Successfully!");
          } else if (response == "1") {
            swalSuccess("Success", "Branch Updated Successfully!");
          } else {
            swalError("Error", "Error Occurs!");
          }

          $("#branchid").val("");
          $("#branch_creation").trigger("reset");
          getBranchTable();
          swapTableAndCreation(); //to change to div to table content.
        },
      );
    }
  });

  /* --- Edit Branch Creation --- */
  $(document).on("click", ".branchActionBtn", async function () {
    var id = $(this).attr("value"); // Get value attribute

    try {
      const response = await $.ajax({
        url: "api/branch_creation/get_branch_creation_data.php",
        type: "POST",
        data: { id },
        dataType: "json",
      });

      swapTableAndCreation();
      await getCompanyName();
      $("#branchid").val(id);
      $("#company_name").val(response[0].company_id);
      $("#branch_name").val(response[0].branch_name);
      $("#address").val(response[0].address);

      await getDistrictList(response[0].state);

      $("#state").val(response[0].state);
      $("#district").val(response[0].district);
      $("#place").val(response[0].place);
      $("#pincode").val(response[0].pincode);
      $("#location").val(response[0].location);
      $("#email_id").val(response[0].email_id);
      $("#mobile_number").val(response[0].mobile_number);
      $("#whatsapp").val(response[0].whatsapp);
      $("#landline_code").val(response[0].landline_code);
      $("#landline").val(response[0].landline);
      $("#branch_code").val(response[0].branch_code);
    } catch (error) {
      console.error("Failed to fetch branch data:", error);
    }
  });

  /* --- Delete Branch Creation --- */
  $(document).on("click", ".branchDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the Branch Name?",
      getBranchDelete,
      id,
    );
    return;
  });

  /* --- Branch Creation Reset --- */
  $('button[type="reset"], #back_btn').click(function () {
    event.preventDefault();
    $("input").each(function () {
      var id = $(this).attr("id");
      if (id !== "company_name" && id !== "branch_code") {
        $(this).val("");
      }
    });
    $("textarea").val("");
    $("#pageHeaderName").text(` - Branch Creation`);
    $("select").each(function () {
      $(this).val($(this).find("option:first").val());
    });
    $("input").css("border", "1px solid #cecece");
    $("select").css("border", "1px solid #cecece");
  });
}); //Document END///

/* --- On Load --- */
$(function () {
  getBranchTable();
});

/* --- Swap Table and hide/show --- */
function swapTableAndCreation() {
  if ($(".branch_table_content").is(":visible")) {
    $(".branch_table_content").hide();
    $(".addbranchBtn").hide();
    $("#branch_creation_content").show();
    $(".backBtn").show();

    getCompanyName();
    getStateList();
  } else {
    $(".branch_table_content").show();
    getBranchLimit(); // Check limit and show/hide button properly
    $("#branch_creation_content").hide();
    $(".backBtn").hide();
  }
}

/* --- Branch Creation Outer List Table --- */
async function getBranchTable() {
  await new Promise((resolve) => {
    serverSideTable(
      "#branch_create",
      "",
      "api/branch_creation/branch_creation_list.php",
      "Branch Creation List",
    );

    // Wait until DataTable draw completes
    $("#branch_create").one("draw.dt", function () {
      resolve();
    });
  });

  getBranchLimit();
}

/* --- Get Branch Limit --- */
function getBranchLimit() {
  $.ajax({
    url: "api/common_files/get_limit.php",
    type: "GET",
    dataType: "json",
    success: function (response) {
      let branchLimit = parseInt(response.branch_limit);

      // Set Branch Limit in Header
      $("#pageHeaderName").html(
        ` - Branch Creation 
        <span style="padding-left:1250px;">
          Branch Limit: ${branchLimit}
        </span>`,
      );

      // Check DataTable exists
      if ($.fn.DataTable.isDataTable("#branch_create")) {
        let table = $("#branch_create").DataTable();

        // Current row count
        let rowCount = table.rows().count();

        // Hide if limit reached
        if (rowCount >= branchLimit) {
          $(".addbranchBtn").hide();
        } else {
          $(".addbranchBtn").show();
        }
      }
    },
  });
}

/* --- Get Company Name --- */
async function getCompanyName() {
  return new Promise((resolve, reject) => {
    $.post(
      "api/branch_creation/getCompanyName.php",
      {},

      function (response) {
        let dropdown = $("#company_name");
        dropdown.empty();
        dropdown.append('<option value="">Select Company Name</option>');
        $.each(response, function (index, item) {
          dropdown.append(
            `<option value="${item.id}">${item.company_name}
                        </option>`,
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

/* --- Get Branch Code --- */
function getBranchCode() {
  var company_name = $("#company_name").val();

  $.ajax({
    url: "api/branch_creation/getBranchCode.php",
    type: "POST",
    data: { company_name: company_name },
    dataType: "json",
    cache: false,

    success: function (response) {
      if (response.branch_code) {
        $("#branch_code").val(response.branch_code);
      } else {
        console.error(response);
      }
    },

    error: function (xhr, status, error) {
      console.error(status, error);
    },
  });
}

/* --- Get State List --- */
function getStateList() {
  $.post(
    "api/common_files/get_state_list.php",
    function (response) {
      let appendStateOption = "";
      appendStateOption += "<option value=''>Select State</option>";
      $.each(response, function (index, val) {
        appendStateOption +=
          "<option value='" + val.id + "'>" + val.state_name + "</option>";
      });
      $("#state").empty().append(appendStateOption);
    },
    "json",
  );
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

/* --- Get Branch Delete --- */
function getBranchDelete(id) {
  $.post(
    "api/branch_creation/delete_branch_creation.php",
    { id },
    function (response) {
      if (response == "1") {
        swalSuccess("Success", "Branch Deleted Successfully!");
        getBranchTable();
      } else if (response == "2") {
        swalError("Access Denied", "Used in Staff Creation");
      } else {
        swalError("Error", "Failed to Delete Branch");
      }
    },
    "json",
  );
}
