<?php
App::uses('ValidationDataGenerator','Tdd.Lib');
App::uses('ValidationRule','Tdd.Lib');
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
	
	public function testGetDataWithNumeric() {
		$data = $this->sut->dispatch(new ValidationRule('numeric'));
		$this->assertTrue(is_numeric($data),"Data must be numeric");
	}
	
	public function testGetDataWithMaxLength() {
		$this->markTestIncomplete();
		$len = 20;
		$data = $this->sut->getData(array('maxLength',$len));
		$this->assertInternalType('string',$data);
		$this->assertTrue(strlen($data) <= $len,"String should be less than $len characters");
	}
	
	public function testGetDataWithMinLength() {
		$this->markTestIncomplete();
		$len = 20;
		$data = $this->sut->getData(array('minLength',$len));
		$this->assertInternalType('string',$data);
		$this->assertTrue(strlen($data) >= $len,"String should be longer than $len characters");
	}
	
	public function testGetDataWithIp() {
		$data = $this->sut->dispatch(new ValidationRule('ip'));
		$this->assertInternalType('string',$data);
		$this->assertRegExp('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$data);
	}
	
	public function testGetDataWithIpv6() {
		$data = $this->sut->dispatch(new ValidationRule('ip',array("IPV6")));
		$this->assertInternalType('string',$data);
		$this->assertRegExp('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$data);
	}
	
	public function testGetDataWithEmail() {
		$data = $this->sut->dispatch(new ValidationRule('email'));
		$this->assertInternalType('string',$data);
		$this->assertRegExp('/^[a-z0-9]+@[a-z0-9]+\.[a-z.]{2,6}$/i',$data,"String should be a valid email address");
	}

	public function testGetDataWithAlphaNumeric() {
		$data = $this->sut->dispatch(new ValidationRule('alphanumeric'));		
		$this->assertInternalType('string',$data);
		$this->assertRegExp('/^[a-z0-9]+$/i',$data,"String should contain only alphanumeric characters");
	}

	public function testGetDataWithAlphaNumericCase() {
		$data = $this->sut->dispatch(new ValidationRule('alphaNumeric'));		
		$this->assertInternalType('string',$data);
		$this->assertRegExp('/^[a-z0-9]+$/i',$data,"String should contain only alphanumeric characters");
	}
	
	public function testGetDataWithBoolean() {
		$data = $this->sut->dispatch(new ValidationRule('boolean'));		
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
		$data = $this->sut->dispatch(new ValidationRule('date',array($format)));

		$this->assertInternalType('string',$data);
		
		$this->assertTrue(Validation::date($data, $format),"Date failed validation");
	}

	public function testGetDataWithDateDefaultFormat() {
		$data = $this->sut->dispatch(new ValidationRule('date'));	
		$this->assertInternalType('string',$data);
		
		$this->assertTrue(Validation::date($data, 'ymd'),"Date failed validation");
	}
	

	/**
	 * @dataProvider provideDateFormats 
	 */
	public function testGetDataWithDatetime($format) {
		$data = $this->sut->dispatch(new ValidationRule('datetime',array($format)));
		$this->assertInternalType('string',$data);
		
		$this->assertTrue(Validation::datetime($data, $format),"Datetime failed validation");
	}
	
	public function testGetDataWithDatetimeDefaultFormat() {
		$data = $this->sut->dispatch(new ValidationRule('datetime'));				
		$this->assertInternalType('string',$data);
		
		$this->assertTrue(Validation::datetime($data, 'ymd'),"Datetime failed validation");
	}
	
	public function testGetDataWithDecimal() {
		$data = $this->sut->dispatch(new ValidationRule('decimal'));		
		$this->assertInternalType('float',$data);
	}
	
	public function testGetDataWithExtension() {
		$data = $this->sut->dispatch(new ValidationRule('extension'));
		$this->assertInternalType('string',$data);
		$this->assertRegExp('~.+\.(gif|jpg|png|jpeg)$~',$data,"invalid extension returned");
	}
	
	public function testGetDataWithCustomExtension() {
		$data = $this->sut->dispatch(new ValidationRule('extension',array(array('pdf','zip'))));
		$this->assertInternalType('string',$data);
		$this->assertRegExp('~.+\.(pdf|zip)$~',$data,"invalid extension returned");
	}
	
	public function testGetDataWithEqualTo() {
		$equalValue = "This must be the result";
		$data = $this->sut->dispatch(new ValidationRule('equalTo',array($equalValue)));
		$this->assertEqual($equalValue,$data,"Result must be equal");
	}
	
	public function testGetDataWithInList() {
		$equalList = array("This is a result",3,"Blah blah");
		$data = $this->sut->dispatch(new ValidationRule('inList',array($equalList)));
		$this->assertTrue(in_array($data,$equalList),"Result must be one of the supplied values");
	}
}

?>