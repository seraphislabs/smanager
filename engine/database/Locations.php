<?php

trait DatabaseLocations {
    public static function GetAllLocationsByAccount($_dbInfo, $_accountid) {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        if (self::ValidateLogin($pdo1)) {
            $stmt = $pdo->prepare("SELECT * FROM `locations` WHERE `accountid` = :id");
            $stmt->bindParam(":id", $_accountid);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $pdo1 = null;
            $pdo = null;

            return $results;
        }
        return false;
    }
}

?>