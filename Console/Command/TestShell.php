<?php

/**
 * Contains the TestShell class.
 *
 * @package Local TV
 * @author Jon Cairns <jon.cairns@ggapps.co.uk>
 * @copyright Copyright (c) Green Gorilla Apps 2012
 */

App::uses('TestsuiteShell','Console/Command');
/**
 * TestShell description
 *
 */
class TestShell extends TestsuiteShell {

	public function getOptionParser() {
		$parser = new ConsoleOptionParser($this->name);
		$parser->description(array(
			__d('cake_console', 'The Tdd test suite allows you to run your app test cases from the command line'),
			__d('cake_console', 'If run with no command line arguments, a list of available test cases will be shown')
		))->addArgument('file', array(
			'help' => __d('cake_console', 'file name or glob pattern with folder prefix and without the test.php suffix.'),
			'required' => false,
		))->addOption('log-junit', array(
			'help' => __d('cake_console', '<file> Log test execution in JUnit XML format to file.'),
			'default' => false
		))->addOption('log-json', array(
			'help' => __d('cake_console', '<file> Log test execution in TAP format to file.'),
			'default' => false
		))->addOption('log-tap', array(
			'help' => __d('cake_console', '<file> Log test execution in TAP format to file.'),
			'default' => false
		))->addOption('log-dbus', array(
			'help' => __d('cake_console', 'Log test execution to DBUS.'),
			'default' => false
		))->addOption('coverage-html', array(
			'help' => __d('cake_console', '<dir> Generate code coverage report in HTML format.'),
			'default' => false
		))->addOption('coverage-clover', array(
			'help' => __d('cake_console', '<file> Write code coverage data in Clover XML format.'),
			'default' => false
		))->addOption('testdox-html', array(
			'help' => __d('cake_console', '<file> Write agile documentation in HTML format to file.'),
			'default' => false
		))->addOption('testdox-text', array(
			'help' => __d('cake_console', '<file> Write agile documentation in Text format to file.'),
			'default' => false
		))->addOption('filter', array(
			'help' => __d('cake_console', '<pattern> Filter which tests to run.'),
			'default' => false
		))->addOption('group', array(
			'help' => __d('cake_console', '<name> Only runs tests from the specified group(s).'),
			'default' => false
		))->addOption('exclude-group', array(
			'help' => __d('cake_console', '<name> Exclude tests from the specified group(s).'),
			'default' => false
		))->addOption('list-groups', array(
			'help' => __d('cake_console', 'List available test groups.'),
			'boolean' => true
		))->addOption('loader', array(
			'help' => __d('cake_console', 'TestSuiteLoader implementation to use.'),
			'default' => false
		))->addOption('repeat', array(
			'help' => __d('cake_console', '<times> Runs the test(s) repeatedly.'),
			'default' => false
		))->addOption('tap', array(
			'help' => __d('cake_console', 'Report test execution progress in TAP format.'),
			'boolean' => true
		))->addOption('testdox', array(
			'help' => __d('cake_console', 'Report test execution progress in TestDox format.'),
			'default' => false,
			'boolean' => true
		))->addOption('no-colors', array(
			'help' => __d('cake_console', 'Do not use colors in output.'),
			'boolean' => true
		))->addOption('stderr', array(
			'help' => __d('cake_console', 'Write to STDERR instead of STDOUT.'),
			'boolean' => true
		))->addOption('stop-on-error', array(
			'help' => __d('cake_console', 'Stop execution upon first error or failure.'),
			'boolean' => true
		))->addOption('stop-on-failure', array(
			'help' => __d('cake_console', 'Stop execution upon first failure.'),
			'boolean' => true
		))->addOption('stop-on-skipped ', array(
			'help' => __d('cake_console', 'Stop execution upon first skipped test.'),
			'boolean' => true
		))->addOption('stop-on-incomplete', array(
			'help' => __d('cake_console', 'Stop execution upon first incomplete test.'),
			'boolean' => true
		))->addOption('strict', array(
			'help' => __d('cake_console', 'Mark a test as incomplete if no assertions are made.'),
			'boolean' => true
		))->addOption('wait', array(
			'help' => __d('cake_console', 'Waits for a keystroke after each test.'),
			'boolean' => true
		))->addOption('process-isolation', array(
			'help' => __d('cake_console', 'Run each test in a separate PHP process.'),
			'boolean' => true
		))->addOption('no-globals-backup', array(
			'help' => __d('cake_console', 'Do not backup and restore $GLOBALS for each test.'),
			'boolean' => true
		))->addOption('static-backup ', array(
			'help' => __d('cake_console', 'Backup and restore static attributes for each test.'),
			'boolean' => true
		))->addOption('syntax-check', array(
			'help' => __d('cake_console', 'Try to check source files for syntax errors.'),
			'boolean' => true
		))->addOption('bootstrap', array(
			'help' => __d('cake_console', '<file> A "bootstrap" PHP file that is run before the tests.'),
			'default' => false
		))->addOption('configuration', array(
			'help' => __d('cake_console', '<file> Read configuration from XML file.'),
			'default' => false
		))->addOption('no-configuration', array(
			'help' => __d('cake_console', 'Ignore default configuration file (phpunit.xml).'),
			'boolean' => true
		))->addOption('include-path', array(
			'help' => __d('cake_console', '<path(s)> Prepend PHP include_path with given path(s).'),
			'default' => false
		))->addOption('directive', array(
			'help' => __d('cake_console', 'key[=value] Sets a php.ini value.'),
			'default' => false
		))->addOption('fixture', array(
			'help' => __d('cake_console', 'Choose a custom fixture manager.'),
		))->addOption('debug', array(
			'help' => __d('cake_console', 'Enable full output of testsuite. (supported in PHPUnit 3.6.0 and greater)'),
		));
		return $parser;
	}

	public function initialize() {
		parent::initialize();

		//Stop trying to use file caching for Cake core
		Cache::drop('_cake_core_');
		Cache::config('_cake_core_', array('engine' => 'Tdd.ArrayCache'));

		Cache::drop('_cake_model_');
		Cache::config('_cake_model_', array('engine' => 'Tdd.ArrayCache'));

		//Cake doesn't want to include my test case, so we'll do it old-school
		$testsuite = realpath(__DIR__.'/../../TestSuite');
		require $testsuite.'/TddTestCase.php';
		require $testsuite.'/TddTestHelper.php';
		require $testsuite.'/TddControllerTestCase.php';
		require $testsuite.'/TddTestSuiteCommand.php';
		require $testsuite.'/TddTestLoader.php';
		require $testsuite.'/TddTestRunner.php';
		require $testsuite.'/TddTestSuite.php';
		require $testsuite.'/TddFixtureManager.php';
	}

	protected function _parseArgs() {
		$params = array(
			'core' => false,
			'app' => true,
			'plugin' => null,
			'output' => 'text',
		);

		if (isset($this->args[0])) {
			$params['case'] = $this->args[0];
		}
		return $params;
	}

	protected function _run($runnerArgs, $options = array()) {
		restore_error_handler();
		restore_error_handler();

		$testCli = new TddTestSuiteCommand('TddTestLoader', $runnerArgs);
		$testCli->run($options);
	}

	public function main() {
		$this->out(__d('cake_console', 'TDD Test Shell'));
		$this->hr();

		$args = $this->_parseArgs();

		if (empty($args['case'])) {
			return $this->available();
		}

		$this->_run($args, $this->_runnerOptions());
	}

	public function available() {
		$params = $this->_parseArgs();
		$testCases = CakeTestLoader::generateTestList($params);
		$app = $params['app'];
		$title = "App Test Cases:";
		$category = 'app';

		if (empty($testCases)) {
			$this->out(__d('cake_console', "No test cases available \n\n"));
			return $this->out($this->OptionParser->help());
		}

		$this->out($title);
		$i = 1;
		$cases = array();
		foreach ($testCases as $testCaseFile => $testCase) {
			$case = str_replace('Test.php', '', $testCase);
			$this->out("[$i] $case");
			$cases[$i] = $case;
			$i++;
		}

		while ($choice = $this->in(__d('cake_console', 'What test case would you like to run?'), null, 'q')) {
			if (is_numeric($choice)  && isset($cases[$choice])) {
				$this->args[0] = $cases[$choice];
				$this->_run($this->_parseArgs(), $this->_runnerOptions());
				break;
			}

			if (is_string($choice) && in_array($choice, $cases)) {
				$this->args[0] = $choice;
				$this->_run($this->_parseArgs(), $this->_runnerOptions());
				break;
			}

			if ($choice == 'q') {
				break;
			}
		}
	}

	public function out($message = null, $newlines = 1, $level = Shell::NORMAL) {
		$currentLevel = Shell::NORMAL;
		if (!empty($this->params['verbose'])) {
			$currentLevel = Shell::VERBOSE;
		}
		if (!empty($this->params['quiet'])) {
			$currentLevel = Shell::QUIET;
		}
		if ($level <= $currentLevel) {
			if (isset($this->params['stderr'])) {
				return $this->stderr->write($message, $newlines);
			} else {
				return $this->stdout->write($message, $newlines);
			}
		}
		return true;
	}

}

?>
