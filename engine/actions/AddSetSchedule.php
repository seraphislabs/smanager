<?php

trait ActionAddSetSchedule {
    public static function AddSetSchedule($_dbInfo, $_postData) {
        $formInformation = json_decode($_postData['formInformation'], true);

        $retVar = DatabaseManager::AddSetSchedule($_dbInfo, $formInformation);
        $boolString = $retVar['success'] ? "true" : "false";

		$retString = $boolString . "|" . $retVar['response'] . "|" . $retVar['time'];
		return $retString;
    }
}

?>