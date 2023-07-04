<?php

trait DatabaseEmployees {
    public static function CheckForEmptyPunch($_dbInfo, $_employeeid) {
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $results = $db2->fetchAll("SELECT * FROM `punches` WHERE `employeeid` = :employeeid AND `timeout` IS NULL", ["employeeid" => $_employeeid]);

        if (count($results) > 0) {
            $formattedResult = [];

            $timeIn = "";
            $timeOut = "";
            if ($results[0]['timein'] != null)
            {
                $timeIn = DateTime::createFromFormat('H:i:s', $results[0]['timein'])->format('h:i A');
            }
            if ($results[0]['timeout'] != null)
            {
                $timeOut = DateTime::createFromFormat('H:i:s', $results[0]['timeout'])->format('h:i A');
            }

            $formattedResult['timein'] = $timeIn;
            $formattedResult['timeout'] = $timeOut;
            $formattedResult['date'] = $results[0]['date'];
            $formattedResult['id'] = $results[0]['id'];

            OpLog::Log("Database: CheckForEmptyPunch");
            OpLog::Log("--Returned: Array containing " . count($formattedResult) . " elements");
            return $formattedResult;
        }

        OpLog::Log("Database: CheckForEmptyPunch");
        OpLog::Log("--Returned: null: No empty punches found");
        return null;
    }

    public static function GetPunches($_dbInfo, $_employeeid, $_date) {

        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $_dbDate = date('Y-m-d', strtotime($_date));
    
        $results = $db2->fetchAll("SELECT * FROM `punches` WHERE `employeeid` = :employeeid AND DATE(`date`) = :date", ["employeeid" => $_employeeid, "date" => $_dbDate]);

        if (count($results) > 0) {
            $retVal = [];
            $retVal['punches'] = array();

            $runningCount = 0;

            foreach ($results as $result) {
                $formattedResult = [];

                $timeIn = "";
                $timeOut = "";
                if ($result['timein'] != null) {
                    $timeIn = DateTime::createFromFormat('H:i:s', $result['timein']);
                }
                if ($result['timeout'] != null) {
                    $timeOut = DateTime::createFromFormat('H:i:s', $result['timeout']);
                }

                $formattedResult['timein'] = $timeIn ? $timeIn->format('h:i A') : "";
                $formattedResult['timeout'] = $timeOut ? $timeOut->format('h:i A') : "";
                $formattedResult['date'] = $result['date'];
                $formattedResult['id'] = $result['id'];

                if ($timeIn && $timeOut) {
                    $timeDifference = $timeOut->diff($timeIn);
                    $runningCount += $timeDifference->h * 3600 + $timeDifference->i * 60;
                }

                array_push($retVal['punches'], $formattedResult);
            }

            $totalHours = floor($runningCount / 3600);
            $totalMinutes = floor(($runningCount % 3600) / 60);
            $totalTimeString = sprintf("%02d:%02d", $totalHours, $totalMinutes);
            $retVal['totalhours'] = $totalTimeString;

            OpLog::Log("Database: GetPunches");
            OpLog::Log("--Returned: Array containing " . count($retVal) . " elements");
            return $retVal;
        } else {
            // No punches on date
            OpLog::Log("Database: GetPunches");
            OpLog::Log("--Returned: null: No punches found");
            return null;
        }
    }

    public static function AddPunch($_dbInfo) { 
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $retVar = [];

        $retVar['success'] = false;
        $retVar['response'] = "Database Error";

        $date = date('Y-m-d');
        $currentTime = date('H:i:s');

        $missingPunch = self::CheckForEmptyPunch($_dbInfo, $ai['id']);

        if ($missingPunch != null) {
            if (count($missingPunch) > 0) {
                $sqlData = [
                    "timeout" => $currentTime
                ];

                $cond = [
                    ["id", "=", $missingPunch['id']],
                ];

                $db2->update("punches", $sqlData, $cond);
            }
        }
        else {
            $sqlData = [
                "employeeid" => $ai['id'],
                "date" => $date,
                "timein" => $currentTime
            ];

            $db2->insert("punches", $sqlData);
        }

        OpLog::Log("Database: AddPunch");
        OpLog::Log("--Returned: Array containing " . count($retVar) . " elements");
        return $retVar;
    }

    public static function GetSetSchedule($_dbInfo, $_employeeid) {
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $sqlData = [
            "employeeid" => $_employeeid
        ];
        $results = $db2->fetchAll("SELECT * FROM `set_schedule` WHERE `employeeid` = :employeeid", $sqlData);

        if (count($results) > 0) {
            $retVal = array();
            foreach($results as $result) {
                $formattedResult = [];
                $formattedResult['timein'] = DateTime::createFromFormat('H:i:s', $result['timein'])->format('h:i A');
                $formattedResult['timeout'] = DateTime::createFromFormat('H:i:s', $result['timeout'])->format('h:i A');

                $retVal[$result['date']] = $formattedResult;
            }

            OpLog::Log("Database: GetSetSchedule");
            OpLog::Log("--Returned: Array containing " . count($retVal) . " elements");
            return $retVal;
        }

        OpLog::Log("Database: GetSetSchedule");
        OpLog::Log("--Returned: null");
        return [];
    }

    public static function AddSetSchedule($_dbInfo, $_formInformation) {
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $timeIn = "00:00 AM";
        $timeOut = "00:00 AM";
        $timeInFixed = "00:00:00";
        $timeOutFixed = "00:00:00";

        if ($_formInformation['timein'] != "" && $_formInformation['timeout'] != "") {
            $timeIn = $_formInformation['timein'];
            $timeOut = $_formInformation['timeout'];
            $timeInFixed = date("H:i:s", strtotime($timeIn));
            $timeOutFixed = date("H:i:s", strtotime($timeOut));

        }

        $employeeInfo = self::GetEmployee($_dbInfo, $_formInformation['employeeid']);
        $regularSchedule = self::GetEmployeeShift($_dbInfo, $employeeInfo['shift']);

        $retVar = [];

        $retVar['success'] = false;
        $retVar['response'] = "Database Error";

        if (!ValidateField::Validate($timeIn, 'time') || !ValidateField::Validate($timeOut, 'time')) {
            $retVar['success'] = false;
            $retVar['response'] = "Validation Error";
            OpLog::Log("Database: AddSetSchedule");
            OpLog::Log("--Returned: Failed Validation");
            return $retVar;
        }

        if (!self::CheckPermission('ees')) {
            $retVar['success'] = false;
            $retVar['response'] = "You do not have permission to view this page. Speak to your account manager to gain access.";
            OpLog::Log("Database: AddSetSchedule");
            OpLog::Log("--Returned: Failed Permissions Check");
            return $retVar;
        }

        $_formInformation['date'] = str_replace(' ', '', $_formInformation['date']);
        $_dbDate = date('Y-m-d', strtotime($_formInformation['date']));
        $dayOfWeek = strtolower(date('l', strtotime($_dbDate)));
        $splitTime = explode("|", $regularSchedule[$dayOfWeek]);

        $sqlData = [
            "employeeid" => $_formInformation['employeeid'],
            "date" => $_dbDate
        ];

        $result = $db2->fetch("SELECT * FROM `set_schedule` WHERE `employeeid` = :employeeid AND DATE(`date`) = :date", $sqlData);

        if (!empty($result)) {
            if (($splitTime[0] == $timeIn && $splitTime[1] == $timeOut) || (strlen($regularSchedule[$dayOfWeek]) == 0)) {

                $db2->delete("set_schedule", ["id" => $result['id']]);

                $retVar['success'] = true;
                $retVar['response'] = $_formInformation['date'];

                if ($timeInFixed == "00:00:00" && $timeOutFixed == "00:00:00")
                {
                    $retVar['time'] = "";
                }
                else {
                    $retVar['time'] = $timeIn . "<br/>" . $timeOut;
                }

                OpLog::Log("Database: AddSetSchedule");
                OpLog::Log("--Returned: Successfully deleted set schedule");

                return $retVar;
            }
            else {
                $sqlData = [
                    "timein" => $timeInFixed,
                    "timeout" => $timeOutFixed
                ];

                $cond = [
                    ["id", "=", $result['id']],
                ];

                $db2->update("set_schedule", $sqlData, $cond);

                $retVar['success'] = true;
                $retVar['response'] = $_formInformation['date'];

                if ($timeInFixed == "00:00:00" && $timeOutFixed == "00:00:00")
                {
                    $retVar['time'] = "";
                }
                else {
                    $retVar['time'] = $timeIn . "<br/>" . $timeOut;
                }

                OpLog::Log("Database: AddSetSchedule");
                OpLog::Log("--Returned: Successfully updated set schedule");  

                return $retVar;
            }
        }
        else {
            if ($splitTime[0] == $timeIn && $splitTime[1] == $timeOut) {
                $retVar['success'] = true;
                $retVar['response'] = $_formInformation['date'];
                $retVar['time'] = $timeIn . "<br/>" . $timeOut;

                OpLog::Log("Database: AddSetSchedule");
                OpLog::Log("--Returned: Did not need to add- matches schedule");
                return $retVar;
            }
            else {      

                if ($regularSchedule[$dayOfWeek] != "") {
                    $sqlData = [
                        "employeeid" => $_formInformation['employeeid'],
                        "date" => $_formInformation['date'],
                        "timein" => $timeInFixed,
                        "timeout" => $timeOutFixed
                    ];
                    $db2->insert("set_schedule", $sqlData);

                    $retVar['success'] = true;
                    $retVar['response'] = $_formInformation['date'];

                    if ($timeInFixed == "00:00:00" && $timeOutFixed == "00:00:00")
                    {
                        $retVar['time'] = "";
                    }
                    else {
                        $retVar['time'] = $timeIn . "<br/>" . $timeOut;
                    }

                    OpLog::Log("Database: AddSetSchedule");
                    OpLog::Log("--Returned: Successfully added new schedule");
                    return $retVar;
                }
                else {
                    $retVar['success'] = true;
                    $retVar['response'] = $_formInformation['date'];
                    if ($timeInFixed == "00:00:00" && $timeOutFixed == "00:00:00")
                    {
                        $retVar['time'] = "";
                    }
                    else {
                        $retVar['time'] = $timeIn . "<br/>" . $timeOut;
                    }

                    OpLog::Log("Database: AddSetSchedule");
                    OpLog::Log("--Returned: Successfully added new schedule");
                    return $retVar;
                }
            }
        }
    }

    public static function GetAllEmployees($_dbInfo) {
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $results = $db2->fetchAll("SELECT * FROM `users`");

        if (count($results) > 0) {
            $retVal = $results;
            OpLog::Log("Database: GetAllEmployees");
            OpLog::Log("--Returned: Array containing " . count($results) . " elements");
            return $retVal;
        }

        OpLog::Log("Database: GetAllEmployees");
        OpLog::Log("--Returned: null");
        return null;
    }

    public static function GetEmployee($_dbInfo, $_employeeid) {
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $result = $db2->fetch("SELECT * FROM `users` WHERE `id` = :employeeid", ["employeeid" => $_employeeid]);

        if (empty($result)) {
            OpLog::Log("Database: GetEmployee");
            OpLog::Log("--Returned: null");
            return null;
        }

        OpLog::Log("Database: GetEmployee");
        OpLog::Log("--Returned: Array");
        return $result;
    }

    public static function AddNewEmployee($_dbInfo, $_formInformation)
    {
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $retVar = [];
        $retVar['success'] = true;
        $retVar['response'] = "Success";

        $employeeInformation = $_formInformation['employeeInformation'];
        if (!is_array($employeeInformation)) {
            OpLog::Log("Database: AddNewEmployee");
            OpLog::Log("**Failed employee is not an array");
            return false;
        }

        if (!self::CheckPermission('ce')) {
            $retVar['success'] = false;
            $retVar['response'] = "You do not have permission to view this page. Speak to your account manager to gain access.";
            OpLog::Log("Database: AddNewEmployee");
            OpLog::Log("--Returned: Failed permissions check");
            return $retVar;
        }

        $validationFields = [
            "firstName" => "name",
            "lastName" => "name",
            "dob" => "date_full",
            "role" => "selectnumvalue",
            "shift" => "selectnumvalue",
            "street1" => "address",
            "street2" => "address_nonrequired",
            "city" => "name",
            "state" => "state",
            "zipCode" => "zipCode",
            "phone" => "phone_nonrequired",
            "email" => "email",
            "workPhone" => "phone_nonrequired",
            "workEmail" => "email",
            "dlNumber" => "name",
            "dlExpiration" => "date_my",
            "pvMake" => "name",
            "pvModel" => "name",
            "pvColor" => "name",
            "pvPlate" => "name_nonrequired",
            "pvYear" => "year",
            "cvMake" => "name",
            "cvModel" => "name",
            "cvYear" => "year",
            "cvVID" => "name",
            "cvPlate" => "name",
            "cvRegExp" => "date_my",
        ];

        foreach($validationFields as $field => $validationType) {
            if (array_key_exists($field, $employeeInformation))
            if (!ValidateField::Validate($employeeInformation[$field], $validationType)) {
                $retVar['success'] = false;
                $retVar['response'] = "Validation Failed";
                OpLog::Log("Database: AddNewEmployee");
                OpLog::Log("--Returned: Failed validation");
                return $retVar;
            }
        }

        $sqlData = [
            "password" => PasswordEncrypt::Encrypt('test'),
            "firstname" => $employeeInformation['firstName'],
            "lastname" => $employeeInformation['lastName'],
            "dob" => date('Y-m-d', strtotime($employeeInformation['dob'])),
            "email" => $employeeInformation['email'],
            "companyid" => $ai['id'],
            "role" => $employeeInformation['role'],
            "shift" => $employeeInformation['shift'],
            "street1" => $employeeInformation['street1'],
            "street2" => $employeeInformation['street2'],
            "city" => $employeeInformation['city'],
            "state" => $employeeInformation['state'],
            "zipcode" => $employeeInformation['zipCode'],
            "phone" => $employeeInformation['phone'],
            "workphone" => $employeeInformation['workPhone'],
            "workemail" => $employeeInformation['workEmail'],
            "dlnumber" => $employeeInformation['dlNumber'] ?? '',
            "dlexpiration" => $employeeInformation['dlExpiration'] ?? '',
            "cvmake" => $employeeInformation['cvMake'] ?? '',
            "cvmodel" => $employeeInformation['cvModel'] ?? '',
            "cvvin" => $employeeInformation['cvVID'] ?? '',
            "cvplate" => $employeeInformation['cvPlate'] ?? '',
            "cvyear" => $employeeInformation['cvYear'] ?? '',
            "svregexp" => $employeeInformation['cvRegExp'] ?? '',
            "pvmake" => $employeeInformation['pvMake'] ?? '',
            "pvmodel" => $employeeInformation['pvModel'] ?? '',
            "pvcolor" => $employeeInformation['pvColor'] ?? '',
            "pvplate" => $employeeInformation['pvPlate'] ?? '',
            "pvyear" => $employeeInformation['pvYear'] ?? ''
        ];

        $eid = $db2->insert("users", $sqlData);

        OpLog::Log("Database: AddNewEmployee");
        OpLog::Log("--Returned: Successfully added employee");
        return $retVar;
    }
}

?>