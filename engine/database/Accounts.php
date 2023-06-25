<?php

trait DatabaseAccounts {
    public static function GetAllAccounts($_dbInfo)
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
                !ValidateField::Validate($accountInformation['type'], 'contractType') ||
                !ValidateField::Validate($accountInformation['firstName'], 'name') ||
                !ValidateField::Validate($accountInformation['lastName'], 'name') ||
                !ValidateField::Validate($accountInformation['street1'], 'name') ||
                !ValidateField::Validate($accountInformation['street2'], 'address_nonrequired') ||
                !ValidateField::Validate($accountInformation['city'], 'name') ||
                !ValidateField::Validate($accountInformation['state'], 'state') ||
                !ValidateField::Validate($accountInformation['zipCode'], 'zipCode') ||
                !ValidateField::Validate($accountInformation['primaryPhone'], 'phone') ||
                !ValidateField::Validate($accountInformation['secondaryPhone'], 'phone_nonrequired') ||
                !ValidateField::Validate($accountInformation['email'], 'email') ||
                !ValidateField::Validate($accountInformation['name'], 'name')
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

            $count = 0;
            if (is_array($locations)) {
                if (count($locations) > 0) {
                    foreach ($locations as $location) {
                        $count++;
                        $finalContactId = $cid;
                        if (
                            !ValidateField::Validate($location['name'], 'name_nonrequired') ||
                            !ValidateField::Validate($location['street1'], 'name') ||
                            !ValidateField::Validate($location['street2'], 'address_nonrequired') ||
                            !ValidateField::Validate($location['city'], 'name') ||
                            !ValidateField::Validate($location['state'], 'state') ||
                            !ValidateField::Validate($location['zipCode'], 'zipCode')
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
                                !ValidateField::Validate($location['contact_firstName'], 'name') ||
                                !ValidateField::Validate($location['contact_lastName'], 'name') ||
                                !ValidateField::Validate($location['contact_email'], 'email') ||
                                !ValidateField::Validate($location['contact_primaryPhone'], 'phone') ||
                                !ValidateField::Validate($location['contact_secondaryPhone'], 'phone_nonrequired')
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

                        $locationName = $location['name'];

                        if (count == 1) {
                            $locationName = "Primary Location";
                        }
                        // Create Location
                        $stmt = $pdo->prepare("INSERT INTO `locations` (`name`, `accountid`,`street1`,`street2`,`city`,`state`,`zipcode`,`contacts`,`notes`) VALUES (:accountname,:accountid,:street1,:street2,:city,:state,:zipcode,:contacts,'')");
                        $stmt->bindParam(":accountname", $locationName);
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
}

?>