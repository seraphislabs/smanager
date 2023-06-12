function ValidateForm(form_input, validation_type) {
  var retVal = {
    success: false,
    response: ''
  };

  switch (validation_type) {
    case 'email':
      var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(form_input)) {
        retVal.response = 'Invalid email';
        return retVal;
      }
      break;

    case 'phone':
      var phoneRegex = /^\d{10}$/;
      if (!phoneRegex.test(form_input)) {
        retVal.response = 'Invalid phone number';
        return retVal;
      }
      break;

    case 'phone_nonrequired':
      var phoneRegex = /^\d{10}$/;
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

function AjaxCall(data, callback) {
    $.ajax({
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
  