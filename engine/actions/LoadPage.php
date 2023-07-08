<?php

trait ActionLoadPage {
	public static function LoadPage($_postData) {
		OpLog::Log("Action: LoadPage");
        OpLog::Log(print_r($_postData, true) . "\n");

		if (isset($_postData['pageid'])) {
			$pageid = $_postData['pageid'];

			$returnedCode = "";
			$className = "Page" . $pageid;
			if (class_exists($className)) {
				$class = new ReflectionClass($className);
				if ($class->hasMethod('Generate')) {
					$method = $class->getMethod('Generate');
					$returnedCode .= $method->invoke(null, $_postData);
				}
			}
			return $returnedCode;
		}

		return null;
	}
}

?>