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

class Buddyvents_AWeber extends Buddyvents_NL_Services
{
	/**
	 * PHP5 Constructor
	 * 
	 * @package	Newsletter
	 * @since 	2.1.1
 	 */
	public function __construct()
	{
		$this->service = 'aweber';
		
		parent::__construct();
	}

	/**
	 * AWeber form
	 * 
	 * @package	Newsletter
	 * @since 	2.1.1
 	 */
	protected function create_edit_form( $context )
	{
		$api_key = bpe_get_eventmeta( bpe_get_displayed_event( 'id' ), 'aweber_api_key' 	);
		$list_id = bpe_get_eventmeta( bpe_get_displayed_event( 'id' ), 'aweber_list_id' 	);
		$display = bpe_get_eventmeta( bpe_get_displayed_event( 'id' ), 'newsletter_service' );
		
		if( ! $auth_code )
			$auth_code = bp_get_user_meta( bp_loggedin_user_id(), 'aweber_api_key', true );

		?>
		<div id="aweber-wrapper" class="news-wrap">
			<?php wp_nonce_field( 'bpe_get_ajax_aweber_list', '_bpe_aweber_nonce', false ); ?>
			
			<?php if( count( Buddyvents_Newsletter::$services ) > 1 ) : ?>
				<div class="show_service_wrap">
					<input type="radio"<?php if( $display == 'aweber' ) echo ' checked="checked"' ?>  class="show_service" id="show_aweber" name="show_service" value="aweber" />
				</div>
			<?php else : ?>
				<input type="hidden" name="show_service" value="aweber" />
			<?php endif; ?>
			
			<div class="news-right">
				<h4><?php _e( 'Enable an AWeber Newsletter list' , 'events' ) ?></h4>
				
				<?php do_action( 'bpe_newsletter_before_aweber_instructions_'. $context ); ?>
		
				<?php if( empty( $list_id ) ) : ?>
				<ol class="nl_instructions">
					<li><?php printf( __( 'Enter your AWeber application key. Click <a onclick="window.open(this.href,\'\',\'resizable=yes,location=no,width=750,height=525,status\'); return false" href="https://auth.aweber.com/1.0/oauth/authorize_app/%s">here</a> to get a key.', 'events' ), bpe_get_config( 'aweber_app_key' ) ) ?></li>
					<li><?php _e( 'Then fetch your lists.', 'events' ) ?></li>
					<li>
						<?php 
						if( $context == 'create' )
							_e( 'Pick one list, then proceed to the next step.', 'events' );
						else
							_e( 'Pick one list, then save your settings.', 'events' );
						?>
					</li>
				</ol>
				<?php endif; ?>
				
				<div id="aweber_list_response"></div>
				
				<?php do_action( 'bpe_newsletter_before_aweber_auth_code_'. $context ); ?>
		
				<label for="aweber_api_key"><?php _e( 'Authorization Code' , 'events' ) ?></label>
				<input type="text" id="aweber_api_key" name="aweber_api_key" value="<?php echo esc_attr( $api_key ) ?>" />
		
				<?php do_action( 'bpe_newsletter_before_aweber_list_id_'. $context ); ?>
		
				<?php if( ! empty( $list_id ) ) : ?>
					<label for="aweber_list_id"><?php _e( 'List ID' , 'events' ) ?></label>
					<input type="text" id="aweber_list_id" name="aweber_list_id" value="<?php echo esc_attr( $list_id ) ?>" />
				<?php else : ?>
					<p class="nl_top"><a id="aweber_fetch_lists" class="button" href="#"><?php _e( 'Fetch lists' , 'events' ) ?></a></p>
					<div id="aweber_lists"></div>
				<?php endif;
				do_action( 'bpe_newsletter_after_aweber_'. $context );
				?>
				<hr />
			</div>
		</div>
		<?php
	}

	/**
	 * Display the signup form
	 * 
	 * @package	Newsletter
	 * @since 	2.1.1
 	 */
	public function signup_form()
	{
		global $bp;
		
		if( bpe_get_eventmeta( bpe_get_displayed_event( 'id' ), 'newsletter_service' ) != 'aweber' )
			return false;

		$email = bp_core_get_user_email( bp_loggedin_user_id() );
		
		bpe_load_template( 'events/includes/event-header' );
		
		do_action( 'bpe_newsletter_before_aweber_signup' );
		?>
		<form id="aweber-subscription-form" name="aweber-subscription-form" action="" method="post" class="standard-form">
			<?php wp_nonce_field( 'bpe_subscribe_ajax_aweber_list', '_bpe_aweber_nonce', false ); ?>

			<?php do_action( 'bpe_newsletter_before_aweber_name' ); ?>

			<label for="aweber_name">* <?php _e( 'Name' , 'events' ) ?></label>
			<input type="text" id="aweber_name" name="aweber_name" value="<?php echo esc_attr( $bp->loggedin_user->fullname ) ?>" />

			<?php do_action( 'bpe_newsletter_before_aweber_email' ); ?>

			<label for="aweber_email">* <?php _e( 'Email' , 'events' ) ?></label>
			<input type="text" id="aweber_email" name="aweber_email" value="<?php echo esc_attr( $email ) ?>" />
			
			<input type="hidden" id="aweber_event_id" name="aweber_event_id" value="<?php echo esc_attr( bpe_get_displayed_event( 'id' ) ) ?>" />

			<?php do_action( 'bpe_newsletter_before_aweber_subscribe' ); ?>

	        <p class="nl_top">
	        	<input type="submit" value="<?php _e( 'Subscribe', 'events' ) ?>" id="aweber-subscribe" name="aweber-subscribe" />
	        </p>
		</form>
		<?php
		do_action( 'bpe_newsletter_after_aweber_signup' );
	}
	
	/**
	 * Add a new subscriber to a AWeber list via AJAX
	 * 
	 * @package	Newsletter
	 * @since 	2.1.1
 	 */
	public function new_subscriber()
	{
		check_ajax_referer( 'bpe_subscribe_ajax_aweber_list', '_bpe_aweber_nonce' );

		$name		= wp_filter_kses( $_POST['aweber_name']  );
		$email		= wp_filter_kses( $_POST['aweber_email'] );
		$event_id	= absint( $_POST['aweber_event_id'] );
		
		if( empty( $name ) || empty( $email ) ) :
			die( json_encode( array(
				'status'  => 'error',
				'message' => __( 'Please enter all fields (Email and name).', 'events' )
			) ) );
		endif;

		if( ! is_email( $email ) ) :
			die( json_encode( array(
				'status'  => 'error',
				'message' => __( 'Please enter a valid email address.', 'events' )
			) ) );
		endif;
		
		require_once( EVENT_ABSPATH .'components/newsletter/aweber/aweber_api.php' );

		$list_id = bpe_get_eventmeta( $event_id, 'aweber_list_id' 	   );
		$data 	 = bpe_get_eventmeta( $event_id, 'aweber_token_secret' );
		$api_key = bpe_get_eventmeta( $event_id, 'aweber_api_key'	   );
			
        list( $application_key, $application_secret, $request_token, $request_token_secret, $oauth_verifier ) = explode( '|', $api_key );
	 
        $api = new AWeberAPI( $application_key, $application_secret );
        $api->user->accessToken  = $data['token'];
        $api->user->tokenSecret  = $data['secret'];

        try
        {
            $account = $api->getAccount( $data['token'], $data['secret'] );
			$list = $account->loadFromUrl( '/accounts/'. $account->id .'/lists/'. $list_id );

		    $list->subscribers->create( array(
		        'email' => $email,
		        'name' 	=> $name
		    ) );

			die( json_encode( array(
				'status'  => 'updated',
				'message' => __( 'You have been subscribed! Look out for the confirmation email.', 'events' )
			) ) );
        }
        catch( AWeberException $exc )
        {
			die( json_encode( array(
				'status'  => 'error',
				'message' => __( 'The newsletter service could not be contacted. Subscription failed.', 'events' )
			) ) );
        }
	}

	/**
	 * Get all lists for an authorization code via AJAX
	 * 
	 * @package	Newsletter
	 * @since 	2.1.1
 	 */
	public function get_lists()
	{
		check_ajax_referer( 'bpe_get_ajax_aweber_list', '_bpe_aweber_nonce' );

		if( empty( $_POST['aweber_api_key'] ) ) :
			die( json_encode( array(
				'status'  => 'error',
				'message' => __( 'You need to enter your authorization code first.', 'events' ),
				'html'	  => '',
				'remove'  => 'no'
			) ) );
		endif;

		require_once( EVENT_ABSPATH .'components/newsletter/aweber/aweber_api.php' );

		$api_key = wp_filter_kses( $_POST['aweber_api_key'] );
		$event_id = absint( $_POST['nl_event_id'] );

        list( $application_key, $application_secret, $request_token, $request_token_secret, $oauth_verifier ) = explode( '|', $api_key );

        $api = new AWeberAPI( $application_key,$application_secret );
        $api->user->tokenSecret  = $request_token_secret;
        $api->user->requestToken = $request_token;
        $api->user->verifier 	 = $oauth_verifier;

        try
        {
            list( $access_token, $access_token_secret ) = $api->getAccessToken();
        }
        catch( AWeberException $exc )
        {
        	$access_token 		 = null;
			$access_token_secret = null;
        }
		
		if( empty( $access_token ) || empty( $access_token_secret ) || empty( $event_id ) ) :
			die( json_encode( array(
				'status'  => 'error',
				'message' => __( 'Unable to retrieve credentials. AWeber lists cannot be activated.', 'events' ),
				'html'	  => '',
				'remove'  => 'no'
			) ) );
		endif;
		
		// save the credentials for later use
		bpe_update_eventmeta( $event_id, 'aweber_token_secret', array(
			'token'  => $access_token,
			'secret' => $access_token_secret
		) );

        $api->user->accessToken  = $access_token;
        $api->user->tokenSecret  = $access_token_secret;

        try
        {
            $account = $api->getAccount( $access_token, $access_token_secret );
        }
        catch( AWeberException $exc )
        {
            $account = null;
        }

		if( ! $account ) :
			die( json_encode( array(
				'status'  => 'error',
				'message' => __( 'Your lists could not be fetched. Enter your list ID manually.', 'events' ),
				'html'	  => '<label for="aweber_list_id">'. __( 'List ID' , 'events' ) .'</label><input type="text" id="aweber_list_id" name="aweber_list_id" value="" />',
				'remove'  => 'yes'
			) ) );
		else :
			$html  = '<label for="aweber_list_id">'. __( 'List ID' , 'events' ) .'</label>';
			$html .= '<select id="aweber_list_id" name="aweber_list_id">';
			foreach( $account->lists as $list )
				$html .= '<option value="'. esc_attr( $list->id ) .'">'. esc_attr( $list->name ) .'</option>';
			$html .= '</select>';

			die( json_encode( array(
				'status'  => 'updated',
				'message' => '',
				'html'	  => $html,
				'remove'  => 'yes'
			) ) );
		endif;
	}
}
$bpe_aweber = new Buddyvents_AWeber();
?>