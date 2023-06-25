function AjaxCall(_xhrArray, data, callback) {
  var xhr = $.ajax({
    url: 'engine/engine.php',
    type: 'POST',
    contentType: 'application/x-www-form-urlencoded',
    data: data,
    success: function (response) {
      callback(true, response);
    },
    error: function (xhr, status, error) {
      callback(false, error);
    }
  });

  _xhrArray.push(xhr);
}

function SerializeNewAccountForm() {
  var formInformation = {};
  var accountInformation = {};

  var rejectedDivs = [];

  var retBool = true;

  // Populate Account Information
  $("#formsection_newaccount_details").find('.formsection_serialize').each(function () {
    var dataSerialize = $(this).data('serialize');
    if (dataSerialize != "none" && dataSerialize != "undefined") {
      var fieldValue = $(this).val();
      var serializeType = $(this).data('validation');

      if (!ValidateForm(fieldValue, serializeType).success) {
        rejectedDivs.push($(this));
        retBool = false;
      }
      else {
        $(this).removeClass('formsection_validation_error');
      }
      accountInformation[dataSerialize] = fieldValue;
    }
  });

  accountInformation['isServiceAddress'] = $('#additional_service_addresses_btn').prop("checked");

  formInformation['accountInformation'] = accountInformation;


  var billingInformation = {};

  // Populate Billing Information
  $("#formsection_billing_info").find('.formsection_serialize').each(function () {
    var dataSerialize = $(this).data('serialize');
    if (dataSerialize != "none" && dataSerialize != "undefined" && dataSerialize.length > 0) {

      if ($(this).is(':checkbox')) {
        var fieldValue = $(this).prop('checked');
        billingInformation[dataSerialize] = fieldValue;
      }
      else {
        var fieldValue = $(this).val();
        var serializeType = $(this).data('validation');

        if (!ValidateForm(fieldValue, serializeType).success) {
          rejectedDivs.push($(this));
          retBool = false;
        }
        else {
          $(this).removeClass('formsection_validation_error');
        }

        billingInformation[dataSerialize] = fieldValue;
      }
    }
  });

  formInformation['billingInformation'] = billingInformation;

  var locations = [];

  //formsection_locations_list

  $("#formsection_locations_list").find('.formsection_location_entry').each(function () {
    var location = {};

    $(this).find('.formsection_serialize').each(function () {
      var dataSerialize = $(this).data('serialize');
      if (dataSerialize != "none" && dataSerialize != "undefined") {
        if ($(this).is(':checkbox')) {
          var fieldValue = $(this).prop('checked');
          location[dataSerialize] = fieldValue;
        }
        else {
          var fieldValue = $(this).val();
          var serializeType = $(this).data('validation');

          if (!ValidateForm(fieldValue, serializeType).success) {
            rejectedDivs.push($(this));
            retBool = false;
          }
          else {
            $(this).removeClass('formsection_validation_error');
          }

          location[dataSerialize] = fieldValue;
        }
      }
    });

    locations.push(location);
  })

  formInformation['locations'] = locations;

  $.each(rejectedDivs, function (index, item) {
    $(this).addClass('formsection_validation_error');
  });

  var returnInformation = {};
  returnInformation['success'] = retBool;
  returnInformation['formInformation'] = formInformation;

  return returnInformation;
}

function SerializeNewShiftForm() {
  var shiftInformation = {};
  var returnInformation = {};

  var mondayEnabled = $('.formsection_data_checkbox_monday').prop('checked');
  var tuesdayEnabled = $('.formsection_data_checkbox_tuesday').prop('checked');
  var wednesdayEnabled = $('.formsection_data_checkbox_wednesday').prop('checked');
  var thursdayEnabled = $('.formsection_data_checkbox_thursday').prop('checked');
  var fridayEnabled = $('.formsection_data_checkbox_friday').prop('checked');
  var saturdayEnabled = $('.formsection_data_checkbox_saturday').prop('checked');
  var sundayEnabled = $('.formsection_data_checkbox_sunday').prop('checked');

  if (mondayEnabled) {
    var shiftStart = $('.formsection_data_monday_start').val();
    var shiftEnd = $('.formsection_data_monday_end').val();

    if (shiftStart.length > 0 && shiftEnd.length > 0) {
      shiftInformation['monday'] = {
        'start': shiftStart,
        'end': shiftEnd
      };
    }
  }

  if (tuesdayEnabled) {
    var shiftStart = $('.formsection_data_tuesday_start').val();
    var shiftEnd = $('.formsection_data_tuesday_end').val();

    if (shiftStart.length > 0 && shiftEnd.length > 0) {
      shiftInformation['tuesday'] = {
        'start': shiftStart,
        'end': shiftEnd
      };
    }
  }

  if (wednesdayEnabled) {
    var shiftStart = $('.formsection_data_wednesday_start').val();
    var shiftEnd = $('.formsection_data_wednesday_end').val();

    if (shiftStart.length > 0 && shiftEnd.length > 0) {
      shiftInformation['wednesday'] = {
        'start': shiftStart,
        'end': shiftEnd
      };
    }
  }

  if (thursdayEnabled) {
    var shiftStart = $('.formsection_data_thursday_start').val();
    var shiftEnd = $('.formsection_data_thursday_end').val();

    if (shiftStart.length > 0 && shiftEnd.length > 0) {
      shiftInformation['thursday'] = {
        'start': shiftStart,
        'end': shiftEnd
      };
    }
  }

  if (fridayEnabled) {
    var shiftStart = $('.formsection_data_friday_start').val();
    var shiftEnd = $('.formsection_data_friday_end').val();

    if (shiftStart.length > 0 && shiftEnd.length > 0) {
      shiftInformation['friday'] = {
        'start': shiftStart,
        'end': shiftEnd
      };
    }
  }

  if (saturdayEnabled) {
    var shiftStart = $('.formsection_data_saturday_start').val();
    var shiftEnd = $('.formsection_data_saturday_end').val();

    if (shiftStart.length > 0 && shiftEnd.length > 0) {
      shiftInformation['saturday'] = {
        'start': shiftStart,
        'end': shiftEnd
      };
    }
  }

  if (sundayEnabled) {
    var shiftStart = $('.formsection_data_sunday_start').val();
    var shiftEnd = $('.formsection_data_sunday_end').val();

    if (shiftStart.length > 0 && shiftEnd.length > 0) {
      shiftInformation['sunday'] = {
        'start': shiftStart,
        'end': shiftEnd
      };
    }
  }

  shiftInformation['name'] = $('.formsection_data_shift_name').val();

  shiftInformation['id'] = $('.formsection_data_shift_name').data('shiftid');
  returnInformation['shiftInformation'] = shiftInformation;
  returnInformation['success'] = true;

  return returnInformation;
}

function SerializeNewEmployeeForm() {
  var formInformation = {};
  var employeeInformation = {};

  var rejectedDivs = [];

  var retBool = true;

  // Populate Account Information
  $(".popup_scrollable").find('.formsection_serialize').each(function () {
    var dataSerialize = $(this).data('serialize');
    if (dataSerialize != "none" && dataSerialize != "undefined") {
      var fieldValue = $(this).val();
      var serializeType = $(this).data('validation');

      if (!ValidateForm(fieldValue, serializeType).success) {
        rejectedDivs.push($(this));
        retBool = false;
      }
      else {
        $(this).removeClass('formsection_validation_error');
      }
      employeeInformation[dataSerialize] = fieldValue;
    }
  });

  formInformation['employeeInformation'] = employeeInformation;

  $.each(rejectedDivs, function (index, item) {
    $(this).addClass('formsection_validation_error');
  });

  var returnInformation = {};
  returnInformation['success'] = retBool;
  returnInformation['formInformation'] = formInformation;

  return returnInformation;
}

function NewAccountFormSerialize(elementarray) {
  var returnString = "";
  var first = true;

  elementarray.forEach(function (element) {
    if (!first) {
      returnString += "|";
    }
    first = false;
    if ($(element).is('div')) {

    }
    else if ($(element).is(':checkbox')) {
      var isChecked = false;

      if ($(element).is(':checked')) {
        isChecked = true;
      }
      returnString += $(element).data('serialize') + "`" + isChecked;
    }
    else {
      returnString += $(element).data('serialize') + "`" + $(element).val();
    }
  });

  return returnString;
}

function GetURLParameters() {
  var params = {};
  var queryString = window.location.search.slice(1);  // Remove the '?' at the start of the string
  var paramArray = queryString.split('&');  // Split the query string into its component parts

  for (var i = 0; i < paramArray.length; i++) {
    var paramPart = paramArray[i].split('=');  // Split each part into an array of [key, value]

    if (paramPart[1] === undefined) {  // If there was no '=', add an empty value
      paramPart[1] = '';
    }

    params[decodeURIComponent(paramPart[0])] = decodeURIComponent(paramPart[1]);
    // Use decodeURIComponent to get a human-readable string, and add it to our final object
  }

  return params;
}

function InitDatePickers() {
  $('.formsection_datepicker').each(function () {
    $(this).datepicker({
      dateFormat: 'mm/yy',
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
    });
  });
  $('.formsection_datepicker_full').each(function () {
    $(this).datepicker({
      dateFormat: 'mm/dd/yy',
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
    });
  });
}

function InitInputMasks() {
  $('.formsection_date_full_mask').each(function () {
    $(this).mask('00/00/0000');
  });
  $('.formsection_date_my_mask').each(function () {
    $(this).mask('00/0000');
  });
  $('.formsection_phone_mask').each(function () {
    $(this).mask('(000) 000-0000');
  });
}

function InitTimePickers() {
  $('.formsection_input_timepicker').each(function () {
    var dTime = $(this).data('defaulttime');
    $(this).timepicker({
      timeFormat: 'h:mm p',
      interval: 1,
      minTime: '00:00am',
      maxTime: '11:49pm',
      defaultTime: dTime,
      startTime: '00:00am',
      dynamic: false,
      dropdown: false
    });
  });
}
