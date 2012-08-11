<?php

//class Eab_NYCGA_Events_FrontPageEditing extends Eab_Events_FrontPageEditing {
	//function nycga_front_end_editing() {
		//parent::Eab_Events_FrontPageEditing( false, $name = __( 'Edit Events', 'incsub-event' ) );
	//}
//}

add_filter( 'eab-events-event_date_string', 'start_date_only', 10, 4 );
function start_date_only($content, $event_id, $start, $end ) {

	$start_date_str = (date('Y-m-d', $start) != date('Y-m-d', $end))
				? date_i18n(get_option('date_format'), $end) : '';

	return sprintf(
				__('On %s <span class=&quot;wpmudevevents-date_format-start&quot;>from %s</span> <span class=&quot;wpmudevevents-date_format-end&quot;>to %s</span><br />', 'eab'),
				'<span class=&quot;wpmudevevents-date_format-start_date&quot;>' . date_i18n(get_option('date_format'), $start) . '</span>',
				'<span class=&quot;wpmudevevents-date_format-start_time&quot;>' . date_i18n(get_option('time_format'), $start) . '</span>',
				'<span class=&quot;wpmudevevents-date_format-end_date&quot;>' . $end_date_str . '</span> <span class=&quot;wpmudevevents-date_format-end_time&quot;>' . date_i18n(get_option('time_format'), $end+3600) . '</span>'
			);
}


public static function get_user_organized_events ($user_id) {
		$events = Eab_CollectionFactory::get_user_organized_events($user_id); 
		
		$ret = '<div class="wpmudevevents-user_bookings wpmudevevents-events-user_organized">';
		foreach ($events as $event) {
			if ($event->is_recurring()) continue;
			$ret .= '<h4>' . self::get_event_link($event) . '</h4>';
			$ret .= '<div class="wpmudevevents-event-meta">' . 
				self::get_event_dates($event) .
				'<br />' .
				$event->get_venue_location() . 
			'</div>';
		}
		$ret .= '</div>';
		return $ret;
	}

?>