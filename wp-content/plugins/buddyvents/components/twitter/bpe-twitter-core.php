<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add the Twitter button to the settings page
 * 
 * Attached to the <code>bpe_event_settings_action_end</code> action hook
 * 
 * @package Twitter
 * @since 	1.6
 * 
 * @param 	mixed 	$user 	Either int or object
 * 
 * @uses 	bpe_get_option()
 * @uses 	bp_get_user_meta()
 * @uses 	wp_nonce_url()
 * @uses 	bp_loggedin_user_domain()
 * @uses 	bp_get_settings_slug()
 * @uses 	bpe_get_base()
 * @uses 	bp_loggedin_user_id()
 */
function bpe_twitter_add_sign_in( $user )
{
	if( is_object( $user ) )
		$user_id = $user->data->ID;
	else
		$user_id = $user;
	
	if( bpe_get_option( 'twitter_consumer_key' ) && bpe_get_option( 'twitter_consumer_secret' ) )
	{
        ?>
        <h4><?php _e( 'Twitter Settings', 'events' ); ?></h4>
		<?php		
		if( ! $token = bp_get_user_meta( $user_id, 'tw_access_token', true ) ) :
			?>
			<p>
				<?php _e( 'Authorize yourself to publish your new events automatically to Twitter:', 'events' ); ?><br />
				<a href="<?php echo wp_nonce_url( bp_loggedin_user_domain() . bp_get_settings_slug() . '/'. bpe_get_base( 'slug' ) .'/twitter/authenticate/', 'bpe_twitter_oauth' ); ?>">
					<img src="<?php echo EVENT_URLPATH .'css/images/twitter.png'; ?>" alt="<?php _e( 'Sign in with Twitter', 'events' ); ?>" />
				</a>
            </p>
			<?php
		else:
			?>
			<p>
				<?php _e( 'Remove your current Twitter token if you expirience any problems:', 'events' ); ?><br />
				<a href="<?php echo wp_nonce_url( bp_loggedin_user_domain() . bp_get_settings_slug() . '/'. bpe_get_base( 'slug' ) .'/twitter/remove/', 'bpe_remove_twitter_access' ) ?>"><?php _e( 'Delete Token', 'events' ); ?></a>
            </p>
			<?php
		endif;
		
		echo '<hr />';
	}
}
add_action( 'bpe_event_settings_action_end', 'bpe_twitter_add_sign_in' );

/**
 * Twitter authenticate
 * 
 * Attached to the <code>wp</code> action hook
 * 
 * @package Twitter
 * @since 	1.6
 * 
 * @uses 	bp_is_settings_component()
 * @uses 	bp_is_action_variable()
 * @uses 	check_admin_referer()
 * @uses 	bpe_get_option()
 * @uses 	bp_loggedin_user_domain()
 * @uses 	bp_loggedin_user_id()
 * @uses 	bp_get_settings_slug()
 * @uses 	bpe_get_base()
 * @uses 	bp_update_user_meta()
 * @uses 	bp_core_redirect()
 */
function bpe_twitter_oauth()
{
	if( bp_is_settings_component() && bp_is_action_variable( 'twitter', 0 ) && bp_is_action_variable( 'authenticate', 1 ) )
	{
		check_admin_referer( 'bpe_twitter_oauth' );
		
		require_once( EVENT_ABSPATH .'components/twitter/twitteroauth/twitteroauth.php' );

		$connection = new TwitterOAuth( bpe_get_option( 'twitter_consumer_key' ), bpe_get_option( 'twitter_consumer_secret' ) );
		$request_token = $connection->getRequestToken( bp_loggedin_user_domain() . bp_get_settings_slug() . '/'. bpe_get_base( 'slug' ) .'/twitter/callback/' );
		
		$oath_return = array(
			'oauth_token' => $request_token['oauth_token'],
			'oauth_token_secret' => $request_token['oauth_token_secret']
		);
		bp_update_user_meta( bp_loggedin_user_id(), 'oauth_token', $oath_return );
		
		switch( $connection->http_code )
		{
			case 200:
				$url = $connection->getAuthorizeURL( $request_token['oauth_token'] );
				bp_core_redirect( $url ); 
				break;
		}
	}
}
add_action( 'wp', 'bpe_twitter_oauth', 1 );

/**
 * Twitter callback
 * 
 * Attached to the <code>wp</code> action hook
 * 
 * @package Twitter
 * @since 	1.6
 * 
 * @uses 	bp_is_settings_component()
 * @uses 	bp_is_action_variable()
 * @uses 	bpe_get_option()
 * @uses 	bp_core_add_message()
 * @uses 	bp_loggedin_user_domain()
 * @uses 	bp_loggedin_user_id()
 * @uses 	bp_get_settings_slug()
 * @uses 	bpe_get_base()
 * @uses 	bp_update_user_meta()
 * @uses 	bp_core_redirect()
 */
function bpe_twitter_callback()
{
	if( bp_is_settings_component() && bp_is_action_variable( 'twitter', 0 ) && bp_is_action_variable( 'callback', 1 ) )
	{
		require_once( EVENT_ABSPATH .'components/twitter/twitteroauth/twitteroauth.php' );

		$session = bp_get_user_meta( bp_loggedin_user_id(), 'oauth_token', true );
		
		if( isset( $_REQUEST['oauth_token'] ) && $session['oauth_token'] !== $_REQUEST['oauth_token'])
		{
			bp_update_user_meta( bp_loggedin_user_id(), 'oauth_status', 'oldtoken' );
			bp_delete_user_meta( bp_loggedin_user_id(), 'oauth_token' );
		}
		
		$connection = new TwitterOAuth( bpe_get_option( 'twitter_consumer_key' ), bpe_get_option( 'twitter_consumer_secret' ), $session['oauth_token'], $session['oauth_token_secret'] );
		$access_token = $connection->getAccessToken( $_REQUEST['oauth_verifier'] );
		
		bp_delete_user_meta( bp_loggedin_user_id(), 'oauth_token' );
		
		if( empty( $access_token['oauth_token'] ) || empty( $access_token['oauth_token_secret'] ) )
		{
			bp_core_add_message( __( 'There was a problem authenticating your Twitter credentials.', 'events' ), 'error' );
		}
		else
		{
			bp_update_user_meta( bp_loggedin_user_id(), 'tw_access_token', $access_token );
			bp_core_add_message( __( 'Your Twitter credentials were authenticated successfully.', 'events' ) );
		}
		
		bp_core_redirect( bp_loggedin_user_domain() .'/'. bp_get_settings_slug() .'/'. bpe_get_base( 'slug' ) .'/' );
	}
}
add_action( 'wp', 'bpe_twitter_callback', 1 );

/**
 * Delete a users twitter token
 * 
 * Attached to the <code>wp</code> action hook
 * 
 * @package Twitter
 * @since 	1.6
 * 
 * @uses 	bp_is_settings_component()
 * @uses 	bp_is_action_variable()
 * @uses 	bp_action_variable()
 * @uses 	bp_delete_user_meta()
 * @uses 	check_admin_referer()
 * @uses 	bp_core_add_message()
 * @uses 	bp_core_redirect()
 * @uses 	bp_loggedin_user_domain()
 * @uses 	bp_get_settings_slug()
 * @uses 	bpe_get_base()
 */
function bpe_twitter_delete_token()
{
	if( bp_is_settings_component() && bp_is_action_variable( 'twitter', 0 ) && bp_is_action_variable( 'remove', 1 ) )
	{
		check_admin_referer( 'bpe_remove_twitter_access' );
		
		bp_delete_user_meta( bp_loggedin_user_id(), 'tw_access_token' );

		bp_core_add_message( __( 'Your Twitter credentials were successfully removed.', 'events' ) );
		bp_core_redirect( bp_loggedin_user_domain() . bp_get_settings_slug() . '/'. bpe_get_base( 'slug' ) .'/' );
	}
}
add_action( 'wp', 'bpe_twitter_delete_token', 2 );

/**
 * Add the twitter option to create page
 * 
 * Attached to the <code>bpe_add_to_create_page</code> action hook
 * 
 * @package Twitter
 * @since 	1.6
 * 
 * @uses 	bp_get_user_meta()
 * @uses 	bp_loggedin_user_id()
 * @uses 	bpe_get_option()
 */
function bpe_twitter_add_to_create()
{
	$oauth = bp_get_user_meta( bp_loggedin_user_id(), 'tw_access_token', true );
	
	if( bpe_get_option( 'twitter_consumer_key' ) && bpe_get_option( 'twitter_consumer_secret' ) && ! empty( $oauth['oauth_token'] ) && ! empty( $oauth['oauth_token_secret'] ) )
	{
		?>
		<label for="send_to_twitter"><input type="checkbox" name="send_to_twitter" value="1" />
			<?php _e( 'Check to publish this event to Twitter.', 'events' ) ?>
		</label>
		<?php
	}
}
add_action( 'bpe_add_to_create_page', 'bpe_twitter_add_to_create' );

/**
 * Note to send to Twitter
 * 
 * Attached to the <code>bpe_event_is_publishable</code> action hook
 * 
 * @package Twitter
 * @since 	1.6
 * 
 * @param 	object 	$event 	Buddyvents event settings
 * 
 * @uses 	bpe_update_eventmeta()
 * @uses 	bpe_get_event_id()
 */
function bpe_twitter_add_event_meta( $event )
{
	if( isset( $_POST['send_to_twitter'] ) )
		bpe_update_eventmeta( bpe_get_event_id( $event ), 'post_to_twitter', 'yes' );
}
add_action( 'bpe_event_is_publishable', 'bpe_twitter_add_event_meta' );

/**
 * Send a note to twitter
 * 
 * Attached to the <code>bpe_saved_new_event</code> action hook
 * Status can be changed via the <code>bpe_twitter_process_send_status_clean</code> filter hook
 * 
 * @package Twitter
 * @since 	1.6
 * 
 * @param 	object 	$event 	Buddyvents event settings
 * 
 * @uses 	bpe_get_eventmeta()
 * @uses 	bpe_get_event_id()
 * @uses 	bp_get_user_meta()
 * @uses 	bpe_get_event_user_id()
 * @uses 	bpe_get_bitly_url()
 * @uses 	bpe_get_base()
 * @uses 	bpe_get_option()
 * @uses 	bpe_get_bitly_url()
 * @uses 	bp_get_root_domain()
 * @uses 	bpe_get_event_latitude()
 * @uses 	bpe_get_event_longitude()
 */
function bpe_twitter_process_send( $event )
{
	$process = false;
	
	if( bpe_get_eventmeta( bpe_get_event_id( $event ), 'post_to_twitter' ) == 'yes' )
		$process = true;
	
	if( $process )
	{
		require_once( EVENT_ABSPATH .'components/twitter/twitteroauth/twitteroauth.php' );
		
		$oauth = bp_get_user_meta( bpe_get_event_user_id( $event ), 'tw_access_token', true );

		$connection = new TwitterOAuth( bpe_get_option( 'twitter_consumer_key' ), bpe_get_option( 'twitter_consumer_secret' ), $oauth['oauth_token'], $oauth['oauth_token_secret'] );
		$content = $connection->get( 'account/verify_credentials' );
			
		$bitly = bpe_get_bitly_url( bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'active_slug' ) .'/'. bpe_get_event_slug( $event ) .'/' );
		$status = apply_filters( 'bpe_twitter_process_send_status', sprintf( __( 'New Event: %s at %s, %s', 'events' ), bpe_get_event_name( $event ), bpe_get_event_location( $event ), $bitly ), $event, $bitly );
		$count = strlen( $status );
		 
		if( $count < 140 )
			$status_clean = $status;
		else
			$status_clean = apply_filters( 'bpe_twitter_process_send_status_clean', sprintf( __( 'New Event: %s, %s', 'events' ), bpe_get_event_name( $event ), $bitly ), $event, $bitly );
			
		$stats['status'] = $status_clean;
		
		if( bpe_get_event_latitude( $event ) || bpe_get_event_longitude( $event ) )
		{
			$stats['lat'] = bpe_get_event_latitude( $event );
			$stats['long'] = bpe_get_event_longitude( $event );
			$stats['display_coordinates'] = true;
		}
		
		$connection->post( 'statuses/update', $stats );
	}	
}
add_action( 'bpe_saved_new_event', 'bpe_twitter_process_send' );
?>