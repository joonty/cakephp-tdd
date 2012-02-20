<?php
App::uses('ModelTask','Console/Command/Task');

/**
 * Task class for creating and updating model files.
 *
 * @package       Cake.Console.Command.Task
 */
class TddModelTask extends ModelTask {
/**
 * tasks
 *
 * @var array
 */
	public $tasks = array('DbConfig', 'Fixture', 'Tdd.TddTest','Tdd.TddTestTask', 'Template');

	public function bakeTest($className) {
		$this->TddTest->interactive = $this->interactive;
		$this->TddTest->plugin = $this->plugin;
		$this->TddTest->connection = $this->connection;
		return $this->TddTest->bake('Model', $className);
	}

	public function bakeFixture($className, $useTable = null,$data = array()) {
		$this->Fixture->params['count'] = 10;
		$this->Fixture->interactive = $this->interactive;
		$this->Fixture->connection = $this->connection;
		$this->Fixture->plugin = $this->plugin;
		$this->Fixture->bake($className, $useTable,array('schema'=>$className,));
	}

	public function bake($name,$data = array()) {
		parent::bake($name,$data);
		$this->bakeTest($name);
		$this->bakeFixture($name,null,$data);
	}

	protected function _checkUnitTest() {
		return false;
	}
}
