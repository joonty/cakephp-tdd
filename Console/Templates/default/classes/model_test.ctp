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
 * @package <?php echo $package.PHP_EOL ?>
 * @subpackage Tests
 */
class <?php echo $fullClassName; ?>TestCase extends TddTestCase {
<?php if (!empty($fixtures)): ?>
	/**
	 * Fixtures
	 *
	 * @var array
	 */
	public $fixtures = array('<?php echo join("', '", $fixtures); ?>');

<?php endif; ?>

	/*
	*
	* Set up / Tear down
	*
	*/

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this-><?php echo $className . ' = ClassRegistry::init("'.$className.'");
'; ?>
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

	<?php if ($validation):?>

	/*
	*
	* Data providers
	*
	*/

	/**
	 * Return a set of data that will pass the model's validation rules.
	 *
	 * @return array
	 */
	public function provideValidData() {
		return array(
			array(array('<?php echo $className?>' => <?php var_export($validation->validData());?>)),
			array(array('<?php echo $className?>' => <?php var_export($validation->validData());?>))
		);
	}

	/**
	 * Return a set of data that will fail the model's validation rules.
	 *
	 * @return array
	 */
	public function provideInvalidData() {
		return array(
			array(array('<?php echo $className?>' => <?php var_export($validation->invalidData());?>)),
			array(array('<?php echo $className?>' => <?php var_export($validation->invalidData());?>))
		);
	}

	<?php endif;?>

	/*
	*
	* Test cases
	*
	*/

	/**
	 * Test that valid data is saved when the save method is called.
	 *
	 * @dataProvider provideValidData
	 *
	 * @param array $data
	 * @return void
	 */
	public function testValidationRulesWithValidData($data) {
		$this-><?php echo $className?>->create();
		$ret = $this-><?php echo $className?>->save($data);
		$this->assertInternalType('array',$ret);
	}

	/**
	 * Test that invalid data is not saved when the save method is called.
	 *
	 * @dataProvider provideInvalidData
	 *
	 * @param array $data
	 * @return void
	 */
	public function testValidationRulesWithInvalidData($data) {
		$this-><?php echo $className?>->create();
		$ret = $this-><?php echo $className?>->save($data);
		$this->assertFalse($ret);
	}
}
