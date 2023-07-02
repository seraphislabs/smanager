function Action_LoadPopup(_pageData) {
    $('.popup_darken').fadeIn(500);
    $('.popup_wrapper').fadeIn(500);
    SetLoadingIcon('.popup_content');
    AjaxCall(_pageData, Action_LoadPopupResponse, true);
}

function Action_LoadPopupResponse(status, response) {
    if (status) {
        $('.popup_content').html(response).fadeIn(500);
    }
}