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

class Buddyvents_Mailchimp extends Buddyvents_NL_Services
{
	/**
	 * PHP5 Constructor
	 * 
	 * @package	Newsletter
	 * @since 	2.1
 	 */
	public function __construct()
	{
		$this->service = 'mailchimp';
		
		parent::__construct();
	}
		
	/**
	 * Mailchimp form
	 * 
	 * @package	Newsletter
	 * @since 	2.1
 	 */
	protected function create_edit_form( $context )
	{
		$api_key = bpe_get_eventmeta( bpe_get_displayed_event( 'id' ), 'mailchimp_api_key' 	);
		$list_id = bpe_get_eventmeta( bpe_get_displayed_event( 'id' ), 'mailchimp_list_id' 	);
		$display = bpe_get_eventmeta( bpe_get_displayed_event( 'id' ), 'newsletter_service' );
		
		if( ! $api_key )
			$api_key = bp_get_user_meta( bp_loggedin_user_id(), 'mailchimp_api_key', true );

		?>
		<div id="mailchimp-wrapper" class="news-wrap">
			<?php wp_nonce_field( 'bpe_get_ajax_mailchimp_list', '_bpe_mailchimp_nonce', false ); ?>
			
			<?php if( count( Buddyvents_Newsletter::$services ) > 1 ) : ?>
				<div class="show_service_wrap">
					<input type="radio"<?php if( $display == 'mailchimp' ) echo ' checked="checked"' ?> class="show_service" id="show_mailchimp" name="show_service" value="mailchimp" />
				</div>
			<?php else : ?>
				<input type="hidden" name="show_service" value="mailchimp" />
			<?php endif; ?>
			
			<div class="news-right">
				<h4><?php _e( 'Enable a Mailchimp Newsletter list' , 'events' ) ?></h4>
				
				<?php do_action( 'bpe_newsletter_before_mailchimp_instructions_'. $context ); ?>
		
				<?php if( empty( $list_id ) ) : ?>
				<ol class="nl_instructions">
					<li><?php _e( 'Enter your Mailchimp API key.', 'events' ) ?></li>
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
				
				<div id="mailchimp_list_response"></div>
				
				<?php do_action( 'bpe_newsletter_before_mailchimp_api_key_'. $context ); ?>
		
				<label for="mailchimp_api_key"><?php _e( 'API Key' , 'events' ) ?></label>
				<input type="text" id="mailchimp_api_key" name="mailchimp_api_key" value="<?php echo esc_attr( $api_key ) ?>" />
		
				<?php do_action( 'bpe_newsletter_before_mailchimp_list_id_'. $context ); ?>
		
				<?php if( ! empty( $list_id ) ) : ?>
					<label for="mailchimp_list_id"><?php _e( 'List ID' , 'events' ) ?></label>
					<input type="text" id="mailchimp_list_id" name="mailchimp_list_id" value="<?php echo esc_attr( $list_id ) ?>" />
				<?php else : ?>
					<p class="nl_top"><a id="mailchimp_fetch_lists" class="button" href="#"><?php _e( 'Fetch lists' , 'events' ) ?></a></p>
					<div id="mailchimp_lists"></div>
				<?php endif;
				do_action( 'bpe_newsletter_after_mailchimp_'. $context );
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
	 * @since 	2.1
 	 */
	public function signup_form()
	{
		global $bp;
		
		if( bpe_get_eventmeta( bpe_get_displayed_event( 'id' ), 'newsletter_service' ) != 'mailchimp' )
			return false;
		
		$fullname = (array)explode( ' ', $bp->loggedin_user->fullname );
		
		$first_name = ( ! empty( $fullname[0] ) ) ? $fullname[0] : '';
		$last_name 	= ( ! empty( $fullname[1] ) ) ? $fullname[1] : '';
		$email		= bp_core_get_user_email( bp_loggedin_user_id() );
		
		bpe_load_template( 'events/includes/event-header' );
		
		do_action( 'bpe_newsletter_before_mailchimp_signup' );
		?>
		<form id="mailchimp-subscription-form" name="mailchimp-subscription-form" action="" method="post" class="standard-form">
			<?php wp_nonce_field( 'bpe_subscribe_ajax_mailchimp_list', '_bpe_mailchimp_nonce', false ); ?>

			<?php do_action( 'bpe_newsletter_before_mailchimp_first_name' ); ?>

			<label for="mailchimp_first_name">* <?php _e( 'First Name' , 'events' ) ?></label>
			<input type="text" id="mailchimp_first_name" name="mailchimp_first_name" value="<?php echo esc_attr( $first_name ) ?>" />

			<?php do_action( 'bpe_newsletter_before_mailchimp_last_name' ); ?>

			<label for="mailchimp_last_name">* <?php _e( 'Last Name' , 'events' ) ?></label>
			<input type="text" id="mailchimp_last_name" name="mailchimp_last_name" value="<?php echo esc_attr( $last_name ) ?>" />

			<?php do_action( 'bpe_newsletter_before_mailchimp_email' ); ?>

			<label for="mailchimp_email">* <?php _e( 'Email' , 'events' ) ?></label>
			<input type="text" id="mailchimp_email" name="mailchimp_email" value="<?php echo esc_attr( $email ) ?>" />

			<input type="hidden" id="mailchimp_event_id" name="mailchimp_event_id" value="<?php echo esc_attr( bpe_get_displayed_event( 'id' ) ) ?>" />
			
			<?php do_action( 'bpe_newsletter_before_mailchimp_subscribe' ); ?>

	        <p class="nl_top">
	        	<input type="submit" value="<?php _e( 'Subscribe', 'events' ) ?>" id="mailchimp-subscribe" name="mailchimp-subscribe" />
	        </p>
		</form>
		<?php
		do_action( 'bpe_newsletter_after_mailchimp_signup' );
	}
	
	/**
	 * Add a new subscriber to a Mailchimp list via AJAX
	 * 
	 * @package	Newsletter
	 * @since 	2.1
 	 */
	public function new_subscriber()
	{
		check_ajax_referer( 'bpe_subscribe_ajax_mailchimp_list', '_bpe_mailchimp_nonce' );
		
		$first_name = wp_filter_kses( $_POST['mailchimp_first_name'] );
		$last_name	= wp_filter_kses( $_POST['mailchimp_last_name']  );
		$email		= wp_filter_kses( $_POST['mailchimp_email']		 );
		$event_id	= absint( $_POST['mailchimp_event_id'] );
		
		if( empty( $first_name ) || empty( $last_name ) || empty( $email ) ) :
			die( json_encode( array(
				'status'  => 'error',
				'message' => __( 'Please enter all fields (Email, first and last name).', 'events' )
			) ) );
		endif;

		if( ! is_email( $email ) ) :
			die( json_encode( array(
				'status'  => 'error',
				'message' => __( 'Please enter a valid email address.', 'events' )
			) ) );
		endif;

		$api_key = bpe_get_eventmeta( $event_id, 'mailchimp_api_key' );
		$list_id = bpe_get_eventmeta( $event_id, 'mailchimp_list_id' );
		
		if( empty( $api_key ) || empty( $list_id ) ) :
			die( json_encode( array(
				'status'  => 'error',
				'message' => __( 'The newsletter service could not be contacted. Subscription failed.', 'events' )
			) ) );
		endif;
		
		require_once( EVENT_ABSPATH .'components/newsletter/mailchimp/MCAPI.class.php' );
		
		$api = new MCAPI( $api_key );
		
		$api->listSubscribe( $list_id, $email, array( 'FNAME' => $first_name, 'LNAME' => $last_name ) );

		if( $api->errorCode ) :
			die( json_encode( array(
				'status'  => 'error',
				'message' => __( 'The newsletter service could not be contacted. Subscription failed.', 'events' )
			) ) );
		else :
			die( json_encode( array(
				'status'  => 'updated',
				'message' => __( 'You have been subscribed! Look out for the confirmation email.', 'events' )
			) ) );
		endif;
	}
	
	/**
	 * Get all lists for an API key via AJAX
	 * 
	 * @package	Newsletter
	 * @since 	2.1
 	 */
	public function get_lists()
	{
		check_ajax_referer( 'bpe_get_ajax_mailchimp_list', '_bpe_mailchimp_nonce' );

		if( empty( $_POST['mailchimp_api_key'] ) ) :
			die( json_encode( array(
				'status'  => 'error',
				'message' => __( 'You need to enter your API key first.', 'events' ),
				'html'	  => '',
				'remove'  => 'no'
			) ) );
		endif;
			
		require_once( EVENT_ABSPATH .'components/newsletter/mailchimp/MCAPI.class.php' );
		
		$api_key = wp_filter_kses( $_POST['mailchimp_api_key'] );
		
		$api = new MCAPI( $api_key );
		
		$lists = $api->lists();
		
		if( $api->errorCode ) :
			die( json_encode( array(
				'status'  => 'error',
				'message' => __( 'Your lists could not be fetched. Enter your list ID manually.', 'events' ),
				'html'	  => '<label for="mailchimp_list_id">'. __( 'List ID' , 'events' ) .'</label><input type="text" id="mailchimp_list_id" name="mailchimp_list_id" value="" />',
				'remove'  => 'yes'
			) ) );
		else :
			$html  = '<label for="mailchimp_list_id">'. __( 'List ID' , 'events' ) .'</label>';
			$html .= '<select id="mailchimp_list_id" name="mailchimp_list_id">';
			foreach( $lists['data'] as $list )
				$html .= '<option value="'. esc_attr( $list['id'] ) .'">'. esc_attr( $list['name'] ) .'</option>';
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
$bpe_mailchimp = new Buddyvents_Mailchimp();
?>