<?php

trait ActionAddNewAccount {
    public static function AddNewAccount($_dbInfo, $_formInformation) {
        $retVar = DatabaseManager::AddNewAccount($_dbInfo, $_formInformation);
        $boolString = $retVar['success'] ? "true" : "false";
        $retString = $boolString . "|" . $retVar['response'];
        return $retString;
    }
}

?>