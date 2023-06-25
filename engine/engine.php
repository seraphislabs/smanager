<?php
	date_default_timezone_set('America/New_York');
	session_start();
	require("validation/validate.php");
	require('databaseinterface.php');
	require_once("/nginx/protectedfiles/config.php");
	require('utils.php');
	require('pages.php');


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
		
		switch ($action) {
			case "Logout":
				Actions::Logout();
				die();
			case "StartPortal":
				$get_PageData = json_decode($_POST['pagedata'], true);
				echo (Actions::StartPortal($dbInfo, $get_PageData));
				break;
			case "LoadPage":
				$buttonid = $_POST['buttonid'];
				$data = [];
				if (isset($_POST['data'])) {
					$data = json_decode($_POST['data'], true);
				}
				echo (Actions::LoadPage($dbInfo, $buttonid, $data));
				break;
			case "LoadPopup":
				$buttonid = $_POST['buttonid'];
				$data = [];
				if (isset($_POST['data'])) {
					$data = json_decode($_POST['data'], true);
				}
				echo (Actions::LoadPopup($dbInfo, $buttonid, $data));
				break;
			case "AddNewAccount":
				$formData = json_decode($_POST['formdata'], true);
				echo(Actions::AddNewAccount($dbInfo, $formData));
				break;
			case "AddNewEmployee":
				$formData = json_decode($_POST['formdata'], true);
				echo(Actions::AddNewEmployee($dbInfo, $formData));
				break;
			case "StartSession":
				Actions::StartSession($dbInfo);
				break;
			case "OpenSettingsMenu":
				echo(Actions::OpenSettingsMenu($dbInfo));
				break;
			case "AddNewRole":
				$perms = $_POST['perms'];
				$rolename = $_POST['name'];
				$isDispatchable = $_POST['isDispatchable'];
				$roleId = $_POST['roleid'];
				echo(Actions::AddNewRole($dbInfo, $rolename, $perms, $isDispatchable, $roleId));
				break;
			case "AddNewShift":
				$postInfo = $_POST['shiftInformation'];
				$shiftInformation = json_decode($postInfo, true);
				echo(Actions::AddNewShift($dbInfo, $shiftInformation));
				break;
			case "GetScheduleForMonth":
				$month = $_POST['month'];
				$year = $_POST['year'];
				$employeeid = $_POST['eid'];
				echo(Actions::GetScheduleForMonth($dbInfo, $month, $year, $employeeid));
				break;
			break;
		}
	}
?>