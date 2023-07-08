<?php

trait DatabaseContacts {
    public static function GetContact($_contactid)
    {
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $result = $db2->fetch("SELECT * FROM `contacts` WHERE `id` = :id", ["id" => $_contactid]);

        OpLog::Log("Database: GetContact");
        OpLog::Log("--Returned: Array");
        return $result;
    }
}

?>