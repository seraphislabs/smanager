<?php
class PopupNewShift {
    public static function Generate($_dbInfo, $_postData) {
        $_shiftid = $_postData['shiftid'];
        if (!DatabaseManager::CheckPermissions($_dbInfo, ['emes'])) {
            die("You do not have permission to view this page. Speak to your account manager to gain access.");
        }
        $returnedCode = <<<HTML
            <script>
                var phpShiftId = `$_shiftid`;
                InitTimePickers();

                if (phpShiftId > 0) {
                    $('.popup_scrollable').hide();
                }
                $('.schedule_enable').change(function() {
                    var timeField = $(this).closest('.formsection_line_leftjustify').children('.formsection_toggle_time_fields');
                    if (timeField.is(':visible')) {
                        timeField.fadeOut(200);
                    }
                    else {
                        timeField.fadeIn(200);
                    }
                });
                $("#submit_new_shift").click(function() {
                    if(!$(this).hasClass('disabled')) {
                        $(this).addClass('disabled');
                    }

                    Action_AddNewShift();
                });
                $("#btn_close_popup").click(function () {
                    ClosePopup();
                });
            </script>
        HTML;

        if ($_shiftid > 0) {
            $returnedCode .= <<<HTML
            <div class='popup_topbar'><span style='color:white;'>Edit</span> Shift</div>
            <div class='popup_scrollable'>
            HTML;
        }
        else {
            $returnedCode .= <<<HTML
            <div class='popup_topbar'><span style='color:white;'>New</span> Shift</div>
            <div class='popup_scrollable'>
            HTML;
        }

        $returnedCode .= <<<HTML
            <div class='formsectionfull' id='timeselections'>
                <div class='formsection_line_leftjustify'>
                <div class='formsection_label_1'>Shift Name: </div><input data-shiftid='$_shiftid' class='formsection_input formsection_data_shift_name'/>
                </div>
                <div class='formsection_line_leftjustify' style='height:40px;'><div class='formsection_label_1'>Monday:</div>
                <div class='checkbox_switch'>
                    <label class='switch'>
                        <input type='checkbox' class='schedule_enable formsection_data_checkbox_monday' checked>
                        <span class='slider round'></span>
                    </label>
                </div>
                <div class='formsection_toggle_time_fields formsection_data_toggle_monday'>
                <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_monday_start' />
                to
                <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_monday_end' />
                </div>
                </div>
                <div class='formsection_line_leftjustify' style='height:40px;'><div class='formsection_label_1'>Tuesday:</div>
                <div class='checkbox_switch'>
                    <label class='switch'>
                        <input type='checkbox' class='schedule_enable formsection_data_checkbox_tuesday' checked>
                        <span class='slider round'></span>
                    </label>
                </div>
                <div class='formsection_toggle_time_fields formsection_data_toggle_tuesday'>
                <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_tuesday_start' />
                to
                <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_tuesday_end' />
                </div>
                </div>
                <div class='formsection_line_leftjustify' style='height:40px;'><div class='formsection_label_1'>Wednesday:</div>
                <div class='checkbox_switch'>
                    <label class='switch'>
                        <input type='checkbox' class='schedule_enable formsection_data_checkbox_wednesday' checked>
                        <span class='slider round'></span>
                    </label>
                </div>
                <div class='formsection_toggle_time_fields formsection_data_toggle_wednesday'>
                <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_wednesday_start' />
                to
                <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_wednesday_end' />
                </div>
                </div>
                <div class='formsection_line_leftjustify' style='height:40px;'><div class='formsection_label_1'>Thursday:</div>
                <div class='checkbox_switch'>
                    <label class='switch'>
                        <input type='checkbox' class='schedule_enable formsection_data_checkbox_thursday' checked>
                        <span class='slider round'></span>
                    </label>
                </div>
                <div class='formsection_toggle_time_fields formsection_data_toggle_thursday'>
                <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_thursday_start' />
                to
                <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_thursday_end' />
                </div>
                </div>
                <div class='formsection_line_leftjustify' style='height:40px;'><div class='formsection_label_1'>Friday:</div>
                <div class='checkbox_switch'>
                    <label class='switch'>
                        <input type='checkbox' class='schedule_enable formsection_data_checkbox_friday' checked>
                        <span class='slider round'></span>
                    </label>
                </div>
                <div class='formsection_toggle_time_fields formsection_data_toggle_friday'>
                <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_friday_start' />
                to
                <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_friday_end' />
                </div>
                </div>
                <div class='formsection_line_leftjustify' style='height:40px;'><div class='formsection_label_1'>Saturday:</div>
                <div class='checkbox_switch'>
                    <label class='switch'>
                        <input type='checkbox' class='schedule_enable formsection_data_checkbox_saturday'>
                        <span class='slider round'></span>
                    </label>
                </div>
                <div class='formsection_toggle_time_fields formsection_data_toggle_saturday' style='display:none;'>
                <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_saturday_start' />
                to
                <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_saturday_end' />
                </div>
                </div>
                <div class='formsection_line_leftjustify' style='height:40px;'><div class='formsection_label_1'>Sunday:</div>
                <div class='checkbox_switch'>
                    <label class='switch'>
                        <input type='checkbox' class='schedule_enable formsection_data_checkbox_sunday'>
                        <span class='slider round'></span>
                    </label>
                </div>
                <div class='formsection_toggle_time_fields formsection_data_toggle_sunday' style='display:none;'>
                <input type='text' data-defaulttime='09:00am' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_sunday_start' />
                to
                <input type='text' data-defaulttime='05:00pm' class='formsection_input_fixed_2 formsection_input_timepicker formsection_data_sunday_end' />
                </div>
            </div>
        </div>
        <div class='popup_footer'>
        <div id='submit_new_shift' class='button_type_2'>Save</div><div class='button_type_1' id='btn_close_popup'>Discard</div>
        </div>
        HTML;

        if ($_shiftid > 0) {
            $shiftInformation = json_encode(DatabaseManager::GetEmployeeShift($_dbInfo, $_shiftid));

            $returnedCode .= <<<HTML
            <script type='text/javascript'>
                var shiftInformation = `$shiftInformation`;
                var shiftData = JSON.parse(shiftInformation);

                var shiftName = shiftData['name'];

                var mondayString = shiftData['monday'].split('|');
                var mondayStart = mondayString[0];
                var mondayEnd = mondayString[1];
                var tuesdayString = shiftData['tuesday'].split('|');
                var tuesdayStart = tuesdayString[0];
                var tuesdayEnd = tuesdayString[1];
                var wednesdayString = shiftData['wednesday'].split('|');
                var wednesdayStart = wednesdayString[0];
                var wednesdayEnd = wednesdayString[1];
                var thursdayString = shiftData['thursday'].split('|');
                var thursdayStart = thursdayString[0];
                var thursdayEnd = thursdayString[1];
                var fridayString = shiftData['friday'].split('|');
                var fridayStart = fridayString[0];
                var fridayEnd = fridayString[1];
                var saturdayString = shiftData['saturday'].split('|');
                var saturdayStart = saturdayString[0];
                var saturdayEnd = saturdayString[1];
                var sundayString = shiftData['sunday'].split('|');
                var sundayStart = sundayString[0];
                var sundayEnd = sundayString[1];

                $('.formsection_data_shift_name').val(shiftName);

                $('.schedule_enable').each(function () {
                    $(this).prop('checked', false);
                });

                $('.formsection_toggle_time_fields').each(function () {
                    $(this).hide();
                });

                if (shiftData['monday'] !== undefined && shiftData['monday'] !== null && shiftData['monday'].length > 0) {  
                    $('.formsection_data_monday_start').timepicker('destroy');
                    $('.formsection_data_monday_start').data('defaulttime',mondayStart);
                    $('.formsection_data_monday_end').timepicker('destroy');
                    $('.formsection_data_monday_end').data('defaulttime',mondayEnd); 
                    $('.formsection_data_checkbox_monday').prop('checked', true);
                    $('.formsection_data_toggle_monday').show();
                }
                if (shiftData['tuesday'] !== undefined && shiftData['tuesday'] !== null && shiftData['tuesday'].length > 0) {
                    $('.formsection_data_tuesday_start').timepicker('destroy');
                    $('.formsection_data_tuesday_start').data('defaulttime',tuesdayStart);
                    $('.formsection_data_tuesday_end').timepicker('destroy');
                    $('.formsection_data_tuesday_end').data('defaulttime',tuesdayEnd);
                    $('.formsection_data_checkbox_tuesday').prop('checked', true);
                    $('.formsection_data_toggle_tuesday').show();
                }
                if (shiftData['wednesday'] !== undefined && shiftData['wednesday'] !== null && shiftData['wednesday'].length > 0) {
                    $('.formsection_data_wednesday_start').timepicker('destroy');
                    $('.formsection_data_wednesday_start').data('defaulttime',wednesdayStart);
                    $('.formsection_data_wednesday_end').timepicker('destroy');
                    $('.formsection_data_wednesday_end').data('defaulttime',wednesdayEnd);
                    $('.formsection_data_checkbox_wednesday').prop('checked', true);
                    $('.formsection_data_toggle_wednesday').show();
                }
                if (shiftData['thursday'] !== undefined && shiftData['thursday'] !== null && shiftData['thursday'].length > 0) {
                    $('.formsection_data_thursday_start').timepicker('destroy');
                    $('.formsection_data_thursday_start').data('defaulttime',thursdayStart);
                    $('.formsection_data_thursday_end').timepicker('destroy');
                    $('.formsection_data_thursday_end').data('defaulttime',thursdayEnd);
                    $('.formsection_data_checkbox_thursday').prop('checked', true);
                    $('.formsection_data_toggle_thursday').show();
                }
                if(shiftData['friday'] !== undefined && shiftData['friday'] !== null && shiftData['friday'].length > 0) {
                    $('.formsection_data_friday_start').timepicker('destroy');
                    $('.formsection_data_friday_start').data('defaulttime',fridayStart);
                    $('.formsection_data_friday_end').timepicker('destroy');
                    $('.formsection_data_friday_end').data('defaulttime',fridayEnd);
                    $('.formsection_data_checkbox_friday').prop('checked', true);
                    $('.formsection_data_toggle_friday').show();
                }
                if (shiftData['saturday'] !== undefined && shiftData['saturday'] !== null && shiftData['saturday'].length > 0) {
                    $('.formsection_data_saturday_start').timepicker('destroy');
                    $('.formsection_data_saturday_start').data('defaulttime', saturdayStart);
                    $('.formsection_data_saturday_end').timepicker('destroy');
                    $('.formsection_data_saturday_end').data('defaulttime', saturdayEnd);
                    $('.formsection_data_checkbox_saturday').prop('checked', true);
                    $('.formsection_data_toggle_saturday').show();
                }
                if (shiftData['sunday'] !== undefined && shiftData['sunday'] !== null && shiftData['sunday'].length > 0) {
                    $('.formsection_data_sunday_start').timepicker('destroy');
                    $('.formsection_data_sunday_start').data('defaulttime',sundayStart);
                    $('.formsection_data_sunday_end').timepicker('destroy');
                    $('.formsection_data_sunday_end').data('defaulttime',sundayEnd);
                    $('.formsection_data_checkbox_sunday').prop('checked', true);
                    $('.formsection_data_toggle_sunday').show();
                }
                InitTimePickers();
                $('.popup_scrollable').show();
            </script>
            HTML;
        }

        return $returnedCode;
    }
}
?>