<?php

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

	protected function _numeric($rule) {
		return rand();
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

	protected function _ip($rule) {
		$parts = 4;
		if (count($rule) > 1) {
			if (strcasecmp($rule[1], 'ipv6') == 0) {
				$parts = 6;
			}
		}
		$ip = '';
		for ($i = 0; $i < $parts - 1; $i++) {
			$ip.=rand(0, 255) . '.';
		}
		return $ip . rand(0, 255);
	}

	protected function _email() {
		$ends = array('com', 'co.uk', 'org', 'net', 'org.uk', 'biz', 'me');
		$words = $this->lipsumWords(2);
		return $words[0] . '@' . $words[1] . '.' . $ends[array_rand($ends)];
	}
	
	protected function _boolean() {
		return true;
	}

	protected function _date($rule) {
		$formats = $this->dateFormatMap();
		//Convert custom cake formats to PHP date() formats
		if (!isset($rule[1]) || !array_key_exists($rule[1],$formats)) {
			$rule[1] = 'ymd';
		}
		return date($formats[$rule[1]]);
	}

	protected function _datetime($rule) {
		$formats = $this->dateFormatMap();
		//Convert custom cake formats to PHP date() formats
		if (!isset($rule[1]) || !array_key_exists($rule[1],$formats)) {
			$rule[1] = 'ymd';
		}
		return date($formats[$rule[1]].' H:i');
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
	
	protected function _alphanumeric() {
		$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$string = '';
		for ($i = 0; $i < 10; $i++) {
			$string .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $string;
	}
	
	protected function _decimal() {
		return (float)rand(0,100)/100;
	}
	
	protected function _extension($rule) {
		if (count($rule) < 2 || !is_array($rule[1])) {
			$extensions = array('gif', 'jpeg', 'png', 'jpg');
		} else {
			$extensions = $rule[1];
		}
		return $this->lipsumWords(1).'.'.  $extensions[array_rand($extensions)];
	}
	
	protected function _equalTo($rule) {
		if (!$this->ensureArray('equalTo',$rule,2)) {
			return false;
		}
		return $rule[1];
	}
	
	protected function _inList($rule) {
		if (!$this->ensureArray('inList',$rule,2)) {
			return false;
		}
		if (!is_array($rule[1])) {
			trigger_error("Invalid rule definition for 'inList' (second parameter must be an array of options)");
		}
		return $rule[1][array_rand($rule[1])];
	}

	protected function ensureArray($ruleName, $data, $length = null) {
		if (!is_array($data)) {
			trigger_error("Invalid rule definition for $ruleName (rule must be an array)", E_USER_WARNING);
			return false;
		}
		if (!is_null($length) && count($data) < $length) {
			trigger_error("Invalid rule definition for $ruleName (expected $length parameters)", E_USER_WARNING);
			return false;
		}
		return true;
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
