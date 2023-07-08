<?php

trait ActionAddSetSchedule {
    public static function AddSetSchedule($_postData) {
        OpLog::Log("Action: AddSetSchedule");
        OpLog::Log(print_r($_postData, true) . "\n");
        $formInformation = json_decode($_postData['formInformation'], true);

        $retVar = DatabaseManager::AddSetSchedule($formInformation);
        $boolString = $retVar['success'] ? "true" : "false";

		$retString = $boolString . "|" . $retVar['response'] . "|" . $retVar['time'];
		return $retString;
    }
}

?>