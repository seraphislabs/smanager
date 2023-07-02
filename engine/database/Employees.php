<?php

trait DatabaseEmployees {
    public static function CheckForEmptyPunch($_dbInfo, $_employeeid) {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        if (self::ValidateLogin($pdo1)) {
            $stmt = $pdo->prepare("SELECT * FROM `punches` WHERE `employeeid` = :employeeid AND `timeout` IS NULL");
            $stmt->bindParam(":employeeid", $_employeeid);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

                $pdo1 = null;
                $pdo = null;
                return $formattedResult;
            }
        }

        $pdo1 = null;
        $pdo = null;
        return null;
    }

    public static function GetPunches($_dbInfo, $_employeeid, $_date) {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $_dbDate = date('Y-m-d', strtotime($_date));
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);
    
        if (self::ValidateLogin($pdo1)) {
            $stmt = $pdo->prepare("SELECT * FROM `punches` WHERE `employeeid` = :employeeid AND DATE(`date`) = :date");
            $stmt->bindParam(":employeeid", $_employeeid);
            $stmt->bindParam(":date", $_dbDate);
            $stmt->execute();
    
            if ($stmt->rowCount() > 0) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    
                $pdo1 = null;
                $pdo = null;
                return $retVal;
            } else {
                // No punches on date
                $pdo1 = null;
                $pdo = null;
                return null;
            }
        }
    }

    public static function AddPunch($_dbInfo) {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);
        $retVar = [];

        $retVar['success'] = false;
        $retVar['response'] = "Database Error";

        $date = date('Y-m-d');
        $currentTime = date('H:i:s');

        if (self::ValidateLogin($pdo1)) {
            $missingPunch = self::CheckForEmptyPunch($_dbInfo, $_SESSION['employeeid']);

            if ($missingPunch != null) {
                $stmt = $pdo->prepare("UPDATE `punches` SET `timeout` = :timeout WHERE `id` = :id");
                $stmt->bindParam(":timeout", $currentTime);
                $stmt->bindParam(":id", $missingPunch['id']);
                $stmt->execute();
            }
            else {
                $stmt = $pdo->prepare("INSERT INTO `punches` (`employeeid`, `date`, `timein`) VALUES (:employeeid, :date, :timein)");
                $stmt->bindParam(":employeeid", $_SESSION['employeeid']);
                $stmt->bindParam(":date", $date);
                $stmt->bindParam(":timein", $currentTime);
                $stmt->execute();
            }
        }

        $pdo1 = null;
        $pdo = null;
        return $retVar;
    }

    public static function GetSetSchedule($_dbInfo, $_employeeid) {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        if (self::ValidateLogin($pdo1)) {
            $stmt = $pdo->prepare("SELECT * FROM `set_schedule` WHERE `employeeid` = :employeeid");
            $stmt->bindParam(":employeeid", $_employeeid);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $retVal = array();
                foreach($results as $result) {
                    $formattedResult = [];
                    $formattedResult['timein'] = DateTime::createFromFormat('H:i:s', $result['timein'])->format('h:i A');
                    $formattedResult['timeout'] = DateTime::createFromFormat('H:i:s', $result['timeout'])->format('h:i A');

                    $retVal[$result['date']] = $formattedResult;
                }
                $pdo1 = null;
                $pdo = null;
                return $retVal;
            }
        }

        $pdo1 = null;
        $pdo = null;
        return [];
    }

    public static function AddSetSchedule($_dbInfo, $_formInformation) {
        $_companyid = $_SESSION['companyid'];

        $timeIn = "00:00:00";
        $timeOut = "00:00:00";
        $timeInFixed = "00:00:00";
        $timeOutFixed = "00:00:00";

        if ($_formInformation['timein'] != "" && $_formInformation['timeout'] != "") {
            $timeIn = $_formInformation['timein'];
            $timeOut = $_formInformation['timeout'];
            $timeInFixed = date("H:i:s", strtotime($timeIn));
            $timeOutFixed = date("H:i:s", strtotime($timeOut));
        }

        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        $employeeInfo = self::GetEmployee($_dbInfo, $_formInformation['employeeid']);
        $regularSchedule = self::GetEmployeeShift($_dbInfo, $employeeInfo['shift']);

        $retVar = [];

        $retVar['success'] = false;
        $retVar['response'] = "Database Error";

        if (!ValidateField::Validate($timeIn, 'time') || !ValidateField::Validate($timeOut, 'time')) {
            $retVar['success'] = false;
            $retVar['response'] = "Validation Error";
        }

        if (self::ValidateLogin($pdo1)) {
            if (!self::CheckPermissions($_dbInfo, ['ees'])) {
                $retVar['success'] = false;
                $retVar['response'] = "You do not have permission to view this page. Speak to your account manager to gain access.";
                $pdo1 = null;
                $pdo = null;
                return $retVar;
            }

            $_formInformation['date'] = str_replace(' ', '', $_formInformation['date']);
            $_dbDate = date('Y-m-d', strtotime($_formInformation['date']));
            $dayOfWeek = strtolower(date('l', strtotime($_dbDate)));
            $splitTime = explode("|", $regularSchedule[$dayOfWeek]);

            $stmt = $pdo->prepare("SELECT * FROM `set_schedule` WHERE `employeeid` = :employeeid AND DATE(`date`) = :date");
            $stmt->bindParam(":employeeid", $_formInformation['employeeid']);
            $stmt->bindParam(":date", $_dbDate);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (($splitTime[0] == $timeIn && $splitTime[1] == $timeOut) || (strlen($regularSchedule[$dayOfWeek]) == 0)) {
                    $stmt = $pdo->prepare("DELETE FROM `set_schedule` WHERE `id` = :id");
                    $stmt->bindParam(":id", $result['id']);
                    $stmt->execute();
                    $retVar['success'] = true;
                    $retVar['response'] = $_formInformation['date'];

                    if ($timeIn == "00:00:00" && $timeOut == "00:00:00")
                    {
                        $retVar['time'] = "";
                    }
                    else {
                        $retVar['time'] = $timeIn . "<br/>" . $timeOut;
                    }
                }
                else {
                    $stmt = $pdo->prepare("UPDATE `set_schedule` SET `timein` = :timein, `timeout` = :timeout WHERE `id` = :id");
                    $stmt->bindParam(":timein", $timeInFixed);
                    $stmt->bindParam(":timeout", $timeOutFixed);
                    $stmt->bindParam(":id", $result['id']);
                    try {
                        if ($stmt->execute()) {
                            $retVar['success'] = true;
                            $retVar['response'] = $_formInformation['date'];

                            if ($timeIn == "00:00:00" && $timeOut == "00:00:00")
                            {
                                $retVar['time'] = "";
                            }
                            else {
                                $retVar['time'] = $timeIn . "<br/>" . $timeOut;
                            }
                        }
                    } catch (PDOException $e) {
                        // Handle the exception here
                        $retVar['success'] = false;
                        $retVar['response'] = $e->getMessage();
                    }
                }
            }
            else {
                if ($splitTime[0] == $timeIn && $splitTime[1] == $timeOut) {
                    $retVar['success'] = true;
                    $retVar['response'] = $_formInformation['date'];
                    $retVar['time'] = $timeIn . "<br/>" . $timeOut;
                }
                else {      

                    if ($regularSchedule[$dayOfWeek] != "") {
                        $stmt = $pdo->prepare("INSERT INTO `set_schedule` (`employeeid`,`date`,`timein`,`timeout`) 
                        VALUES (:employeeid,:date,:timein,:timeout)");

                        $stmt->bindParam(":employeeid", $_formInformation['employeeid']);
                        $stmt->bindParam(":date", $_formInformation['date']);

                        $stmt->bindParam(":timein", $timeInFixed);
                        $stmt->bindParam(":timeout", $timeOutFixed);

                        try {
                            if ($stmt->execute()) {
                                $retVar['success'] = true;
                                $retVar['response'] = $_formInformation['date'];

                                if ($timeIn == "00:00:00" && $timeOut == "00:00:00")
                                {
                                    $retVar['time'] = "";
                                }
                                else {
                                    $retVar['time'] = $timeIn . "<br/>" . $timeOut;
                                }
                            }
                        } catch (PDOException $e) {
                            // Handle the exception here
                            $retVar['success'] = false;
                            $retVar['response'] = $e->getMessage();
                        }
                    }
                    else {
                        $retVar['success'] = true;
                        $retVar['response'] = $_formInformation['date'];
                        if ($timeIn == "00:00:00" && $timeOut == "00:00:00")
                                {
                                    $retVar['time'] = "";
                                }
                                else {
                                    $retVar['time'] = $timeIn . "<br/>" . $timeOut;
                                }
                    }
                }
            }
        }

        $pdo1 = null;
        $pdo = null;
        return $retVar;
    }

    public static function GetAllEmployees($_dbInfo) {
        $_companyid = $_SESSION['companyid'];
        $pdo = self::connect($_dbInfo, 'servicemanager');

        if (self::ValidateLogin($pdo)) {
            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `companyid` = :companyid");
            $stmt->bindParam(":companyid", $_companyid);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $retVal = $results;
                $pdo = null;
                return $retVal;
            }
        }

        $pdo = null;
        return null;
    }

    public static function GetEmployee($_dbInfo, $_employeeid) {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $pdo = self::connect($_dbInfo, 'servicemanager');

        if (self::ValidateLogin($pdo)) {
            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `id` = :employeeid");
            $stmt->bindParam(":employeeid", $_employeeid);
            $stmt->execute();

            if ($stmt->rowCount() <= 0) {
                $pdo = null;
                return null;
            }
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            $pdo = null;
            return $results;
        }
        return false;
    }

    public static function AddNewEmployee($_dbInfo, $_formInformation)
    {
        $pdo = self::connect($_dbInfo, 'servicemanager');

        $retVar = [];
        $retVar['success'] = true;
        $retVar['response'] = "Success";

        if (self::ValidateLogin($pdo)) {
            $employeeInformation = $_formInformation['employeeInformation'];
            if (!is_array($employeeInformation)) {
                $pdo = null;
                return false;
            }

            if (!self::CheckPermissions($_dbInfo, ['ce'])) {
                $retVar['success'] = false;
                $retVar['response'] = "You do not have permission to view this page. Speak to your account manager to gain access.";
                $pdo = null;
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
                    $pdo = null;
                    return $retVar;
                }
            }

            // Create Contact for Primary Contact
            $stmt = $pdo->prepare(
                "INSERT INTO `users` (
                    `password`,
                    `email`,
                    `companyid`,
                    `firstname`,
                    `lastname`,
                    `dob`,
                    `role`,
                    `shift`,
                    `street1`,
                    `street2`,
                    `city`,
                    `state`,
                    `zipcode`,
                    `phone`,
                    `workphone`,
                    `workemail`,
                    `roledetails`,
                    `dlnumber`,
                    `dlexpiration`,
                    `cvmake`,
                    `cvmodel`,
                    `cvvin`,
                    `cvplate`,
                    `cvyear`,
                    `svregexp`,
                    `pvmake`,
                    `pvmodel`,
                    `pvcolor`,
                    `pvplate`,
                    `pvyear`
                ) VALUES (
                    :password,
                    :email,
                    :companyid,
                    :firstname,
                    :lastname,
                    :dob,
                    :role,
                    :shift,
                    :street1,
                    :street2,
                    :city,
                    :state,
                    :zipcode,
                    :phone,
                    :workphone,
                    :workemail,
                    '',
                    :dlnumber,
                    :dlexpiration,
                    :cvmake,
                    :cvmodel,
                    :cvvin,
                    :cvplate,
                    :cvyear,
                    :svregexp,
                    :pvmake,
                    :pvmodel,
                    :pvcolor,
                    :pvplate,
                    :pvyear
                )"
            );
            
            $tempRole = $employeeInformation['role'];
            $tempShift = $employeeInformation['shift'];
            $tempPassword = PasswordEncrypt::Encrypt('test');
            $stmt->bindParam(':password', $tempPassword);
            $stmt->bindParam(':firstname', $employeeInformation['firstName']);
            $stmt->bindParam(':lastname', $employeeInformation['lastName']);
            $xdob = date('Y-m-d', strtotime($employeeInformation['dob']));
            $stmt->bindParam(':dob', $xdob);
            $stmt->bindParam(':email', $employeeInformation['email']);
            $stmt->bindParam(':companyid', $_SESSION['companyid']);
            $stmt->bindParam(':role', $tempRole);
            $stmt->bindParam(':shift', $tempShift);
            $stmt->bindParam(':street1', $employeeInformation['street1']);
            $stmt->bindParam(':street2', $employeeInformation['street2']);
            $stmt->bindParam(':city', $employeeInformation['city']);
            $stmt->bindParam(':state', $employeeInformation['state']);
            $stmt->bindParam(':zipcode', $employeeInformation['zipCode']);
            $stmt->bindParam(':phone', $employeeInformation['phone']);
            $stmt->bindParam(':workphone', $employeeInformation['workPhone']);
            $stmt->bindParam(':workemail', $employeeInformation['workEmail']);
            $xdlnumber = $employeeInformation['dlNumber'] ?? '';
            $stmt->bindParam(':dlnumber', $xdlnumber);
            $xdlexpiration = $employeeInformation['dlExpiration'] ?? '';
            $stmt->bindParam(':dlexpiration', $xdlexpiration);
            $xcvmake = $employeeInformation['cvMake'] ?? '';
            $stmt->bindParam(':cvmake', $xcvmake);
            $xcvmodel = $employeeInformation['cvModel'] ?? '';
            $stmt->bindParam(':cvmodel', $xcvmodel);
            $xcvvin = $employeeInformation['cvVID'] ?? '';
            $stmt->bindParam(':cvvin', $xcvvin);
            $xcvplate = $employeeInformation['cvPlate'] ?? '';
            $stmt->bindParam(':cvplate', $xcvplate);
            $xcvyear = $employeeInformation['cvYear'] ?? '';
            $stmt->bindParam(':cvyear', $xcvyear);
            $xsvregexp = $employeeInformation['cvRegExp'] ?? '';
            $stmt->bindParam(':svregexp', $xsvregexp);
            $pvmake = $employeeInformation['pvMake'] ?? '';
            $stmt->bindParam(':pvmake', $pvmake);
            $pvmodel = $employeeInformation['pvModel'] ?? '';
            $stmt->bindParam(':pvmodel', $pvmodel);
            $pvcolor = $employeeInformation['pvColor'] ?? '';
            $stmt->bindParam(':pvcolor', $pvcolor);
            $pvplate = $employeeInformation['pvPlate'] ?? '';
            $stmt->bindParam(':pvplate', $pvplate);
            $pvyear = $employeeInformation['pvYear'] ?? '';
            $stmt->bindParam(':pvyear', $pvyear);
            $stmt->execute();
            //Contact ID
            $eid = $pdo->lastInsertId();

            $pdo = null;
            return $retVar;
        }
    }
}

?>