<?php
	session_start();
	require("/nginx/protectedfiles/config.php");
	require_once('databaseinterface.php');
	require('user.php');
	require('utils.php');

	function Action_CheckSession() {
		$isLogged = true;
		if (!isset($_SESSION['email']) || !isset($_SESSION['password']) || !isset($_SESSION['companyid'])) {
			$isLogged = false;
		}

		return $isLogged ? 'true' : 'false';
	}

	function Action_InitPortal() {
		echo("<div id='topbar_container' class='outline'>");
		echo("<div class='sitelogo'>Service <div class='color1'>Manager</div></div>");
		echo("</div>");

		echo("<div id='leftpane_container' class='outline'>2");
		echo("</div>");

		echo("<div id='rightpane_container' class='outline'>3");
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

	function Action_CheckLogin($_email, $_password) {
	}

	if (isset($_POST['action'])) {
		$action = $_POST['action'];
		
		switch ($action) {
			case "CheckSession":
				echo (Action_CheckSession());
				break;
			case "InitPortal":
				Action_InitPortal();
				break;
			case "InitLogin":
				Action_InitLogin();
				break;
			case "CheckLogin":
				$_SESSION['email'] = $_POST['email'];
				$_SESSION['password'] = $_POST['password']; 
				$_SESSION['companyid'] = "1";
				Action_CheckLogin($pEmail, $pPassword);
				break;
		}
	}
?>