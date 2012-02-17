<?php

/**
 * Contains the TddTestSuiteCommand class.
 *
 * @package Local TV
 * @author Jon Cairns <jon.cairns@22blue.co.uk>
 * @copyright Copyright (c) 22 Blue 2012
 */
App::uses('CakeTestSuiteCommand','TestSuite');
/**
 * TddTestSuiteCommand description
 *
 */
class TddTestSuiteCommand extends CakeTestSuiteCommand {
	public function getRunner($loader) {
 		return new TddTestRunner($loader, $this->_params);
	}
}

?>
