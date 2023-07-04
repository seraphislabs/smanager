<?php

trait ActionPunchIn {
    public static function PunchIn($_dbInfo, $_postData) {
        OpLog::Log("Action: PunchIn");
        DatabaseManager::AddPunch($_dbInfo);
    }
}

?>