<?php
/**
 * This class is responsible of handling the behaviour of Facebook apps
 *
 * @author time.ly
 *
 */
class Ai1ec_Facebook_Application {
	const GRAPH_URL_FOR_APP_AUTHORIZATION   = 'https://graph.facebook.com/oauth/access_token?';
	const GRAPH_API_URL                     = 'https://graph.facebook.com/';
	const STANDARD_APP_PATH_ON_FACEBOOK     = 'http://www.facebook.com/apps/application.php';
	const EXC_MESSAGE_INVALID_APP_ID        = 'Error validating application. Invalid application ID.';
	const EXC_MESSAGE_INVALID_APP_SECRET    = 'Error validating client secret.';
	/**
	 * @var string
	 */
	private $app_id;
	/**
	 * @var string
	 */
	private $secret;
	public function __construct( $app_id, $secret ) {
		$this->app_id = $app_id;
		$this->secret = $secret;
	}
	/**
	 * Makes a call to the facebook graph api and return the result if no error occurred during the call
	 *
	 * @param string $url
	 *
	 * @throws Exception
	 *
	 * @return mixed
	 */
	private function make_graph_call_to_facebook_and_return_response( $url ) {
		// Use the wrapper function to make remote calls as suggested in the codex.
		$result = wp_remote_get( $url, array( 'sslverify' => FALSE ) );
		// If reslut is a WP_Error, something went wrong with the call
		if( is_wp_error( $result ) ) {
			throw new Exception( 'An error occured while making the call to facebook, try again' );
		}
		// Facebook either returns a json object (if we have an error) or a string, so i make that check when returning
		$decoded_body = json_decode( $result['body'] );
		return $decoded_body === NULL ? $result['body'] : $decoded_body;
	}
	/**
	 * Return a human understandable error message
	 *
	 * @param string $error_message
	 */
	private function make_meaningful_message_from_fb_error_message( $error_message ) {
		$message = '';
		switch ( $error_message ) {
			case self::EXC_MESSAGE_INVALID_APP_ID : $message = 'The Facebook app-id you have entered is not valid.';
				break;
			case self::EXC_MESSAGE_INVALID_APP_SECRET : $message = 'The Facebook app-secret you have entered is not valid.';
				break;
			default: $message = "Something unexpected hapenned while validating your app id and secret, check you have entered them correctly";
		}
		return $message;
	}
	/**
	 * Try to get an access token from Facebook fo rthe App
	 *
	 * @throws Exception
	 * @throws Ai1ec_Error_Validating_App_Id_And_Secret
	 * @return string
	 */
	public function get_back_an_access_token_from_facebook_for_the_app() {
		$url = self::GRAPH_URL_FOR_APP_AUTHORIZATION . "client_id={$this->app_id}&&client_secret={$this->secret}&grant_type=client_credentials";
		try {
			$fb_object =$this->make_graph_call_to_facebook_and_return_response( $url );
		} catch ( Exception $e ) {
			throw $e;
		}
		if( isset( $fb_object->error ) ) {
			$message = $this->make_meaningful_message_from_fb_error_message( $fb_object->error->message );
			throw new Ai1ec_Error_Validating_App_Id_And_Secret( $message );
		}
		return $fb_object;
	}
	/**
	 * Check that the app-id provided refers to a valid Facebook App ( http://stackoverflow.com/questions/6650107/how-to-validate-facebook-app-id )
	 *
	 * @param string $app_id the app_id to check
	 *
	 * @throws Exception if the remote call gives an error
	 *
	 * @return boolean TRUE if it is a valid app id
	 */
	private function is_valid_facebook_app_id( $app_id ) {
		// If the app_id is valid, when we make a call to this url we gat back an object.
		$url = self::GRAPH_API_URL . $app_id;
		try {
			$fb_object =$this->make_graph_call_to_facebook_and_return_response( $url );
		} catch ( Exception $e ) {
			throw $e;
		}
		// The link to the object must begin with the standard app path of Facebook apps.
		if ( $fb_object && isset( $fb_object->link ) && strstr( $fb_object->link, self::STANDARD_APP_PATH_ON_FACEBOOK ) ) {
			return TRUE;
		} else {
			throw new InvalidArgumentException( 'This is not a valid id of an application' );
		}
	}
}
?>
