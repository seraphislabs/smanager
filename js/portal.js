function CheckSessionAjax(callback) {
  var requestData = [
    { name: 'action', value: 'CheckSession' }
  ];

  AjaxCall(requestData, function(status, response) {
    callback(status, response);
  });
}

function InitPortal() {
  var requestData = [
    {name: 'action', value: 'InitPortal'}
  ];
  AjaxCall(requestData, function(status, response) {
    if (status) {
      $("#pagewrap").html(response);
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

$(document).ready(function() {
  CheckSessionAjax(function(status, response) {
    if (status)
    {
      if (response === "true") {
        InitPortal();
      }
      else {
        InitLogin();
      }
    }
  });

  $('body').on('click', '.input_login_button', function() {
    var loginEmail = $('.input_login_email').val();
    var loginPassword = $('.input_login_password').val();
    CheckLogin(loginEmail, loginPassword);
  });
});