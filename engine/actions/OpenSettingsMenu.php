<?php

trait ActionOpenSettingsMenu {
    public static function OpenSettingsMenu($_dbInfo) {
        return MenuSettings::Generate($_dbInfo);
    }
}

?>