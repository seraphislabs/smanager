<?php

trait ActionAddNewShift {
    public static function AddNewShift($_postData) {
        OpLog::Log("Action: AddNewShift");
        OpLog::Log(print_r($_postData, true) . "\n");

        $postInfo = $_postData['shiftInformation'];
		$shiftInformation = json_decode($postInfo, true);

        $retVar = DatabaseManager::AddNewEmployeeShift($shiftInformation);
		$boolString = $retVar['success'] ? "true" : "false";
		$retString = $boolString . "|" . $retVar['response'];
		return $retString;
    }
}

?>