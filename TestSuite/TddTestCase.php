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
/**
 * TddTestCase description
 *
 */
class TddTestCase extends CakeTestCase {

	public function setUp() {
		parent::setUp();

		// Just use arrays to hold session data
		Configure::write('Session.handler.engine', 'Tdd.ArraySession');
		CakeSession::clear();

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
}

?>
