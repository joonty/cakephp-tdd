<?php

/**
 * Contains the TddTestRunner class.
 *
 * @package Local TV
 * @author Jon Cairns <jon.cairns@22blue.co.uk>
 * @copyright Copyright (c) 22 Blue 2012
 */

/**
 * TddTestRunner description
 *
 */
class TddTestRunner extends CakeTestRunner {

	public function getTest($suiteClassName, $suiteClassFile = '') {

		//Attempt a glob to find multiple files
		$loader = $this->getLoader();
		$files = $loader->glob($suiteClassName,$suiteClassFile);
		if ($files && count($files)) {
			echo "Including test files:".PHP_EOL;
			foreach ($files as $f) {
				echo " - $f".PHP_EOL;
			}
			echo PHP_EOL;
			$suite = new PHPUnit_Framework_TestSuite($suiteClassName);
			$suite->addTestFiles($files);
			return $suite;
		}
		return parent::getTest($suiteClassName,$suiteClassFile);
	}
}

?>
