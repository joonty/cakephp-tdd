<?php

App::uses('ValidationAnalyser','Tdd.Lib');
App::uses('Model','Model');

class Examplemodel extends Model {
	public $validate = array(
		'id'=>'numeric'
	);
}

class ValidationAnalyserTestCase extends CakeTestCase {
	protected $sut;
	
	public function setUp() {
		$model = new Examplemodel();
		$this->sut = new ValidationAnalyser($model);
	}
	
	public function testHasRules() {
		$this->assertTrue($this->sut->hasRules());
	}
	
	public function testExampleIdIsNumeric() {
		
	}
}
?>
