<?php

trait ActionOpenSettingsMenu {
    public static function OpenSettingsMenu($_dbInfo, $_postData) {
        OpLog::Log("Action: OpenSettingsMenu");
        return MenuSettings::Generate($_dbInfo);
    }
}

?>