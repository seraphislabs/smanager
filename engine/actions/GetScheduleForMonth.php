<?php

trait ActionGetScheduleForMonth {
    public static function GetScheduleForMonth($_dbInfo, $_month, $_year, $_employeeid) {
		if (!DatabaseManager::CheckPermissions($_dbInfo, ['ves'])) {
			if ($_SESSION['eid'] != $_employeeid) {
							return "";
			}
  		}
		$monthSchedule = Calendar::GetMonthSchedule($_dbInfo, $_month, $_year, $_employeeid);
		return $monthSchedule;
	}
}

?>