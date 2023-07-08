<?php

trait DatabaseHolidaySchedules {
    public static function GetAllHolidaySchedules() {
        $ai = self::GetActiveSession();
        $db2 = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $retVal = [];
        
        OpLog::Log("Database: GetAllHolidaySchedules");
        OpLog::Log("--Returned: false: No schedules found");
        return null;
    }
}

?>