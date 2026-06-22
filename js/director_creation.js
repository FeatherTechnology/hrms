$(document).ready(function () {
  /* --- Add Company Button & Back Button Click --- */
  $(document).on("click", "#add_director,#back_btn", function () {
    swapTableAndCreation();
  });

  // edit button click event
  $(document).on("click", ".directorActionBtn", async function () {
    var id = $(this).attr("value");
    await swapTableAndCreation();
    await getDirectorDetail(id);
  });

  // state on change to get the district
  $("#state").change(function () {
    getDistrictList($(this).val());
  });

  /* --- Submit Director Creation --- */
  $("#submit_director_creation").click(function () {
    event.preventDefault();
    //Validation
    let director_id = $("#director_id").val();
    let director_name = $("#director_name").val();
    let state = $("#state").val();
    let district = $("#district").val();
    let address = $("#address").val();
    let mobile_number = $("#mobile_number").val();
    let directorID = $("#directorID").val();

    var data = [
      "director_name",
      "state",
      "district",
      "address",
      "mobile_number",
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
        "Do you want to submit this Director Creation?",
        function () {
          $.post(
            "api/director_creation_files/submit_director_creation.php",
            {
              director_id,
              director_name,
              state,
              district,
              address,
              mobile_number,
              directorID,
            },
            function (response) {
              if (response == "2") {
                swalSuccess("Success", "Director Added Successfully!");
              } else if (response == "1") {
                swalSuccess("Success", "Director Updated Successfully!");
              } else {
                swalError("Error", "Error Occurs!");
              }
              $(".adddirectorbtn").show();
              $(".director_table_content").show();
              $("#director_creation_content").hide();
              getDirectorList();
            },
          );
        },
      );
    }
  });
});
// document end
// function start
$(function () {
  getDirectorList();
});

// to get the director list
function getDirectorList() {
  $.post(
    "api/director_creation_files/direction_creation_list.php",
    function (response) {
      var columnMapping = [
        "sno",
        "director_id",
        "director_name",
        "state",
        "district",
        "address",
        "mobile_number",
        "action",
      ];
      appendDataToTable("#director_creation", response, columnMapping);
      setdtable("#director_creation", "Director Creation List");
    },
    "json",
  );
}

// to get the state list
async function getStateList() {
  let response = await $.ajax({
    url: "api/common_files/get_state_list.php",
    type: "POST",
    dataType: "json",
  });

  let appendStateOption = "<option value=''>Select State</option>";

  $.each(response, function (index, val) {
    appendStateOption +=
      "<option value='" + val.id + "'>" + val.state_name + "</option>";
  });

  $("#state").empty().append(appendStateOption);
}

// to get the district list
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

// to get auto id
async function getAutoId(id) {
  try {
    let response = await $.ajax({
      url: "api/director_creation_files/get_auto_increment_id.php",
      type: "POST",
      data: { id: id },
      dataType: "json",
      cache: false,
    });

    $("#director_id").val(response.director_id);
  } catch (error) {
    console.error("AJAX Error:", error);
  }
}

// to get the director details when we click edit
async function getDirectorDetail(id) {
  let response = await $.ajax({
    url: "api/director_creation_files/get_director_details.php",
    type: "POST",
    data: { id: id },
    dataType: "json",
  });

  $("#directorID").val(response.id);
  $("#director_id").val(response.director_id);
  $("#director_name").val(response.director_name);

  $("#state").val(response.state);

  await getDistrictList(response.state);

  $("#district").val(response.district);

  $("#address").val(response.address);
  $("#mobile_number").val(response.mobile_number);
}

// when we click back and add button this function call 
async function swapTableAndCreation() {
  if ($("#director_creation_content").is(":visible")) {
    $("#director_creation_content").hide();
    $(".adddirectorbtn").show();
    $(".director_table_content").show();
    $(".backBtn").hide();
    

  } else {

    await getDirectorList();
    getAutoId("");
    $( "#director_name,#state,#district,#address,#mobile_number,#directorID",).val("");
    $("#director_name,#state,#district,#address,#mobile_number").css("border","1px solid #cecece",);
    $("#director_creation_content").show();
    $(".director_table_content").hide();
    $(".backBtn").show();
    $(".adddirectorbtn").hide();
    await getStateList();
  }
}
