<?php
/**
 * This class represent the currently Facebook logged on user.
 *
 * @author The Seed Network
 *
 *
 */
class Ai1ec_Facebook_Current_User {
	/**
	 * 
	 * @var bigint the facebook id
	 */
	private $_id;
	/**
	 * 
	 * @var int
	 */
	private $_timezone;
	/**
	 * 
	 * @var string The user name
	 */
	private $_name;
	/**
	 * 
	 * @var string The username
	 */
	private $_username;
	/**
	 * 
	 * @var Facebook an instance of the Facebook Class
	 */
	private $_facebook;
	/**
	 * 
	 * @var string the Facebook access token
	 */
	private $_token;
	/**
	 * 
	 * @var string the error message when login fails
	 */
	private $_error_message;
	/**
	 * 
	 * @var string the tags associated with the use
	 */
	private $_tag;
	/**
	 * 
	 * @var int the category of the logged on user
	 */
	private $_category;
	/**
	 * 
	 * @var boolean wheter the user is subscribed or not
	 */
	private $_subscribed;

	/**
	 * @return the $_tag
	 */
	public function get_tag() {
		return $this->_tag;
	}

	/**
	 * @return the $_category
	 */
	public function get_category() {
		return $this->_category;
	}

	/**
	 * @param string $_tag
	 */
	public function set_tag( $_tag ) {
		$this->_tag = $_tag;
	}

	/**
	 * @param number $_category
	 */
	public function set_category( $_category ) {
		$this->_category = $_category;
	}

	/**
	 * @return the $_subscribed
	 */
	public function get_subscribed() {
		return $this->_subscribed;
	}

	/**
	 * @param field_type $_subscribed
	 */
	public function set_subscribed( $_subscribed ) {
		$this->_subscribed = $_subscribed;
	}

	/**
	 * @return the $_name
	 */
	public function get_name() {
		return $this->_name;
	}

	/**
	 * @return the $_username
	 */
	public function get_username() {
		return $this->_username;
	}

	/**
	 * @return the $_token
	 */
	public function get_token() {
		return $this->_token;
	}

	/**
	 * @return the $_error_message
	 */
	public function get_error_message() {
		return $this->_error_message;
	}

	/**
	 * @return the $_id
	 */
	public function get_id() {
		return $this->_id;
	}

	/**
	 * @return the $_timezone
	 */
	public function get_timezone() {
		return $this->_timezone;
	}
	/**
	 * Loads the facebook class
	 * 
	 * @param Facebook_WP_Extend_Ai1ec $facebook
	 */
	public function __construct( Facebook_WP_Extend_Ai1ec $facebook ) {
		$this->_facebook = $facebook;
	}
	/**
	 * Logs the current user in
	 * 
	 * @return boolean TRUE if the login was succesful, FALSE otherwise
	 */
	public function do_login() {
		$facebook = $this->_facebook;
		$user = $facebook->getUser();
		$logged_in = FALSE;
		// We may or may not have this data based on whether the user is logged in.
		//
		// If we have a $user id here, it means we know the user is logged into
		// Facebook, but we don't know if the access token is valid. An access
		// token is invalid if the user logged out of Facebook.
		// Unfortunately this means an API call each time https://developers.facebook.com/blog/post/2011/05/13/how-to--handle-expired-access-tokens/
		if ( $user ) {
			try {
				$user_data = $facebook->api( '/me' );
				// If no exception is thrown we have a new valid token, save it
				$this->_token = $facebook->getAccessToken();
				$this->_id = $user_data['id'];
				$this->_username = $user_data['username'];
				$this->_name = $user_data['name'];
				$this->_timezone = $user_data['timezone'];
				$this->check_if_user_is_subscribed();
				$logged_in = TRUE;
				$this->_error_message = FALSE;
			}
			catch ( WP_FacebookApiException $e ) {
				$user = NULL;
				// If the type is OAuthException something happened
				if ( $e->getCode() === Ai1ecFacebookConnectorPlugin::FB_OAUTH_EXC_CODE ) {
					$facebook->destroySession();
					$this->_error_message = $e->getMessage();
				}
			}
		}
		return $logged_in;
	}
	/**
	 * Loads data about subscription, tag and category in the object.
	 */
	private function check_if_user_is_subscribed() {
		global $wpdb;
		$table_name = Ai1ec_Facebook_Factory::get_plugin_table();
		$query = $wpdb->prepare( "SELECT subscribed, category, tag FROM $table_name WHERE user_id = %s", $this->_id );
		$user = $wpdb->get_row( $query, ARRAY_A );
		$this->_subscribed = ( (int) $user['subscribed'] === 1 ) ? TRUE : FALSE;
		$this->_tag = $user['tag'];
		$this->_category = $user['category'];
	}

}

?>