<?php
	date_default_timezone_set('America/New_York');
	session_start();
	require("validation/validate.php");
	require('databaseinterface.php');
	require_once("/nginx/protectedfiles/config.php");
	require('utils.php');
	require_once('pages.php');

	class Actions {
		use ActionCheckSession;
		use ActionLoadPage;
		use ActionLoadPopup;
		use ActionAddNewRole;
		use ActionAddNewShift;
		use ActionAddNewAccount;
		use ActionAddNewEmployee;
		use ActionGetScheduleForMonth;
		use ActionOpenSettingsMenu;
		use ActionStartSession;
		use ActionStartPortal;
		use ActionGetMainMenuButtons;
		use ActionLogout;
	}

	if (isset($_POST['action'])) {
		$action = $_POST['action'];

		$postData = array();

		foreach ($_POST as $key => $value) {
			$postData[$key] = $value;
		}

		$className = $action;
		if (method_exists('Actions', $className)) {
			$method = new ReflectionMethod('Actions', $className);
			echo $method->invoke(null, $dbInfo, $postData);
		}
	}
?>