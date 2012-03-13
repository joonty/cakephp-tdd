<?php

App::uses('ValidationAnalyser','Tdd.Lib');
App::uses('ValidationField','Tdd.Lib');
App::uses('Model','Model');

class Examplemodel extends Model {
	public $validate = array(
		'id'=>'numeric',
		'afield'=>array('rule'=>array('between',10,20)),
		'email'=>'email'
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

	public function testIdFieldIsNumeric() {
		$ret = $this->sut->validField('id');
		$this->assertTrue(is_numeric($ret),"Return value for 'id' should be numeric");
	}

	public function testInvalidIdFieldIsNotNumeric() {
		$ret = $this->sut->invalidField('id');
		$this->assertFalse(is_numeric($ret),"Invalid value for 'id' should NOT be numeric");
	}

	public function testAFieldFieldIsNumeric() {
		$ret = $this->sut->validField('afield');
		$this->assertGreaterThanOrEqual(10, strlen($ret));
		$this->assertLessThanOrEqual(20, strlen($ret));
	}

	public function testValidDataContainsAllKeys() {
		$data = $this->sut->validData();
		$this->assertInternalType('array',$data);
		$this->assertArrayHasKey('id', $data);
		$this->assertArrayHasKey('afield', $data);
		$this->assertArrayHasKey('email', $data);
	}

	public function testInvalidDataContainsAllKeys() {
		$data = $this->sut->invalidData();
		$this->assertInternalType('array',$data);
		$this->assertArrayHasKey('id', $data);
		$this->assertArrayHasKey('afield', $data);
		$this->assertArrayHasKey('email', $data);
	}
}
?>
