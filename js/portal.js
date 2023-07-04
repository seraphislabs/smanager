var nextWindowID = 1;
var xhrArray = [];
var settingsMenuOpen = false;
var tooltipTimer;
var tooltipText;

$(window).on("popstate", function (event) {
  ClosePopup();
  SetLoadingIcon("#rightpane_container");
  StartPortal();
});

function ClosePopup() {
  $(".popup_darken").fadeOut(400);
  $(".popup_wrapper").fadeOut(400);
}

// To cancel all AJAX calls
function CancelAllAjaxCalls() {
  for (var i = 0; i < xhrArray.length; i++) {
    xhrArray[i].abort();
  }

  // Clear the array
  xhrArray = [];
}

function SetLoadingIcon(selectedClass) {
  $(selectedClass).html(
    "<div class='loadingicon1'><img src='img/loader2.gif'/></div>"
  );
}

function SetLoadingIconSmall(selectedClass) {
  $(selectedClass).html(
    "<img src='img/loader2.gif' width='30px' height='30px'/>"
  );
}

function UpdateSelectedMenuItem(menuItem) {
  $(".leftpanebutton").each(function () {
    if ($(this).data("pageid") == menuItem) {
      $(this).css("color", "#14A76C");
    } else {
      $(this).css("color", "white");
    }
  });
}

function StartSession(_email, _password) {
  var requestData = [
    { name: "action", value: "StartSession" },
    { name: "email", value: _email },
    { name: "password", value: _password },
  ];
  Action_StartSession(requestData);
}

function ClickLeftPaneMenuItem(pageid, pushHistory) {
  UpdateSelectedMenuItem(pageid);
  SetLoadingIcon("#rightpane_container");

  var requestData = [
    { name: "action", value: "LoadPage" },
    { name: "pageid", value: pageid },
  ];

  Action_LoadPage(requestData);
}

function Logout() {
  var requestData = [{ name: "action", value: "Logout" }];
  AjaxCall(requestData, function (status, response) {
    if (status) {
      $("#pagewrap_master").html(response);
      location.reload();
    }
  });
}

function StartPortal() {
  var urlParams = JSON.stringify(GetURLParameters());

  if (!urlParams.hasOwnProperty("page")) {
    urlParams["page"] = "Dashboard";
  }

  UpdateSelectedMenuItem(urlParams["page"]);

  var requestData = {
    action: "StartPortal",
    pagedata: urlParams,
  };
  AjaxCall(requestData, function (status, response) {
    if (status) {
      $("#pagewrap").html(response);
    }
  });
}

function HideTooltip() {
  tooltipText = "";
  clearTimeout(tooltipTimer);
  $(".tooltip").hide();
}

function ShowTooltip(text) {
  $(".tooltip").html(text).show();
}

$(document).ready(function () {
  $(".popup_darken").hide();
  $(".popup_wrapper").hide();

  StartPortal();

  $(document).on("mouseenter", ".tooltip_trigger", function () {
    tooltipText = $(this).children(".mytooltip").html();
    tooltipTimer = setTimeout(function () {
      ShowTooltip(tooltipText);
    }, 150);
  });

  $(document).on("mouseleave", ".tooltip_trigger", function () {
    HideTooltip();
  });

  $(document).on("mousemove", function (e) {
    var tooltip = $(".tooltip");
    tooltip.css({
      top: e.clientY + 20, // Adjust the offset to your liking
      left: e.clientX + 20, // Adjust the offset to your liking
    });
  });

  // Button Handlers
  $(document).on("click", ".leftpanebutton", function () {
    var pageid = $(this).data("pageid");
    ClickLeftPaneMenuItem(pageid, true);
  });

  $(document).on("click", "#logoutbutton", function () {
    Logout();
  });

  $(document).on("click", function (event) {
    if (settingsMenuOpen) {
      if (!$(event.target).closest(".open_settings_page").length) {
        $(".settingsmenu_container").fadeOut(200, function () {
          $(this).html("");
        });
        settingsMenuOpen = false;
      }
    }
  });

  $(document).on("click", ".open_settings_page", function () {
    if (!settingsMenuOpen) {
      var requestData = [{ name: "action", value: "OpenSettingsMenu" }];
      Action_OpenSettingsMenu(requestData);
    } else {
      $(".settingsmenu_container").hide().html("");
      settingsMenuOpen = false;
    }
  });

  $(document).on("click", ".btn_punch_in", function () {
    Action_PunchIn();
  });
});
