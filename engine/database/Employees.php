<?php

trait DatabaseEmployees {
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
                return $retVal;
            }
        }

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

            return $retVar;
        }
    }
}

?>