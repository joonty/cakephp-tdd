<?php

/**
 * Contains the TddTestSuiteCommand class.
 *
 * @package Local TV
 * @author Jon Cairns <jon.cairns@ggapps.co.uk>
 * @copyright Copyright (c) Green Gorilla Apps 2012
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