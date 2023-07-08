<?php

trait ActionAddNewEmployee {
    public static function AddNewEmployee($_postData) { 
		OpLog::Log("Action: AddNewEmployee");
        OpLog::Log(print_r($_postData, true) . "\n");

		$formData = json_decode($_postData['formdata'], true);
		$retVar = DatabaseManager::AddNewEmployee($formData);
		$boolString = $retVar['success'] ? "true" : "false";
		$retString = $boolString . "|" . $retVar['response'];
		return $retString;
	}
}

?>

