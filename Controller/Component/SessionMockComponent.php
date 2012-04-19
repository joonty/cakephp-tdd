<?php
/**
 * Contains the SessionMockComponent class.
 *
 * @package Tdd
 * @author Jon Cairns <jon.cairns@22blue.co.uk>
 * @copyright Copyright (c) 22 Blue 2012
 */
App::uses('SessionComponent','Controller/Component');
/**
 * SessionMockComponent
 *
 */
class SessionMockComponent extends SessionComponent {
	protected $id;
	protected $data = array();
	protected $userAgent = '';

	public function userAgent($userAgent = null) {
		if ($userAgent) {
			$this->userAgent = $userAgent;
		}
		return $this->userAgent;
	}

	public function write($name, $value = null) {
		$write = $name;
		if (!is_array($name)) {
			$write = array($name => $value);
		}
		foreach ($write as $key => $val) {
			self::_overwrite($this->data, Set::insert($this->data, $key, $val));
			if (Set::classicExtract($this->data, $key) !== $val) {
				return false;
			}
		}
		return true;
	}

	protected static function _overwrite(&$old, $new) {
		if (!empty($old)) {
			foreach ($old as $key => $var) {
				if (!isset($new[$key])) {
					unset($old[$key]);
				}
			}
		}
		foreach ($new as $key => $var) {
			$old[$key] = $var;
		}
	}

	public function read($name = null) {
		return Set::classicExtract($this->data, $name);
	}

	public function delete($name) {
		if ($this->check($name)) {
			Set::remove($this->data,$name);
		}
		return true;
	}

	public function check($name) {
		$result = Set::classicExtract($this->data, $name);
		return isset($result);
	}

	public function error() {
		return false;
	}

	public function setFlash($message, $element = 'default', $params = array(), $key = 'flash') {
		$this->write('Message.' . $key, compact('message', 'element', 'params'));
		return true;
	}

	public function renew() {
		return true;
	}

	public function valid() {
		return true;
	}

	public function destroy() {
		$this->data = array();
		return true;
	}

	public function id($id = null) {
		if ($id) {
			$this->id = $id;
		}
		if (!isset($this->id)) {
			$this->id = uniqid();
		}
		return $this->id;
	}

	public function started() {
		return true;
	}
}
?>
