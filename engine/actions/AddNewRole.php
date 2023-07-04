<?php

trait ActionAddNewRole {

    public static function AddNewRole($_dbInfo, $_postData) {
		OpLog::Log("Action: AddNewRole");
        OpLog::Log(print_r($_postData, true) . "\n");

        $perms = $_postData['perms'];
		$rolename = $_postData['name'];
		$isDispatchable = $_postData['isDispatchable'];
		$roleId = $_postData['roleid'];

        $retVar = DatabaseManager::AddNewEmployeeRole($_dbInfo, $rolename, $perms, $isDispatchable, $roleId);
		$boolString = $retVar['success'] ? "true" : "false";
		$retString = $boolString . "|" . $retVar['response'];
		return $retString;
    }
}

?>