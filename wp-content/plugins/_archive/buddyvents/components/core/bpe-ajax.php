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
 * Get an events list page via AJAX
 * 
 * Also makes sure that the correct template is loaded to retain any
 * template modifications. Handles special cases as well, where template
 * locations do not conform to URL segments naming conventions
 *
 * @package Core
 * @since 	2.1
 */
function bpe_ajax_get_events_page()
{
	global $eventloop_args, $bp;
		
    $eventloop_args = array(
		'page' 	=> ( ( empty( $_POST['page'] ) ) ? 1 : $_POST['page'] )
    );
	
	$template = false;
	$segments = explode( '/', trim( $_POST['pathname'], '/' ) );
	
	// Lets do the attending slug early on as it's only present in one scenario
	if( in_array( bpe_get_option( 'attending_slug' ), $segments ) ) :
		$template = 'events/member/attending';
		
	// now do all the rest
	else :
		// Lets get the component first
		if( $segments[0] == $bp->groups->slug ) :
			$component = 'group';
			
		elseif( $segments[0] == $bp->members->slug ) :
			$component = 'member';
			
		else :
			$component = 'top-level';
		endif;
		
		// We need to change component to includes in certain cases
		if( $component == 'top-level' && ( 
			in_array( bpe_get_option( 'active_slug' ), $segments ) ||
			in_array( bpe_get_option( 'archive_slug' ), $segments ) ||
			bpe_get_option( 'default_tab' ) == bpe_get_option( 'active_slug' ) ||
			bpe_get_option( 'default_tab' ) == bpe_get_option( 'archive_slug' )
		) )
			$component = 'includes';

		// Now do the file names, slugs have to be unique, so they can only appear once
		if( in_array( bpe_get_option( 'day_slug' ), $segments ) ) :
			$file = 'day';
			
		elseif( in_array( bpe_get_option( 'month_slug' ), $segments ) ) :
			$file = 'month';

		elseif( in_array( bpe_get_option( 'timezone_slug' ), $segments ) ) :
			$file = 'timezone';

		elseif( in_array( bpe_get_option( 'venue_slug' ), $segments ) ) :
			$file = 'venue';

		elseif( in_array( bpe_get_option( 'category_slug' ), $segments ) ) :
			$file = 'category';

		elseif( in_array( bpe_get_option( 'archive_slug' ), $segments ) ) :
			$file = 'archive';

		elseif( in_array( bpe_get_option( 'active_slug' ), $segments ) ) :
			$file = 'active';
			
		else :
			$file = bpe_get_option( 'default_tab' );
			
		endif;
		
		// Again, change the file for certain cases
		if( $component == 'group' && $file == 'active' )
			$file = 'home';
		
		if( $component == 'includes' ) :
			if( $file == 'archive' )
				$file = 'archive-loop';
			
			elseif( $file == 'active' )
				$file = 'loop';
		endif;
		
		$template = 'events/'. $component .'/'. $file;
	endif;
	
	$located = false;
	
	// make sure that the file exists before includig it
	if( file_exists( STYLESHEETPATH .'/'. $template .'.php' ) )
		$located = true;
		
	elseif( file_exists( TEMPLATEPATH .'/'. $template .'.php' ) )
		$located = true;
	
	elseif( file_exists( EVENT_ABSPATH .'templates/'. $template .'.php' ) )
		$located = true;	
	
	// lastly, let's include a backup template
	if( ! $template || ! $located )
		$template = 'events/includes/loop';	
		
	bpe_load_template( $template );
}
add_action( 'wp_ajax_events_pagination', 		'bpe_ajax_get_events_page' );
add_action( 'wp_ajax_nopriv_events_pagination', 'bpe_ajax_get_events_page' );

/**
 * Get the adjacent month for the ajax calendar
 *
 * @package Core
 * @since 	1.4
 */
function bpe_ajax_get_adjacent_month()
{
	global $bp;
	
	$id = (int)$_POST['id'];
	$month = (int)$_POST['month'];
	$year = (int)$_POST['year'];
	
	// make sure we have everything
	if( empty( $id ) || empty( $month ) || empty( $year ) || empty( $_POST['type'] ) )
	{
		echo json_encode( array( 'type' => 'error', 'content' => '' ) );
		die();
	}
	
	// get the adjacent month
	if( $_POST['type'] == 'cal-widget-next' )
	{
		if( $month == 12 )
		{
			$month = 1;
			$year++;
		}
		else
			$month++;
	}	
	elseif( $_POST['type'] == 'cal-widget-prev' )
	{
		if( $month == 1 )
		{
			$month = 12;
			$year--;
		}
		else
			$month--;
	}
	
	$month = ( $month < 10 ) ? '0'. $month : $month;
	
	$result = bpe_get_events( array( 'month' => $month, 'year' => $year, 'sort' => 'calendar', 'per_page' => false, 'future' => false ) );
	$events = $result['events'];
	
	$content = '<form id="cal_bpe_calendar-'. $id .'">';
		$content .= '<input type="hidden" name="current_month" id="current_month_bpe_calendar-'. $id .'" value="'. $month .'" />';
		$content .= '<input type="hidden" name="current_year" id="current_year_bpe_calendar-'. $id .'" value="'. $year .'" />';
		$content .= '<div class="cal-widget-head">';
			$content .= '<a class="cal-widget-prev" title="'. __( 'Previous month', 'events' ) .'" href="#">&lt;&lt;</a>';
			$content .= '<span class="cal-widget-title"><a href="'. bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'month_slug' ) .'/'. $month .'/'. $year .'/">'. bpe_localize_month_name( $month, $year ) .' '. $year .'</a></span>';
			$content .= '<a class="cal-widget-next" title="'. __( 'Next month', 'events' ) .'" href="#">&gt;&gt;</a>';
		$content .= '</div>';
		
		$content .= bpe_draw_calendar( $month, $year, $events, 'widget' );
	$content .= '</form>';

	echo json_encode( array( 'type' => 'success', 'content' => $content ) );
	die();
}
add_action( 'wp_ajax_bpe_cal_widget_get_month', 		'bpe_ajax_get_adjacent_month' );
add_action( 'wp_ajax_nopriv_bpe_cal_widget_get_month',  'bpe_ajax_get_adjacent_month' );

/**
 * Custom autocomplete AJAX hook
 *
 * @package	 Core
 * @since 	 1.5
 */
function bpe_autocomplete_invites()
{
	global $wpdb;
	
	$search = like_escape( $wpdb->escape( $_GET['q'] ) );
	
	$users = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->users} WHERE user_login LIKE '%%{$search}%%' OR display_name LIKE '%%{$search}%%' OR user_nicename LIKE '%%{$search}%%' LIMIT %d", $_GET['limit'] ) );

	foreach ( (array)$users as $user )
		echo bp_core_fetch_avatar( array( 'item_id' => $user->ID, 'type' => 'thumb', 'width' => 15, 'height' => 15 ) ) .' &nbsp;'. $user->display_name .' ('. $user->user_login .')
		';
		
	die();
}
add_action( 'wp_ajax_bpe_invites_autocomplete_results', 'bpe_autocomplete_invites' );

/**
 * Get events for a given date range for fullcalendar
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_fullcalendar_get_ajax_events()
{
	global $bp;
	
	check_ajax_referer( 'bpe_get_fullcalendar_events_nonce' );
	
	$begin = $_POST['begin'];
	$end = $_POST['end'];

	if( empty( $begin ) || empty( $end ) )
		die( '-1' );
		
	$fullcalendar = array();
	
	$group_id = ( bp_is_active( 'groups' ) ) ? bp_get_current_group_id() : false;
	
	$user_id = bp_displayed_user_id();
	
	$result = bpe_get_events( array( 'begin' => $begin, 'end' => $end, 'per_page' => false, 'future' => false, 'past' => false, 'user_id' => $user_id, 'group_id' => $group_id ) );
	
	// get all recurrrent events for the time range
	$recurrent_events = bpe_get_calendar_recurrent_events( $begin, $end, $user_id, $group_id );
	
	$events = array_merge( (array)$recurrent_events, $result['events'] );
	
	foreach( (array)$events as $event )
	{
		$fullcalendar[] = array(
			'id' 		=> bpe_get_event_id( $event ),
			'parent_id' => ( ( isset( $event->parent_id ) ) ? $event->parent_id : 0 ),
			'title' 	=> esc_js( bpe_get_event_name( $event ) ),
			'start' 	=> mysql2date( 'M d, Y H:i:s', bpe_get_event_start_date_raw( $event ) .' '. bpe_get_event_start_time_raw( $event ) ),
			'end' 		=> mysql2date( 'M d, Y H:i:s', bpe_get_event_end_date_raw( $event ) .' '. bpe_get_event_end_time_raw( $event ) ),
			'allday' 	=> ( ( bpe_get_event_start_date_raw( $event ) != bpe_get_event_end_date_raw( $event ) ) ? 'yes' : 'no' ),
			'desc' 		=> bpe_get_event_description_excerpt_raw( $event )
		);
	}

	die( json_encode( $fullcalendar ) );
}
add_action( 'wp_ajax_bpe_fullcalendar_get_calevent', 		'bpe_fullcalendar_get_ajax_events' );
add_action( 'wp_ajax_nopriv_bpe_fullcalendar_get_calevent', 'bpe_fullcalendar_get_ajax_events' );

/**
 * Get popup for a given event
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_fullcalendar_click_event_action()
{
	check_ajax_referer( 'bpe_click_fullcalendar_event_nonce' );

	$is_recurrent = ( strpos( $_POST['event_id'], 'recurrent' ) === false ) ? false : true;
	$event_id = (int)( ( ! $is_recurrent ) ? $_POST['event_id'] : $_POST['parent_id'] );

	if( empty( $event_id ) )
		die( '-1' );

	$result = bpe_get_events( array( 'ids' => $event_id, 'future' => false, 'past' => false ) );
	$event = $result['events'][0];
	
	if( $is_recurrent )
	{
		$event->start_date = $_POST['begin'];
		$event->end_date = $_POST['end'];
	}
	
	$calendar  = bpe_calendar_popup_html( $event, $is_recurrent );
	$calendar .= bpe_calendar_map_js( $event, true );
	
	die( json_encode( array( 'html' => $calendar, 'hasLocation' => ( ( bpe_has_event_location( $event ) ) ? 1 : 0 ) ) ) );
}
add_action( 'wp_ajax_bpe_fullcalendar_click_calevent', 		  'bpe_fullcalendar_click_event_action' );
add_action( 'wp_ajax_nopriv_bpe_fullcalendar_click_calevent', 'bpe_fullcalendar_click_event_action' );

/**
 * Check if a given day has an archive
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_fullcalendar_check_day_archive()
{
	check_ajax_referer( 'bpe_fullcalendar_day_click' );

	$day = $_POST['day'];

	if( empty( $day ) )
		die( '-1' );
		
	$result = bpe_get_events( array( 'day' => $day, 'future' => false, 'past' => false ) );
	
	if( (int)$result['total'] <= 0 )
		die( '-1' );
		
	die( '1' );
}
add_action( 'wp_ajax_bpe_fullcalendar_day_click', 		 'bpe_fullcalendar_check_day_archive' );
add_action( 'wp_ajax_nopriv_bpe_fullcalendar_day_click', 'bpe_fullcalendar_check_day_archive' );

/**
 * Get the infobox content
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_get_google_map_content()
{
	global $bp;
	
	if( empty( $_POST['id'] ) )
	{
		echo json_encode( array( 'url' => bp_get_root_domain(), 'content' => __( 'The content for this post could not be retrieved.', 'events' ) ) );
		exit;
	}
	
	$event_id = absint( $_POST['id'] );

	$result = bpe_get_events( array( 'ids' => $event_id, 'future' => false, 'past' => false ) );
	$event = $result['events'][0];
	
	$pu = '<div id="calevent-'. bpe_get_event_id( $event ) .'" class="map-event">';
	
		$pu .= '<div class="item-avatar">';
			$pu .= bpe_get_event_image( array( 'event' => $event, 'type' => 'thumb', 'width' => 50, 'height' => 50, ) );
		$pu .= '</div>';
	
		$pu .= '<div class="mapevent-item">';
			$pu .= '<div class="event-meta">';
				$pu .= '<dl class="column">';
					$pu .= sprintf( __( '<dt>Venue:</dt><dd><span class="location">%s</span></dd>', 'events' ), bpe_get_event_location_link( $event ) );
					if( bpe_is_all_day_event( $event ) ) :
						$pu .= sprintf( __( '<dt>Start:</dt><dd><span class="dtstart">%s</span> (all day event)</dd>', 'events' ), bpe_get_event_start_date( $event ) );
						if( bpe_get_event_start_date( $event ) != bpe_get_event_end_date( $event ) ) :
							$pu .= sprintf( __( '<dt>End:</dt><dd><span class="dtend">%s</span> (all day event)</dd>', 'events' ), bpe_get_event_end_date( $event ) );
						endif;
					else :
						$pu .= sprintf( __( '<dt>Start:</dt><dd><span class="dtstart">%s</span> at %s</dd>', 'events' ), bpe_get_event_start_date( $event ), bpe_get_event_start_time( $event ) );
						$pu .= sprintf( __( '<dt>End:</dt><dd><span class="dtend">%s</span> at %s</dd>', 'events' ), bpe_get_event_end_date( $event ), bpe_get_event_end_time( $event ) );
					endif;
					
					if( bpe_has_event_timezone( $event ) )
						$pu .= sprintf( __( '<dt>Timezone:</dt><dd><span class="timezone">%s</dd>', 'events' ), bpe_get_event_timezone( $event ) );

					$pu .= sprintf( __( '<dt>Category:</dt><dd><span class="category">%s</span></dd>', 'events' ), bpe_get_event_category( $event ) );
					if( bpe_has_url( $event ) )
						$pu .= sprintf( __( '<dt>Website:</dt><dd><span class="url">%s</span></dd>', 'events' ), bpe_get_event_url( $event ) );

					if( bpe_get_event_group_id( $event ) )
						$pu .= sprintf( __( '<dt>Group:</dt><dd><a href="%s">%s</a></dd>', 'events' ), bpe_event_get_group_permalink( $event ), bpe_event_get_group_name( $event ) );

				$pu .= '</dl>';
			$pu .= '</div>';
			$pu .= '<div class="item-desc description">'. bpe_get_event_description_excerpt( $event, false ) .'</div>';
		$pu .= '</div>';
	$pu .= '</div>';
	
	echo json_encode( array( 'url' => bpe_get_event_link( $event ), 'content' => apply_filters( 'bpe_ajax_map_content', $pu, $event ) ) );
	exit;
}
add_action( 'wp_ajax_bpe_get_google_map_content', 		 'bpe_get_google_map_content' );
add_action( 'wp_ajax_nopriv_bpe_get_google_map_content', 'bpe_get_google_map_content' );
?>