<?php

class DatabaseManager {
    public static function connect($_dbInfo, $_dbname) {
        $dsn = "mysql:host=localhost;dbname={$_dbname}";

        try {
            $pdo = new PDO($dsn, $_dbInfo['dusername'], $_dbInfo['dpassword']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }

        return null;
    }

    public static function ManuallyValidateLogin($_dbInfo, $_email, $_password) {
        $pdo = self::connect($_dbInfo, 'servicemanager');
        $retVal = self::GetLoginPasswordHash($pdo, $_email);
			if (!empty($retVal)) {
				if (PasswordEncrypt::Check($_password, $retVal)) {
                    $pdo = null;
					return true;
				}
			}

            return false;
    }

    private static function ValidateLogin($_pdo, $_email, $_password) {
        // Retreive password hash from database
			$retVal = self::GetLoginPasswordHash($_pdo, $_email);
			if (!empty($retVal)) {
				if (PasswordEncrypt::Check($_password, $retVal)) {
					return true;
				}
			}

            return false;
    }

    private static function GetLoginPasswordHash($_pdo, $_email) {
        $stmt = $_pdo->prepare("SELECT * FROM `users` WHERE `email`=:email");
        $stmt->bindParam(":email", $_email);
        $stmt->execute();
        if ($stmt->rowCount() == 1) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $passHash = $results[0]['password'];

            return $passHash;
        }
    }

    public function GetLoginInformation($_dbInfo, $_email, $_password) {
        $pdo = self::connect($_dbInfo, 'servicemanager');

        if (self::ValidateLogin($pdo, $_email, $_password)) {
            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `email`=:email");
            $stmt->bindParam(":email", $_email);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $pdo = null;
                return $results[0];
            }
        }
    }

    public static function GetUserPermissions($_dbInfo, $_email, $_password) {
        $pdo = self::connect($_dbInfo, 'servicemanager');

        if (self::ValidateLogin($pdo, $_email, $_password)) {
            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `email`=:email");
            $stmt->bindParam(":email", $_email);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $pdo = null;
                $retArray = explode("|", $results[0]['permissions']);
                return $retArray;
            }
        }
    }

    public static function CheckPermissions($_dbInfo, $_email, $_password, $_perms) {
        $perms = self::GetUserPermissions($_dbInfo, $_email, $_password);

        $diff = array_diff($_perms, $perms);

        if (empty($diff)) {
            return true;
        }

        return false;
    }
}

?>