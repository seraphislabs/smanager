<?php

trait DatabaseHolidaySchedules {
    public static function GetAllHolidaySchedules($_dbInfo) {
        $_companyid = $_SESSION['companyid'];

        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        $retVal = [];

        if (self::ValidateLogin($pdo1)) {
            $stmt = $pdo->prepare("SELECT * FROM `holidayschedules` ORDER BY `id`");
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($results as $result) {
                    $rAr = [];
                    $rAr['scheduleinfo'] = $result;

                    $stmt = $pdo->prepare("SELECT * FROM `offdays` WHERE `holidayscheduleid` = :holidayscheduleid");
                    $stmt->bindParam(":holidayscheduleid", $result['id']);
                    $stmt->execute();

                    $resultsx = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $rAr['offdays'] = $resultsx;

                    array_push($retVal, $rAr);
                }

                return $retVal;
            }
        }  
        return null;
    }
}

?>