<?php

/**
 * ValidationDataGenerator
 *
 * @author Jon Cairns <jon@joncairns.com>
 */
class ValidationDataGenerator {
	public function getData($rule) {
		if (is_string($rule)) {
			$method = '_'.strtolower($rule);
		} else if (is_array($rule)) {
			$method = '_'.$rule[0];
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
		if (!$this->ensureArray('maxLength',$rule,2)) {
			return false;
		}
		return $this->lipsum($rule[1]);
	}
	
	protected function _minLength($rule) {
		if (!$this->ensureArray('minLength',$rule,2)) {
			return false;
		}
		
		$string = $this->lipsum($rule[1]);
		while(strlen($string) < $rule[1]) {
			$string .= ' '.$string;
		}
		return $string;
	}
	
	protected function _ip($rule) {
		$parts = 4;
		if (is_array($rule)) {
			if (strcasecmp($rule[1],'ipv6')==0) {
				$parts = 6;
			}
		}
		$ip = '';
		for ($i=0; $i < $parts -1; $i++) {
			$ip.=rand(0,255).'.';
		}
		return $ip.rand(0,255);
	}
	
	protected function email() {
		$ends = array('com','co.uk','org','net','org.uk','biz','me');
		$words = $this->lipsumWords(2);
		return $words[0].'@'.$words[1].'.'.array_rand($ends);
	}
	
	protected function ensureArray($ruleName,$data,$length = null) {
		if (!is_array($data)) {
			trigger_error("Invalid rule definition for $ruleName (rule must be an array)",E_USER_WARNING);
			return false;
		}
		if (!is_null($length) && count($data) < $length) {
			trigger_error("Invalid rule definition for $ruleName (expected $length parameters)",E_USER_WARNING);
			return false;
		}
		return true;
	}
	
	protected function lipsum($length = null) {
		$lipsum = <<<EOD
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris imperdiet, dui id malesuada tristique, nisl diam feugiat erat, a accumsan purus neque sit amet sapien. Aliquam gravida sollicitudin est, ultricies dapibus purus molestie ac. Pellentesque faucibus sem a enim accumsan posuere.
EOD;
		if (!is_null($length)) {
			return substr($lipsum,0,$length);
		} else {
			return $lipsum;
		}
	}
	
	protected function lipsumWords($number) {
		$lipsum = $this->lipsum();
		$parts = explode(' ',$lipsum);
		$ret = array();
		while(count($ret) < $number) {
			$ret[] = str_replace('.','',array_rand($parts));
		}
		return $ret;
	}
}

?>
