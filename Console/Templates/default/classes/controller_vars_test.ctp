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
?>
/**
 * Contains a test case for <?php echo $fullClassName?>.
 *
 * @copyright Copyright (c) 22 Blue 2012
 */
App::uses('<?php echo $fullClassName; ?>', '<?php echo $realType; ?>');

/**
 * <?php echo $fullClassName; ?> Test Case
 *
 * @package <?php echo $package.PHP_EOL ?>
 * @subpackage Tests
 */
class <?php echo $fullClassName; ?>VarsTestCase extends TddControllerTestCase {
<?php if (!empty($fixtures)): ?>
	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array('<?php echo join("', '", $fixtures); ?>');

	/*
	*
	* Set up / Tear down
	*
	*/

<?php endif; ?>
	/**
	 * Create the mock controller to be used by all tests.
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

		//Always allow ACL
		case 'Acl':
			echo <<<EOD
				'Acl' => array('check'),

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

<?php if (in_array('Acl',$components)): ?>
		$this-><?php echo $className ?>->Acl
			->expects($this->any())
			->method('check')
			->will($this->returnValue(true));
<?php endif; ?>
	}

	/**
	 * Unset the mock controller and call the parent tearDown method.
	 */
	public function tearDown() {
		unset($this-><?php echo $className;?>);

		parent::tearDown();
	}

	/*
	*
	* Test cases
	*
	*/

<?php foreach ($methods as $method): ?>
<?php switch ($method['type']):?>
<?php case 'index':?>

	/**
	 * Check that the index action sends the correct set of variables to the view.
	 */
	public function test<?php echo Inflector::classify($method['name']); ?>() {
		$vars = $this->testAction(
			'<?php echo $method['action']?>', array('return' => 'vars')
		);
		$this->assertArrayHasKey('<?php echo strtolower($className)?>', $vars);
		$this->assertCount(10,$vars['<?php echo strtolower($className)?>']);
		$this->assertArrayHasKey('<?php echo $primaryModel?>', $vars['<?php echo strtolower($className)?>'][0]);
	}

<?php break;
	case 'view':?>

	/**
	 * Check that the view action sends the correct set of variables to the view.
	 */
	public function test<?php echo Inflector::classify($method['name']); ?>() {
		$vars = $this->testAction(
			'<?php echo $method['action']?>/1',
			array(
				'return' => 'vars',
				'method' => 'get'
			)
		);
		$this->assertArrayHasKey('<?php echo strtolower($primaryModel)?>', $vars);
		$this->assertArrayHasKey('<?php echo $primaryModel?>', $vars['<?php echo strtolower($primaryModel)?>']);
	}
<?php break;
	case 'edit':?>

	/**
	 * Check that the edit action sends the correct set of variables to the view.
	 *
	 * @todo Make some assertions to complete the testyy
	 */
	public function test<?php echo Inflector::classify($method['name']); ?>() {
		$vars = $this->testAction(
			'<?php echo $method['action']?>/1',
			array(
				'return' => 'vars',
				'method' => 'get'
			)
		);
		$this->markTestIncomplete("Assertions required to complete test");
	}
<?php break;
	case 'add':?>

	/**
	 * Check that the add action sends the correct set of variables to the view.
	 *
	 * @todo Make some assertions to complete the testyy
	 */
	public function test<?php echo Inflector::classify($method['name']); ?>() {
		$vars = $this->testAction(
			'<?php echo $method['action']?>',
			array(
				'return' => 'vars',
				'method' => 'get'
			)
		);
		$this->markTestIncomplete("Assertions required to complete test");
	}
<?php break;
	case 'delete':
	break;
	default:?>

	/**
	 * Check that the action sends the correct set of variables to the view.
	 *
	 * @todo Make some assertions to complete the testyy
	 */
	public function test<?php echo Inflector::classify($method['name']); ?>() {
		$vars = $this->testAction(
			'<?php echo $method['action']?>',
			array('return'=>'vars')
		);
		$this->markTestIncomplete("Assertions required to complete test");
	}

<?php endswitch;?>
<?php endforeach;?>
}
