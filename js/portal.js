var nextWindowID = 1;
var xhrArray = [];
var settingsMenuOpen = false;

$(window).on('popstate', function(event) {
  ClosePopup();
  SetLoadingIcon("#rightpane_container");
  StartPortal();
});

function ClosePopup() {
  $('.popup_darken').fadeOut(400);
  $('.popup_wrapper').fadeOut(400);
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
  $(selectedClass).html("<div class='loadingicon1'><img src='img/loader2.gif'/></div>");
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

function StartSession(_email, _password) {
  var requestData = [
    {name: 'action', value: 'StartSession'},
    {name: 'email', value: _email},
    {name: 'password', value: _password}
  ];
  CancelAllAjaxCalls();
  AjaxCall(xhrArray, requestData, function(status, response) {
    if (status) {
      location.reload();
    }
  });
}

function ClickLeftPaneMenuItem(buttonid, pushHistory) {
  var requestData = [
    {name: 'action', value: 'LeftPaneButtonClick'},
    {name: 'buttonid', value: buttonid}
  ];

  if (buttonid == "Accounts") {
    var searchParams = new URLSearchParams(window.location.search);
    var get_currentPage = searchParams.get('currentPage');

    if (pushHistory == true) {
      requestData.push({name: 'currentPage', value: get_currentPage})
    }
  }

  UpdateSelectedMenuItem(buttonid);
  SetLoadingIcon("#rightpane_container");

  CancelAllAjaxCalls();
  AjaxCall(xhrArray, requestData, function(status, response) {
    if (status) {
      $("#rightpane_container").html(response);
    }
  });
}

function Logout() {
  var requestData = [
    {name: 'action', value: 'Logout'}
  ];
  CancelAllAjaxCalls();
  AjaxCall(xhrArray, requestData, function(status, response) {
    if (status) {
      $('#pagewrap_master').html(response);
      location.reload();
    }
  });
}

function StartPortal() {
  var searchParams = new URLSearchParams(window.location.search);
  var urlParams = JSON.stringify(GetURLParameters());

  if (!urlParams.hasOwnProperty('page')) {
    urlParams['page'] = 'Dashboard';
  }

  var requestData = {
    action:'StartPortal',
    pagedata: urlParams
  };
  CancelAllAjaxCalls();
  AjaxCall(xhrArray, requestData, function(status, response) {
    if (status) {
      $('#pagewrap').html(response);
    }
  });
}

$(document).ready(function() {
  $(".popup_darken").hide();
  $(".popup_wrapper").hide();
  
  StartPortal();

  // Button Handlers
  $(document).on('click', '.leftpanebutton', function() {
    var buttonid = $(this).find('.buttonid').html();
    ClickLeftPaneMenuItem(buttonid, true);
  });

  $(document).on('click', '#logoutbutton', function( ) {
    Logout();
  });

  $(document).on('click', function(event) {
    if(settingsMenuOpen) {
      if (!$(event.target).closest('.open_settings_page').length) {
        $('.settingsmenu_container').hide().html("");
        settingsMenuOpen = false;
      }
    }
  });

  $(document).on('click', '.open_settings_page', function() {
    if (!settingsMenuOpen) {
      var requestData = [
        {name: 'action', value: 'OpenSettingsMenu'}
      ];
      CancelAllAjaxCalls();
      AjaxCall(xhrArray, requestData, function(status, response) {
        if (status) {
          $('.settingsmenu_container').html(response).show();
          settingsMenuOpen = true;
        }
      });
    }
    else {
      $('.settingsmenu_container').hide().html("");
      settingsMenuOpen = false;
    }
  });
});