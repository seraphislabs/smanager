<?php
	session_start();
	require('databaseinterface.php');
	require_once("/nginx/protectedfiles/config.php");
	require('user.php');
	require('utils.php');
	require('menuitems.php');
	require('pages.php');

	function PopulateLeftPaneMenu($_dbInfo) {
		$perms = DatabaseManager::GetUserPermissions($_dbInfo, $_SESSION['email'], $_SESSION['password']);

		echo(LeftPaneMenuItem::GenerateButton("Dashboard"));

		if (in_array('ac', $perms)) {
			echo(LeftPaneMenuItem::GenerateButton("Accounts"));
		}
	}

	function Action_CheckSession($_dbInfo) {
		$isLogged = false;
		if (isset($_SESSION['email']) && isset($_SESSION['password'])) {
			$retVal = DatabaseManager::ManuallyValidateLogin($_dbInfo, $_SESSION['email'], $_SESSION['password']);
			echo $retVal ? 'true' : 'false';
		}
		else {
			echo ("false");
		}
	}

	function Action_InitPortal($_dbInfo, $_page, $_currentPage, $_dataid) {
		$userInfo = DatabaseManager::GetLoginInformation($_dbInfo, $_SESSION['email'], $_SESSION['password']);
		$_SESSION['companyid'] = $userInfo['companyid'];
		$_SESSION['userid'] = $userInfo['id'];
		$_SESSION['firstname'] = $userInfo['firstname'];

		$finalID = $userInfo['companyid'] + 1000;

		echo("<div id='topbar_container'>");
		echo("<div class='sitelogo'><img src='img/logo1.png' width='400px'/></div>");
		echo("<div class='topright_pane'>
		<div class='topbarbuttons'>
		<img src='img/user_green.png'/>
		<img src='img/help_green.png'/>
		<img src='img/settings_green.png'/>
		</div>
		<div class='searchboxholder'><input type='text' placeholder='Search'/><img src='img/search_gray.png'/></div>
		<div class='topbarloginnote'>Welcome back, " . $_SESSION['firstname']  . " <span class='text_button_type_1' id='logoutbutton'>LOGOUT</span></div>
		</div>");
		echo("</div>");

		echo("<div id='leftpane_container'>");

		PopulateLeftPaneMenu($_dbInfo);

		echo("</div>");

		echo("<div id='rightpane_container'>");
		if ($_page == "ViewAccount") {
			echo Action_LoadViewAccount($_dbInfo, $_dataid);
		}
		else {
			echo Action_LoadPage($_dbInfo, $_page, $_currentPage);
		}
		echo("</div>");
	}

	function Action_InitLogin() {
		echo("<center>
		<h1>Login</h1>
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
				<div class='formsection_
				</div>
			</div>
		</div>	
		</center>
		");
	}

	function Action_LoadPage($_dbInfo, $_pageid, $_currentPage) {
		switch ($_pageid) {
			case "Accounts":
				echo("<script>history.pushState(null, null, '/index.php?page=$_pageid&currentPage=$_currentPage');</script>");
				return PageManager::GenerateAccountsPage($_dbInfo, $_SESSION['email'], $_SESSION['password'], $_SESSION['companyid'], $_currentPage);
				break;
			case "Dashboard":
				echo("<script>history.pushState(null, null, '/index.php?page=$_pageid');</script>");
				break;
		}
	}

	function Action_LoadViewAccount($_dbInfo, $_accountid) {
		echo("<script>history.pushState(null, null, '/index.php?page=ViewAccount&accountid=$_accountid');</script>");
		return PageManager::GenerateViewAccountPage($_dbInfo, $_SESSION['email'], $_SESSION['password'], $_SESSION['companyid'], $_accountid);
	}

	function Action_ValidateNewAccountForm($_dbInfo, $_formInformation) { 
		$retVar = DatabaseManager::AddNewAccount($_dbInfo, $_SESSION['email'], $_SESSION['password'], $_SESSION['companyid'], $_formInformation);
		$boolString = $retVar['success'] ? "true" : "false";
		$retString = $boolString . "|" . $retVar['response'];
		return $retString;
	}

	if (isset($_POST['action'])) {
		$action = $_POST['action'];
		
		switch ($action) {
			case "Logout":
				echo("<script>history.pushState(null, null, '/index.php');</script>");
				session_destroy();
				die();
				break;
			case "CheckSession":
				Action_CheckSession($dbInfo);
				break;
			case "InitPortal":
				$get_Page = $_POST['page'];
				$get_currentPage = 0;
				$get_accountid = 0;
				if ($get_Page == "Accounts") {
					$get_currentPage = $_POST['currentPage'];
				}
				else if ($get_Page == "ViewAccount") {
					$get_accountid = $_POST['accountid'];
				}
				Action_InitPortal($dbInfo, $get_Page, $get_currentPage, $get_accountid);
				break;
			case "InitLogin":
				Action_InitLogin();
				break;
			case "CheckLogin":
				$_SESSION['email'] = $_POST['email'];
				$_SESSION['password'] = $_POST['password'];
				break;
			case "LeftPaneButtonClick":
				$buttonid = $_POST['buttonid'];
				$get_currentPage = 1;
				echo (Action_LoadPage($dbInfo, $buttonid, $get_currentPage));
				break;
			case "VAPageRight":
				$get_currentPage = $_POST['currentPage'];
				echo(Action_LoadPage($dbInfo, 'Accounts', $get_currentPage+1));
				break;
			case "VAPageLeft":
				$get_currentPage = $_POST['currentPage'];
				echo(Action_LoadPage($dbInfo, 'Accounts', $get_currentPage-1));
				break;
			case "GenerateNewAccountPage":
				echo (PageManager::GenerateNewAccountPage($dbInfo, $_SESSION['email'], $_SESSION['password']));
				break;
			case "SubmitNewAccountForm":
				$formData = json_decode($_POST['formdata'], true);
				echo(Action_ValidateNewAccountForm($dbInfo, $formData));
				break;
			case "ViewAccount":
				$accountid = $_POST['accountid'];
				echo(Action_LoadViewAccount($dbInfo, $accountid));
				break;
		}
	}
?>