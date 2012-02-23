<?php

App::uses('SessionMockComponent','Tdd.Controller/Component');
/**
 * Contains a test case for the SessionMockComponent class.
 *
 * @package Bromford
 * @author Jon Cairns <jon.cairns@22blue.co.uk>
 * @copyright Copyright (c) 22 Blue 2012
 */
class SessionMockComponentTestCase extends CakeTestCase {
	protected $component;

	public function setUp() {
		$this->component = new SessionMockComponent(new ComponentCollection);
	}

	public function testWrite() {
		$this->component->write('key',3);
		$data = $this->component->read('key');
		$this->assertEquals(3,$data);
	}

	public function testReadReturnsNothing() {
		$val = $this->component->read('key');
		$this->assertNUll($val);
	}

	public function testWriteNS() {
		$this->component->write('some',array('key'=>10));
		$data = $this->component->read('some.key');
		$this->assertEquals(10,$data);
	}

	public function testSetFlash() {
		$this->component->setFlash('Flash message');
		$data = $this->component->read('Message.flash.message');
		$this->assertEquals("Flash message",$data);
	}

	public function testDelete() {
		$this->component->write('key',5);
		$this->component->delete('key');
		$data = $this->component->read('key');
		$this->assertEquals(null,$data);
	}

	public function testClear() {
		$this->component->write('key',"This is a value");
		$this->component->destroy();
		$data = $this->component->read('key');
		$this->assertEquals(null,$data);
	}

	public function testIdReturnsId() {
		$id = $this->component->id();
		$this->assertRegExp('/^[a-z0-9]+$/i', $id);
	}

	public function testSetIdReturnsId() {
		$idToSet = 'anid';
		$id = $this->component->id($idToSet);
		$this->assertEquals($idToSet, $id);
	}

	public function testCheck() {
		$this->component->write('key',3);
		$data = $this->component->check('key');
		$this->assertTrue($data);
	}

	public function testCheckReturnsFalse() {
		$data = $this->component->check('key');
		$this->assertFalse($data);
	}

	public function testCheckNS() {
		$this->component->write('some',array('key'=>3));
		$data = $this->component->check('some.key');
		$this->assertTrue($data);
	}
}

?>
