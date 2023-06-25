<?php

class PasswordEncrypt {
    public static function Encrypt($_password) {
        return sodium_crypto_pwhash_str($_password,SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE);
    }
    public static function Check($entered_password, $stored_password) {
        return sodium_crypto_pwhash_str_verify($stored_password, $entered_password);
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