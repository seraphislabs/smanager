function Action_StartSession(_requestData) {
    AjaxCall(_requestData, Action_StartSessionResponse);
}

function Action_StartSessionResponse(status, response) {
    if (status) {
        location.reload();
    }
}