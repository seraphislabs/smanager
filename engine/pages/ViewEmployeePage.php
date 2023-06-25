<?php
class PageViewEmployee {
   public static function Generate($_dbInfo, $_employeeid) {
    $returnedCode = "";
    // Permission Check

    $employeeInfo = DatabaseManager::GetEmployee($_dbInfo, $_employeeid);
    $shiftInfo = DatabaseManager::GetShift($_dbInfo, $employeeInfo['shift']);
    $roleInfo = DatabaseManager::GetRole($_dbInfo, $employeeInfo['role']);

    $year = date('Y');
    $month = date('m');
    $monthName = date('F', mktime(0, 0, 0, $month, 1));

    $calendarView = "";
    $scheduleTemplate = "<center>You do not have permission to view this employee's schedule.</center>";
    $canviewschedule = true;
    if (!DatabaseManager::CheckPermissions($_dbInfo, ['ves'])) {
        if ($_SESSION['eid'] != $_employeeid) {
            $canviewschedule = false;
        }
    }
    if ($canviewschedule) {
        $calendarView = MenuListing::GenerateThisMonthSchedule($month, $year, $shiftInfo);
        $scheduleTemplate = <<<HTML
            <div class='display_section_header_2'>
                    <span class='button_type_4 btn_month_left' data-curmonth='$month' data-curyear='$year>'><</span>
                    <span class='monthyeardisplay' style='width:150px;text-align:center;'>$monthName
                    <span class='textcolor_green'>$year</span></span>
                    <span class='button_type_4 btn_month_right' data-curmonth='$month' data-curyear='$year'>></span>
                </div>
                <div class='calendar_header'>
                    <span>Monday</span><span>Tuesday</span><span>Wednesday</span><span>Thursday</span><span>Friday</span>
                    <span>Saturday</span><span>Sunday</span>
                </div>
                <div class='display_section_content'>
                    <div class='calendar_container'>
                        $calendarView
                    </div>
                </div>
        HTML;
    }

    if (!is_array($employeeInfo)) {
        die();
    }

    if (count($employeeInfo) <= 0) {
        die();
    }

    //$dateString = '04/09/2023';
    //$dateTime = DateTime::createFromFormat('m/d/Y', $dateString);

    /*if (HolidayChecker::IsHoliday($dateTime)) {
        error_log($dateString . " is a holiday");
    } else {
        error_log($dateString . " is not a holiday");
    }*/

    $returnedCode .= <<<HTML
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
            SetLoadingIcon('.calendar_loading');
            $('.calendar_loading').fadeIn(100);
            CancelAllAjaxCalls();
            var eid = `$_employeeid`;
            var requestData = [
                    {name: 'action', value: 'getmonthschedule'},
                    {name: 'month', value: month},
                    {name: 'year', value: year},
                    {name: 'eid', value: eid}
                ];
                CancelAllAjaxCalls();
                AjaxCall(xhrArray, requestData, function(status, response) {
                    if (status) {
                        $('.calendar_container').html(response);
                        $('.calendar_loading').hide();
                    }
                });
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
            SetLoadingIcon('.calendar_loading');
            $('.calendar_loading').fadeIn(100);
            CancelAllAjaxCalls();
            var eid = `$_employeeid`;
            var requestData = [
                    {name: 'action', value: 'getmonthschedule'},
                    {name: 'month', value: month},
                    {name: 'year', value: year},
                    {name: 'eid', value: eid}
                ];
                CancelAllAjaxCalls();
                AjaxCall(xhrArray, requestData, function(status, response) {
                    if (status) {
                        $('.calendar_container').html(response);
                        $('.calendar_loading').hide();
                    }
                });
        });
    </script>

    <div class ='display_container'>
        <div class='display_header'>
            <span class='textcolor_green'>Employee:</span> &nbsp; $employeeInfo[firstname] $employeeInfo[lastname]
        </div>
        <div class='display_row'>
            <div class='display_section'>
                <div class='display_section_header'>
                    Employee Details
                </div>
                <div class='display_section_content'>
                    <table class='table_employeedetails'>
                        <tbody>
                            <tr>
                                <td style='justify-content:right;'>
                                    <span class='textcolor_green'>Name</span>
                                </td>
                                <td>
                                    $employeeInfo[firstname] $employeeInfo[lastname]
                                </td>
                            </tr>
                            <tr>
                                <td style='justify-content:right;'>
                                    <span class='textcolor_green'>Address</div>
                                </td>
                                <td>
                                    $employeeInfo[street1] $employeeInfo[street2] $employeeInfo[city], $employeeInfo[state] $employeeInfo[zipcode]
                                </td>
                            </tr>
                            <tr>
                                <td style='justify-content:right;'>
                                    <span class='textcolor_green'>Role</span>
                                </td>
                                <td>
                                    $roleInfo[name]
                                </td>
                            </tr>
                            <tr>
                                <td style='justify-content:right;'>
                                    <span class='textcolor_green'>Shift</div>
                                </td>
                                <td>
                                    $shiftInfo[name]
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class='display_section'>
                <div class='display_section_header'>
                    Contact Information
                </div>
                <div class='display_section_content'>
                    <table class='table_employeedetails'>
                        <tbody>
                            <tr>
                                <td style='justify-content:right;'>
                                    <span class='textcolor_green'>Work Email</span>
                                </td>
                                <td>
                                    $employeeInfo[workemail]
                                </td>
                            </tr>
                            <tr>
                                <td style='justify-content:right;'>
                                    <span class='textcolor_green'>Personal Email</span>
                                </td>
                                <td>
                                    $employeeInfo[email]
                                </td>
                            </tr>
                            <tr>
                                <td style='justify-content:right;'>
                                    <span class='textcolor_green'>Primary Phone</span>
                                </td>
                                <td>
                                    $employeeInfo[phone]
                                </td>
                            </tr>
                            <tr>
                                <td style='justify-content:right;'>
                                    <span class='textcolor_green'>Work Phone</span>
                                </td>
                                <td>
                                    $employeeInfo[workphone]
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class='display_row'>
            <div class='display_section_nowrap'>
                <div class='display_section_header'>
                    Schedule
                </div>
                    $scheduleTemplate
            </div>
            <div class='display_section_nowrap'>
                <div class='display_section_header'>
                    TODO
                </div>
                <div class='display_section_content'>
                </div>
            </div>
        </div>
    </div>
    HTML;

    return $returnedCode;
  }
}
?>