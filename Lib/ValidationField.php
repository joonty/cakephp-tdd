<?php

App::uses('ValidationRule','Tdd.Lib');

/**
 * Contains a set of rules for a given model field.
 *
 * @author jon
 */
class ValidationField {
	protected $fieldName;
	protected $rules = array();
	protected $allowEmpty = true;

	/**
	 * Defaults applied to validation rulsets
	 * @var array
	 */
	protected $defaults = array(
		'allowEmpty' => true,
		'required' => false,
		'last' => true,
		'on' => null
	);
	
	public function __construct($fieldName,$ruleSet) {
		$this->fieldName = $fieldName;
		
		$this->parseRuleSet($ruleSet);
	}
	
	public function getName() {
		return $this->fieldName;
	}
	
	public function rules() {
		return $this->rules;
	}
	
	public function allowEmpty() {
		return $this->allowEmpty;
	}
	
	protected function parseRuleSet($ruleSet) {
		if (!is_array($ruleSet)) {
			$ruleSet = array(array('rule'=>$ruleSet));
		} else if ((is_array($ruleSet) && isset($ruleSet['rule']))) {
			$ruleSet = array($ruleSet);
		}
		foreach ($ruleSet as $validator) {
			
			$validator = array_merge($this->defaults, $validator);
			if ($validator['allowEmpty'] == false) {
				$this->allowEmpty = false;
			}
			if (isset($validator['rule'])) {
				$this->addRule($validator['rule']);
			}
		}
	}
	
	protected function addRule($rule){
		if (is_array($rule)) {
			$ruleName = array_shift($rule);
			$params = $rule;
		} else {
			$ruleName = $rule;
			$params = array();
		}
		$found = false;
		foreach ($this->rules as $r) {
			if ($r->getName() == $ruleName) {
				$found = true;
			}
		}
		if (!$found) {
			$this->rules[] = new ValidationRule($ruleName,$params);
		}
	}
}

?>
