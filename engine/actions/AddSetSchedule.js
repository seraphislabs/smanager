function Action_AddSetSchedule(_postData) {

    if (!ValidateForm(_postData['timein'], 'time').success) {
        return false;
    }

    if (!ValidateForm(_postData['timeout'], 'time').success) {
        return false;
    }

    var postData = JSON.stringify(_postData);

    var requestData = [
        { name: 'action', value: 'AddSetSchedule' },
        { name: 'formInformation', value: postData }
    ];
    $('.schedule_edit_pane').hide();
    SetLoadingIcon('.calendar_loading');
    $('.calendar_loading').fadeIn(100);
    console.log(JSON.stringify(requestData, null, 4));
    AjaxCall(requestData, Action_AddSetScheduleResponse, false);
}

function Action_AddSetScheduleResponse(status, response) {
    if (status) {
        var resVar = response.split('|').map(function (item) {
            return item.trim();
        });
        if (resVar[0] == 'true') {

            $('.schedule_view_pane').each(function (element) {
                if ($(this).data('date') == resVar[1]) {
                    if (!resVar[2].length > 0) {
                        resVar[2] = "";
                    }

                    $(this).html("<span class='textcolor_grey'>" + resVar[2] + "</span>");
                    $(this).show();
                }
            });
        }
    }
    $('.calendar_loading').hide();
}