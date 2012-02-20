<?php
/**
 * Test Case bake template
 *
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Console.Templates.default.classes
 * @since         CakePHP(tm) v 1.3
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
echo "<?php\n";
echo "/* ". $className ." Test cases generated on: " . date('Y-m-d H:i:s') . " : ". time() . "*/\n";
?>
App::uses('<?php echo $fullClassName; ?>', '<?php echo $realType; ?>');

/**
 * <?php echo $fullClassName; ?> Test Case
 *
 */
class <?php echo $fullClassName; ?>TestCase extends TddControllerTestCase {
<?php if (!empty($fixtures)): ?>
	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array('<?php echo join("', '", $fixtures); ?>');

<?php endif; ?>

	protected static $deleteIncr = 0;

	/***************
	*
	* Set up / Tear down
	*
	***************/

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this-><?php echo $className?> = $this->generate('<?php echo $className ?>'<?php if (count($components)==0):?>);<?php else: ?>, array(
			'components' => array(
<?php foreach ($components as $c) {
	switch ($c) {

		//Always skip authorisation
		case 'Auth':
			echo <<<EOD
				'Auth' => array('isAuthorized'),

EOD;
			break;

		//Do nothing for the session - use ArraySession for mocking
		case 'Session':
			break;
		default:
			echo <<<EOD
				'$c',

EOD;
	}
}
?>
			)
		));
<?php endif;?>
<?php if (in_array('Auth',$components)): ?>
		$this-><?php echo $className ?>->Auth
			->expects($this->any())
			->method('isAuthorized')
			->will($this->returnValue(true));
<?php endif; ?>
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown() {
		unset($this-><?php echo $className;?>);

		parent::tearDown();
	}

        /***************
        *
        * Data providers
        *
        ***************/

	/**
	 * Return a couple of IDs that are entered by the fixture.
	 */
	public function provideIds() {
		return array(
			array(1),
			array(5)
		);
	}

	/**
	 * Return a new ID for testing a delete, each time this provider is called.
	 */
	public function provideDeleteIds() {
		self::$deleteIncr++;
		return array(
			array(self::$deleteIncr)
		);
	}

	/**
	 * Return some examples of invalid IDs.
	 */
	public function provideInvalidIds() {
		return array(
			array(''),
			array(12345),
			array('notanid')
		);
	}

	/**
	 * Return some invalid model data.
	 *
	 * @todo You should add some more entries with different data as you supply validation rules on the model. This currently assumes your ids are numeric only.
	 */
	public function provideInvalidData() {
		return array(
			array(array('<?php echo $primaryModel?>' => array(
					'id' => 'invalidid'
				)
			))
		);
	}

        /***************
        *
        * Test cases
        *
        ***************/

<?php foreach ($methods as $method): ?>
<?php switch ($method['type']):?>
<?php case "index":?>
<?php // We don't need an index test in this controller test case - try vars or view?>
<?php break;
	case "view":?>
	/**
	 * test<?php echo $method['name'] ?> method
	 *
	 * @dataProvider provideInvalidIds
	 * @expectedException NotFoundException
	 */
	public function test<?php echo $method['name']?>ThrowsExceptionWithInvalidId($id) {
		$retval = $this->testAction(
			'<?php echo $method['action']?>/'.$id,
			array('return'=>'result')
		);
	}
<?php break;
	case "add":?>

	/**
	 * @dataProvider provideInvalidData
	 */
	public function test<?php echo $method['name']?>FailsWithInvalidData($data) {
		$this->testAction(
			'<?php echo $method['action']?>',
			array('data'=>$data)
		);
		$flash = $this->controller->Session->read('Message.flash.message');
		$this->assertContains("could not be saved", $flash);
	}

	public function test<?php echo $method['name']?>SavesData() {
		//You might want to create your own data, rather than using newFixureRecord()
		$postData = array('<?php echo $primaryModel?>'=>$this->newFixtureRecord('<?php echo strtolower($primaryModel)?>'));
		$this->testAction(
			'<?php echo $method['action']?>',
			array('data'=>$postData)
		);

		$conditions = array();
		foreach ($postData['<?php echo $primaryModel?>'] as $n=>$v) {
			$conditions['<?php echo $primaryModel?>.'.$n] = $v;
		}

		$dbData = $this->controller-><?php echo $primaryModel?>->find('first',array('conditions'=>$conditions));

		$this->assertInternalType('array',$dbData);
		foreach ($postData['<?php echo $primaryModel?>'] as $key=>$value) {
			$this->assertEqual($value,$dbData['<?php echo $primaryModel?>'][$key]);
		}
	}
<?php break;
        case "edit":?>

	/**
	 * test<?php echo $method['name'] ?> method
	 *
	 * @dataProvider provideInvalidIds
	 * @expectedException NotFoundException
	 */
	public function test<?php echo $method['name']?>ThrowsExceptionWithInvalidId($id) {
		$retval = $this->testAction(
			'<?php echo $method['action']?>/'.$id,
			array('return'=>'result')
		);
	}

	public function test<?php echo $method['name'] ?>ModifiesData() {
		$id = 1;

		// You may want to put your own data manipulation in here.
		$postData = array('<?php echo $primaryModel?>'=>$this->newFixtureRecord('<?php echo strtolower($primaryModel)?>'));
		$postData['<?php echo $primaryModel?>']['id'] = $id;

		$this->testAction(
			'<?php echo $method['action']?>/'.$id,
			array('data'=>$postData)
		);

		$dbData = $this->controller-><?php echo $primaryModel?>->findById($id);

		$this->assertInternalType('array',$dbData);
		foreach ($postData['<?php echo $primaryModel?>'] as $key=>$value) {
			$this->assertEqual($value,$dbData['<?php echo $primaryModel?>'][$key]);
		}
	}

	/**
	 * @dataProvider provideIds
	 */
	public function test<?php echo $method['name'] ?>WithGetMethodDoesARead($id) {
		$this->testAction(
				'<?php echo $method['action']?>/'.$id,
				array('method'=>'get')
		);
		$this->assertArrayHasKey('<?php echo $primaryModel?>',$this->controller->request->data);
		$result = $this->controller->request->data;
		$this->assertEqual($result['<?php echo $primaryModel?>']['id'],$id);
	}

<?php break;
        case "delete":?>

	/**
	 * test<?php echo $method['name'] ?> method
	 *
	 * @expectedException MethodNotAllowedException
	 */
	public function test<?php echo $method['name']?>ThrowsExceptionWithInvalidMethod() {
		$retval = $this->testAction(
			'<?php echo $method['action']?>/1',
			array(
				'return'=>'result',
				'method'=>'get'
			)
		);
	}

	/**
	 * test<?php echo $method['name'] ?> method
	 *
	 * @dataProvider provideInvalidIds
	 * @expectedException NotFoundException
	 */
	public function test<?php echo $method['name']?>ThrowsExceptionWithInvalidId($id) {
		$retval = $this->testAction(
			'<?php echo $method['action']?>/'.$id,
			array(
				'return'=>'result',
				'method'=>'post'
			)
		);
	}

	/**
	 * test<?php echo $method['name'] ?> method
	 *
	 * Test that the delete method actually removes the data from the database.
	 *
	 * @dataProvider provideDeleteIds
	 */
	public function test<?php echo $method['name']?>RemovesData($id) {
		$this->testAction(
			'<?php echo $method['action']?>/'.$id,
			array(
				'return'=>'result',
				'method'=>'post'
			)
		);

		$ret = $this-><?php echo $className ?>-><?php echo $primaryModel?>->findById($id);
		$this->assertFalse($ret);
	}

<?php break;
	default:?>

	/**
	 * test<?php echo $method['name'] ?> method
	 *
	 */
	public function test<?php echo $method['name'] ?>() {
		$retval = $this->testAction(
			'<?php echo $method['action']?>',
			array('return'=>'result')
		);
		$this->markTestIncomplete("Assertions required to complete test");

	}
<?php endswitch;?>
<?php endforeach;?>
}