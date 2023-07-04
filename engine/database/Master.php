<?php
trait DatabaseMaster
{
    public static function connect($_dbInfo, $_dbname)
    {
        $dsn = "mysql:host=localhost;dbname={$_dbname}";

        try {
            $pdo = new PDO($dsn, $_dbInfo['dusername'], $_dbInfo['dpassword']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("SET time_zone = '-05:00'");
            return $pdo;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function FirstLoginValidate($email, $password) {
        $db = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $results = $db->fetch("SELECT * FROM `users` WHERE `workemail`=:email", array(
            "email" => $email
        ));

        if ($results != false) {
            $hashed = $results['password'];
            if (PasswordEncrypt::Check($password, $hashed)) {
                return $results;
            }
        }

        return false;
    }

    public static function GetUserJoinedInformation($userid) {
        $db = DBI::getInstance($GLOBALS['dbinfo']['db']);

        $results = $db->fetch("SELECT `users`.*, `roles`.`permissions` FROM `users` INNER JOIN `roles` ON `roles`.`id` = `users`.`role` WHERE `users`.`id`=:userid", array(
            "userid" => $userid
        ));

        return $results;
    }


    public static function CheckPermission($permission) {
        $ai = self::GetActiveSession();
        $permissions  = $ai['permissions'];
        $perms = explode("|", $permissions);

        if (in_array($permission, $perms)) {
            OpLog::Log("Database: CheckPermission");
            OpLog::Log("--Returned: true");
            return true;
        }

        return false;
    }

    public static function GetActiveSession() {
        $myToken = $_SESSION['token'];

        $rdb = RDB::getInstance();
        $ai = $rdb->get($myToken);

        if ($ai === false) {
            Actions::Logout();
            die();
        }
        else {
            $rdb->extendExpiry($myToken, 30000);
            return $ai;
        }
    }
}
?>