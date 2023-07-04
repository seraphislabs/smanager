<?php

trait DatabaseEmployeeShifts {
    public static function GetAllEmployeeShifts($_dbInfo, $asAssoc) {
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $retVal = [];

        $results = $db2->fetchAll("SELECT * FROM `shifts`");

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

        OpLog::Log("Database: GetAllEmployeeShifts");
        OpLog::Log("--Returned: " . count($retVal) . " shifts");
        return $retVal;
    }

    public static function GetEmployeeShift($_dbInfo, $_shiftid) {
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $result = $db2->fetch("SELECT * FROM `shifts` WHERE `id` = :shiftid", ["shiftid" => $_shiftid]);

        OpLog::Log("Database: GetEmployeeShift");
        OpLog::Log("--Returned: Array");
        return $result;
    }

    public static function AddNewEmployeeShift($_dbInfo, $_shiftInformation) {
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

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

        
        if ($shiftid > 0) {

            $results = $db2->fetch("SELECT * FROM `shifts` WHERE `id` = :shiftid", ["shiftid" => $shiftid]);

            if (count($results) > 0) {
                $shiftid = $results['id'];

                $sqlData = [
                    "name" => $name,
                    "mondayString" => $mondayString,
                    "tuesdayString" => $tuesdayString,
                    "wednesdayString" => $wednesdayString,
                    "thursdayString" => $thursdayString,
                    "fridayString" => $fridayString,
                    "saturdayString" => $saturdayString,
                    "sundayString" => $sundayString
                ];

                $cond = [
                    ["id", "=", $shiftid],
                ];

                $db2->update("shifts", $sqlData, $cond);
            }
        }
        else {

            $sqlData = [
                "name" => $name,
                "mondayString" => $mondayString,
                "tuesdayString" => $tuesdayString,
                "wednesdayString" => $wednesdayString,
                "thursdayString" => $thursdayString,
                "fridayString" => $fridayString,
                "saturdayString" => $saturdayString,
                "sundayString" => $sundayString
            ];

            $db2->insert("shifts", $sqlData);
        }

        OpLog::Log("Database: AddNewEmployeeShift");
        OpLog::Log("--Returned: Succesfully added shift");
        return $retVar;
    }
}

?>