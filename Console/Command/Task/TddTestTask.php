<?php
App::uses('ValidationAnalyser','Tdd.Lib');
App::uses('TestTask','Console/Command/Task');
class TddTestTask extends TestTask {
	public $package = "none";

	public function bake($type, $className) {
		$plugin = null;
		if ($this->plugin) {
			$plugin = $this->plugin . '.';
		}

		$realType = $this->mapType($type, $plugin);
		$fullClassName = $this->getRealClassName($type, $className);

		if ($this->typeCanDetectFixtures($type) && $this->isLoadableClass($realType, $fullClassName)) {
			$this->out(__d('cake_console', 'Bake is detecting possible fixtures...'));
			$testSubject = $this->buildTestSubject($type, $className);
			$this->generateFixtureList($testSubject);
			try {
				$validation = new ValidationAnalyser($testSubject);
			} catch (Exception $e) {
				debug($e);
				$validation = null;
			}
		} elseif ($this->interactive) {
			$this->getUserFixtures();
			$validation = null;
		}
		App::uses($fullClassName, $realType);

		$methods = array();
		if (class_exists($fullClassName)) {
			$methods = $this->getTestableMethods($fullClassName);
		}
		$mock = $this->hasMockClass($type, $fullClassName);
		$construction = $this->generateConstructor($type, $fullClassName);

		$this->out("\n" . __d('cake_console', 'Baking test case for %s %s ...', $className, $type), 1, Shell::QUIET);

		$this->Template->set('validation',$validation);
		$this->Template->set('package',$this->package);
		$this->Template->set('fixtures', $this->_fixtures);
		$this->Template->set('plugin', $plugin);
		$this->Template->set(compact(
			'className', 'methods', 'type', 'fullClassName', 'mock',
			'construction', 'realType'
		));
		$templateName = (strtolower($type) == 'model')?'model_test':'test';
		$out = $this->Template->generate('classes', $templateName);

		$filename = $this->testCaseFileName($type, $className);
		$made = $this->createFile($filename, $out);
		$warnings = $validation->getWarningsAsString();
		if ($warnings) {
			echo PHP_EOL.$warnings;
		}
		if ($made) {
			return $out;
		}
		return false;
	}

}
?>
