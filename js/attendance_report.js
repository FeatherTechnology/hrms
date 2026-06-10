$(document).ready(function () {
  // company Name change to get branch list
  $("#cmpy_name").change(function () {
    let cmpy_id = $(this).val();
    if (cmpy_id == "") {
      $("#branch_name").val("");
      $("#dep_name").val("");
    } else {
      getBranchList(cmpy_id);
      getDepartmentList(cmpy_id);
      $("#stf_name").val("");
    }
  });

// department Name change to get staff list
  $("#dep_name").change(function () {
    let dep_name = $(this).val();
    let cmpy_id = $("#cmpy_name").val();
    let branch_name = $("#branch_name").val();
    getstaffList(cmpy_id, dep_name, branch_name);
  });

// toget the attendance list
  $("#submit_search").click(function () {
    let company_id = $("#cmpy_name").val();
    let branch_id = $("#branch_name").val();
    let dep_name = $("#dep_name").val();
    let date = $("#date").val();
    let stf_name = $("#stf_name").val();
    if (
      company_id == "" ||
      branch_id == "" ||
      dep_name == "" ||
      date == "" ||
      stf_name == ""
    ) {
      swalError("Error", "Please Filled The Manditaory Feild");
    } else {
      $(".attendance_report").show();
      getAttendanceList();
    }
  });

});

// initial load
$(function () {
  getCompanyList();
});

// function start
// to get the company list
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

// to get the department list
function getDepartmentList(cmpy_id) {
  $.post(
    "api/attendance_files/get_department_list.php",
    { cmpy_id },
    function (response) {
      $("#dep_name").empty();
      $("#dep_name").append("<option value=''>Select Department Name</option>");

      $.each(response, function (index, val) {
        $("#dep_name").append(
          "<option value='" +
            val["id"] +
            "'>" +
            val["department_name"] +
            "</option>",
        );
      });
    },
    "json",
  );
}

// to get the staff list
function getstaffList(cmpy_id, dep_name, branch_name) {
  $.post(
    "api/attendance_files/get_staff_list.php",
    { cmpy_id, dep_name, branch_name },
    function (response) {
      $("#stf_name").empty();
      $("#stf_name").append("<option value=''>Select Staff Name</option>");
      if (response.length > 0) {
        $("#stf_name").append("<option value='0'>All</option>");

        $.each(response, function (index, val) {
          $("#stf_name").append(
            "<option value='" +
              val["id"] +
              "'>" +
              val["staff_name"] +
              "</option>",
          );
        });
      }
    },
    "json",
  );
}

// to get the attendance list
function getAttendanceList() {

  let month = $("#date").val();

  if (!month) return;

  let dateObj = new Date(month + "-01");
  let days = new Date(dateObj.getFullYear(), dateObj.getMonth() + 1, 0).getDate();

  let thead = `
    <tr>
      <th rowspan="2">Staff Name</th>
      <th colspan="${days}">
        ${dateObj.toLocaleString('default', { month: 'long', year: 'numeric' })}
      </th>
    </tr>
    <tr>
  `;

  for (let i = 1; i <= days; i++) {
    thead += `<th>${i}</th>`;
  }

  thead += `</tr>`;

  $("#att_head").html(thead);

  if ($.fn.DataTable.isDataTable("#attendance_table")) {
    $("#attendance_table").DataTable().clear().destroy();
  }

  getUserAccess(function (downloadAccess) {

    let buttons = [];

    if (downloadAccess === 1) {
      buttons.push({
        extend: "excelHtml5",
        title: "Attendance Report"
      });
    }

    buttons.push({ extend: "colvis" });

$("#attendance_table").DataTable({

  order: [[0, "asc"]],
  processing: true,
  serverSide: true,

  ajax: {
    url: "api/attendance_report_files/get_attendance_report.php",
    type: "POST",
    data: function (data) {
      data.company_id = $("#cmpy_name").val();
      data.branch_id = $("#branch_name").val();
      data.department = $("#dep_name").val();
      data.staff_id = $("#stf_name").val();
      data.month = $("#date").val();
    }
  },

  columns: buildColumns(days),

  dom: "lBfrtip",
  buttons: buttons,
  lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
});

  });
}
function buildColumns(days) {

  let cols = [
    { data: "staff_name" }
  ];

  for (let i = 1; i <= days; i++) {
    cols.push({
      data: "d" + i,
      orderable: false,
      searchable: false
    });
  }

  return cols;
}
