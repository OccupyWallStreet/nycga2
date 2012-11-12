<?php

class Ai1ec_Facebook_Friends_Sync_Exception extends Exception {
	private $error_messages = array();
	/**
	 * @return the $error_messages
	 */
	public function get_error_messages() {
		return $this->error_messages;
	}
	
	/**
	 * @param array: $error_messages
	 */
	public function set_error_messages( array $error_messages ) {
		$this->error_messages = $error_messages;
	}
}

?>