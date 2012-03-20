<?php
/**
 * This fixture manager allows individual table set up.
 *
 * @package TDD
 */
class TddFixtureManager extends CakeFixtureManager {
	public function setupFixture($fixtureName) {
		$this->_initDb();
		$fixture = new $fixtureName($this->_db);
		$this->_setupTable($fixture);
	}

}

?>