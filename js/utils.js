function ValidateForm(form_input, validation_type) {
  var retVal = {
    success: false,
    response: ''
  };

  switch (validation_type) {
    case 'year':
      var yearRegex = /^\d{4}$/;
      if (!yearRegex.test(form_input)) {
        retVal.response = 'Invalid year';
        return retVal;
      }
      break;
    case 'date':
      var dateRegex = /^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2[0-9]|3[0-1])\/\d{4}$/;
      if (!dateRegex.test(form_input)) {
        retVal.response = 'Invalid Date';
        return retVal;
      }
      break;
    case 'date_my':
      var datemyRegex = /^(0[1-9]|1[0-2])\/\d{4}$/;
      if (!datemyRegex.test(form_input)) {
        retVal.response = 'Invalid Date';
        return retVal;
      }
      break;
    case 'email':
      var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(form_input)) {
        retVal.response = 'Invalid email';
        return retVal;
      }
      break;

    case 'phone':
      var phoneRegex = /(\d{3})\D*(\d{3})\D*(\d{4})/;
      if (!phoneRegex.test(form_input)) {
        retVal.response = 'Invalid phone number';
        return retVal;
      }
      break;

    case 'phone_nonrequired':
      var phoneRegex = /(\d{3})\D*(\d{3})\D*(\d{4})/;
      if (form_input.length > 0 && !phoneRegex.test(form_input)) {
        retVal.response = 'Invalid phone number';
        return retVal;
      }
      break;

    case 'zipCode':
      var zipcodeRegex = /^\d{5}$/;
      if (!zipcodeRegex.test(form_input)) {
        retVal.response = 'Invalid zip code';
        return retVal;
      }
      break;

    case 'address':
      // Customize the regular expression for street address validation
      var streetAddressRegex = /^[a-zA-Z0-9\s.,'-]+$/;
      if (!streetAddressRegex.test(form_input)) {
        retVal.response = 'Invalid street address';
        return retVal;
      }
      break;
    case 'address_nonrequired':
      // Customize the regular expression for street address validation
      var streetAddressRegex = /^[a-zA-Z0-9\s.,'-]+$/;
      if (form_input != "") {
        if (!streetAddressRegex.test(form_input)) {
          retVal.response = 'Invalid street address';
          return retVal;
        }
        else if (form_input.length <= 3) {
          retVal.response = 'Invalid street address';
          return retVal;
        }
      }
      break;
    case 'name':
      if (form_input.length <= 2) {
        retVal.response = 'Invalid Name Length';
        return retVal;
      }
      break;
    case 'name_nonrequired':
      if (form_input.length > 0 && form_input.length <= 2) {
        retVal.response = 'Invalid Name Length';
        return retVal;
      }
      break;
    case 'contractType':
      if (form_input == null) {
        retVal.response = 'Invalid Name Length';
        return retVal;
      }
      break;
    case 'state': {
      var stateRegex = /^[a-zA-Z]+$/;
      if (!stateRegex.test(form_input) || form_input.length != 2) {
        retVal.response = 'State';
        return retVal;
      }
    }
    default:
      retVal.success = true;
      return retVal;
  }

  // Validation passed
  retVal.success = true;
  return retVal;
}

function AjaxCall(_xhrArray, data, callback) {
    var xhr = $.ajax({
      url: 'engine/engine.php',
      type: 'POST',
      contentType: 'application/x-www-form-urlencoded',
      data: data,
      success: function(response) {
        callback(true, response);
      },
      error: function(xhr, status, error) {
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
    $("#formsection_newaccount_details").find('.formsection_serialize').each(function() {
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
    $("#formsection_billing_info").find('.formsection_serialize').each(function() {
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

    $("#formsection_locations_list").find('.formsection_location_entry').each(function() {
      var location = {};

      $(this).find('.formsection_serialize').each(function() {
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

    $.each(rejectedDivs, function(index, item) {
      $(this).addClass('formsection_validation_error');
    });

    var returnInformation = {};
    returnInformation['success'] = retBool;
    returnInformation['formInformation'] = formInformation;

    return returnInformation;
  }

  function SerializeNewEmployeeForm() {
    var formInformation = {};
    var employeeInformation = {};

    var rejectedDivs = [];

    var retBool = true;

    // Populate Account Information
    $(".popup_scrollable").find('.formsection_serialize').each(function() {
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

    $.each(rejectedDivs, function(index, item) {
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

    elementarray.forEach(function(element) {
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
  