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

class Buddyvents_Push
{
	private $event;
	private $action;
	private $hook;
	private $allowed_hooks;
	private $query_string;
	
	/**
	 * PHP5 Constructor
	 * 
	 * @package API
	 * @since 	1.7
	 */
	public function __create( $event = false, $action = false )
	{
		if( ! $event || ! $action )
			return false;

		$this->action = $action;
		$this->allowed_hooks = $this->allowed_hooks();
				
		$this->event = $event;
		$this->hook = bpe_get_hook_for_user( $this->event->user_id, $this->event );
		
		if( empty( $this->hook ) )
			return false;
			
		$this->query_string = $this->get_post_vars();
	}
	
	/**
	 * All allowed webhooks
	 * 
	 * @package API
	 * @since 	1.7
	 */
	static function allowed_hooks()
	{
		$webhooks = array(
			'bpe_saved_new_event' => 'new_event',
			'bpe_approved_created_event' => 'new_event',
			'bpe_edited_event_action' => 'update_event',
			'bpe_updated_event_schedules' => 'update_schedule',
			'bpe_updated_event_documents' => 'update_document',
			'bpe_promote_user_to_admin' => 'promote_admin',
			'bpe_promote_user_to_organizer' => 'promote_organizer',
			'bpe_demote_user_to_organizer' => 'demote_organizer',
			'bpe_demote_user_to_attendee' => 'demote_attendee',
			'bpe_removed_user_from_event' => 'remove_attendee',
			'bpe_new_event_logo' => 'update_logo',
			'bpe_delete_event_action' => 'delete_event',
			'bpe_send_comment' => 'new_comment',
			'bpe_attend_event' => 'attending',
			'bpe_not_attending_event_general' => 'not_attending',
			'bpe_maybe_attend_event' => 'maybe_attending'
		);
		
		return $webhooks;
	}
	
	/**
	 * Get all unique hooks
	 * 
	 * @package API
	 * @since 	1.7
	 */
	static function unique_hooks()
	{
		$events = self::allowed_hooks();
		unset( $events['bpe_approved_created_event'] );
		
		return $events;
	}
	
	/**
	 * Get post vars
	 * 
	 * @package API
	 * @since 	1.7
	 */
	private function get_post_vars()
	{
		$key = $this->allowed_hooks[$this->action];
		
		return array(
			$key => $this->event->id
		);
	}

	/**
	 * Push the data out
	 * 
	 * @package API
	 * @since 	1.7
	 */
	public function push()
	{
		if( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV )
			return false;

		$url = ( isset( $this->hook->url ) ) ? $this->hook->url : false;
		
		if( empty( $url ) )
			return false;
		
		return wp_remote_post( $url, array( 'body' => http_build_query( (array)$this->query_string, '', '&' ) ) );
	}
}

/**
 * Initiate the webhooks
 * 
 * @package API
 * @since 	1.7
 */
function bpe_api_init_webhooks( $event )
{
	$action = current_filter();
	
	$webhook = new Buddyvents_Push( $event, $action );
	$webhook->push();	
}
add_action( 'bpe_saved_new_event', 'bpe_api_init_webhooks' );
add_action( 'bpe_approved_created_event', 'bpe_api_init_webhooks' );
add_action( 'bpe_edited_event_action', 'bpe_api_init_webhooks' );
add_action( 'bpe_delete_event_action', 'bpe_api_init_webhooks' );
add_action( 'bpe_send_comment', 'bpe_api_init_webhooks' );
add_action( 'bpe_attend_event', 'bpe_api_init_webhooks' );
add_action( 'bpe_not_attending_event_general', 'bpe_api_init_webhooks' );
add_action( 'bpe_maybe_attend_event', 'bpe_api_init_webhooks' );
add_action( 'bpe_removed_user_from_event', 'bpe_api_init_webhooks' );
add_action( 'bpe_promote_user_to_admin', 'bpe_api_init_webhooks' );
add_action( 'bpe_promote_user_to_organizer', 'bpe_api_init_webhooks' );
add_action( 'bpe_demote_user_to_organizer', 'bpe_api_init_webhooks' );
add_action( 'bpe_demote_user_to_attendee', 'bpe_api_init_webhooks' );
add_action( 'bpe_updated_event_schedules', 'bpe_api_init_webhooks' );
add_action( 'bpe_updated_event_documents', 'bpe_api_init_webhooks' );
add_action( 'bpe_new_event_logo', 'bpe_api_init_webhooks' );
?>