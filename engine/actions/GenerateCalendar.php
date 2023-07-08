<?php

trait ActionGenerateCalendar {
    public static function GenerateCalendar($_postData) {
		OpLog::Log("Action: GenerateCalendar");
        OpLog::Log(print_r($_postData, true) . "\n");
		
		$month = $_postData['month'];
		$year = $_postData['year'];
		$employeeid = $_postData['employeeid'];
		$viewType = $_postData['viewType'];
		$listType = $_postData['listType'];

		$calendar = Calendar::Generate($month, $year, $employeeid, $viewType, $listType);
		return $calendar;
	}
}

?>