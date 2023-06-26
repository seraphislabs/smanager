function Action_GetScheduleForMonth(_xhrArray, _employeeid, month, year) {
    var requestData = [
        { name: 'action', value: 'GetScheduleForMonth' },
        { name: 'month', value: month },
        { name: 'year', value: year },
        { name: 'eid', value: _employeeid }
    ];
    CancelAllAjaxCalls();
    AjaxCall(_xhrArray, requestData, Action_GetScheduleForMonthResponse);
}

function Action_GetScheduleForMonthResponse(status, response) {
    if (status) {
        $('.calendar_container').html(response);
        $('.calendar_loading').hide();
    }
}