<?php

trait ActionGenerateCalendar {
    public static function GenerateCalendar($_dbInfo, $_postData) {
		OpLog::Log("Action: GenerateCalendar");
        OpLog::Log(print_r($_postData, true) . "\n");
		$calendar = Calendar::Generate($_dbInfo, $_postData);
		return $calendar;
	}
}

?>