<?php

class Calendar {
    public static function GetMonthSchedule($_dbInfo, $_month, $_year, $_employeeid) {
        $employeeInfo = DatabaseManager::GetEmployee($_dbInfo, $_employeeid);
        $shiftInfo = DatabaseManager::GetShift($_dbInfo, $employeeInfo['shift']);
        return self::GenerateThisMonthSchedule($_month, $_year, $shiftInfo);
    }

    public static function GenerateThisMonthSchedule($_month, $_year, $_schedule) {
        $returnedCode = "";

        $_month = ltrim($_month, "0");
        $_year = ltrim($_year, "0");

        $returnedCode .= <<<HTML
                <div class='calendar_loading' style='display:none;'></div>
        HTML;

        // Calculate previous month and number of days in previous month
        $prevMonth = ($_month == 1) ? 12 : $_month - 1;
        $prevYear = ($_month == 1) ? $_year - 1 : $_year;
        $prevNumDays = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);

        // Calculate the day of the week for the first day of the current month
        $firstDayOfWeek = date('N', strtotime("{$_year}-{$_month}-01"));
        // Calculate the number of days to display from the previous month
        $numPrevDays = $firstDayOfWeek - 1;

        // Display the days from the previous month
        for ($day = $prevNumDays - $numPrevDays + 1; $day <= $prevNumDays; $day++) {
            $date = "$prevMonth/$day/$prevYear";
            $dayOfWeek = date('l', strtotime($date));

            $dayOfWeekLwr = strtolower($dayOfWeek);
            $scheduleTime = "";
            if (strlen($_schedule[$dayOfWeekLwr]) > 0)
            {
                $scheduleTime = str_replace("|", "<br/>", $_schedule[$dayOfWeekLwr]);
            }
            $day = ltrim($day, "0");

            if ($dayOfWeek == 'Monday') {
                $returnedCode .= '<span class="calendar_week">';
            }

            $dateTime = DateTime::createFromFormat('m/d/Y', $date);

            if ($dateTime < new DateTime('today')) {
                $returnedCode .= <<<HTML
                <div class='calendar_day'>
                    <div class='calendar_day_header'>
                        <span class='textcolor_grey'>$prevMonth/$day</span>
                    </div>
                </div>
                HTML;
            }
            else if (date('m/d/Y', strtotime($date)) == date('m/d/Y')) 
            {
                if (HolidayChecker::IsHoliday($dateTime)) {
                    $returnedCode .= <<<HTML
                    <div class='calendar_day'>
                        <div class='calendar_day_header'>
                            <span>$prevMonth/$day</span>
                        </div>
                        <div class='calendar_day_content' style='margin-top:8px;'>
                            <span class='textcolor_orange'>$scheduleTime</span>
                        </div>
                    </div>
                    HTML;
                } else {
                    $returnedCode .= <<<HTML
                    <div class='calendar_day'>
                        <div class='calendar_day_header'>
                            <span class='textcolor_green'>$prevMonth/$day</span>
                        </div>
                        <div class='calendar_day_content' style='margin-top:8px;'>
                            <span class='textcolor_grey'>$scheduleTime</span>
                        </div>
                    </div>
                    HTML;
                }
            }
            else
            {
                if (HolidayChecker::IsHoliday($dateTime)) {
                    $returnedCode .= <<<HTML
                    <div class='calendar_day'>
                        <div class='calendar_day_header'>
                            <span>$prevMonth/$day</span>
                        </div>
                        <div class='calendar_day_content' style='margin-top:8px;'>
                            <span class='textcolor_orange'>$scheduleTime</span>
                        </div>
                    </div>
                    HTML;
                } else {
                    $returnedCode .= <<<HTML
                    <div class='calendar_day'>
                        <div class='calendar_day_header'>
                            <span>$prevMonth/$day</span>
                        </div>
                        <div class='calendar_day_content' style='margin-top:8px;'>
                            <span class='textcolor_grey'>$scheduleTime</span>
                        </div>
                    </div>
                    HTML;
                }
            }

            if ($dayOfWeek == 'Sunday') {
                $returnedCode .= '</span>';
            }
        }

        // Display the days from the current month
        $numDays = cal_days_in_month(CAL_GREGORIAN, $_month, $_year);

        for ($day = 1; $day <= $numDays; $day++) {
            $date = "$_month/$day/$_year";
            $dayOfWeek = date('l', strtotime($date));

            $dayOfWeekLwr = strtolower($dayOfWeek);
            $scheduleTime = "";
            if (strlen($_schedule[$dayOfWeekLwr]) > 0)
            {
                $scheduleTime = str_replace("|", "<br/>", $_schedule[$dayOfWeekLwr]);
            }
            $day = ltrim($day, "0");

            if ($dayOfWeek == 'Monday') {
                $returnedCode .= '<span class="calendar_week">';
            }

            $dateTime = DateTime::createFromFormat('m/d/Y', $date);

            if ($dateTime < new DateTime('today')) {
                $returnedCode .= <<<HTML
                <div class='calendar_day'>
                    <div class='calendar_day_header'>
                        <span class='textcolor_grey'>$_month/$day</span>
                    </div>
                </div>
                HTML;
            } else {
                if (HolidayChecker::IsHoliday($dateTime)) {
                    $returnedCode .= <<<HTML
                    <div class='calendar_day'>
                        <div class='calendar_day_header'>
                            <span>$_month/$day</span>
                        </div>
                        <div class='calendar_day_content' style='margin-top:8px;'>
                            <span class='textcolor_orange'>$scheduleTime</span>
                        </div>
                    </div>
                    HTML;
                }
                else if (date('m/d/Y', strtotime($date)) == date('m/d/Y')) 
                {
                    if (HolidayChecker::IsHoliday($dateTime)) {
                        $returnedCode .= <<<HTML
                        <div class='calendar_day'>
                            <div class='calendar_day_header'>
                                <span>$_month/$day</span>
                            </div>
                            <div class='calendar_day_content' style='margin-top:8px;'>
                                <span class='textcolor_orange'>$scheduleTime</span>
                            </div>
                        </div>
                        HTML;
                    } else {
                        $returnedCode .= <<<HTML
                        <div class='calendar_day' style='background-color:#ebf5f1;'>
                            <div class='calendar_day_header'>
                                <span class='textcolor_green'>$_month/$day</span>
                            </div>
                            <div class='calendar_day_content' style='margin-top:8px;'>
                                <span class='textcolor_grey'>$scheduleTime</span>
                            </div>
                        </div>
                        HTML;
                    }
                }
                else 
                {
                    $returnedCode .= <<<HTML
                    <div class='calendar_day'>
                        <div class='calendar_day_header'>
                            <span>$_month/$day</span>
                        </div>
                        <div class='calendar_day_content' style='margin-top:8px;'>
                            <span class='textcolor_grey'>$scheduleTime</span>
                        </div>
                    </div>
                    HTML;
                }
            }

            if ($dayOfWeek == 'Sunday') {
                $returnedCode .= '</span>';
            }
        }

        // Display the days from the next month
        $nextMonth = ($_month == 12) ? 1 : $_month + 1;
        $nextYear = ($_month == 12) ? $_year + 1 : $_year;
        $numNextDays = 8 - date('N', strtotime("$nextYear-$nextMonth-01"));

        for ($day = 1; $day <= $numNextDays; $day++) {
            $date = "$nextMonth/$day/$nextYear";
            $dayOfWeek = date('l', strtotime($date));

            $dayOfWeekLwr = strtolower($dayOfWeek);
            $scheduleTime = "";
            if (strlen($_schedule[$dayOfWeekLwr]) > 0)
            {
                $scheduleTime = str_replace("|", "<br/>", $_schedule[$dayOfWeekLwr]);
            }
            $day = ltrim($day, "0");

            if ($dayOfWeek == 'Monday') {
                $returnedCode .= '<span class="calendar_week">';
            }

            $dateTime = DateTime::createFromFormat('m/d/Y', $date);

            if ($dateTime < new DateTime('today')) {
                $returnedCode .= <<<HTML
                <div class='calendar_day'>
                    <div class='calendar_day_header'>
                        <span class='textcolor_grey'>$nextMonth/$day</span>
                    </div>
                </div>
                HTML;
            } 
            else if (date('m/d/Y', strtotime($date)) == date('m/d/Y')) 
            {
                if (HolidayChecker::IsHoliday($dateTime)) {
                    $returnedCode .= <<<HTML
                    <div class='calendar_day'>
                        <div class='calendar_day_header'>
                            <span>$nextMonth/$day</span>
                        </div>
                        <div class='calendar_day_content' style='margin-top:8px;'>
                            <span class='textcolor_orange'>$scheduleTime</span>
                        </div>
                    </div>
                    HTML;
                } else {
                    $returnedCode .= <<<HTML
                    <div class='calendar_day'>
                        <div class='calendar_day_header'>
                            <span class='textcolor_green'>$nextMonth/$day</span>
                        </div>
                        <div class='calendar_day_content' style='margin-top:8px;'>
                            <span class='textcolor_grey'>$scheduleTime</span>
                        </div>
                    </div>
                    HTML;
                }
            }
            else
            {
                if (HolidayChecker::IsHoliday($dateTime)) {
                    $returnedCode .= <<<HTML
                    <div class='calendar_day'>
                        <div class='calendar_day_header'>
                            <span>$nextMonth/$day</span>
                        </div>
                        <div class='calendar_day_content' style='margin-top:8px;'>
                            <span class='textcolor_orange'>$scheduleTime</span>
                        </div>
                    </div>
                    HTML;
                } else {
                    $returnedCode .= <<<HTML
                    <div class='calendar_day'>
                        <div class='calendar_day_header'>
                            <span>$nextMonth/$day</span>
                        </div>
                        <div class='calendar_day_content' style='margin-top:8px;'>
                            <span class='textcolor_grey'>$scheduleTime</span>
                        </div>
                    </div>
                    HTML;
                }
            }

            if ($dayOfWeek == 'Sunday') {
                $returnedCode .= '</span>';
            }
        }

        return $returnedCode;
    }
}

?>