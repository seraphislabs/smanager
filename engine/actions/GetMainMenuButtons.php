<?php

trait ActionGetMainMenuButtons {
    public static function GetMainMenuButtons($_dbInfo) {
		$ai = DatabaseManager::GetActiveSession();
		OpLog::Log("Action: GetMainMenuButtons");

		$returnedCode = "";

		$returnedCode .= ListButtons::GenerateLeftPaneButton("Dashboard");

		if (DatabaseManager::CheckPermission('va')) {
			$returnedCode .= ListButtons::GenerateLeftPaneButton("Accounts");
		}
		if (DatabaseManager::CheckPermission('vel')) {
			$returnedCode .= ListButtons::GenerateLeftPaneButton("Employees");
		}
		if (DatabaseManager::CheckPermission('vwo')) {
			$returnedCode .= ListButtons::GenerateLeftPaneButton("WorkOrders");
		}
		if (DatabaseManager::CheckPermission('vi')) {
			$returnedCode .= ListButtons::GenerateLeftPaneButton("Invoices");
		}
		if (DatabaseManager::CheckPermission('vsr')) {
			$returnedCode .= ListButtons::GenerateLeftPaneButton("ServiceReports");
		}

		return $returnedCode;
	}
}

?>