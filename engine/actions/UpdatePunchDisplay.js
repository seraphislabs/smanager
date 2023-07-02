function Action_UpdatePunchDisplay() {
    var requestData = [
        { name: 'action', value: 'UpdatePunchDisplay' }
    ];
    $('.clockin_display').html("");
    $('.clockin_time').html("");
    AjaxCall(requestData, Action_UpdatePunchDisplayResponse, false);
}

function Action_UpdatePunchDisplayResponse(status, response) {
    if (status) {
        var resVar = response.split('|').map(function (item) {
            return item.trim();
        });
        if (resVar[0] == "true") {
            $('.clockin_time').html("Clocked in at " + resVar[1]);
            $('.clockin_display').html("<span class='text_button_type_1 btn_punch_in'>Clock Out</span>");
        }
        else {
            $('.clockin_time').html("");
            $('.clockin_display').html("<span class='textcolor_green'>Welcome back, " + resVar[1] + "</span><span class='text_button_type_1 btn_punch_in'>Clock In</span>");
        }
    }
}