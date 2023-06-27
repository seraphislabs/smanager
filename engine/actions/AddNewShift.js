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

function Action_AddNewShift() {
    var returnInformation = SerializeNewShiftForm();
    var shiftInformation = JSON.stringify(returnInformation['shiftInformation']);

    var requestData = [
        { name: 'action', value: 'AddNewShift' },
        { name: 'shiftInformation', value: shiftInformation }
    ];
    AjaxCall(requestData, Action_AddNewShiftResponse);
}

function Action_AddNewShiftResponse(status, response) {
    if (status) {
        var resVar = response.split('|');
        if (resVar[0] == 'true') {
            $('.popup_wrapper').hide();
            $('.popup_darken').fadeOut(400);
            ClickLeftPaneMenuItem('ViewScheduleSettings', false);
        }
        else {
            $('.popup_scrollable').prepend("<div class='formsection_line_centered'><div class='formsection_input_centered_text'>" + resVar[1] + "</div></div>");
        }
        if ($('#submit_new_role').hasClass('disabled')) {
            $('#submit_new_role').removeClass('disabled');
        }
    }
}