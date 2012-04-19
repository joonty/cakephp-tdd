<?php

/**
 * Contains the TddTestHelper class.
 *
 * @package TDD
 * @author Jon Cairns <jon.cairns@22blue.co.uk>
 * @copyright Copyright (c) 22 Blue 2012
 */

App::uses('ClassRegistry','Utility');
App::uses('ValidationAnalyser','Tdd.Lib');

class TestFixtureException extends Exception {}
class TestValidatorException extends Exception {}
class TestDataException extends Exception {}

/**
 * TddTestHelper description
 *
 */
class TddTestHelper {

	protected static $fixtures;
	protected static $validators = array();

	protected static function getFixture($name) {
		if (substr($name,0,4) == 'app.') {
			$name = substr($name,4);
		}
		$class = ucfirst(strtolower($name)).'Fixture';

		if (isset(self::$fixtures[$class])) {
			return self::$fixtures[$class];
		}

		if (!class_exists($class)) {
			$path = TESTS."Fixture".DS.$class.'.php';

			if (!file_exists($path)) {
				throw new TestFixtureException("Missing or invalid fixture '$name'");
			}
			include $path;
			if (!class_exists($class)) {
				throw new TestFixtureException("Missing fixture class '$name'");
			}
		}

		self::$fixtures[$class] = new $class;
		return self::$fixtures[$class];
	}

	/**
	 * Get a validator for a model name.
	 *
	 * @throws TestValidatorException
	 * @param string $modelName e.g. 'User'
	 * @return ValidationAnalyser
	 */
	public static function validator($modelName) {
		if (array_key_exists($modelName,self::$validators)) {
			return self::$validators[$modelName];
		}
		$model = ClassRegistry::init($modelName);
		if (!$model) {
			throw new TestValidatorException("Could not load model '$modelName' for creating validation data'");
		}
		if ($model instanceof Model) {
			self::$validators[$modelName] = new ValidationAnalyser($model);
			return self::$validators[$modelName];
		} else {
			throw new TestValidatorException("CLass name passed to validator must be a Model");
		}
	}
	public static function getAllFixtureRecords($name) {
		return self::getFixture($name)->records;
	}

	public static function getFixtureRecord($name,$index) {
		$records = self::getAllFixtureRecords($name);
		if (count($records) && array_key_exists($index,$records)) {
			return $records[$index];
		} else {
			throw new TestFixtureException("No record with index $index exists in fixture $name");
		}
	}

	public static function getNewFixtureRecord($name) {
		$records = self::getAllFixtureRecords($name);
		$num = count($records);
		$index = rand(0,$num-1);
		$record = $records[$index];
		$ret = array();
		foreach ($record as $n=>$v) {
			switch ($n) {
				case 'id':
				case 'modified':
				case 'created':
					break;
				case 'email':
					$ret[$n]=$v;
					break;
				default:
					if (is_numeric($v)) {
						if ($v == 1) {
							$ret[$n] = $v;
						} else {
							$ret[$n] = $v+1;
						}
					} else {
						$ret[$n] = strrev($v);
					}
			}
		}
		return $ret;
	}

	public static function getRawData($file) {
		$path = self::getDataFile($file);
		return file_get_contents($path);
	}
	
	public static function getEvalData($file) {
		$path = self::getDataFile($file);
		return include $path;
	}

	protected static function getDataFile($file) {
		$file = ltrim(trim($file),DS);
		$root = TESTS."Data";
		if (!is_dir($root)) {
			throw new TestDataException("'Data' directory missing in Test directory - cannot load data");
		}
		if (strlen($file) == 0) {
			throw new TestDataException("Empty data file name");
		}
		
		$path = $root.DS.$file;
		if (!is_file($path)) {
			throw new TestDataException("Missing data file '$file', expected it at '$path'");
		}
		return $path;
	}
}

?>
