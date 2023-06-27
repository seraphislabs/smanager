function Action_GenerateCalendar(_postData) {

    SetLoadingIcon('.calendar_loading');
    $('.calendar_loading').fadeIn(100);

    var requestData = [
        { name: 'action', value: 'GenerateCalendar' }
    ];

    requestData = requestData.concat(_postData);

    CancelAllAjaxCalls();
    AjaxCall(requestData, Action_GenerateCalendarResponse);
}

function Action_GenerateCalendarResponse(status, response) {
    if (status) {
        $('.calendar_container').html(response);
        $('.calendar_loading').hide();
    }
}