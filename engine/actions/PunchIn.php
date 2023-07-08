<?php

trait ActionPunchIn {
    public static function PunchIn($_postData) {
        OpLog::Log("Action: PunchIn");
        DatabaseManager::AddPunch();
    }
}

?>