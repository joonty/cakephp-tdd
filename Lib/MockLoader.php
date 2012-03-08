<?php

/**
 * Contains the MockLoader class.
 *
 * @package Tdd
 * @author Jon Cairns <jon.cairns@22blue.co.uk>
 * @copyright Copyright (c) 22 Blue 2012
 */

/**
 * MockLoader is a class for loading Mock classes for testing.
 *
 * @package Tdd
 */
class MockLoader {

	protected $paths = array();

	/**
	 * @param array $additionalPaths Any paths to add to the list
	 */
	public function __construct($additionalPaths = array()) {
		if (count($additionalPaths)) {
			foreach ($additionalPaths as $path) {
				if (self::testPath($path)) {
					$this->paths[] = $path;
				} else {
					trigger_error("Mock class path '$path' does not exists", E_USER_NOTICE);
				}
			}
		}
		//Add the default path for mock classes
		$this->addPath(APP."Test".DIRECTORY_SEPARATOR."Mock".DIRECTORY_SEPARATOR);
		spl_autoload_register(array($this,'load'));
	}

	/**
	 * Add a path to the internal list of paths.
	 * @param string $path
	 */
	public function addPath($path) {
		if ($path[strlen($path)-1] != DIRECTORY_SEPARATOR) {
			$path.=DIRECTORY_SEPARATOR;
		}
		$this->paths[] = $path;
	}

	/**
	 * Try and load the class from the internal list of paths.
	 *
	 * @param string $class_name Name of the class to load
	 */
	public function load($class_name) {
		foreach ($this->paths as $path) {
			$filePath = $path.$class_name.'.php';
			if (file_exists($path.$class_name.'.php')) {
				include $filePath;
				if (class_exists($class_name)) {
					break;
				}
			}
		}
	}

	/**
	 * Check that the path is valid.
	 *
	 * @param string $path
	 * @return boolean
	 */
	protected static function testPath($path) {
		return is_dir($path);
	}

}

?>
