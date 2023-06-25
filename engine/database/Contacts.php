<?php

trait DatabaseContacts {
    public static function GetContact($_dbInfo, $_contactid)
    {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        if (self::ValidateLogin($pdo1)) {
            $stmt = $pdo->prepare("SELECT * FROM `contacts` WHERE `id` = :id");
            $stmt->bindParam(":id", $_contactid);
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            $pdo1 = null;
            $pdo = null;

            return $results;
        }
        return false;
    }
}

?>