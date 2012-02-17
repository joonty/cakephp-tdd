<?php

/**
 * Contains the ArrayCache class.
 *
 * @package Local TV
 * @author Jon Cairns <jon.cairns@22blue.co.uk>
 * @copyright Copyright (c) 22 Blue 2012
 */
App::uses('CacheEngine','Cache/Engine');
/**
 * ArrayCache description
 *
 */
class ArrayCacheEngine extends CacheEngine {
	private $data = array();

	public  function write($key, $value, $duration) {
		$this->data[$key] = array('v'=>$value,'e'=>time()+$duration);
	}

	public  function read($key) {
		if (!isset($this->data[$key])) {
			return false;
		}
		$v = $this->data[$key];
		if (time() > $v['e']) {
			return false;
		}
		return $v['v'];
	}

	public  function delete($key) {
		if (isset($this->data[$key])) {
			unset($this->data[$key]);
			return true;
		}
		return false;
	}

	public  function clear($check = false) {
		if ($check) {
			foreach ($this->data as $k=>$v) {
				if (time() > $v['e']) {
					unset($this->data[$k]);
				}
			}
		} else {
			$this->data = array();
		}
		return true;
	}

	public  function decrement($key,$offset = 1) {
		if (isset($this->data[$key]) && is_numeric($this->data[$key]['v'])) {
			$this->data[$key]['v'] -= $offset;
			return true;
		}
		return false;
	}

	public  function increment($key,$offset = 1) {
		if (isset($this->data[$key]) && is_numeric($this->data[$key]['v'])) {
			$this->data[$key]['v'] += $offset;
			return true;
		}
		return false;
	}
}

?>
