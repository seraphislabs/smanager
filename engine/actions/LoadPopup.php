<?php

trait ActionLoadPopup {
    public static function LoadPopup($_dbInfo, $_postData) {
        if (isset($_postData['buttonid'])) {
			$buttonid = $_postData['buttonid'];
			$data = [];
			if (isset($_POST['data'])) {
				$data = json_decode($_POST['data'], true);
			}

            $returnedCode = "";
            $className = "Popup" . $buttonid;
            if (class_exists($className)) {
                $class = new ReflectionClass($className);
                if ($class->hasMethod('Generate')) {
                    $method = $class->getMethod('Generate');
                    $returnedCode .= $method->invoke(null, $_dbInfo, $data);
                }
            }
            return $returnedCode;
        }

        return null;
    }
}

?>