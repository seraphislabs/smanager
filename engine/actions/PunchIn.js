function Action_PunchIn() {
  var requestData = [{ name: "action", value: "PunchIn" }];
  $(".clockin_display").html("");
  $(".clockin_time").html("");
  AjaxCall(requestData, Action_PunchInResponse, false);
}

function Action_PunchInResponse(status, response) {
  if (status) {
    location.reload();
    Action_UpdatePunchDisplay();
  }
}
