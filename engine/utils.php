<?php

class OpLog {
    private static $lastLogTime = 0; // Global variable to store the last log time

    public static function Log($_entry) {
        $timestamp = date('H:i:s');
        $logfile = "/nginx/logs/www/oplog.log";
        $phpLogFile = "/nginx/logs/www/php.log";
        $currentTime = time();

        $timestampTemplate = "[$timestamp]] ";

        // Check if the last log entry was more than 10 second ago
        if ($currentTime - self::$lastLogTime > 10) {
            $_entry = "-----------------------\n" . $_entry; // Append the separator before the log entry
            $timestampTemplate = "";
        }

        $_entry = $timestampTemplate . $_entry; // Append the timestamp to the log entry

        // Append the log message to the log file
        if ($_entry == "Action: StartPortal") {
            file_put_contents($logfile, "\n*********************\n" . $_entry . PHP_EOL);
            file_put_contents($phpLogFile, "\n*********************\n" . $_entry . PHP_EOL);
        } else {
            file_put_contents($logfile, $_entry . PHP_EOL, FILE_APPEND);
            file_put_contents($phpLogFile, $_entry . PHP_EOL, FILE_APPEND);
        }
        // Update the last log time
        self::$lastLogTime = $currentTime;
    }
}

class PasswordEncrypt {
    public static function Encrypt($_password) {
        return sodium_crypto_pwhash_str($_password,SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE);
    }
    public static function Check($entered_password, $stored_password) {
        return sodium_crypto_pwhash_str_verify($stored_password, $entered_password);
    }
}

class TimeManagement {
    public static function getTimeDifference($time1, $time2) {
        // Create DateTime objects from the time strings
        $dateTime1 = DateTime::createFromFormat('H:i:s', $time1);
        $dateTime2 = DateTime::createFromFormat('H:i:s', $time2);
    
        // Get the difference between the two DateTime objects
        $interval = $dateTime1->diff($dateTime2);
    
        // Format the difference as a time string
        $timeDifference = $interval->format('%H:%I:%S');
    
        return $timeDifference;
    }

    public static function getDatesBetween($startDate, $endDate) {
        $dates = array();
        $currentDate = new DateTime($startDate);
        $endDate = new DateTime($endDate);
    
        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->modify('+1 day');
        }
    
        return $dates;
    }

    public static function addTimes($time1, $time2) {
        // Convert time strings to DateTime objects
        $dateTime1 = DateTime::createFromFormat('H:i:s', $time1);
        $dateTime2 = DateTime::createFromFormat('H:i:s', $time2);
    
        // Get hours, minutes and seconds
        list($hours2, $minutes2, $seconds2) = explode(':', $dateTime2->format('H:i:s'));
    
        // Add time
        $dateTime1->add(new DateInterval("PT{$hours2}H{$minutes2}M{$seconds2}S"));
    
        // Return time string
        return $dateTime1->format('H:i:s');
    }
}

class UUID {
    public static function Create()
    {
        $data = random_bytes(16);
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
            
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

class HolidayChecker {
    public function IsHoliday($date) {
        $year = $date->format('Y');
        $easterDate = date('m/d/Y', easter_date($year));
        $easterDateTime = new DateTime($easterDate);
        $easterDateTime->modify('+1 day'); // Easter Monday
        $easterMondayDate = $easterDateTime->format('m/d/Y');
        $holidays = [
            '01/01/' . $year, // New Year's Day
            date('m/d/Y', strtotime('third monday of January ' . $year)), // Martin Luther King Jr. Day
            date('m/d/Y', strtotime('third monday of February ' . $year)), // Presidents' Day
            $easterDate, // Easter Sunday
            $easterMondayDate, // Easter Monday
            '05/25/' . $year, // Memorial Day
            '07/04/' . $year, // Independence Day
            date('m/d/Y', strtotime('first monday of September ' . $year)), // Labor Day
            date('m/d/Y', strtotime('second monday of October ' . $year)), // Columbus Day
            '11/11/' . $year, // Veterans Day
            date('m/d/Y', strtotime('fourth thursday of November ' . $year)), // Thanksgiving Day
            '12/25/' . $year, // Christmas Day
            '06/19/' . $year, // Juneteenth
        ];
        return in_array($date->format('m/d/Y'), $holidays);
    }
}
?>