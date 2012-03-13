<?php

App::uses('ValidationField','Tdd.Lib');
App::uses('Validation','Utility');
/**
 * Description of ValidationFieldTest
 *
 * @author jon
 */
class ValidationFieldTestCase extends CakeTestCase {

	public function testFieldName() {
		$vf = new ValidationField('email','email');
		$this->assertEquals('email',$vf->getName());
	}

	public function testFieldWithSingleRule() {
		$vf = new ValidationField('email','email');
		$rules = $vf->rules();
		$this->assertCount(1,$rules);
		$this->assertInstanceOf('ValidationRule',$rules[0]);
		$this->assertEqual("email",$rules[0]->getName());
	}

	public function provideComplexRule() {
		return array(array(
			array(
				'firstrule'=>array(
					'rule'=>'email'
				),
				'secondrule'=>array(
					'rule'=>'[a-z0-9]+'
				)
			)
		));
	}

	/**
	 * @dataProvider provideComplexRule
	 */
	public function testFieldWithSingleComplexRule($rule) {
		$vf = new ValidationField('email',$rule);
		$rules = $vf->rules();
		$this->assertCount(2,$rules);
		return $vf;
	}

	/**
	 * @dataProvider provideComplexRule
	 */
	public function testFieldWithComplexRuleHasValidFirstRule($rule) {
		$vf = new ValidationField('email',$rule);
		$rules = $vf->rules();
		$rule1 = $rules[0];
		$this->assertInstanceOf('ValidationRule',$rule1);
		$this->assertEquals('email',$rule1->getName());
		$this->assertEquals(array(),$rule1->getParameters());
	}


	/**
	 * @dataProvider provideComplexRule
	 */
	public function testFieldWithComplexRuleHasValidSecondRule($rule) {
		$vf = new ValidationField('email',$rule);
		$rules = $vf->rules();
		$rule2 = $rules[1];
		$this->assertInstanceOf('ValidationRule',$rule2);
		$this->assertEquals('[a-z0-9]+',$rule2->getName());
	}

	public function testDuplicateRulesAreIgnored() {
		$rules = array(
			'first'=>array('rule'=>'email'),
			'second'=>array('rule'=>'email')
		);
		$vf = new ValidationField('email',$rules);
		$this->assertCount(1,$vf->rules());
	}

	public function testNotEmptyIsAbsorbed() {
		$vf = new ValidationField('myfield',array('rule'=>'notempty'));
		$this->assertCount(0,$vf->rules());
		$this->assertFalse($vf->allowEmpty());
	}

	public function provideIncompatibleRuleTypes() {
		return array(
			array(array(array('rule'=>'numeric'),array('rule'=>'email'))),
			array(array(array('rule'=>'range'),array('rule'=>'url'))),
			array(array(array('rule'=>'ip'),array('rule'=>'decimal'))),

		);
	}

	/**
	 * @dataProvider provideIncompatibleRuleTypes
	 */
	public function testIncompatibleRuleTypesAddWarning($rules) {
		$vf = new ValidationField('myfield',$rules);
		$warnings = $vf->getWarnings();
		$this->assertCount(1,$warnings);
		$this->assertStringStartsWith("Ignoring validation rule", $warnings[0]);
	}

	public function testExclusiveRuleTypesAddWarningWithOtherRules() {
		$vf = new ValidationField('myfield',array(array('rule'=>array('equalTo',1)),array('rule'=>'url')));
		$warnings = $vf->getWarnings();
		$this->assertCount(1,$warnings);
		$this->assertStringStartsWith("A rule type exists", $warnings[0]);
	}

	public function testInvalidMaxLengthRuleAddsWarning() {
		$vf = new ValidationField('myfield',array('rule'=>array('maxLength')));
		$warnings = $vf->getWarnings();
		$this->assertCount(1,$warnings);
		$this->assertStringStartsWith("Missing parameter for max", $warnings[0]);
	}

	public function testInvalidMinLengthRuleAddsWarning() {
		$vf = new ValidationField('myfield',array('rule'=>array('minLength')));
		$warnings = $vf->getWarnings();
		$this->assertCount(1,$warnings);
		$this->assertStringStartsWith("Missing parameter for min", $warnings[0]);
	}

	public function testInvalidMaxAndMinLengthRuleCombinationAddsWarning() {
		$vf = new ValidationField('myfield',array(array('rule'=>array('maxLength',10)),array('rule'=>array('minLength',20))));
		$warnings = $vf->getWarnings();
		$this->assertCount(1,$warnings);
		$this->assertStringStartsWith("Min length", $warnings[0]);
	}

	public function testGetDataWithMaxLength() {
		$vf = new ValidationField('myfield',array('rule'=>array('maxLength',20)));
		$data = $vf->getData();
		$this->assertLessThanOrEqual(20,strlen($data),"String should be less than 20 characters");
	}

	public function testGetDataWithMinLength() {
		$vf = new ValidationField('myfield',array('rule'=>array('minLength',100)));
		$data = $vf->getData();
		$this->assertGreaterThanOrEqual(100,strlen($data),"String should be less than 100 characters");
	}

	public function testGetInvalidDataWithEmail() {
		$vf = new ValidationField('myfield',array('rule'=>'email'));
		$data = $vf->getInvalidData();
		$this->assertFalse(Validation::email($data),"Data should not match the email validation");
	}

	public function testGetInvalidDataWithUrl() {
		$vf = new ValidationField('myfield',array('rule'=>'url'));
		$data = $vf->getInvalidData();
		$this->assertFalse(Validation::url($data),"Data should not match the url validation");
	}

	public function testGetInvalidDataWithNumeric() {
		$vf = new ValidationField('myfield',array('rule'=>'numeric'));
		$data = $vf->getInvalidData();
		$this->assertFalse(Validation::numeric($data),"Data should not match the numeric validation");
	}

	public function testGetInvalidDataWithEqualTo() {
		$vf = new ValidationField('myfield',array('rule'=>array('equalTo',3)));
		$data = $vf->getInvalidData();
		$this->assertNotEqual(3,$data,"Data should not match the passed value");
	}

	public function testGetInvalidDataWithUnkownRuleSetsWarning() {
		$vf = new ValidationField('myfield',array('rule'=>'cc'));
		$data = $vf->getInvalidData();
		$warnings = $vf->getWarnings();
		//Two warnings - one for the unknown rule 'cc' and one for the
		// possible failure of creating invalid data
		$this->assertCount(2,$warnings);
		$this->assertStringStartsWith("The attempt to create", $warnings[1]);
	}
}
?>