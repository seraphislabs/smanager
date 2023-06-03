<?php
	require_once('databaseinterface.php');
	require_once('user.php');
	require_once('utils.php');
	session_start();

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

	if (isset($_POST['action'])) {
		$action = $_POST['action'];
		
		switch ($action) {
			case "CheckSession":
				echo (Action_CheckSession());
				break;
			case "InitPortal":
				Action_InitPortal();
				break;
		}
	}
?>