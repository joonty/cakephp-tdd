<?php

App::uses('TestTask', 'Console/Command/Task');

class TddControllerTestTask extends TestTask {

	public function bake($type, $className, $actions, $helpers, $components) {
		$plugin = null;
		if ($this->plugin) {
			$plugin = $this->plugin . '.';
		}
		$bakeActions = false;
		if (strlen($actions)) {
			$bakeActions = true;
		}

		$realType = $this->mapType($type, $plugin);
		$fullClassName = $className . 'Controller';
		$primaryModel = Inflector::singularize($className);

		if ($this->typeCanDetectFixtures($type) && $this->isLoadableClass($realType, $fullClassName)) {
			$this->out(__d('cake_console', 'Bake is detecting possible fixtures...'));
			$testSubject = $this->buildTestSubject($type, $fullClassName);
			$this->generateFixtureList($testSubject);
			$models = $testSubject->uses;
		} elseif ($this->interactive) {
			$this->getUserFixtures();
			$models = array($primaryModel);
		}
		$primaryModel = $testSubject->modelClass;

		App::uses($fullClassName, 'app.'.$realType);

		$methods = array();
		if (class_exists($fullClassName, true)) {
			$methods = $this->getTestableMethods($fullClassName, strtolower($className));
		}
		$mock = $this->hasMockClass($type, $fullClassName);

		$this->out("\n" . __d('cake_console', 'Baking test case for %s %s ...', $className, $type), 1, Shell::QUIET);

		$this->Template->set('fixtures', $this->_fixtures);
		$this->Template->set('plugin', $plugin);
		$this->Template->set(compact(
		'className', 'methods', 'components', 'type', 'fullClassName', 'mock', 'construction', 'realType','primaryModel','models'
		));
		$out = $this->Template->generate('classes', 'controller_test');
		$outView = $this->Template->generate('classes', 'controller_view_test');
		$outVars = $this->Template->generate('classes', 'controller_vars_test');

		$filename = $this->testCaseFileName($type, $fullClassName);
		$made = $this->createFile($filename, $out);
		if ($made) {
			$this->createFile($this->testCaseFileName($type, $fullClassName, 'Vars'), $outVars);
			$this->createFile($this->testCaseFileName($type, $fullClassName, 'View'), $outView);
			return $out;
		}
		return false;
	}

	public function getTestableMethods($className, $urlName) {
		$classMethods = get_class_methods($className);
		$parentMethods = get_class_methods(get_parent_class($className));
		$thisMethods = array_diff($classMethods, $parentMethods);
		$out = array();
		foreach ($thisMethods as $method) {
			if (substr($method, 0, 1) != '_' && $method != strtolower($className)) {
				$type = $method;
				$prefix = '';
				$parts = explode('_', $method);
				if ($parts) {
					$type = array_pop($parts);
					if (count($parts)) {
						$prefix = '/' . $parts[0];
					}
				}
				$out[] = array(
					'name' => Inflector::classify($method),
					'action' => $prefix . '/' . $urlName . '/' . $type,
					'original_name' => $method,
					'type' => $type
				);
			}
		}
		return $out;
	}

	public function testCaseFileName($type, $className, $append = '') {
		$path = $this->getPath() . 'Case' . DS;
		$type = Inflector::camelize($type);
		if (isset($this->classTypes[$type])) {
			$path .= $this->classTypes[$type] . DS;
		}
		$className = $this->getRealClassName($type, $className);
		return str_replace('/', DS, $path) . Inflector::camelize($className) . $append . 'Test.php';
	}

}

?>
