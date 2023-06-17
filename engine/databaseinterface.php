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
        $_email = $_SESSION['email'];
        $pdo = self::connect($_dbInfo, 'servicemanager');

        if (self::ValidateLogin($pdo)) {
            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `workemail`=:email");
            $stmt->bindParam(":email", $_email);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $pdo = null;
                $retArray = explode("|", $results[0]['permissions']);
                return $retArray;
            }
        }
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
                    `permissions`,
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
                    :permissions,
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
            
            $tempRole = 'Technician';
            $tempPermissions = 'ac|va|ca|vt|ce';
            $tempPassword = PasswordEncrypt::Encrypt('test');
            $stmt->bindParam(':password', $tempPassword);
            $stmt->bindParam(':firstname', $employeeInformation['firstName']);
            $stmt->bindParam(':lastname', $employeeInformation['lastName']);
            $xdob = date('Y-m-d', strtotime($employeeInformation['dob']));
            $stmt->bindParam(':dob', $xdob);
            $stmt->bindParam(':email', $employeeInformation['email']);
            $stmt->bindParam(':companyid', $_SESSION['companyid']);
            $stmt->bindParam(':permissions', $tempPermissions);
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
                $phoneRegex = '/^\d{10}$/';
                if (!preg_match($phoneRegex, $form_input)) {
                    return $retVal;
                }
                break;

            case 'phone_nonrequired':
                $phoneRegex = '/^\d{10}$/';
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