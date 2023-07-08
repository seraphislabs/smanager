<?php

trait DatabaseEmployeeRoles {
    public static function GetAllEmployeeRoles($asAssoc) {
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $retVal = [];

        $results = $db2->fetchAll("SELECT * FROM `roles` ORDER BY `id`");

        if (count($results) > 0) {
            if ($asAssoc) {
                foreach($results as $result) {
                    $retVal[$result['id']] = $result;
                }
            }
            else {
                $retVal = $results;
            }
        }

        OpLog::Log("Database: GetAllEmployeeRoles");
        OpLog::Log("--Returned: " . count($retVal) . " roles");
        return $retVal;
    }

    public static function GetEmployeeRole($_roleid) { 
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $result = $db2->fetch("SELECT * FROM `roles` WHERE `id` = :id", ["id" => $_roleid]);

        OpLog::Log("Database: GetEmployeeRole");
        OpLog::Log("--Returned: Array");
        return $result;
    }

    public static function AddNewEmployeeRole($_name, $_perms, $_dispatchable, $_roleid) {

        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $retVar = [];
        $retVar['success'] = true;
        $retVar['response'] = "Success";


        if (ValidateField::Validate($_name, 'name')) {

            if ($_roleid > 0) {
                $results = $db2->fetch("SELECT * FROM `roles` WHERE `id` = :roleid", ["roleid" => $_roleid]);
                if (count($results) > 0) {
                    $rowid = $results['id'];

                    $sqlData = [
                        "name" => $_name,
                        "permissions" => $_perms,
                        "dispatchable" => $_dispatchable
                    ];
                    $cond = [
                        ["id", "=", $rowid],
                    ];
                    $db2->update("roles", $sqlData, $cond);
                }
            }
            else {
                $sqlData = [
                    "name" => $_name,
                    "permissions" => $_perms,
                    "dispatchable" => $_dispatchable
                ];

                $db2->insert("roles", $sqlData);
            }
        }
        else {
            $retVar['success'] = false;
            $retVar['response'] = "Validation Error";
            OpLog::Log("Database: AddNewEmployeeRole");
            OpLog::Log("--Returned: false: Validation Error");
            return $retVar;
        }
        OpLog::Log("Database: AddNewEmployeeRole");
        OpLog::Log("--Returned: Successfully added new employee role");
        return $retVar;
    }
}

?>