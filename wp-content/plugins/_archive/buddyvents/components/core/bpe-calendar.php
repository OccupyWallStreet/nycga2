<?php
/**
 * @package WordPress
 * @subpackage BuddyPress
 * @author Boris Glumpler
 * @copyright 2010, ShabuShabu Webdesign
 * @link http://shabushabu.eu
 * @license http://www.opensource.org/licenses/gpl-2.0.php GPL License
 * @credits Initial version: David Walsh http://davidwalsh.name/php-event-calendar
 */
 
// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Print the calendar including controls
 * 
 * Wrapper function for all calendar display funtions
 * Parameters will have no effect if fullcalendar.js is active
 * 
 * @package	Core
 * @since 	1.0
 * 
 * @param	int		$month		The month number to look for. Will be automatically zeroized if necessary
 * @param	int		$year		The year to look up. Needs to be four digits
 * @param	string	$title		The title tag. Default is h4
 * @param	string	$class 		The title CSS class. Default is .cal-title
 * 
 * @uses	bpe_fullcalendar()
 * @uses	bpe_calendar_controls()
 * @uses	bpe_draw_calendar()
 */
function bpe_calendar( $month = false, $year = false, $title = 'h4', $class = 'cal-title' )
{
	if( bpe_get_option( 'use_fullcalendar' ) === true ) :
		echo bpe_fullcalendar();
		
	else :
		$month = ( ! empty( $month ) ) ? $month : ( ( bp_action_variable( 0 ) ) ? bp_action_variable( 0 ) : gmdate( 'm' ) );
		$month = apply_filters( 'bpe_calendar_month_variable', zeroise( $month, 2 ) );

		$year = ( ! empty( $year ) ) ? $year : ( ( bp_action_variable( 1 ) ) ? bp_action_variable( 1 ) : gmdate( 'Y' ) );
		$year = apply_filters( 'bpe_calendar_year_variable', $year );
		
		$result = bpe_get_events( array( 
			'month' 	=> $month,
			'year' 		=> $year,
			'sort' 		=> 'calendar',
			'per_page' 	=> false,
			'future' 	=> false,
			'past' 		=> false
		) );
		
		$events = $result['events'];
		
		$link = apply_filters( 'bpe_calendar_title_link', bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'month_slug' ) .'/'. $month .'/'. $year .'/', $month, $year, $events );
		
		echo '<'. $title .' class="'. $class .'"><a href="'. $link .'">'. bpe_localize_month_name( $month, $year ) .' '. $year .'</a></'. $title .'>';
		echo bpe_calendar_controls( $month, $year );
		echo bpe_draw_calendar( $month, $year, $events );
	endif;
}

/**
 * Return the calendar display
 * 
 * The output can be filtered via the <code>bpe_calendar</code> filter. It takes
 * the month, year and events array as additional arguments. Since 2.0 mainly
 * used for the widgets, as use of fullcalendar.js is preferred.
 *
 * @package	 Core
 * @since 	 1.0
 * 
 * @param	int		$month		The month number to look for. Will be automatically zeroized if necessary
 * @param	int		$year		The year to look up. Needs to be four digits
 * @param	array 	$events		An array of Buddyvents events objects
 * @param	string	$type		The type of calendar. Default is 'normal', can also be 'widget'
 * @param	string	$context	The ccontext in which to show the calendar. Can be 'index' (default), 'user', or 'group'
 */
function bpe_draw_calendar( $month, $year, $events = array(), $type = 'normal', $context = 'index' )
{
	global $bp;

	$headings = bpe_get_calendar_headings( $type );
	
	$calendar  = '<table cellpadding="0" cellspacing="0" class="calendar">';
	$calendar .= '<tr class="calendar-row"><td class="calendar-day-head">'. implode( '</td><td class="calendar-day-head">', $headings ) .'</td></tr>';

	$running_day = gmdate( 'w', mktime( 0, 0, 0, $month, 1, $year ) );
	
	// adjust for a week start on monday
	if( bpe_get_option( 'week_start' ) == 1 )
	{
		$running_day--;
		if( $running_day == -1 )
			$running_day = 6;
	}
	
	$days_in_month 		= gmdate( 't', mktime( 0, 0, 0, $month, 1, $year ) );
	$days_in_this_week 	= 1;
	$day_counter 		= 0;
	$dates_array 		= array();

	$calendar .= '<tr class="calendar-row">';

	for( $x = 0; $x < $running_day; $x++ )
	{
		$calendar .= '<td class="calendar-day-np">'. ( ( $type == 'normal' ) ? str_repeat( '<p>&nbsp;</p>', 2 ) : '' ) .'</td>';
		$days_in_this_week++;
	}

	for( $list_day = 1; $list_day <= $days_in_month; $list_day++ )
	{
		$d 		= zeroise( $list_day, 2 );
		$day 	= $year .'-'. $month .'-'. $d;
		$today 	= ( gmdate( 'Y-m-d' ) == $day ) ? ' cal-today' : '';
		
		// we check for cell content
		$content 	 = false;
		$cal_content = '';
		
		if( $type == 'normal' )
		{
			foreach( $events as $key => $event )
			{
				$e = bpe_calendar_event_normal( $event, $day, $list_day );
				if( ! empty( $e ) )
				{
					$cal_content .= $e;
					$content = true;
				}
			}
		}
		elseif( $type == 'widget' )
		{
			$eCounter = 0;
			foreach( $events as $key => $event )
			{
				if( ( $day >= bpe_get_event_start_date_raw( $event ) ) && ( $day <= bpe_get_event_end_date_raw( $event ) ) )
					$eCounter++;
			}
								
			$title = sprintf( _n( '%d event', '%d events', $eCounter, 'events' ), $eCounter );
				
			if( $eCounter > 0 ) :
				$cal_content .= '<a class="cal-widget-content" href="'. bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'day_slug' ) .'/'. $day .'/" title="'. $title .'"></a>';
				$content = true;
			endif;
		}
		
		// if there is content, display a link to the daily archive
		switch( $context )
		{
			case 'user':
				$list_link = bp_displayed_user_domain() . bpe_get_base( 'slug' ) .'/'. bpe_get_option( 'day_slug' ) .'/'. $day .'/';
				break;
				
			case 'group':
				$list_link = bp_get_group_permalink( groups_get_current_group() ) . bpe_get_base( 'slug' ) .'/'. bpe_get_option( 'day_slug' ) .'/'. $day .'/';
				break;

			case 'index': default:
				$list_link = bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/'. bpe_get_option( 'day_slug' ) .'/'. $day .'/';
				break;
		}
		
		$this_day = ( $content && $type == 'normal' ) ? '<a class="cal-daily" href="'. $list_link .'">'. $list_day .'</a>' : $list_day;

		// start the cell output
		$calendar .= '<td class="calendar-day'. $today .'">';
		$calendar .= '<div class="day-number">'. $this_day .'</div>';
		$calendar .= $cal_content;
		
		// pad the table cell if it's empty
		if( $content === false )
			$calendar .= str_repeat( '<p>&nbsp;</p>', ( ( $context == 'normal' ) ? 2 : 1 ) );
		
		$calendar .= '</td>';

		if( $running_day == 6 )
		{
			$calendar .= '</tr>';
			if( ( $day_counter + 1 ) != $days_in_month )
				$calendar .= '<tr class="calendar-row">';

			$running_day = -1;
			$days_in_this_week = 0;
		}
		
		$days_in_this_week++; $running_day++; $day_counter++;
	}

	if( $days_in_this_week < 8 )
	{
		for( $x = 1; $x <= ( 8 - $days_in_this_week ); $x++ )
			$calendar .= '<td class="calendar-day-np">'. ( ( $type == 'normal' ) ? str_repeat( '<p>&nbsp;</p>', 2 ) : '' ) .'</td>';
	}

	$calendar .= '</tr>';
	$calendar .= '</table>';

	return apply_filters( 'bpe_calendar', $calendar, $month, $year, $events );
}

/**
 * Return the calendar controls
 * 
 * The returned controls can be filtered with <code>bpe_calendar_controls</code>
 * The filter takes the moneth and the year as additional arguments.
 * The use of the PHP calendar is not recommended anymore. Fullcalendar.js is the 
 * preferred way to display events in a calendar context from v2.0 onwards.
 *
 * @package	 Core
 * @since 	 1.0
 * 
 * @param	int		$month		The month number to look for. Will be automatically zeroized if necessary
 * @param	int		$year		The year to look up. Needs to be four digits
 */
function bpe_calendar_controls( $month, $year )
{
	global $bp;
	
	$select_month_control = '<select name="month" id="month">';
	
	for( $x = 1; $x <= 12; $x++ )
		$select_month_control.= '<option value="'. $x .'"'. ( ( $x != $month ) ? '' : ' selected="selected"' ) .'>'. bpe_localize_month_name( $x, $year ) .'</option>';

	$select_month_control.= '</select>';
	
	$year_range = 7;
	$select_year_control = '<select name="year" id="year">';

	for( $x = ( $year - floor( $year_range / 2 ) ); $x <= ( $year + floor( $year_range / 2 ) ); $x++ )
		$select_year_control.= '<option value="'. $x .'"'. ( ( $x != $year ) ? '' : ' selected="selected"' ) .'>'. $x .'</option>';

	$select_year_control.= '</select>';
	
	if( bp_get_current_group_id() )
		$link = apply_filters( 'bpe_group_month_link', bp_get_group_permalink( groups_get_current_group() ) . bpe_get_base( 'slug' ) .'/calendar/' );
		
	elseif( bp_displayed_user_id() )
		$link = apply_filters( 'bpe_user_month_link', bp_displayed_user_domain() . bpe_get_base( 'slug' ) .'/calendar/' );
		
	else
		$link = apply_filters( 'bpe_global_month_link', bp_get_root_domain() .'/'. bpe_get_base( 'root_slug' ) .'/calendar/' );
	
	$previous_month_link = '<a class="button" href="'. $link . ( ( $month != 1 ) ? $month - 1 : 12 ) .'/'. ( ( $month != 1 ) ? $year : $year - 1 ) .'/" class="control">'. __( '&larr; Previous Month', 'events' ) .'</a>';
	$next_month_link = '<a class="button" href="'. $link . ( ( $month != 12 ) ? $month + 1 : 1 ) .'/'. ( ( $month != 12 ) ? $year : $year + 1 ) .'/" class="control">'. __( 'Next Month &rarr;', 'events' ) .'</a>';
	
	$controls = '<form method="get" id="cal-controls">'. apply_filters( 'bpe_prev_month_link', $previous_month_link, $month, $year ) .' '. apply_filters( 'bpe_next_month_link', $next_month_link, $month, $year ) .' <span id="cal-controls-dd">'. apply_filters( 'bpe_month_control', $select_month_control, $month, $year ) . apply_filters( 'bpe_year_control', $select_year_control, $month, $year ) .' <input type="submit" name="submit" value="'. __( 'Go', 'events' ) .'" /></span></form>';
	
	return apply_filters( 'bpe_calendar_controls', $controls, $month, $year );
}

/**
 * Return the calendar display
 * 
 * The headings output can be filtered via <code>bpe_get_calendar_headings</code>
 * The type of headings (normal or widget) is an additional parameter.
 *
 * @package	 Core
 * @since 	 1.1
 * 
 * @param	string		$type		Either 'normal' or 'widget'
 */
function bpe_get_calendar_headings( $type = 'normal' )
{
	if( bpe_get_option( 'week_start' ) == 7 )
	{
		if( $type == 'normal' )
			$headings = array( __( 'Sunday', 'events' ), __( 'Monday', 'events' ), __( 'Tuesday', 'events' ), __( 'Wednesday', 'events' ), __( 'Thursday', 'events' ), __( 'Friday', 'events' ), __( 'Saturday', 'events' ) );
		else
			$headings = array( __( 'S', 'events' ), __( 'M', 'events' ), __( 'T', 'events' ), __( 'W', 'events' ), __( 'T', 'events' ), __( 'F', 'events' ), __( 'S', 'events' ) );
	}
	else
	{
		if( $type == 'normal' )
			$headings = array( __( 'Monday', 'events' ), __( 'Tuesday', 'events' ), __( 'Wednesday', 'events' ), __( 'Thursday', 'events' ), __( 'Friday', 'events' ), __( 'Saturday', 'events' ), __( 'Sunday', 'events' ) );
		else
			$headings = array( __( 'M', 'events' ), __( 'T', 'events' ), __( 'W', 'events' ), __( 'T', 'events' ), __( 'F', 'events' ), __( 'S', 'events' ), __( 'S', 'events' ) );
	}
	
	return apply_filters( 'bpe_get_calendar_headings', $headings, $type );
}

/**
 * User calendar display
 *
 * @package	 Core
 * @since 	 1.1
 */
function bpe_user_calendar()
{
	global $bp;

	if( bpe_get_option( 'use_fullcalendar' ) === true ) :
		echo bpe_fullcalendar();

	else :
		$month = apply_filters( 'bpe_user_calendar_month_variable', (int)( ( bp_action_variable( 0 ) ) ? bp_action_variable( 0 ) : gmdate( 'm' ) ) );
		$month = ( $month < 10 ) ? '0'. $month : $month;
	
		$year = apply_filters( 'bpe_user_calendar_year_variable', (int)( ( bp_action_variable( 1 ) ) ? bp_action_variable( 1 ) : gmdate( 'Y' ) ) );

		$events = bpe_get_events( array( 'month' => $month, 'year' => $year, 'sort' => 'calendar', 'per_page' => false, 'user_id' => bp_displayed_user_id(), 'future' => false ) );
		$events = $events['events'];
		
		echo '<h4 class="cal-title"><a href="'. bp_displayed_user_domain() . bpe_get_base( 'slug' ) .'/'. bpe_get_option( 'month_slug' ) .'/'. $month .'/'. $year .'/">'. bpe_localize_month_name( $month, $year ) .' '. $year .'</a></h4>';
		echo bpe_calendar_controls( $month, $year );
		echo bpe_draw_calendar( $month, $year, $events, 'normal', 'user' );	
		?>
		<script type="text/javascript">
		jQuery(document).ready( function() {
			jQuery('form#cal-controls').submit(function() {
				var year = jQuery('#cal-controls #year').val();
				var month = jQuery('#cal-controls #month').val();
				window.location.href = '<?php echo bp_displayed_user_domain() . bpe_get_base( 'slug' ) .'/'. bpe_get_option( 'calendar_slug' ) ?>/'+ month +'/'+ year +'/';
				return false;
			});
		});
		</script>
		<?php
	endif;
}

/**
 * Event output for a normal calendar
 *
 * @package	 Core
 * @since 	 1.4
 */
function bpe_calendar_event_normal( $event, $day, $list_day )
{
	$calendar = '';
	
	// check the day for any events
	if( ( $day >= bpe_get_event_start_date_raw( $event ) ) && ( $day <= bpe_get_event_end_date_raw( $event ) ) )
	{
		$text = ( $day == bpe_get_event_end_date_raw( $event ) ) ? __( ' (end)', 'events' ) : __( ' (cont.)', 'events' );
		
		// no link if the event started already
		if( $day > bpe_get_event_start_date_raw( $event ) && $list_day != 1 )
		{
			$calendar .= '<div class="single-event">'. bpe_get_event_name( $event ) . $text .'</div>';
		}
		else
		{
			$add_cont = '';
			// let's add $text if it's the first day of the month and and the event has started already
			if( $list_day == 1 && $day > bpe_get_event_start_date_raw( $event ) )
				$add_cont = $text;			
		
			// it's the first day or first of the month, so we need a link and the colorbox popup content
			$calendar .= '<div class="single-event">';
			$calendar .= apply_filters( 'bpe_calendar_link', '<a id="colorbox-'. bpe_get_event_id( $event ) .'" rel="event" class="cal-popup" href="#">'. bpe_get_event_name( $event ) . $add_cont .'</a>', $event, $add_cont );
			$calendar .= '</div>';
		}
	}
	
	return apply_filters( 'bpe_calendar_event_normal', $calendar, $day, $list_day );
}

/**
 * JS output for event map
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_calendar_map_js( $event, $show_tags = false )
{
	if( bpe_has_event_location( $event ) )
	{
		$js = '';
		if( $show_tags )
			$js .= '<script type="text/javascript">';
			
		$js .= 'function map_init'. bpe_get_event_id( $event ) .'() {'."\n";
			$js .= 'var coords'. bpe_get_event_id( $event ) .' = new google.maps.LatLng('. bpe_get_event_latitude( $event ) .', '. bpe_get_event_longitude( $event ) .');'."\n";
			$js .= 'var mapOptions'. bpe_get_event_id( $event ) .' = {'."\n";
				$js .= 'zoom: 14,'."\n";
				$js .= 'center: coords'. bpe_get_event_id( $event ) .','."\n";
				$js .= 'navigationControl: true,'."\n";
				$js .= 'mapTypeControl: false,'."\n";
				$js .= 'scaleControl: false,'."\n";
				$js .= 'mapTypeId: google.maps.MapTypeId.HYBRID'."\n";
			$js .= '};'."\n";
			$js .= 'var map'. bpe_get_event_id( $event ) .' = new google.maps.Map(document.getElementById("eventmap'. bpe_get_event_id( $event ) .'"), mapOptions'. bpe_get_event_id( $event ) .');'."\n";
			$js .= 'var marker'. bpe_get_event_id( $event ) .' = new google.maps.Marker({'."\n";
				$js .= 'position: coords'. bpe_get_event_id( $event ) ."\n";
			$js .= '});'."\n";
			$js .= 'marker'. bpe_get_event_id( $event ) .'.setMap(map'. bpe_get_event_id( $event ) .');'."\n";
		$js .= '}'."\n";

		if( $show_tags )
			$js .= '</script>'."\n";
	}
	
	return apply_filters( 'bpe_filter_calendar_maps_js', $js, $event, $show_tags );
}

/**
 * Event output for a normal calendar event output
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_calendar_popup_html( $event, $recurrent = false )
{
	$pu = '<div id="calevent-'. bpe_get_event_id( $event ) .'" class="item-list vevent '. ( ( bpe_is_event_cancelled( $event ) ) ? 'cancelled' : 'active' ) .'">';
	
		$pu .= '<div id="event-actions">';           
			$pu .= '<div class="item-avatar">';
				$pu .= bpe_get_event_image( array( 'event' => $event ) );
			$pu .= '</div>';
			if( $recurrent )
				$pu .= '<span class="activity">'. __( 'Event is not active yet.', 'events' ) .'</span>';
			else
				$pu .= bpe_get_attendance_button( $event );
		$pu .= '</div>';
	
		$pu .= '<div class="calevent-item">';
			if( $recurrent )
				$pu .= '<div class="item-title"><span class="summary">'. bpe_get_event_name( $event ) .'</span></div>';
			else
				$pu .= '<div class="item-title"><a class="url" href="'. bpe_get_event_link( $event ) .'"><span class="summary">'. bpe_get_event_name( $event ) .'</span></a></div>';
			
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
				$pu .= '</dl>';
				$pu .= '<dl class="column">';
					$pu .= sprintf( __( '<dt>Category:</dt><dd><span class="category">%s</span></dd>', 'events' ), bpe_get_event_category( $event ) );
					if( bpe_has_url( $event ) )
						$pu .= sprintf( __( '<dt>Website:</dt><dd><span class="url">%s</span></dd>', 'events' ), bpe_get_event_url( $event ) );

					if( bpe_get_event_group_id( $event ) )
					{
						$pu .= sprintf( __( '<dt>Group:</dt><dd><a href="%s">%s</a></dd>', 'events' ), bpe_event_get_group_permalink( $event ), bpe_event_get_group_name( $event ) );
						
						if( bpe_is_address_enabled() )
							$pu .= sprintf( __( '<dt>Address:</dt><dd>%s<br />%s<br />%s<br />%s</dd>', 'events' ), bpe_event_get_group_address_street( $event ), bpe_event_get_group_address_city( $event ), bpe_event_get_group_address_postcode( $event ), bpe_event_get_group_address_country( $event ) );
					}
				$pu .= '</dl>';
			$pu .= '</div>';
			$pu .= '<div class="item-desc description">'. bpe_get_event_description( $event ) .'</div>';
			
			if( bpe_has_event_location( $event ) )
				$pu .= '<div class="eventmap-wrapper"><div id="eventmap'. bpe_get_event_id( $event ) .'" class="calendar-map"></div></div>';
	
		$pu .= '</div>';
		$pu .= '<div class="action">';
			if( ! $recurrent )
				$pu .= '<span class="activity">'. bpe_get_event_attendees( $event ) .'</span>';
			$pu .= '<span class="event-admin organizer">'. __( 'Creator:', 'events' ) .'<br />'. bpe_get_event_user_avatar( array( 'e' => $event ) ) .'</span>';
		$pu .= '</div>';
	$pu .= '</div>';

	return apply_filters( 'bpe_filter_calendar_colorbox_popup', $pu, $event, $recurrent );
}

/**
 * Fullcalendar.js version
 * 
 * No parameters, is really just a wrapper for the necessary calendar div
 * Filter at your own risk via <code>bpe_fullcalendar_html_wrapper</code>
 *
 * @package	 Core
 * @since 	 2.0
 */
function bpe_fullcalendar()
{
	echo apply_filters( 'bpe_fullcalendar_html_wrapper', '<div id="fullcalendar"></div>' );
}
?>