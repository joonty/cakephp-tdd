<?php

App::uses('ValidationAnalyser','Tdd.Lib');
App::uses('Model','Model');

class Examplemodel extends Model {
	public $validate = array(
		'id'=>'numeric'
	);
}

class ValidationAnalyserTestCase extends CakeTestCase {
	/**
	 * Subject under test.
	 * @var ValidationAnalyser
	 */
	protected $sut;
	
	public function setUp() {
		$model = new Examplemodel();
		$this->sut = new ValidationAnalyser($model);
	}
	
	public function testHasRules() {
		$this->assertTrue($this->sut->hasRules());
	}
	
	public function testValidFieldIsNumeric() {
		$ret = $this->sut->validField('id');
		$this->assertTrue(is_numeric($ret),"Return value for 'id' should be numeric");
	}
}
?>
