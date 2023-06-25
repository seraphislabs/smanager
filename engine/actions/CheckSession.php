<?php
    trait ActionCheckSession {
        public static function CheckSession($_dbInfo) {

            if (isset($_SESSION['email']) && isset($_SESSION['password'])) {
                $retVal = DatabaseManager::ManuallyValidateLogin($_dbInfo);
                return $retVal;
            }
            return false;
        }
    }
?>