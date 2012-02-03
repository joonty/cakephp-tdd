<?php
App::uses('ModelTask','Console/Command/Task');

/**
 * Task class for creating and updating model files.
 *
 * @package       Cake.Console.Command.Task
 */
class TModelTask extends ModelTask {
/**
 * tasks
 *
 * @var array
 */
	public $tasks = array('DbConfig', 'Fixture', 'Test', 'Template');

        public function bakeTest($className) {
		echo "bakeTest()\n";
        }

	public function bake($name,$data = array()) {
		parent::bake($name,$data);
		$this->bakeUnitTest($data);
	}

	private function bakeUnitTest(array $data) {
		var_dump($data);
	}

	protected function _checkUnitTest() {
		return false;
	}
}
