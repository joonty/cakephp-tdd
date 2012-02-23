<?php

/**
 * Contains the TddTestHelper class.
 *
 * @package TDD
 * @author Jon Cairns <jon.cairns@22blue.co.uk>
 * @copyright Copyright (c) 22 Blue 2012
 */

class TestFixtureException extends Exception {}

/**
 * TddTestHelper description
 *
 */
class TddTestHelper {

	protected static $fixtures;

	protected static function getFixture($name) {
		if (substr($name,0,4) == 'app.') {
			$name = substr($name,4);
		}
		$class = ucfirst(strtolower($name)).'Fixture';

		if (isset(self::$fixtures[$class])) {
			return self::$fixtures[$class];
		}

		if (!class_exists($class)) {
			throw new TestFixtureException("Invalid or uninitialized fixture '$name'");
		}

		self::$fixtures[$class] = new $class;
		return self::$fixtures[$class];
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
}

?>
