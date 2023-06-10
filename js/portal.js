var nextWindowID = 1;

function SetLoadingIcon(selectedClass) {
  $(selectedClass).html("<div class='loadingicon1'><img src='img/loader2.gif'/></div>");
}

function CreatePopup() {
  activeWindows.push();
  var div = $("<div>").addClass("popupframe-" + nextWindowID);
  div.addClass$("<div>").addClass("popupwindowframe");
  nextWindowID++;
}

function CloseRightPanel() {
  $('#rightslide').animate({ width: 0 }, 300, function() {
    $(this).hide();
  });
  var pWidth = $(window).width();
  $('#pagewrap').animate({ width: pWidth }, 300);
}

function OpenRightPanel() {
  var slideWidth = 600; // Maximum width of #rightslide
  var pagewrapMinWidth = 700; // Minimum width of #pagewrap

  $('#rightslide').show().animate({ width: slideWidth }, 300);
  var pagewrapWidth = $(window).width() - slideWidth;
  if (pagewrapWidth < pagewrapMinWidth) {
      pagewrapWidth = pagewrapMinWidth;
  }
  $('#pagewrap').animate({ width: pagewrapWidth }, 300);
}

function UpdateSelectedMenuItem(menuItem) {
  $('.leftpanebutton').each(function() {
    if ($(this).find('.buttonid').html() == menuItem) {
      $(this).addClass('textcolor_green');
      $(this).removeClass('textcolor_white');
    }
    else {
      $(this).removeClass('textcolor_green');
      $(this).addClass('textcolor_white');
    }
  });
}

function CheckSessionAjax(callback) {
  var requestData = [
    { name: 'action', value: 'CheckSession' }
  ];

  AjaxCall(requestData, function(status, response) {
    callback(status, response);
  });
}

function InitPortal(get_Page) {
  var requestData = [
    {name: 'action', value: 'InitPortal'},
    {name: 'page', value: get_Page}
  ];

  if (get_Page == "Accounts") {
    var searchParams = new URLSearchParams(window.location.search);
    var get_currentPage = searchParams.get('currentPage');

    requestData.push({name: 'currentPage', value: get_currentPage})
  }

  AjaxCall(requestData, function(status, response) {
    if (status) {
      $("#pagewrap").html(response);
      UpdateSelectedMenuItem(get_Page);
    }
  });
}

function InitLogin() {
  var requestData = [
    {name: 'action', value: 'InitLogin'}
  ];
  
  AjaxCall(requestData, function(status, response) {
    if (status) {
      $("#pagewrap").html(response);
    }
  });
}

function CheckLogin(_email, _password) {
  var requestData = [
    {name: 'action', value: 'CheckLogin'},
    {name: 'email', value: _email},
    {name: 'password', value: _password}
  ];
  AjaxCall(requestData, function(status, response) {
    if (status) {
      //$("#pagewrap").html(response);
      location.reload();
    }
  });
}

function ClickLeftPaneMenuItem(buttonid) {
  var requestData = [
    {name: 'action', value: 'LeftPaneButtonClick'},
    {name: 'buttonid', value: buttonid}
  ];

  if (buttonid == "Accounts") {
    var searchParams = new URLSearchParams(window.location.search);
    var get_currentPage = searchParams.get('currentPage');
    requestData.push({name: 'currentPage', value: get_currentPage})
  }

  UpdateSelectedMenuItem(buttonid);
  $("#rightpane_container").html("<div class='loadingicon1'><img src='img/loader2.gif'/></div>");

  AjaxCall(requestData, function(status, response) {
    if (status) {
      $("#rightpane_container").html(response);
    }
  });
}

$(document).ready(function() {
  $(".popup_darken").hide();
  $(".popup_wrapper").hide();

  $("#rightslide").hide();
  $("#pagewrap").width('100%');

  $(window).resize(function () {
    var rightSlideVisible = $("#rightslide").is(':visible');
    if (rightSlideVisible) {
      $("#pagewrap").width(Math.max(700, $(window).width()-600));
    }
    else {
      $("#pagewrap").width($(window).width());
    }
  });
  
  CheckSessionAjax(function(status, response) {
    if (status)
    {
      if (response === "true") {
        var searchParams = new URLSearchParams(window.location.search);
        var get_Page = searchParams.get('page');

        if (!get_Page) {
          get_Page = "Dashboard";
        }

        InitPortal(get_Page);
      }
      else {
        InitLogin();
      }
    }
  });

  // Button Handlers

  $(document).on('click', '.input_login_button', function() {
    var loginEmail = $('.input_login_email').val();
    var loginPassword = $('.input_login_password').val();
    CheckLogin(loginEmail, loginPassword);
  });

  $(document).on('click', '.leftpanebutton', function() {
    var buttonid = $(this).find('.buttonid').html();
    ClickLeftPaneMenuItem(buttonid);
  });

  $(document).on('click', '.viewaccounts_pageright', function() {
    var searchParams = new URLSearchParams(window.location.search);
    var get_currentPage = searchParams.get('currentPage');

    if (get_currentPage.length <= 0) {
      get_currentPage = 0;
    }

    var requestData = [
      {name: 'action', value: 'VAPageRight'},
      {name: 'currentPage', value: get_currentPage}
    ];

    $("#rightpane_viewport").html("<div class='loadingicon1'><img src='img/loader2.gif'/></div>");
    $("#rightpane_footer").html("");

    AjaxCall(requestData, function(status, response) {
      if (status) {
        $("#rightpane_container").html(response);
      }
    });
  });

  $(document).on('click', '.viewaccounts_pageleft', function() {
    var searchParams = new URLSearchParams(window.location.search);
    var get_currentPage = searchParams.get('currentPage');

    if (get_currentPage.length <= 0) {
      get_currentPage = 0;
    }

    var requestData = [
      {name: 'action', value: 'VAPageLeft'},
      {name: 'currentPage', value: get_currentPage}
    ];

    $("#rightpane_viewport").html("<div class='loadingicon1'><img src='img/loader2.gif'/></div>");
    $("#rightpane_footer").html("");

    AjaxCall(requestData, function(status, response) {
      if (status) {
        $("#rightpane_container").html(response);
      }
    });
  });

  $(document).on('click', '.btn_temp', function() {
    $('.popup_darken').fadeOut(500);
    $('.popup_wrapper').fadeOut(500);
  });
});