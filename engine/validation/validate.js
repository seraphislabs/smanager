function ValidateForm(form_input, validation_type) {
  var retVal = {
    success: false,
    response: "",
  };

  switch (validation_type) {
    case "time":
      var timeRegex = /^(1[0-2]|0?[0-9]):[0-5][0-9] (AM|PM)$/;
      if (form_input.length == 0) {
        retVal.success = true;
        retVal.response = "null";
        return retVal;
      } else if (!timeRegex.test(form_input)) {
        retVal.response = "Invalid time";
        return retVal;
      }
      break;
    case "year":
      var yearRegex = /^\d{4}$/;
      if (!yearRegex.test(form_input)) {
        retVal.response = "Invalid year";
        return retVal;
      }
      break;
    case "date":
      var dateRegex = /^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2[0-9]|3[0-1])\/\d{4}$/;
      if (!dateRegex.test(form_input)) {
        retVal.response = "Invalid Date";
        return retVal;
      }
      break;
    case "date_my":
      var datemyRegex = /^(0[1-9]|1[0-2])\/\d{4}$/;
      if (!datemyRegex.test(form_input)) {
        retVal.response = "Invalid Date";
        return retVal;
      }
      break;
    case "email":
      var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(form_input)) {
        retVal.response = "Invalid email";
        return retVal;
      }
      break;

    case "phone":
      var phoneRegex = /^(?=.*\d)\(\d{3}\) \d{3}-\d{4}$/;
      if (!phoneRegex.test(form_input)) {
        retVal.response = "Invalid phone number";
        return retVal;
      }
      break;

    case "phone_nonrequired":
      var phoneRegex = /^\(\d{3}\) \d{3}-\d{4}$/;
      if (form_input.length > 0 && !phoneRegex.test(form_input)) {
        retVal.response = "Invalid phone number";
        return retVal;
      }
      break;

    case "zipCode":
      var zipcodeRegex = /^\d{5}$/;
      if (!zipcodeRegex.test(form_input)) {
        retVal.response = "Invalid zip code";
        return retVal;
      }
      break;

    case "address":
      // Customize the regular expression for street address validation
      var streetAddressRegex = /^[a-zA-Z0-9\s.,'-]+$/;
      if (!streetAddressRegex.test(form_input)) {
        retVal.response = "Invalid street address";
        return retVal;
      }
      break;
    case "address_nonrequired":
      // Customize the regular expression for street address validation
      var streetAddressRegex = /^[a-zA-Z0-9\s.,'-]+$/;
      if (form_input != "") {
        if (!streetAddressRegex.test(form_input)) {
          retVal.response = "Invalid street address";
          return retVal;
        } else if (form_input.length <= 3) {
          retVal.response = "Invalid street address";
          return retVal;
        }
      }
      break;
    case "name":
      if (form_input.length <= 2) {
        retVal.response = "Invalid Name Length";
        return retVal;
      }
      break;
    case "name_nonrequired":
      if (form_input.length > 0 && form_input.length <= 2) {
        retVal.response = "Invalid Name Length";
        return retVal;
      }
      break;
    case "contractType":
      if (form_input == null) {
        retVal.response = "Invalid Name Length";
        return retVal;
      }
      break;
    case "selectnumvalue":
      var selectnumvalueRegex = /^-?\d+$/;
      if (!selectnumvalueRegex.test(form_input)) {
        retVal.response = "Select Not Selected";
        return retVal;
      }
      break;
    case "state": {
      var stateRegex = /^[a-zA-Z]+$/;
      if (!stateRegex.test(form_input) || form_input.length != 2) {
        retVal.response = "State";
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
