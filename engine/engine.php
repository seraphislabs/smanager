<?php
	date_default_timezone_set('America/New_York');
	session_start();
	require('databaseinterface.php');
	require_once("/nginx/protectedfiles/config.php");
	require('utils.php');
	require('pages.php');

	function PopulateLeftPaneMenu($_dbInfo) {
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

	function Action_CheckSession($_dbInfo) {

		if (isset($_SESSION['email']) && isset($_SESSION['password'])) {
			$retVal = DatabaseManager::ManuallyValidateLogin($_dbInfo);
			return $retVal;
		}
		return false;
	}

	function Action_LoadPage($_dbInfo, $_pageid) {
		$returnedCode = "";
		switch ($_pageid) {
			case "Accounts":
				$returnedCode .= "<script>history.pushState(null, null, '/index.php?page=$_pageid');</script>";
				$returnedCode .= PageViewAccounts::Generate($_dbInfo);
			case "Dashboard":
				$returnedCode .= "<script>history.pushState(null, null, '/index.php?page=$_pageid');</script>";
				break;
			case "Employees":
				$returnedCode .= "<script>history.pushState(null, null, '/index.php?page=$_pageid');</script>";
				$returnedCode .= PageViewEmployees::Generate($_dbInfo);
				break;
			case "WorkOrders";
				$returnedCode .= "<script>history.pushState(null, null, '/index.php?page=$_pageid');</script>";
				$returnedCode .= PageViewWorkOrders::Generate($_dbInfo);
				break;
			case "EmployeeSettings";
				$returnedCode .= "<script>history.pushState(null, null, '/index.php?page=$_pageid');</script>";
				$returnedCode .= PageEmployeeSettings::Generate($_dbInfo);
				break;
			case "ScheduleSettings";
			 $returnedCode .= "<script>history.pushState(null, null, '/index.php?page=$_pageid');</script>";
				$returnedCode .= PageScheduleSettings::Generate($_dbInfo);
			break;
			case "Invoices":
				$returnedCode .= "<script>history.pushState(null, null, '/index.php?page=$_pageid');</script>";
				$returnedCode .= PageViewInvoices::Generate($_dbInfo);
				break;
		}
		return $returnedCode;
	}

	function Action_LoadViewAccount($_dbInfo, $_accountid) {
		$returnedCode = "";
		$returnedCode .= "<script>history.pushState(null, null, '/index.php?page=ViewAccount&accountid=$_accountid');</script>";
		$returnedCode .= PageViewAccount::Generate($_dbInfo, $_accountid);
		return $returnedCode;
	}

	function Action_LoadViewEmployee($_dbInfo, $_employeeid) {
		$returnedCode = "";
		$returnedCode .= "<script>history.pushState(null, null, '/index.php?page=ViewEmployee&employeeid=$_employeeid');</script>";
		$returnedCode .= PageViewEmployee::Generate($_dbInfo, $_employeeid);
		return $returnedCode;
	}

	function Action_ValidateNewAccountForm($_dbInfo, $_formInformation) { 
		$retVar = DatabaseManager::AddNewAccount($_dbInfo, $_formInformation);
		$boolString = $retVar['success'] ? "true" : "false";
		$retString = $boolString . "|" . $retVar['response'];
		return $retString;
	}

	function Action_ValidateNewEmployeeForm($_dbInfo, $_formInformation) { 
		$retVar = DatabaseManager::AddNewEmployee($_dbInfo, $_formInformation);
		$boolString = $retVar['success'] ? "true" : "false";
		$retString = $boolString . "|" . $retVar['response'];
		return $retString;
	}

	function Action_ValidateNewRoleForm($_dbInfo, $_name, $_perms, $_dispatchable, $_roleid) {
		$retVar = DatabaseManager::AddNewRole($_dbInfo, $_name, $_perms, $_dispatchable, $_roleid);
		$boolString = $retVar['success'] ? "true" : "false";
		$retString = $boolString . "|" . $retVar['response'];
		return $retString;
	}

	function Action_ValidateNewShiftForm($_dbInfo, $_shiftInformation) {
		$retVar = DatabaseManager::AddNewShift($_dbInfo, $_shiftInformation);
		$boolString = $retVar['success'] ? "true" : "false";
		$retString = $boolString . "|" . $retVar['response'];
		return $retString;
	}

	function Action_StartSession($_dbInfo) {
		session_unset();
		$_SESSION['email'] = $_POST['email'];
		$_SESSION['password'] = $_POST['password'];
		$accountInfo = DatabaseManager::GetLoginInformation($_dbInfo);

		if (is_array($accountInfo)) {
			$_SESSION['companyid'] = $accountInfo['companyid'];
			$_SESSION['firstname'] = $accountInfo['firstname'];
			$_SESSION['eid'] = $accountInfo['id'];
		}
	}

	function Action_OpenSettingsMenu($_dbInfo) {
		return MenuSettings::Generate($_dbInfo);
	}

	function Action_GetMonthSchedule($_dbInfo, $_month, $_year, $_employeeid) {
		if (!DatabaseManager::CheckPermissions($_dbInfo, ['ves'])) {
			if ($_SESSION['eid'] != $_employeeid) {
							return "";
			}
  }
		$monthSchedule = Calendar::GetMonthSchedule($_dbInfo, $_month, $_year, $_employeeid);
		return $monthSchedule;
	}

	function Action_StartPortal($_dbInfo, $_pageData) {
		$returnedCode = "";

		$_page = "Dashboard";
		$_accountid = 0;

		if (isset($_pageData['page'])) {
			$_page = $_pageData['page'];
		}

		if (isset($_pageData['accountid'])){
			$_accountid = $_pageData['accountid'];
		}

		if (isset($_pageData['employeeid'])){
			$_employeeid = $_pageData['employeeid'];
		}

		if (Action_CheckSession($_dbInfo)) {
			$returnedCode .= <<<HTML
				<script>
					
				</script>
			HTML;

			$returnedCode .= <<<HTML
			<div id='topbar_container'>
				<div class='sitelogo'>
					<img src='img/logo1.png' width='400px'/>
				</div>
				<div class='topright_pane'>
					<div class='topbarbuttons'>
						<img src='img/user_green.png'/>
						<img src='img/help_green.png'/>
						<img class='open_settings_page' src='img/settings_green.png'/>
					</div>
					<div class='searchboxholder'>
						<input type='text' placeholder='Search'/><img src='img/search_gray.png'/>
					</div>
					<div class='topbarloginnote'>
						Welcome back, $_SESSION[firstname] <span class='text_button_type_1' id='logoutbutton'></span>
					</div>
				</div>
			</div>	
			<div id='leftpane_container'>
			HTML;
	
			$returnedCode .= PopulateLeftPaneMenu($_dbInfo);
	
			$returnedCode .= <<<HTML
			</div>
				<div id='rightpane_container'>
			HTML;
			
			if ($_page == "ViewAccount") {
				$returnedCode .= Action_LoadViewAccount($_dbInfo, $_accountid);
			}
			if ($_page == "ViewEmployee") {
				$returnedCode .= Action_LoadViewEmployee($_dbInfo, $_employeeid);
			}
 			else {
				$returnedCode .= Action_LoadPage($_dbInfo, $_page);
			}
			$returnedCode .= "</div>";
		}
		else {
			$returnedCode .= <<<HTML
			<script>
				$('.input_login_password').keydown(function (event) {
					if (event.keyCode === 13) {
					event.preventDefault();
					var loginEmail = $('.input_login_email').val();
					var loginPassword = $('.input_login_password').val();
					StartSession(loginEmail, loginPassword);
					}
				});
				$('.input_login_email').keydown(function (event) {
					if (event.keyCode === 13) {
					event.preventDefault();
					var loginEmail = $('.input_login_email').val();
					var loginPassword = $('.input_login_password').val();
					StartSession(loginEmail, loginPassword);
					}
				});
				$('.input_login_button').click(function() {
					var loginEmail = $('.input_login_email').val();
					var loginPassword = $('.input_login_password').val();
					StartSession(loginEmail, loginPassword);
				});
			</script>
			HTML;

			$returnedCode .= <<<HTML
			<div class='login_wrapper_bg'>
				<div class='login_page_backer'>
					<img src='img/logo2.png' width='340px' style='margin-left:8px;'/>
					<div class='login_page_content'>
					<div class='formsection_line' style='margin-bottom:10px;'>
						<input type='text' placeholder='Email' class='input_login_email formsection_input_2'/>
					</div>
					<div class='formsection_line' style='margin-bottom:10px;'>
						<input type='password' placeholder='Password' class='input_login_password formsection_input_2'/>
					</div>
					<div class='formsection_line_centered' style='margin-bottom:20px;'>
						<div class='input_login_button button_type_2' style='padding-top:7px;padding-bottom:7px;padding-left:45px;padding-right:45px'>Log In</div>
					</div>
					<div class='formsection_line_centered' style='margin-bottom:20px;'>
						<div class='formsection_input_centered_text_button'>Trouble Logging In?</div>
					</div>
					<div class='formsection_line_centered'>
						<div class='formsection_input_centered_text'>This page is for current account holders. To set up a new account, reach out to your account manager.</div>
					</div>
				</div>
			</div>
			HTML;
		}

		return $returnedCode;
	}

	if (isset($_POST['action'])) {
		$action = $_POST['action'];
		
		switch ($action) {
			case "Logout":
				echo("<script>history.pushState(null, null, '/index.php');</script>");
				session_unset();
				session_destroy();
				die();
			case "StartPortal":
				$get_PageData = json_decode($_POST['pagedata'], true);
				echo (Action_StartPortal($dbInfo, $get_PageData));
				break;
			case "LeftPaneButtonClick":
				$buttonid = $_POST['buttonid'];
				$get_currentPage = 1;
				echo (Action_LoadPage($dbInfo, $buttonid));
				break;
			case "GenerateNewRolePage":
				$post_roleid = 0;
				if (isset($_POST['roleid'])) {
					$post_roleid = $_POST['roleid'];
				}
				echo (PopupNewRole::Generate($dbInfo, $post_roleid));
				break;
			case "GenerateNewShiftPage":
				$post_shiftid = 0;
				if (isset($_POST['shiftid'])) {
					$post_shiftid = $_POST['shiftid'];
				}
				echo (PopupNewShift::Generate($dbInfo, $post_shiftid));
				break;
			case "GenerateNewAccountPage":
				echo (PopupNewAccount::Generate($dbInfo));
				break;
			case "GenerateNewEmployeePage":
				echo (PopupNewEmployee::Generate($dbInfo));
				break;
			case "GenerateNewHolidaySchedulePage":
				echo (PopupNewHolidaySchedule::GeneratePage($dbInfo));
				break;
			case "SubmitNewAccountForm":
				$formData = json_decode($_POST['formdata'], true);
				echo(Action_ValidateNewAccountForm($dbInfo, $formData));
				break;
			case "SubmitNewEmployeeForm":
				$formData = json_decode($_POST['formdata'], true);
				echo(Action_ValidateNewEmployeeForm($dbInfo, $formData));
				break;
			case "ViewAccount":
				$accountid = $_POST['accountid'];
				echo(Action_LoadViewAccount($dbInfo, $accountid));
				break;
			case "ViewEmployee":
				$employeeid = $_POST['employeeid'];
				echo(Action_LoadViewEmployee($dbInfo, $employeeid));
				break;
			case "StartSession":
				Action_StartSession($dbInfo);
				break;
			case "OpenSettingsMenu":
				echo(Action_OpenSettingsMenu($dbInfo));
				break;
			case "AddNewRole":
				$perms = $_POST['perms'];
				$rolename = $_POST['name'];
				$isDispatchable = $_POST['isDispatchable'];
				$roleId = $_POST['roleid'];
				echo(Action_ValidateNewRoleForm($dbInfo, $rolename, $perms, $isDispatchable, $roleId));
				break;
			case "AddNewShift":
				$postInfo = $_POST['shiftInformation'];
				$shiftInformation = json_decode($postInfo, true);
				echo(Action_ValidateNewShiftForm($dbInfo, $shiftInformation));
				break;
			case "getmonthschedule":
				$month = $_POST['month'];
				$year = $_POST['year'];
				$employeeid = $_POST['eid'];
				echo(Action_GetMonthSchedule($dbInfo, $month, $year, $employeeid));
				break;
			break;
		}
	}
?>