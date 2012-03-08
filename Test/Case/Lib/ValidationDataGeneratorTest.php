<?php
App::uses('ValidationDataGenerator','Tdd.Lib');

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
		$data = $this->sut->getData('numeric');
		$this->assertTrue(is_numeric($data),"Data must be numeric");
	}
	
	public function testGetDataWithMaxLength() {
		$len = 20;
		$data = $this->sut->getData(array('maxLength',$len));
		$this->assertInternalType('string',$data);
		$this->assertTrue(strlen($data) <= $len,"String should be less than $len characters");
	}
	
	public function testGetDataWithMinLength() {
		$len = 20;
		$data = $this->sut->getData(array('minLength',$len));
		$this->assertInternalType('string',$data);
		$this->assertTrue(strlen($data) >= $len,"String should be longer than $len characters");
	}
	
	public function testGetDataWithIp() {
		$data = $this->sut->getData('ip');
		$this->assertRegExp('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$data);
	}
	
	public function testGetDataWithIpv6() {
		$data = $this->sut->getData(array('ip','IPV6'));
		$this->assertRegExp('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$data);
	}
	
	public function testGetDataWithEmail() {
		$data = $this->sut->getData(array('ip','IPV6'));
		$this->assertRegExp('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/',$data);
	}
}

?>
