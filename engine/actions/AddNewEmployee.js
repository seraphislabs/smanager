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


function Action_AddNewEmployee() {

    var formattedString = SerializeNewEmployeeForm();
    var formInfo = JSON.stringify(formattedString['formInformation']);

    if (formattedString.success) {
        var requestData = [
            { name: 'action', value: 'AddNewEmployee' },
            { name: 'formdata', value: formInfo }
        ];
        AjaxCall(requestData, Action_AddNewEmployeeResponse)
    }
    else {
        if ($('#submit_new_employee_form').hasClass('disabled')) {
            $('#submit_new_employee_form').removeClass('disabled');
        }
    }
}

function Action_AddNewEmployeeResponse(status, response) {
    if (status) {
        var resVar = response.split('|');
        if (resVar[0] == 'true') {
            $('.popup_wrapper').hide();
            $('.popup_darken').fadeOut(400);
            ClickLeftPaneMenuItem('ViewEmployees', true);
        }
        else {
            $('.popup_scrollable').prepend("<div class='formsection_line_centered'><div class='formsection_input_centered_text'>" + resVar[1] + "</div></div>");
        }
        if ($('#submit_new_employee_form').hasClass('disabled')) {
            $('#submit_new_employee_form').removeClass('disabled');
        }
    }
    else {
        location.reload(true);
    }
}