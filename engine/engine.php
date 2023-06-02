<?php
	require_once('databaseinterface.php');
	require_once('user.php');
	require_once('utils.php');

	$password = "test";
	$email = "seraphislabs@gmail.com";
	$hashed = PasswordEncrypt::Encrypt($password);

	$nUser = SM_UserManager::CreateUser($email, $hashed);
	$insertId = $SMDATA->insert('users', $nUser->AsArray());

	$retVal = PasswordEncrypt::Check("test2", $hashed);
?>