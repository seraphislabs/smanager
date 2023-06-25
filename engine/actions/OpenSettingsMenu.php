<?php

trait ActionOpenSettingsMenu {
    public static function OpenSettingsMenu($_dbInfo, $_postData) {
        return MenuSettings::Generate($_dbInfo);
    }
}

?>