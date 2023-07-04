<?php

trait ActionAddNewAccount {
    public static function AddNewAccount($_dbInfo, $_postData) {
        OpLog::Log("Action: AddNewAccount");
        OpLog::Log(print_r($_postData, true) . "\n");

        $formData = json_decode($_postData['formdata'], true);
        $retVar = DatabaseManager::AddNewAccount($_dbInfo, $formData);
        $boolString = $retVar['success'] ? 'true' : 'false';
        $retString = $boolString . "|" . $retVar['response'];
        return $retString;
    }
}

?>