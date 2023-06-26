function Action_StartSession(_xhrArray, _requestData) {
    AjaxCall(_xhrArray, _requestData, Action_StartSessionResponse);
}

function Action_StartSessionResponse(status, response) {
    if (status) {
        location.reload();
    }
}