<?php

trait ActionLoadPopup {
    public static function LoadPopup($_dbInfo, $_postData) {
        OpLog::Log("Action: LoadPopup");
        OpLog::Log(print_r($_postData, true) . "\n");

        if (isset($_postData['pageid'])) {
			$pageid = $_postData['pageid'];
			$data = [];
			if (isset($_POST['data'])) {
				$data = json_decode($_POST['data'], true);
			}

            $returnedCode = "";
            $className = "Popup" . $pageid;
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