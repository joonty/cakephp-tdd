<?php
/**
 * Contains the ArrayCacheTestCase class.
 *
 * @package Tdd
 * @author Jon Cairns <jon.cairns@22blue.co.uk>
 * @copyright Copyright (c) 22 Blue 2012
 */

App::uses('ArrayCacheEngine','Tdd.Cache/Engine');

/**
 * ArrayCacheTestCase is a test case for the ArrayCache Engine.
 *
 */
class ArrayCacheTestCase extends TddTestCase {
	/**
	 * Subject under test
	 * @var ArrayCacheEngine
	 */
	protected $sut;

	public function setUp() {
		parent::setUp();

		$this->sut = new ArrayCacheEngine();
	}

	public function provideValues() {
		return array(
			array('keyname','value',5),
			array('something',313,10),
			array('anarray',array('hello'),3600)
		);
	}

	/**
	 * @dataProvider provideValues
	 */
	public function testWrite($key,$value,$cachetime) {
		$this->sut->write($key,$value,$cachetime);
		$this->assertAttributeEquals(
			array($key=>array('v'=>$value,'e'=>time()+$cachetime)),
			'data',
			$this->sut,
			'Cached data is invalid'
		);
	}

	/**
	 * @dataProvider provideValues
	 */
	public function testRead($key,$value,$cachetime) {
		$this->sut->write($key,$value,$cachetime);
		$returnValue = $this->sut->read($key);
		$this->assertEqual($value,$returnValue,"Cached data is not equal to input data!");
	}

	public function testReadFails() {
		$data = $this->sut->read('invalidkey');
		$this->assertFalse($data,"Return value of read() with an invalid key should be false");
	}

	public function testCacheExpiry() {
		$this->sut->write('key',1,-1);
		$returnValue = $this->sut->read('key');
		$this->assertFalse($returnValue,"Cached data did not expire");
	}

	/**
	 * @dataProvider provideValues
	 */
	public function testDelete($key,$value,$cachetime) {
		$this->sut->write($key,$value,$cachetime);

		$delRet = $this->sut->delete($key);
		$this->assertTrue($delRet,"Delete failed - key doesn't exist");

		$returnValue = $this->sut->read($key);
		$this->assertFalse($returnValue,"Delete failed - return value of read() should be false");
	}

	public function testDeleteFails() {
		$retVal = $this->sut->delete('missingkey');
		$this->assertFalse($retVal,"Return value of deleting a non-existent key should be false");
	}

	public function testClearDeletesData() {
		$this->sut->write('key',1,10);
		$retval = $this->sut->clear();
		$this->assertTrue($retval,"Return value of clear() should be true");
		$this->assertAttributeEquals(array(), 'data', $this->sut);
	}

	public function testClearExpiredOnly() {
		$this->sut->write('key1',1,10);
		$this->sut->write('key2',2,-1);

		$this->sut->clear(true);

		$this->assertEqual(1,$this->sut->read('key1'));
		$this->assertFalse($this->sut->read('key2'));
	}

	public function testIncrement() {
		$this->sut->write('key',0,10);
		$this->sut->increment('key');
		$this->assertEqual(1,$this->sut->read('key'));
	}

	public function testIncrementByAmount() {
		$amount = 5;
		$this->sut->write('key',0,10);
		$this->sut->increment('key',$amount);
		$this->assertEqual($amount,$this->sut->read('key'));
	}

	public function testIncrementFailsWithInvalidKey() {
		$ret = $this->sut->increment('key');
		$this->assertFalse($ret);
	}

	public function testDecrement() {
		$this->sut->write('key',0,10);
		$this->sut->decrement('key');
		$this->assertEqual(-1,$this->sut->read('key'));
	}

	public function testDecrementByAmount() {
		$amount = 5;
		$this->sut->write('key',0,10);
		$this->sut->decrement('key',$amount);
		$this->assertEqual(-$amount,$this->sut->read('key'));
	}

	public function testDecrementFailsWithInvalidKey() {
		$ret = $this->sut->decrement('key');
		$this->assertFalse($ret);
	}
}

?>
