<?php

trait DatabaseEmployeeRoles {
    public static function GetAllEmployeeRoles($_dbInfo, $asAssoc) {
        $_companyid = $_SESSION['companyid'];

        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        $retVal = [];

        if (self::ValidateLogin($pdo1)) {
            $stmt = $pdo->prepare("SELECT * FROM `roles` ORDER BY `id`");
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                if ($asAssoc) {
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach($results as $result) {
                        $retVal[$result['id']] = $result;
                    }
                }
                else {
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $retVal = $results;
                }
            }
        }
        $pdo1 = null;
        $pdo = null;
        return $retVal;
    }

    public static function GetEmployeeRole($_dbInfo, $_roleid) {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        if (self::ValidateLogin($pdo1)) {
            $stmt = $pdo->prepare("SELECT * FROM `roles` WHERE `id` = :roleid");
            $stmt->bindParam(":roleid", $_roleid);
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            $pdo1 = null;
            $pdo = null;

            $pdo1 = null;
            $pdo = null;
            return $results;
        }
        $pdo1 = null;
        $pdo = null;
        return false;
    }

    public static function AddNewEmployeeRole($_dbInfo, $_name, $_perms, $_dispatchable, $_roleid) {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        $retVar = [];
        $retVar['success'] = true;
        $retVar['response'] = "Success";

        if (self::ValidateLogin($pdo1)) {
            if (ValidateField::Validate($_name, 'name')) {

                if ($_roleid > 0) {
                    $stmt = $pdo->prepare("SELECT * FROM `roles` WHERE `id` = :roleid");
                    $stmt->bindParam(":roleid", $_roleid);
                    $stmt->execute();
                    $rowCount = $stmt->rowCount();
                    if ($rowCount == 1) {
                        $results = $stmt->fetch(PDO::FETCH_ASSOC);
                        $rowid = $results['id'];
                        $stmt = $pdo->prepare("UPDATE `roles` SET `name` = :name , `permissions` = :permissions , `dispatchable` = :dispatchable WHERE `id` = :roleid");
                        $stmt->bindParam(":name", $_name);
                        $stmt->bindParam(":permissions", $_perms);
                        $stmt->bindParam(":dispatchable", $_dispatchable);
                        $stmt->bindParam(":roleid", $_roleid);
                        $stmt->execute();
                    }
                }
                else {
                    $stmt = $pdo->prepare("INSERT INTO `roles` (`name`,`permissions`,`dispatchable`) VALUES (:name,:permissions,:dispatchable)");
                    $stmt->bindParam(":name", $_name);
                    $stmt->bindParam(":permissions", $_perms);
                    $stmt->bindParam(":dispatchable", $_dispatchable);
                    $stmt->execute();
                }
            }
            else {
                $retVar['success'] = false;
                $retVar['response'] = "Validation Error";
                $pdo1 = null;
                $pdo = null;
                return $retVar;
            }
        }
        else {
            $retVar['success'] = false;
            $retVar['response'] = "Database Error";
        }
        $pdo1 = null;
        $pdo = null;
        return $retVar;
    }
}

?>