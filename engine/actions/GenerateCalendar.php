<?php

trait ActionGenerateCalendar {
    public static function GenerateCalendar($_dbInfo, $_postData) {
		$calendar = Calendar::Generate($_dbInfo, $_postData);
		return $calendar;
	}
}

?>