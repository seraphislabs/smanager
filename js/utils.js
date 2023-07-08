function AjaxCall(data, callback, addToCancelList) {
  CancelAllAjaxCalls();
  var xhr = $.ajax({
    url: "engine/engine.php",
    type: "POST",
    contentType: "application/x-www-form-urlencoded",
    data: data,
    success: function (response) {
      callback(true, response);
    },
    error: function (xhr, status, error) {
      callback(false, error);
    },
  });

  if (addToCancelList) {
    xhrArray.push(xhr);
  }

  return false;
}

function GetURLParameters() {
  var params = {};
  var queryString = window.location.search.slice(1); // Remove the '?' at the start of the string
  var paramArray = queryString.split("&"); // Split the query string into its component parts

  for (var i = 0; i < paramArray.length; i++) {
    var paramPart = paramArray[i].split("="); // Split each part into an array of [key, value]

    if (paramPart[1] === undefined) {
      // If there was no '=', add an empty value
      paramPart[1] = "";
    }

    params[decodeURIComponent(paramPart[0])] = decodeURIComponent(paramPart[1]);
    // Use decodeURIComponent to get a human-readable string, and add it to our final object
  }

  return params;
}

function InitDatePickers() {
  $(".formsection_datepicker").each(function () {
    $(this).datepicker({
      dateFormat: "mm/yy",
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
    });
  });
  $(".formsection_datepicker_full").each(function () {
    $(this).datepicker({
      dateFormat: "mm/dd/yy",
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
    });
  });
}

function InitInputMasks() {
  $(".formsection_date_full_mask").each(function () {
    $(this).mask("00/00/0000");
  });
  $(".formsection_date_my_mask").each(function () {
    $(this).mask("00/0000");
  });
  $(".formsection_phone_mask").each(function () {
    $(this).mask("(000) 000-0000");
  });
}

function InitTimePickers() {
  $(".formsection_input_timepicker").each(function () {
    var dTime = $(this).data("defaulttime");
    $(this).timepicker({
      timeFormat: "h:mm p",
      interval: 1,
      minTime: "00:00am",
      maxTime: "11:49pm",
      defaultTime: dTime,
      startTime: "00:00am",
      dynamic: false,
      dropdown: false,
    });
  });
}
