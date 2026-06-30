$(document).ready(function () {
  $(document).on("click", "#add_staff", function () {
    swapTableAndCreation();
  });

  $("#add_staff").click(async function () {
    getStateList();
    $(".staff_content").hide();
    resetStaffData();

    await getCompanyName("#company_name");
  });

  $("#back_btn").click(function () {
    let staff_id = $("#staff_auto_id").val();
    let staff_profile_id = $("#staff_profile_id").val();
    $.post(
      "api/staff_creation/staff_sts_check.php",
      { staff_id: staff_id, staff_profile_id: staff_profile_id },
      function (response) {
        if (response.status == 0) {
          // If status is 0, proceed with confirmation
          swalConfirm(
            "Warning",
            "Are you sure you want to go back? Personal information will be lost because the staff profile is incomplete.",
            staffDeleteStatus,
            staff_id,
          );
          return;
        } else {
          // Do nothing if cancelled
          swapTableAndCreation();
          clearStaffProfileForm();
        }
      },
      "json",
    );
  });

  $("#mobile1, #mobile2, #whatsapp_no, #fam_mobile,#whatsapp").change(
    function () {
      checkMobileNo($(this).val(), $(this).attr("id"));
    },
  );

  $("#mailid").on("change", function () {
    validateEmail($(this).val(), $(this).attr("id"));
  });

  let today = new Date().toISOString().split("T")[0];

  // Restrict future dates
  $("#dob, #fam_dob, #anniversary_date").attr("max", today);

  $("#dob, #fam_dob, #anniversary_date").on("change", function () {
    var dobValue = $(this).val();

    if (!dobValue) return;

    var dob = new Date(dobValue);
    var today = new Date();

    // Remove time portion
    today.setHours(0, 0, 0, 0);

    // Future date validation
    if (dob > today) {
      swalError("Warning", "Future date is not allowed.");
      $(this).val("");

      // Clear age only when DOB field changes
      if ($(this).attr("id") === "dob") {
        $("#age").val("");
      }

      return;
    }

    // Calculate age only for main DOB field
    if ($(this).attr("id") === "dob") {
      var age = today.getFullYear() - dob.getFullYear();
      var m = today.getMonth() - dob.getMonth();

      if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
        age--;
      }

      $("#age").val(age);
    }
  });

  $("#exp_type").on("change", function () {
    const isFresher = $(this).val();
    if (isFresher === "1") {
      $(".experience").find("input").val("");
      $(".experience").find("input").prop("readonly", true);
    } else {
      $(".experience").find("input").prop("readonly", false);
    }
  });

  $("#company_name").on("change", function () {
    let company_id = $(this).val();
    let company_text = $("#company_name option:selected").text(); // selected company name
    if (company_id) {
      $("#company").val(company_text);
      autoGenStaffId("", company_id);
      getBranchList(company_id, "#branch_name,#branch");
      getDepartmentList(company_id, "#department", "");
      getDesignationList(company_id, "");
      getShiftList(company_id);
      getCTCInfoTable(company_id);
    } else {
      $("#branch,#branch_name")
        .empty()
        .append('<option value="">Select Branch Name</option>');
      $("#department")
        .empty()
        .append('<option value="">Select Department</option>');
      $("#designation")
        .empty()
        .append('<option value="">Select Designation</option>');
      $("#shift").empty().append('<option value="">Select Shift</option>');
      // Reset CTC Table
      $("#ctc_info_table tbody").empty();

      // Reset Total Fields
      $("#total_ctc_amount").val("");
      $("#company").val("");
      $("#total_ctc_percentage").val("");
    }
  });

  $("#department").on("change", function () {
    let dept_id = $(this).val();
    if (dept_id) {
      getTeamList(dept_id, "");
    } else {
      $("#team").empty().append('<option value="">Select Team</option>');
    }
  });

  $("#company_search").on("change", function () {
    let company_id = $(this).val();
    if (company_id) {
      getBranchList(company_id, "#branch_search");
      getDepartmentList(company_id, "#department_search", "");
    } else {
      $("#branch_search")
        .empty()
        .append('<option value="">Select Branch Name</option>');
      $("#department_search")
        .empty()
        .append('<option value="">Select Department</option>');
    }
  });

  $("#designation").on("change", async function () {
    let selectedLevel = parseInt(
      $("#designation option:selected").data("level"),
    );

    let company_id = $("#company_name").val();

    await getReportingPerson(company_id, selectedLevel);
  });

  $("#pic").change(function () {
    let pic = $("#pic")[0];
    let img = $("#imgshow");
    compressImage(this, 200);
    img.attr("src", URL.createObjectURL(pic.files[0]));
  });

  $("#marital_status").on("change", function () {
    toggleSpouseField();
  });

  $("#branch_admin").on("change", function () {
    toggleBranchField();
  });

  $("#ot_payment").on("change", function () {
    toggleOTField();
  });

  $("#state").change(function () {
    getDistrictList($(this).val());
  });

  /// Document Info

  /////Document Modal////
  $("#submit_document").click(function (event) {
    event.preventDefault();
    //Validation
    let staff_id = $("#staff_auto_id").val();
    let staff_profile_id = $("#staff_profile_id").val();
    let upload = $("#upload")[0].files[0];
    let doc_upload = $("#doc_upload").val();
    let doc_name = $("#doc_name").val();
    let doc_type = $("#doc_type").val();
    let document_id = $("#document_id").val();

    var data = ["doc_name", "doc_type"];
    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });
    if (isValid) {
      let docDetail = new FormData();
      docDetail.append("doc_name", doc_name);
      docDetail.append("doc_type", doc_type);
      docDetail.append("document_id", document_id);
      docDetail.append("upload", upload);
      docDetail.append("doc_upload", doc_upload);

      docDetail.append("staff_id", staff_id);
      docDetail.append("staff_profile_id", staff_profile_id);
      $.ajax({
        url: "api/staff_creation/submit_document.php",
        type: "post",
        data: docDetail,
        contentType: false,
        processData: false,
        cache: false,
        success: function (response) {
          if (response == "2") {
            swalSuccess("Success", "Document Info Added Successfully!");
          } else if (response == "1") {
            swalSuccess("Success", "Document Info Updated Successfully!");
          } else {
            swalError("Error", "Error in Document Info Table");
          }
          getDocumentTable();
        },
      });
    }
  });

  // Edit Document Info
  $(document).on("click", ".documentActionBtn", function () {
    var id = $(this).attr("value"); // Get value attribute
    $.post(
      "api/staff_creation/document_creation_data.php",
      { id: id },
      function (response) {
        $("#document_id").val(id);
        $("#doc_name").val(response[0].doc_name);
        $("#doc_type").val(response[0].doc_type);
        $("#doc_upload").val(response[0].upload);
      },
      "json",
    );
  });

  // Delete Document Info

  $(document).on("click", ".documentDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the Document?",
      getDocumentDelete,
      id,
    );
    return;
  });
  // Document Info End

  /////family Modal////
  $("#submit_family").click(function (event) {
    event.preventDefault();
    // Validation

    let staff_id = $("#staff_auto_id").val();
    let staff_profile_id = $("#staff_profile_id").val();
    let fam_name = $("#fam_name").val();
    let fam_dob = $("#fam_dob").val();
    let fam_relationship = $("#fam_relationship").val();
    let fam_occupation = $("#fam_occupation").val();
    let fam_mobile = $("#fam_mobile").val();
    let family_id = $("#family_id").val();

    var data = [
      "fam_name",
      "fam_relationship",
      "fam_dob",
      "fam_occupation",
      "fam_mobile",
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
        "api/staff_creation/submit_family_info.php",
        {
          staff_id,
          staff_profile_id,
          fam_name,
          fam_relationship,
          fam_dob,
          fam_occupation,
          fam_mobile,
          family_id,
        },
        function (response) {
          if (response == "2") {
            swalSuccess("Success", "Family Info Added Successfully!");
          } else if (response == "1") {
            swalSuccess("Success", "Family Info Updated Successfully!");
          } else {
            swalError("Error", "Error in Family Info Table");
          }
          // Refresh the family table
          getFamilyTable();
        },
      );
    }
  });

  $(document).on("click", ".familyActionBtn", function () {
    var id = $(this).attr("value"); // Get value attribute
    $.post(
      "api/staff_creation/family_creation_data.php",
      { id: id },
      function (response) {
        $("#family_id").val(id);
        $("#fam_name").val(response[0].fam_name);
        $("#fam_relationship").val(response[0].fam_relationship);
        $("#fam_dob").val(response[0].fam_dob);
        $("#fam_occupation").val(response[0].fam_occupation);
        $("#fam_mobile").val(response[0].fam_mobile);
      },
      "json",
    );
  });

  $(document).on("click", ".familyDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the Family Details?",
      getFamilyDelete,
      id,
    );
    return;
  });
  // Family Info End
  // Qualification Info Start
  $("#submit_qualification").click(function (event) {
    event.preventDefault();
    // Validation

    let staff_id = $("#staff_auto_id").val();
    let staff_profile_id = $("#staff_profile_id").val();
    let highest_qualification = $("#highest_qualification").val();
    let degree = $("#degree").val();
    let specialization = $("#specialization").val();
    let college = $("#college").val();
    let university = $("#university").val();
    let year_of_passing = $("#year_of_passing").val();
    let qualification_id = $("#qualification_id").val();

    var data = [
      "highest_qualification",
      "degree",
      "specialization",
      "college",
      "university",
      "year_of_passing",
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
        "api/staff_creation/submit_qualification_info.php",
        {
          staff_id,
          highest_qualification,
          degree,
          specialization,
          college,
          university,
          year_of_passing,
          qualification_id,
          staff_profile_id,
        },
        function (response) {
          if (response == "2") {
            swalSuccess("Success", "Qualification Info Added Successfully!");
          } else if (response == "1") {
            swalSuccess("Success", "Qualification Info Updated Successfully!");
          } else {
            swalError("Error", "Error in Qualification Info Table");
          }
          // Refresh the qualification table
          getQualificationTable();
        },
      );
    }
  });

  $(document).on("click", ".qualifyActionBtn", function () {
    var id = $(this).attr("value"); // Get value attribute
    $.post(
      "api/staff_creation/qualification_creation_data.php",
      { id: id },
      function (response) {
        $("#qualification_id").val(id);
        $("#highest_qualification").val(response[0].highest_qualification);
        $("#degree").val(response[0].degree);
        $("#specialization").val(response[0].specialization);
        $("#college").val(response[0].college);
        $("#university").val(response[0].university);
        $("#year_of_passing").val(response[0].year_of_passing);
      },
      "json",
    );
  });

  $(document).on("click", ".qualifyDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the Qualification Details?",
      getQualificationDelete,
      id,
    );
    return;
  });
  // Qualification Info End

  // Experience Info Start
  $("#submit_experience").click(function (event) {
    event.preventDefault();
    // Validation

    let staff_id = $("#staff_auto_id").val();
    let staff_profile_id = $("#staff_profile_id").val();
    let exp_type = $("#exp_type").val();
    let total_experience = $("#total_experience").val();
    let pre_company = $("#pre_company").val();
    let pre_designation = $("#pre_designation").val();
    let work_duration = $("#work_duration").val();
    let last_salary = $("#last_salary").val();
    let reason_for_leaving = $("#reason_for_leaving").val();
    let experience_id = $("#experience_id").val();

    var data = ["exp_type"];

    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });

    if (isValid) {
      $.post(
        "api/staff_creation/submit_experience_info.php",
        {
          staff_id,
          exp_type,
          total_experience,
          pre_company,
          pre_designation,
          work_duration,
          last_salary,
          reason_for_leaving,
          experience_id,
          staff_profile_id,
        },
        function (response) {
          if (response == "2") {
            swalSuccess("Success", "Experience Info Added Successfully!");
          } else if (response == "1") {
            swalSuccess("Success", "Experience Info Updated Successfully!");
          } else {
            swalError("Error", "Error in Experience Info Table");
          }
          // Refresh the experience table
          getExperienceTable();
        },
      );
    }
  });

  $(document).on("click", ".expActionBtn", function () {
    var id = $(this).attr("value"); // Get value attribute
    $.post(
      "api/staff_creation/experience_creation_data.php",
      { id: id },
      function (response) {
        $("#experience_id").val(id);
        $("#exp_type").val(response[0].exp_type);
        $("#total_experience").val(response[0].total_experience);
        $("#pre_company").val(response[0].pre_company);
        $("#pre_designation").val(response[0].pre_designation);
        $("#work_duration").val(response[0].work_duration);
        $("#last_salary").val(response[0].last_salary);
        $("#reason_for_leaving").val(response[0].reason_for_leaving);
      },
      "json",
    );
  });

  $(document).on("click", ".expDeleteBtn", function () {
    var id = $(this).attr("value");
    swalConfirm(
      "Delete",
      "Do you want to Delete the Experience Details?",
      getExperienceDelete,
      id,
    );
    return;
  });
  // Experience Info End
  // Submit Staff Creation BASIC Info
  $("#submit_staff").click(function (event) {
    $("#submit_staff").attr("disabled", true);
    event.preventDefault();
    // Validate form fields
    let pic = $("#pic")[0].files[0];
    let per_pic = $("#per_pic").val();
    let company_name = $("#company_name").val();
    let staff_id = $("#staff_auto_id").val();
    let staff_name = $("#staff_name").val();
    let address = $("#address").val();
    let state = $("#state").val();
    let district = $("#district").val();
    let place = $("#place").val();
    let pincode = $("#pincode").val();
    let dob = $("#dob").val();
    let age = $("#age").val();
    let blood_group = $("#blood_group").val();
    let gender = $("#gender").val();
    let marital_status = $("#marital_status").val();
    let spouse_name = $("#spouse_name").val();
    let anniversary_date = $("#anniversary_date").val();
    let joining_date = $("#joining_date").val();
    let relieve_date = $("#relieve_date").val();
    let notice_period = $("#notice_period").val();
    let email = $("#mailid").val();
    let mobile1 = $("#mobile1").val();
    let mobile2 = $("#mobile2").val();
    let whatsapp = $("#whatsapp").val();
    let instagram = $("#instagram").val();
    let facebook = $("#facebook").val();
    let acc_holder_name = $("#acc_holder_name").val();
    let bank_name = $("#bank_name").val();
    let acc_number = $("#acc_number").val();
    let ifsc_code = $("#ifsc_code").val();
    let bank_branch = $("#bank_branch").val();
    let staff_profile_id = $("#staff_profile_id").val();

    var data = [
      "company_name",
      "staff_auto_id",
      "staff_name",
      "address",
      "state",
      "district",
      "place",
      "pincode",
      "gender",
      "marital_status",
      "joining_date",
      "notice_period",
      "mailid",
      "mobile1",
      "acc_holder_name",
      "bank_name",
      "acc_number",
      "ifsc_code",
      "bank_branch",
    ];
    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });
    // Additional validation for married staff
    if (marital_status == "1") {
      var spouseValid = validateField($("#spouse_name").val(), "spouse_name");
      var anniversaryValid = validateField(
        $("#anniversary_date").val(),
        "anniversary_date",
      );

      if (!spouseValid || !anniversaryValid) {
        isValid = false;
      }
    } else {
      $("#spouse_name").css("border", "1px solid #cecece");
      $("#anniversary_date").css("border", "1px solid #cecece");
    }
    if (pic === undefined && per_pic === "") {
      let isUploadValid = validateField("", "pic");
      let isHiddenValid = validateField("", "per_pic");
      if (!isUploadValid || !isHiddenValid) {
        isValid = false;
      } else {
        $("#pic").css("border", "1px solid #cecece");
        $("#per_pic").css("border", "1px solid #cecece");
      }
    } else {
      $("#pic").css("border", "1px solid #cecece");
      $("#per_pic").css("border", "1px solid #cecece");
    }

    if (isValid) {
      let personalDetail = new FormData();
      personalDetail.append("staff_id", staff_id);
      personalDetail.append("staff_name", staff_name);
      personalDetail.append("company_name", company_name);
      personalDetail.append("address", address);
      personalDetail.append("state", state);
      personalDetail.append("district", district);
      personalDetail.append("place", place);
      personalDetail.append("pincode", pincode);
      personalDetail.append("dob", dob);
      personalDetail.append("age", age);
      personalDetail.append("blood_group", blood_group);
      personalDetail.append("gender", gender);
      personalDetail.append("marital_status", marital_status);
      personalDetail.append("spouse_name", spouse_name);
      personalDetail.append("anniversary_date", anniversary_date);
      personalDetail.append("joining_date", joining_date);
      personalDetail.append("relieve_date", relieve_date);
      personalDetail.append("notice_period", notice_period);
      personalDetail.append("pf_available", pf_available);
      personalDetail.append("esi_available", esi_available);
      personalDetail.append("pt_available", pt_available);
      personalDetail.append("pic", pic);
      personalDetail.append("per_pic", per_pic);
      personalDetail.append("email", email);
      personalDetail.append("mobile1", mobile1);
      personalDetail.append("mobile2", mobile2);
      personalDetail.append("whatsapp", whatsapp);
      personalDetail.append("instagram", instagram);
      personalDetail.append("facebook", facebook);
      personalDetail.append("acc_holder_name", acc_holder_name);
      personalDetail.append("bank_name", bank_name);
      personalDetail.append("acc_number", acc_number);
      personalDetail.append("ifsc_code", ifsc_code);
      personalDetail.append("bank_branch", bank_branch);
      personalDetail.append("staff_profile_id", staff_profile_id);
      $.ajax({
        url: "api/staff_creation/submit_personal_info.php",
        type: "POST",
        data: personalDetail,
        contentType: false,
        processData: false,
        cache: false,
        dataType: "json",
        success: function (response) {
          // Handle success response
          if (response.result == 0) {
            swalError("Error", "Personal Info Not Added!");
            $("#submit_staff").attr("disabled", false);
          } else if (response.result == 1) {
            swalSuccess("Success", "Personal Info Added Successfully!");
            $("#pf_available").val('');
            $("#esi_ available").val('');
            $("#pt_available").val('');
            $(".staff_content").show();
            $("#staff_profile_id").val(response.last_id);
            $("#per_pic").val(response.pic);
            $(".personal_info_disble").attr("disabled", true);
            $("#submit_staff").attr("disabled", true);
            getDocumentInfoTable();
            getFamilyInfoTable();
            getQualificationInfoTable();
            getExperienceInfoTable();
            getCompanyPFDetails(company_name);
           
          }
        },
      });
    } else {
      $("#submit_staff").attr("disabled", false);
    }
  });
  // Submit Staff Creation BASIC Info
  $("#submit_staff_creation").click(function (event) {
    event.preventDefault();
    // Validate form fields
    let famInfoRowCount = $("#fam_info_table").DataTable().rows().count();
    let qualInfoRowCount = $("#qual_info_table").DataTable().rows().count();
    let ExpInfoRowCount = $("#exp_info_table").DataTable().rows().count();
    let pic = $("#pic")[0].files[0];
    let per_pic = $("#per_pic").val();
    let staff_id = $("#staff_auto_id").val();
    let staff_name = $("#staff_name").val();
    let address = $("#address").val();
    let state = $("#state").val();
    let district = $("#district").val();
    let place = $("#place").val();
    let pincode = $("#pincode").val();
    let dob = $("#dob").val();
    let age = $("#age").val();
    let blood_group = $("#blood_group").val();
    let gender = $("#gender").val();
    let marital_status = $("#marital_status").val();
    let spouse_name = $("#spouse_name").val();
    let anniversary_date = $("#anniversary_date").val();
    let joining_date = $("#joining_date").val();
    let relieve_date = $("#relieve_date").val();
    let notice_period = $("#notice_period").val();
    let pf_available = $("#pf_available").val();
    let esi_available = $("#esi_available").val();
    let pt_available = $("#pt_available").val();
    let email = $("#mailid").val();
    let mobile1 = $("#mobile1").val();
    let mobile2 = $("#mobile2").val();
    let whatsapp = $("#whatsapp").val();
    let instagram = $("#instagram").val();
    let facebook = $("#facebook").val();
    let acc_holder_name = $("#acc_holder_name").val();
    let bank_name = $("#bank_name").val();
    let acc_number = $("#acc_number").val();
    let ifsc_code = $("#ifsc_code").val();
    let bank_branch = $("#bank_branch").val();
    let company_name = $("#company_name").val();
    let branch_name = $("#branch_name").val();
    let department = $("#department").val();
    let team = $("#team").val();
    let designation = $("#designation").val();
    let off_type = $("#off_type").val();
    let reporting_person = $("#reporting_person").val();
    let reporting_person_type = $("#reporting_person")
      .find(":selected")
      .data("type");
    let branch_admin = $("#branch_admin").val();
    let branch = $("#branch").val();
    let total_ctc = $("#total_ctc").val().replace(/,/g, "");
    let annual_ctc = $("#annual_ctc").val().replace(/,/g, "");
    let shift = $("#shift").val();
    let ot_payment = $("#ot_payment").val();
    let ot_per_day = $("#ot_per_day").val();
    let staff_profile_id = $("#staff_profile_id").val();
    let company_id = $("#company_search").val();
    let branch_id = $("#branch_search").val();
    let department_id = $("#department_search").val();
    let total_ctc_amount = $("#total_ctc_amount").val();
    console.log("pf_available",pf_available);
    console.log("esi_available",esi_available);
    console.log("pt_available",pt_available);

    var data = [
      "staff_auto_id",
      "staff_name",
      "address",
      "state",
      "district",
      "place",
      "pincode",
      "gender",
      "marital_status",
      "joining_date",
      "notice_period",
      `mailid`,
      "mobile1",
      "acc_holder_name",
      "bank_name",
      "acc_number",
      "ifsc_code",
      "bank_branch",
      "company_name",
      "branch_name",
      "department",
      "designation",
      "team",
      "branch_admin",
      "total_ctc",
      "annual_ctc",
      "shift",
      "ot_payment",
      "off_type",
      "pt_available",
      "esi_available",
      "pf_available",
    ];
    if (ot_payment == "2") {
      data.push("ot_per_day");
    }
    if (branch_admin == "1") {
      data.push("branch");
    }

    var isValid = true;
    data.forEach(function (entry) {
      var fieldIsValid = validateField($("#" + entry).val(), entry);
      if (!fieldIsValid) {
        isValid = false;
      }
    });
    if (pic === undefined && per_pic === "") {
      let isUploadValid = validateField("", "pic");
      let isHiddenValid = validateField("", "per_pic");
      if (!isUploadValid || !isHiddenValid) {
        isValid = false;
      } else {
        $("#pic").css("border", "1px solid #cecece");
        $("#per_pic").css("border", "1px solid #cecece");
      }
    } else {
      $("#pic").css("border", "1px solid #cecece");
      $("#per_pic").css("border", "1px solid #cecece");
    }

    if (isValid) {
      // CTC Table Validation
      let totalCTC = parseFloat($("#total_ctc").val().replace(/,/g, "")) || 0;

      let tableTotalAmount = 0;
      let salaryTotalAmount = 0;

      let ctcRowCount = $("#ctc_info_table tbody tr").length;

      if (ctcRowCount == 0) {
        swalError("Warning", "Please Fill CTC Info Table!");
        return false;
      }

      $("#ctc_info_table tbody tr").each(function () {
        let amount =
          parseFloat($(this).find(".ctc_amount").val().replace(/,/g, "")) || 0;
        let category = $(this).find("td:eq(3)").text().trim();

        tableTotalAmount += amount;

        // Only Salary Components
        if (category == "Salary") {
          salaryTotalAmount += amount;
        }
      });

      // Salary components must equal Total CTC
      if (salaryTotalAmount != totalCTC) {
        swalError(
          "Warning",
          "Salary Components Total Must Be Equal to Total CTC!",
        );

        return false;
      }

      if (
        famInfoRowCount === 0 ||
        qualInfoRowCount === 0 ||
        ExpInfoRowCount === 0
      ) {
        swalError(
          "Warning",
          "Please Fill out Family Info and Qualification Info and Experience Info!",
        );
        return false;
      }
      let staffDetail = new FormData();
      staffDetail.append("staff_id", staff_id);
      staffDetail.append("staff_name", staff_name);
      staffDetail.append("address", address);
      staffDetail.append("state", state);
      staffDetail.append("district", district);
      staffDetail.append("place", place);
      staffDetail.append("pincode", pincode);
      staffDetail.append("dob", dob);
      staffDetail.append("age", age);
      staffDetail.append("blood_group", blood_group);
      staffDetail.append("gender", gender);
      staffDetail.append("marital_status", marital_status);
      staffDetail.append("spouse_name", spouse_name);
      staffDetail.append("anniversary_date", anniversary_date);
      staffDetail.append("joining_date", joining_date);
      staffDetail.append("relieve_date", relieve_date);
      staffDetail.append("notice_period", notice_period);
      staffDetail.append("pf_available", pf_available);
      staffDetail.append("esi_available", esi_available);
      staffDetail.append("pt_available", pt_available);
      staffDetail.append("pic", pic);
      staffDetail.append("per_pic", per_pic);
      staffDetail.append("email", email);
      staffDetail.append("mobile1", mobile1);
      staffDetail.append("mobile2", mobile2);
      staffDetail.append("whatsapp", whatsapp);
      staffDetail.append("instagram", instagram);
      staffDetail.append("facebook", facebook);
      staffDetail.append("acc_holder_name", acc_holder_name);
      staffDetail.append("bank_name", bank_name);
      staffDetail.append("acc_number", acc_number);
      staffDetail.append("ifsc_code", ifsc_code);
      staffDetail.append("bank_branch", bank_branch);
      staffDetail.append("company_name", company_name);
      staffDetail.append("branch_name", branch_name);
      staffDetail.append("department", department);
      staffDetail.append("team", team);
      staffDetail.append("designation", designation);
      staffDetail.append("reporting_person", reporting_person);
      staffDetail.append("reporting_person_type", reporting_person_type);
      staffDetail.append("branch_admin", branch_admin);
      staffDetail.append("branch", branch);
      staffDetail.append("total_ctc", total_ctc);
      staffDetail.append("annual_ctc", annual_ctc);
      staffDetail.append("shift", shift);
      staffDetail.append("ot_payment", ot_payment);
      staffDetail.append("ot_per_day", ot_per_day);
      staffDetail.append("off_type", off_type);
      staffDetail.append("total_ctc_amount", total_ctc_amount);
      let ctcDetails = [];

      $("#ctc_info_table tbody tr").each(function () {
        let ctc_id = $(this).find(".ctc_id").val();

        let ctc_amount = $(this).find(".ctc_amount").val();

        let ctc_percentage = $(this).find(".ctc_percentage").val();

        ctcDetails.push({
          ctc_id: ctc_id,
          ctc_amount: ctc_amount,
          ctc_percentage: ctc_percentage,
        });
      });

      staffDetail.append("ctcDetails", JSON.stringify(ctcDetails));

      staffDetail.append("staff_profile_id", staff_profile_id);
      swalConfirm(
        "Are you sure?",
        "Do you want to submit this Staff Creation?",
        function () {
          $("#submit_staff_creation").attr("disabled", true);
          $.ajax({
            url: "api/staff_creation/submit_staff_info.php",
            type: "POST",
            data: staffDetail,
            contentType: false,
            processData: false,
            cache: false,
            dataType: "json",

            success: function (response) {
              if (response.result == 0) {
                swalError("Error", "Staff Info Not Added!");
              } else if (response.result == 1) {
                swalSuccess("Success", "Staff Info Updated Successfully!");
                $("#staff_profile_id").val("");
                swapTableAndCreation();
                getStaffTable(company_id, branch_id, department_id);
                clearStaffProfileForm();
              }
              $("#submit_staff_creation").attr("disabled", false);
            },

            error: function () {
              swalError("Error", "Something went wrong!");
              $("#submit_staff_creation").attr("disabled", false);
            },
          });
        },
      );
    }
  });

  $("#total_ctc").on("input", function () {
    let monthly_ctc = parseFloat($(this).val()) || 0;

    let annual_ctc = monthly_ctc * 12;

    $("#annual_ctc").val(moneyFormatIndia(annual_ctc));
  });

  $(document).on("input", ".ctc_amount", function () {
    let totalCTC = parseFloat($("#total_ctc").val()) || 0;

    if (totalCTC <= 0) {
      swalError("Warning", "Please Enter Total CTC First");
      $(this).val("");
      return false;
    }

    let currentAmount = parseFloat($(this).val()) || 0;
    let category = $(this).closest("tr").find("td:eq(3)").text().trim();

    let percentage = 0;
    // Only Salary rows calculate %
    if (category == "Salary") {
      percentage = (currentAmount / totalCTC) * 100;
      $(this).closest("tr").find(".ctc_percentage").val(percentage.toFixed(2));
    } else {
      // Reimbursement no %
      $(this).closest("tr").find(".ctc_percentage").val("0");
    }

    calculateTotals($(this));
  });

  // Radio Change
  $('input[name="staff_status"]').on("change", function () {
    let company_id = $("#company_search").val();
    let branch_id = $("#branch_search").val();
    let department_id = $("#department_search").val();
    if (!company_id && !branch_id && !department_id) {
      swalError("Warning", "Please Select Atleast One Fields!");
      return;
    }
    getStaffTable(company_id, branch_id, department_id);
  });
  // Radio Change
  $("#view_staff").on("click", function () {
    let company_id = $("#company_search").val();
    let branch_id = $("#branch_search").val();
    let department_id = $("#department_search").val();

    if (!company_id && !branch_id && !department_id) {
      swalError("Warning", "Please Select Atleast One Fields!");
      return;
    }
    $(".radio-card").show();
    getStaffTable(company_id, branch_id, department_id);
  });

  $(document).on("click", ".staffEditBtn", function () {
    let id = $(this).attr("value");
    $("#staff_profile_id").val(id);
    swapTableAndCreation();
    editStaffProfile(id);
  });

  $("#clear_staff").click(function () {
    event.preventDefault();
    clearStaffProfileForm();
  });

  // DOcument Ready End
});

$(function () {
  nameFormatter("#staff_name");
  getCompanyName("#company_search");
});

function getStaffTable(company_id, branch_id, department_id) {
  let status = $('input[name="staff_status"]:checked').val(); // 1 / 2
  let params = {
    company_id: company_id,
    branch_id: branch_id,
    department_id: department_id,
    status: status,
  };
  serverSideTable(
    "#staff_create",
    params,
    "api/staff_creation/staff_list.php",
    " Staff List",
  );
}

function swapTableAndCreation() {
  if ($(".staff_table_content").is(":visible")) {
    $(".staff_table_content").hide();
    $("#add_staff").hide();
    $(".outer_search_card").hide();
    $("#staff_creation_content").show();
    $("#back_btn").show();
  } else {
    $(".staff_table_content").show();
    $("#add_staff").show();
    $(".outer_search_card").show();
    $("#staff_creation_content").hide();
    $("#back_btn").hide();
  }
}
async function autoGenStaffId(id = "", company_id = "") {
  try {
    let response = await $.ajax({
      url: "api/staff_creation/get_autostaff_id.php",
      type: "POST",
      data: { id: id, company_id: company_id },
      dataType: "json",
      cache: false,
    });

    $("#staff_auto_id").val(response.staff_id);
  } catch (error) {
    console.error("AJAX Error:", error);
  }
}

function toggleSpouseField() {
  if ($("#marital_status").val() == "1") {
    // Yes
    $(".spouse-div").show();
  } else {
    $(".spouse-div").hide();
    $("#spouse_name").val("");
  }
}

function toggleBranchField() {
  if ($("#branch_admin").val() == "1") {
    // Yes
    $(".branch_div").show();
  } else {
    $(".branch_div").hide();
    $("#branch").val("");
  }
}

function toggleOTField() {
  if ($("#ot_payment").val() == "1") {
    $(".ot_per_day_div").hide();
  } else {
    $(".ot_per_day_div").show();
  }
}

// Get Document  Table
function getDocumentTable() {
  let staff_profile_id = $("#staff_profile_id").val();
  $.post(
    "api/staff_creation/document_list.php",
    { staff_profile_id },
    function (response) {
      var columnMapping = [
        "sno",
        "doc_name",
        "doc_type",
        "upload",
        "created_date",
        "return_date",
        "action",
      ];
      appendDataToTable("#document_table", response, columnMapping);
      setdtable("#document_table", "Document Info List");
      $("#document_form input").val("");
      $("#document_form input").css("border", "1px solid #cecece");
      $("#document_form select").css("border", "1px solid #cecece");
      $("#document_form select").each(function () {
        $(this).val($(this).find("option:first").val());
      });
    },
    "json",
  );
}

async function getDocumentInfoTable() {
  let staff_profile_id = $("#staff_profile_id").val();

  if (staff_profile_id == "") return false;

  try {
    let response = await $.ajax({
      url: "api/staff_creation/document_list.php",
      type: "POST",
      data: { staff_profile_id: staff_profile_id },
      dataType: "json",
    });

    var columnMapping = [
      "sno",
      "doc_name",
      "doc_type",
      "upload",
      "created_date",
      "return_date",
    ];

    appendDataToTable("#doc_info_table", response, columnMapping);
    setdtable("#doc_info_table", "Document Info List");
  } catch (error) {
    console.error("Document Table Error:", error);
  }
}

function getDocumentDelete(id) {
  $.post(
    "api/staff_creation/delete_document.php",
    { id },
    function (response) {
      if (response == "0") {
        swalError("Warning", "Failed to Delete Document");
      } else if (response == "1") {
        swalSuccess("Success", "Document Info Deleted Successfully!");
        getDocumentTable();
      }
    },
    "json",
  );
}

async function getFamilyInfoTable() {
  let staff_profile_id = $("#staff_profile_id").val();
  try {
    let response = await $.ajax({
      url: "api/staff_creation/family_creation_list.php",
      type: "POST",
      data: { staff_profile_id: staff_profile_id },
      dataType: "json",
    });

    var columnMapping = [
      "sno",
      "fam_name",
      "fam_relationship",
      "fam_dob",
      "fam_occupation",
      "fam_mobile",
    ];
    appendDataToTable("#fam_info_table", response, columnMapping);
    setdtable("#fam_info_table", "Family Info List");
  } catch (error) {
    console.error("Family Info Table Error:", error);
  }
}

function getFamilyTable() {
  let staff_profile_id = $("#staff_profile_id").val();
  $.post(
    "api/staff_creation/family_creation_list.php",
    { staff_profile_id: staff_profile_id },
    function (response) {
      var columnMapping = [
        "sno",
        "fam_name",
        "fam_relationship",
        "fam_dob",
        "fam_occupation",
        "fam_mobile",
        "action",
      ];
      appendDataToTable("#family_creation_table", response, columnMapping);
      setdtable("#family_creation_table", "Family Info List");
      $("#family_form input").val("");
      $("#family_form input").css("border", "1px solid #cecece");
      $("#family_form select").css("border", "1px solid #cecece");
      $("#fam_relationship").val("");
    },
    "json",
  );
}

function getFamilyDelete(id) {
  let staff_id = $("#staff_auto_id").val();
  let staff_profile_id = $("#staff_profile_id").val();
  $.post(
    "api/staff_creation/delete_family_creation.php",
    { id, staff_id, staff_profile_id },
    function (response) {
      if (response == "0") {
        swalError("Warning", "Have to maintain atleast one Family Info");
      } else if (response == "1") {
        swalSuccess("Success", "Family Info Deleted Successfully!");
        getFamilyTable();
      } else if (response == "2") {
        swalError("Access Denied", "Family Member Already Used");
      } else {
        swalError("Warning", "Error occur While Delete Family Info.");
      }
    },
    "json",
  );
}

async function getQualificationInfoTable() {
  let staff_profile_id = $("#staff_profile_id").val();
  try {
    let response = await $.ajax({
      url: "api/staff_creation/qualification_creation_list.php",
      type: "POST",
      data: { staff_profile_id: staff_profile_id },
      dataType: "json",
    });

    var columnMapping = [
      "sno",
      "highest_qualification",
      "degree",
      "specialization",
      "college",
      "university",
      "year_of_passing",
    ];
    appendDataToTable("#qual_info_table", response, columnMapping);
    setdtable("#qual_info_table", "Qualification Info List");
  } catch (error) {
    console.error("Qualification Info Table Error:", error);
  }
}

function getQualificationTable() {
  let staff_profile_id = $("#staff_profile_id").val();
  $.post(
    "api/staff_creation/qualification_creation_list.php",
    { staff_profile_id: staff_profile_id },
    function (response) {
      var columnMapping = [
        "sno",
        "highest_qualification",
        "degree",
        "specialization",
        "college",
        "university",
        "year_of_passing",
        "action",
      ];
      appendDataToTable(
        "#qualification_creation_table",
        response,
        columnMapping,
      );
      setdtable("#qualification_creation_table", "Qualification Info List");
      $("#qualification_form input").val("");
      $("#qualification_form input").css("border", "1px solid #cecece");
      $("#qualification_form select").css("border", "1px solid #cecece");
    },
    "json",
  );
}

function getQualificationDelete(id) {
  let staff_id = $("#staff_auto_id").val();
  let staff_profile_id = $("#staff_profile_id").val();
  $.post(
    "api/staff_creation/delete_qualification_creation.php",
    { id, staff_id, staff_profile_id },
    function (response) {
      if (response == "0") {
        swalError("Warning", "Have to maintain atleast one Qualification Info");
      } else if (response == "1") {
        swalSuccess("Success", "Qualification Info Deleted Successfully!");
        getQualificationTable();
      } else if (response == "2") {
        swalError("Access Denied", "Qualification Info Already Used");
      } else {
        swalError("Warning", "Error occur While Delete Qualification Info.");
      }
    },
    "json",
  );
}

async function getExperienceInfoTable() {
  let staff_profile_id = $("#staff_profile_id").val();
  try {
    let response = await $.ajax({
      url: "api/staff_creation/experience_creation_list.php",
      type: "POST",
      data: { staff_profile_id: staff_profile_id },
      dataType: "json",
    });

    var columnMapping = [
      "sno",
      "exp_type",
      "total_experience",
      "pre_company",
      "pre_designation",
      "work_duration",
      "last_salary",
      "reason_for_leaving",
    ];
    appendDataToTable("#exp_info_table", response, columnMapping);
    setdtable("#exp_info_table", "Experience Info List");
    $(".experience").find("input").prop("readonly", false);
  } catch (error) {
    console.error("Experience Info Table Error:", error);
  }
}
async function getCompanyPFDetails(company_name) {

  try {
    let response = await $.ajax({
      url: "api/staff_creation/get_company_pf_details.php",
      type: "POST",
      data: { company_name: company_name },
      dataType: "json",
    });

    console.log(response);

    if (response[0].pf_applicable == 2) {
      console.log("jj");

      $("#pf_available")
        .val(response[0].pf_applicable)
        .prop("disabled", true);

    } else {

      $("#pf_available")
        .val(response[0].pf_applicable)
        .prop("disabled", false);

    }


    if (response[0].esi_applicable == 2) {

      $("#esi_available")
        .val(response[0].esi_applicable)
        .prop("disabled", true);

    } else {

      $("#esi_available")
        .val(response[0].esi_applicable)
        .prop("disabled", false);

    }


    if (response[0].professional_tax_applicable == 2) {

      $("#pt_available")
        .val(response[0].professional_tax_applicable)
        .prop("disabled", true);

    } else {

      $("#pt_available")
        .val(response[0].professional_tax_applicable)
        .prop("disabled", false);

    }

  } catch (error) {
    console.error("Experience Info Table Error:", error);
  }
}

function getExperienceTable() {
  let staff_profile_id = $("#staff_profile_id").val();
  $.post(
    "api/staff_creation/experience_creation_list.php",
    { staff_profile_id: staff_profile_id },
    function (response) {
      var columnMapping = [
        "sno",
        "exp_type",
        "total_experience",
        "pre_company",
        "pre_designation",
        "work_duration",
        "last_salary",
        "reason_for_leaving",
        "action",
      ];
      appendDataToTable("#experience_creation_table", response, columnMapping);
      setdtable("#experience_creation_table", "Experience Info List");
      $("#experience_form input").val("");
      $("#experience_form input").css("border", "1px solid #cecece");
      $("#experience_form select").css("border", "1px solid #cecece");
      $("#exp_type").val("");
    },
    "json",
  );
}

function getExperienceDelete(id) {
  let staff_id = $("#staff_auto_id").val();
  let staff_profile_id = $("#staff_profile_id").val();
  $.post(
    "api/staff_creation/delete_experience_creation.php",
    { id, staff_id, staff_profile_id },
    function (response) {
      if (response == "0") {
        swalError("Warning", "Have to maintain atleast one Experience Info");
      } else if (response == "1") {
        swalSuccess("Success", "Experience Info Deleted Successfully!");
        getExperienceTable();
      } else if (response == "2") {
        swalError("Access Denied", "Experience Info Already Used");
      } else {
        swalError("Warning", "Error occur While Delete Experience Info.");
      }
    },
    "json",
  );
}

async function getStateList() {
  try {
    const response = await $.ajax({
      url: "api/common_files/get_state_list.php",
      type: "POST",
      dataType: "json",
    });

    let appendStateOption = "<option value=''>Select State</option>";

    $.each(response, function (index, val) {
      appendStateOption += `
                <option value="${val.id}">
                    ${val.state_name}
                </option>
            `;
    });

    $("#state").empty().append(appendStateOption);
  } catch (error) {
    console.error("Error loading state list:", error);
  }
}

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

function staffDeleteStatus(staff_id) {
  let staff_profile_id = $("#staff_profile_id").val();
  // Proceed with deletion
  $.post(
    "api/staff_creation/staff_sts_delete.php",
    { staff_id: staff_id, staff_profile_id: staff_profile_id },
    function (deleteResponse) {
      if (deleteResponse.success) {
        swalSuccess("Success", "Personal Info Deleted Successfully.");
        clearStaffProfileForm();
        swapTableAndCreation();
      } else {
        swalError("Error", "Failed to delete personal info.");
      }
    },
    "json",
  );
}

async function getCompanyName(selector) {
  return new Promise((resolve, reject) => {
    $.post(
      "api/attendance_files/get_company_list.php",
      {},

      function (response) {
        let dropdown = $(selector);
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

async function getBranchList(company_id, selector) {
  try {
    const response = await $.ajax({
      url: "api/staff_creation/company_mapped_branches.php",
      data: { company_id: company_id },
      type: "POST",
      dataType: "json",
    });

    let appendBranchOption = '<option value="">Select Branch Name</option>';

    $.each(response, function (index, val) {
      appendBranchOption += `
                <option value="${val.id}">
                    ${val.branch_name}
                </option>
            `;
    });

    $(selector).empty().append(appendBranchOption);
  } catch (error) {
    console.error("Error loading branch list:", error);
  }
}

async function getDepartmentList(company_id, selector, selected_dept = "") {
  try {
    const response = await $.ajax({
      url: "api/staff_creation/company_mapped_department.php",
      type: "POST",
      dataType: "json",
      data: {
        company_id: company_id,
        selected_dept: selected_dept,
      },
    });

    let deptOption = '<option value="">Select Department</option>';

    $.each(response, function (index, val) {
      deptOption += `<option value="${val.id}">${val.department_name}</option>`;
    });

    $(selector).empty().append(deptOption);
  } catch (error) {
    console.error(error);
  }
}

async function getShiftList(company_id) {
  try {
    const response = await $.ajax({
      url: "api/staff_creation/company_mapped_shift.php",
      data: { company_id: company_id },
      type: "POST",
      dataType: "json",
    });

    let shiftOption = '<option value="">Select Shift</option>';

    $.each(response, function (index, val) {
      shiftOption += `
                <option 
                    value="${val.id}"
                    data-time="${val.shift_time}">
                    ${val.shift_name}
                </option>
            `;
    });

    $("#shift").empty().append(shiftOption);
  } catch (error) {
    console.error("Error loading shift list:", error);
  }
}

async function getDesignationList(company_id, selected_designation = "") {
  try {
    const response = await $.ajax({
      url: "api/staff_creation/company_mapped_designation.php",
      type: "POST",
      dataType: "json",
      data: {
        company_id: company_id,
        selected_designation: selected_designation,
      },
    });

    let designationOption = '<option value="">Select Designation</option>';

    $.each(response, function (index, val) {
      designationOption += `
                <option 
                    value="${val.id}"
                    data-level="${val.designation_level}">
                    ${val.designation}
                </option>
            `;
    });

    $("#designation").empty().append(designationOption);
  } catch (error) {
    console.error("Error loading Designation list:", error);
  }
}

async function getTeamList(dep_id, selected_team = "") {
  let company_id = $("#company_name").val();

  try {
    const response = await $.ajax({
      url: "api/staff_creation/company_mapped_team.php",
      type: "POST",
      dataType: "json",
      data: {
        dep_id: dep_id,
        company_id: company_id,
        selected_team: selected_team,
      },
    });

    let teamOption = '<option value="">Select Team</option>';

    $.each(response, function (index, val) {
      teamOption += `
                <option value="${val.id}">
                    ${val.team_name}
                </option>
            `;
    });

    $("#team").empty().append(teamOption);
  } catch (error) {
    console.error("Error loading Team list:", error);
  }
}

function getCTCInfoTable(company_id) {
  return $.ajax({
    url: "api/staff_creation/get_ctc_info.php",
    type: "POST",
    dataType: "json",
    data: { company_id: company_id },
    success: function (response) {
      let tr = "";

      response.forEach((row, index) => {
        tr += `
                    <tr>
                        <td>${index + 1}</td>

                        <td>
                            ${row.salary_component}
                            <input type="hidden" class="ctc_id" value="${row.id}">
                        </td>

                        <td>${row.component_classification}</td>

                        <td>
                            ${row.component_category}
                        </td>

                        <td>
                            <input type="text"
                                   class="form-control ctc_amount"
                                   id="ctc_amount_${row.id}"
                                   min="0">
                        </td>

                        <td>
                            <input type="text"
                                   class="form-control ctc_percentage"
                                   id="ctc_percentage_${row.id}"
                                   readonly>
                        </td>
                    </tr>
                `;
      });

      $("#ctc_info_table tbody").html(tr);
    },
  });
}

function calculateTotals(currentInput) {
  let totalAmount = 0;
  let totalPercentage = 0;

  let salaryAmount = 0;
  let salaryPercentage = 0;

  let enteredCTC = parseFloat($("#total_ctc").val()) || 0;

  $("#ctc_info_table tbody tr").each(function () {
    let amount = parseFloat($(this).find(".ctc_amount").val()) || 0;
    let percentage = parseFloat($(this).find(".ctc_percentage").val()) || 0;
    let category = $(this).find("td:eq(3)").text().trim();

    totalAmount += amount;
    totalPercentage += percentage;

    // // Only Salary validation
    if (category == "Salary") {
      salaryAmount += amount;
      salaryPercentage += percentage;
    }
  });

  totalPercentage = Math.round(totalPercentage * 100) / 100;

  if (totalPercentage > 100) {
    totalPercentage = 100;
  }

  $("#total_ctc_amount").val(totalAmount);
  $("#total_ctc_percentage").val(totalPercentage);

  // Salary should not exceed CTC
  if (salaryAmount > enteredCTC) {
    swalError("Warning", "Salary Components should not exceed Total CTC");

    currentInput.val("");
    currentInput.closest("tr").find(".ctc_percentage").val("");

    recalculateTotals();
    return false;
  }
  // Salary % should not exceed 100
  salaryPercentage = Math.min(
    enteredCTC > 0 ? (salaryAmount / enteredCTC) * 100 : 0,
    100,
  );
  if (salaryPercentage > 100) {
    swalError("Warning", "Salary Percentage should not exceed 100");

    currentInput.val("");
    currentInput.closest("tr").find(".ctc_percentage").val("");

    recalculateTotals();
    return false;
  }
}
function recalculateTotals() {
  let totalAmount = 0;
  let totalPercentage = 0;

  $(".ctc_amount").each(function () {
    totalAmount += parseFloat($(this).val()) || 0;
  });

  $(".ctc_percentage").each(function () {
    totalPercentage += parseFloat($(this).val()) || 0;
  });
  // Round to 2 decimal places to avoid 100.01, 99.999, etc.
  totalPercentage = Math.round(totalPercentage * 100) / 100;
  // Optional: cap at 100% (if you want max 100 shown)
  if (totalPercentage > 100) {
    totalPercentage = 100;
  }

  $("#total_ctc_amount").val(totalAmount);

  $("#total_ctc_percentage").val(totalPercentage);
}

async function getReportingPerson(company_id, selectedLevel) {
  try {
    if (!selectedLevel) {
      $("#reporting_person")
        .empty()
        .append('<option value="">Select Reporting Person</option>');

      return;
    }

    const response = await $.ajax({
      url: "api/staff_creation/get_reporting_person.php",
      type: "POST",
      dataType: "json",
      data: {
        company_id: company_id,
        designation_level: selectedLevel,
      },
    });

    let option = '<option value="">Select Reporting Person</option>';

    $.each(response, function (index, value) {
      option += `
                <option value="${value.id}" data-type="${value.designation}">
                    ${value.staff_name} (${value.designation})
                </option>
            `;
    });

    $("#reporting_person").empty().append(option);
  } catch (error) {
    console.error("Error loading Reporting Person:", error);
  }
}

async function editStaffProfile(id) {
  try {
    const response = await $.post(
      "api/staff_creation/staff_profile_data.php",
      { id: id },
      null,
      "json",
    );

    if (!response || response.length === 0) {
      console.error("No customer data returned.");
      return;
    }
    $(".staff_content").show();
    $("#clear_staff").hide();
    const data = response.staff;
    const ctcData = response.ctc;
    $(".personal_info_disble").attr("disabled", false);
    $("#submit_staff_creation").attr("disabled", false);
    $("#staff_profile_id").val(id);
    $("#staff_auto_id").val(data.staff_id);
    await getCompanyName("#company_name");
    $("#company_name").val(data.company_id);
    $("#company_name").trigger("change");

    /* Load dependent dropdowns */
    await autoGenStaffId(data.staff_id, data.company_id);
    await getBranchList(data.company_id, "#branch_name,#branch");
    await getDepartmentList(data.company_id, "#department", data.department);
    await getDesignationList(data.company_id, data.designation);
    await getShiftList(data.company_id);
    await getCTCInfoTable(data.company_id);
    await getCompanyPFDetails(data.company_id);

    $("#staff_name").val(data.staff_name);
    $("#address").val(data.address);
    await getStateList();
    $("#state").val(data.state);
    await getDistrictList(data.state);
    $("#place").val(data.place);
    $("#district").val(data.district);
    $("#pincode").val(data.pincode);
    $("#dob").val(data.dob);
    $("#age").val(data.age);
    $("#blood_group").val(data.blood_group);
    $("#gender").val(data.gender);
    $("#marital_status").val(data.marital_status);
    $("#spouse_name").val(data.spouse_name);
    $("#anniversary_date").val(data.anniversary_date);
    $("#joining_date").val(data.joining_date);
    $("#relieve_date").val(data.relieve_date);
    $("#notice_period").val(data.notice_period);
    $("#mobile2").val(data.mobile2);
    $("#whatsapp").val(data.whatsapp);
    $("#mobile1").val(data.mobile1);
    $("#mailid").val(data.email);
    $("#instagram").val(data.instagram);
    $("#facebook").val(data.facebook);
    $("#acc_holder_name").val(data.acc_holder_name);
    $("#bank_name").val(data.bank_name);
    $("#acc_number").val(data.acc_number);
    $("#ifsc_code").val(data.ifsc_code);
    $("#bank_branch").val(data.bank_branch);
    $("#branch_name").val(data.branch_id);
    $("#branch").val(data.branch);
    $("#department").val(data.department);
    $("#designation").val(data.designation);
    $("#off_type").val(data.off_type);
    $("#relieve_date").val(data.relieve_date);

    $("#branch_admin").val(moneyFormatIndia(data.branch_admin));
    $("#pf_available").val(data.pf_available);
    $("#esi_available").val(data.esi_available);
    $("#pt_available").val(data.pt_available);
    $("#total_ctc").val(parseFloat(data.total_ctc));
    $("#annual_ctc").val(moneyFormatIndia(data.annual_ctc));
    $("#shift").val(data.shift);
    $("#ot_payment").val(data.ot_payment);
    $("#ot_per_day").val(data.ot_per_day);

    let selectedLevel = parseInt(
      $("#designation option:selected").data("level"),
    );

    await getTeamList(data.department, data.team);

    /* then set selected team */
    $("#team").val(data.team);

    await getReportingPerson(data.company_id, selectedLevel);

    $("#reporting_person").val(data.reporting_person);

    await getDocumentInfoTable();
    await getFamilyInfoTable();
    await getQualificationInfoTable();
    await getExperienceInfoTable();

    $("#marital_status").trigger("change");

    $("#branch_admin").trigger("change");
    toggleOTField();
    let path = "uploads/staff_creation/staff_pic/";
    $("#per_pic").val(data.pic);
    $("#imgshow").attr("src", path + data.pic);
    // Disable editing
    let totalAmt = 0;
    let totalPer = 0;

    $.each(ctcData, function (index, row) {
      $("#ctc_amount_" + row.ctc_id).val(moneyFormatIndia(row.ctc_amount));
      $("#ctc_percentage_" + row.ctc_id).val(row.ctc_percentage);

      totalAmt += parseFloat(row.ctc_amount || 0);
      totalPer += parseFloat(row.ctc_percentage || 0);
    });

    $("#total_ctc_amount").val(moneyFormatIndia(totalAmt));
    $("#total_ctc_percentage").val(totalPer);

    enableEditMode();
  } catch (error) {
    console.error("Error in editStaffProfile:", error);
  }
}
function enableEditMode() {
  /* Hide Next Button */
  $("#submit_staff").hide();
  $("#add_experience").show();
  $("#add_qualification").show();
  $("#add_family").show();
  $("#add_document").show();

  /* Company Name Readonly / Disable */
  $("#company_name").prop("disabled", true);

  /* Occupation Card Fields Readonly */
  $("#branch_name").prop("disabled", true);
  $("#department").prop("disabled", true);
  $("#team").prop("disabled", true);
  $("#designation").prop("disabled", true);
  $("#off_type").prop("disabled", true);
  $("#reporting_person").prop("disabled", true);
  $("#branch_admin").prop("disabled", true);
  $("#branch").prop("disabled", true);
  // $("#pf_available").prop("disabled", true);
  // $("#esi_available").prop("disabled", true);
  // $("#pt_available").prop("disabled", true);

  /* CTC Card Fields Readonly */
  $("#total_ctc").prop("readonly", true);
  $("#annual_ctc").prop("readonly", true);
  $("#shift").prop("disabled", true);
  $("#ot_payment").prop("disabled", true);
  $("#ot_per_day").prop("readonly", true);

  /* CTC Table Fields Readonly */
  $(".ctc_amount").prop("readonly", true);
  $(".ctc_percentage").prop("readonly", true);

  let status = $('input[name="staff_status"]:checked').val(); // 1 = Inactive, 2 = Active
  if (status == "2") {
    $(".personal_info_disble").attr("disabled", true);
    $("#submit_staff_creation").hide();
    $("#clear_staff").hide();
    $("#add_experience").hide();
    $("#add_qualification").hide();
    $("#add_family").hide();
    $("#add_document").hide();
  }
}

function clearStaffProfileForm() {
  // Clear input fields except those with IDs 'loan_id_calc' and 'loan_date_calc'
  $("#staff_creation")
    .find("input")
    .each(function () {
      let id = $(this).attr("id");
      $(".personal_info_disble").val("");
      $("#staff_profile_id").val("");
      $("#per_pic").val("");
      $("#submit_staff").attr("disabled", false);

      $("#staff_creation input").css("border", "1px solid #cecece");
      $("#staff_creation select").css("border", "1px solid #cecece");
      $("#staff_creation").find('input[type="radio"]').prop("checked", false);
    });
  $("#staff_creation").find('input[type="radio"]').prop("checked", false);

  // Clear all textarea fields within the specific form
  $("#staff_creation").find("textarea").val("");

  //clear all upload inputs within the form.
  $("#staff_creation").find('input[type="file"]').val("");

  // Reset all select fields within the specific form
  $("#staff_creation")
    .find("select")
    .each(function () {
      let selectid = $(this).attr("id");
      if (selectid != "gender") {
        $(this).val($(this).find("option:first").val());
      }
    });

  //Reset all  images within the form
  $("#imgshow").attr("src", "img/avatar.png");
}

function resetStaffData() {
  $("#submit_staff").show();
  $("#company_name").prop("disabled", false);
  $("#staff_auto_id").val("");
  $(".personal_info_disble").attr("disabled", false);
  /* Occupation Card Fields */
  $("#branch_name").prop("disabled", false);
  $("#department").prop("disabled", false);
  $("#team").prop("disabled", false);
  $("#designation").prop("disabled", false);
  $("#off_type").prop("disabled", false);
  $("#reporting_person").prop("disabled", false);
  $("#branch_admin").prop("disabled", false);
  $("#branch").prop("disabled", false);
  // $("#pf_available").prop("disabled", false);
  // $("#esi_available").prop("disabled", false);
  // $("#pt_available").prop("disabled", false);

  /* CTC Card Fields */
  $("#total_ctc").prop("readonly", false).val("");
  $("#annual_ctc").prop("readonly", true).val("");
  $("#shift").prop("disabled", false);
  $("#ot_payment").prop("disabled", false);
  $("#ot_per_day").prop("readonly", false).val("");

  /* =========================
       RESET CTC TABLE VALUES
    ========================= */
  $(".ctc_amount").prop("readonly", false).val("");
  $(".ctc_percentage").prop("readonly", false).val("");

  $("#total_ctc_amount").val("");
  $("#total_ctc_percentage").val("");
  $("#submit_staff_creation").show();
  $("#clear_staff").show();
  $("#add_experience").show();
  $("#add_qualification").show();
  $("#add_family").show();
  $("#add_document").show();

  $(".spouse-div").hide();
  $(".branch_div").hide();
  $(".ot_per_day_div").hide();
}
