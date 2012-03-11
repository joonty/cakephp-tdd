<?php
App::uses('ValidationDataGenerator','Tdd.Lib');
App::uses('ValidationRule','Tdd.Lib');
App::uses('ValidationField','Tdd.Lib');
App::uses('ValidationAnalyser','Tdd.Lib');
App::uses('Validation','Utility');

/**
 * Description of ValidationDataGeneratorTest
 */
class ValidationDataGeneratorTestCase extends CakeTestCase {
	/**
	 *
	 * @var ValidationDataGenerator
	 */
	protected $sut;
	public function setUp() {
		parent::setUp();
		$this->sut = new ValidationDataGenerator();
	}
	
	protected function getRule($rule,$params = array()) {
		$field = $this->getMock('ValidationField',array(),array(),'',false);
		return new ValidationRule($field,$rule,$params);
	}
	
	public function testGetDataWithBlank() {
		$data = $this->sut->dispatch($this->getRule('blank'));
		$this->assertEqual("",$data,"Data must be an empty string");
	}
	
	public function testGetDataWithNumeric() {
		$data = $this->sut->dispatch($this->getRule('numeric'));
		$this->assertTrue(is_numeric($data),"Data must be numeric");
	}
	
	public function testGetDataWithBetween() {
		$data = $this->sut->dispatch($this->getRule('between',array(5,10)));
		$this->assertGreaterThanOrEqual(5,$data,"Data must be >= 5");
		$this->assertLessThanOrEqual(10,$data,"Data must be <= 10");
	}
	
	public function testGetDataWithRange() {
		$data = $this->sut->dispatch($this->getRule('range',array(-1,11)));
		$this->assertGreaterThanOrEqual(0,$data,"Data must be >= 0");
		$this->assertLessThanOrEqual(10,$data,"Data must be <= 10");
	}
	
	public function testGetDefaultData() {
		$data = $this->sut->dispatch($this->getRule('unknownrule'));
		$this->assertInternalType('string',$data);
	}
	
	public function testGetDefaultDataSetsWarning() {
		$field = $this->getMock('ValidationField',array(),array(),'',false);
		$field->expects($this->once())
			->method("addWarning")
			->with("Using default data for rule 'unknownrule'");
		$rule = new ValidationRule($field,"unknownrule");
		$data = $this->sut->dispatch($rule);
	}
	
	public function testGetDataWithIp() {
		$data = $this->sut->dispatch($this->getRule('ip'));
		$this->assertInternalType('string',$data);
		$this->assertRegExp('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$data);
	}
	
	public function testGetDataWithIpv6() {
		$data = $this->sut->dispatch($this->getRule('ip',array("IPV6")));
		$this->assertInternalType('string',$data);
		$this->assertRegExp('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$data);
	}
	
	public function testGetDataWithEmail() {
		$data = $this->sut->dispatch($this->getRule('email'));
		$this->assertInternalType('string',$data);
		$this->assertTrue(Validation::email($data),"String should be a valid email address");
	}
	
	public function testGetDataWithUrl() {
		$data = $this->sut->dispatch($this->getRule('url'));
		$this->assertInternalType('string',$data);
		$this->assertTrue(Validation::url($data),"String should be a valid URL");
	}


	public function testGetDataWithAlphaNumeric() {
		$data = $this->sut->dispatch($this->getRule('alphanumeric'));		
		$this->assertInternalType('string',$data);
		$this->assertRegExp('/^[a-z0-9]+$/i',$data,"String should contain only alphanumeric characters");
	}

	public function testGetDataWithAlphaNumericCase() {
		$data = $this->sut->dispatch($this->getRule('alphaNumeric'));		
		$this->assertInternalType('string',$data);
		$this->assertRegExp('/^[a-z0-9]+$/i',$data,"String should contain only alphanumeric characters");
	}
	
	public function testGetDataWithBoolean() {
		$data = $this->sut->dispatch($this->getRule('boolean'));		
		$this->assertInternalType('boolean',$data);
		$this->assertTrue($data);
	}
	
	public function provideDateFormats() {
		return array(
			array('dmy'),
			array('mdy'),
			array('ymd'),
			array('dMy'),
			array('Mdy'),
			array('My'),
			array('my')
		);
	}

	/**
	 * @dataProvider provideDateFormats 
	 */
	public function testGetDataWithDate($format) {
		$data = $this->sut->dispatch($this->getRule('date',array($format)));

		$this->assertInternalType('string',$data);
		
		$this->assertTrue(Validation::date($data, $format),"Date failed validation");
	}

	public function testGetDataWithDateDefaultFormat() {
		$data = $this->sut->dispatch($this->getRule('date'));	
		$this->assertInternalType('string',$data);
		
		$this->assertTrue(Validation::date($data, 'ymd'),"Date failed validation");
	}
	

	/**
	 * @dataProvider provideDateFormats 
	 */
	public function testGetDataWithDatetime($format) {
		$data = $this->sut->dispatch($this->getRule('datetime',array($format)));
		$this->assertInternalType('string',$data);
		
		$this->assertTrue(Validation::datetime($data, $format),"Datetime failed validation");
	}
	
	public function testGetDataWithDatetimeDefaultFormat() {
		$data = $this->sut->dispatch($this->getRule('datetime'));				
		$this->assertInternalType('string',$data);
		
		$this->assertTrue(Validation::datetime($data, 'ymd'),"Datetime failed validation");
	}
	
	public function testGetDataWithTime() {
		$data = $this->sut->dispatch($this->getRule('time'));
		$this->assertInternalType('string',$data);
		
		$this->assertTrue(Validation::time($data),"Time failed validation");
	}
	
	public function testGetDataWithDecimal() {
		$data = $this->sut->dispatch($this->getRule('decimal'));		
		$this->assertInternalType('float',$data);
	}
	
	public function testGetDataWithExtension() {
		$data = $this->sut->dispatch($this->getRule('extension'));
		$this->assertInternalType('string',$data);
		$this->assertRegExp('~.+\.(gif|jpg|png|jpeg)$~',$data,"invalid extension returned");
	}
	
	public function testGetDataWithCustomExtension() {
		$data = $this->sut->dispatch($this->getRule('extension',array(array('pdf','zip'))));
		$this->assertInternalType('string',$data);
		$this->assertRegExp('~.+\.(pdf|zip)$~',$data,"invalid extension returned");
	}
	
	public function testGetDataWithEqualTo() {
		$equalValue = "This must be the result";
		$data = $this->sut->dispatch($this->getRule('equalTo',array($equalValue)));
		$this->assertEqual($equalValue,$data,"Result must be equal");
	}
	
	public function testGetDataWithInList() {
		$equalList = array("This is a result",3,"Blah blah");
		$data = $this->sut->dispatch($this->getRule('inList',array($equalList)));
		$this->assertTrue(in_array($data,$equalList),"Result must be one of the supplied values");
	}
}

?>