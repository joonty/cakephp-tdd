<?php

/**
 * Description of MockSession
 *
 * @author Jon Cairns <jon@ggapps.co.uk>
 */
class ArraySession implements CakeSessionHandlerInterface {
	protected static $_data = array();

	/**
	 * Method called on open of a database session.
	 *
	 * @return boolean Success
	 */
	public function open() {
		return true;
	}

	/**
	 * Method called on close of a database session.
	 *
	 * @return boolean Success
	 */
	public function close() {
		self::$_data = array();
		return true;
	}

	/**
	 * Method used to read from a database session.
	 *
	 * @param mixed $id The key of the value to read
	 * @return mixed The value of the key or false if it does not exist
	 */
	public function read($id) {
		if (isset(self::$_data[$id])) {
			return self::$_data[$id];
		} else {
			return false;
		}
	}

	/**
	 * Helper function called on write for database sessions.
	 *
	 * @param integer $id ID that uniquely identifies session in database
	 * @param mixed $data The value of the data to be saved.
	 * @return boolean True for successful write, false otherwise.
	 */
	public function write($id, $data) {
		 self::$_data[$id] = $data;
		 return true;
	}

	/**
	 * Method called on the destruction of a database session.
	 *
	 * @param integer $id ID that uniquely identifies session in database
	 * @return boolean True for successful delete, false otherwise.
	 */
	public function destroy($id) {
		unset(self::$_data[$id]);
		return true;
	}

	/**
	 * Helper function called on gc for database sessions.
	 *
	 * @param integer $expires Timestamp (defaults to current time)
	 * @return void
	 */
	public function gc($expires = null) {
		return null;
	}

	/**
	 * Closes the session before the objects handling it become unavailable
	 *
	 * @return void
	 */
	public function __destruct() {
		self::$_data = array();
	}
	
	protected static function _startSession() {
		return true;
	}
	
	public static function renew() {
		self::$_data = array();
	}
	
	protected static function _validAgentAndTime() {
		return true;
	}
}

?>
