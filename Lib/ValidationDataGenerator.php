<?php


class ValidationDataGeneratorMethodNotFound extends Exception {
	public function __construct($name) {
		parent::__construct("Data cannot be generated for the validation type '$name'");
	}
}
/**
 * ValidationDataGenerator
 *
 * @author Jon Cairns <jon@joncairns.com>
 */
class ValidationDataGenerator {

	public function getData($rule) {
		if (is_string($rule)) {
			$method = '_' . strtolower($rule);
			$rule = array($rule);
		} else if (is_array($rule)) {
			$method = '_' . $rule[0];
		} else {
			throw new Exception("Invalid data type for rule");
		}

		if (method_exists($this, $method)) {
			return $this->$method($rule);
		} else {
			return false;
		}
	}
	
	public function dispatch(ValidationRule $rule) {
		$name = $rule->getName();
		$method = 'create'.ucfirst($name);
		if (method_exists($this, $method)) {
			return $this->$method($rule);
		} else {
			$rule->getField()->addWarning("Using default data for rule '".$rule->getName()."'");
			return $this->createDefault($rule);
		}
	}
	
	protected function createBlank(ValidationRule $rule) {
		return '';
	}
	
	protected function createDefault(ValidationRule $rule) {
		return join(' ',$this->lipsumWords(5));
	}

	protected function createNumeric(ValidationRule $rule) {
		return rand();
	}

	protected function createBetween(ValidationRule $rule) {
		$lower = $rule->param();
		$upper = $rule->param(1);
		if (is_null($lower)) {
			$rule->getField()->addWarning("Missing lower value for rule 'between'");
			$lower = 0;
		}
		if (is_null($upper)) {
			$rule->getField()->addWarning("Missing upper value for rule 'between'");
			$upper = 100;
		}
		if ($lower > $upper) {
			$rule->getField()->addWarning("Lower value cannot be higher than the upper value for rule 'between'");
			$lower = $upper;
		}
		return rand($lower,$upper);
	}

	protected function createRange(ValidationRule $rule) {
		$lower = $rule->param();
		$upper = $rule->param(1);
		if (is_null($lower)) {
			$lower = -1;
		}
		if (is_null($upper)) {
			$upper = getrandmax();
		}
		if ($lower > $upper) {
			$rule->getField()->addWarning("Lower value cannot be higher than the upper value for rule 'between'");
			$lower = $upper;
		}
		return rand($lower+1,$upper-1);
	}

	protected function _maxLength($rule) {
		if (!$this->ensureArray('maxLength', $rule, 2)) {
			return false;
		}
		return $this->lipsum($rule[1]);
	}

	protected function _minLength($rule) {
		if (!$this->ensureArray('minLength', $rule, 2)) {
			return false;
		}

		$string = $this->lipsum($rule[1]);
		while (strlen($string) < $rule[1]) {
			$string .= ' ' . $string;
		}
		return $string;
	}

	protected function createIp(ValidationRule $rule) {
		$param = $rule->param();
		$parts = 4;
		if ($param && strcasecmp($param, 'ipv6') == 0) {
			$parts = 6;
		}

		$ip = '';
		for ($i = 0; $i < $parts - 1; $i++) {
			$ip.=rand(0, 255) . '.';
		}

		return $ip . rand(0, 255);
	}

	protected function createEmail(ValidationRule $rule) {
		$ends = array('com', 'co.uk', 'org', 'net', 'org.uk', 'biz', 'me');
		$words = $this->lipsumWords(2);
		return $words[0] . '@' . $words[1] . '.' . $ends[array_rand($ends)];
	}

	protected function createUrl(ValidationRule $rule) {
		$protocols = array('http://','https://','http://wwww.','https://wwww.');
		$ends = array('com', 'co.uk', 'org', 'net', 'org.uk', 'biz', 'me');
		$words = $this->lipsumWords(1);
		return $protocols[array_rand($protocols)].$words[0] . '.'.$ends[array_rand($ends)];
	}
	
	protected function createBoolean(ValidationRule $rule) {
		return true;
	}

	protected function createDate(ValidationRule $rule) {
		$formats = $this->dateFormatMap();
		$format = $rule->param();
		//Convert custom cake formats to PHP date() formats
		if (!$format || !array_key_exists($format,$formats)) {
			$format = 'ymd';
		}
		return date($formats[$format]);
	}

	protected function createDatetime(ValidationRule $rule) {
		$formats = $this->dateFormatMap();
		$format = $rule->param();
		//Convert custom cake formats to PHP date() formats
		if (!$format || !array_key_exists($format,$formats)) {
			$format = 'ymd';
		}
		return date($formats[$format].' H:i');
	}
	
	protected function createTime() {
		return date('H:i');
	}
	
	protected function dateFormatMap() {
		$format = array();
		$format['dmy'] = 'd-m-Y';
		$format['mdy'] = 'm-d-Y';
		$format['ymd'] = 'Y-m-d';
		$format['dMy'] = 'd F Y';
		$format['Mdy'] = 'F d Y';
		$format['My'] = 'F Y';
		$format['my'] = 'm/Y';
		return $format;
	}
	
	protected function createAlphanumeric(ValidationRule $rule) {
		$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$string = '';
		for ($i = 0; $i < 10; $i++) {
			$string .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $string;
	}
	
	protected function createDecimal(ValidationRule $rule) {
		return (float)rand(0,100)/100;
	}
	
	protected function createExtension(ValidationRule $rule) {
		$extensions = $rule->param();
		if (!is_array($extensions)) {
			$extensions = array('gif', 'jpeg', 'png', 'jpg');
		}
		return $this->lipsumWords(1).'.'.  $extensions[array_rand($extensions)];
	}
	
	protected function createEqualTo(ValidationRule $rule) {
		return $rule->param();
	}
	
	protected function createInList(ValidationRule $rule) {
		$list = $rule->param();
		if (!is_array($list)) {
			trigger_error("Invalid rule definition for 'inList' (second parameter must be an array of options)");
		}
		return $list[array_rand($list)];
	}


	protected function lipsum($length = null) {
		$lipsum = <<<EOD
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris imperdiet, dui id malesuada tristique, nisl diam feugiat erat, a accumsan purus neque sit amet sapien. Aliquam gravida sollicitudin est, ultricies dapibus purus molestie ac. Pellentesque faucibus sem a enim accumsan posuere.
EOD;
		if (!is_null($length)) {
			return substr($lipsum, 0, $length);
		} else {
			return $lipsum;
		}
	}

	protected function lipsumWords($number) {
		$lipsum = $this->lipsum();
		$parts = explode(' ', $lipsum);
		$ret = array();
		while (count($ret) < $number) {
			$ret[] = str_replace(array('.', ','), '', $parts[array_rand($parts)]);
		}
		return $ret;
	}

}

?>
