<?php

trait DatabaseLocations {
    public static function GetAllLocationsByAccount($_accountid) {
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $results = $db2->fetchAll("SELECT * FROM `locations` WHERE `accountid` = :id", ["id" => $_accountid]);

        OpLog::Log("Database: GetAllLocationsByAccount");
        OpLog::Log("--Returned: Array containing " . count($results) . " elements");
        return $results;
    }
}

?>