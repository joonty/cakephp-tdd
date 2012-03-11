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
	protected $warnings = array();

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
	
	/**
	 * Create a new ValidationField object.
	 * 
	 * @param string $fieldName Name of the field requiring validations
	 * @param mixed $ruleSet Array or string of validation rules
	 */
	public function __construct($fieldName,$ruleSet) {
		$this->fieldName = $fieldName;
		
		$this->parseRuleSet($ruleSet);
	}
	
	/**
	 * Add a warning message relating to this field.
	 * 
	 * @param string $message 
	 */
	public function addWarning($message) {
		$this->warnings[] = $message;
	}
	
	/**
	 * Get any warnings created during data generation or rule parsing.
	 * 
	 * @return array
	 */
	public function getWarnings() {
		return $this->warnings;
	}
	
	/**
	 * Get the field name.
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->fieldName;
	}
	
	/**
	 * Get a list of ValidationRules for this field.
	 * 
	 * @return array<ValidationRule>
	 */
	public function rules() {
		return $this->rules;
	}
	
	/**
	 * Whether this field can be left empty.
	 * 
	 * @return boolean
	 */
	public function allowEmpty() {
		return $this->allowEmpty;
	}
	
	/**
	 * Parse the set of validation rules associated with this field.
	 * 
	 * @param mixed $ruleSet
	 */
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
	
	/**
	 * Add a validation rule to the current list for this field.
	 * 
	 * A new rule creates a {@link ValidationRule ValidationRule} object. Certain
	 * rules are special case, such as notempty, minlength and maxlength. These
	 * are not added to the list of rules, but are used elsewhere.
	 * 
	 * Also, some rules don't make sense with others. For instance, numeric
	 * doesn't make sense with email. A warning is thrown if conflicting rules
	 * are found.
	 * 
	 * @param mixed $rule Array or string to represent validation rule
	 */
	protected function addRule($rule){
		if (is_array($rule)) {
			$ruleName = array_shift($rule);
			$params = $rule;
		} else {
			$ruleName = $rule;
			$params = array();
		}
		
		switch (strtolower($ruleName)) {
			case 'notempty':
				$this->allowEmpty = false;
				return;
				break;
		}
		
		$found = false;
		foreach ($this->rules as $r) {
			if ($r->getName() == $ruleName) {
				$found = true;
			}
		}
		if (!$found) {
			$this->rules[] = new ValidationRule($this,$ruleName,$params);
		}
	}
}

?>