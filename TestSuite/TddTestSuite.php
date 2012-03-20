<?php
/**
 * Description
 *
 * @author jon
 * @copyright 22 Blue (c) 2012
 */
/**
 * This test suite allows for the creation of fixture tables before all tests run.
 *
 * @package Tdd
 */
class TddTestSuite extends CakeTestSuite {

/*
    public function run(PHPUnit_Framework_TestResult $result = NULL, $filter = FALSE, array $groups = array(), array $excludeGroups = array(), $processIsolation = FALSE) {
	    $fixturePath = TESTS . 'Fixture';
	    $allFixtures = scandir($fixturePath);
	    $manager = new TddFixtureManager();
	    foreach ($allFixtures as $fixtureFile) {
		    if (stristr($fixtureFile,'.php')) {
			    $fixtureName = str_replace('.php','',$fixtureFile);
			    include_once $fixturePath.DS.$fixtureFile;
			    $manager->setupFixture($fixtureName);
		    }
	    }
	    parent::run($result,$filter,$groups,$excludeGroups,$processIsolation);
    }
*/
	/**
	 * Recursively adds all the files in a directory to the test suite.
	 *
	 * @param string $directory The directory subtree to add tests from.
	 * @return void
	 */
	public function addTestDirectoryRecursive($directory = '.') {
		$Folder = new Folder($directory);
		$files = $Folder->tree(null, false, 'files');
		sort($files,SORT_STRING);
		foreach ($files as $file) {
			if (strpos($file, DS . '.') !== false) {
				continue;
			}
			$this->addTestFile($file);
		}
	}
}

?>