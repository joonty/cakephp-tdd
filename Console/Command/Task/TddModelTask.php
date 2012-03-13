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
	public $tasks = array('DbConfig', 'Tdd.TddFixture', 'Tdd.TddTest','Tdd.TddTestTask', 'Template');
	public $package = "none";

	public function bakeTest($className) {
		$this->TddTest->interactive = $this->interactive;
		$this->TddTest->plugin = $this->plugin;
		$this->TddTest->connection = $this->connection;
		$this->TddTest->package = $this->package;
		return $this->TddTest->bake('Model', $className);
	}

	public function bakeFixture($className, $useTable = null,$data = array()) {
		$this->TddFixture->params['count'] = 10;
		$this->TddFixture->interactive = $this->interactive;
		$this->TddFixture->connection = $this->connection;
		$this->TddFixture->plugin = $this->plugin;
		$this->TddFixture->package = $this->package;
		$this->TddFixture->bake($className, $useTable,array('schema'=>$className,));
	}

	public function bake($name,$data = array()) {
		$this->Template->set('package',$this->package);
		parent::bake($name,$data);
		strtoupper($this->in(__d('cake_console',
		'Take a moment to check the validation rules on your model. These are used to automatically add test data, to help your tests pass first time! Press "y" to continue.'),
		array('y')));
		$this->bakeTest($name);
		$this->bakeFixture($name,null,$data);
	}

	protected function _checkUnitTest() {
		return false;
	}
}
