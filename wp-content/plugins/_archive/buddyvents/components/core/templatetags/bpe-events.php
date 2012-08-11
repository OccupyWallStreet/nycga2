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

class Buddyvents_Event_Template
{
	var $current_event = -1;
	var $event_count;
	var $events;
	var $event;

	var $in_the_loop;

	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $total_event_count;

	function __construct( $ids, $user_id, $group_id, $name, $slug, $start_date, $start_time, $end_date, $end_time, $timezone, $day, $month, $year, $future, $past, $location, $venue_name, $venue, $map, $radius, $longitude, $latitude, $public, $rsvp, $category, $meta, $meta_key, $operator, $begin, $end, $page, $per_page, $max, $search_terms, $populate_extras, $sort, $restrict, $spam, $approved, $group_approved, $plus_days )
	{
		$this->pag_page = isset( $_REQUEST['epage']  ) ? intval( $_REQUEST['epage'] 	) : $page;
		$this->pag_num  = isset( $_REQUEST['num'] 	) ? intval( $_REQUEST['num'] 	) : $per_page;

		$this->events = bpe_get_events( array( 
			'ids' 				=> $ids,
			'user_id' 			=> $user_id,
			'group_id' 			=> $group_id,
			'name' 				=> $name,
			'slug' 				=> $slug,
			'start_date' 		=> $start_date,
			'start_time' 		=> $start_time,
			'end_date' 			=> $end_date,
			'end_time' 			=> $end_time,
			'timezone' 			=> $timezone,
			'day' 				=> $day,
			'month' 			=> $month,
			'year' 				=> $year,
			'future' 			=> $future,
			'past' 				=> $past,
			'location' 			=> $location,
			'venue_name' 		=> $venue_name,
			'venue' 			=> $venue,
			'map' 				=> $map,
			'radius' 			=> $radius,
			'longitude' 		=> $longitude,
			'latitude' 			=> $latitude,
			'public' 			=> $public,
			'rsvp' 				=> $rsvp,
			'category' 			=> $category,
			'meta' 				=> $meta,
			'meta_key' 			=> $meta_key,
			'operator' 			=> $operator,
			'begin' 			=> $begin,
			'end' 				=> $end,
			'per_page' 			=> $this->pag_num,
			'page' 				=> $this->pag_page,
			'search_terms' 		=> $search_terms,
			'populate_extras' 	=> $populate_extras,
			'sort' 				=> $sort,
			'restrict' 			=> $restrict,
			'spam' 				=> $spam,
			'approved' 		 	=> $approved,
			'group_approved' 	=> $group_approved,
			'plus_days'			=> $plus_days
		) );

		if( ! $max || $max >= absint( $this->events['total'] ) )
			$this->total_event_count = absint( $this->events['total'] );
		else
			$this->total_event_count = absint( $max );

		$this->events = $this->events['events'];

		if( $max )
		{
			if( $max >= count( $this->events ) )
				$this->event_count = count( $this->events );
			else
				$this->event_count = (int)$max;
		}
		else
			$this->event_count = count( $this->events );
		
		$this->pag_links = paginate_links( array(
			'base' 		=> add_query_arg( array( 'epage' => '%#%' ) ),
			'format' 	=> '',
			'total' 	=> ceil( $this->total_event_count / $this->pag_num ),
			'current' 	=> $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' 	=> 3
		));
	}

	function has_events()
	{
		if( $this->event_count )
			return true;

		return false;
	}

	function next_event()
	{
		$this->current_event++;
		$this->event = $this->events[$this->current_event];

		return $this->event;
	}

	function rewind_events()
	{
		$this->current_event = -1;
		
		if( $this->event_count > 0 )
		{
			$this->event = $this->events[0];
		}
	}

	function events()
	{
		if( $this->current_event + 1 < $this->event_count )
		{
			return true;
		}
		elseif( $this->current_event + 1 == $this->event_count )
		{
			do_action( 'bpe_events_loop_end' );
			$this->rewind_events();
		}

		$this->in_the_loop = false;
		return false;
	}

	function the_event()
	{
		$this->in_the_loop = true;
		$this->event = $this->next_event();

		if( $this->current_event == 0 )
			do_action( 'bpe_events_loop_start' );
	}

}

function bpe_has_events( $args = '' )
{
	global $event_template, $bp, $bpe, $eventloop_args;
	
	$search_terms 	= ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
	$location 		= ( isset( $_REQUEST['l'] ) ) ? $_REQUEST['l'] : false;
	$radius 		= ( isset( $_REQUEST['r'] ) ) ? $_REQUEST['r'] : false;
	$user_id 		= bp_displayed_user_id();
	$group_id 		= ( bp_is_active( 'groups' ) ) ? bp_get_current_group_id() : false;
	$sort 			= 'start_date_asc';
	$future 		= true;
	$past 			= false;
	$venue_name 	= false;
	$venue 			= false;
	$day 			= false;
	$month 			= false;
	$year 			= false;
	$category 		= false;
	$slug 			= false;
	$ids 			= false;
	$timezone 		= false;
	$group_approved = true;
	
	if( bpe_is_event_search_results() ||
		bpe_is_event_timezone() 	  || 
		bpe_is_event_venue() 		  || 
		bpe_is_event_month_archive()  || 
		bpe_is_event_day_archive() 	  || 
		bpe_is_event_category() 	  || 
		bpe_is_groups() && bp_is_action_variable( bpe_get_option( 'day_slug' ), 0 ) || 
		bpe_is_groups() && bp_is_action_variable( bpe_get_option( 'month_slug' ), 0 ) || 
		bpe_is_groups() && bp_is_action_variable( bpe_get_option( 'timezone_slug' ), 0 ) || 
		bpe_is_groups() && bp_is_action_variable( bpe_get_option( 'venue_slug' ), 0 )
		)
	{
	 	$sort 	= 'start_date_desc';
		$future = false;
		$past 	= false;
	}
	
	$sort = ( empty( $_GET['sort'] ) ) ? $sort : $_GET['sort'];
	
	if( bpe_is_member_archive() || bpe_is_events_archive() || bp_is_action_variable( bpe_get_option( 'archive_slug' ), 0 ) && ! in_array( 'archive', (array)bpe_get_option( 'deactivated_tabs' ) ) )
	{
		$future = false; 
		$past 	= true;
 	}
	
	if( bpe_is_single_event() )
	{
		$future = false;
		$past 	= false;
		$slug 	= bpe_get_ev_slug();
	}
	
	if( bpe_is_event_day_archive() )
		$day = bp_action_variable( 0 );
		
	elseif( bpe_is_groups() && bp_is_action_variable( bpe_get_option( 'day_slug' ), 0 ) )
		$day = bp_action_variable( 1 );
		
	if( bpe_is_event_month_archive() )
	{
		$month 	= bp_action_variable( 0 );
		$year 	= bp_action_variable( 1 );
	}
	elseif( bpe_is_groups() && bp_is_action_variable( bpe_get_option( 'month_slug' ), 0 ) )
	{
		$month 	= bp_action_variable( 1 );
		$year 	= bp_action_variable( 2 );
	}
	
	if( bpe_is_event_category() || bpe_is_groups() && bp_is_action_variable( bpe_get_option( 'category_slug' ), 0 ) && ! empty( $bp->action_variables[1] ) )
		$category = bpe_get_catid_from_slug( bp_action_variable( 0 ) );
		
	if( bpe_is_groups() && bp_is_action_variable( bpe_get_option( 'approve_slug' ), 0 ) )
	{
		$future 		= false;
		$past 			= false;
		$group_approved = false;
	}
		
	if( bpe_is_event_timezone() )
		$timezone = bpe_get_config( 'timezones', bp_action_variable( 0 ) );
	elseif( bp_is_action_variable( bpe_get_option( 'timezone_slug' ), 0 ) && bpe_is_groups() )
		$timezone = bpe_get_config( 'timezones', bp_action_variable( 1 ) );

	if( bpe_is_event_venue() )
		$venue = bpe_get_config( 'venues', bp_action_variable( 0 ) );
	elseif( bp_is_action_variable( bpe_get_option( 'venue_slug' ), 0 ) && bpe_is_groups() )
		$venue = bpe_get_config( 'venues', bp_action_variable( 1 ) );
	
	if( isset( $_GET['prox'] ) )
	{
		$ids = bpe_proximity_event_ids( $_GET['prox'] );
		if( ! $ids ) $ids = -1;
	}
	
	if( bp_is_current_action( bpe_get_option( 'attending_slug' ) ) )
	{
		$user_id = 0;
		$ids 	 = bpe_get_user_events();
		if( ! $ids ) $ids = -1;
	}
	
	$defaults = array(
		'ids' 				=> $ids,
		'user_id' 			=> $user_id,
		'group_id' 			=> $group_id,
		'name' 				=> false,
		'slug' 				=> $slug,
		'start_date' 		=> false,
		'start_time' 		=> false,
		'end_date' 			=> false,
		'end_time' 			=> false,
		'timezone' 			=> $timezone,
		'day' 				=> $day,
		'month' 			=> $month,
		'year' 				=> $year,
		'future' 			=> $future,
		'past' 				=> $past,
		'location' 			=> $location,
		'venue_name' 		=> $venue_name,
		'venue' 			=> $venue,
		'map' 				=> false,
		'radius' 			=> $radius,
		'longitude' 		=> false,
		'latitude' 			=> false,
		'public' 			=> false,
		'rsvp' 				=> false,
		'category' 			=> $category,
		'meta' 				=> false,
		'meta_key' 			=> false,
		'operator' 			=> '=',
		'begin' 			=> false,
		'end' 				=> false,
		'page' 				=> 1,
		'per_page' 			=> bpe_get_view_per_page(),
		'max' 				=> false,
		'search_terms' 		=> $search_terms,
		'populate_extras' 	=> true,
		'sort' 				=> $sort,
		'restrict' 			=> true,
		'spam' 				=> 0,
		'approved' 			=> 1,
		'group_approved' 	=> $group_approved,
		'plus_days'			=> 0
	);

	$r = wp_parse_args( $args, $defaults );

	if( isset( $eventloop_args ) && count( $eventloop_args ) > 0 )
		$r = wp_parse_args( $eventloop_args, $r );
	
	extract( $r );

	$event_template = new Buddyvents_Event_Template( $ids, (int)$user_id, (int)$group_id, $name, $slug, $start_date, $start_time, $end_date, $end_time, $timezone, $day, $month, $year, $future, $past, $location, $venue_name, $venue, (bool)$map, $radius, $longitude, $latitude, (int)$public, (int)$rsvp, (int)$category, $meta, $meta_key, $operator, $begin, $end, (int)$page, (int)$per_page, (int)$max, $search_terms, (bool)$populate_extras, $sort, (bool)$restrict, $spam, (int)$approved, (int)$group_approved, (int)$plus_days );

	return apply_filters( 'bpe_has_events', $event_template->has_events(), &$event_template );
}

function bpe_events()
{
	global $event_template;

	return $event_template->events();
}

function bpe_the_event()
{
	global $event_template;

	return $event_template->the_event();
}

function bpe_get_events_count()
{
	global $event_template;

	return $event_template->event_count;
}

function bpe_get_total_events_count()
{
	global $event_template;

	return $event_template->total_event_count;
}

/**
 * Pagination links
 * @since 1.0
 */
function bpe_events_pagination_links()
{
	echo bpe_get_events_pagination_links();
}
	function bpe_get_events_pagination_links()
	{
		global $event_template;
	
		if( ! empty( $event_template->pag_links ) )
			return sprintf( __( 'Page: %s', 'events' ), $event_template->pag_links );
	}

/**
 * Pagination count
 * @since 1.0
 */
function bpe_events_pagination_count()
{
	echo bpe_get_events_pagination_count();
}
	function bpe_get_events_pagination_count()
	{
		global $bp, $event_template;
	
		$from_num = bp_core_number_format( intval( ( $event_template->pag_page - 1 ) * $event_template->pag_num ) + 1 );
		$to_num = bp_core_number_format( ( $from_num + ( $event_template->pag_num - 1 ) > $event_template->total_event_count ) ? $event_template->total_event_count : $from_num + ( $event_template->pag_num - 1 ) );
		$total = bp_core_number_format( $event_template->total_event_count );
	
		return apply_filters( 'bpe_get_events_pagination_count', sprintf( __( 'Viewing event %1$s to %2$s (of %3$s events)', 'events' ), $from_num, $to_num, $total ) );
	}

/**
 * Event id
 * @since 1.0
 */
function bpe_event_id( $e = false )
{
	echo bpe_get_event_id( $e );
}
	function bpe_get_event_id( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->id ) )
			return false;

		return apply_filters( 'bpe_get_event_id', $event->id, $event );
	}

/**
 * Event user_id
 * @since 1.0
 */
function bpe_event_user_id( $e = false )
{
	echo bpe_get_event_user_id( $e );
}
	function bpe_get_event_user_id( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->user_id ) )
			return false;

		return apply_filters( 'bpe_get_event_user_id', $event->user_id, $event );
	}

/**
 * Event user avatar
 * @since 1.1
 */
function bpe_event_user_avatar( $args = '' )
{
	echo apply_filters( 'bpe_event_user_avatar', bpe_get_event_user_avatar( $args ) );
}
	function bpe_get_event_user_avatar( $args = '' )
	{
		$defaults = array(
			'e' 	=> false,
			'type' 	=> 'thumb',
			'width' => false,
			'height'=> false,
			'class' => 'avatar',
			'id' 	=> false,
			'alt' 	=> __( 'Event Admin Avatar', 'events' )
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );
		
		$email = bp_core_get_user_email( bpe_get_event_user_id( $e ) );
		$domain = bp_core_get_user_domain( bpe_get_event_user_id( $e ) );

		if( ! empty( $domain ) )
			return '<a href="'. esc_url( $domain ) .'">'. apply_filters( 'bpe_get_event_user_avatar', bp_core_fetch_avatar( array( 'item_id' => bpe_get_event_user_id( $e ), 'type' => $type, 'alt' => $alt, 'css_id' => $id, 'class' => $class, 'width' => $width, 'height' => $height, 'email' => $email ) ) ) .'</a>';

		else
			return apply_filters( 'bpe_get_event_user_avatar', bp_core_fetch_avatar( array( 'item_id' => bpe_get_event_user_id( $e ), 'type' => $type, 'alt' => $alt, 'css_id' => $id, 'class' => $class, 'width' => $width, 'height' => $height, 'email' => $email ) ) );
	}

/**
 * Event group_id
 * @since 1.0
 */
function bpe_event_group_id( $e = false )
{
	echo bpe_get_event_group_id( $e );
}
	function bpe_get_event_group_id( $e =  false )
	{
		global $event_template;
		
		if( ! bp_is_active( 'groups' ) )
			return false;

		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->group_id ) )
			return false;

		return apply_filters( 'bpe_get_event_group_id', $event->group_id, $event );
	}

/**
 * Event name
 * @since 1.0
 */
function bpe_event_name( $e = false )
{
	echo bpe_get_event_name( $e );
}
	function bpe_get_event_name( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->name ) )
			return false;

		return apply_filters( 'bpe_events_get_events_name', $event->name, $event );
	}

/**
 * Event slug
 * @since 1.0
 */
function bpe_event_slug( $e = false )
{
	echo bpe_get_event_slug( $e );
}
	function bpe_get_event_slug( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;
		
		if( ! isset( $event->slug ) )
			return false;

		return apply_filters( 'bpe_get_event_slug', $event->slug, $event );
	}

/**
 * Event link
 * @since 1.0
 */
function bpe_event_link( $e = false )
{
	echo bpe_get_event_link( $e );
}
	function bpe_get_event_link( $e = false )
	{
		$slug = ( bpe_get_event_end_date_raw( $e ) .' '. bpe_get_event_end_time_raw( $e ) < bp_core_current_time() ) ? bpe_get_option( 'archive_slug' ) : bpe_get_option( 'active_slug' );

		return apply_filters( 'bpe_get_event_link', esc_url( bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. $slug .'/'. bpe_get_event_slug( $e ) .'/' ), $e );
	}

/**
 * Event icalendar link
 * @since 1.0
 */
function bpe_event_ical_link( $e = false )
{
	echo bpe_get_event_ical_link( $e );
}
	function bpe_get_event_ical_link( $e = false )
	{
		$slug = ( bpe_get_event_end_date_raw( $e ) .' '. bpe_get_event_end_time_raw( $e ) < bp_core_current_time() ) ? bpe_get_option( 'archive_slug' ) : bpe_get_option( 'active_slug' );
		
		return apply_filters( 'bpe_get_event_ical_link', esc_url( bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. $slug .'/'. bpe_get_event_slug( $e ) .'/ical/'. bpe_get_event_slug( $e ) .'.ics' ), $e );
	}

/**
 * Event description
 * @since 1.0
 */
function bpe_event_description( $e = false )
{
	echo bpe_get_event_description( $e );
}
	function bpe_get_event_description( $e = false )
	{
		return apply_filters( 'bpe_events_get_events_description', bpe_get_event_description_raw( $e ), $e );
	}

function bpe_event_description_raw( $e = false )
{
	echo bpe_get_event_description_raw( $e );
}
	function bpe_get_event_description_raw( $e = false )
	{
		global $event_template;

		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->description ) )
			return false;
		
		return apply_filters( 'bpe_get_raw_event_description', $event->description, $event );
	}

function bpe_event_description_excerpt( $e = false, $oembed = true )
{
	echo bpe_get_event_description_excerpt( $e, $oembed );
}
	function bpe_get_event_description_excerpt( $e = false, $oembed = true )
	{
		if( $oembed )
			return apply_filters( 'bpe_events_get_events_description_excerpt', bpe_get_event_description_excerpt_raw( $e ), $e );
		else
			return apply_filters( 'bpe_events_get_raw_events_description_excerpt', bpe_get_event_description_excerpt_raw( $e ), $e );
	}

/**
 * @since 1.2
 */
function bpe_event_description_excerpt_raw( $e = false )
{
	echo bpe_get_event_description_excerpt_raw( $e );
}
	function bpe_get_event_description_excerpt_raw( $e = false )
	{
		return apply_filters( 'bpe_get_raw_event_description_excerpt', bp_create_excerpt( bpe_get_event_description_raw( $e ) ), $e );
	}

/**
 * Event category
 * @since 1.0
 */
function bpe_event_category( $e = false )
{
	echo bpe_get_event_category( $e );
}
	function bpe_get_event_category( $e = false )
	{
		global $bp;
		
		$link = '';

		if( bp_displayed_user_id() )
			$link = bp_displayed_user_domain() . bpe_get_base( 'slug' ) .'/';

		if( bp_is_active( 'groups' ) )
			if( bp_get_current_group_id() )
				$link = bp_get_group_permalink( groups_get_current_group() ) .bpe_get_base( 'slug' ) .'/';

		if( empty( $link ) )
			$link = bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/';

		return apply_filters( 'bpe_get_event_category', '<a href="'. esc_url( $link . bpe_get_option( 'category_slug' ) .'/'. bpe_get_event_category_slug( $e ) .'/' ) .'">'. apply_filters( 'bpe_events_get_events_cat_name', bpe_get_event_category_name( $e ) ) .'</a>' );
	}

/**
 * Event category name
 * @since 1.0
 */
function bpe_event_category_name( $e = false )
{
	echo bpe_get_event_category_name( $e );
}
	function bpe_get_event_category_name( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;
		
		if( ! isset( $event->cat_name ) )
			return false;

		return apply_filters( 'bpe_events_get_events_cat_name', $event->cat_name, $event );
	}

/**
 * Event category id
 * @since 1.0
 */
function bpe_event_category_id( $e = false )
{
	echo bpe_get_event_category_id( $e );
}
	function bpe_get_event_category_id( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->cat_id ) )
			return false;

		return apply_filters( 'bpe_get_event_category_id', $event->cat_id, $event );
	}

/**
 * Event category slug
 * @since 1.0
 */
function bpe_event_category_slug( $e = false )
{
	echo bpe_get_event_category_slug( $e );
}
	function bpe_get_event_category_slug( $e = false )
	{
		global $event_template;

		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->cat_slug ) )
			return false;

		return apply_filters( 'bpe_get_event_category_slug', $event->cat_slug, $event );
	}

/**
 * Event venue name
 * @since 1.7.6
 */
function bpe_event_venue_name( $e = false )
{
	echo bpe_get_event_venue_name( $e );
}
	function bpe_get_event_venue_name( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->venue_name ) )
			return false;

		return apply_filters( 'bpe_events_get_events_venue_name', $event->venue_name, $event );
	}

/**
 * Event location
 * @since 1.0
 */
function bpe_event_location( $e = false )
{
	echo bpe_get_event_location( $e );
}
	function bpe_get_event_location( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->location ) )
			return false;

		return apply_filters( 'bpe_events_get_events_location', $event->location, $event );
	}

/**
 * Event location
 * @since 1.0
 */
function bpe_event_location_link( $e = false )
{
	echo bpe_get_event_location_link( $e );
}
	function bpe_get_event_location_link( $e = false )
	{
		global $event_template, $bp, $bpe;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( bpe_get_event_venue_name( $event ) )
		{
			$title = bpe_get_event_venue_name( $event );
			$title_tag = ( bpe_get_event_location( $event ) ) ? ' title="'. bpe_get_event_location( $event ) .'"' : '';
		}
		elseif( bpe_get_event_location( $event ) )
		{
			$title = bpe_get_event_location( $event );
			$title_tag = '';
		}
	
		$slug = sanitize_title_with_dashes( $title );
		
		$link = '';
		
		if( bp_displayed_user_id() )
			$link = bp_displayed_user_domain() .bpe_get_base( 'slug' ) .'/';

		if( bp_is_active( 'groups' ) )
			if( bp_get_current_group_id() )
				$link = bp_get_group_permalink( groups_get_current_group() ) . bpe_get_base( 'slug' ) .'/';

		if( empty( $link ) )
			$link = bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/';
			
		return apply_filters( 'bpe_get_event_location_link', '<a href="'. esc_url( $link . bpe_get_option( 'venue_slug' ) .'/'. $slug .'/' ) .'"'. $title_tag .'>'. apply_filters( 'bpe_events_get_events_location', $title ) .'</a>' );
	}

/**
 * Event url
 * @since 1.7
 */
function bpe_event_url( $e = false )
{
	echo bpe_get_event_url( $e );
}
	function bpe_get_event_url( $e = false )
	{
		return apply_filters( 'bpe_events_get_events_url', bpe_get_event_url_raw( $e ), $e );
	}

/**
 * Event url raw
 * @since 1.7
 */
function bpe_event_url_raw( $e = false )
{
	echo bpe_get_event_url_raw( $e );
}
	function bpe_get_event_url_raw( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->url ) )
			return false;

		return apply_filters( 'bpe_get_raw_event_url', $event->url, $event );
	}

/**
 * Event longitude
 * @since 1.0
 */
function bpe_event_longitude( $e = false )
{
	echo bpe_get_event_longitude( $e );
}
	function bpe_get_event_longitude( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->longitude ) )
			return false;

		return apply_filters( 'bpe_get_event_longitude', $event->longitude, $event );
	}

/**
 * Event latitude
 * @since 1.0
 */
function bpe_event_latitude( $e = false )
{
	echo bpe_get_event_latitude( $e );
}
	function bpe_get_event_latitude( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->latitude ) )
			return false;

		return apply_filters( 'bpe_get_event_latitude', $event->latitude, $event );
	}

/**
 * Event attendees
 * @since 1.0
 */
function bpe_event_attendees( $e = false )
{
	echo bpe_get_event_attendees( $e );
}
	function bpe_get_event_attendees( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->attendees ) )
			return false;

		return apply_filters( 'bpe_get_event_attendees', sprintf( _n( '%d Attendee', '%d Attendees', $event->attendees, 'events' ), (int)$event->attendees ), $event );
	}

/**
 * Event start_date
 * @since 1.0
 */
function bpe_event_start_date( $e = false )
{
	echo bpe_get_event_start_date( $e );
}
	function bpe_get_event_start_date( $e = false )
	{
		return apply_filters( 'bpe_get_event_start_date', mysql2date( bpe_get_option( 'date_format' ), bpe_get_event_start_date_raw( $e ), true ), $e );
	}

/**
 * Event start_date
 * @since 1.0
 */
function bpe_event_start_date_raw( $e = false )
{
	echo bpe_get_event_start_date_raw( $e );
}
	function bpe_get_event_start_date_raw( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->start_date ) )
			return false;

		return apply_filters( 'bpe_get_raw_event_start_date', $event->start_date, $event );
	}

/**
 * Event end_date
 * @since 1.0
 */
function bpe_event_end_date( $e = false )
{
	echo bpe_get_event_end_date( $e );
}
	function bpe_get_event_end_date( $e = false )
	{
		return apply_filters( 'bpe_get_event_end_date', mysql2date( bpe_get_option( 'date_format' ), bpe_get_event_end_date_raw( $e ), true ), $e );
	}

/**
 * Event end_date
 * @since 1.0
 */
function bpe_event_end_date_raw( $e = false )
{
	echo bpe_get_event_end_date_raw( $e );
}
	function bpe_get_event_end_date_raw( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->end_date ) )
			return false;
		
		return apply_filters( 'bpe_get_raw_event_end_date', $event->end_date, $event );
	}

/**
 * Event start_time
 * @since 1.0
 */
function bpe_event_start_time( $e = false )
{
	echo bpe_get_event_start_time( $e );
}
	function bpe_get_event_start_time( $e = false )
	{
		return apply_filters( 'bpe_get_event_start_time', ( bpe_get_option( 'clock_type' ) == 24 ) ? gmdate( 'H:i', strtotime( bpe_get_event_start_time_raw( $e ) ) ) : gmdate( 'g:i a', strtotime( bpe_get_event_start_time_raw( $e ) ) ) );
	}

function bpe_event_start_time_raw( $e = false )
{
	echo bpe_get_event_start_time_raw( $e );
}
	function bpe_get_event_start_time_raw( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( bpe_get_event_all_day( $event ) == 1 )
			return false;
			
		if( ! isset( $event->start_time ) )
			return false;

		return apply_filters( 'bpe_get_raw_event_start_time', $event->start_time, $event );
	}

/**
 * Event end_time
 * @since 1.0
 */
function bpe_event_end_time( $e = false )
{
	echo bpe_get_event_end_time( $e );
}
	function bpe_get_event_end_time( $e = false )
	{
		return apply_filters( 'bpe_get_event_end_time', ( bpe_get_option( 'clock_type' ) == 24 ) ? gmdate( 'H:i', strtotime( bpe_get_event_end_time_raw( $e ) ) ) : gmdate( 'g:i a', strtotime( bpe_get_event_end_time_raw( $e ) ) ) );
	}

function bpe_event_end_time_raw( $e = false )
{
	echo bpe_get_event_end_time_raw( $e );
}
	function bpe_get_event_end_time_raw( $e =  false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( bpe_get_event_all_day( $event ) == 1 )
			return false;
			
		if( ! isset( $event->end_time ) )
			return false;

		return apply_filters( 'bpe_get_raw_event_end_time', $event->end_time, $event );
	}

/**
 * Event date_created
 * @since 1.0
 */
function bpe_event_date_created( $e = false )
{
	echo bpe_get_event_date_created( $e );
}
	function bpe_get_event_date_created( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->date_created ) )
			return false;

		return apply_filters( 'bpe_get_event_date_created', $event->date_created, $event );
	}

/**
 * Event date_created for backend
 * @since 1.2.4
 */
function bpe_event_date_created_be( $e = false )
{
	echo bpe_get_event_date_created_be( $e );
}
	function bpe_get_event_date_created_be( $e = false )
	{
		return apply_filters( 'bpe_get_event_date_created_be', mysql2date( "Y-m-d H:i:s", bpe_get_event_date_created( $e ), true ) );
	}

/**
 * Event public
 * @since 1.0
 */
function bpe_event_public( $e = false )
{
	echo bpe_get_event_public( $e );
}
	function bpe_get_event_public( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->public ) )
			return false;

		return apply_filters( 'bpe_get_event_public', $event->public, $event );
	}

/**
 * Event enable rsvp
 * @since 1.6
 */
function bpe_event_rsvp( $e = false )
{
	echo bpe_get_event_rsvp( $e );
}
	function bpe_get_event_rsvp( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->rsvp ) )
			return false;

		return apply_filters( 'bpe_get_event_rsvp', $event->rsvp, $event );
	}

/**
 * Event enable all_day
 * @since 1.7
 */
function bpe_event_all_day( $e = false )
{
	echo bpe_get_event_all_day( $e );
}
	function bpe_get_event_all_day( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->all_day ) )
			return false;

		return apply_filters( 'bpe_get_event_all_day', $event->all_day, $event );
	}

/**
 * Event timezone
 * @since 1.7
 */
function bpe_event_timezone( $e = false )
{
	echo bpe_get_event_timezone( $e );
}
	function bpe_get_event_timezone( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->timezone ) )
			return false;
		
		$slug = sanitize_title_with_dashes( str_replace( '/', '-', bpe_get_event_timezone_raw( $event ) ) );
		
		if( bp_displayed_user_id() )
			$link = bp_displayed_user_domain() .bpe_get_base( 'slug' ) .'/';

		elseif( bp_get_current_group_id() )
			$link = bp_get_group_permalink( groups_get_current_group() ) .bpe_get_base( 'slug' ) .'/';

		else
			$link = bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/';

		return apply_filters( 'bpe_get_event_timezone', '<a href="'. esc_url( $link . bpe_get_option( 'timezone_slug' ) .'/'. $slug .'/' ) .'">'. bpe_get_event_timezone_raw( $event ) .'</a>', $event );
	}

function bpe_event_timezone_raw( $e = false )
{
	echo bpe_get_event_timezone_raw( $e );
}
	function bpe_get_event_timezone_raw( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->timezone ) )
			return false;

		return apply_filters( 'bpe_get_raw_event_timezone', $event->timezone, $event );
	}

/**
 * Event recurrent
 * @since 1.0
 */
function bpe_event_recurrent( $e = false )
{
	echo bpe_get_event_recurrent( $e );
}
	function bpe_get_event_recurrent( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->recurrent ) )
			return false;

		return apply_filters( 'bpe_get_event_recurrent', $event->recurrent, $event );
	}

/**
 * Event is_spam
 * @since 1.2.4
 */
function bpe_event_is_spam( $e = false )
{
	echo bpe_get_event_is_spam( $e );
}
	function bpe_get_event_is_spam( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;
		

		return apply_filters( 'bpe_get_event_is_spam', ( empty( $event->is_spam ) ) ? __( 'No', 'events' ) : __( 'Yes', 'events' ), $event );
	}

/**
 * Event approved
 * @since 1.4
 */
function bpe_event_approved( $e = false )
{
	echo bpe_get_event_approved( $e );
}
	function bpe_get_event_approved( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->approved ) )
			return false;

		return apply_filters( 'bpe_get_event_approved', $event->approved, $event );
	}

/**
 * Event group approved
 * @since 2.0
 */
function bpe_event_group_approved( $e = false )
{
	echo bpe_get_event_group_approved( $e );
}
	function bpe_get_event_group_approved( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->group_approved ) )
			return false;

		return apply_filters( 'bpe_get_event_group_approved', $event->group_approved, $event );
	}
	
/**
 * Event limit_members
 * @since 1.0
 */
function bpe_event_limit_members( $e = false )
{
	echo bpe_get_event_limit_members( $e );
}
	function bpe_get_event_limit_members( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->limit_members ) )
			return false;

		return apply_filters( 'bpe_get_event_limit_members', $event->limit_members, $event );
	}

/**
 * Event images
 * @since 1.0
 */
function bpe_event_image( $args = '' )
{
	echo bpe_get_event_image( $args );
}
	function bpe_get_event_image( $args = '' )
	{
		global $bpe, $event_template, $bp;

		$defaults = array(
			'type' => 'full',
			'width' => BP_AVATAR_FULL_WIDTH,
			'height' => BP_AVATAR_FULL_HEIGHT,
			'class' => 'avatar',
			'id' => false,
			'alt' => __( 'Event logo', 'events' ),
			'event' => false,
			'html' => true
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );
		
		if( ! $event )
			$event = $event_template->event;

		$url = bp_core_fetch_avatar( array( 'item_id' => bpe_get_event_id( $event ), 'object' => 'event', 'type' => $type, 'avatar_dir' => 'event-avatars', 'alt' => $alt, 'css_id' => $id, 'class' => $class, 'width' => $width, 'height' => $height, 'no_grav' => true, 'html' => false ) );

		if( strpos( $url, 'bp-core' ) !== false )
			$url = false;

		if( ! $url )
		{
			if( $type == 'full' )
				$size = 'mid';
			else
				$size = 'mini';
				
			if( bpe_get_option( 'default_avatar', $size ) )
				$url = bp_get_root_domain() . bpe_get_option( 'default_avatar', $size );
			
			else
			{
				if( $size == 'mid' )
					$default = 'default.png';
				elseif( $size == 'mini' )
					$default = 'default-small.png';
				
				$url = EVENT_URLPATH .'css/images/'. $default;
			}
		}
		
		if( $html === false )
			return $url;
			
		$avatar = '<img src="'. esc_url( $url ) .'" alt="'. esc_attr( $alt ) .'" width="'. esc_attr( $width ) .'" height="'. esc_attr( $height ) .'" class="'. esc_attr( $class ) .'" id="'. esc_attr( $id ) .'" />';
			
		return apply_filters( 'bpe_get_event_image', $avatar, $event );
	}

function bpe_event_image_thumb( $e = false )
{
	echo bpe_get_event_image_thumb( $e );
}
	function bpe_get_event_image_thumb( $e = false )
	{
		return bpe_get_event_image( array( 'type' => 'thumb', 'width' => 50, 'height' => 50, 'event' => $e ) );
	}

function bpe_event_image_mini( $e = false )
{
	echo bpe_get_event_image_mini( $e );
}
	function bpe_get_event_image_mini( $e = false )
	{
		return bpe_get_event_image( array( 'type' => 'thumb', 'width' => 30, 'height' => 30, 'event' => $e ) );
	}

/**
 * Event distance from logged in users position
 * @since 1.0
 */
function bpe_event_distance( $e = false )
{
	echo bpe_get_event_distance( $e );
}
	function bpe_get_event_distance( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		if( ! isset( $event->distance ) )
			return false;

		return apply_filters( 'bpe_get_event_distance', $event->distance, $event );
	}

/**
* Display the distance from a users location
* @since 1.6
*/
function bpe_display_distance_from_user( $e = false )
{
	$dist = ( bpe_get_option( 'system' )  == 'm' ) ? __( 'miles', 'events' ) : __( 'km', 'events' );
	
	if( bpe_get_event_distance( $e ) )
		printf( apply_filters( 'bpe_display_event_distance_from_user', __( '<span class="activity">This event is <strong>%s</strong> away from your location.</span>', 'events' ), number_format( bpe_get_event_distance( $e ), 2 ) .' '. $dist ) );
}

/**
 * Event leftover spots
 * @since 1.0
 */
function bpe_event_leftover_spots( $e = false )
{
	echo bpe_get_event_leftover_spots( $e );
}
	function bpe_get_event_leftover_spots( $e = false )
	{
		global $event_template, $nav_counter;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;
		
		if( bpe_is_closed_event( $event ) )
			return false;

		if( bpe_get_event_limit_members( $event ) > 0 )
		{
			$nav_counter++;

			if( bpe_is_reached_max( $event ) )
				$spots = 0;
			else
				$spots = bpe_get_event_limit_members( $event ) - $event->attendees;
			
			return apply_filters( 'bpe_get_event_leftover_spots', '<span class="activity">'. sprintf( _n( '%d spot left', '%d spots left', $spots, 'events' ), $spots ) .'</span>', $event );
		}
		
		return false;
	}
	
/**
 * Get all invitations for an event
 * @since 2.0.7
 */
function bpe_get_invitations( $e = false )
{
	global $event_template;

	$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

	$invitations = array();
	
	if( ! empty( $event->invitations ) )
		$invitations = maybe_unserialize( $event->invitations );
	
	if( ! is_array( $invitations ) )
		return array();

	return apply_filters( 'bpe_get_event_invitations', $invitations, $event );
}

/**
 * Event join button
 * @since 1.0
 */
function bpe_attendance_button( $e = false )
{
	echo bpe_get_attendance_button( $e );
}
	function bpe_get_attendance_button( $e = false )
	{
		global $event_template, $nav_counter;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;
		
		if( bpe_is_event_cancelled( $event ) )
			return false;
 
 		$out = '';

		if( ! bpe_is_admin( $event, false ) && bpe_get_event_public( $event ) == 0 && ! in_array( bp_loggedin_user_id(), bpe_get_invitations( $event ) ) )
		{
			$nav_counter++;
			return '<span class="activity">'. __( 'Private Event', 'events' ) .'</span>';
		}

		if( bpe_is_closed_event( $event ) )
		{
			$nav_counter++;
			return '<span class="activity">'. __( 'Event is closed', 'events' ) .'</span>';
		}

		if( ! bpe_is_rsvp_enabled( $event ) )
			return false;

		if( bpe_is_reached_max( $event ) && ! bpe_is_member( $event ) )
		{
			$nav_counter++;
			return '<span class="activity">'. __( 'Registration is closed', 'events' ) .'</span>';
		}
			
		if( ! is_user_logged_in() )
			return false;
		
		if( bpe_is_admin( $event, false ) )
			return false;
		
		if( bpe_is_member( $event ) )
		{
			$nav_counter++;
			$out .= '<a class="button confirm" href="'. bpe_get_event_link( $event ) .'not-attending/'. bp_loggedin_user_id() .'/">'. __( 'Remove from event', 'events' ) .'</a>';
		}
		else
		{
			$nav_counter++;
			$out .= '<a class="button" href="'. bpe_get_event_link( $event ) .'attending/'. bp_loggedin_user_id() .'">'. __( 'Attending', 'events' ) .'</a>';
			$out .= '<a class="button" href="'. bpe_get_event_link( $event ) .'maybe/'. bp_loggedin_user_id() .'">'. __( 'Maybe attending', 'events' ) .'</a>';
		}

		// do it again for already attending members
		if( bpe_is_reached_max( $event ) )
			$out .= '<span class="activity">'. __( 'Registration is closed', 'events' ) .'</span>';
		
		return apply_filters( 'bpe_get_attendance_button', $out, $event );
	}

/**
 * Event group name
 * @since 1.0
 */
function bpe_event_group_name( $e = false )
{
	echo bpe_event_get_group_name( $e );
}
	function bpe_event_get_group_name( $e = false )
	{
		global $event_template;
		
		if( ! bp_is_active( 'groups' ) )
			return false;

		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		return apply_filters( 'bpe_get_event_group_name', $event->group_name, $event );
	}

/**
 * Event group description
 * @since 1.0
 */
function bpe_event_group_description( $e = false )
{
	echo bpe_event_get_group_description( $e );
}
	function bpe_event_get_group_description( $e = false )
	{
		global $event_template;
		
		if( ! bp_is_active( 'groups' ) )
			return false;

		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		return apply_filters( 'bpe_get_event_group_description', $event->group_desc, $event );
	}

/**
 * Event group slug
 * @since 1.0
 */
function bpe_event_group_slug( $e = false )
{
	echo bpe_event_get_group_slug( $e );
}
	function bpe_event_get_group_slug( $e = false )
	{
		global $event_template;
		
		if( ! bp_is_active( 'groups' ) )
			return false;

		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		return apply_filters( 'bpe_get_event_group_slug', $event->group_slug, $event );
	}

/**
 * Event group permalink
 * @since 1.0
 */
function bpe_event_group_permalink( $e = false )
{
	echo bpe_event_get_group_permalink( $e );
}
	function bpe_event_get_group_permalink( $e = false )
	{
		global $event_template, $bp;
		
		if( ! bp_is_active( 'groups' ) )
			return false;

		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		return apply_filters( 'bpe_get_event_group_permalink', esc_url( bp_get_root_domain() .'/'. bp_get_groups_slug() .'/'. bpe_event_get_group_slug( $event ) .'/' ), $event );
	}

/**
 * Event group slug
 * @since 1.0
 */
function bpe_event_group_status( $e = false )
{
	echo bpe_event_get_group_status( $e );
}
	function bpe_event_get_group_status( $e = false )
	{
		global $event_template;
		
		if( ! bp_is_active( 'groups' ) )
			return false;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

		return apply_filters( 'bpe_get_event_group_status', $event->group_status, $event );
	}

/**
 * Turn address into array
 * @since 1.1
 */
function bpe_event_get_group_address( $e = false )
{
	global $event_template;

	if( ! bp_is_active( 'groups' ) )
		return false;

	$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;

	return apply_filters( 'bpe_get_event_group_address', ( isset( $event->address ) ? (object) maybe_unserialize( stripslashes( $event->address ) ) : false ), $event );
}

/**
 * Event group address street
 * @since 1.0
 */
function bpe_event_group_address_street( $e = false )
{
	echo bpe_event_get_group_address_street( $e );
}
	function bpe_event_get_group_address_street( $e = false )
	{
		if( ! bp_is_active( 'groups' ) )
			return false;

		$address = bpe_event_get_group_address( $e );
		
		return apply_filters( 'bpe_get_event_group_address_street', ( isset( $address->street ) ? $address->street : false ), $event );
	}

/**
 * Event group address postcode
 * @since 1.0
 */
function bpe_event_group_address_postcode( $e = false )
{
	echo bpe_event_get_group_address_postcode( $e );
}
	function bpe_event_get_group_address_postcode( $e = false )
	{
		if( ! bp_is_active( 'groups' ) )
			return false;

		$address = bpe_event_get_group_address( $e );

		return apply_filters( 'bpe_get_event_group_address_postcode', ( isset( $address->postcode ) ? $address->postcode : false ), $event );
	}

/**
 * Event group address city
 * @since 1.0
 */
function bpe_event_group_address_city( $e = false )
{
	echo bpe_event_get_group_address_city( $e );
}
	function bpe_event_get_group_address_city( $e = false )
	{
		if( ! bp_is_active( 'groups' ) )
			return false;

		$address = bpe_event_get_group_address( $e );

		return apply_filters( 'bpe_get_event_group_address_city', ( isset( $address->city ) ? $address->city : false ), $event );
	}

/**
 * Event group address country
 * @since 1.0
 */
function bpe_event_group_address_country( $e = false )
{
	echo bpe_event_get_group_address_country( $e );
}
	function bpe_event_get_group_address_country( $e = false )
	{
		if( ! bp_is_active( 'groups' ) )
			return false;

		$address = bpe_event_get_group_address( $e );

		return apply_filters( 'bpe_get_event_group_address_country', ( isset( $address->country ) ? $address->country : false ), $event );
	}

/**
 * Event group address telephone
 * @since 1.0
 */
function bpe_event_group_address_telephone( $e = false )
{
	echo bpe_event_get_group_address_telephone( $e );
}
	function bpe_event_get_group_address_telephone( $e = false )
	{
		if( ! bp_is_active( 'groups' ) )
			return false;

		$address = bpe_event_get_group_address( $e );

		return apply_filters( 'bpe_get_event_group_address_phone', ( isset( $address->telephone ) ? $address->telephone : false ), $event );
	}

/**
 * Event group address mobile
 * @since 1.0
 */
function bpe_event_group_address_mobile( $e = false )
{
	echo bpe_event_get_group_address_mobile( $e );
}
	function bpe_event_get_group_address_mobile( $e =  false )
	{
		if( ! bp_is_active( 'groups' ) )
			return false;

		$address = bpe_event_get_group_address( $e );

		return apply_filters( 'bpe_get_event_group_address_mobile', ( isset( $address->mobile ) ? $address->mobile : false ), $event );
	}

/**
 * Event group address fax
 * @since 1.0
 */
function bpe_event_group_address_fax( $e = false )
{
	echo bpe_event_get_group_address_fax( $e );
}
	function bpe_event_get_group_address_fax( $e = false )
	{
		if( ! bp_is_active( 'groups' ) )
			return false;

		$address = bpe_event_get_group_address( $e );

		return apply_filters( 'bpe_get_event_group_address_fax', ( isset( $address->fax ) ? $address->fax : false ), $event );
	}
	
/**
 * Event group address website
 * @since 1.0
 */
function bpe_event_group_address_website( $e = false )
{
	echo bpe_event_get_group_address_website( $e );
}
	function bpe_event_get_group_address_website( $e = false )
	{
		if( ! bp_is_active( 'groups' ) )
			return false;

		$address = bpe_event_get_group_address( $e );

		return apply_filters( 'bpe_get_event_group_address_website', ( isset( $address->website ) ? apply_filters( 'bpe_event_get_group_address_website', esc_url( $address->website ) ) : false ), $event );
	}
	
/**
 * Cancel event button
 * @since 2.1
 */
function bpe_cancel_event_button( $e = false )
{
	echo bpe_get_cancel_event_button( $e );
}
	function bpe_get_cancel_event_button( $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;
		
		$button = '<a href="'. esc_url( wp_nonce_url( bpe_get_event_link( $event ) . bpe_get_option( 'edit_slug' ) .'/'. bpe_get_option( 'general_slug' ) .'/?action=cancel_event', 'bpe_cancel_event_now' ) ) .'">'. __( 'Cancel event', 'events' ) .'</a>';

		return apply_filters( 'bpe_get_cancel_event_button', $button );
	}

/**
 * Get all event member ids (comma separated)
 * @since 1.0
 */
function bpe_event_get_member_ids( $e = false, $context = false )
{
	global $event_template, $bp;
	
	$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;
	
	if( ! $context )
		$context = bp_action_variable( 2 );
		
	switch( $context )
	{
		case 'maybe':
			$member_ids = $event->maybe_attendee_ids;
			break;
	
		case 'admins':
			$member_ids = $event->admin_ids;
			break;
	
		case 'organizers':
			$member_ids = $event->organizer_ids;
			break;
	
		case 'not_attending':
			$member_ids = $event->not_attendee_ids;
			break;
		
		case 'attendees': default:
			$member_ids = $event->attendee_ids;
			break;
	}
	
	$ids = implode( ',', (array)$member_ids );
	
	if( empty( $ids ) )
		return -1;
	
	return apply_filters( 'bpe_get_event_member_ids', $ids, $event );	
}

/**
 * Get the tabs for attendee pages
 * @since 1.7
 */
function bpe_event_attendee_tabs( $e = false )
{
	echo bpe_get_event_attendee_tabs( $e );
}
	function bpe_get_event_attendee_tabs( $e = false )
	{
		global $event_template;

		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;
		
		$tabs  = '<li'. ( ( ! bp_action_variable( 2 ) ) ? ' class="current"' : '' ) .'><a href="'. bpe_get_event_link( $event ) . bpe_get_option( 'attendee_slug' ) .'/">'. sprintf( __( 'Attending (%s)', 'events' ), count( (array)$event->attendee_ids ) ) .'</a></li>';
		$tabs .= '<li'. ( ( bp_is_action_variable( 'maybe', 2 ) ) ? ' class="current"' : '' ) .'><a href="'. bpe_get_event_link( $event ) . bpe_get_option( 'attendee_slug' ) .'/maybe/">'. sprintf( __( 'Maybe Attending (%s)', 'events' ), count( (array)$event->maybe_attendee_ids ) ) .'</a></li>';

		if( bpe_event_has_organizers( $event ) )
			$tabs .= '<li'. ( ( bp_is_action_variable( 'organizers', 2 ) ) ? ' class="current"' : '' ) .'><a href="'. bpe_get_event_link( $event ) . bpe_get_option( 'attendee_slug' ) .'/organizers/">'. sprintf( __( 'Organizers (%s)', 'events' ), count( (array)$event->organizer_ids ) ) .'</a></li>';

		$tabs .= '<li'. ( ( bp_is_action_variable( 'admins', 2 ) ) ? ' class="current"' : '' ) .'><a href="'. bpe_get_event_link( $event ) . bpe_get_option( 'attendee_slug' ) .'/admins/">'. sprintf( __( 'Admins (%s)', 'events' ), count( (array)$event->admin_ids ) ) .'</a></li>';
		
		return apply_filters( 'bpe_get_event_attendee_tabs', $tabs, $event );
	}

/**
 * Get a users attending status
 * @since 1.6
 */
function bpe_attending_status( $user_id, $e = false )
{
	echo bpe_get_attending_status( $user_id, $e );
}
	function bpe_get_attending_status( $user_id, $e = false )
	{
		global $event_template;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;
		
		$status = '';
		
		if( $event->attending_status[$user_id] == 1 )
			$status = __( 'attending', 'events' );
		
		elseif( $event->attending_status[$user_id] == 2 )
			$status = __( 'might attend', 'events' );
			
		return apply_filters( 'bpe_get_user_attending_status', $status, $event );
	}

/**
 * Displayed user attending status
 * @since 1.6
 */
function bpe_event_attending_status( $e = false )
{
	echo bpe_get_event_attending_status( $e );
}
	function bpe_get_event_attending_status( $e = false )
	{
		global $event_template, $nav_counter;
		
		$event = ( isset( $event_template->event ) && empty( $e ) ) ? $event_template->event : $e;
		
		$status = ( isset( $event->attending_status[bp_loggedin_user_id()] ) ) ? $event->attending_status[bp_loggedin_user_id()] : false;
		
		if( bpe_is_closed_event() )
			return false;
		
		if( bp_is_current_action( bpe_get_option( 'attending_slug' ) ) )
		{
			if( bp_loggedin_user_id() == bp_displayed_user_id() )
			{
				if( $status == 1 )
				{
					$nav_counter++;
					return '<span class="activity">'. __( 'I am attending', 'events' ) .'</span>';
				}
				elseif( $status == 2 )
				{
					$nav_counter++;
					return '<span class="activity">'. __( 'I might attend', 'events' ) .'</span>';
				}
			}
			else
			{
				if( $status == 1 )
				{
					$nav_counter++;
					return '<span class="activity">'. sprintf( __( '%s is attending', 'events' ), bp_get_user_firstname( bp_get_displayed_user_fullname() ) ) .'</span>';
				}
				elseif( $status == 2 )
				{
					$nav_counter++;
					return '<span class="activity">' .sprintf( __( '%s might attend', 'events' ), bp_get_user_firstname( bp_get_displayed_user_fullname() ) ) .'</span>';
				}
			}
		}
		else
		{
			if( bpe_is_admin( $event, false ) )
				return false;
				
			if( $status == 1 )
			{
				$nav_counter++;
				return '<span class="activity">'. __( 'I am attending', 'events' ) .'</span>';
			}
			elseif( $status == 2 )
			{
				$nav_counter++;
				return '<span class="activity">'. __( 'I might attend', 'events' ) .'</span>';
			}
		}
	}

/**
 * Get main link to directory
 * @since 1.0
 */
function bpe_events_link()
{
	echo bpe_get_events_link();	
}
	function bpe_get_events_link()
	{
		return apply_filters( 'bpe_get_event_main_link', esc_url( bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/' ) );
	}

/**
 * The next event
 * @since 1.0
 */
function bpe_next_event_link( $suffix = '&rarr;' )
{
	echo bpe_get_next_event_link( $suffix );
}
	/**
	 * Get the next event
	 * @since 1.0
	 */
	function bpe_get_next_event_link( $suffix = '&rarr;' )
	{
		$event = bpe_get_adjacent_event( false );

		$slug = '';
		if( bp_is_current_action( bpe_get_option( 'archive_slug' ) ) )
			$slug = bpe_get_option( 'archive_slug' ) .'/';
		else
			$slug = bpe_get_option( 'active_slug' ) .'/';
		
		$link = '';
		if( ! empty( $event ) )
			$link .= '<a href="'. esc_url( bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. $slug . bpe_get_event_slug( $event ) .'/' ) .'" title="'. __( 'View the next event', 'events' ) .'">'. bpe_get_event_name( $event ) .' '. $suffix .'</a>';
		
		return apply_filters( 'bpe_get_next_event_link', $link, $event );
	}
	
/**
 * The previous event
 * @since 1.0
 */
function bpe_previous_event_link( $prefix = '&larr;' )
{
	echo bpe_get_previous_event_link( $prefix );
}
	/**
	 * Get the previous event
	 * @since 1.0
	 */
	function bpe_get_previous_event_link( $prefix = '&larr;' )
	{
		$event = bpe_get_adjacent_event( true );
		
		$slug = '';
		if( bp_is_current_action( bpe_get_option( 'archive_slug' ) ) )
			$slug = bpe_get_option( 'archive_slug' ) .'/';
		else
			$slug = bpe_get_option( 'active_slug' ) .'/';

		$link = '';
		if( ! empty( $event ) )
			$link .= '<a href="'. esc_url( bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. $slug . bpe_get_event_slug( $event ) .'/' ) .'" title="'. __( 'View the previous event', 'events' ) .'">'. $prefix .' '. bpe_get_event_name( $event ) .'</a>';
		
		return apply_filters( 'bpe_get_previous_event_link', $link, $event );
	}

/**
 * Get the activity comments
 * @since 1.0
 */
function bpe_event_has_activity( $e = false )
{
	$event = ( ! empty( $e ) ) ? $e : bpe_get_displayed_event();
	
	if( $forum_id = bpe_get_eventmeta( $event->id, 'forum_id' ) ) :
		$activity_ids = bpe_get_activity_ids_with_forum( $forum_id, $event->id );
		
		$args = array(
			'include'			=> $activity_ids,
			'show_hidden'		=> true,
			'display_comments' 	=> 'threaded'
		);
	else :
		$args = array(
			'object'			=> 'events',
			'primary_id'		=> bpe_get_event_id( $event ),
			'show_hidden'		=> true,
			'display_comments' 	=> 'threaded'
		);
	endif;
	
	return bp_has_activities( apply_filters( 'bpe_event_has_activity_args', $args ) );
}

/**
 * Get the event slug
 * @since 1.2
 */
function bpe_get_ev_slug()
{
	return ( in_array( bp_current_action(), array( bpe_get_option( 'active_slug' ), bpe_get_option( 'archive_slug' ) ) ) ) ? bp_action_variable( 0 ) : bp_current_action();
}

/**
 * Get an alt css class
 * @since 2.0
 */
function bpe_approve_css_class()
{
	echo bpe_get_approve_css_class();
}

	function bpe_get_approve_css_class()
	{
		global $event_template;

		$class = false;

		if ( $event_template->current_event % 2 == 1 )
			$class = 'alt';

		return apply_filters( 'bpe_get_approve_css_class', trim( $class ) );
	}

/**
 * Set the nav counter to null
 * @since 2.0
 */
function bpe_reset_counter()
{
	global $nav_counter;
	$nav_counter = 0;
}

/**
 * CSS class when logos are disabled
 * @since 2.0
 */
function bpe_item_class()
{
	global $nav_counter;
	
	if( ! bpe_are_logos_enabled() ) :
		if( $nav_counter <= 0 )
			echo ' no-event-logo';
	endif;
}

/**
 * CSS status class
 * @since 2.0
 */
function bpe_event_status_class()
{
	global $event_template, $bpe;
		
	$event = ( isset( $event_template->event ) && empty( $bpe->displayed_event ) ) ? $event_template->event : $bpe->displayed_event;
	
	if( bpe_is_event_cancelled( $event ) )
		echo 'cancelled';
	
	else
		echo 'active';
}
?>