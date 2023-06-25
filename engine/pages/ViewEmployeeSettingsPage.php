<?php
class PageEmployeeSettings {
    public static function Generate($_dbInfo) {
        if (!DatabaseManager::CheckPermissions($_dbInfo, ['emes'])) {
            die("You do not have permission to view this page. Speak to your account manager to gain access.");
        }
        $returnedCode = <<<HTML
            <script type='text/javascript'>
                InitTimePickers();
                $('.btn_open_new_role').click(function() {
                    $('.popup_darken').fadeIn(500);
                    $('.popup_wrapper').fadeIn(500);
                    SetLoadingIcon('.popup_content');
                    var requestData = [
                        {name: 'action', value: 'GenerateNewRolePage'}
                    ];
                    CancelAllAjaxCalls();
                    AjaxCall(xhrArray, requestData, function(status, response) {
                        if (status) {
                            $('.popup_content').html(response).fadeIn(500);
                        }
                    });
                });
            </script>
        HTML;

        $getRoles = DatabaseManager::GetAllEmployeeRoles($_dbInfo, false);
        $rolesList = ListEmployeeRoles::AsList($getRoles);

        $getShifts = DatabaseManager::GetAllEmployeeShifts($_dbInfo, false);
        $shiftsList = ListEmployeeShifts::AsList($getShifts);

        $returnedCode .= <<<HTML
        <div id='rightpane_viewport' style='top:0px'>
            <div class='formsection_width_unset' style='width:800px'>
                <div class='formsection_header'>
                    Employee Roles / Permissions
                </div>
                <div class='formsection_content'>
                    <div class='formsection_subheader_title'>
                        <div class='formsection_line_centered_between'>
                            This is a list of your employee roles and the permissions for each role.
                            <div class='button_type_1 btn_open_new_role'><img src='img/add_user_green.png' style ='width:20px;padding-right:10px;'/>New Role</div>
                        </div>
                    </div>
                    <div style='padding-left:40px;' id='display_employee_roles'>
                    $rolesList
                    </div>
                </div>
            </div>      
        </div>
        HTML;

        return $returnedCode;
    }
}
?>