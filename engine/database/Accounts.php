<?php

trait DatabaseAccounts {
    public static function GetAllAccounts($_dbInfo)
    {
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $results = $db2->fetchAll("SELECT * FROM `accounts`");
        if (count($results) > 0) {
            return $results;
        }

        OpLog::Log("Database: GetAllAccounts");
        OpLog::Log("Return count: ". count($results));

        return [];
    }

    public static function GetAccount($_dbInfo, $_accountid)
    {
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $result = $db2->fetch("SELECT * FROM `accounts` WHERE `id` = :id", ["id" => $_accountid]);

        OpLog::Log("Database: GetAccount");
        OpLog::Log("--Returned: Array");

        return $result;
    }

    public static function AddNewAccount($_dbInfo, $_formInformation)
    {
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $retVar = [];
        $retVar['success'] = true;
        $retVar['response'] = "Success";

        $accountInformation = $_formInformation['accountInformation'];

        if (!is_array($accountInformation)) {
            OpLog::Log("Database: AddNewAccount");
            OpLog::Log("--Returned: FALSE: accountInformation is not an array");
            return false;
        }

        if (!self::CheckPermission(['ca'])) {
            $retVar['success'] = false;
            $retVar['response'] = "You do not have permission to view this page. Speak to your account manager to gain access.";
            OpLog::Log("Database: AddNewAccount");
            OpLog::Log("--Returned: FALSE: No permissions");
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
            OpLog::Log("Database: AddNewAccount");
            OpLog::Log("--Returned: FALSE: Validation Failed");
            return $retVar;
        }

        $sqlData = [
            "firstname" => $accountInformation['firstName'],
            "lastname" => $accountInformation['lastName'],
            "email" => $accountInformation['email'],
            "primaryphone" => $accountInformation['primaryPhone'],
            "secondaryphone" => $accountInformation['secondaryPhone']
        ];
        $cid = $db2->insert("contacts", $sqlData);

        $sqlData = [
            "name" => $accountInformation['name'],
            "type" => $accountInformation['type'],
            "primarycontactid" => $cid,
            "street1" => $accountInformation['street1'],
            "street2" => $accountInformation['street2'],
            "city" => $accountInformation['city'],
            "state" => $accountInformation['state'],
            "zipcode" => $accountInformation['zipCode']
        ];
        $aid = $db2->insert("accounts", $sqlData);

        $sqlData = [
            "accountid" => $aid,
            "street1" => $accountInformation['street1'],
            "street2" => $accountInformation['street2'],
            "city" => $accountInformation['city'],
            "state" => $accountInformation['state'],
            "zipcode" => $accountInformation['zipCode'],
            "contacts" => $cid
        ];
        $lid = $db2->insert("locations", $sqlData);

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
                        OpLog::Log("Database: AddNewAccount");
                        OpLog::Log("--Returned: FALSE: Validation Error");
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
                            OpLog::Log("Database: AddNewAccount");
                            OpLog::Log("--Returned: FALSE: Validation Error");
                            return $retVar;
                        }

                        $sqlData = [
                            "firstname" => $location['contact_firstName'],
                            "lastname" => $location['contact_lastName'],
                            "email" => $location['contact_email'],
                            "primaryphone" => $location['contact_primaryPhone'],
                            "secondaryphone" => $location['contact_secondaryPhone']
                        ];
                        $finalContactId = $db2->insert("contacts", $sqlData);
                    }

                    $locationName = $location['name'];

                    if ($count == 1) {
                        $locationName = "Primary Location";
                    }
                    // Create Location

                    $sqlData = [
                        "accountname" => $locationName,
                        "accountid" => $aid,
                        "street1" => $location['street1'],
                        "street2" => $location['street2'],
                        "city" => $location['city'],
                        "state" => $location['state'],
                        "zipcode" => $location['zipCode'],
                        "contacts" => $finalContactId
                    ];
                    $lidx = $db2->insert("locations", $sqlData);
                }
            }
        }
        else {
            $retVar['success'] = false;
            $retVar['response'] = "Database Error";
            OpLog::Log("Database: AddNewAccount");
            OpLog::Log("--Returned: FALSE: Database Error");
            return $retVar;
        }

        OpLog::Log("Database: AddNewAccount");
        OpLog::Log("--Returned: Successfully added account");
        return $retVar;
    }
}

?>