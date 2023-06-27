<?php

class Calendar {

    public static function Init($_dbInfo, $_postData) {
        $_employeeid = 0;
        $_schedule = [];
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

        $returnedCode = <<<HTML
        <script>
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
                var data = [
                    { name: 'month', value: month },
                    { name: 'year', value: year },
                    { name: 'eid', value: eid }
                ];
                Action_GenerateCalendar(data);
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
                var data = [
                    { name: 'month', value: month },
                    { name: 'year', value: year },
                    { name: 'eid', value: eid }
                ];
                Action_GenerateCalendar(data);
            });
        </script>
        HTML;

        $returnedCode .= <<<HTML
            <div class='display_section_header_2'>
                <span class='button_type_4 btn_month_left' data-curmonth='$_month' data-curyear='$_year>'><</span>
                <span class='monthyeardisplay' style='width:150px;text-align:center;'>$monthName
                <span class='textcolor_green'>$_year</span></span>
                <span class='button_type_4 btn_month_right' data-curmonth='$_month' data-curyear='$_year'>></span>
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

        if(array_key_exists('eid', $_postData)) {
            $_employeeid = $_postData['eid'];
            $employeeInfo = DatabaseManager::GetEmployee($_dbInfo, $_employeeid);
            $_schedule = DatabaseManager::GetEmployeeShift($_dbInfo, $employeeInfo['shift']);
        }

        $_month = $_postData['month'];
        $_year = $_postData['year'];

        $returnedCode = "";

        // Populate Calendar Days

        $_calendarDays = self::GetMonthDaysToArray($_month, $_year);

        foreach($_calendarDays as $calendarDay) {
            $_day = $calendarDay['day'];
            $_month = $calendarDay['month'];
            $_year = $calendarDay['year'];
            $_dayOfWeek = $calendarDay['dayOfWeek'];

            $date = "$_month/$_day/$_year";

            if ($_dayOfWeek == 'monday') {
                $returnedCode .= '<span class="calendar_week">';
            }

            $dateTime = DateTime::createFromFormat('m/d/Y', $date);
            $currentDateTime = new DateTime('today');

            $returnedCode .= <<<HTML
                <div class='calendar_day'>
                    <div class='calendar_day_header'>
            HTML;

            if ($dateTime < $currentDateTime) {
                $returnedCode .= <<<HTML
                        <span class='textcolor_grey'>$_month/$_day</span>
                    HTML;
            } else if ($dateTime->format('m/d/Y') == $currentDateTime->format('m/d/Y')) {
                $returnedCode .= <<<HTML
                        <span class='textcolor_green'>$_month/$_day</span>
                    HTML;
            } else {
                $returnedCode .= <<<HTML
                        <span>$_month/$_day</span>
                    HTML;
            }

            $returnedCode .= <<<HTML
                </div>
                <div class='calendar_day_content'>
            HTML;

            $returnedCode .= <<<HTML
                    <span class='textcolor_orange'></span>
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