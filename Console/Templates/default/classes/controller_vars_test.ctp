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
class <?php echo $fullClassName; ?>VarsTestCase extends TddControllerTestCase {
<?php if (!empty($fixtures)): ?>
	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array('<?php echo join("', '", $fixtures); ?>');

<?php endif; ?>
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

<?php foreach ($methods as $method): ?>
<?php switch ($method['type']):?>
<?php case 'index':?>

	/**
	 * test<?php echo Inflector::classify($method['name']) ?> method
	 *
	 * @return void
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
	 * test<?php echo Inflector::classify($method['name']); ?> method
	 *
	 * @return void
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
	 * test<?php echo Inflector::classify($method['name']); ?> method
	 *
	 * @return void
	 */
	public function test<?php echo Inflector::classify($method['name']); ?>() {
		$vars = $this->testAction(
			'<?php echo $method['action']?>/1',
			array(
				'return' => 'vars',
				'method' => 'get'
			)
		);
	}
<?php break;
	case 'add':?>

	/**
	 * test<?php echo Inflector::classify($method['name']); ?> method
	 *
	 * @return void
	 */
	public function test<?php echo Inflector::classify($method['name']); ?>() {
		$vars = $this->testAction(
			'<?php echo $method['action']?>',
			array(
				'return' => 'vars',
				'method' => 'get'
			)
		);
	}
<?php break;
	case 'delete':
	break;
	default:?>

	/**
	 * test<?php echo Inflector::classify($method['name']); ?> method
	 *
	 * @return void
	 */
	public function test<?php echo Inflector::classify($method['name']); ?>() {
		$vars = $this->testAction(
			'<?php echo $method['action']?>',
			array('return'=>'vars')
		);
	}

<?php endswitch;?>
<?php endforeach;?>
}
