<?php

trait ActionAddNewRole {

    public static function AddNewRole($_dbInfo, $_rolename, $_perms, $_isDispatchable, $_roleId) {
        $retVar = DatabaseManager::AddNewEmployeeRole($_dbInfo, $_rolename, $_perms, $_isDispatchable, $_roleId);
		$boolString = $retVar['success'] ? "true" : "false";
		$retString = $boolString . "|" . $retVar['response'];
		return $retString;
    }
}

?>