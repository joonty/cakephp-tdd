<?php
/**
 * The ControllerTask handles creating and updating controller files.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 1.2
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppShell', 'Console/Command');
App::uses('BakeTask', 'Console/Command/Task');
App::uses('AppModel', 'Model');
App::uses('ControllerTask', 'Console/Command/Task');

/**
 * Task class for creating and updating controller files.
 *
 * @package       Cake.Console.Command.Task
 */
class TddControllerTask extends ControllerTask {

/**
 * Tasks to be loaded by this Task
 *
 * @var array
 */
	public $tasks = array('Model', 'Tdd.TddControllerTest', 'Template', 'DbConfig', 'Project');
	public $package = "none";


/**
 * Assembles and writes a Controller file
 *
 * @param string $controllerName Controller name already pluralized and correctly cased.
 * @param string $actions Actions to add, or set the whole controller to use $scaffold (set $actions to 'scaffold')
 * @param array $helpers Helpers to use in controller
 * @param array $components Components to use in controller
 * @return string Baked controller
 */
	public function bake($controllerName, $actions = '', $helpers = null, $components = null) {
		$this->Template->set('package',$this->package);
		parent::bake($controllerName,$actions,$helpers,$components);
		$this->bakeTest($controllerName,$actions,$helpers,$components);
	}

/**
 * Assembles and writes a unit test file
 *
 * @param string $className Controller class name
 * @return string Baked test
 */
	public function bakeTest($className,$actions='',$helpers=null,$components=null) {
		$this->TddControllerTest->plugin = $this->plugin;
		$this->TddControllerTest->connection = $this->connection;
		$this->TddControllerTest->interactive = $this->interactive;
		$this->TddControllerTest->package = $this->package;
		return $this->TddControllerTest->bake('Controller', $className,$actions,$helpers,$components);
	}

	protected function _checkUnitTest() {
		return false;
	}
}
