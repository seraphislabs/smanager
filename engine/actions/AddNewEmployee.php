<?php

trait ActionAddNewEmployee {
    public static function AddNewEmployee($_dbInfo, $_formInformation) { 
		$retVar = DatabaseManager::AddNewEmployee($_dbInfo, $_formInformation);
		$boolString = $retVar['success'] ? "true" : "false";
		$retString = $boolString . "|" . $retVar['response'];
		return $retString;
	}
}

?>