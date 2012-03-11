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
	protected $type;
	
	const TYPE_STRING = 'string';
	const TYPE_NUMBER = 'number';
	const TYPE_EXCLUSIVE = 'exclusive';
	
	protected static $ruletypes = array(
		'string'=>array(
			'email',
			'url',
			'default',
			'date',
			'datetime',
			'time',
			'ip',
		),
		'number'=>array(
			'decimal',
			'numeric',
			'range',
			'between'
		),
		'exclusive'=>array(
			'blank',
			'equalto'
		)
	);
	
	public function __construct(ValidationField $field, $ruleName, $parameters = array()) {
		$this->field = $field;
		$this->name = strtolower($ruleName);
		$this->parameters = $parameters;
		
		$this->determineType();
	}
	
	public function getField() {
		return $this->field;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getType() {
		return $this->type;
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
	
	protected function determineType() {
		$foundType = null;
		foreach (self::$ruletypes as $type=>$rules) {
			if (in_array($this->name,$rules)) {
				$foundType = $type;
				break;
			}
		}
		$this->type = $foundType;
	}
}

?>
