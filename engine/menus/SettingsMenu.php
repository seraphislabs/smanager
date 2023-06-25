<?php

class MenuSettings {
    public static function Generate($_dbInfo) {
        $myPerms = DatabaseManager::GetUserPermissions($_dbInfo);

        $returnedCode = <<<HTML
            <script type='text/javascript'>
                $('.open_employee_settings_page').click(function() { 
                    var requestData = [
                    {name: 'action', value: 'LoadPage'},
                    {name: 'buttonid', value: 'EmployeeSettings'}
                    ];
                    SetLoadingIcon('#rightpane_container');
                    CancelAllAjaxCalls();
                    AjaxCall(xhrArray, requestData, function(status, response) {
                        if (status) {
                            $("#rightpane_container").html(response);
                        }
                    });
                });
                $('.open_schedule_settings_page').click(function() { 
                    var requestData = [
                    {name: 'action', value: 'LoadPage'},
                    {name: 'buttonid', value: 'ScheduleSettings'}
                    ];
                    SetLoadingIcon('#rightpane_container');
                    CancelAllAjaxCalls();
                    AjaxCall(xhrArray, requestData, function(status, response) {
                        if (status) {
                            $("#rightpane_container").html(response);
                        }
                    });
                });
            </script>
        HTML;

        $returnedCode .= <<<HTML
        <div class='settingsmenu_header'>Settings</div>
        <div class='settingsmenu_divider'></div>
        <div class='settingsmenu_button'><img src='img/report_green.png' width='30px' style='padding-right:10px;'/>Dashboard</div>
        HTML;

        if (DatabaseManager::ManuallyCheckPermissions($myPerms, ["emes"])) {
            $returnedCode .= <<<HTML
            <div class='settingsmenu_button open_employee_settings_page'><img src='img/tech_green.png' width='30px' style='padding-right:10px;'/>Employee Role Settings</div>
            HTML;
        }
        if (DatabaseManager::ManuallyCheckPermissions($myPerms, ["emss"])) {
            $returnedCode .= <<<HTML
            <div class='settingsmenu_button open_schedule_settings_page'><img src='img/calendar_green.png' width='30px' style='padding-right:10px;'/>Schedule Settings</div>
            HTML;
        }

        $returnedCode .= <<<HTML
            <div class='settingsmenu_divider_grey'></div>


            <div class='settingsmenu_button' id='logoutbutton'><img src='img/logout_green.png' width='30px' style='padding-right:10px;'/>Sign Out</div>
        HTML;
        return $returnedCode;
    }
}

?>