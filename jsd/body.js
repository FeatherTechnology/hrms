let notificationSummary = [];
$(document).ready(function () {
  $.post(
    "api/base_api/getSessionData.php",
    function (response) {
      if (response != null) {
        $("#search_screens").click(function () {
          let search_input = $("#search_input_").val();
          if (search_input != "") {
            $.post(
              "api/base_api/getScreens.php",
              { search_input },
              function (response) {
                let append = "";
                $.each(response, function (index, val) {
                  if (val.display_name != undefined) {
                    append +=
                      "<li class='dropdown-contents'><a href='" +
                      val.module_name +
                      "'>" +
                      val.display_name +
                      "</a></li>";
                  }
                });
                $("#search_ul").empty().append(append);
              },
              "json",
            );
          }
        });

        getPageHeaderName();
        getHeaderUserName();
        getBodyContentPage();
        getNotifications();
      } else {
        const current_page = localStorage.getItem("currentPage");

        if (current_page != "index.php" && current_page != "") {
          window.location.href = "index.php";
        }
      }
    },
    "json",
  );

 $(document).on("mouseenter", ".notification-row", function (e) {

  let module = $(this).data("module");

  let filteredData = notificationSummary.filter(item => item.module == module);

  let html = `
    <table class="notification-table">
        <thead>
            <tr>
                <th>Request Type</th>
                <th>Today</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
  `;

if (filteredData.length) {

  $.each(filteredData, function (i, row) {

    // ❗ BLOCK FEEDBACK INSIDE HOVER
    if (row.module !== "Regularization") {
      return; // skip anything not regularization
    }

    html += `
      <tr>
          <td>${row.request_type}</td>
          <td class="text-center">${row.today_count}</td>
          <td class="text-center">${row.total_count}</td>
      </tr>
    `;
  });

} else {

    html += `
      <tr>
          <td colspan="3" style="text-align:center">
              No Data Found
          </td>
      </tr>
    `;
  }

  html += `
        </tbody>
    </table>
  `;

  $("#notificationTooltip")
    .appendTo("body")
    .html(html)
    .css({
      display: "block",
      top: e.clientY + "px",
      left: e.clientX + 15 + "px",
    });

});

  $(document).on("mouseleave", ".notification-row", function () {
    $("#notificationTooltip").hide();
  });
});

function getPageHeaderName() {
  $.post(
    "api/base_api/getPageHeaderName.php",
    { current_page: localStorage.getItem("currentPage") },
    function (response) {
      if (response.length != 0) {
        $("#pageHeaderName").text(" - " + response.sub_menu);
      }
    },
    "json",
  );
}

function getHeaderUserName() {
  $.post(
    "api/base_api/getHeaderUserName.php",
    function (response) {
      $(".show-username").html(response.user_name);
    },
    "json",
  );
}

function getBodyContentPage() {
  const current_page = localStorage.getItem("currentPage");
  $.post(
    "api/base_api/getBodyContentPage.php",
    { current_page: current_page },
    function (response) {
      $("#main-container").html(response);
      if ($("#main-container").find(".error").length > 0) {
        //removed the page header name when 404 called
        $("#pageHeaderName").text("");
      }
      $.post(
        "api/base_api/getCurrentJs.php",
        { current_page: current_page },
        function (response) {
          $("footer").append(
            `<script type="text/javascript" src="` + response + `"></script>`,
          );
        },
      );
    },
  );
}

function getNotifications() {

    $.post(
        "api/base_api/getNotifications.php",
        function (response) {

            notificationSummary = response;

            let moduleTotals = {};

            // STEP 1: Group by module
            $.each(response, function (i, item) {

                if (!moduleTotals[item.module]) {
                    moduleTotals[item.module] = {
                        today: 0,
                        total: 0
                    };
                }

                moduleTotals[item.module].today += parseInt(item.today_count);
                moduleTotals[item.module].total += parseInt(item.total_count);
            });

            // STEP 2: Build HTML dynamically
            let html = `
                <table class="notification-table">
                    <thead>
                        <tr>
                            <th style="width:50%">Type</th>
                            <th style="width:25%">Today</th>
                            <th style="width:25%">Total</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            let totalNotification = 0;

            $.each(moduleTotals, function (module, data) {

                let label = (module === "Regularization") ? "Regularization" : "My Feedback";

                html += `
                    <tr class="notification-row" data-module="${module}">
                        <td>${label}</td>
                        <td class="text-center">${data.today}</td>
                        <td class="text-center">${data.total}</td>
                    </tr>
                `;

                totalNotification += data.total;
            });

            html += `
                    </tbody>
                </table>
            `;

            $(".header-notifications").html(html);

            $(".count-label").text(totalNotification);

            if (totalNotification == 0)
                $(".count-label").hide();
            else
                $(".count-label").show();

        },
        "json"
    );
}

// let notificationStaff = {};

// function loadNotificationStaff() {
//   $.post(
//     "api/base_api/getNotificationStaff.php",
//     function (response) {
//       notificationStaff = {};

//       $.each(response, function (i, item) {
//         if (!notificationStaff[item.req_type]) {
//           notificationStaff[item.req_type] = [];
//         }

//         notificationStaff[item.req_type].push({
//           staff_id: item.staff_id,
//           staff_name: item.staff_name,
//         });
//       });
//     },
//     "json",
//   );
// }
