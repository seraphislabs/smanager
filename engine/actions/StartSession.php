<?php

trait ActionStartSession {
    public static function StartSession($_dbInfo, $_postData) {
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
}

?>