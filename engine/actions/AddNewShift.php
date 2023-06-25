<?php

trait ActionAddNewShift {
    public static function AddNewShift($_dbInfo, $_shiftInformation) {
        $retVar = DatabaseManager::AddNewEmployeeShift($_dbInfo, $_shiftInformation);
		$boolString = $retVar['success'] ? "true" : "false";
		$retString = $boolString . "|" . $retVar['response'];
		return $retString;
    }
}

?>