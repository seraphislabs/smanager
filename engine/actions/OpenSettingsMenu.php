<?php

trait ActionOpenSettingsMenu {
    public static function OpenSettingsMenu($_postData) {
        OpLog::Log("Action: OpenSettingsMenu");
        return MenuSettings::Generate();
    }
}

?>