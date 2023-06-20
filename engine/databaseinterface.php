<?php

class DatabaseManager
{
    public static function connect($_dbInfo, $_dbname)
    {
        $dsn = "mysql:host=localhost;dbname={$_dbname}";

        try {
            $pdo = new PDO($dsn, $_dbInfo['dusername'], $_dbInfo['dpassword']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function ManuallyValidateLogin($_dbInfo)
    {
        $_password = $_SESSION['password'];

        $pdo = self::connect($_dbInfo, 'servicemanager');
        $retVal = self::GetLoginPasswordHash($pdo);
        if (!empty($retVal)) {
            if (PasswordEncrypt::Check($_password, $retVal)) {
                $pdo = null;
                return true;
            }
        }

        return false;
    }

    private static function ValidateLogin($_pdo)
    {
        $_password = $_SESSION['password'];

        // Retreive password hash from database
        $retVal = self::GetLoginPasswordHash($_pdo);
        if (!empty($retVal)) {
            if (PasswordEncrypt::Check($_password, $retVal)) {
                return true;
            }
        }

        return false;
    }

    private static function GetLoginPasswordHash($_pdo)
    {
        $_email = $_SESSION['email'];
        $stmt = $_pdo->prepare("SELECT * FROM `users` WHERE `workemail`=:email");
        $stmt->bindParam(":email", $_email);
        $stmt->execute();
        if ($stmt->rowCount() == 1) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $passHash = $results[0]['password'];

            return $passHash;
        }
    }

    public static function GetLoginInformation($_dbInfo)
    {
        $_email = $_SESSION['email'];
        $pdo = self::connect($_dbInfo, 'servicemanager');

        if (self::ValidateLogin($pdo)) {
            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `workemail`=:email");
            $stmt->bindParam(":email", $_email);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $pdo = null;
                return $results[0];
            }
        }
    }

    public static function GetUserPermissions($_dbInfo)
    {
        $_companyid = $_SESSION['companyid'];

        $ccid = $_companyid + 1000;
        $_email = $_SESSION['email'];
        $pdo = self::connect($_dbInfo, 'servicemanager');
        $pdo2 = self::connect($_dbInfo, "company_" . $ccid);

        if (self::ValidateLogin($pdo)) {
            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `workemail`=:email");
            $stmt->bindParam(":email", $_email);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $roleid = $results[0]['role'];

                $stmt2 = $pdo2->prepare("SELECT * FROM `roles` WHERE `id`=:roleid");
                $stmt2->bindParam(":roleid", $roleid);
                $stmt2->execute();

                $permString = "";

                if ($stmt2->rowCount() == 1) {
                    $results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                    $permString = $results2[0]['permissions'];
                }

                $pdo = null;
                $pdo2 = null;
                $retArray = explode("|", $permString);
                return $retArray;
            }
        }
    }

    public static function ManuallyCheckPermissions($_userPerms, $_perms) {
        $diff = array_diff($_perms, $_userPerms);

        if (empty($diff)) {
            return true;
        }

        return false;
    }

    public static function CheckPermissions($_dbInfo, $_perms)
    {
        $perms = self::GetUserPermissions($_dbInfo);

        $diff = array_diff($_perms, $perms);

        if (empty($diff)) {
            return true;
        }

        return false;
    }

    public static function GetEmployeeAccounts($_dbInfo) {
        $_companyid = $_SESSION['companyid'];
        $pdo = self::connect($_dbInfo, 'servicemanager');

        if (self::ValidateLogin($pdo)) {
            $stmt = $pdo->prepare("SELECT * FROM `accounts` ORDER BY `id` WHERE `companyid` = :companyid AND `role` = 'employee'");
            $stmt->bindParam(":companyid", $_companyid);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $retVal = $results;
            }
        }
    }

    public static function GetShifts($_dbInfo) {
        $_companyid = $_SESSION['companyid'];

        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        $retVal = null;

        if (self::ValidateLogin($pdo1)) {
            $stmt = $pdo->prepare("SELECT * FROM `shifts` ORDER BY `id`");
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $retVal = $results;
            }
        }
        $pdo1 = null;
        $pdo = null;
        return $retVal;
    }

    public static function GetRoles($_dbInfo) {
        $_companyid = $_SESSION['companyid'];

        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        $retVal = null;

        if (self::ValidateLogin($pdo1)) {
            $stmt = $pdo->prepare("SELECT * FROM `roles` ORDER BY `id`");
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $retVal = $results;
            }
        }
        $pdo1 = null;
        $pdo = null;
        return $retVal;
    }

    public static function GetAccounts($_dbInfo)
    {
        $_companyid = $_SESSION['companyid'];

        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        $retVal = null;

        if (self::ValidateLogin($pdo1)) {
            $stmt = $pdo->prepare("SELECT * FROM `accounts` ORDER BY `id` LIMIT 10");
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $retVal = $results;
            }
        }
        $pdo1 = null;
        $pdo = null;
        return $retVal;
    }

    public static function GetShift($_dbInfo, $_shiftid) {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        if (self::ValidateLogin($pdo1)) {
            $stmt = $pdo->prepare("SELECT * FROM `shifts` WHERE `id` = :shiftid");
            $stmt->bindParam(":shiftid", $_shiftid);
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            $pdo1 = null;
            $pdo = null;

            return $results;
        }
        return false;
    }

    public static function GetRole($_dbInfo, $_roleid) {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        if (self::ValidateLogin($pdo1)) {
            $stmt = $pdo->prepare("SELECT * FROM `roles` WHERE `id` = :roleid");
            $stmt->bindParam(":roleid", $_roleid);
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            $pdo1 = null;
            $pdo = null;

            return $results;
        }
        return false;
    }

    public static function GetAccount($_dbInfo, $_accountid)
    {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        if (self::ValidateLogin($pdo1)) {
            $stmt = $pdo->prepare("SELECT * FROM `accounts` WHERE `id` = :id");
            $stmt->bindParam(":id", $_accountid);
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            $pdo1 = null;
            $pdo = null;

            return $results;
        }
        return false;
    }

    public static function GetContact($_dbInfo, $_contactid)
    {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        if (self::ValidateLogin($pdo1)) {
            $stmt = $pdo->prepare("SELECT * FROM `contacts` WHERE `id` = :id");
            $stmt->bindParam(":id", $_contactid);
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            $pdo1 = null;
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
                return false;
            }

            if (!self::CheckPermissions($_dbInfo, ['ce'])) {
                $retVar['success'] = false;
                $retVar['response'] = "You do not have permission to view this page. Speak to your account manager to gain access.";
                return $retVar;
            }

            $validationFields = [
                "firstName" => "name",
                "lastName" => "name",
                "dob" => "date_full",
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
                if (!self::ValidateField($employeeInformation[$field], $validationType)) {
                    $retVar['success'] = false;
                    $retVar['response'] = "Validation Failed";
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
            
            $tempRole = 7;
            $tempPassword = PasswordEncrypt::Encrypt('test');
            $stmt->bindParam(':password', $tempPassword);
            $stmt->bindParam(':firstname', $employeeInformation['firstName']);
            $stmt->bindParam(':lastname', $employeeInformation['lastName']);
            $xdob = date('Y-m-d', strtotime($employeeInformation['dob']));
            $stmt->bindParam(':dob', $xdob);
            $stmt->bindParam(':email', $employeeInformation['email']);
            $stmt->bindParam(':companyid', $_SESSION['companyid']);
            $stmt->bindParam(':role', $tempRole);
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

            return $retVar;
        }
    }

    public static function AddNewShift($_dbInfo, $_shiftInformation) {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        $shiftid = 0;
        if (array_key_exists('id', $_shiftInformation)) {
            $shiftid = (int)$_shiftInformation['id'];
        }

        $name = $_shiftInformation['name'];

        $mondayString = "";
        if (array_key_exists('monday', $_shiftInformation)) {
            $mondayString = $_shiftInformation['monday']['start'] . "|" . $_shiftInformation['monday']['end'];
        }
        $tuesdayString = "";
        if (array_key_exists('tuesday', $_shiftInformation)) {
            $tuesdayString = $_shiftInformation['tuesday']['start'] . "|" . $_shiftInformation['tuesday']['end'];
        }
        $wednesdayString = "";
        if (array_key_exists('wednesday', $_shiftInformation)) {
            $wednesdayString = $_shiftInformation['wednesday']['start'] . "|" . $_shiftInformation['wednesday']['end'];
        }
        $thursdayString = "";
        if (array_key_exists('thursday', $_shiftInformation)) {
            $thursdayString = $_shiftInformation['thursday']['start'] . "|" . $_shiftInformation['thursday']['end'];
        }
        $fridayString = "";
        if (array_key_exists('friday', $_shiftInformation)) {
            $fridayString = $_shiftInformation['friday']['start'] . "|" . $_shiftInformation['friday']['end'];
        }
        $saturdayString = "";
        if (array_key_exists('saturday', $_shiftInformation)) {
            $saturdayString = $_shiftInformation['saturday']['start'] . "|" . $_shiftInformation['saturday']['end'];
        }
        $sundayString = "";
        if (array_key_exists('sunday', $_shiftInformation)) {
            $sundayString = $_shiftInformation['sunday']['start'] . "|" . $_shiftInformation['sunday']['end'];
        }

        $retVar = [];
        $retVar['success'] = true;
        $retVar['response'] = "Success";

        if (self::ValidateLogin($pdo1)) {
            if ($shiftid > 0) {
                $stmt = $pdo->prepare("SELECT * FROM `shifts` WHERE `id` = :shiftid");
                $stmt->bindParam(":shiftid", $shiftid);
                $stmt->execute();
                $rowCount = $stmt->rowCount();
                if ($rowCount == 1) {
                    $results = $stmt->fetch(PDO::FETCH_ASSOC);
                    $shiftid = $results['id'];
                    $stmt = $pdo->prepare("UPDATE `shifts` SET 
                        `name` = :name ,
                        `monday` = :mondayString ,
                        `tuesday` = :tuesdayString ,
                        `wednesday` = :wednesdayString ,
                        `thursday` = :thursdayString ,
                        `friday` = :fridayString ,
                        `saturday` = :saturdayString ,
                        `sunday` = :sundayString  
                        WHERE `id` = :shiftid");
                    $stmt->bindParam(":name", $name);
                    $stmt->bindParam(":mondayString", $mondayString);
                    $stmt->bindParam(":tuesdayString", $tuesdayString);
                    $stmt->bindParam(":wednesdayString", $wednesdayString);
                    $stmt->bindParam(":thursdayString", $thursdayString);
                    $stmt->bindParam(":fridayString", $fridayString);
                    $stmt->bindParam(":saturdayString", $saturdayString);
                    $stmt->bindParam(":sundayString", $sundayString);
                    $stmt->bindParam(":shiftid", $shiftid);
                    $stmt->execute();
                }
            }
            else {
                $stmt = $pdo->prepare("INSERT INTO `shifts` (`name`,`monday`,`tuesday`,`wednesday`,`thursday`,`friday`,`saturday`,`sunday`) VALUES (:name,:mondayString,:tuesdayString,:wednesdayString,:thursdayString,:fridayString,:saturdayString,:sundayString)");
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":mondayString", $mondayString);
                $stmt->bindParam(":tuesdayString", $tuesdayString);
                $stmt->bindParam(":wednesdayString", $wednesdayString);
                $stmt->bindParam(":thursdayString", $thursdayString);
                $stmt->bindParam(":fridayString", $fridayString);
                $stmt->bindParam(":saturdayString", $saturdayString);
                $stmt->bindParam(":sundayString", $sundayString);
                $stmt->execute();
            }
        }
        else {
            $retVar['success'] = false;
            $retVar['response'] = "Database Error";
        }
        return $retVar;
    }

    public static function AddNewRole($_dbInfo, $_name, $_perms, $_dispatchable, $_roleid) {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        $retVar = [];
        $retVar['success'] = true;
        $retVar['response'] = "Success";

        if (self::ValidateLogin($pdo1)) {
            if (self::ValidateField($_name, 'name')) {

                if ($_roleid > 0) {
                    $stmt = $pdo->prepare("SELECT * FROM `roles` WHERE `id` = :roleid");
                    $stmt->bindParam(":roleid", $_roleid);
                    $stmt->execute();
                    $rowCount = $stmt->rowCount();
                    if ($rowCount == 1) {
                        $results = $stmt->fetch(PDO::FETCH_ASSOC);
                        $rowid = $results['id'];
                        $stmt = $pdo->prepare("UPDATE `roles` SET `name` = :name , `permissions` = :permissions , `dispatchable` = :dispatchable WHERE `id` = :roleid");
                        $stmt->bindParam(":name", $_name);
                        $stmt->bindParam(":permissions", $_perms);
                        $stmt->bindParam(":dispatchable", $_dispatchable);
                        $stmt->bindParam(":roleid", $_roleid);
                        $stmt->execute();
                    }
                }
                else {
                    $stmt = $pdo->prepare("INSERT INTO `roles` (`name`,`permissions`,`dispatchable`) VALUES (:name,:permissions,:dispatchable)");
                    $stmt->bindParam(":name", $_name);
                    $stmt->bindParam(":permissions", $_perms);
                    $stmt->bindParam(":dispatchable", $_dispatchable);
                    $stmt->execute();
                }
            }
            else {
                $retVar['success'] = false;
                $retVar['response'] = "Validation Error";
                return $retVar;
            }
        }
        else {
            $retVar['success'] = false;
            $retVar['response'] = "Database Error";
        }
        return $retVar;
    }

    public static function AddNewAccount($_dbInfo, $_formInformation)
    {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        $retVar = [];
        $retVar['success'] = true;
        $retVar['response'] = "Success";

        if (self::ValidateLogin($pdo1)) {
            $accountInformation = $_formInformation['accountInformation'];
            if (!is_array($accountInformation)) {
                return false;
            }

            if (!self::CheckPermissions($_dbInfo, ['ca'])) {
                $retVar['success'] = false;
                $retVar['response'] = "You do not have permission to view this page. Speak to your account manager to gain access.";
                return $retVar;
            }

            if (strlen($accountInformation['name']) <= 0) {
                $accountInformation['name'] = $accountInformation['firstName'] . " " . $accountInformation['lastName'];
            }

            if (
                !self::ValidateField($accountInformation['type'], 'contractType') ||
                !self::ValidateField($accountInformation['firstName'], 'name') ||
                !self::ValidateField($accountInformation['lastName'], 'name') ||
                !self::ValidateField($accountInformation['street1'], 'name') ||
                !self::ValidateField($accountInformation['street2'], 'address_nonrequired') ||
                !self::ValidateField($accountInformation['city'], 'name') ||
                !self::ValidateField($accountInformation['state'], 'state') ||
                !self::ValidateField($accountInformation['zipCode'], 'zipCode') ||
                !self::ValidateField($accountInformation['primaryPhone'], 'phone') ||
                !self::ValidateField($accountInformation['secondaryPhone'], 'phone_nonrequired') ||
                !self::ValidateField($accountInformation['email'], 'email') ||
                !self::ValidateField($accountInformation['name'], 'name')
            ) {
                $retVar['success'] = false;
                $retVar['response'] = "Validation Failed";
                return $retVar;
            }

            // Create Contact for Primary Contact
            $stmt = $pdo->prepare("INSERT INTO `contacts` (`firstname`,`lastname`,`email`,`primaryphone`,`secondaryphone`) VALUES (:firstname,:lastname,:email,:primaryphone,:secondaryphone)");
            $stmt->bindParam(":firstname", $accountInformation['firstName']);
            $stmt->bindParam(":lastname", $accountInformation['lastName']);
            $stmt->bindParam(":email", $accountInformation['email']);
            $stmt->bindParam(":primaryphone", $accountInformation['primaryPhone']);
            $stmt->bindParam(":secondaryphone", $accountInformation['secondaryPhone']);
            $stmt->execute();
            //Contact ID
            $cid = $pdo->lastInsertId();

            // Create Initial Account
            $stmt = $pdo->prepare("INSERT INTO `accounts` (`name`, `type`, `primarycontactid`, `street1`, `street2`, `city`, `state`, `zipcode`) VALUES (:name,:type,:primarycontactid,:street1,:street2,:city,:state,:zipcode)");
            $stmt->bindParam(":name", $accountInformation['name']);
            $stmt->bindParam(":type", $accountInformation['type']);
            $stmt->bindParam(":primarycontactid", $cid);
            $stmt->bindParam(":street1", $accountInformation['street1']);
            $stmt->bindParam(":street2", $accountInformation['street2']);
            $stmt->bindParam(":city", $accountInformation['city']);
            $stmt->bindParam(":state", $accountInformation['state']);
            $stmt->bindParam(":zipcode", $accountInformation['zipCode']);

            if (!$stmt->execute()) {
                // Clear all other entires if this one fails
                $stmt = $pdo->prepare("DELETE FROM `contacts` WHERE `id` = :id");
                $stmt->bindParam(":id", $cid);
                $stmt->execute();

                $retVar['success'] = false;
                $retVar['response'] = "Database Error";
                return $retVar;
            }
            //Account ID
            $aid = $pdo->lastInsertId();

            // Create Primary Location for account
            $stmt = $pdo->prepare("INSERT INTO `locations` (`accountid`, `street1`,`street2`,`city`,`state`,`zipcode`,`contacts`,`notes`) VALUES (:accountid,:street1,:street2,:city,:state,:zipcode,:contacts,'')");
            $stmt->bindParam(":accountid", $aid);
            $stmt->bindParam(":street1", $accountInformation['street1']);
            $stmt->bindParam(":street2", $accountInformation['street2']);
            $stmt->bindParam(":city", $accountInformation['city']);
            $stmt->bindParam(":state", $accountInformation['state']);
            $stmt->bindParam(":zipcode", $accountInformation['zipCode']);
            $stmt->bindParam(":contacts", $cid);
            $stmt->execute();
            //Location ID
            $lid = $pdo->lastInsertId();

            $locations = $_formInformation['locations'];

            if (is_array($locations)) {
                if (count($locations) > 0) {
                    foreach ($locations as $location) {
                        $finalContactId = $cid;
                        if (
                            !self::ValidateField($location['street1'], 'name') ||
                            !self::ValidateField($location['street2'], 'address_nonrequired') ||
                            !self::ValidateField($location['city'], 'name') ||
                            !self::ValidateField($location['state'], 'state') ||
                            !self::ValidateField($location['zipCode'], 'zipCode')
                        ) {
                            // Clear all other entires if this one fails
                            $stmt = $pdo->prepare("DELETE FROM `contacts` WHERE `id` = :id");
                            $stmt->bindParam(":id", $cid);
                            $stmt->execute();
                            $stmt = $pdo->prepare("DELETE FROM `accounts` WHERE `id` = :id");
                            $stmt->bindParam(":id", $aid);
                            $stmt->execute();

                            $retVar['success'] = false;
                            $retVar['response'] = "Validation Error";
                            return $retVar;
                        }

                        if ($location['copyContact'] === false) {
                            if (
                                !self::ValidateField($location['contact_firstName'], 'name') ||
                                !self::ValidateField($location['contact_lastName'], 'name') ||
                                !self::ValidateField($location['contact_email'], 'email') ||
                                !self::ValidateField($location['contact_primaryPhone'], 'phone') ||
                                !self::ValidateField($location['contact_secondaryPhone'], 'phone_nonrequired')
                            ) {
                                $stmt = $pdo->prepare("DELETE FROM `contacts` WHERE `id` = :id");
                                $stmt->bindParam(":id", $cid);
                                $stmt->execute();
                                $stmt = $pdo->prepare("DELETE FROM `accounts` WHERE `id` = :id");
                                $stmt->bindParam(":id", $aid);
                                $stmt->execute();

                                $retVar['success'] = false;
                                $retVar['response'] = "Validation Error";
                                return $retVar;
                            }

                            $stmt = $pdo->prepare("INSERT INTO `contacts` (`firstname`,`lastname`,`email`,`primaryphone`,`secondaryphone`) VALUES (:firstname,:lastname,:email,:primaryphone,:secondaryphone)");
                            $stmt->bindParam(":firstname", $location['contact_firstName']);
                            $stmt->bindParam(":lastname", $location['contact_lastName']);
                            $stmt->bindParam(":email", $location['contact_email']);
                            $stmt->bindParam(":primaryphone", $location['contact_primaryPhone']);
                            $stmt->bindParam(":secondaryphone", $location['contact_secondaryPhone']);

                            if (!$stmt->execute()) {
                                $stmt = $pdo->prepare("DELETE FROM `contacts` WHERE `id` = :id");
                                $stmt->bindParam(":id", $cid);
                                $stmt->execute();
                                $stmt = $pdo->prepare("DELETE FROM `accounts` WHERE `id` = :id");
                                $stmt->bindParam(":id", $aid);
                                $stmt->execute();

                                $retVar['success'] = false;
                                $retVar['response'] = "Database Error";
                                return $retVar;
                            }
                            //Contact ID
                            $finalContactId = $pdo->lastInsertId();
                        }

                        // Create Location
                        $stmt = $pdo->prepare("INSERT INTO `locations` (`accountid`,`street1`,`street2`,`city`,`state`,`zipcode`,`contacts`,`notes`) VALUES (:accountid,:street1,:street2,:city,:state,:zipcode,:contacts,'')");
                        $stmt->bindParam(":accountid", $aid);
                        $stmt->bindParam(":street1", $location['street1']);
                        $stmt->bindParam(":street2", $location['street2']);
                        $stmt->bindParam(":city", $location['city']);
                        $stmt->bindParam(":state", $location['state']);
                        $stmt->bindParam(":zipcode", $location['zipCode']);
                        $stmt->bindParam(":contacts", $finalContactId);

                        if (!$stmt->execute()) {
                            $stmt = $pdo->prepare("DELETE FROM `contacts` WHERE `id` = :id");
                            $stmt->bindParam(":id", $cid);
                            $stmt->execute();
                            if ($cid != $finalContactId) {
                                $stmt = $pdo->prepare("DELETE FROM `contacts` WHERE `id` = :id");
                                $stmt->bindParam(":id", $finalContactId);
                                $stmt->execute();
                            }
                            $stmt = $pdo->prepare("DELETE FROM `accounts` WHERE `id` = :id");
                            $stmt->bindParam(":id", $aid);
                            $stmt->execute();

                            $retVar['success'] = false;
                            $retVar['response'] = "Database Error";
                            return $retVar;
                        }
                        //Location ID
                        $lidx = $pdo->lastInsertId();
                    }
                }
            }
            else {
                $retVar['success'] = false;
                $retVar['response'] = "Database Error";
            }

            return $retVar;
        }
    }

    public static function ValidateField($form_input, $validation_type)
    {
        $retVal = false;

        switch ($validation_type) {
            case 'year':
                $yearRegex = '/^\d{4}$/';
                if (!preg_match($yearRegex, $form_input)) {
                    return $retVal;
                }
                break;
            case 'date':
                $dateRegex = '/^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2[0-9]|3[0-1])\/\d{4}$/';
                if (!preg_match($dateRegex, $form_input)) {
                    return $retVal;
                }
                break;
            case 'date_my':
                $datemyRegex = '/^(0[1-9]|1[0-2])\/\d{4}$/';
                if (!preg_match($datemyRegex, $form_input)) {
                    return $retVal;
                }
                break;
            case 'email':
                $emailRegex = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
                if (!preg_match($emailRegex, $form_input)) {
                    return $retVal;
                }
                break;

            case 'phone':
                $phoneRegex = '/^(?=.*\d)\(\d{3}\) \d{3}-\d{4}$/';
                if (!preg_match($phoneRegex, $form_input)) {
                    return $retVal;
                }
                break;

            case 'phone_nonrequired':
                $phoneRegex = '/^\(\d{3}\) \d{3}-\d{4}$/';
                if (strlen($form_input) > 0 && !preg_match($phoneRegex, $form_input)) {
                    return $retVal;
                }
                break;

            case 'zipCode':
                $zipcodeRegex = '/^\d{5}$/';
                if (!preg_match($zipcodeRegex, $form_input)) {
                    return $retVal;
                }
                break;

            case 'address':
                // Customize the regular expression for street address validation
                $streetAddressRegex = '/^[a-zA-Z0-9\s.,\'-]+$/';
                if (!preg_match($streetAddressRegex, $form_input)) {
                    return $retVal;
                }
                break;

            case 'address_nonrequired':
                // Customize the regular expression for street address validation
                $streetAddressRegex = '/^[a-zA-Z0-9\s.,\'-]+$/';
                if (!empty($form_input)) {
                    if (!preg_match($streetAddressRegex, $form_input)) {
                        return $retVal;
                    } elseif (strlen($form_input) <= 3) {
                        return $retVal;
                    }
                }
                break;

            case 'name':
                if (strlen($form_input) <= 2) {
                    return $retVal;
                }
                break;

            case 'name_nonrequired':
                if (strlen($form_input) > 0 && strlen($form_input) <= 2) {
                    return $retVal;
                }
                break;

            case 'contractType':
                if ($form_input === null) {
                    return $retVal;
                }
                break;

            case 'state':
                $stateRegex = '/^[a-zA-Z]+$/';
                if (!preg_match($stateRegex, $form_input) || strlen($form_input) != 2) {
                    return $retVal;
                }
                break;

            default:
                $retVal = true;
                return $retVal;
        }

        // Validation passed
        $retVal = true;
        return $retVal;
    }
}