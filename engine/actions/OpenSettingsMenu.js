function Action_OpenSettingsMenu(_xhrArray, _requestData) {
    $('.settingsmenu_container').fadeIn(400);
    SetLoadingIcon(".settingsmenu_container");

    AjaxCall(_xhrArray, _requestData, Action_OpenSettingsMenuResponse);
}

function Action_OpenSettingsMenuResponse(status, response) {
    if (status) {
        $('.settingsmenu_container').html(response).show();
        settingsMenuOpen = true;
    }
    else {
        $('.settingsmenu_container').hide().html("");
        settingsMenuOpen = false;
    }
}