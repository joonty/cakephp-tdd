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
		case 'Auth':
			echo <<<EOD
				'Auth' => array('isAuthorized'),

EOD;
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
	 * Return some examples of invalid IDs.
	 */
	public function provideInvalidIds() {
		return array(
			array(''),
			array(12345),
			array('notanid')
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
<?php break;
        case "delete":?>
        /**
         * test<?php echo $method['name'] ?> method
         *
         * @dataProvider provideInvalidIds
         * @expectedException MethodNotAllowedException
         */
        public function test<?php echo $method['name']?>ThrowsExceptionWithInvalidMethod($id) {
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
         * @dataProvider provideInvalidIds
         * @expectedException NotFoundException
         */
        public function test<?php echo $method['name']?>ThrowsExceptionWithInvalidId($id) {
                $retval = $this->testAction(
                        '<?php echo $method['action']?>/'.$id,
                        array(
				'return'=>'result',
				'method'=>'get'
			)
                );
        }
	
	/**
	 * test<?php echo $method['name'] ?> method
	 *
	 * Test that the delete method actually removes the data from the database.
	 *
	 * @dataProvider provideIds
	 */
	public function test<?php echo $method['name']?>RemovesData($id) {
                $this->testAction(
                        '<?php echo $method['action']?>/'.$id,
                        array(
				'return'=>'result',
				'method'=>'get'
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

        }
<?php endswitch;?>
<?php endforeach;?>
}
