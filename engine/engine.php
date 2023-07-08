<?php
	date_default_timezone_set('America/New_York');
	session_start();
	require_once("/nginx/protectedfiles/config.php");
	require("validation/validate.php");
	require('databaseinterface.php');
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
		use ActionGenerateCalendar;
		use ActionOpenSettingsMenu;
		use ActionStartSession;
		use ActionStartPortal;
		use ActionGetMainMenuButtons;
		use ActionLogout;
		use ActionAddSetSchedule;
		use ActionPunchIn;
		use ActionUpdatePunchDisplay;
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
			echo $method->invoke(null, $postData);
		}
	}

	DBI::closeAll();
	RDB::closeInstance();

?>