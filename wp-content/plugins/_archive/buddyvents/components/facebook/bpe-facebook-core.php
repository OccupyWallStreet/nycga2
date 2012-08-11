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
 * Display the FB login button
 * 
 * Attached to the <code>bpe_event_settings_action_end</code> action hook
 * 
 * @package Facebook
 * @since 	1.6
 * 
 * @param 	mixed 	$user 	Int or object
 * 
 * @uses 	bpe_get_option()
 */
function bpe_facebook_display_button( $user )
{
	if( is_object( $user ) )
		$user_id = $user->data->ID;
	else
		$user_id = $user;

	if( bpe_get_option( 'facebook_appid' ) && bpe_get_option( 'facebook_secret' ) )
	{
		echo '<h4>'. __( 'Facebook Settings', 'events' ) .'</h4>';

		if( ! $token = bp_get_user_meta( $user_id, 'fb_access_token', true ) ) :
			?>
			<p>
				<?php _e( 'Authorize yourself to publish your new events automatically to Facebook:', 'events' ); ?><br />
				<a href="<?php echo wp_nonce_url( bp_loggedin_user_domain() . bp_get_settings_slug() . '/'. bpe_get_base( 'slug' ) .'/facebook/authenticate/', 'bpe_facebook_oauth' ); ?>">
					<img src="<?php echo EVENT_URLPATH .'css/images/facebook.gif'; ?>" alt="<?php _e( 'Sign in with Facebook', 'events' ); ?>" />
				</a>
            </p>
			<?php
		else:
			?>
			<p>
				<?php _e( 'Remove authorization to post events to Facebook:', 'events' ); ?><br />
				<a href="<?php echo wp_nonce_url( bp_loggedin_user_domain() . bp_get_settings_slug() . '/'. bpe_get_base( 'slug' ) .'/facebook/remove/', 'bpe_remove_facebook_access' ) ?>"><?php _e( 'Delete Token', 'events' ); ?></a>
			</p>
			<?php
					endif;

		echo '<hr />';
	}
}
add_action( 'bpe_event_settings_action_end', 'bpe_facebook_display_button' );

/**
 * Facebook authenticate
 * 
 * Attached to the <code>wp</code> action hook
 * 
 * @package Facebook
 * @since 	2.1
 * 
 * @uses 	bp_is_settings_component()
 * @uses 	bp_is_action_variable()
 * @uses 	check_admin_referer()
 * @uses 	bpe_get_option()
 * @uses 	bp_core_redirect()
 */
function bpe_facebook_oauth()
{
	if( bp_is_settings_component() && bp_is_action_variable( 'facebook', 0 ) && bp_is_action_variable( 'authenticate', 1 ) )
	{
		check_admin_referer( 'bpe_facebook_oauth' );
		
		require_once( EVENT_ABSPATH .'components/facebook/bpe-facebook-class.php' );
	
		$fb = new Facebook( array(
			'appId'  => bpe_get_option( 'facebook_appid'  ),
			'secret' => bpe_get_option( 'facebook_secret' ),
			'cookie' => true
		) );

		$url   = bp_loggedin_user_domain() . bp_get_settings_slug() . '/'. bpe_get_base( 'slug' ) .'/';
		$user  = $fb->getUser();
		$token = bp_get_user_meta( bp_loggedin_user_id(), 'fb_access_token', true );
		
		if( ! $user || ! $token ):
   			$url_params = array(
				'scope' 		=> 'status_update,publish_stream,read_stream,offline_access,manage_pages',
				'fbconnect' 	=> 1,
				'redirect_uri' 	=> $url .'facebook/callback/'
			);
			
			$login_url = $fb->getLoginUrl( $url_params );
 
			bp_core_redirect( $login_url ); 
		endif;
		
		bp_core_redirect( $url );
	}
}
add_action( 'wp', 'bpe_facebook_oauth', 1 );

/**
 * Twitter callback
 * 
 * Attached to the <code>wp</code> action hook
 * 
 * @package Facebook
 * @since 	2.1
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
function bpe_facebook_callback()
{
	if( bp_is_settings_component() && bp_is_action_variable( 'facebook', 0 ) && bp_is_action_variable( 'callback', 1 ) )
	{
		require_once( EVENT_ABSPATH .'components/facebook/bpe-facebook-class.php' );
	
		$fb = new Facebook( array(
			'appId'  => bpe_get_option( 'facebook_appid'  ),
			'secret' => bpe_get_option( 'facebook_secret' ),
			'cookie' => true
		) );

		$user = $fb->getUser();
		
		if( $user ) :
			$token = $fb->getAccessToken();
			
			//check permissions
			$permissions = $fb->api(
				'/me/permissions',
				'GET',
				array( 'access_token' => $access_token )
			);
			
			// send the user back to FB if we didn't get all needed permissions
			$permissions_needed = array( 'status_update', 'publish_stream', 'read_stream', 'offline_access', 'manage_pages' );
			
			foreach( $permissions_needed as $permission )
			{
				if( ! isset( $permissions['data'][0][$permission]) || $permissions['data'][0][$permission] != 1 )
				{
					$url_params = array(
						'scope' 	=> 'status_update,publish_stream,read_stream,offline_access,manage_pages',
						'fbconnect' => 1,
						'display'   => 'page',
						'next' 		=> bp_loggedin_user_domain() . bp_get_settings_slug() . '/'. bpe_get_base( 'slug' ) .'/facebook/callback/'
					);
					
					$login_url = $fb->getLoginUrl( $url_params );
					
					bp_core_redirect( $login_url );
					exit();
				}
			}
			
			// this gets all the pages of a user
			$account = $fb->api(
				'/me/accounts',
				'GET',
				array( 'access_token' => $token )
			);
			
			$pages = array();
			
			foreach( (array) $account['data'] as $data ) :
				if( $data['category'] == 'Application' )
					continue;
				
				unset( $data['category'] );
				
				$pages[] = $data;
			endforeach;
			
			bp_update_user_meta( bp_loggedin_user_id(), 'fb_pages', $pages );
		endif;

		bp_core_redirect( bp_loggedin_user_domain() .'/'. bp_get_settings_slug() .'/'. bpe_get_base( 'slug' ) .'/' );
	}
}
add_action( 'wp', 'bpe_facebook_callback', 1 );

/**
 * Delete a users Facebook token
 * 
 * Attached to the <code>wp</code> action hook
 * 
 * @package Facebook
 * @since 	2.1
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
function bpe_facebook_delete_token()
{
	if( bp_is_settings_component() && bp_is_action_variable( 'facebook', 0 ) && bp_is_action_variable( 'remove', 1 ) )
	{
		check_admin_referer( 'bpe_remove_facebook_access' );
		
		bp_delete_user_meta( bp_loggedin_user_id(), 'fb_pages' 		  );
		bp_delete_user_meta( bp_loggedin_user_id(), 'fb_access_token' );
		bp_delete_user_meta( bp_loggedin_user_id(), 'fb_code' 		  );
		bp_delete_user_meta( bp_loggedin_user_id(), 'fb_state' 		  );
		bp_delete_user_meta( bp_loggedin_user_id(), 'fb_user_id' 	  );

		bp_core_add_message( __( 'Your Facebook credentials were successfully removed.', 'events' ) );
		bp_core_redirect( bp_loggedin_user_domain() . bp_get_settings_slug() . '/'. bpe_get_base( 'slug' ) .'/' );
	}
}
add_action( 'wp', 'bpe_facebook_delete_token', 2 );

/**
 * Add the facebook option to create page
 * 
 * Attached to the <code>bpe_add_to_create_page</code> action hook
 * 
 * @package Facebook
 * @since 	1.6
 * 
 * @uses 	bpe_get_option()
 * 
 * name,access_token,id
 */
function bpe_facebook_add_to_create()
{
	if( bpe_get_option( 'facebook_appid' ) && bpe_get_option( 'facebook_secret' ) )
	{
		if( bp_get_user_meta( bp_loggedin_user_id(), 'fb_access_token', true ) ):
			$page_data = bp_get_user_meta( bp_loggedin_user_id(), 'fb_pages', true );
			?>
			<label for="send_to_facebook">
				<input type="checkbox" name="send_to_facebook" id="send_to_facebook" value="1" />
				<?php _e( 'Check to publish this event to Facebook.', 'events' ) ?>
			</label>
			
			<?php if( count( $page_data ) > 0 && bpe_get_option( 'enable_facebook_pages' ) ) : ?>
			<div id="fb-pages">
				<select id="fb_post_to" name="fb_post_to[]" multiple="multiple">
					<option value="me"><?php _e( 'Personal stream', 'events' ) ?></option>
					<?php foreach( $page_data as $page ) : ?>
						<option value="<?php echo esc_attr( $page['id'] ) ?>"><?php echo esc_attr( $page['name'] ) ?></option>
					<?php endforeach; ?>
				</select><br /><small><?php _e( 'Choose the streams to publish to. Hold down CTRL to select more than one.', 'events' ) ?></small>				
			</div>
			<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('#fb-pages').hide();
				jQuery('#send_to_facebook').click(function() {
					if( jQuery(this).is(':checked') ){
						jQuery('#fb-pages').show();
					} else {
						jQuery('#fb-pages').hide();
					}
				});				
			});
			</script>
			<?php endif;
		endif;
	}
}
add_action( 'bpe_add_to_create_page', 'bpe_facebook_add_to_create' );

/**
 * Send a note to facebook
 *
 * Attached to the <code>bpe_event_is_publishable</code> action hook
 * 
 * @package Facebook
 * @since 	1.6
 * 
 * @param 	object 	$event 	Buddyvents event settings
 * 
 * @uses 	bpe_update_eventmeta()
 * @uses 	bpe_get_event_id()
 */
function bpe_facebook_add_event_meta( $event )
{
	if( isset( $_POST['send_to_facebook'] ) ) :
		if( isset( $_POST['fb_post_to'] ) )
			bpe_update_eventmeta( bpe_get_event_id( $event ), 'post_to_facebook_data', $_POST['fb_post_to'] );
		
		bpe_update_eventmeta( bpe_get_event_id( $event ), 'post_to_facebook', 'yes' );
	endif;
}
add_action( 'bpe_event_is_publishable', 'bpe_facebook_add_event_meta' );

/**
 * Send one or more messages to Facebook
 * 
 * Attached to the <code>bpe_saved_new_event</code> action hook
 * 
 * @package Facebook
 * @since 	1.6
 * 
 * @TODO	Add more params to API call, like description, name, link, image, etc
 * 
 * @param 	object 	$event 	Buddyvents event settings
 * 
 * @uses 	bpe_get_eventmeta()
 * @uses 	bpe_get_event_id()
 * @uses 	bpe_get_option()
 * @uses 	bp_get_root_domain()
 * @uses 	bpe_get_base()
 * @uses 	bpe_get_bitly_url()
 */
function bpe_facebook_send_update( $event )
{
	if( bpe_get_eventmeta( bpe_get_event_id( $event ), 'post_to_facebook' ) == 'yes' )
	{
		require_once( EVENT_ABSPATH .'components/facebook/bpe-facebook-class.php' );
		
		$fb = new Facebook( array(
			'appId'  => bpe_get_option( 'facebook_appid'  ),
			'secret' => bpe_get_option( 'facebook_secret' ),
			'cookie' => true
		) );
	
		$user  			= $fb->getUser();
		$tokens  		= array();
		$personal_token = bp_get_user_meta( bp_loggedin_user_id(), 'fb_access_token', true );
		$post_to 		= bpe_get_eventmeta( bpe_get_event_id( $event ), 'post_to_facebook_data', true );
		
		// if pages are disabled, reset $post_to
		if( ! bpe_get_option( 'enable_facebook_pages' ) )
			$post_to = false;
		
		// if we don't have any pages
		if( ! $post_to ) :
			if( ! $personal_token )
				return false;

			$tokens['me'] = $personal_token;
		// there are pages
		else :
			$page_data = bp_get_user_meta( bp_loggedin_user_id(), 'fb_pages', true );
			
			// loop through the pages and build the tokens
			foreach( (array) $post_to as $id ) :
				foreach( (array) $page_data as $data ) :
					if( $id == 'me' ) :
						$tokens['me'] = $personal_token;
						continue;
					endif;
					
					if( $data['id'] == $id ) :
						$tokens[$data['id']] = $data['access_token'];
						break;
					endif;
				endforeach;
			endforeach;
		endif;
		
		if( ! $user || count( $tokens ) <= 0 )
			return false;
		
		// build the message
		$bitly = bpe_get_bitly_url( bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'active_slug' ) .'/'. bpe_get_event_slug( $event ) .'/' );
		$message = apply_filters( 'bpe_facebook_send_update_message', sprintf( __( 'New Event: %s at %s, %s', 'events' ), bpe_get_event_name( $event ), bpe_get_event_location( $event ), $bitly ), $event, $bitly );

		// send message to all streams
		// NOTE: Facebook has a limit of between 10-25 POST queries per user per application per day
		foreach( $tokens as $stream => $token ) :
			$fb->api( '/'. $stream .'/feed', 'POST', array( 
				'message' 	   => $message,
				'access_token' => $token
			) );
		endforeach;
	}
}
add_action( 'bpe_saved_new_event', 'bpe_facebook_send_update' );
?>