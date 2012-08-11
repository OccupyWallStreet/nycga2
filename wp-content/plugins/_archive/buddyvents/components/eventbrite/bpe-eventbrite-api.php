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

class Buddyvents_API_Eventbrite
{
	/**
	 * Application key
	 * @since 1.6
	 */
	private $app_key;

	/**
	 * User identifier
	 * @since 1.6
	 */
	private $user_key;

	/**
	 * Eventbrite API endpoint url
	 * @since 1.6
	 */
	private $api_url;

	/**
	 * The current API method
	 * @since 1.6
	 */
	private $method;

	/**
	 * Buddyvents event data
	 * @since 1.6
	 */
	private $event;
	
	/**
	 * Variables for the event_new method
	 * @since 1.6
	 */
	private $event_new;

	/**
	 * Variables for the event_update method
	 * @since 1.6
	 */
	private $event_update;
	
	/**
	 * Variables for the event_list_attendees method
	 * @since 1.6
	 */
	private $event_list_attendees;

	/**
	 * Variables for the user_get method
	 * @since 1.6
	 */
	private $user_get;
	
	/**
	 * Variables for the organizer_new method
	 * @since 1.6
	 */
	private $organizer_new;
	
	/**
	 * Variables for the organizer_update method
	 * @since 1.6
	 */
	private $organizer_update;
	
	/**
	 * Variables for the organizer_list_events method
	 * @since 1.6
	 */
	private $organizer_list_events;
	
	/**
	 * PHP 5 constructor
	 * 
	 * @package Eventbrite
	 * @since 	1.6
	 * 
	 * @access 	public
	 * 
	 * @param 	object 	$event
	 * @param 	string 	$method
	 * @param 	string 	$format
	 */
	public function __construct( $event = false, $method = false, $format = 'json' )
	{
		if( ! method_exists( $this, $method ) )
			return false;
			
		if( in_array( $method, array( 'request', 'xml', 'json', 'save_data' ) ) )
			return false;
			
		if( ! in_array( $format, array( 'xml', 'json' ) ) )
			return false;
			
		$this->event = $event;	
		$this->method = $method;
		$this->format = $format;
		$this->app_key = bpe_get_option( 'eventbrite_appkey' );
		$this->user_key = bp_get_user_meta( $this->event->user_id, 'bpe_eventbrite_user_key', true );
		
		if( empty( $this->app_key ) || empty( $this->user_key ) )
			return false;
		
		$this->api_url = 'https://www.eventbrite.com/'. $this->format .'/' . $this->method .'?';
			
		$this->{$method}();
	}
	
	/**
	 * Request the data from the API endpoint
	 * 
	 * @package Eventbrite
	 * @since 	1.6
	 * 
	 * @access 	public
	 * @return 	object
	 * 
	 * @uses 	wp_remote_get()
	 * @uses 	wp_remote_retrieve_body()
	 */
	public function request()
	{
		if( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV )
			return false;

		$params = get_object_vars( (object)$this->{$this->method} );
		$params['app_key'] = $this->app_key;
		$params['user_key'] = $this->user_key;
		
		foreach( $params as $key => $val )
			$p[] = $key .'='. urlencode( $val );
			
		$url = $this->api_url . ( implode( '&', (array)$p ) );
		
		$result = wp_remote_get( $url );		

		$data = $this->{$this->format}( wp_remote_retrieve_body( $result ) );
		
		return $this->save_data( $data );
	}
	
	/**
	 * Save returned data to the database
	 * 
	 * @package Eventbrite
	 * @since 	1.6
	 * 
	 * @return 	object 	$data 
	 * @param 	object	$data
	 * 
	 * @uses 	bpe_update_eventmeta()
	 * @uses 	bp_update_user_meta()
	 * @access 	private
	 */
	private function save_data( $data )
	{
		switch( $this->method )
		{
			case 'event_new':
				bpe_update_eventmeta( $this->event->id, 'bpe_eventbrite_event_id', $data->process->id );
				break;			

			case 'user_get':
				bp_update_user_meta( $this->event->user_id, 'bpe_eventbrite_user_id', $data->user->user_id );
				break;	
				
			case 'organizer_new':
				bp_update_user_meta( $this->event->user_id, 'bpe_eventbrite_organizer_id', $data->process->id );
				break;		
		}
		
		return $data;
	}

	/**
	 * Load XML into an object
	 * 
	 * @package Eventbrite
	 * @since 	1.6
	 * 
	 * @param 	string 	$string		XML encoded string
	 * 
	 * @return 	object XML object
	 * @access 	private
	 */
	private function xml( $string )
	{
		return simplexml_load_string( $string );
	}
	
	/**
	 * Load JSON into an object
	 * 
	 * @package Eventbrite
	 * @since 	1.6
	 * 
	 * @access 	private
	 * @param 	string 	$string	JSON encoded string
	 * @return 	object 	JSON object
	 */
	private function json( $string )
	{
		return json_decode( $string );
	}
	
	/**
	 * Add a new event
	 * 
	 * @package Eventbrite
	 * @since 	1.6
	 * 
	 * @access private
	 */
	private function event_new()
	{
		$this->event_new->title = $this->event->name;
		$this->event_new->description = $this->event->description;
		$this->event_new->start_date = $this->event->start_date .' '. $this->event->start_time;
		$this->event_new->end_date = $this->event->end_date .' '. $this->event->end_time;
		$this->event_new->privacy = $this->event->public;
		
		if( $this->event->limit_members > 0 )
			$this->event_new->capacity = $this->event->limit_members;
		
		$this->event_new->status = 'draft';
	}

	/**
	 * Add a new event
	 * 
	 * @package Eventbrite
	 * @since 	1.6
	 * 
	 * @access private
	 */
	private function event_update()
	{
		$this->event_update->event_id = $this->event->meta->bpe_eventbrite_event_id;
		$this->event_update->title = $this->event->name;
		$this->event_update->description = $this->event->description;
		$this->event_update->start_date = $this->event->start_date .' '. $this->event->start_time;
		$this->event_update->end_date = $this->event->end_date .' '. $this->event->end_time;
		$this->event_update->privacy = $this->event->public;
		
		if( $this->event->limit_members > 0 )
			$this->event_update->capacity = $this->event->limit_members;
		
		$this->event_update->status = 'draft';
	}
	
	/**
	 * Add a new event
	 * 
	 * @package Eventbrite
	 * @since 	1.6
	 * 
	 * @access 	private
	 * @param 	int 	$page 	The page of attendeess to return
	 */
	private function event_list_attendees( $page = 1 )
	{
		$this->event_list_attendees->id = $this->event->meta->bpe_eventbrite_event_id;
		$this->event_list_attendees->count = 50;
		$this->event_list_attendees->page = $page;
		$this->event_list_attendees->show_full_barcodes = 'true';
	}
	
	/**
	 * Get a user
	 * 
	 * @package Eventbrite
	 * @since 	1.6
	 * 
	 * @access 	private
	 */
	private function user_get()
	{
		$this->user_get = new stdClass;
	}

	/**
	 * Add a new organizer
	 * 
	 * @package Eventbrite
	 * @since 	1.6
	 * 
	 * @access 	private
	 * @uses 	bp_core_get_user_displayname()
	 * @uses 	bpe_get_option()
	 * @uses 	bp_get_profile_field_data()
	 */
	private function organizer_new()
	{
		$this->organizer_new->name = bp_core_get_user_displayname( $this->event->user_id );
		
		if( bpe_get_option( 'ebapi_desc_field' ) )
			$this->organizer_new->description = bp_get_profile_field_data( array( 'field' => bpe_get_option( 'ebapi_desc_field' ), 'user_id' => $this->event->user_id ) );
	}

	/**
	 * Update an organizer
	 * 
	 * @package Eventbrite
	 * @since 	1.6
	 * 
	 * @access 	private
	 * @uses 	bp_core_get_user_displayname()
	 * @uses 	bpe_get_option()
	 * @uses 	bp_get_profile_field_data()
	 */
	private function organizer_update()
	{
		$this->organizer_update->id = $this->event->meta->bpe_eventbrite_organizer_id;
		$this->organizer_update->name = bp_core_get_user_displayname( $this->event->user_id );
		
		if( bpe_get_option( 'ebapi_desc_field' ) )
			$this->organizer_update->description = bp_get_profile_field_data( array( 'field' => bpe_get_option( 'ebapi_desc_field' ), 'user_id' => $this->event->user_id ) );
	}
	
	/**
	 * Get a list of events for an organizer
	 * 
	 * @package Eventbrite
	 * @since 	1.6
	 * 
	 * @access 	private
	 */
	private function organizer_list_events()
	{
		$this->organizer_list_events->id = $this->event->meta->bpe_eventbrite_organizer_id;
	}
}
?>