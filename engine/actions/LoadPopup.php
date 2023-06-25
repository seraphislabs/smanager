<?php

trait ActionLoadPopup {
    public static function LoadPopup($_dbInfo, $_popupid, $_data) {
        $returnedCode = "";
        $className = "Popup" . $_popupid;
        if (class_exists($className)) {
            $class = new ReflectionClass($className);
            if ($class->hasMethod('Generate')) {
                $method = $class->getMethod('Generate');
                $returnedCode .= $method->invoke(null, $_dbInfo, $_data);
            }
        }
        return $returnedCode;
    }
}

?>