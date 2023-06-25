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

function Action_AddNewAccountResponse(status, response) {
    if (status) {
        var resVar = response.split('|');
        var successString = resVar[0];
        if (successString == 'true') {
            $('.popup_wrapper').hide();
            $('.popup_darken').fadeOut(400);
            ClickLeftPaneMenuItem('ViewAccounts', true);
        }
        else {
            $('.popup_scrollable').prepend("<div class='formsection_line_centered'><div class='formsection_input_centered_text'></div></div>");
        }
        if ($('#submit_new_account_form').hasClass('disabled')) {
            $('#submit_new_account_form').removeClass('disabled');
        }
    }
    else {
        location.reload(true);
    }
}

function Action_AddNewAccount(_xhrArray) {
    var formattedString = SerializeNewAccountForm();
    var formInfo = JSON.stringify(formattedString['formInformation']);

    if (formattedString.success) {
        var requestData = [
            { name: 'action', value: 'AddNewAccount' },
            { name: 'formdata', value: formInfo }
        ];
        AjaxCall(_xhrArray, requestData, Action_AddNewAccountResponse);
    }
    else {
        if ($('#submit_new_account_form').hasClass('disabled')) {
            $('#submit_new_account_form').removeClass('disabled');
        }
    }
}