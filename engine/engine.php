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

	function Action_InitPortal($_dbInfo, $_page, $_currentPage) {
		$userInfo = DatabaseManager::GetLoginInformation($_dbInfo, $_SESSION['email'], $_SESSION['password']);
		$_SESSION['companyid'] = $userInfo['companyid'];
		$_SESSION['userid'] = $userInfo['id'];

		$finalID = $userInfo['companyid'] + 1000;

		echo("<div id='topbar_container'>");
		echo("<div class='sitelogo'>Service<div class='color1'>Manager</div> 360</div>");
		echo("<div class='topright_pane'>
		<div class='topbarbuttons'>
		<img src='img/user_green.png'/>
		<img src='img/help_green.png'/>
		<img src='img/settings_green.png'/>
		</div>
		<div class='searchboxholder'><input type='text' placeholder='Search'/><img src='img/search_gray.png'/></div>
		</div>");
		echo("</div>");

		echo("<div id='leftpane_container'>");

		PopulateLeftPaneMenu($_dbInfo);

		echo("</div>");

		echo("<div id='rightpane_container'>");
		echo Action_LoadPage($_dbInfo, $_page, $_currentPage);
		echo("</div>");
	}

	function Action_InitLogin() {
		echo("<center>
		<h1>Login</h1>
		<input type='text' class='input_login_email'/>
		<input type='text' class='input_login_password'/>
		<input type='button' class='input_login_button'/>
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

	if (isset($_POST['action'])) {
		$action = $_POST['action'];
		
		switch ($action) {
			case "CheckSession":
				Action_CheckSession($dbInfo);
				break;
			case "InitPortal":
				$get_Page = $_POST['page'];
				$get_currentPage = "";
				if ($get_Page == "Accounts") {
					$get_currentPage = $_POST['currentPage'];
				}
				Action_InitPortal($dbInfo, $get_Page, $get_currentPage);
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
		}
	}
?>