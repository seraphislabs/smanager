function Action_LoadPage(_pageData) {
    SetLoadingIcon('#rightpane_container');
    AjaxCall(_pageData, Action_LoadPageResponse, true);
}

function Action_LoadPageResponse(status, response) {
    if (status) {
        $('#rightpane_container').html(response);
        //Dingleberries
    }
}