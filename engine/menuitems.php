<?php

class MenuListing {
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

    public static function GetRolesAsSelect($_dbInfo) {
        $returnedCode = "";

        $retVar = DatabaseManager::GetRoles($_dbInfo, false);

        foreach($retVar as $role) {
            $roleName = $role['name'];
            $roleID = $role['id'];
            $returnedCode .= <<<HTML
            <option value='$roleID'>$roleName</option>
            HTML;
        }

        return $returnedCode;
    }

    public static function GetShiftsAsSelect($_dbInfo) {
        $returnedCode = "";

        $retVar = DatabaseManager::GetShifts($_dbInfo, false);

        foreach($retVar as $shift) {
            $shiftName = $shift['name'];
            $shiftID = $shift['id'];
            $returnedCode .= <<<HTML
            <option value='$shiftID'>$shiftName</option>
            HTML;
        }

        return $returnedCode;
    }

    public static function GenerateButton($buttonType) {
        $returnedCode = "";
        switch ($buttonType) {
            case "Accounts":
                $returnedCode = <<<HTML
                <div class='leftpanebutton' data-buttonid='Accounts'><img src='img/customer_green.png' class='img_icon leftpanebuttonicon'/><span class='leftpanebuttontext'>Accounts</span></div>
                HTML;
                break;
            case "Dashboard":
                $returnedCode = <<<HTML
                <div class='leftpanebutton' data-buttonid='Dashboard'><img src='img/menu_green.png' class='img_icon leftpanebuttonicon'/><span class='leftpanebuttontext'>Dashboard</span></div>
                HTML;
                break;
            case "Employees":
                $returnedCode = <<<HTML
                <div class='leftpanebutton' data-buttonid='Employees'><img src='img/tech_green.png' class='img_icon leftpanebuttonicon'/><span class='leftpanebuttontext'>Employees</span></div>
                HTML;
                break;
            case "WorkOrders":
                $returnedCode = <<<HTML
                <div class='leftpanebutton' data-buttonid='WorkOrders'><img src='img/order_green.png' class='img_icon leftpanebuttonicon'/><span class='leftpanebuttontext'>Work Orders</span></div>
                HTML;
                break;
            case "Invoices":
                $returnedCode = <<<HTML
                <div class='leftpanebutton' data-buttonid='Invoices'><img src='img/invoice_green.png' class='img_icon leftpanebuttonicon'/><span class='leftpanebuttontext'>Invoices</span></div>
                HTML;
                break;
            case "ServiceReports":
                $returnedCode = <<<HTML
                <div class='leftpanebutton' data-buttonid='ServiceReports'><img src='img/report_green.png' class='img_icon leftpanebuttonicon'/><span class='leftpanebuttontext'>Service Reports</span></div>
                HTML;
                break;
        }

        return $returnedCode;
    }

    public static function GenerateEmployeesList($_dbInfo, $_retArray, $_shifts, $_roles) {
        $returnedCode = "";
        $employees = $_retArray;
        if (is_array($employees)) {
            foreach($employees as $employee) {
                $employeeName = $employee['firstname'] . ' ' . $employee['lastname'];
                $employeeRole = $employee['role'];
                $employeeShift = $employee['shift'];
                $employeeId = $employee['id'];

                $roleName = $_roles[$employeeRole]['name'];
                $shiftName = $_shifts[$employeeShift]['name'];

                $returnedCode .= <<<HTML
                <tr class='openemployeebutton' data-employeeid='$employeeId'>
                    <td>$employeeName</td>
                    <td>$roleName</td>
                    <td>$shiftName</td>
                </tr>
                HTML;
            }
        }

        $returnedCode .= <<<HTML
        <script>
            $('.openemployeebutton').click(function () { 
                $(this).hide();
                var eid = $(this).data('employeeid');
                var requestData = [
                {name: 'action', value: 'ViewEmployee'},
                {name: 'employeeid', value: eid}
                ];
                CancelAllAjaxCalls();
                SetLoadingIcon('#rightpane_container');
                AjaxCall(xhrArray, requestData, function(status, response) {
                    if (status) {
                        $('#rightpane_container').html(response);
                    }
                });

                console.log('clicked');
            });
        </script>
    HTML;

        return $returnedCode;
    }

    public static function GenerateAccountsList($_retArray) {
        $returnedCode = "";
        $_accounts = $_retArray;
        if (is_array($_accounts)) {
            foreach($_accounts as $account) {

                $accountName = $account['name'];
                $accountType = $account['type'];
                $aid = $account['id'];

                $returnedCode .= <<<HTML
                <tr class='openaccountbutton' data-accountid='$aid'>
                    <td>$accountName</td>
                    <td>$accountType</td>
                </tr>
                HTML;
            }
        }

        $returnedCode .= <<<HTML
            <script>
                $('.openaccountbutton').click(function () { 
                    $(this).hide();
                    var aid = $(this).data('accountid');
                    var requestData = [
                    {name: 'action', value: 'ViewAccount'},
                    {name: 'accountid', value: aid}
                    ];
                    CancelAllAjaxCalls();
                    SetLoadingIcon('#rightpane_container');
                    AjaxCall(xhrArray, requestData, function(status, response) {
                        if (status) {
                            $('#rightpane_container').html(response);
                        }
                    });

                    console.log('clicked');
                });
            </script>
        HTML;

        return $returnedCode;
    }

    public static function GenerateShiftsList($_retArray) {
        $count = 0;
        $returnedCode = "";
        $_shifts = $_retArray;
        if (is_array($_shifts)) {
            foreach($_shifts as $shift) {
                $count++;
                $color = "#E0DFE5";

                if ($count%2 == 0) {
                    $color = "#FAFAFA";
                }

                $shiftName = $shift['name'];
                $shiftId = $shift['id'];

                $returnedCode .= <<<HTML
                <div class='formsection_line_leftjustify_width_unset edit_shift_button' data-shiftid='$shiftId'>
                    <img src='img/edit_green.png' style='width:20px;'/><span class='tooltip_trigger'>$shiftName
                    <span class='mytooltip' style='display:none;'>
                    <span style='color:#14A76C'>$shiftName</span><br/>
                    Monday: 9-5<br/>
                    Tuesday: 9-5<br/>
                    Wednesday: 9-5<br/>
                    Thursday: 9-5<br/>
                    Friday: 9-5
                    </span>
                    </span>
                </div>
                HTML;
            }
        }

        $returnedCode .= <<<HTML
            <script>
                $(".edit_shift_button").click(function() {
                        var shiftid = $(this).data('shiftid');
                        $('.popup_darken').fadeIn(500);
                        $('.popup_wrapper').fadeIn(500);
                        $('.popup_content').fadeIn(300);
                        SetLoadingIcon('.popup_content');
                        var requestData = [
                            {name: 'action', value: 'GenerateNewShiftPage'},
                            {name: 'shiftid', value: shiftid}
                        ];
                        CancelAllAjaxCalls();
                        AjaxCall(xhrArray, requestData, function(status, response) {
                            if (status) {
                                $('.popup_content').html(response).show();
                            }
                        });
                    });
            </script>
        HTML;

        return $returnedCode;
    }

    public static function GenerateLocationsList($_dbInfo, $_retArray) {
        $returnedCode = "";
        $count = 0;
        $_locations = $_retArray;
        if (is_array($_locations)) {
            foreach($_locations as $location) {
                $count++;
                $locationName = $location['name'];
                $locationAddress = $location['city'] . "<span class='textcolor_green'>&nbsp;|&nbsp;</span>" .$location['street1'];
                $lid = $location['id'];
                $lcs = $location['contacts'];
                $lcsf = explode("|", $lcs);
                $lcdata = DatabaseManager::GetContact($_dbInfo, $lcsf[0]);

                $locationContactName = $lcdata['firstname'] . " " . $lcdata['lastname'];
                $locationContactPhone = $lcdata['primaryphone'];

                if (count($_locations) > 1 ) {
                    if ($locationName == "") {
                        $locationName = "Location $count";
                    }
                }
                else {
                    if ($locationName == "") {
                        $locationName = "Primary Location";
                    }
                }

                $returnedCode .= <<<HTML
                <tr class='openlocationbuttonx' data-accountid='$lid'>
                    <td>$locationName</td>
                    <td>$locationAddress</td>
                    <td>$locationContactName</td>
                    <td>$locationContactPhone</td>
                </tr>
                HTML;
            }
        }

        $returnedCode .= <<<HTML
            <script>
                $('.openlocationbutton').click(function () { 
                    $(this).hide();
                    var aid = $(this).data('accountid');
                    var requestData = [
                    {name: 'action', value: 'ViewAccount'},
                    {name: 'accountid', value: aid}
                    ];
                    CancelAllAjaxCalls();
                    SetLoadingIcon('#rightpane_container');
                    AjaxCall(xhrArray, requestData, function(status, response) {
                        if (status) {
                            $('#rightpane_container').html(response);
                        }
                    });

                    console.log('clicked');
                });
            </script>
        HTML;

        return $returnedCode;
    }

    public static function GenerateEmployeeRoleList($_retArray) {
        $count = 0;
        $returnedCode = "";
        $_roles = $_retArray;
        if (is_array($_roles)) {
            foreach($_roles as $role) {
                $count++;
                $color = "#E0DFE5";

                if ($count%2 == 0) {
                    $color = "#FAFAFA";
                }

                $roleName = $role['name'];
                $roleId = $role['id'];
                $returnedCode .= <<<HTML
                <div class='formsection_line_leftjustify edit_role_button' data-roleid='$roleId'>
                    <img src='img/edit_green.png' style='width:20px;'/>$roleName
                </div>
                HTML;
            }
        }

        $returnedCode .= <<<HTML
            <script>
                $(".edit_role_button").click(function() {
                        var roleid = $(this).data('roleid');
                        $('.popup_darken').fadeIn(500);
                        $('.popup_wrapper').fadeIn(500); 
                        $('.popup_scrollable').fadeIn(300);
                        SetLoadingIcon('.popup_scrollable');
                        var requestData = [
                            {name: 'action', value: 'GenerateNewRolePage'},
                            {name: 'roleid', value: roleid}
                        ];
                        CancelAllAjaxCalls();
                        AjaxCall(xhrArray, requestData, function(status, response) {
                            if (status) {
                                $('.popup_content').html(response).fadeIn(300);
                            }
                        });
                    });
            </script>
        HTML;

        return $returnedCode;
    }
}

?>