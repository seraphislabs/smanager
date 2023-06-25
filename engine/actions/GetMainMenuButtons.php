<?php

trait ActionGetMainMenuButtons {
    public static function GetMainMenuButtons($_dbInfo) {
		$perms = DatabaseManager::GetUserPermissions($_dbInfo);

		$returnedCode = "";

		$returnedCode .= ListButtons::GenerateLeftPaneButton("Dashboard");

		if (in_array('va', $perms)) {
			$returnedCode .= ListButtons::GenerateLeftPaneButton("Accounts");
		}
		if (in_array('vel', $perms)) {
			$returnedCode .= ListButtons::GenerateLeftPaneButton("Employees");
		}
		if (in_array('vwo', $perms)) {
			$returnedCode .= ListButtons::GenerateLeftPaneButton("WorkOrders");
		}
		if (in_array('vi', $perms)) {
			$returnedCode .= ListButtons::GenerateLeftPaneButton("Invoices");
		}
		if (in_array('vsr', $perms)) {
			$returnedCode .= ListButtons::GenerateLeftPaneButton("ServiceReports");
		}

		return $returnedCode;
	}
}

?>