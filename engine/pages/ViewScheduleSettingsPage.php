<?php
class PageScheduleSettings {
    public static function Generate($_dbInfo) {
        if (!DatabaseManager::CheckPermissions($_dbInfo, ['emes'])) {
            die("You do not have permission to view this page. Speak to your account manager to gain access.");
        }
        $returnedCode = <<<HTML
            <script type='text/javascript'>
                InitTimePickers();
                $('.btn_open_new_shift').click(function() {
                    $('.popup_darken').fadeIn(500);
                    $('.popup_wrapper').fadeIn(500);
                    SetLoadingIcon('.popup_content');
                    var data = {};
                    data['shiftid'] = 0;
                    var requestData = [
                        {name: 'action', value: 'LoadPopup'},
                        {name: 'buttonid', value: 'NewShift'},
                        {name: 'data', value: JSON.stringify(data)}
                    ];
                    CancelAllAjaxCalls();
                    AjaxCall(xhrArray, requestData, function(status, response) {
                        if (status) {
                            $('.popup_content').html(response).fadeIn(500);
                        }
                    });
                });
                $('.btn_open_new_holiday_schedule').click(function() {
                    $('.popup_darken').fadeIn(500);
                    $('.popup_wrapper').fadeIn(500);
                    SetLoadingIcon('.popup_content');
                    var data = {};
                    var requestData = [
                        {name: 'action', value: 'LoadPopup'},
                        {name: 'buttonid', value: 'NewHolidaySchedule'},
                        {name: 'data', value: JSON.stringify(data)}
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
            <div class='formsection_width_unset' style='width:800px'>
                <div class='formsection_header'>
                Configure Holiday Hours
                </div>
                <div class='formsection_content'>
                    <div class='formsection_subheader_title'>
                        <div class='formsection_line_centered_between'>
                            Setup holiday hours for your company.
                        </div>
                    </div>
                    <div style='padding-left:40px;' id='display_holiday_hours'>
        HTML;       
                
        $getHolidaySchedule = DatabaseManager::GetAllHolidaySchedules($_dbInfo);

        foreach ($getHolidaySchedule as $schedule) {
            $scheduleDaysoff = $schedule['offdays'];
            $scheduleName = $schedule['scheduleinfo']['name'];
            $returnedCode .= <<<HTML
            <div class='formsection_line_leftjustify_width_unset edit_holiday_schedule_button'>
                <img src='img/edit_green.png' style='width:20px;'/>$scheduleName
            </div>
            HTML;
        }

        $returnedCode .= <<<HTML
                    </div>
                </div>
            </div>   
            <div class='formsection_width_unset' style='width:800px;margin-top:20px;'>
                <div class='formsection_header'>
                Employee Shifts
                </div>
                <div class='formsection_content'>
                    <div class='formsection_subheader_title'>
                        <div class='formsection_line_centered_between'>
                            This is a list of assignable shifts for your employees.
                            <div class='button_type_1 btn_open_new_shift'><img src='img/add_user_green.png' style ='width:20px;padding-right:10px;'/>New Shift</div>
                        </div>
                    </div>
                    <div style='padding-left:40px;' id='display_shifts'>
                    $shiftsList
                    </div>
                </div>
            </div>
        </div>
        HTML;

        return $returnedCode;
    }
}
?>