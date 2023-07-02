<?php

class Calendar {

    public static function Init($_dbInfo, $_postData) {
        $_employeeid = 0;
        $_schedule = [];
        $_selectionType = $_postData['selectionType'];
        $_selectionAction = $_postData['selectionAction'];

        $helpTooltip = "";
        if (DatabaseManager::CheckPermissions($_dbInfo, ['ees'])) {
            $helpTooltip .= "<span class='textcolor_green'>To edit the schedule</span><br/>
            1. Click on the day you wish to edit.<br/>
            2. Hit <span class='textcolor_orange'>ENTER</span> to save the changes.<br/><br/>
            <span class='textcolor_green'>To erase a schedule</span><br/>
            1. Click on the day you wish to erase<br/>
            2. Hit <span class='textcolor_orange'>ENTER</span> with <span class='textcolor_orange'>BOTH TIMES EMPTY</span>.";
        }
        

        if(array_key_exists('eid', $_postData)) {
            $_employeeid = $_postData['eid'];
            $employeeInfo = DatabaseManager::GetEmployee($_dbInfo, $_employeeid);
            $_schedule = DatabaseManager::GetEmployeeShift($_dbInfo, $employeeInfo['shift']);
        }

        $_month = $_postData['month'];
        $_year = $_postData['year'];

        $returnedCode = "";

        $_month = ltrim($_month, "0");
        $_year = ltrim($_year, "0");
        $monthName = date('F', mktime(0, 0, 0, $_month, 1));

        $pData = json_encode($_postData);

        $returnedCode .= <<<HTML
        <script>
            Action_GenerateCalendar($pData);

            $(document).click(function(event) {
                var target = $(event.target);

                if (!target.closest('.calendar_day').length) {
                    $('.schedule_view_pane').show();
                    $('.schedule_edit_pane').hide();
                }
            });

            $('.btn_month_left').click(function() {
                var monthx = parseInt($(this).data('curmonth'));
                var yearx = parseInt($(this).data('curyear'));
                var month = monthx;
                var year = yearx;
                if (month == 1) {
                    month = 12;
                    year -= 1;
                } else {
                    month -= 1;
                }
                const monthName = new Date(Date.UTC(year, month, 1)).toLocaleString('default', { month: 'long' });
                $('.monthyeardisplay').html(monthName + "<span class='textcolor_green'> " + year + "</span>");
                $(this).data('curmonth', month);
                $(this).data('curyear', year);
                $('.btn_month_right').data('curmonth', month);
                $('.btn_month_right').data('curyear', year);
                var eid = `$_employeeid`;
                var pData = $pData;
                pData.month = month;
                pData.year = year;
                Action_GenerateCalendar(pData);
            });
            $('.btn_month_right').click(function() {
                var monthx = parseInt($(this).data('curmonth'));
                var yearx = parseInt($(this).data('curyear'));
                var month = monthx;
                var year = yearx;          
                if (month == 12) {
                    month = 1;
                    year += 1;
                } else {
                    month += 1;
                }
                const monthName = new Date(Date.UTC(year, month, 1)).toLocaleString('default', { month: 'long' });
                $('.monthyeardisplay').html(monthName + "<span class='textcolor_green'> " + year + "</span>");
                $(this).data('curmonth', month);
                $(this).data('curyear', year);
                $('.btn_month_left').data('curmonth', month);
                $('.btn_month_left').data('curyear', year);
                var eid = `$_employeeid`;
                var pData = $pData;
                pData.month = month;
                pData.year = year;
                Action_GenerateCalendar(pData);
            });
        </script>
        HTML;

        $helpIconTemplate = "";

        if ($helpTooltip != "") {
            $helpIconTemplate = <<<HTML
                <span class='calendar_header_helpicon tooltip_trigger'>
                    <span class='mytooltip' style='display:none;'>
                        $helpTooltip
                    </span>
                    <img src='img/help_green.png' style='width:24px;height:24px;'/>
                </span>
            HTML;
        }

        $returnedCode .= <<<HTML
            <div class='display_section_header_2'>
                <span class='button_type_4 btn_month_left' data-curmonth='$_month' data-curyear='$_year>'><</span>
                <span class='monthyeardisplay' style='width:150px;text-align:center;'>$monthName
                <span class='textcolor_green'>$_year</span></span>
                <span class='button_type_4 btn_month_right' data-curmonth='$_month' data-curyear='$_year'>></span>
                $helpIconTemplate
            </div>
            <div class='calendar_header'>
                <span>Monday</span><span>Tuesday</span><span>Wednesday</span><span>Thursday</span><span>Friday</span>
                <span>Saturday</span><span>Sunday</span>
            </div>
            <div class='display_section_content'>
                <div class='calendar_loading' style='display:none;'></div>
                <div class='calendar_container'>
                </div>
            </div>
        HTML;

        return $returnedCode;
    }

    public static function Generate($_dbInfo, $_postData) {
        $_employeeid = 0;
        $_schedule = [];
        $_setSchedule = [];
        
        $scheduleString = "";

        $canEditSchedule = false;

        $_selectionType = $_postData['selectionType'];
        $_selectionAction = $_postData['selectionAction'];

        if(array_key_exists('eid', $_postData)) {
            $_employeeid = $_postData['eid'];
            $employeeInfo = DatabaseManager::GetEmployee($_dbInfo, $_employeeid);
            $_schedule = DatabaseManager::GetEmployeeShift($_dbInfo, $employeeInfo['shift']);
            $_setSchedule = DatabaseManager::GetSetSchedule($_dbInfo, $_employeeid);
        }

        if($_selectionAction == "EditSchedule") {
            if (DatabaseManager::CheckPermissions($_dbInfo, ['ees'])) {
                $canEditSchedule = true;
            }
        }

        $_monthx = $_postData['month'];
        $_monthx = ltrim($_monthx, "0");
        $_yearx = $_postData['year'];

        $returnedCode = "";

        // Populate Calendar Days
        $_calendarDays = self::GetMonthDaysToArray($_monthx, $_yearx);
        $count = 0;

        $returnedCode .= <<<HTML
            <script>
                InitTimePickers();
                $('.calendar_edit_input').keydown(function (event) {
                    if (event.keyCode === 13) {
                        var eid = $_employeeid;
                        var idate = $(this).parent().data('date');
                        var itimein = $(this).parent().find('.calendar_edit_input').eq(0).val();
                        var itimeout = $(this).parent().find('.calendar_edit_input').eq(1).val();
                        var pData = {
                            'employeeid': eid,
                            'date': idate,
                            'timein': itimein,
                            'timeout': itimeout
                        };
                        Action_AddSetSchedule(pData);
                    }
                });
            </script>
        HTML;

        foreach($_calendarDays as $calendarDay) {
            $count++;
            $_day = $calendarDay['day'];
            $_month = $calendarDay['month'];
            $_year = $calendarDay['year'];
            $_dayOfWeek = $calendarDay['dayOfWeek'];

            $date = "$_month/$_day/$_year";

            if ($_dayOfWeek == 'monday') {
                $returnedCode .= '<span class="calendar_week">';
                $count = 0;
            }

            $dateTime = DateTime::createFromFormat('m/d/Y', $date);

            $dateDisplay = $dateTime->format('Y/m/d');
            $dateDisplayFormatted = $dateTime->format('Y-m-d');

            $currentDateTime = new DateTime('today');
            $currentMonth = $currentDateTime->format('m');
            $currentMonth = ltrim($currentMonth, "0");

            // Package date info for javascript

            $_isToday = false;
            if ($dateTime->format('m/d/Y') == $currentDateTime->format('m/d/Y')) {
                $_isToday = true;
            }

            $returnedCode .= <<<HTML
                <script>
                    $('.schedule_edit_pane').hide();
                </script>
            HTML;

            if (!$_isToday ) {
                if ($count % 2 == 0) {
                    $backgroundColor = "#f2f2f2;";
                    $returnedCode .= <<<HTML
                    <div class='calendar_day' style='background-color:$backgroundColor'>
                        <div class='calendar_day_header'>
                    HTML;
                } else {
                    $backgroundColor = "#fff;";
                    $returnedCode .= <<<HTML
                    <div class='calendar_day' style='background-color:$backgroundColor'>
                        <div class='calendar_day_header'>
                    HTML;
                }
            }
            else {
                $backgroundColor = "rgba(20,167,108,0.24);";
                    $returnedCode .= <<<HTML
                    <div class='calendar_day' style='background-color:$backgroundColor'>
                        <div class='calendar_day_header'>
                    HTML;
            }

            // CALENDAR HEADER
            if ($dateTime >= $currentDateTime) {
                $returnedCode .= <<<HTML
                    <span class='textcolor_green'>$_month/$_day</span>
                HTML;
            }
            else {
                $returnedCode .= <<<HTML
                    <span class='textcolor_grey'>$_month/$_day</span>
                HTML;   
            }

            $returnedCode .= <<<HTML
                </div>
                <div class='calendar_day_content'>
            HTML;

            //CALENDAR CONTENT

            if ($_selectionAction == "EditSchedule") {
                $scheduleString = str_replace("|", "<br/>", $_schedule[$_dayOfWeek]);

                // IF CAN EDIT EMPLOYEE SCHEDULES (ees)
                if ($canEditSchedule) {
                    $returnedCode .= <<<HTML
                        <script>
                            $('.calendar_day').click(function () {
                                var setFocus = true;
                                if ($(this).find('.schedule_edit_pane').is(':visible')) {
                                    setFocus = false;
                                }
                                $('.schedule_view_pane').show();
                                $('.schedule_edit_pane').hide();
                                $(this).find('.schedule_view_pane').hide();
                                $(this).find('.schedule_edit_pane').show();

                                if (setFocus) {
                                    $(this).find('.calendar_edit_input').eq(0).focus();
                                }
                            });
                        </script>
                    HTML;

                    if ($dateTime->format('m/d/Y') > $currentDateTime->format('m/d/Y')) {
                        if (isset($_setSchedule[$dateDisplayFormatted])) {
                            if ($_setSchedule[$dateDisplayFormatted]['timein'] == "12:00 AM" && $_setSchedule[$dateDisplayFormatted]['timeout'] == "12:00 AM") {
                                $scheduleString = "";
                            }
                            else {
                                $scheduleString = $_setSchedule[$dateDisplayFormatted]['timein'] . "<br/>" . $_setSchedule[$dateDisplayFormatted]['timeout'];
                            }
                        }

                        $returnedCode .= <<<HTML
                            <div class='schedule_edit_pane' data-date='$dateDisplay' style='display:inline-flex;flex-direction:column;overflow:hidden;justify-content:center;'>
                                <input class='calendar_edit_input formsection_input_timepicker'/>
                                <input class='calendar_edit_input formsection_input_timepicker'/>
                            </div>
                            <div class='schedule_view_pane' data-date='$dateDisplay'>
                                <span class='textcolor_grey'>$scheduleString</span>
                            </div>
                        HTML;
                    }
                    else if ($dateTime->format('m/d/Y') < $currentDateTime->format('m/d/Y')) {

                        $getPunches = DatabaseManager::GetPunches($_dbInfo, $_employeeid, $dateDisplayFormatted);

                        $hourString = "";

                        if ($getPunches != null) {
                            $totalHours = ltrim($getPunches['totalhours'], "0");
                            $hourString = $totalHours . " hours";
                            
                        }

                        $returnedCode .= <<<HTML
                            <div class='schedule_edit_pane' data-date='$dateDisplay' style='display:inline-flex;flex-direction:column;overflow:hidden;justify-content:center;'>
                                <span class='textcolor_grey'>$hourString
                                    </span>
                                </div>
                                <div class='schedule_view_pane' data-date='$dateDisplay'>
                                    <span class='textcolor_grey'>$hourString
                                    </span>
                            </div>
                        HTML;
                    }
                    else {
                        $missingPunches = DatabaseManager::CheckForEmptyPunch($_dbInfo, $_employeeid);

                        $todaysMissingPunch = "";
                        if ($missingPunches != null) {
                            if ($missingPunches['date'] == $dateDisplayFormatted) {
                                $todaysMissingPunch = "<span class='textcolor_orange'>Clocked in at<br/>" . $missingPunches['timein'] . "</span>";
                            }
                        }

                        if (strlen($todaysMissingPunch) == 0) {
                            if (isset($_setSchedule[$dateDisplayFormatted])) {
                                if ($_setSchedule[$dateDisplayFormatted]['timein'] == "12:00 AM" && $_setSchedule[$dateDisplayFormatted]['timeout'] == "12:00 AM") {
                                    $scheduleString = "";
                                }
                                else {
                                    $scheduleString = $_setSchedule[$dateDisplayFormatted]['timein'] . "<br/>" . $_setSchedule[$dateDisplayFormatted]['timeout'];
                                }
                            }
                        }
                        else {
                            $scheduleString = $todaysMissingPunch;
                        }

                        $returnedCode .= <<<HTML
                            <div class='schedule_edit_pane' data-date='$dateDisplay' style='display:inline-flex;flex-direction:column;overflow:hidden;justify-content:center;'>
                                <span class='textcolor_grey'>$scheduleString</span>
                            </div>
                            <div class='schedule_view_pane' data-date='$dateDisplay'>
                                <span class='textcolor_grey'>$scheduleString</span>
                            </div>
                        HTML;
                    }

                    
                }
                else { // View Schedule Default
                    if ($dateTime->format('m/d/Y') < $currentDateTime->format('m/d/Y'))
                    {
                        $scheduleString = "";
                    }
                    $returnedCode .= <<<HTML
                            <span class='textcolor_grey'>$scheduleString</span>
                    HTML;
                }         
            }
            else {
                $returnedCode .= <<<HTML
                    <span class='textcolor_orange'></span>
                HTML;
            }
            
            $returnedCode .= <<<HTML
                </div>
            HTML;

            $returnedCode .= <<<HTML
                </div>
            HTML;

            if ($_dayOfWeek == 'sunday') {
                $returnedCode .= '</span>';
            }
        }

        $returnedCode .= <<<HTML
            </div>
        HTML;

        return $returnedCode;
    }

    private function GetMonthDaysToArray($_month, $_year) {
        $_calendarDays = array();
        $_month = ltrim($_month, "0");
        $_year = ltrim($_year, "0");
        $monthName = date('F', mktime(0, 0, 0, $_month, 1));

        // Calculate previous month and number of days in previous month
        $prevMonth = ($_month == 1) ? 12 : $_month - 1;
        $prevYear = ($_month == 1) ? $_year - 1 : $_year;
        $prevNumDays = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);

        // Calculate the day of the week for the first day of the current month
        $firstDayOfMonth = new DateTime("{$_year}-{$_month}-01");
        $firstDayOfWeek = $firstDayOfMonth->format('N');
        // Calculate the number of days to display from the previous month
        $numPrevDays = $firstDayOfWeek - 1;

        // Display the days from the previous month
        for ($day = $prevNumDays - $numPrevDays + 1; $day <= $prevNumDays; $day++) {
            $date = new DateTime("$prevYear-$prevMonth-$day");
            $dayOfWeekLwr = strtolower($date->format('l'));

            $calendarDay = [];
            $calendarDay['day'] = $day;
            $calendarDay['month'] = $prevMonth;
            $calendarDay['year'] = $prevYear;
            $calendarDay['dayOfWeek'] = $dayOfWeekLwr;

            array_push($_calendarDays, $calendarDay);
        }

        // Display the days from the current month
        $numDays = cal_days_in_month(CAL_GREGORIAN, $_month, $_year);

        for ($day = 1; $day <= $numDays; $day++) {
            $date = new DateTime("{$_year}-{$_month}-$day");
            $dayOfWeekLwr = strtolower($date->format('l'));

            $calendarDay = [];
            $calendarDay['day'] = $day;
            $calendarDay['month'] = $_month;
            $calendarDay['year'] = $_year;
            $calendarDay['dayOfWeek'] = $dayOfWeekLwr;

            array_push($_calendarDays, $calendarDay);
        }

        // Display the days from the next month
        $nextMonth = ($_month == 12) ? 1 : $_month + 1;
        $nextYear = ($_month == 12) ? $_year + 1 : $_year;
        $nextMonthFirstDay = new DateTime("$nextYear-$nextMonth-01");
        $numNextDays = 8 - $nextMonthFirstDay->format('N');

        for ($day = 1; $day <= $numNextDays; $day++) {
            $date = new DateTime("$nextYear-$nextMonth-$day");
            $dayOfWeekLwr = strtolower($date->format('l'));

            $calendarDay = [];
            $calendarDay['day'] = $day;

            $calendarDay['month'] = $nextMonth;
            $calendarDay['year'] = $nextYear;
            $calendarDay['dayOfWeek'] = $dayOfWeekLwr;

            array_push($_calendarDays, $calendarDay);
        }

        return $_calendarDays;
    }
}

?>