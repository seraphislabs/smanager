<?php

trait ActionLoadPage {
	public static function LoadPage($_dbInfo, $_postData) {
		if (isset($_postData['buttonid'])) {
			$buttonid = $_postData['buttonid'];

			$returnedCode = "";
			$className = "Page" . $buttonid;
			if (class_exists($className)) {
				$class = new ReflectionClass($className);
				if ($class->hasMethod('Generate')) {
					$method = $class->getMethod('Generate');
					$returnedCode .= $method->invoke(null, $_dbInfo, $_postData);
				}
			}
			return $returnedCode;
		}

		return null;
	}
}

?>