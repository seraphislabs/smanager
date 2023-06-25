<?php

trait ActionAddNewEmployee {
    public static function AddNewEmployee($_dbInfo, $_postData) { 
		$formData = json_decode($_postData['formdata'], true);
		$retVar = DatabaseManager::AddNewEmployee($_dbInfo, $formData);
		$boolString = $retVar['success'] ? "true" : "false";
		$retString = $boolString . "|" . $retVar['response'];
		return $retString;
	}
}

?>