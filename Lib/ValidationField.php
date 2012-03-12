<?php

App::uses('ValidationRule','Tdd.Lib');
App::uses('ValidationDataGenerator','Tdd.Lib');


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
	protected $ruleType;
	
	/**
	 * Keep maximum length rule to one side. Only used for string based rules.
	 * @var ValidationRule
	 */
	protected $maxLength;
	/**
	 * Keep minimum length rule to one side. Only used for string based rules.
	 * @var ValidationRule
	 */
	protected $minLength;

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
	 * Get the rule for maxLength, if it exists.
	 * @return ValidationRule
	 */
	public function getMaxLengthRule() {
		return $this->maxLength;
	}
	
	/**
	 * Get the rule for minLength, if it exists.
	 * @return ValidationRule
	 */
	public function getMinLengthRule() {
		return $this->minLength;
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
	 * Create example data for this field.
	 * 
	 * Priority is given to validation rules that are more strict, such as 
	 * "email" and "numeric".
	 * 
	 * @return mixed
	 */
	public function getData() {
		if (count($this->rules) == 0) {
			$this->rules[] = new ValidationRule($this,'default');
		}
		
		$generator = new ValidationDataGenerator();
		$data = null;
		foreach ($this->rules as $rule) {
			$data = $generator->dispatch($rule);
			if ($rule->getType()) {
				break;
			}
		}
		return $data;
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
	 * Run a basic sanity check on a validation rule against the current set of rules.
	 * 
	 * This checks for incompatible types, such as number and string based
	 * rules on the same field.
	 * 
	 * A warning is set if there are incompatible rules, and the first rule
	 * is picked over later incompatible rules.
	 * 
	 * @param ValidationRule $rule Rules to check 
	 */
	protected function sanityCheckRule(ValidationRule $rule) {
		
		if (isset($this->type) && $this->type == ValidationRule::TYPE_EXCLUSIVE) {
			$this->addWarning("A rule type exists that is incompatible with any other, cannot add $rule");
			return false;
		}
		
		if (!$this->checkRuleName($rule)) {
			return false;
		}
		$name = $rule->getName();
		
		$exists = $this->getRuleByName($rule->getName()) != null;
		if ($exists) {
			$this->addWarning("Ignoring duplicate $rule");
			return false;
		}
		
		$type = $rule->getType();
		if ($type) {
			if (isset($this->type)) {
				$this->addWarning("Ignoring $rule, as this is incompatible with previous rules on this field");
				return false;
			} else {
				$this->type = $type;
			}
		}
		return true;
	}
	
	/**
	 * Filter on the rule name, to determine whether it should be added to the list.
	 * 
	 * The notempty rule is absorbed into the $allowEmpty property. Minlength and
	 * maxlength rules are kept aside and used after all other rules have been
	 * applied, and if they make sense.
	 * 
	 * @param ValidationRule $rule
	 * @return boolean 
	 */
	protected function checkRuleName(ValidationRule $rule) {
		$name = $rule->getName();
				
		switch ($name) {
			case 'notempty':
				$this->allowEmpty = false;
				return false;
				break;
			case 'minlength':
				if ($rule->param() != null) {
					if ($this->maxLength) {
						if ($this->maxLength->param() < $rule->param()) {
							$this->addWarning("Min length cannot be greater than the max length");
						} else {
							$this->minLength = $rule;
						}
					} else {
						$this->minLength = $rule;
					}
				} else {
					$this->addWarning("Missing parameter for min length rule");
				}
				return false;
				break;
			case 'maxlength':
				if ($rule->param() != null) {
					if ($this->minLength) {
						if ($this->minLength->param() > $rule->param()) {
							$this->addWarning("Max length cannot be less than the minimum length");
						} else {
							$this->maxLength = $rule;
						}
					} else {
						$this->maxLength = $rule;
					}
				} else {
					$this->addWarning("Missing parameter for max length rule");
				}
				return false;
				break;
			default:
				return true;
		}
	}
	
	/**
	 * Get a validation rule from the current list by name.
	 * 
	 * @param string $name 
	 * @return ValidationRule|null
	 */
	protected function getRuleByName($name) {
		$return = null;
		foreach ($this->rules as $rule) {
			if ($rule->getName() == $name) {
				$return = $rule;
				break;
			}
		}
		return $return;
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

		
		$validationRule = new ValidationRule($this,$ruleName,$params);
		
		if ($this->sanityCheckRule($validationRule)) {
			$this->rules[] = $validationRule;
		}		
	}
}

?>