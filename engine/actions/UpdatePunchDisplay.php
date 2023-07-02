<?php

trait ActionUpdatePunchDisplay {
    public static function UpdatePunchDisplay($_dbInfo, $_postData) {
        $missingPunch = DatabaseManager::CheckForEmptyPunch($_dbInfo, $_SESSION['employeeid']);

        if ($missingPunch != null) {
            $retString = "true|" . $missingPunch['timein'];
            return $retString;
        } else {
            $retString = "false|" . $_SESSION['firstname'];
            return $retString;
        }
    }
}

?>