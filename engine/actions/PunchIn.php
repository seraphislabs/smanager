<?php

trait ActionPunchIn {
    public static function PunchIn($_dbInfo, $_postData) {
        DatabaseManager::AddPunch($_dbInfo);
    }
}

?>