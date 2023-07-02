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

    public static function ManuallyValidateLogin($_dbInfo)
    {
        $_password = $_SESSION['password'];

        $pdo = self::connect($_dbInfo, 'servicemanager');
        $retVal = self::GetLoginPasswordHash($pdo);
        if (!empty($retVal)) {
            if (PasswordEncrypt::Check($_password, $retVal)) {
                $pdo = null;
                return true;
            }
        }
        $pdo = null;
        return false;
    }

    private static function ValidateLogin($_pdo)
    {
        $_password = $_SESSION['password'];

        // Retreive password hash from database
        $retVal = self::GetLoginPasswordHash($_pdo);
        if (!empty($retVal)) {
            if (PasswordEncrypt::Check($_password, $retVal)) {
                return true;
            }
        }

        return false;
    }

    private static function GetLoginPasswordHash($_pdo)
    {
        $_email = $_SESSION['email'];
        $stmt = $_pdo->prepare("SELECT * FROM `users` WHERE `workemail`=:email");
        $stmt->bindParam(":email", $_email);
        $stmt->execute();
        if ($stmt->rowCount() == 1) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $passHash = $results[0]['password'];

            return $passHash;
        }
    }

    public static function GetLoginInformation($_dbInfo)
    {
        $_email = $_SESSION['email'];
        $pdo = self::connect($_dbInfo, 'servicemanager');

        if (self::ValidateLogin($pdo)) {
            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `workemail`=:email");
            $stmt->bindParam(":email", $_email);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $pdo = null;
                return $results[0];
            }
        }

        $pdo = null;
        return null;
    }

    public static function GetUserPermissions($_dbInfo)
    {
        $_companyid = $_SESSION['companyid'];

        $ccid = $_companyid + 1000;
        $_email = $_SESSION['email'];
        $pdo = self::connect($_dbInfo, 'servicemanager');
        $pdo2 = self::connect($_dbInfo, "company_" . $ccid);

        if (self::ValidateLogin($pdo)) {
            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `workemail`=:email");
            $stmt->bindParam(":email", $_email);
            $stmt->execute();
            if ($stmt->rowCount() == 1) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $roleid = $results[0]['role'];

                $stmt2 = $pdo2->prepare("SELECT * FROM `roles` WHERE `id`=:roleid");
                $stmt2->bindParam(":roleid", $roleid);
                $stmt2->execute();

                $permString = "";

                if ($stmt2->rowCount() == 1) {
                    $results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                    $permString = $results2[0]['permissions'];
                }

                $pdo = null;
                $pdo2 = null;
                $retArray = explode("|", $permString);
                return $retArray;
            }
        }

        $pdo = null;
        $pdo2 = null;

        return null;
    }

    public static function ManuallyCheckPermissions($_userPerms, $_perms) {
        $diff = array_diff($_perms, $_userPerms);

        if (empty($diff)) {
            return true;
        }

        return false;
    }
}
?>