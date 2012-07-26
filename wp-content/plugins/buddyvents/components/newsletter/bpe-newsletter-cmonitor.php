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

class Buddyvents_CMonitor extends Buddyvents_NL_Services
{
	/**
	 * PHP5 Constructor
	 * 
	 * @package	Newsletter
	 * @since 	2.1.1
 	 */
	public function __construct()
	{
		$this->service = 'cmonitor';
		
		parent::__construct();

		add_action( 'wp_ajax_bpe_cmonitor_get_clients', array( &$this, 'get_clients' ) );
	}
	
	/**
	 * Campaign Monitor form
	 * 
	 * @package	Newsletter
	 * @since 	2.1.1
 	 */
	protected function create_edit_form( $context )
	{
		$api_key = bpe_get_eventmeta( bpe_get_displayed_event( 'id' ), 'cmonitor_api_key' 	);
		$list_id = bpe_get_eventmeta( bpe_get_displayed_event( 'id' ), 'cmonitor_list_id' 	);
		$display = bpe_get_eventmeta( bpe_get_displayed_event( 'id' ), 'newsletter_service' );

		if( ! $api_key )
			$api_key = bp_get_user_meta( bp_loggedin_user_id(), 'cmonitor_api_key', true );
			
		?>
		<div id="cmonitor-wrapper" class="news-wrap">
			<?php wp_nonce_field( 'bpe_get_ajax_cmonitor_list', '_bpe_cmonitor_nonce', false ); ?>

			<?php if( count( Buddyvents_Newsletter::$services ) > 1 ) : ?>
				<div class="show_service_wrap">
					<input type="radio"<?php if( $display == 'cmonitor' ) echo ' checked="checked"' ?> class="show_service" id="show_cmonitor" name="show_service" value="cmonitor" />
				</div>
			<?php else : ?>
				<input type="hidden" name="show_service" value="cmonitor" />
			<?php endif; ?>

			<div class="news-right">
				<h4><?php _e( 'Enable a Campaign Monitor Newsletter list' , 'events' ) ?></h4>
				
				<?php do_action( 'bpe_newsletter_before_cmonitor_instructions_'. $context ); ?>
		
				<?php if( empty( $list_id ) ) : ?>
				<ol class="nl_instructions">
					<li><?php _e( 'Enter your Campaign Monitor API key and fetch clients.', 'events' ) ?></li>
					<li><?php _e( 'Click on a client and fetch all lists.', 'events' ) ?></li>
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
				
				<div id="cmonitor_list_response"></div>
				
				<?php do_action( 'bpe_newsletter_before_cmonitor_api_key_'. $context ); ?>
		
				<label for="cmonitor_api_key"><?php _e( 'API Key' , 'events' ) ?></label>
				<input type="text" id="cmonitor_api_key" name="cmonitor_api_key" value="<?php echo esc_attr( $api_key ) ?>" />
		
				<?php do_action( 'bpe_newsletter_before_cmonitor_list_id_'. $context ); ?>
		
				<?php if( ! empty( $list_id ) ) : ?>
					<label for="cmonitor_list_id"><?php _e( 'List ID' , 'events' ) ?></label>
					<input type="text" id="cmonitor_list_id" name="cmonitor_list_id" value="<?php echo esc_attr( $list_id ) ?>" />
				<?php else : ?>
					<p class="nl_top"><a id="cmonitor_fetch_clients" class="button" href="#"><?php _e( 'Fetch clients' , 'events' ) ?></a></p>
					<div id="cmonitor_response"></div>
				<?php endif;
				do_action( 'bpe_newsletter_after_cmonitor_'. $context );
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
		
		if( bpe_get_eventmeta( bpe_get_displayed_event( 'id' ), 'newsletter_service' ) != 'cmonitor' )
			return false;

		$email = bp_core_get_user_email( bp_loggedin_user_id() );
		
		bpe_load_template( 'events/includes/event-header' );
		
		do_action( 'bpe_newsletter_before_cmonitor_signup' );
		?>
		<form id="cmonitor-subscription-form" name="cmonitor-subscription-form" action="" method="post" class="standard-form">
			<?php wp_nonce_field( 'bpe_subscribe_ajax_cmonitor_list', '_bpe_cmonitor_nonce', false ); ?>

			<?php do_action( 'bpe_newsletter_before_cmonitor_name' ); ?>

			<label for="cmonitor_name">* <?php _e( 'Name' , 'events' ) ?></label>
			<input type="text" id="cmonitor_name" name="cmonitor_name" value="<?php echo esc_attr( $bp->loggedin_user->fullname ) ?>" />

			<?php do_action( 'bpe_newsletter_before_cmonitor_email' ); ?>

			<label for="cmonitor_email">* <?php _e( 'Email' , 'events' ) ?></label>
			<input type="text" id="cmonitor_email" name="cmonitor_email" value="<?php echo esc_attr( $email ) ?>" />
			
			<input type="hidden" id="cmonitor_event_id" name="cmonitor_event_id" value="<?php echo esc_attr( bpe_get_displayed_event( 'id' ) ) ?>" />

			<?php do_action( 'bpe_newsletter_before_cmonitor_subscribe' ); ?>

	        <p class="nl_top">
	        	<input type="submit" value="<?php _e( 'Subscribe', 'events' ) ?>" id="cmonitor-subscribe" name="cmonitor-subscribe" />
	        </p>
		</form>
		<?php
		do_action( 'bpe_newsletter_after_cmonitor_signup' );
	}
	
	/**
	 * Add a new subscriber to a Campaign Monitor list via AJAX
	 * 
	 * @package	Newsletter
	 * @since 	2.1.1
 	 */
	public function new_subscriber()
	{
		check_ajax_referer( 'bpe_subscribe_ajax_cmonitor_list', '_bpe_cmonitor_nonce' );

		$name	= wp_filter_kses( $_POST['cmonitor_name']  );
		$email	= wp_filter_kses( $_POST['cmonitor_email'] );
		$event_id = absint( $_POST['cmonitor_event_id'] );
		
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

		$api_key = bpe_get_eventmeta( $event_id, 'cmonitor_api_key' );
		$list_id = bpe_get_eventmeta( $event_id, 'cmonitor_list_id' );
		
		if( empty( $api_key ) || empty( $list_id ) ) :
			die( json_encode( array(
				'status'  => 'error',
				'message' => __( 'The newsletter service could not be contacted. Subscription failed.', 'events' )
			) ) );
		endif;
	
		require_once( EVENT_ABSPATH .'components/newsletter/cmonitor/csrest_subscribers.php' );
		
		$api = new CS_REST_Subscribers( $list_id, $api_key );
		
		$result = $api->add( array(
		    'EmailAddress' 	=> $email,
		    'Name' 			=> $name,
		    'Resubscribe' 	=> true
		));
		
		if( ! $result->was_successful() ) :
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
	 * Get all clients for an API key via AJAX
	 * 
	 * @package	Newsletter
	 * @since 	2.1.1
 	 */
	public function get_clients()
	{
		check_ajax_referer( 'bpe_get_ajax_cmonitor_list', '_bpe_cmonitor_nonce' );

		if( empty( $_POST['cmonitor_api_key'] ) ) :
			die( json_encode( array(
				'status'  => 'error',
				'message' => __( 'You need to enter your API key first.', 'events' ),
				'html'	  => '',
				'remove'  => 'no'
			) ) );
		endif;

		require_once( EVENT_ABSPATH .'components/newsletter/cmonitor/csrest_general.php' );

		$api_key = wp_filter_kses( $_POST['cmonitor_api_key'] );
		
		$api = new CS_REST_General( $api_key );

		$result = $api->get_clients();

		if( ! $result->was_successful() ) :
			die( json_encode( array(
				'status'  => 'error',
				'message' => __( 'Your clients could not be fetched. Enter your list ID manually.', 'events' ),
				'html'	  => '<label for="cmonitor_list_id">'. __( 'List ID' , 'events' ) .'</label><input type="text" id="cmonitor_list_id" name="cmonitor_list_id" value="" />',
				'remove'  => 'yes'
			) ) );
		else :
			$html  = '<label for="cmonitor_client_id">'. __( 'Client ID' , 'events' ) .'</label>';
			$html .= '<select id="cmonitor_client_id" name="cmonitor_client_id">';
			foreach( $result->response as $client )
				$html .= '<option value="'. esc_attr( $client->ClientID ) .'">'. esc_attr( $client->Name ) .'</option>';
			$html .= '</select>';
			$html .= '<p class="nl_top"><a id="cmonitor_fetch_lists" class="button" href="#">'. __( 'Fetch lists' , 'events' ) .'</a></p>';

			die( json_encode( array(
				'status'  => 'updated',
				'message' => '',
				'html'	  => $html,
				'remove'  => 'yes'
			) ) );
		endif;
	}
	
	/**
	 * Get all lists for an API key and a client via AJAX
	 * 
	 * @package	Newsletter
	 * @since 	2.1.1
 	 */
	public function get_lists()
	{
		check_ajax_referer( 'bpe_get_ajax_cmonitor_list', '_bpe_cmonitor_nonce' );

		if( empty( $_POST['cmonitor_api_key'] ) || empty( $_POST['cmonitor_client_id'] ) ) :
			die( json_encode( array(
				'status'  => 'error',
				'message' => __( 'You need to enter your API key and pick a client ID.', 'events' ),
				'html'	  => '',
				'remove'  => 'no'
			) ) );
		endif;

		require_once( EVENT_ABSPATH .'components/newsletter/cmonitor/csrest_clients.php' );

		$api_key   = wp_filter_kses( $_POST['cmonitor_api_key']   );
		$client_id = wp_filter_kses( $_POST['cmonitor_client_id'] );

		$api = new CS_REST_Clients( $client_id, $api_key );
		
		$result = $api->get_lists();
		
		if( ! $result->was_successful() ) :
			die( json_encode( array(
				'status'  => 'error',
				'message' => __( 'Your lists could not be fetched. Enter your list ID manually.', 'events' ),
				'html'	  => '<label for="cmonitor_list_id">'. __( 'List ID' , 'events' ) .'</label><input type="text" id="cmonitor_list_id" name="cmonitor_list_id" value="" />',
				'remove'  => 'yes'
			) ) );
		else :
			$html  = '<label for="cmonitor_list_id">'. __( 'List ID' , 'events' ) .'</label>';
			$html .= '<select id="cmonitor_list_id" name="cmonitor_list_id">';
			foreach( $result->response as $list )
				$html .= '<option value="'. esc_attr( $list->ListID ) .'">'. esc_attr( $list->Name ) .'</option>';
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
$bpe_cmonitor = new Buddyvents_CMonitor();
?>