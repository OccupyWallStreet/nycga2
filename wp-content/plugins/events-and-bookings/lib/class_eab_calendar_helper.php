<?php
/**
 * Holds calendar abstract hub class, 
 * and concrete implementations.
 * V1.3
 */

/**
 * Calendar table hub class.
 */
abstract class WpmuDev_CalendarTable {
	
	protected $_events = array();
	protected $_current_timestamp;
	
	public function __construct ($events) {
		$this->_events = $events;
		// To follow WP Start of week setting
		if ( !$this->start_of_week = get_option('start_of_week') )
			$this->start_of_week = 0;
	}
	/**
	 * Arranges days array acc. to start of week, e.g 1234560 (Week starting with Monday)
	 * @ days: input array
	 */	
	public function arrange( $days ) {
		if ( $this->start_of_week ) {
			for ( $n = 1; $n<=$this->start_of_week; $n++ )
				array_push( $days, array_shift( $days ) );
		}

		return $days;
	}
	
	public function get_timestamp () {
		return $this->_current_timestamp;
	}
	
	public function get_month_calendar ($timestamp=false) {
		$date = $timestamp ? $timestamp : current_time('timestamp');
		
		$this->_current_timestamp = $date;
		
		$year = date('Y', $date);
		$month = date('m', $date);
		$time = strtotime("{$year}-{$month}-01");
		
		$days = (int)date('t', $time);
		$first = (int)date('w', strtotime(date('Y-m-01', $time)));
		$last = (int)date('w', strtotime(date('Y-m-' . $days, $time)));
		
		
		$post_info = array();
		foreach ($this->_events as $event) {
			$post_info[] = $this->_get_item_data($event);
		}

		$tbl_id = $this->get_calendar_id();
		$tbl_id = $tbl_id ? "id='{$tbl_id}'" : '';
		$tbl_class = $this->get_calendar_class();
		$tbl_class = $tbl_class ? "class='{$tbl_class}'" : '';
		
		$ret = '';
		$ret .= "<table width='100%' {$tbl_id} {$tbl_class}>";
		$ret .= $this->_get_table_meta_row('thead');
		$ret .= '<tbody>';
		
		$ret .= $this->_get_first_row();
		
		if ( $first > $this->start_of_week )
			$ret .= '<tr><td class="no-left-border" colspan="' . ($first - $this->start_of_week) . '">&nbsp;</td>';
		else if ( $first < $this->start_of_week )
			$ret .= '<tr><td class="no-left-border" colspan="' . (7 + $first - $this->start_of_week) . '">&nbsp;</td>';
		else
			$ret .= '<tr>';
		
		
		for ($i=1; $i<=$days; $i++) {
			$date = date('Y-m-' . sprintf("%02d", $i), $time);
			$dow = (int)date('w', strtotime($date));
			$current_day_start = strtotime("{$date} 00:00"); 
			$current_day_end = strtotime("{$date} 23:59");
			if ($this->start_of_week == $dow) $ret .= '</tr><tr>';
			
			$this->reset_event_info_storage();
			foreach ($post_info as $ipost) {
				for ($k = 0; $k < count($ipost['event_starts']); $k++) {
					$start = strtotime($ipost['event_starts'][$k]);
					$end = strtotime($ipost['event_ends'][$k]);
					if ($start < $current_day_end && $end > $current_day_start) {
						$this->set_event_info(
							array('start' => $start, 'end'=> $end), 
							array('start' => $current_day_start, 'end'=> $current_day_end),
							$ipost
						);
					}
				}
			} 
			
			$activity = $this->get_event_info_as_string($i);
			$activity = $activity ? $activity : "<p>{$i}</p>";
			$today = ($date == date('Y-m-d')) ? 'class="today"' : '';
			$ret .= "<td {$today}>{$activity}</td>";
		}
		
		if ( $last > $this->start_of_week )
			$ret .= '<td class="no-right-border" colspan="' . (6 - $last + $this->start_of_week) . '">&nbsp;</td></tr>'; 
		else if ( $last + 1 == $this->start_of_week )
			$ret .= '</tr>'; 
		else
			$ret .= '<td class="no-right-border" colspan="' . (6 + $last - $this->start_of_week) . '">&nbsp;</td></tr>';
		
		$ret .= $this->_get_last_row();
		
		$ret .= '</tbody>';
		$ret .= $this->_get_table_meta_row('tfoot');
		$ret .= '</table>';
		
		return $ret;
	}
	
	protected function _get_table_meta_row ($which) {
		$day_names_array = $this->arrange( $this->get_day_names() );
		$cells = '<th>' . join('</th><th>', $day_names_array) . '</th>';
		return "<{$which}><tr>{$cells}</tr></{$which}>";
	}
	
	public function get_day_names () {
		return array(
			__('Sunday', $this->_get_text_domain()),
			__('Monday', $this->_get_text_domain()),
			__('Tuesday', $this->_get_text_domain()),
			__('Wednesday', $this->_get_text_domain()),
			__('Thursday', $this->_get_text_domain()),
			__('Friday', $this->_get_text_domain()),
			__('Saturday', $this->_get_text_domain()),
		);
	}
	
	
	protected function _get_first_row () { return ''; }
	protected function _get_last_row () { return ''; }

	abstract protected function _get_text_domain ();
	
	abstract public function get_calendar_id ();
	abstract public function get_calendar_class ();
	
	/**
	 * @return array Hash of post data
	 */
	abstract protected function _get_item_data ($post);
	
	abstract public function reset_event_info_storage ();
	abstract public function set_event_info ($event_tstamps, $current_tstamps, $event_info);
	abstract public function get_event_info_as_string ($day);
	
}


/**
 * Abstract event hub class.
 */
abstract class Eab_CalendarTable extends WpmuDev_CalendarTable {
	
	protected function _get_item_data ($post) {
		$event = ($post instanceof Eab_EventModel) ? $post : new Eab_EventModel($post);
		$event_starts = $event->get_start_dates();
		$event_ends = $event->get_end_dates();
		$res = array(
			'id' => $event->get_id(),
			'title' => $event->get_title(),
			'event_starts' => $event_starts,
			'event_ends' => $event_ends,
			'status_class' => Eab_Template::get_status_class($event),
			'event_venue' => $event->get_venue_location(),
		);
		if (isset($post->blog_id)) $res['blog_id'] = $post->blog_id;
		return $res;
	}
	
	protected function _get_text_domain () {
		return Eab_EventsHub::TEXT_DOMAIN;
	}
}


/**
 * Upcoming calendar widget concrete implementation.
 */
class Eab_CalendarTable_UpcomingCalendarWidget extends Eab_CalendarTable {
	
	protected $_titles = array();
	protected $_data = array();
	
	public function get_calendar_id () { return false; }
	public function get_calendar_class () { return 'eab-upcoming_calendar_widget'; }
	
	public function get_day_names () {
		return array(
			__('Su', Eab_EventsHub::TEXT_DOMAIN),
			__('Mo', Eab_EventsHub::TEXT_DOMAIN),
			__('Tu', Eab_EventsHub::TEXT_DOMAIN),
			__('We', Eab_EventsHub::TEXT_DOMAIN),
			__('Th', Eab_EventsHub::TEXT_DOMAIN),
			__('Fr', Eab_EventsHub::TEXT_DOMAIN),
			__('Sa', Eab_EventsHub::TEXT_DOMAIN),
		);
	}
	
	protected function _get_table_meta_row ($which) {
		if ('tfoot' == $which) return '';
		return parent::_get_table_meta_row($which);
	}
	
	public function reset_event_info_storage () {
		$this->_titles = array();
		$this->_data = array();
	}

	public function set_event_info ($event_tstamps, $current_tstamps, $event_info) {
		$this->_titles[] = esc_attr($event_info['title']);
		$this->_data[] = '<a class="wpmudevevents-upcoming_calendar_widget-event ' . $event_info['status_class'] . '" href="' . get_permalink($event_info['id']) . '">' . 
			$event_info['title'] .
			'<span class="wpmudevevents-upcoming_calendar_widget-event-info">' . 
				date_i18n(get_option('date_format'), $event_tstamps['start']) . ' ' . $event_info['event_venue'] .
			'</span>' .
		'</a>'; 
	}
	
	public function get_event_info_as_string ($day) {
		$activity = '';
		if ($this->_titles && $this->_data) {
			$ttl = join(', ', $this->_titles);
			$einfo = join('<br />', $this->_data);
			$activity = "<p><a href='#' title='{$ttl}'>{$day}</a><span class='wdpmudevevents-upcoming_calendar_widget-info_wrapper' style='display:none'>{$einfo}</span></p>";
		}
		return $activity;
	}

	protected function _get_last_row () {
		$time = $this->get_timestamp();
		return '<tr>' .
			'<td>' .
				'<a class="' . $this->get_calendar_class() . '-navigation-link eab-navigation-next eab-time_unit-year" href="' . 
					Eab_Template::get_archive_url_next($time, true) . '">' . 
					'&nbsp;&laquo;' .
				'</a>' .
			'</td>' .
			'<td>' .
				'<a class="' . $this->get_calendar_class() . '-navigation-link eab-navigation-next eab-time_unit-month" href="' . 
					Eab_Template::get_archive_url_next_year($time, false) . '">' . 
					'&nbsp;&lsaquo;' .
				'</a>' .
			'</td>' .
			'<td colspan="3" style="text-align:center;">' .
				'<input type="hidden" class="eab-cuw-calendar_date" value="' . $time . '" />' .
				'<a href="' . Eab_Template::get_archive_url($time, true) . '" class="' . $this->get_calendar_class() . '-navigation-link eab-cuw-calendar_date">' . date('M Y', $time) . '</a>' .
			'</td>' .
			'<td>' .
				'<a class="' . $this->get_calendar_class() . '-navigation-link eab-navigation-prev eab-time_unit-month" href="' . 
					Eab_Template::get_archive_url_prev_year($time, false) . '">&rsaquo;&nbsp;' . 
				'</a>' .
			'</td>' .
			'<td>' .
				'<a class="' . $this->get_calendar_class() . '-navigation-link eab-navigation-prev eab-time_unit-year" href="' . 
					Eab_Template::get_archive_url_prev($time, true) . '">&raquo;&nbsp;' . 
				'</a>' .
			'</td>' .
		'</tr>';
	}
}


class Eab_CalendarTable_EventArchiveCalendar extends Eab_CalendarTable {
	
	protected $_data = array();
	
	public function get_calendar_id () { return false; }
	public function get_calendar_class () { return false; }
	public function reset_event_info_storage () { $this->_data = array(); }
	
	public function set_event_info ($event_tstamps, $current_tstamps, $event_info) {
		$this->_data[] = '<a class="wpmudevevents-calendar-event ' . $event_info['status_class'] . '" href="' . get_permalink($event_info['id']) . '">' . 
			$event_info['title'] .
			'<span class="wpmudevevents-calendar-event-info">' . 
				date_i18n(get_option('date_format'), $event_tstamps['start']) . ' ' . $event_info['event_venue'] .
			'</span>' . 
		'</a>'; 
	}
	
	public function get_event_info_as_string ($day) {
		$activity = '';
		if ($this->_data) {
			$activity = '<p>' . $day . '<br />' . join(' ', $this->_data) . '</p>';
		}
		return $activity;
	}
	
	public function get_month_calendar ($timestamp) {
		return parent::get_month_calendar($timestamp) . $this->_get_js();
	}
	
	protected function _get_js () {
		if (defined('EAB_EVENT_ARCHIVE_CALENDAR_HAS_JS')) return false;
		define('EAB_EVENT_ARCHIVE_CALENDAR_HAS_JS', true);
		return <<<EabEctEacJs
<script type="text/javascript">
(function ($) {
$(function () {
// Info popups
$(".wpmudevevents-calendar-event")
	.mouseenter(function () {
		$(this).find(".wpmudevevents-calendar-event-info").show();
	})
	.mouseleave(function () {
		$(this).find(".wpmudevevents-calendar-event-info").hide();
	})
;
});
})(jQuery);
</script>
EabEctEacJs;
	}
}



class Eab_CalendarTable_EventShortcodeCalendar extends Eab_CalendarTable {
		
	protected $_data = array();
	protected $_class;
	protected $_id;
	protected $_use_footer = true;
	
	public function get_calendar_id () { return $this->_id; }
	public function get_calendar_class () { return $this->_class; }
	public function reset_event_info_storage () { $this->_data = array(); }
	
	public function set_class ($class) {
		$this->_class = sanitize_html_class($class);
	}
	
	public function set_footer ($use) {
		$this->_use_footer = (bool)$use;
	}
	
	protected function _get_table_meta_row ($which) {
		if ('tfoot' == $which && !$this->_use_footer) return '';
		return parent::_get_table_meta_row($which);
	}
	
	public function set_event_info ($event_tstamps, $current_tstamps, $event_info) {
		$title = esc_attr($event_info['event_venue']);
		$permalink = isset($event_info['blog_id']) ? get_blog_permalink($event_info['blog_id'], $event_info['id']) : get_permalink($event_info['id']);
		$this->_data[] = '<a title="' . $title . '" class="wpmudevevents-calendar_shortcode-event ' . $event_info['status_class'] . '" href="' . $permalink . '">' . 
			$event_info['title'] .
		'</a>'; 
	}
	
	public function get_event_info_as_string ($day) {
		$activity = '';
		if ($this->_data) {
			$activity = '<p>' . $day . '<br />' . join('<br />', $this->_data) . '</p>';
		}
		return $activity;
	}
}
