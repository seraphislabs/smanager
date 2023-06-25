<?php

trait DatabaseEmployeeShifts {
    public static function GetAllEmployeeShifts($_dbInfo, $asAssoc) {
        $_companyid = $_SESSION['companyid'];

        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        $retVal = [];

        if (self::ValidateLogin($pdo1)) {
            $stmt = $pdo->prepare("SELECT * FROM `shifts` ORDER BY `id`");
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

    public static function GetEmployeeShift($_dbInfo, $_shiftid) {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        if (self::ValidateLogin($pdo1)) {
            $stmt = $pdo->prepare("SELECT * FROM `shifts` WHERE `id` = :shiftid");
            $stmt->bindParam(":shiftid", $_shiftid);
            $stmt->execute();
            $results = $stmt->fetch(PDO::FETCH_ASSOC);
            $pdo1 = null;
            $pdo = null;

            return $results;
        }
        return false;
    }

    public static function AddNewEmployeeShift($_dbInfo, $_shiftInformation) {
        $_companyid = $_SESSION['companyid'];
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        $shiftid = 0;
        if (array_key_exists('id', $_shiftInformation)) {
            $shiftid = (int)$_shiftInformation['id'];
        }

        $name = $_shiftInformation['name'];

        $mondayString = "";
        if (array_key_exists('monday', $_shiftInformation)) {
            $mondayString = $_shiftInformation['monday']['start'] . "|" . $_shiftInformation['monday']['end'];
        }
        $tuesdayString = "";
        if (array_key_exists('tuesday', $_shiftInformation)) {
            $tuesdayString = $_shiftInformation['tuesday']['start'] . "|" . $_shiftInformation['tuesday']['end'];
        }
        $wednesdayString = "";
        if (array_key_exists('wednesday', $_shiftInformation)) {
            $wednesdayString = $_shiftInformation['wednesday']['start'] . "|" . $_shiftInformation['wednesday']['end'];
        }
        $thursdayString = "";
        if (array_key_exists('thursday', $_shiftInformation)) {
            $thursdayString = $_shiftInformation['thursday']['start'] . "|" . $_shiftInformation['thursday']['end'];
        }
        $fridayString = "";
        if (array_key_exists('friday', $_shiftInformation)) {
            $fridayString = $_shiftInformation['friday']['start'] . "|" . $_shiftInformation['friday']['end'];
        }
        $saturdayString = "";
        if (array_key_exists('saturday', $_shiftInformation)) {
            $saturdayString = $_shiftInformation['saturday']['start'] . "|" . $_shiftInformation['saturday']['end'];
        }
        $sundayString = "";
        if (array_key_exists('sunday', $_shiftInformation)) {
            $sundayString = $_shiftInformation['sunday']['start'] . "|" . $_shiftInformation['sunday']['end'];
        }

        $retVar = [];
        $retVar['success'] = true;
        $retVar['response'] = "Success";

        if (self::ValidateLogin($pdo1)) {
            if ($shiftid > 0) {
                $stmt = $pdo->prepare("SELECT * FROM `shifts` WHERE `id` = :shiftid");
                $stmt->bindParam(":shiftid", $shiftid);
                $stmt->execute();
                $rowCount = $stmt->rowCount();
                if ($rowCount == 1) {
                    $results = $stmt->fetch(PDO::FETCH_ASSOC);
                    $shiftid = $results['id'];
                    $stmt = $pdo->prepare("UPDATE `shifts` SET 
                        `name` = :name ,
                        `monday` = :mondayString ,
                        `tuesday` = :tuesdayString ,
                        `wednesday` = :wednesdayString ,
                        `thursday` = :thursdayString ,
                        `friday` = :fridayString ,
                        `saturday` = :saturdayString ,
                        `sunday` = :sundayString  
                        WHERE `id` = :shiftid");
                    $stmt->bindParam(":name", $name);
                    $stmt->bindParam(":mondayString", $mondayString);
                    $stmt->bindParam(":tuesdayString", $tuesdayString);
                    $stmt->bindParam(":wednesdayString", $wednesdayString);
                    $stmt->bindParam(":thursdayString", $thursdayString);
                    $stmt->bindParam(":fridayString", $fridayString);
                    $stmt->bindParam(":saturdayString", $saturdayString);
                    $stmt->bindParam(":sundayString", $sundayString);
                    $stmt->bindParam(":shiftid", $shiftid);
                    $stmt->execute();
                }
            }
            else {
                $stmt = $pdo->prepare("INSERT INTO `shifts` (`name`,`monday`,`tuesday`,`wednesday`,`thursday`,`friday`,`saturday`,`sunday`) VALUES (:name,:mondayString,:tuesdayString,:wednesdayString,:thursdayString,:fridayString,:saturdayString,:sundayString)");
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":mondayString", $mondayString);
                $stmt->bindParam(":tuesdayString", $tuesdayString);
                $stmt->bindParam(":wednesdayString", $wednesdayString);
                $stmt->bindParam(":thursdayString", $thursdayString);
                $stmt->bindParam(":fridayString", $fridayString);
                $stmt->bindParam(":saturdayString", $saturdayString);
                $stmt->bindParam(":sundayString", $sundayString);
                $stmt->execute();
            }
        }
        else {
            $retVar['success'] = false;
            $retVar['response'] = "Database Error";
        }
        return $retVar;
    }
}

?>