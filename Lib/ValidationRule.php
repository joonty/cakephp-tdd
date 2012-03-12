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
			'ip'
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
	
	/**
	 * Create a new ValidationRule object.
	 * 
	 * This contains information for a single validation rule on a field.
	 * 
	 * @param ValidationField $field The field that this rule belongs to
	 * @param string $ruleName 
	 * @param array $parameters OPTIONAL Any parameters that this rule takes
	 */
	public function __construct(ValidationField $field, $ruleName, $parameters = array()) {
		$this->field = $field;
		$this->name = strtolower($ruleName);
		$this->parameters = $parameters;
		
		$this->determineType();
	}
	
	/**
	 * Get the ValidationField for this rule.
	 * 
	 * @return ValidationField
	 */
	public function getField() {
		return $this->field;
	}
	
	/**
	 * Get the rule name.
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Get the rule type.
	 * 
	 * @return string One of the class TYPE constants.
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * Get any parameters passed to the rule.
	 * 
	 * @return array Numerically indexed array
	 */
	public function getParameters() {
		return $this->parameters;
	}
	
	/**
	 * Get a single parameter by numerical index.
	 * 
	 * @param integer $offset
	 * @return mixed|null Parameter value, or null if non-existent 
	 */
	public function param($offset = 0) {
		if (array_key_exists($offset,$this->parameters)) {
			return $this->parameters[$offset];
		} else {
			return null;
		}
	}
	
	/**
	 * Determine the type of rule.
	 * 
	 * The type is defined by one of the class TYPE constants, and also by
	 * {@link ValidationRule::$ruletypes $ruletypes}.
	 * 
	 * This can be used to detect incompatible rule sets. 
	 */
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
	
	public function __toString() {
		return "validation rule \"{$this->name}\"";
	}
}

?>
