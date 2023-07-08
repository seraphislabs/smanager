<?php

trait ActionUpdatePunchDisplay {
    public static function UpdatePunchDisplay($_postData) {
        $ai = DatabaseManager::GetActiveSession();
        OpLog::Log("Action: UpdatePunchDisplay");
        $rdb = RDB::getInstance();
        $ai = $rdb->get($_SESSION['token']);
        $_employeeid = $ai['id'];
        $missingPunch = DatabaseManager::CheckForEmptyPunch($_employeeid);

        if ($missingPunch != null) {
            $retString = "true|" . $missingPunch['timein'];
            return $retString;
        } else {
            $retString = "false|" . $ai['firstname'];
            return $retString;
        }
    }
}

?>