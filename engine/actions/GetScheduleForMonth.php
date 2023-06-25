<?php

trait ActionGetScheduleForMonth {
    public static function GetScheduleForMonth($_dbInfo, $_postData) {
		$month = $_postData['month'];
		$year = $_postData['year'];
		$employeeid = $_postData['eid'];

		if (!DatabaseManager::CheckPermissions($_dbInfo, ['ves'])) {
			if ($_SESSION['eid'] != $employeeid) {
							return "";
			}
  		}
		$monthSchedule = Calendar::GetMonthSchedule($_dbInfo, $month, $year, $employeeid);
		return $monthSchedule;
	}
}

?>