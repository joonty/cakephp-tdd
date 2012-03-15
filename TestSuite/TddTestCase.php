<?php

/**
 * Contains the TddTestCase class.
 *
 * @package Local TV
 * @author Jon Cairns <jon.cairns@22blue.co.uk>
 * @copyright Copyright (c) 22 Blue 2012
 */

App::uses('CakeTestCase','TestSuite');
App::uses('CakeSession','Model/Datasource');
App::uses('MockLoader', 'Tdd.Lib');
/**
 * TddTestCase description
 *
 */
class TddTestCase extends CakeTestCase {

	/**
	 * @var MockLoader
	 */
	protected $loader;

	public function setUp() {
		parent::setUp();

		$this->loader = new MockLoader();

		// Just use arrays to hold cached data
		Cache::drop('default');
		Cache::config('default', array('engine' => 'Tdd.ArrayCache'));
	}

	/**
	 * Force a cache configuration to use the ArrayCacheEngine for temporary caching.
	 *
	 * This should be done where a custom cache is used, so that tests are isolated
	 * from eachother and don't re-use cache.
	 *
	 * @param string $config Cache configuration name, e.g. 'default'
	 */
	public function mockCache($config) {
		Cache::drop($config);
		Cache::config($config,array('engine'=>'Tdd.ArrayCache'));
	}

	/**
	 * Get all records from a fixture.
	 *
	 * To get a single record, use {@link TddTestCase::fixtureRecord() fixtureRecord()}.
	 *
	 * @param string $name Fixture name, e.g. user ("app." prefix not required)
	 * @return array All fixture records
	 */
	public function fixtureData($name) {
		return TddTestHelper::getAllFixtureRecords($name);
	}


	/**
	 * Get a single record from a fixture.
	 *
	 * Throws an exception if the fixture or record doesn't exist.
	 *
	 * @param string $name Fixture name, e.g. user ("app." prefix not required)
	 * @param int $index OPTIONAL Array offset for the desired record, defaults to the first
	 * @return array Fixture record
	 */
	public function fixtureRecord($name,$index = 0) {
		return TddTestHelper::getFixtureRecord($name, $index);
	}

	/**
	 * Create a new (hopefully) unique record, using and modifiying existing fixture records.
	 *
	 * The "id" field is removed, so that it can be used for adding. Please note
	 * that this only works if the table primary key is actually called "id".
	 *
	 * @param string $name Fixture name, e.g. user ("app." prefix not required)
	 */
	public function newFixtureRecord($name) {
		return TddTestHelper::getNewFixtureRecord($name);
	}
}

?>
