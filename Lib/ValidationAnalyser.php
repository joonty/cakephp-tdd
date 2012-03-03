<?php

class InvalidValidateRulesetException extends Exception {
	public function __construct($modelName,$key=null) {
		$message = "Invalid validation ruleset on model '$modelName'";
		if ($key) {
			$message .= " relating to the key '$key'";
		}
		parent::__construct($message);
	}
}

class ValidationAnalyser {
	
	protected $hasValidationRules = true;
	/**
	 * @var Model 
	 */
	protected $model;
	/**
	 * Defaults applied to validation rulsets
	 * @var array
	 */
	protected $default = array(
		'allowEmpty' => null,
		'required' => null,
		'rule' => 'blank',
		'last' => true,
		'on' => null
	);
	
	public function __construct(Model $model) {
		$this->model = $model;
		$this->parseRules();
	}
	
	/**
	 * Parse the entire validation rule set.
	 * @return void
	 * @throws InvalidValidateRulesetException 
	 */
	protected function parseRules() {
		if (!is_array($this->model->validate)) {
			throw new InvalidValidateRulesetException(get_class($this->model));
		}
		if (!count($this->model->validate)) {
			$this->hasValidationRules = false;
			return;
		}
		foreach ($this->model->validate as $fieldName => $ruleSet) {
			if (!is_array($ruleSet) || (is_array($ruleSet) && isset($ruleSet['rule']))) {
				$ruleSet = array($ruleSet);
			}
			
			$this->parseRuleSet($fieldName, $ruleSet);
		}
	}
	
	/**
	 * Parse one or more rule sets relating to a specific field.
	 * @param string $fieldName
	 * @param array $ruleSet 
	 */
	protected function parseRuleSet($fieldName,$ruleSet) {
		foreach ($ruleSet as $index => $validator) {
			
			if (!is_array($validator)) {
				$validator = array('rule' => $validator);
			}
			$validator = array_merge($this->default, $validator);
			
		}
	}
	
	/**
	 * Whether the model has validation rules at all.
	 * @return bool 
	 */
	public function hasRules() {
		return $this->hasValidationRules;
	}
	
	/**
	 * Get an example value that fits the validation rule set for a given field.
	 * @param string $field 
	 */
	public function passExample($field) {
		
	}
	
	/**
	 * Get an example value that doesn't pass the validation rule set for a field.
	 * @param string $field 
	 */
	public function failExample($field) {
		
	}
}
?>
