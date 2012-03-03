<?php

App::uses('ValidationAnalyser','Tdd.Lib');
App::uses('Model','Model');

class Examplemodel2 extends Model {
}

class ValidationAnalyserNoRulesTestCase extends CakeTestCase {
	protected $sut;
	
	public function setUp() {
		$model = new Examplemodel2();
		$this->sut = new ValidationAnalyser($model);
	}
	
	public function testHasRulesReturnsFalse() {
		$this->assertFalse($this->sut->hasRules());
	}
}
?>
