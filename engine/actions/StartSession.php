<?php

trait ActionStartSession {
    public static function StartSession($_postData) {
		OpLog::Log("Action: StartSession");

		
		$postEmail = $_POST['email'];
		$postPassword = $_POST['password'];
        session_unset();

		$results = DatabaseManager::FirstLoginValidate($postEmail, $postPassword);
		if ($results !== false) {
			$newToken = UUID::Create();

			$usrJoin = DatabaseManager::GetUserJoinedInformation($results['id']);

			$_SESSION['token'] = $newToken;
			$rdb = RDB::getInstance();
			$jResults = json_encode($usrJoin);
			$rdb->set($newToken, $jResults, 30000);
		}
    }
}

?>