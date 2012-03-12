<?php
App::uses('ValidationField','Tdd.Lib');

class InvalidValidateRulesetException extends Exception {
	public function __construct($modelName,$key=null) {
		$message = "Invalid validation ruleset on model '$modelName'";
		if ($key) {
			$message .= " relating to the key '$key'";
		}
		parent::__construct($message);
	}
}

class InvalidFieldNameException extends Exception {
	public function __construct($modelName,$key) {
		$message = "No key (or no validation on key) \"$key\" for model \"$modelName\"";
		parent::__construct($message);
	}
}


/**
 * @author Jon Cairns <jon@joncairns.com> 
 */
class ValidationAnalyser {
	
	protected $hasValidationRules = true;
	protected $allowNullValues = false;
	/**
	 * @var Model 
	 */
	protected $model;
	protected $fields = array();
	
	
	/**
	 * Create a new ValidationAnalyser object, bound to a given model.
	 * 
	 * @param Model $model 
	 */
	public function __construct(Model $model) {
		$this->model = $model;
		$this->parseRules();
	}
	
	/**
	 * Get all validation warnings as a formatted string.
	 * 
	 * @return string 
	 */
	public function getWarningsAsString() {
		$warningString = "The following warnings occurred when parsing the validation rules on the ".$this->model->name." model:".PHP_EOL.PHP_EOL;
		foreach ($this->fields as $fieldName=>$field) {
			$warnings = $field->getWarnings();
			if (count($warnings)) { 
				$warningString .= "Field '$fieldName'";
				$warningString .= PHP_EOL."\t- ".implode(PHP_EOL."\t- ",$warnings).PHP_EOL.PHP_EOL;
			}
		}
		return $warningString;
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
			$this->fields[$fieldName] = new ValidationField($fieldName,$ruleSet);
		}
	}
	
	/**
	 * Whether the model has validation rules at all.
	 * @return boolean
	 */
	public function hasRules() {
		return $this->hasValidationRules;
	}
	
	/**
	 * Get an example value that fits the validation rule set for a given field.
	 * 
	 * @throws InvalidFieldNameException
	 * 
	 * @param string $field 
	 * @return mixed Example datas
	 */
	public function validField($field) {
		if (!array_key_exists($field,$this->fields)) {
			throw new InvalidFieldNameException($this->model->name,$field);
		}
		$validationField = $this->fields[$field];
		return $validationField->getData();
	}
	
	public function validData() {
		$ret = array();
		foreach ($this->fields as $fieldName=>$field) {
			$ret[$fieldName] = $field->getData();
		}
		return $ret;
	}
	
	/**
	 * Get an example value that doesn't pass the validation rule set for a field.
	 * 
	 * @throws InvalidFieldNameException
	 * 
	 * @param string $field 
	 * @return mixed Example datas
	 */
	public function invalidField($field) {
		
	}
}
?>
