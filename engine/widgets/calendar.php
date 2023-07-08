<?php

class Calendar {

    public static function Init($_postData) {
        $_employeeid = 0;

        $helpTooltip = "";
        if (DatabaseManager::CheckPermission('ees')) {
            $helpTooltip .= "<span class='textcolor_green'>To edit the schedule</span><br/>
            1. Click on the day you wish to edit.<br/>
            2. Hit <span class='textcolor_orange'>ENTER</span> to save the changes.<br/><br/>
            <span class='textcolor_green'>To erase a schedule</span><br/>
            1. Click on the day you wish to erase<br/>
            2. Hit <span class='textcolor_orange'>ENTER</span> with <span class='textcolor_orange'>BOTH TIMES EMPTY</span>.";
        }

        $_month = $_postData['month'];
        $_year = $_postData['year'];

        $returnedCode = "";

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
                <span class='monthyeardisplay' style='width:150px;text-align:center;font-size:20px;'>$monthName
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
                <div class='calendar_container' style='min-height:480px;'>
                </div>
            </div>
        HTML;

        return $returnedCode;
    }

    public static function Generate($_month, $_year, $_eid = 0, $_viewType = "schedule", $_listType = "month") {
        $employeeid = $_eid;
        $selectedMonth = $_month;
        $selectedYear = $_year;
        $returnedCode = "";

        $calendarDays = self::GetMonthDaysToArray($selectedMonth, $selectedYear);

        // Create UI Interactions
        $returnedCode .= <<<HTML
            <script>
                InitTimePickers();
                $('.calendar_edit_input').keydown(function (event) {
                    if (event.keyCode === 13) {
                        var eid = $employeeid;
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

        if ($_viewType == "schedule") {
            $returnedCode .= self::GenerateScheduleView($selectedMonth, $selectedYear, $employeeid, $_listType, $calendarDays);
        }

        return $returnedCode;
    }

    private static function GenerateScheduleView($_selectedMonth, $_selectedYear, $_employeeid, $_listType, $_calendarDays) {
        $returnedCode = "";

        $canEditSchedule = false;

        if (DatabaseManager::CheckPermission('ees')) {
            $canEditSchedule = true;
        }

        $calendarStart = $_calendarDays[0];
        $calendarEnd = $_calendarDays[count($_calendarDays) - 1];

        $calendarStartDate = $calendarStart['year'] . "-" . $calendarStart['month'] . "-" . $calendarStart['day'];
        $calendarEndDate = $calendarEnd['year'] . "-" . $calendarEnd['month'] . "-" . $calendarEnd['day'];

        $setSchedules = DatabaseManager::GetSetSchedulesBetweenDates($_employeeid, $calendarStartDate, $calendarEndDate);
        $punches = DatabaseManager::GetPunchesBetweenDates($_employeeid, $calendarStartDate, $calendarEndDate);
        $employeeInfo = DatabaseManager::GetEmployee($_employeeid);
        $regularShift = DatabaseManager::GetEmployeeShift($employeeInfo['shift']);

        $xPunches = [];
        $xSetSchedules = [];

        if ($_listType == "month") {
            foreach($_calendarDays as $calendarDay) {
                $day = $calendarDay['day'];
                $month = $calendarDay['month'];
                $year = $calendarDay['year'];
                $date = "$year-$month-$day";

                if (isset($setSchedules[$date])) {
                    $xSetSchedules = $setSchedules[$date];
                }
                else {
                    $xSetSchedules = [];
                }

                if (isset($punches[$date])) {
                    $xPunches = $punches[$date];
                }
                else {
                    $xPunches = [];
                }

                $returnedCode .= self::GenerateDaySchedule($_selectedYear, $_selectedMonth, $calendarDay, $xSetSchedules, $xPunches, $regularShift, $canEditSchedule);
            }
        }

        return $returnedCode;
    }

    private static function GenerateDaySchedule($_selectedYear, $_selectedMonth, $_calendarDay, $_setSchedule, $_punches, $_regularShift, $_caneEditSchedule = false) {
        $returnedCode = "";
        $day = $_calendarDay['day'];
        $month = $_calendarDay['month'];
        $year = $_calendarDay['year'];
        $dayOfWeek = $_calendarDay['dayOfWeek'];
        $dayOfWeekLwr = strtolower($dayOfWeek);
        $date = "$month/$day/$year";

        $dateTime = DateTime::createFromFormat('m/d/Y', $date)->setTime(0, 0);
        $dateDisplay = $dateTime->format('Y/m/d');
        $dateDisplayFormatted = $dateTime->format('Y-m-d');

        $currentDateTime = new DateTime('today');
        $currentMonth = $currentDateTime->format('m');

        // Conditional Pre-Checks
        $isToday = $dateTime->format('m/d/Y') == $currentDateTime->format('m/d/Y');
        $isInPast = $dateTime < $currentDateTime;
        $isInFuture = $dateTime > $currentDateTime;

        // Color Formatting
        // TODO: HARD CODED COLOR
        $dayBackgroundColor = "#fff;";
        $dayHeaderColorClass = "textcolor_grey";

        // Handle Punch totals
        $totalPunchedHours = "00:00:00";
        $punchedHoursString = "";
        $hasOpenPunch = false;
        $hasPunches = false;
        $openPunchDate = null;
        $openPunchDateFormatted = null;

        foreach($_punches as $punch) {
            $hasPunches = true;
            if ($punch['timein'] != null) {
                if ($punch['timeout'] != null && $punch['timeout'] != "") {
                    $timeString = TimeManagement::getTimeDifference($punch['timein'], $punch['timeout']);
                    $totalPunchedHours = TimeManagement::addTimes($totalPunchedHours, $timeString);
                }
                else {
                    $hasOpenPunch = true;
                    $totalPunchedHours = $punch['timein'];
                    $openPunchDate = $punch['date'];
                    $openPunchDateFormatted = DateTime::createFromFormat('Y-m-d', $openPunchDate)->format('m/d/Y');
                }
            }
        }

        $totalPunchedHoursFormatted = DateTime::createFromFormat('H:i:s', $totalPunchedHours);
        $totalPunchedHoursHours = ltrim($totalPunchedHoursFormatted->format('H'), '0');
        $totalPunchedHoursMinutes =  ltrim($totalPunchedHoursFormatted->format('i'), '0');

        if (!$hasOpenPunch && $hasPunches) {
            if ($totalPunchedHoursHours <= 0) {
                if ($totalPunchedHoursMinutes <= 0 || $totalPunchedHoursMinutes == "") {
                    $punchedHoursString = "";
                    $hasPunches = false;
                }
                else {
                    $punchedHoursString = "Hours<br/>0:" . $totalPunchedHoursMinutes . "";
                }
            }
            else {
                $punchedHoursString = "Hours<br/>" . $totalPunchedHoursHours . ":" . $totalPunchedHoursMinutes . "";
            }
        }
        else if ($hasOpenPunch && $hasPunches) {
            $dx = DateTime::createFromFormat("H:i:s", $totalPunchedHours)->format("g:i A");
            $punchedHoursString = "Clocked in at<br/>$dx";
        }

        // Alternate colors on calendar
        if ($dayOfWeekLwr == 'monday' || $dayOfWeekLwr == 'wednesday' || $dayOfWeekLwr == 'friday' || $dayOfWeekLwr == 'sunday') {
            $dayBackgroundColor = "#f2f2f2;";
        }

        if ($isToday) {
            $dayHeaderColorClass = "textcolor_green";
            $dayBackgroundColor = "rgba(20,167,108,0.24);";
        }
        else if ($isInFuture) {
            $dayHeaderColorClass = "textcolor_green";
        }
        else {
            $dayHeaderColorClass = "textcolor_grey";
        }

        if ($dayOfWeekLwr == 'monday') {
            $returnedCode .= '<span class="calendar_week">';
        }

        // Day HTML
        $returnedCode .= <<<HTML
            <div class='calendar_day' style='background-color:$dayBackgroundColor'>
                <div class='calendar_day_header'>
                    <span class='$dayHeaderColorClass'>$month/$day</span>
                </div>
            <div class='calendar_day_content'>
        HTML;

        $returnedCode .= <<<HTML
            <script>
                $('.schedule_edit_pane').hide();
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

        $postBody = "";

        if ($isInFuture) {
            // TODO: Check holiday schedule
            // Check if there is a schedule that overrides the default one
            if (!empty($_setSchedule)) {
                if ($_setSchedule['timein'] == "12:00 AM" && $_setSchedule['timeout'] == "12:00 AM") {
                    $adjTime = "";
                }
                else {
                    $adjTime = $_setSchedule['timein'] . "<br/>" . $_setSchedule['timeout'];
                }
                $postBody = "<span class='textcolor_grey'>" . $adjTime . "</span>";
            }
            else if ($_regularShift[$dayOfWeekLwr] != "") {
                $adjTime = str_replace("|", "<br/>", $_regularShift[$dayOfWeekLwr]);
                $postBody = "<span class='textcolor_grey'>" . $adjTime . "</span>";
            }

            $returnedCode .= <<<HTML
                <div class='schedule_edit_pane' data-date='$dateDisplay' style='display:inline-flex;flex-direction:column;overflow:hidden;justify-content:center;'>
                    <input class='calendar_edit_input formsection_input_timepicker'/>
                    <input class='calendar_edit_input formsection_input_timepicker'/>
                </div>
            HTML;
        }
        else if ($isInPast) {
            if ($hasOpenPunch && ($openPunchDate == $dateDisplayFormatted)) {
                $postBody = "<span class='textcolor_green'>Clocked in at<br/>" . DateTime::createFromFormat("H:i:s", $totalPunchedHours)->format("g:i A") . "</span>";
            }
            else {
                $postBody = "<span class='textcolor_grey'>$punchedHoursString</span>";
            }
        }
        else {
            if (!$hasPunches) {
                if ($hasOpenPunch && ($openPunchDate != $dateDisplayFormatted)) {
                    if (!empty($_setSchedule)) {
                        if ($_setSchedule['timein'] == "12:00 AM" && $_setSchedule['timeout'] == "12:00 AM") {
                            $adjTime = "test";
                        }
                        else {
                            $scheduledTimeInDateTime = DateTime::createFromFormat('g:i A', $_setSchedule['timein']);

                            // Late punch
                            $cdt = new DateTime();
                            if ($scheduledTimeInDateTime < $cdt) {
                                $adjTime = "<span class='textcolor_red'>" . $_setSchedule['timein'] . "</span><br/>" . $_setSchedule['timeout'];
                            }
                            else {
                                $adjTime = $_setSchedule['timein'] . "<br/>" . $_setSchedule['timeout'];
                            }
                        }
                    }
                    else if ($_regularShift[$dayOfWeekLwr] != "") {
                        $exShift = explode("|", $_regularShift[$dayOfWeekLwr]);
                        $scheduledTimeInDateTime = DateTime::createFromFormat('g:i A', $exShift[0]);
                        $scheduledTimeOutDateTime = DateTime::createFromFormat('g:i A', $exShift[1]);

                            // Late punch
                        $cdt = new DateTime();
                        if ($scheduledTimeInDateTime < $cdt) {
                            if ($scheduledTimeOutDateTime < $cdt) {
                                $adjTime = "<span class='textcolor_orange'>" . $exShift[0] . "</span><br/><span class='textcolor_orange'>" . $exShift[1] . "</span>";
                            }
                            else {
                                $adjTime = "<span class='textcolor_orange'>" . $exShift[0] . "</span><br/>" . $exShift[1];
                            }
                        }
                        else {
                            $adjTime = str_replace("|", "<br/>", $_regularShift[$dayOfWeekLwr]);
                        }
                    }
                }
                else {
                    $adjTime = "";
                }

                $postBody = "<span class='textcolor_green'>" . $adjTime . "</span>";
            }
            else if ($hasPunches && !$hasOpenPunch) {
                $postBody = "<span class='textcolor_green'>$punchedHoursString</span>";
            }
            else {
                $tph= DateTime::createFromFormat('H:i:s', $totalPunchedHours)->format("g:i A");
                $postBody = "<span class='textcolor_green'>Clocked in at<br/>$tph</span>";
            }
        }


        $returnedCode .= <<<HTML
                <div class='schedule_view_pane' data-date='$dateDisplay'>
                    $postBody
                </div>
            </div>
        HTML;

        $returnedCode .= <<<HTML
            </div>
        HTML;

        if ($dayOfWeekLwr == 'sunday') {
            $returnedCode .= '</span>';
        }

        $returnedCode .= <<<HTML
            </div>
        HTML;

        return $returnedCode;
    }

    private static function GetMonthDaysToArray($_month, $_year) {
        $_calendarDays = array();
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
            $calendarDay['day'] = str_pad($day, 2, "0", STR_PAD_LEFT);
            $calendarDay['month'] = str_pad($prevMonth, 2, "0", STR_PAD_LEFT);
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
            $calendarDay['day'] = str_pad($day, 2, "0", STR_PAD_LEFT);
            $calendarDay['month'] = str_pad($_month, 2, "0", STR_PAD_LEFT);
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
            $calendarDay['day'] = str_pad($day, 2, "0", STR_PAD_LEFT);
            $calendarDay['month'] = str_pad($nextMonth, 2, "0", STR_PAD_LEFT);  
            $calendarDay['year'] = $nextYear;
            $calendarDay['dayOfWeek'] = $dayOfWeekLwr;

            array_push($_calendarDays, $calendarDay);
        }

        return $_calendarDays;
    }
}

?>