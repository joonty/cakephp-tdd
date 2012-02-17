<?php
App::uses('TestTask','Console/Command/Task');
class TddControllerTestTask extends TestTask {

	public function bake($type, $className,$actions,$helpers,$components) {
		$plugin = null;
		if ($this->plugin) {
			$plugin = $this->plugin . '.';
		}
		$bakeActions = false;
		if (strlen($actions)) {
			$bakeActions = true;
		}

		$realType = $this->mapType($type, $plugin);
		$fullClassName = $className.'Controller';

		if ($this->typeCanDetectFixtures($type) && $this->isLoadableClass($realType, $fullClassName)) {
			$this->out(__d('cake_console', 'Bake is detecting possible fixtures...'));
			$testSubject = $this->buildTestSubject($type, $fullClassName);
			$this->generateFixtureList($testSubject);
		} elseif ($this->interactive) {
			$this->getUserFixtures();
		}
		App::uses($fullClassName,$realType);

		$methods = array();
		if (class_exists($fullClassName,true)) {
			$methods = $this->getTestableMethods($fullClassName);
		}
		$mock = $this->hasMockClass($type, $fullClassName);
		$construction = $this->generateConstructor($type, $fullClassName,$className,$components);

		$this->out("\n" . __d('cake_console', 'Baking test case for %s %s ...', $className, $type), 1, Shell::QUIET);

		$this->Template->set('fixtures', $this->_fixtures);
		$this->Template->set('plugin', $plugin);
		$this->Template->set(compact(
			'className', 'methods', 'type', 'fullClassName', 'mock',
			'construction', 'realType'
		));
		$out = $this->Template->generate('classes', 'controller_test');
		$outView = $this->Template->generate('classes', 'controller_view_test');
		$outVars = $this->Template->generate('classes', 'controller_vars_test');

		$filename = $this->testCaseFileName($type, $className);
		$made = $this->createFile($filename, $out);
		if ($made) {
			$this->createFile($this->testCaseFileName($type,$className,'Vars'),$outVars);
			$this->createFile($this->testCaseFileName($type,$className,'View'),$outView);
			return $out;
		}
		return false;
	}

	public function generateConstructor($type,$fullClassName,$className,$components) {
		return '$this->generate("'.$className.'");'.PHP_EOL;
	}

	public function getTestableMethods($className) {
		$classMethods = get_class_methods($className);
		$parentMethods = get_class_methods(get_parent_class($className));
		$thisMethods = array_diff($classMethods, $parentMethods);
		$out = array();
		foreach ($thisMethods as $method) {
			if (substr($method, 0, 1) != '_' && $method != strtolower($className)) {
				$out[] = $method;
			}
		}
		return $out;
	}

	public function testCaseFileName($type, $className,$append = '') {
		$path = $this->getPath() . 'Case' . DS;
		$type = Inflector::camelize($type);
		if (isset($this->classTypes[$type])) {
			$path .= $this->classTypes[$type] . DS;
		}
		$className = $this->getRealClassName($type, $className);
		return str_replace('/', DS, $path) . Inflector::camelize($className) .$append. 'Test.php';
	}
}
?>
