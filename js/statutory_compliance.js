$(document).ready(function () {
  /* --- Add Statutory Compliance & Back Button Click --- */
  $(document).on("click", "#add_statutory_compliance, #back_btn", function () {
    swapTableAndCreation();
    $(".pf_apply").find("input").prop("readonly", false);
    $(".pf_apply").find("select").prop("disabled", false);
    $(".esi_apply").find("input").prop("readonly", false);
    $(".professional_tax_apply").find("select").prop("disabled", false);
    $("#pf_wage_div").hide();
    $("#percentage_div").hide();
    $("#slab_div").hide();
  });

  /* --- Statutory Compliance Creation On Change Events --- */
  $("#calculation_type").on("change", function () {
    var calculationType = $(this).val();
    if (calculationType === "1") {
      $("#percentage_div").show();
      $("#slab_div").hide();
    } else if (calculationType === "2") {
      $("#slab_div").show();
      $("#percentage_div").hide();
    } else {
      $("#percentage_div").hide();
      $("#slab_div").hide();
    }
  });

  $("#apply_wage_limit").on("change", function () {
    var apply_wage_limit = $(this).val();
    if (apply_wage_limit === "1") {
      $("#pf_wage_div").show();
    } else {
      $("#pf_wage_div").hide();
    }
  });

  $("#pf_applicable").on("change", function () {
    var pf_applicable = $(this).val();

    if (pf_applicable === "2") {
      $(".pf_apply").find("input").val("");
      $(".pf_apply").find("input").prop("readonly", true);
      $(".pf_apply").find("select").val("").prop("disabled", true);
      $("#pf_wage_div").hide();
    } else {
      $(".pf_apply").find("input").prop("readonly", false);
      $(".pf_apply").find("select").prop("disabled", false);
    }
  });

  $("#esi_applicable").on("change", function () {
    var esi_applicable = $(this).val();

    if (esi_applicable === "2") {
      // Empty all input fields
      $(".esi_apply").find("input").val("");

      // Make input fields readonly
      $(".esi_apply").find("input").prop("readonly", true);
    } else {
      // Enable input fields
      $(".esi_apply").find("input").prop("readonly", false);
    }
  });

  $("#professional_tax_applicable").on("change", function () {
    var professional_tax_applicable = $(this).val();

    if (professional_tax_applicable === "2") {
      $(".professional_tax_apply")
        .find("select")
        .val("")
        .prop("disabled", true);
      $("#percentage_div").hide().find("input").val("");
      $("#slab_div").hide().find("input").val("");
    } else {
      $(".professional_tax_apply").find("select").prop("disabled", false);
    }
  });

  /* --- Submit Statutory Compliance Creation --- */
  $("#submit_statutory_compliance").click(function (event) {
    event.preventDefault();
    // Validation
    let company_id = $("#company_name").val();
    let state = $("#state").val();
    let pf_applicable = $("#pf_applicable").val();
    let pf_number = $("#pf_number").val();
    let employee_contribution = $("#employee_contribution").val();
    let employer_contribution = $("#employer_contribution").val();
    let admin_charge = $("#admin_charge").val();
    let pension = $("#pension").val();
    let apply_wage_limit = $("#apply_wage_limit").val();
    let pf_wage_limit = $("#pf_wage_limit").val();
    let esi_applicable = $("#esi_applicable").val();
    let employee_share = $("#employee_share").val();
    let employer_share = $("#employer_share").val();
    let professional_tax_applicable = $("#professional_tax_applicable").val();
    let calculation_type = $("#calculation_type").val();
    let percentage = $("#percentage").val();
    let slab = $("#slab").val();
    let statutory_compliance_id = $("#statutory_compliance_id").val();
    console.log("ADSD", company_id);
    var data = ["company_name", "state", "pf_applicable", "esi_applicable"];

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
        "Do you want to submit this Statutory Compliance ?",
        function () {
          $.post(
            "api/statutory_compliance_files/submit_statutory_compliance_info.php",
            {
              company_id,
              state,
              pf_applicable,
              pf_number,
              employee_contribution,
              employer_contribution,
              admin_charge,
              pension,
              apply_wage_limit,
              pf_wage_limit,
              esi_applicable,
              employee_share,
              employer_share,
              professional_tax_applicable,
              calculation_type,
              percentage,
              slab,
              statutory_compliance_id,
            },
            function (result) {
              if (result === "2") {
                swalSuccess(
                  "Success",
                  "Statutory Compliance Added Successfully!",
                );
              } else if (result === "1") {
                swalSuccess(
                  "Success",
                  "Statutory Compliance Updated Successfully!",
                );
              } else {
                swalError("Error", "Error Occurred!");
              }

              $("#statutory_compliance_id").val("");
              $("#statutory_compliance_creation").trigger("reset");

              getStatutoryComplianceTable();
              swapTableAndCreation();
            },
          );
        },
      );
    }
  });

  /* --- Edit Statutory Compliance Creation --- */
  $(document).on("click", ".statutoryComplianceActionBtn", async function () {
    var id = $(this).attr("value"); // Get value attribute

    try {
      const response = await $.ajax({
        url: "api/statutory_compliance_files/statutory_compliance_data.php",
        type: "POST",
        data: { id },
        dataType: "json",
      });

      await swapTableAndCreation();
      $("#statutory_compliance_id").val(id);
      await getCompanyName(response[0].company_id);

      $("#company_name").val(response[0].company_id);
      $("#state").val(response[0].state);
      $("#pf_applicable").val(response[0].pf_applicable);
      $("#pf_number").val(response[0].pf_number);
      $("#employee_contribution").val(response[0].employee_contribution);
      $("#employer_contribution").val(response[0].employer_contribution);
      $("#admin_charge").val(response[0].admin_charge);
      $("#pension").val(response[0].pension);
      $("#apply_wage_limit").val(response[0].apply_wage_limit);
      $("#pf_wage_limit").val(response[0].pf_wage_limit);
      $("#esi_applicable").val(response[0].esi_applicable);
      $("#employee_share").val(response[0].employee_share);
      $("#employer_share").val(response[0].employer_share);
      $("#professional_tax_applicable").val(
        response[0].professional_tax_applicable,
      );
      $("#calculation_type").val(response[0].calculation_type);

      if (response[0].calculation_type == 1) {
        $("#percentage_div").show();
        $("#slab_div").hide();
      } else if (response[0].calculation_type == 2) {
        $("#slab_div").show();
        $("#percentage_div").hide();
      } else {
        $("#percentage_div").hide();
        $("#slab_div").hide();
      }

      if (response[0].apply_wage_limit == 1) {
        $("#pf_wage_div").show();
      } else {
        $("#pf_wage_div").hide();
      }

      $("#percentage").val(response[0].percentage);
      $("#slab").val(response[0].slab);

      if (response[0].pf_applicable == 2) {
        $(".pf_apply").find("input").prop("readonly", true);
        $(".pf_apply").find("select").val("").prop("disabled", true);
        $(".pf_apply").find("input").val("");
      } else {
        $(".pf_apply").find("input").prop("readonly", false);
        $(".pf_apply").find("select").prop("disabled", false);
      }

      if (response[0].esi_applicable == 2) {
        $(".esi_apply").find("input").prop("readonly", true);
        $(".esi_apply").find("input").val("");
      } else {
        $(".esi_apply").find("input").prop("readonly", false);
      }

      if (response[0].professional_tax_applicable == 2) {
        $(".professional_tax_apply")
          .find("select")
          .val("")
          .prop("disabled", true);
      } else {
        $(".professional_tax_apply").find("select").prop("disabled", false);
      }
    } catch (error) {
      console.error("Failed to fetch branch data:", error);
    }
  });

  /* --- Statutory Compliance Creation Reset --- */
  $('button[type="reset"], #back_btn').click(function () {
    event.preventDefault();
    $("input").val("");

    $("select").each(function () {
      $(this).val($(this).find("option:first").val());
    });
    $("input").css("border", "1px solid #cecece");
    $("select").css("border", "1px solid #cecece");
  });
});

/* --- On Load --- */
$(function () {
  getStatutoryComplianceTable();
});

/* --- Swap Table and hide/show --- */
async function swapTableAndCreation() {
  if ($(".statutory_compliance_table_content").is(":visible")) {
    $(".statutory_compliance_table_content").hide();
    $("#add_statutory_compliance").hide();

    $("#statutory_compliance_creation_content").show();
    $("#back_btn").show();

    await getCompanyName();
    await getStateList();
  } else {
    $(".statutory_compliance_table_content").show();
    $("#add_statutory_compliance").show();

    $("#statutory_compliance_creation_content").hide();
    $("#back_btn").hide();
  }
}

/* --- Get Company Name --- */
async function getCompanyName(editCompanyId = "") {
  return new Promise((resolve, reject) => {
    // Get all companies
    $.post(
      "api/branch_creation/getCompanyName.php",
      {},
      function (companyResponse) {
        // Get already used company ids
        $.post(
          "api/statutory_compliance_files/getStatutoryCompanyIds.php",
          {},
          function (usedCompanies) {
            let usedIds = usedCompanies.map(String);

            let dropdown = $("#company_name");

            dropdown.empty();
            dropdown.append('<option value="">Select Company Name</option>');

            $.each(companyResponse, function (index, item) {
              let companyId = item.id.toString();

              let isDisabled =
                usedIds.includes(companyId) &&
                companyId !== editCompanyId.toString();

              dropdown.append(`
                                <option value="${companyId}"
                                    ${isDisabled ? "disabled" : ""}>
                                    ${item.company_name}
                                    ${isDisabled ? "" : ""}
                                </option>
                            `);
            });

            if (editCompanyId !== "") {
              dropdown.val(editCompanyId);
            }

            resolve();
          },
          "json",
        ).fail(function (xhr, status, error) {
          reject(error);
        });
      },
      "json",
    ).fail(function (xhr, status, error) {
      reject(error);
    });
  });
}

/* --- Get State List --- */
async function getStateList() {
  return new Promise((resolve, reject) => {
    $.post(
      "api/common_files/get_state_list.php",
      function (response) {
        let appendStateOption = "<option value=''>Select State</option>";
        $.each(response, function (index, val) {
          appendStateOption +=
            "<option value='" + val.id + "'>" + val.state_name + "</option>";
        });
        $("#state").empty().append(appendStateOption);
        resolve(response);
      },
      "json",
    ).fail(function (xhr, status, error) {
      reject(error);
    });
  });
}

/* --- Get Statutory Compliance Outer List Table --- */
function getStatutoryComplianceTable() {
  serverSideTable(
    "#statutory_compliance_table",
    "",
    "api/statutory_compliance_files/statutory_compliance_list.php",
    "Statutory Compliance List",
  );
}
