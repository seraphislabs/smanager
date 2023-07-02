function Action_AddNewRole(_roleid) {
    var endperms = "";
    var first = true;
    $("#permissions_listings").find('.formsection_permissions_checkbox').each(function () {
        if ($(this).is(':checked')) {
            if (!first) {
                endperms += "|";
            }
            endperms += $(this).data('flag');
            first = false;
        }
    });

    var isDispatchable = $('.checkbox_can_be_dispatched').is(":checked");
    var roleName = $(".formsection_rolename").val();
    var requestData = [
        { name: 'action', value: 'AddNewRole' },
        { name: 'name', value: roleName },
        { name: 'perms', value: endperms },
        { name: 'isDispatchable', value: isDispatchable },
        { name: 'roleid', value: _roleid }
    ];
    AjaxCall(requestData, Action_AddNewRoleResponse, true);
}

function Action_AddNewRoleResponse(status, response) {
    if (status) {
        var resVar = response.split('|');
        if (resVar[0] == 'true') {
            $('.popup_wrapper').hide();
            $('.popup_darken').fadeOut(400);
            ClickLeftPaneMenuItem('ViewEmployeeSettings', false);
        }
        else {
            $('.popup_scrollable').prepend("<div class='formsection_line_centered'><div class='formsection_input_centered_text'>" + resVar[1] + "</div></div>");
        }
        if ($('#submit_new_role').hasClass('disabled')) {
            $('#submit_new_role').removeClass('disabled');
        }
    }
}