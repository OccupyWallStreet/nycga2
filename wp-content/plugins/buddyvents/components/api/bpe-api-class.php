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

class Buddyvents_API_Response
{
	// can be either json or ics
	private $format	= false;
	
	// can be global, user or group
	private $action	= false;
	
	// can be future, past, category, ids, calendar, day, search
	private $method	= false;
	
	// can be either a user_id or username
	private $user = false;
	
	// can be either a group_id or a group_slug
	private $group = false;
	
	// events category id or slug
	private $category = false;
	
	// events ids seperated by comma
	private $ids = false;
	
	// date, YYYY-MM-DD
	private $day = false;
	
	// string, urlencoded string
	private $term = false;

	// string, urlencoded string
	private $location = false;

	// float
	private $latitude = false;

	// float
	private $longitude = false;

	// integer
	private $radius = false;

	// string
	private $meta = false;

	// string
	private $meta_key = false;

	// string
	private $operator = false;

	// string
	private $begin = false;

	// string
	private $end = false;
	
	// integer value, 1-12
	private $month = false;
	
	// integer value, e.g. 2011
	private $year = false;
	
	// string
	private $venue = false;

	// string
	private $venue_name = false;

	// integer value, defaults to 10, max 50
	private $limit = false;
	
	// boolean if a response should be given
	private $response = false;

	// holds any $_POST vars
	private $post = false;

	// WP username
	private $username =	false;

	// id of the current api user
	private $user_id = false;

	// api key
	private $apikey = false;

	// api row
	private $api = false;

	// url data
	private $data = array();

	// query results
	private $result = array();

	// class output
	private $output = '';

	// any xml data
	private $xml = '';

	// any ics file fata
	private $ics = '';
	
	/**
	 * PHP5 Constructor
	 * 
	 * @package API
	 * @since 	1.6
	 */
	public function __construct( $username = false, $api_key = false, $post_data = false )
	{
		$this->data	= $this->get_parameters();

		if( $this->is_post() )
		{
			$this->post( $post_data );
			$this->response = true;
		}
		else		
		{
			$this->get();

			if( $this->check_input() )
				$this->response = true;
		}

		$this->username = $username;
		$this->apikey = $api_key;
		$this->api = $this->populate();

		if( ! $checked = $this->check_apikey() )
			$this->response = false;
			
		if( $checked )
		{
			if( ! $this->check_hit_limit() )
				$this->response = false;
		}
		
		if( $this->response )
		{
			if( $this->is_post() )
				$this->process_post();
			
			else
				$this->response();
		}
			
		$this->render_output();
	}
	
	/**
	 * Is the request a post request
	 * 
	 * @package API
	 * @since 	1.6 
	 */
	private function is_post()
	{
		if( in_array( $this->data['method'], $this->post_methods() ) )
			return true;
			
		return false;
	}
	
	/**
	 * Setup a get request
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function get()
	{
		$this->format 		= ( empty( $this->data['format'] ) || ! in_array( $this->data['format'], array( 'json', 'xml', 'ics' ) ) ) ? 'json' : $this->data['format'];
		$this->method		= ( empty( $this->data['method'] ) || ! in_array( $this->data['method'], array( 'future', 'past', 'category', 'ids', 'calendar', 'day', 'search', 'attending' ) ) ) ? 'future' : $this->data['method'];
		$this->action		= ( empty( $this->data['action'] ) || ! in_array( $this->data['action'], array( 'global', 'user', 'group' ) ) ) ? 'global' : $this->data['action'];
		$this->user			= ( empty( $this->data['user']		) ) ? false : $this->format( 'user', $this->data['user'] );
		$this->group		= ( empty( $this->data['group']		) ) ? false : $this->format( 'group', $this->data['group'] );
		$this->ids			= ( empty( $this->data['ids']		) ) ? false : $this->format( 'ids', $this->data['ids'] );
		$this->category		= ( empty( $this->data['category']	) ) ? false : $this->format( 'category', $this->data['category'] );
		$this->location		= ( empty( $this->data['location']	) ) ? false : $this->format( 'location', $this->data['location'] );
		$this->venue_name	= ( empty( $this->data['venue_name']) ) ? false : $this->data['venue_name'];
		$this->venue		= ( empty( $this->data['venue']		) ) ? false : $this->data['venue'];
		$this->radius		= ( empty( $this->data['radius']	) ) ? false : (int)$this->data['radius'];
		$this->longitude	= ( empty( $this->data['longitude'] ) ) ? false : (float)$this->data['longitude'];
		$this->latitude		= ( empty( $this->data['latitude']	) ) ? false : (float)$this->data['latitude'];
		$this->meta			= ( empty( $this->data['meta']		) ) ? false : $this->data['meta'];
		$this->meta_key		= ( empty( $this->data['meta_key']	) ) ? false : $this->data['meta_key'];
		$this->operator		= ( empty( $this->data['operator']	) ) ? false : $this->data['operator'];
		$this->begin		= ( empty( $this->data['begin']		) ) ? false : $this->data['begin'];
		$this->end			= ( empty( $this->data['end']		) ) ? false : $this->data['end'];
		$this->day			= ( empty( $this->data['day']		) ) ? false : $this->data['day'];
		$this->month		= ( empty( $this->data['month']		) ) ? false : $this->data['month'];
		$this->year			= ( empty( $this->data['year']		) ) ? false : $this->data['year'];
		$this->term			= ( empty( $this->data['term']		) ) ? false : $this->format( 'term', $this->data['term'] );
		$this->limit		= ( empty( $this->data['limit']		) ) ? 10	: $this->format( 'limit', $this->data['limit'] );
	}
	
	/**
	 * Allowed post requests
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function post_methods()
	{
		return apply_filters( 'bpe_api_post_methods', array(
			'create',
			'update',
			'delete',
			'comment',
			'schedule',
			'document',
			'invite',
			'logo',
			'attendee',
			'ticket'
		) );
	}
	
	/**
	 * Setup a post request
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function post( $post_data )
	{
		$this->format 	= ( empty( $this->data['format'] ) || ! in_array( $this->data['format'], array( 'json', 'xml' ) ) ) ? 'json' : $this->data['format'];
		$this->method	= ( empty( $this->data['method'] ) || ! in_array( $this->data['method'], $this->post_methods() ) ) ? false : $this->data['method'];
		$this->post		= $this->{$this->format}( $post_data );
	}
	
	/**
	 * Route a post request
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function process_post()
	{
		if( ! $this->post )
		{
			$this->result = array( 'status' => 'failed', 'message' => 'No post vars' );
			$this->response = false;
			return false;
		}
		
		if( method_exists( &$this, $this->method ) )
			$this->{$this->method}();
	}
	
	/**
	 * Create an event
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function create()
	{
		$this->result = bpe_process_event_creation( $this->post, true, $this->user_id );
	}

	/**
	 * Update an event
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function update()
	{
		$result = bpe_get_events( array( 'ids' => $this->post['id'], 'future' => false, 'past' => false ) );
		$displayed_event = $result['events'][0];

		$this->result = bpe_process_event_edit( $this->post, $displayed_event, true );
	}

	/**
	 * Delete an event
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function delete()
	{
		$event = new Buddyvents_Events( $this->post['id'] );
		
		if( bpe_get_event_user_id( $event ) != $this->user_id )
		{
			$this->result = array( 'status' => 'failed', 'message' => 'Not authorized for this action' );
			return false;
		}
		
		if( $event->delete() )
		{
			add_action( 'bpe_delete_event_action', $this->post );

			$this->result = array( 'status' => 'success', 'message' => 'Event has been deleted', 'event_id' => $event->id );
		}
		else
			$this->result = array( 'status' => 'failed', 'message' => 'Event could not be deleted' );

	}
	
	/**
	 * Comment on an event
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function comment()
	{
		if( ! bp_is_active( 'activity' ) )
		{
			$this->result = array( 'status' => 'success', 'message' => 'Activity component is not active.' );
			return false;
		}
		
		if( empty( $this->post['comment'] ) )
		{
			$this->result = array( 'status' => 'failed', 'message' => 'No comment text provided' );
			return false;
		}
		
		if( empty( $this->post['id'] ) )
		{
			$this->result = array( 'status' => 'failed', 'message' => 'No event id provided' );
			return false;
		}
		
		bp_activity_add( array(
			'action' => sprintf( __( '%s posted a new event comment:', 'events' ), bp_core_get_userlink( $this->user_id ) ),
			'hide_sitewide' => true, 
			'component' => 'events', 
			'type' => 'event_comment', 
			'content' => $this->post['comment'], 
			'item_id' => $this->post['id']
		) );

		$this->result = array( 'status' => 'success', 'message' => 'Comment has been published' );
	}

	/**
	 * Create a schedule
	 * 
	 * @package API
	 * @since 	1.7
	 */
	private function schedule()
	{
		$this->result = array( 'status' => 'failed', 'message' => 'Creating a schedule is not supported yet' );
	}

	/**
	 * Create a document
	 * 
	 * @package API
	 * @since 	1.7
	 */
	private function document()
	{
		$this->result = array( 'status' => 'failed', 'message' => 'Uploading documents is not supported yet' );
	}

	/**
	 * Invite members
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function invite()
	{
		$this->result = array( 'status' => 'failed', 'message' => 'Member invitations are not supported yet' );
	}

	/**
	 * Attendee management
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function attendee()
	{
		$this->result = array( 'status' => 'failed', 'message' => 'Attenndee management is not supported yet' );
	}

	/**
	 * Upload a logo
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function logo()
	{
		$this->result = array( 'status' => 'failed', 'message' => 'Logo upload is not supported yet' );
	}
	
	/**
	 * Create a ticket
	 * 
	 * @package API
	 * @since 	2.0
	 */
	private function ticket()
	{
		$this->result = array( 'status' => 'failed', 'message' => 'Ticket creation is not supported yet' );
	}

	/**
	 * Setup a post request
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function json( $input )
	{
		return json_decode( stripslashes( $input ), true );
	}
	
	/**
	 * Get a row
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function populate()
	{
		global $wpdb, $bpe;
		
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->api} WHERE api_key = %s", $this->apikey ) );	
	}
	
	/**
	 * Check if an api key hits the limit
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function check_hit_limit()
	{
		$diff = time() - strtotime( $this->api->hit_date );
		
		if( $this->api->hits > bpe_get_option( 'restrict_api_hits' ) && $diff < bpe_get_option( 'restrict_api_timespan' ) )
		{
			if( $this->api->hits_over < 5 )
			{
				bpe_incriment_hits_over( $this->api->id );
				
				$this->result = array( 'status' => 'failed', 'message' => 'Hit limit per hour has been reached. Please cache the API results to avoid your API access getting revoked.' );
				return false;
			}
			else
			{
				bpe_reset_hits_over( $this->api->id );
				bpe_set_api_access( $this->api->id, 0 );
				
				$this->result = array( 'status' => 'failed', 'message' => 'Hit limit per hour has been reached 5 times. Your API key has been suspended.' );
				return false;				
			}
		}

		if( $diff > bpe_get_option( 'restrict_api_timespan' ) )
			bpe_reset_api_time_hits( $this->api->id );

		else
			bpe_incriment_api_hits( $this->api->id );
			
		return true;
	}
	
	/**
	 * Get the parameters
	 * Parameters always need to be pairs
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function get_parameters()
	{
		$no = count( (array) bp_action_variables() );
		
		$data = array();
		
		if( $no > 0 )
		{
			for( $i = 0; $i <= $no; $i++ )
			{
				$current = bp_action_variable( $i );
				$next = $i + 1;
				if( bp_action_variable( $next ) && ! empty( $current ) )
					$data[$current] = bp_action_variable( $next );
					
				$i++;
			}
		}
		
		return $data;		
	}

	/**
	 * Format the input
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function format( $context, $input )
	{
		global $wpdb, $bp;
		
		switch( $context )
		{
			case 'user':
				if( ! is_numeric( $input ) )
					$input = bp_core_get_userid( $input );
				break;

			case 'group':
				if( ! is_numeric( $input ) )
					$input = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bp->groups->table_name} WHERE slug = %s", $input ) );
				break;

			case 'ids':
				$input = trim( $input );
				break;

			case 'category':
				if( ! is_numeric( $input ) )
					$input = bpe_get_catid_from_slug( $input );
				break;

			case 'term': case 'location':
				$input = urldecode( $input );
				break;

			case 'limit':
				$input = (int)$input;
				if( $input > 50 )
					$input = 50;
				break;
		}
		
		return $input;
	}
	
	/**
	 * Check the api key
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function check_apikey()
	{
		if( ! $this->username || ! $this->apikey )
		{
			$this->result = array( 'status' => 'failed', 'message' => 'Invalid login/API key' );
			return false;
		}			
		
		$this->user_id = bp_core_get_userid( $this->username );
		
		if( $this->api->api_key == $this->apikey && $this->api->user_id == $this->user_id )
			return true;
			
		$this->result = array( 'status' => 'failed', 'message' => 'Invalid login/API key' );
		return false;
	}
	
	/**
	 * Check for any errors
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function check_input()
	{
		if( $this->action == 'user' && ! $this->user )
		{
		   $this->result = array( 'status' => 'failed', 'message' => 'No user provided' );
		   return false;
		}

		if( $this->action == 'group' && ! $this->group )
		{
		   $this->result = array( 'status' => 'failed', 'message' => 'No group provided' );
		   return false;
		}

		if( $this->method == 'category' && ! $this->category )
		{
		   $this->result = array( 'status' => 'failed', 'message' => 'No category provided' );
		   return false;
		}

		if( $this->method == 'ids' && ! $this->ids )
		{
		   $this->result = array( 'status' => 'failed', 'message' => 'No event ids provided' );
		   return false;
		}

		if( $this->method == 'calendar' && ( ! $this->month || ! $this->year ) )
		{
		   $this->result = array( 'status' => 'failed', 'message' => 'No month and/or year provided' );
		   return false;
		}

		if( $this->method == 'day' && ! $this->day )
		{
		   $this->result = array( 'status' => 'failed', 'message' => 'No day provided' );
		   return false;
		}

		if( $this->method == 'search' && ! $this->term )
		{
		   $this->result = array( 'status' => 'failed', 'message' => 'No search term provided' );
		   return false;
		}

		if( $this->method == 'attending' && ! $this->user )
		{
		   $this->result = array( 'status' => 'failed', 'message' => 'No user provided' );
		   return false;
		}

		if( $this->location  && ! $this->radius || ! $this->location  && $this->radius )
		{
		   $this->result = array( 'status' => 'failed', 'message' => 'Radius and location need to be provided together' );
		   return false;
		}

		if( $this->longitude && ! $this->latitude || ! $this->longitude && $this->latitude )
		{
		   $this->result = array( 'status' => 'failed', 'message' => 'Longitude and latitude need to be provided together' );
		   return false;
		}

		if( $this->meta && ! $this->meta_key || ! $this->meta && $this->meta_key )
		{
		   $this->result = array( 'status' => 'failed', 'message' => 'Meta and meta_key need to be provided together' );
		   return false;
		}

		if( $this->operator && ! $this->meta_key || $this->operator && $this->meta )
		{
		   $this->result = array( 'status' => 'failed', 'message' => 'Meta and meta_key need to be provided if operator is supplied.' );
		   return false;
		}

		if( $this->begin && ! $this->end || ! $this->begin && $this->end )
		{
		   $this->result = array( 'status' => 'failed', 'message' => 'Begin and end need to be provided together' );
		   return false;
		}
		
		return true;
	}

	/**
	 * Get the results
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function response()
	{
		if( $this->method == 'attending' )
		{
			$this->ids = bpe_get_user_events( $this->user );
			$this->user = 0;
			if( ! $this->ids ) $this->ids = -1;
		}
        
		$this->result = bpe_get_events( array(
			'user_id'		=> $this->user,
			'group_id'		=> $this->group,
			'ids'			=> $this->ids,
			'category'		=> $this->category,
			'location'		=> $this->location,
			'radius'		=> $this->radius,
			'longitude'		=> $this->longitude,
			'latitude'		=> $this->latitude,
			'venue'			=> $this->venue,
			'venue_name'	=> $this->venue_name,
			'day'			=> $this->day,
			'month'			=> $this->month,
			'meta'			=> $this->meta,
			'meta_key'		=> $this->meta_key,
			'operator'		=> $this->operator,
			'begin'			=> $this->begin,
			'end'			=> $this->end,
			'year'			=> $this->year,
			'future'		=> $this->future,
			'past'			=> $this->past,
			'search_terms'	=> $this->term,
			'per_page'		=> $this->limit,
			'page'			=> 1,
			'is_spam'		=> 0,
			'approved'		=> 1
		) );
		
		if( ! empty( $this->result['events'] ) )
		{
			// unset/unserialize some data
			foreach( $this->result['events'] as $key => $event )
			{
				if( $this->user != $event->user_id )
				{
					unset( $event->invitations );
					unset( $event->attendee_ids );
					unset( $event->maybe_attendee_ids );
					unset( $event->admin_ids );
					unset( $event->organizer_ids );
					unset( $event->not_attendee_ids );
					unset( $event->is_spam );
					unset( $event->approved );
				}
	
				$event->address = maybe_unserialize( $event->address );
			}
			
			$this->result['domain'] = bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/';
			$this->result['status'] = 'success';
			$this->result['total'] = ( $this->result['total'] < $this->limit ) ? $this->result['total'] : $this->limit;
		}
		else
			$this->result = array( 'status' => 'failed', 'message' => 'No results found' );
			
		
	}
	
	/**
	 * Read in all the output
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function render_output()
	{
		global $wpdb;
		
		switch( $this->format )
		{
			case 'ics':
				header( "Content-type:text/calendar" );
				$this->output = $this->ics();
				break;

			case 'json': default:
				header( "Content-type:application/json;charset=". $wpdb->charset );
				$this->output = json_encode( $this->result );
				break;
		}
	}

	/**
	 * Transform an array or an object into valid ics format
	 * 
	 * @package API
	 * @since 	1.6
	 */
	private function ics()
	{
		require_once( EVENT_ABSPATH .'components/core/bpe-ical.php' );
		$this->ics = new Buddyvents_iCal( $this->result['events'], false );
	}
	
	/**
	 * PHP5 style destructor and will run when the class is finished.
	 * 
	 * @package API
	 * @since 	1.6
	 */
	function __destruct()
	{
		echo $this->output;
	}
}
?>