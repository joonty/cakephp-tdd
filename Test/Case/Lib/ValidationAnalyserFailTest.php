<?php

App::uses('ValidationAnalyser','Tdd.Lib');
App::uses('ValidationField','Tdd.Lib');
App::uses('Model','Model');
App::uses('Validation','Utility');

class Badexamplemodel extends Model {
	public $validate = array(
		'id'=>array(
			'rule1'=>array('rule'=>'numeric'),
			'rule2'=>array('rule'=>array('between',1,10))
		),
		'afield'=>array(
			'email'=>array('rule'=>'email'),
			'url'=>array('rule'=>'url'),
			'ip'=>array('rule'=>'ip')
		),
		'text'=>array(
			'maxLength'=>array('rule'=>'maxLength')
		)
	);
}

class ValidationAnalyserFailTestCase extends CakeTestCase {
	/**
	 * Subject under test.
	 * @var ValidationAnalyser
	 */
	protected $sut;
	
	public function setUp() {
		$model = new Badexamplemodel();
		$this->sut = new ValidationAnalyser($model);
	}
	
	public function testThereAreWarnings() {
		$warning = $this->sut->getWarningsAsString();
		debug($warning);
		$this->assertTrue(strlen($warning)>0,"There should be a warning message for parsing this rule set");
	}
	
	public function testIdFieldIsNumeric() {
		$ret = $this->sut->validField('id');
		$this->assertTrue(is_numeric($ret),"Return value for 'id' should be numeric");
	}
	
	public function testAfieldFieldIsNumeric() {
		$ret = $this->sut->validField('afield');
		$this->assertTrue(Validation::email($ret),"Return value for 'afield' should be an email");
	}
	
	/**
	 * @expectedException InvalidFieldNameException 
	 */
	public function testInvalidFieldThrowsException() {
		$this->sut->validField('anonexistentfield');
	}
}
?>
