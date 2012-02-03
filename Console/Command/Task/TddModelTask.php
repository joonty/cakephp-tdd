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
	public $tasks = array('DbConfig', 'Fixture', 'TddModelTest', 'Template');

        public function bakeTest($className) {
		echo "bakeTest()\n";
        }

        public function bakeFixture($className) {
		echo "bakeFixture()\n";
        }

	public function bake($name,$data = array()) {
		parent::bake($name,$data);
		$this->bakeTestAndFixture($name,$data);
	}

	private function bakeTestAndFixture($name,array $data) {
		var_dump($data);
	}

	protected function _checkUnitTest() {
		return false;
	}
}
