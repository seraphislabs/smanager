function Action_LoadPage(_xhrArray, _pageData) {
    SetLoadingIcon('#rightpane_container');
    AjaxCall(_xhrArray, _pageData, Action_LoadPageResponse);
}

function Action_LoadPageResponse(status, response) {
    if (status) {
        $('#rightpane_container').html(response);
    }
}