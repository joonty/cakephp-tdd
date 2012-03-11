<?php

App::uses('ValidationField','Tdd.Lib');
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
					'rule'=>array('minLength',5)
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
		$this->assertEquals('minLength',$rule2->getName());
		$this->assertEquals(array(5),$rule2->getParameters());
	}
	
	public function testDuplicateRulesAreIgnored() {
		$rules = array(
			'first'=>array('rule'=>'email'),
			'second'=>array('rule'=>'email')
		);
		$vf = new ValidationField('email',$rules);
		$this->assertCount(1,$vf->rules());
	}
}

?>
