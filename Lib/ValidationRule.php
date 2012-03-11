<?php

/**
 * Description of ValidationRule
 *
 * @author jon
 */
class ValidationRule {
	protected $name;
	protected $parameters;
	
	public function __construct($ruleName, $parameters = array()) {
		$this->name = $ruleName;
		$this->parameters = $parameters;
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
