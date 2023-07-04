<?php
    trait ActionCheckSession {
        public static function CheckSession($_dbInfo) {
            OpLog::Log("Action: CheckSession");

            if (isset($_SESSION['token'])) {

                $rdb = RDB::getInstance();
                $accountInfo = $rdb->get($_SESSION['token']);

                if ($accountInfo != false) {
                    return true;
                }

                return false;
            }

            return false;
        }
    }
?>