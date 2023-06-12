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

    public static function GetAccounts($_dbInfo, $_email, $_password, $_companyid, $_currentPage) {
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);

        if ($_currentPage == 0) {
            $_currentPage = 1;
        }

        $rOffset = (30 * ($_currentPage-1));
        $rLimit = 30;

        if (self::ValidateLogin($pdo1, $_email, $_password)) {
            $sql = "SELECT * FROM `accounts`";
            $stmt = $pdo->prepare($sql);
            $resultsx = $stmt->execute();
            $rowCountResult = $stmt->rowCount();
            $stmt = null; 

            $stmt = $pdo->prepare("SELECT * FROM `accounts` ORDER BY `id` LIMIT $rOffset , $rLimit");
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $pdo1 = null;
                $pdo = null;

                $retArray = ["count" => $rowCountResult, "result" => $results];
                return $retArray;
            }
            else {
                $retArray = ["count" => 0, "result" => null];
                return $retArray;
            }
        }
        $pdo1 = null;
        $pdo = null;
    }

    public static function AddNewAccount($_dbInfo, $_email, $_password, $_companyid, $_formInformation) {
        $ccid = $_companyid + 1000;
        $pdo1 = self::connect($_dbInfo, 'servicemanager');
        $pdo = self::connect($_dbInfo, "company_" . $ccid);
        if (self::ValidateLogin($pdo1, $_email, $_password)) {
            $accountInformation = $_formInformation['accountInformation'];
            if (!is_array($accountInformation)) {
                return false;
            }

            if (!self::CheckPermissions($_dbInfo, $_email, $_password, ['ca'])) {
                die("You do not have permission to view this page. Speak to your account manager to gain access.");
            }

            if (strlen($accountInformation['name']) <= 0) {
                $accountInformation['name'] = $accountInformation['firstName'] . " " . $accountInformation['lastName'];
            }

            if (!self::ValidateField($accountInformation['type'], 'contractType') ||
            !self::ValidateField($accountInformation['firstName'], 'name') ||
            !self::ValidateField($accountInformation['lastName'], 'name') ||
            !self::ValidateField($accountInformation['street1'], 'name') ||
            !self::ValidateField($accountInformation['street2'], 'address_nonrequired') ||
            !self::ValidateField($accountInformation['city'], 'name') ||
            !self::ValidateField($accountInformation['state'], 'state') ||
            !self::ValidateField($accountInformation['zipCode'], 'zipCode') ||
            !self::ValidateField($accountInformation['primaryPhone'], 'phone') ||
            !self::ValidateField($accountInformation['secondaryPhone'], 'phone_nonrequired') ||
            !self::ValidateField($accountInformation['email'], 'email') ||
            !self::ValidateField($accountInformation['name'], 'name')
            )
            {
                die("Validation Error");
            }

            // Create Contact for Primary Contact
            $stmt = $pdo->prepare("INSERT INTO `contacts` (`firstname`,`lastname`,`email`,`primaryphone`,`secondaryphone`) VALUES (:firstname,:lastname,:email,:primaryphone,:secondaryphone)");
            $stmt->bindParam(":firstname", $accountInformation['firstName']);
            $stmt->bindParam(":lastname", $accountInformation['lastName']);
            $stmt->bindParam(":email", $accountInformation['email']);
            $stmt->bindParam(":primaryphone", $accountInformation['primaryPhone']);
            $stmt->bindParam(":secondaryphone", $accountInformation['secondaryPhone']);
            $stmt->execute();
            //Contact ID
            $cid = $pdo->lastInsertId();

            // Create Initial Account
            $stmt = $pdo->prepare("INSERT INTO `accounts` (`name`, `type`, `primarycontactid`, `street1`, `street2`, `city`, `state`, `zipcode`) VALUES (:name,:type,:primarycontactid,:street1,:street2,:city,:state,:zipcode)");
            $stmt->bindParam(":name", $accountInformation['name']);
            $stmt->bindParam(":type", $accountInformation['type']);
            $stmt->bindParam(":primarycontactid", $cid);
            $stmt->bindParam(":street1", $accountInformation['street1']);
            $stmt->bindParam(":street2", $accountInformation['street2']);
            $stmt->bindParam(":city", $accountInformation['city']);
            $stmt->bindParam(":state", $accountInformation['state']);
            $stmt->bindParam(":zipcode", $accountInformation['zipCode']);
            $stmt->execute();
            //Account ID
            $aid = $pdo->lastInsertId();

            // Create Primary Location for account
            $stmt = $pdo->prepare("INSERT INTO `locations` (`accountid`, `street1`,`street2`,`city`,`state`,`zipcode`,`contacts`,`notes`) VALUES (:accountid,:street1,:street2,:city,:state,:zipcode,:contacts,'')");
            $stmt->bindParam(":accountid", $aid);
            $stmt->bindParam(":street1", $accountInformation['street1']);
            $stmt->bindParam(":street2", $accountInformation['street2']);
            $stmt->bindParam(":city", $accountInformation['city']);
            $stmt->bindParam(":state", $accountInformation['state']);
            $stmt->bindParam(":zipcode", $accountInformation['zipCode']);
            $stmt->bindParam(":contacts", $cid);
            $stmt->execute();
            //Location ID
            $lid = $pdo->lastInsertId();

            $locations = $_formInformation['locations'];

            if (is_array($locations)) {
                if (count($locations) > 0) {
                    foreach ($locations as $location) {
                        $finalContactId = $cid;
                        if (!self::ValidateField($location['street1'], 'name') ||
                            !self::ValidateField($location['street2'], 'address_nonrequired') ||
                            !self::ValidateField($location['city'], 'name') ||
                            !self::ValidateField($location['state'], 'state') ||
                            !self::ValidateField($location['zipCode'], 'zipCode')
                            )
                            {
                                die("Validation Error");
                            }
                        
                        if ($location['copyContact'] === false)
                        {
                            if (!self::ValidateField($location['contact_firstName'], 'name') ||
                            !self::ValidateField($location['contact_lastName'], 'name') ||
                            !self::ValidateField($location['contact_email'], 'email') ||
                            !self::ValidateField($location['contact_primaryPhone'], 'phone') ||
                            !self::ValidateField($location['contact_secondaryPhone'], 'phone_nonrequired')
                            )
                            {
                                die("Validation Error");
                            }

                            $stmt = $pdo->prepare("INSERT INTO `contacts` (`firstname`,`lastname`,`email`,`primaryphone`,`secondaryphone`) VALUES (:firstname,:lastname,:email,:primaryphone,:secondaryphone)");
                            $stmt->bindParam(":firstname", $location['contact_firstName']);
                            $stmt->bindParam(":lastname", $location['contact_lastName']);
                            $stmt->bindParam(":email", $location['contact_email']);
                            $stmt->bindParam(":primaryphone", $location['contact_primaryPhone']);
                            $stmt->bindParam(":secondaryphone", $location['contact_secondaryPhone']);
                            $stmt->execute();
                            //Contact ID
                            $finalContactId = $pdo->lastInsertId();
                        }

                        // Create Location
                        $stmt = $pdo->prepare("INSERT INTO `locations` (`accountid`,`street1`,`street2`,`city`,`state`,`zipcode`,`contacts`,`notes`) VALUES (:accountid,:street1,:street2,:city,:state,:zipcode,:contacts,'')");
                        $stmt->bindParam(":accountid", $aid);
                        $stmt->bindParam(":street1", $location['street1']);
                        $stmt->bindParam(":street2", $location['street2']);
                        $stmt->bindParam(":city", $location['city']);
                        $stmt->bindParam(":state", $location['state']);
                        $stmt->bindParam(":zipcode", $location['zipCode']);
                        $stmt->bindParam(":contacts", $finalContactId);
                        $stmt->execute();
                        //Location ID
                        $lidx = $pdo->lastInsertId();
                    }
                }
            }

            return "done";
        }
    }

    public static function ValidateField($form_input, $validation_type) {
        $retVal = false;
      
        switch ($validation_type) {
          case 'email':
            $emailRegex = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
            if (!preg_match($emailRegex, $form_input)) {
              return $retVal;
            }
            break;
      
          case 'phone':
            $phoneRegex = '/^\d{10}$/';
            if (!preg_match($phoneRegex, $form_input)) {
              return $retVal;
            }
            break;
      
          case 'phone_nonrequired':
            $phoneRegex = '/^\d{10}$/';
            if (strlen($form_input) > 0 && !preg_match($phoneRegex, $form_input)) {
              return $retVal;
            }
            break;
      
          case 'zipCode':
            $zipcodeRegex = '/^\d{5}$/';
            if (!preg_match($zipcodeRegex, $form_input)) {
              return $retVal;
            }
            break;
      
          case 'address':
            // Customize the regular expression for street address validation
            $streetAddressRegex = '/^[a-zA-Z0-9\s.,\'-]+$/';
            if (!preg_match($streetAddressRegex, $form_input)) {
              return $retVal;
            }
            break;
      
          case 'address_nonrequired':
            // Customize the regular expression for street address validation
            $streetAddressRegex = '/^[a-zA-Z0-9\s.,\'-]+$/';
            if (!empty($form_input)) {
              if (!preg_match($streetAddressRegex, $form_input)) {
                return $retVal;
              } elseif (strlen($form_input) <= 3) {
                return $retVal;
              }
            }
            break;
      
          case 'name':
            if (strlen($form_input) <= 2) {
              return $retVal;
            }
            break;
      
          case 'name_nonrequired':
            if (strlen($form_input) > 0 && strlen($form_input) <= 2) {
              return $retVal;
            }
            break;
      
          case 'contractType':
            if ($form_input === null) {
              return $retVal;
            }
            break;
      
          case 'state':
            $stateRegex = '/^[a-zA-Z]+$/';
            if (!preg_match($stateRegex, $form_input) || strlen($form_input) != 2) {
              return $retVal;
            }
            break;
      
          default:
            $retVal = true;
            return $retVal;
        }
      
        // Validation passed
        $retVal = true;
        return $retVal;
      }
}

?>