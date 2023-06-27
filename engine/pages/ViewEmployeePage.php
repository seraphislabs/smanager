<?php
class PageViewEmployee {
   public static function Generate($_dbInfo, $_postData) {

    $_employeeid = $_postData['employeeid'];
    $returnedCode = "";
    $returnedCode .= "<script>history.pushState(null, null, '/index.php?page=ViewEmployee&employeeid=$_employeeid');</script>";
    // Permission Check

    $employeeInfo = DatabaseManager::GetEmployee($_dbInfo, $_employeeid);
    if ($employeeInfo == null) {
        $returnedCode = "<script>ClickLeftPaneMenuItem('ViewEmployees', true);</script>";
        return $returnedCode;
    }
    $shiftInfo = DatabaseManager::GetEmployeeShift($_dbInfo, $employeeInfo['shift']);
    $roleInfo = DatabaseManager::GetEmployeeRole($_dbInfo, $employeeInfo['role']);

    $year = date('Y');
    $month = date('m');

    $calendarView = "";
    $scheduleTemplate = "<center>You do not have permission to view this employee's schedule.</center>";
    $canviewschedule = true;
    if (!DatabaseManager::CheckPermissions($_dbInfo, ['ves'])) {
        if ($_SESSION['eid'] != $_employeeid) {
            $canviewschedule = false;
        }
    }

    if ($canviewschedule) {
        $postData = [];
        $postData['month'] = $month;
        $postData['year'] = $year;
        $postData['eid'] = $_employeeid;
        $scheduleTemplate = Calendar::Init($_dbInfo, $postData);
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

    $returnedCode .= <<<HTML
    <script>
        var xdata = [
            {name: 'month', value: $month},
            {name: 'year', value: $year},
            {name: 'eid', value: $_employeeid},
        ];
        Action_GenerateCalendar(xdata);
    </script>
    HTML;

    return $returnedCode;
  }
}
?>