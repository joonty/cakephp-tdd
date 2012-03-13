<?php
/**
 * Test suite that runs all tests cases.
 *
 * @package Bromford
 * @subpackage Tests
 */
class AllTest extends CakeTestSuite {
	public static function suite() {
		$suite = new CakeTestSuite('All tests');
		$suite->addTestDirectoryRecursive(__DIR__);
		return $suite;
	}
}
?>