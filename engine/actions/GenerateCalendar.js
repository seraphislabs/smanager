function Action_GenerateCalendar(_postData) {

    var requestData = _postData;

    SetLoadingIcon('.calendar_loading');
    $('.calendar_loading').fadeIn(100);

    requestData['action'] = 'GenerateCalendar';

    CancelAllAjaxCalls();
    AjaxCall(requestData, Action_GenerateCalendarResponse, true);
}

function Action_GenerateCalendarResponse(status, response) {
    if (status) {
        $('.calendar_container').html(response);
        $('.calendar_loading').hide();
    }
}