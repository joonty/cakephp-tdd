<?php

/**
 * Description of ValidationRule
 *
 * @author jon
 */
class ValidationRule {
	protected $name;
	protected $parameters;
	protected $field;
	
	public function __construct(ValidationField $field, $ruleName, $parameters = array()) {
		$this->field = $field;
		$this->name = $ruleName;
		$this->parameters = $parameters;
	}
	
	public function getField() {
		return $this->field;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getParameters() {
		return $this->parameters;
	}
	
	public function param($offset = 0) {
		if (array_key_exists($offset,$this->parameters)) {
			return $this->parameters[$offset];
		} else {
			return null;
		}
	}
}

?>
