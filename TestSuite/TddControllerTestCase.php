<?php

/**
 * Contains the TddTestCase class.
 *
 * @package Local TV
 * @author Jon Cairns <jon.cairns@ggapps.co.uk>
 * @copyright Copyright (c) Green Gorilla Apps 2012
 */

App::uses('ControllerTestCase','TestSuite');
App::uses('CakeSession','Model/Datasource');
App::uses('SessionMockComponent','Tdd.Controller/Component');
/**
 * TddTestCase description
 *
 */
class TddControllerTestCase extends ControllerTestCase {

	public function setUp() {
		parent::setUp();

		$this->loader = new MockLoader();
		
		// Just use arrays to hold cached data
		Cache::drop('default');
		Cache::config('default', array('engine' => 'Tdd.ArrayCache'));
	}

	/**
	 * Get a validator for a model name.
	 *
	 * @param string $modelName e.g. 'User'
	 * @return ValidationAnalyser
	 */
	public function validation($modelName) {
		return TddTestHelper::validator($modelName);
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
	 * To get a single record, use {@link TddControllerTestCase::fixtureRecord() fixtureRecord()}.
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

	public function generate($controller, $mocks = array()) {
		list($plugin, $controller) = pluginSplit($controller);
		if ($plugin) {
			App::uses($plugin . 'AppController', $plugin . '.Controller');
			$plugin .= '.';
		}
		App::uses($controller . 'Controller', $plugin . 'Controller');
		if (!class_exists($controller.'Controller')) {
			throw new MissingControllerException(array(
				'class' => $controller . 'Controller',
				'plugin' => substr($plugin, 0, -1)
			));
		}
		ClassRegistry::flush();

		$mocks = array_merge_recursive(array(
			'methods' => array('_stop'),
			'models' => array(),
			'components' => array()
		), (array)$mocks);

		list($plugin, $name) = pluginSplit($controller);
		$_controller = $this->getMock($name.'Controller', $mocks['methods'], array(), '', false);
		$_controller->name = $name;
		$request = $this->getMock('CakeRequest');
		$response = $this->getMock('CakeResponse', array('_sendHeader'));
		$_controller->__construct($request, $response);

		$config = ClassRegistry::config('Model');
		$modelNames = array();
		foreach ($mocks['models'] as $model => $methods) {
			if (is_string($methods)) {
				$model = $methods;
				$methods = true;
			}
			if ($methods === true) {
				$methods = array();
			}
			ClassRegistry::init($model);
			list($plugin, $name) = pluginSplit($model);
			$config = array_merge((array)$config, array('name' => $model));
			$_model = $this->getMock($name, $methods, array($config));
			ClassRegistry::removeObject($name);
			ClassRegistry::addObject($name, $_model);
			$modelNames[] = $name;
		}

		foreach ($mocks['components'] as $component => $contents) {
			if (is_string($contents)) {
				$component = $contents;
				$methods = true;
			}
			if ($component == 'Session') {
				continue;
			}
			if ($contents === true) {
				$methods = array();
			}
			list($plugin, $name) = pluginSplit($component, true);
			$componentClass = $name . 'Component';
			App::uses($componentClass, $plugin . 'Controller/Component');
			if (!class_exists($componentClass)) {
				throw new MissingComponentException(array(
					'class' => $componentClass
				));
			}
			if (is_array($contents) && array_key_exists('methods', $contents)) {
				$methods = $contents['methods'];
			} else {
				$methods = array();
			}
			if (is_array($contents) && array_key_exists('class',$contents)) {

				$_controller->Components->set($name, new $contents['class']($_controller->Components));
			} else {
				$_component = $this->getMock($componentClass, $methods, array(), '', false);
				$_controller->Components->set($name, $_component);
			}
		}
		$_controller->Components->set('Session',new SessionMockComponent(new ComponentCollection));

		$_controller->constructClasses();
		$this->__dirtyController = false;
		foreach ($modelNames as $n) {
			$_controller->{$n};
		}

		$this->controller = $_controller;
		return $this->controller;
	}

	/**
	 * Retrieve raw data from a data file.
	 *
	 * The data file should be located at Test/Data/<file>. This method returns the exact contents of the file
	 * as a string.
	 * 
	 * @param string $file 
	 * @access public
	 * @return string
	 */
	public function getRawData($file) {
		return TddTestHelper::getRawData($file);
	}

	/**
	 * Retrieve PHP data from a data file.
	 *
	 * The data file should be located at Test/Data/<file>. This method includes the file, expecting it to be PHP code.
	 * The returned data is whatever the file returns.
	 *
	 * @param string $file 
	 * @access public
	 * @return string
	 */
	public function getEvalData($file) {
		return TddTestHelper::getEvalData($file);
	}

}

?>
