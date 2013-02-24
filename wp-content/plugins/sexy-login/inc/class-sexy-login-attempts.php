<?php
class Sexy_Login_Attempts {
	
	private $ip;
	
	public function __construct() {
		
		$this->ip	= $this->get_ip();
		
	}
	
	private function get_ip() {

		$ip = $_SERVER['REMOTE_ADDR'];
		$ip = ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $ip;
		$ip = ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) ? $_SERVER['HTTP_CLIENT_IP'] : $ip;
		
		return $ip;
		
	}
	
	public function get_attempts() {
		
		global $wpdb;
		
		$this->delete_attempts( true );
		
		$result = $wpdb->get_var( $wpdb->prepare( 'SELECT login_attempts FROM ' . SL_LOGIN_TABLE . ' WHERE ip = %s', array( $this->ip ) ) );
		 
		$wpdb->flush();
		
		if ( ! empty( $result ) )
			return $result;
		else
			return false;
			
	}
	
	public function update_attempts() {
	
		global $wpdb;
		
		$attempts	= $this->get_attempts();
		
		if ( ! $attempts )
			$wpdb->insert( SL_LOGIN_TABLE, array( 'ip' => $this->ip ), array( '%s' ) );
		else
			$wpdb->update( SL_LOGIN_TABLE, array( 'login_attempts' => $attempts + 1 ), array( 'ip' => $this->ip ), array( '%d' ), array( '%s' ) );
		
		return $attempts + 1;
		
	}
	
	public function delete_attempts( $all = false ) {

		global $wpdb;
		
		if ( $all ) {
			$query = 'DELETE FROM ' . SL_LOGIN_TABLE . ' WHERE DATE_ADD( last_date , INTERVAL %d MINUTE ) < NOW()';
			$format = array( SL_LOGIN_ATTEMPTS_LAPSE );
		} else {
			$query	= 'DELETE FROM ' . SL_LOGIN_TABLE . ' WHERE ip = %s';
			$format	= $this->ip;
		}
		
		$wpdb->query( $wpdb->prepare( $query, $format ) );
		
	}

}
?>