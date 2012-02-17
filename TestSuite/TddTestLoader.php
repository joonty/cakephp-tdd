<?php

/**
 * Contains the TddTestLoader class.
 *
 * @package Local TV
 * @author Jon Cairns <jon.cairns@22blue.co.uk>
 * @copyright Copyright (c) 22 Blue 2012
 */
App::uses('CakeTestLoader', 'TestSuite');

/**
 * TddTestLoader description
 *
 */
class TddTestLoader extends CakeTestLoader {

	/**
	 * Find test files to include by glob pattern.
	 *
	 * @param string $filePath Glob pattern
	 * @param mixed $params
	 * @return array|false Test files found by pattern $filePath, or false on failure
	 */
	public function glob($filePath, $params = '') {
		$basePath = $this->_basePath($params) . DS . $filePath;
		$files = $this->globRecursive($basePath);
		return $files;
	}

	/**
	 * Find files recursively using a glob pattern.
	 *
	 * @param string $pattern Glob pattern
	 * @param int $flags Glob options
	 * @return array
	 */
	private function globRecursive($pattern, $flags = 0) {
		$files_r = glob($pattern, $flags);
		$files = array();
		foreach ($files_r as $f) {
			if (!is_dir($f)) {
				$files[] = $f;
			}
		}

		foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
			$files = array_merge($files, $this->globRecursive($dir . '/' . basename($pattern), $flags));
		}

		return $files;
	}

}

?>
