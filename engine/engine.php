<?php
	session_start();
	require('databaseinterface.php');
	require_once("/nginx/protectedfiles/config.php");
	require('user.php');
	require('utils.php');
	require('menuitems.php');

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

	function Action_InitPortal($_dbInfo) {
		$userInfo = DatabaseManager::GetLoginInformation($_dbInfo, $_SESSION['email'], $_SESSION['password']);
		$_SESSION['companyid'] = $userInfo['companyid'];
		$_SESSION['userid'] = $userInfo['id'];

		$finalID = $userInfo['companyid'] + 1000;

		echo("<div id='topbar_container' class='outline'>");
		echo("<div class='sitelogo'>Service <div class='color1'>Manager</div></div>");
		echo("</div>");

		echo("<div id='leftpane_container' class='outline'>");

			echo(LeftPaneMenuItem::GenerateButton("Dashboard"));
			echo(LeftPaneMenuItem::GenerateButton("Accounts"));

		echo("</div>");

		echo("<div id='rightpane_container' class='outline'>");

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

	if (isset($_POST['action'])) {
		$action = $_POST['action'];
		
		switch ($action) {
			case "CheckSession":
				Action_CheckSession($dbInfo);
				break;
			case "InitPortal":
				Action_InitPortal($dbInfo);
				break;
			case "InitLogin":
				Action_InitLogin();
				break;
			case "CheckLogin":
				$_SESSION['email'] = $_POST['email'];
				$_SESSION['password'] = $_POST['password'];
				break;
		}
	}
?>