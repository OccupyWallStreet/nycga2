<?php

abstract class WpmuDev_DatedItem {
	
	/**
	 * Packs event start dates as an array of (string)MySQL dates.
	 * @return array Start dates. 
	 */
	abstract public function get_start_dates ();
	abstract public function has_no_start_time ($key=0);
	
	/**
	 * Packs event end dates as an array of (string)MySQL dates.
	 * @return array End dates. 
	 */
	abstract public function get_end_dates ();
	abstract public function has_no_end_time ($key=0);
	
	/**
	 * Gets indexed start date as (string)MySQL date.
	 * Calls get_start_dates() if needed.
	 * @param int Date index
	 * @return string Date.
	 */
	public function get_start_date ($idx=0) {
		$dates = $this->get_start_dates();
		return isset($dates[$idx]) ? $dates[$idx] : false;
	}
	
	/**
	 * Gets indexed start date timestamp.
	 * @param int Date index
	 * @return int Date timestamp.
	 */
	public function get_start_timestamp ($idx=0) {
		return strtotime($this->get_start_date($idx));
	}

	/**
	 * Gets indexed end date as (string)MySQL date.
	 * Calls get_end_dates() if needed.
	 * @param int Date index
	 * @return string Date.
	 */
	public function get_end_date ($idx=0) {
		$dates = $this->get_end_dates();
		return isset($dates[$idx]) ? $dates[$idx] : false;
	}

	/**
	 * Gets last end date.
	 * @return string Date.
	 */
	public function get_last_end_date () {
		$dates = $this->get_end_dates();
		$idx = count($dates);
		return isset($dates[$idx]) ? $dates[$idx] : false;
	}
	
	/**
	 * Gets indexed start date timestamp.
	 * @param int Date index
	 * @return int Date timestamp.
	 */
	public function get_end_timestamp ($idx=0) {
		return strtotime($this->get_end_date($idx));
	}

	/**
	 * Gets last end date timestamp.
	 * @return int Date timestamp.
	 */
	public function get_last_end_timestamp () {
		return strtotime($this->get_last_end_date());
	}
}


abstract class WpmuDev_RecurringDatedItem extends WpmuDev_DatedItem {
	
	const RECURRANCE_DAILY = 'daily';
	const RECURRANCE_WEEKLY = 'weekly';
	const RECURRANCE_MONTHLY = 'monthly';
	const RECURRANCE_YEARLY = 'yearly';
	
	const RECURRENCE_STATUS = 'recurrent';
	const RECURRENCE_TRASH_STATUS = 'recurrent_trash';
	
	/**
	 * @return array Hash of supported recurrance items and their labels
	 */
	abstract public function get_supported_recurrence_intervals ();
	
	/**
	 * @return array A list of instances occuring between start and end dates.
	 */
	abstract public function spawn_recurring_instances ($start, $end, $interval, $time_parts); // @TODO: REFACTOR
	
	/**
	 * @return mixed Recurrence interval (see constants)
	 */
	abstract public function get_recurrence ();
	
	/**
	 * @param mixed $key See constants. Optional recurrence constant. If not passed, will check if the item recurs at all.
	 * @return bool
	 */
	public function is_recurring ($key=false) {
		$recurrence = $this->get_recurrence();
		if (!$key) return $recurrence;
		else return ($key == $recurrence);
	}

	public function is_recurring_child () {
		return $this->get_parent();
	}
}


abstract class WpmuDev_DatedVenueItem extends WpmuDev_RecurringDatedItem {
	
	const VENUE_AS_ADDRESS = 'address';
	const VENUE_AS_MAP = 'map';
	
	/**
	 * Pack venue info, and return it.
	 * Venue type agnostic.
	 * @return string Venue
	 */
	abstract public function get_venue ();
	
	/**
	 * Does the event has venue info set?
	 * @return bool
	 */
	public function has_venue () {
		return $this->get_venue() ? true : false; 
	}
	
	/**
	 * Returns venue as requested type.
	 * @param mixed $as Venue type (see constants)
	 * @param array $args Optional map overrides
	 * @return string Venue
	 */
	public function get_venue_location ($as=false, $args=array()) {
		$as = $as ? $as : self::VENUE_AS_ADDRESS;
		$venue = $this->get_venue();
		return (self::VENUE_AS_ADDRESS == $as) ? $this->_venue_to_address($venue) : $this->_venue_to_map($venue, $args);
	}
	
	/**
	 * Is the event venue a map?
	 * @param string $venue Optional venue
	 * @return bool
	 */
	public function has_venue_map ($venue=false) {
		if (!class_exists('AgmMapModel')) return false;
		$venue = $venue ? $venue : $this->get_venue();
		if (!$venue) {
			// Check associated map
			$map_id = get_post_meta($this->get_id(), 'agm_map_created', true);
			return $map_id ? true : false;
		}
		if (preg_match_all('/map id="([0-9]+)"/', $venue, $matches) > 0) return true;
		$map_id = get_post_meta($this->get_id(), 'agm_map_created', true);
		return $map_id ? true : false;
	} 
	
	/**
	 * Convert venue map to address.
	 * @param string $venue Venue
	 * @return string Venue address
	 */
	private function _venue_map_to_address ($venue) {
		$venue = $venue ? $venue : $this->get_venue();
		$map = $this->_get_venue_map($venue);
		return @$map['markers'][0]['title'];
	}
	
	/**
	 * Venue address getting dispatcher.
	 * @param string $venue Venue
	 * @return string Venue address
	 */
	private function _venue_to_address ($venue) {
		$venue = $venue ? $venue : $this->get_venue();
		return $this->has_venue_map($venue) ? $this->_venue_map_to_address($venue) : $venue;
	}
	
	/**
	 * Get venue map tag.
	 * @param string $venue Venue
	 * @param array $args Optional map overrides
	 * @return string Map tag
	 */
	private function _venue_to_map ($venue, $args=array()) {
		$venue = $venue ? $venue : $this->get_venue();
		if (!class_exists('AgmMarkerReplacer')) return $venue;
		if (!$this->has_venue_map($venue)) return $venue;
		$codec = new AgmMarkerReplacer;

		return $codec->create_tag($this->_get_venue_map($venue), $args);
	}
	
	/**
	 * Get map object.
	 * @param string $venue Venue
	 * @param array $args Optional map overrides
	 * @return object Map object
	 */
	private function _get_venue_map ($venue, $args=array()) { 
		$venue = $venue ? $venue : $this->get_venue();
		$map_id = false;
		if (!class_exists('AgmMapModel')) return $venue;
		if (preg_match_all('/map id="([0-9]+)"/', $venue, $matches) <= 0) {
			$map_id = get_post_meta($this->get_id(), 'agm_map_created', true);
			if (!$map_id) return $venue;
		} else if (!isset($matches[1]) || !isset($matches[1][0])) return $venue;
		
		$model = new AgmMapModel();
		return $map_id ? $model->get_map($map_id) : $model->get_map($matches[1][0]);
	}
}



abstract class WpmuDev_DatedVenuePremiumItem extends WpmuDev_DatedVenueItem {
	
	/**
	 * Does the event require payment?
	 * @return bool
	 */
	public function is_premium () {
		return $this->get_price() ? true : false;
	}
	
	/**
	 * Packs price meta info and returns it.
	 * @return price
	 */
	abstract public function get_price ();
	
	abstract public function user_paid ($user_id=false);
}


abstract class WpmuDev_DatedVenuePremiumModel extends WpmuDev_DatedVenuePremiumItem {
	
	const POST_STATUS_TRASH = 'trash';
	
	abstract public function get_id();
	abstract public function get_title();
	abstract public function get_author();
	abstract public function get_excerpt();
	abstract public function get_content();
	abstract public function get_type();
	abstract public function get_parent();
	abstract public function is_trashed();
}



class Eab_EventModel extends WpmuDev_DatedVenuePremiumModel {
	
	const POST_TYPE = 'incsub_event';
	
	const STATUS_OPEN = 'open';
	const STATUS_CLOSED = 'closed';
	const STATUS_ARCHIVED = 'archived';
	const STATUS_EXPIRED = 'expired';
	
	const BOOKING_YES = 'yes';
	const BOOKING_MAYBE = 'maybe';
	const BOOKING_NO = 'no';
	
	private $_event_id;
	private $_event;
	
	private $_start_dates;
	private $_end_dates;
	
	private $_venue;
	private $_price;
	private $_status;
	
	public function __construct ($post=false) {
		$this->_event_id = is_object($post) ? (int)@$post->ID : $post;
		$this->_event = $post;
	}
	
	/**
	 * General purpose get_* override.
	 * Used for getting post properties.
	 */
	/*
	public function __call ($method, $args) {
		if ('get_' != substr($method, 0, 4)) return false;
		if (!$this->_event) return false;
		$what =  substr($method, 4);
		$property = "post_{$what}";
		if (isset($this->_event->$property)) return $this->_event->$property;
		if (isset($this->_event->$what)) return $this->_event->$what;
		return false;
	}
	*/
	
	public function get_id () {
		return $this->_event_id;
	}

	public function get_title () {
		return $this->_event->post_title;
	}

	public function get_author () {
		return $this->_event->post_author;
	}

	public function get_excerpt () {
		return $this->_event->post_excerpt;
	}

	public function get_content () {
		return $this->_event->post_content;
	}
	
	public function get_type () {
		return $this->_event->post_type;
	}

	public function get_parent () {
		return $this->_event->post_parent;
	}

	public function is_trashed () {
		return ($this->_event->post_status == self::POST_STATUS_TRASH);
	}

	public function get_categories () {
		$list = get_the_terms($this->get_id(), 'eab_events_category');
		return is_wp_error($list) ? false : $list;
	}

	public function get_category_ids () {
		$list = $this->get_categories();
		if (!$list) return false;
		$cats = array();
		foreach ($list as $category) $cats[] = $category->term_id;
		return $cats;
	}


/* ----- Date/Time methods ----- */

	public function has_no_start_time ($key=0) {
		$raw = get_post_meta($this->get_id(), 'incsub_event_no_start');
		return $raw[$key];
	}
	
	public function has_no_end_time ($key=0) {
		$raw = get_post_meta($this->get_id(), 'incsub_event_no_end');
		return $raw[$key];
	}
	
	/**
	 * Packs event start dates as an array of (string)MySQL dates.
	 * @return array Start dates. 
	 */
	public function get_start_dates () {
		if ($this->_start_dates) return $this->_start_dates;
		$this->_start_dates = get_post_meta($this->get_id(), 'incsub_event_start');
		return $this->_start_dates;
	}
	
	/**
	 * Packs event end dates as an array of (string)MySQL dates.
	 * @return array End dates. 
	 */
	public function get_end_dates () {
		if ($this->_end_dates) return $this->_end_dates;
		$this->_end_dates = get_post_meta($this->get_id(), 'incsub_event_end');
		return $this->_end_dates;
	}
	
	
/* ----- Recurrence methods ----- */

	public function get_supported_recurrence_intervals () {
		return array (
			self::RECURRANCE_DAILY => __('Day', Eab_EventsHub::TEXT_DOMAIN),
			self::RECURRANCE_WEEKLY => __('Week', Eab_EventsHub::TEXT_DOMAIN),
			self::RECURRANCE_MONTHLY => __('Month', Eab_EventsHub::TEXT_DOMAIN),
			self::RECURRANCE_YEARLY => __('Year', Eab_EventsHub::TEXT_DOMAIN),
		);
	}
	
	public function get_recurrence () {
		return get_post_meta($this->get_id(), 'eab_event_recurring', true);
	}
	
	public function get_recurrence_parts () {
		return get_post_meta($this->get_id(), 'eab_event_recurrence_parts', true);
	}
	
	public function get_recurrence_starts () {
		return get_post_meta($this->get_id(), 'eab_event_recurrence_starts', true);
	}
	
	public function get_recurrence_ends () {
		return get_post_meta($this->get_id(), 'eab_event_recurrence_ends', true);
	}
	
	protected function _get_recurring_children_ids () {
		$events = Eab_CollectionFactory::get_all_recurring_children_events($this);
		$ids = array();
		foreach ($events as $event) {
			$ids[] = $event->get_id();
		}
		return $ids;
	}
	
	public function trash_recurring_instances () {
		global $wpdb;
		$ids = $this->_get_recurring_children_ids();
		$id_str = join(',', $ids);
		$wpdb->query("UPDATE {$wpdb->posts} SET post_status='" . self::RECURRENCE_TRASH_STATUS . "' WHERE ID IN ({$id_str})");
	}

	public function untrash_recurring_instances () {
		global $wpdb;
		$ids = $this->_get_recurring_children_ids();
		$id_str = join(',', $ids);
		$wpdb->query("UPDATE {$wpdb->posts} SET post_status='" . self::RECURRENCE_STATUS . "' WHERE ID IN ({$id_str})");
	}
	
	public function delete_recurring_instances () {
		global $wpdb;
		$ids = $this->_get_recurring_children_ids();
		$id_str = join(',', $ids);
		$wpdb->query("DELETE FROM {$wpdb->posts} WHERE ID IN ({$id_str})");
		$wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id IN ({$id_str})");
	}
	
	public function spawn_recurring_instances ($start, $end, $interval, $time_parts) {
		if ($this->is_recurring()) {
			$this->delete_recurring_instances();
		}
		
		$instances = $this->_get_recurring_instances_timestamps($start, $end, $interval, $time_parts);
	
		$duration = (float)@$time_parts['duration'];
		$duration = $duration ? $duration : 1;
		
		$venue = $this->get_venue();
		foreach ($instances as $key => $instance) {
			$post = array(
				'post_type' => self::POST_TYPE,
				'post_status' => self::RECURRENCE_STATUS,
				'post_parent' => $this->get_id(),
				'post_name' => "{$this->_event->post_name}-{$key}",
				'post_title' => $this->get_title(),
				'post_author' => $this->get_author(),
				'post_excerpt' => $this->get_excerpt(),
				'post_content' => $this->get_content(),
			);
			global $wpdb;
			if (false !== $wpdb->insert($wpdb->posts, $post)) {
				$post_id = $wpdb->insert_id;

				$event_cats = $this->get_category_ids();
				if ($event_cats) {
					wp_set_post_terms($post_id, $event_cats, 'eab_events_category', false);
					do_action('eab-events-recurrent_event_child-assigned_taxonomies', $post_id, $event_cats);
				}
				
				update_post_meta($post_id, 'incsub_event_start', date("Y-m-d H:i:s", $instance));
				update_post_meta($post_id, 'incsub_event_end', date("Y-m-d H:i:s", $instance + ($duration * 3600)));
				update_post_meta($post_id, 'incsub_event_venue', $venue);
				update_post_meta($post_id, 'incsub_event_status', self::STATUS_OPEN);
				if ($this->is_premium()) {
					update_post_meta($post_id, 'incsub_event_paid', 1);
					update_post_meta($post_id, 'incsub_event_fee', $this->get_price());
				}
				do_action('eab-events-recurrent_event_child-save_meta', $post_id);
			}
		}
		update_post_meta($this->get_id(), 'eab_event_recurring', $interval);
		update_post_meta($this->get_id(), 'eab_event_recurrence_parts', $time_parts);
		update_post_meta($this->get_id(), 'eab_event_recurrence_starts', $start);
		update_post_meta($this->get_id(), 'eab_event_recurrence_ends', $end);
	}
	
	protected function _get_recurring_instances_timestamps ($start, $end, $interval, $time_parts) {
		$instances = array();

		if (self::RECURRANCE_DAILY == $interval) {
			for ($i = $start; $i <= $end; $i+=86400) {
				$timestamp = date("Y-m-d", $i) . ' ' . $time_parts['time'];
				$instances[] = strtotime($timestamp);
			}
		}

		if (self::RECURRANCE_WEEKLY == $interval) {
			for ($i = 0; $i<=6; $i++) {
				if (!in_array($i, $time_parts['weekday'])) continue;
				$begin = strtotime("this Sunday", $start) + ($i * 86400);
				$increment = 7*86400;
				for ($j = $begin; $j<=$end; $j+=$increment) {
					$timestamp = date('Y-m-d', $j) . ' ' . $time_parts['time'];
					$instances[] = strtotime($timestamp);
				}
			}
		}
		
		if (self::RECURRANCE_MONTHLY == $interval) {
			$month_days = date('t', $start)*86400;
			for ($i = $start; $i <= $end; $i+=$month_days) {
				$month_days = date('t', $i)*86400;
				$timestamp = date("Y-m-" . $time_parts['day'], $i) . ' ' . $time_parts['time'];
				$instances[] = strtotime($timestamp);
			}
		}
		
		if (self::RECURRANCE_YEARLY == $interval) {
			$year_days = (date('L', $start) ? 366 : 365) * 86400;
			for ($i = $start; $i <= $end; $i+=$year_days) {
				$year_days = (date('L', $i) ? 366 : 365) * 86400;
				$timestamp = date("Y-" . $time_parts['month'] . "-" . $time_parts['day'], $i) . ' ' . $time_parts['time'];
				$instances[] = strtotime($timestamp);
			}
		}	
		return $instances;	
	}
	

/* ----- Venue methods ----- */
	
	/**
	 * Pack venue info, and return it.
	 * Venue type agnostic.
	 * @return string Venue
	 */
	public function get_venue () {
		if ($this->_venue) return $this->_venue;
		$this->_venue = get_post_meta($this->get_id(), 'incsub_event_venue', true);
		return $this->_venue;
	}
	
	
/* ----- Price methods ----- */

	/**
	 * Packs price meta info and returns it.
	 * @return price
	 */
	public function get_price () {
		if ($this->_price) return apply_filters('eab-payment-event_price', $this->_price, $this->get_id());
		$this->_price = get_post_meta($this->get_id(), 'incsub_event_fee', true);
		return apply_filters('eab-payment-event_price', $this->_price, $this->get_id());
	}
	
	public function user_paid ($user_id=false) {
		$user_id = $this->_to_user_id($user_id);
		$booking_id = $this->get_user_booking_id($user_id);
		return $this->get_booking_paid($booking_id);
	}
	
/* ----- Status methods ----- */

	/**
	 * Is event open?
	 * @return bool
	 */
	public function is_open () {
		return (self::STATUS_OPEN == $this->get_status()) ? true : false;
	}
	
	/**
	 * Is event closed?
	 * @return bool
	 */
	public function is_closed () {
		return (self::STATUS_CLOSED == $this->get_status()) ? true : false;
	}
	
	/**
	 * Is event archived?
	 * @return bool
	 */
	public function is_archived () {
		return (self::STATUS_ARCHIVED == $this->get_status()) ? true : false;
	}
	
	/**
	 * Is event expired?
	 * @return bool
	 */
	public function is_expired () {
		return (self::STATUS_EXPIRED == $this->get_status()) ? true : false;
	}
	
	/**
	 * Pack and return event status info.
	 * @return mixed Event status (see constants)
	 */
	public function get_status () {
		if ($this->_status) return $this->_status;
		$this->_status = get_post_meta($this->get_id(), 'incsub_event_status', true);
		return $this->_status;
	}

	/**
	 * Packs and sets event status.
	 * @param mixed Event status (see constants)
	 * @return mixed Event status (see constants)
	 */
	public function set_status ($status) {
		$this->_status = $status;
		update_post_meta($this->get_id(), 'incsub_event_status', $status);
		return $this->_status;
	}
	
/* ----- Booking methods ----- */

	/**
	 * Does the event have some RSVPs?
	 * @param bool $coming Only count positive RSVPs (yes and maybe)
	 * @return array
	 */
	public function has_bookings($coming=true) {
		global $wpdb;

		return $coming
			? $wpdb->get_results($wpdb->prepare("SELECT id FROM " . Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE) . " WHERE event_id = %d AND status != 'no';", $this->get_id()))
			: $wpdb->get_results($wpdb->prepare("SELECT id FROM " . Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE) . " WHERE event_id = %d;", $this->get_id()))
		;
	}
	
	public function get_rsvps () {
		global $wpdb;
		$rsvps = array(
			self::BOOKING_YES => array(),
			self::BOOKING_MAYBE => array(),
			self::BOOKING_NO => array(),
		);
		$bookings = $wpdb->get_results($wpdb->prepare("SELECT user_id, status FROM ".Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE)." WHERE event_id = %d;", $this->get_id()));
		foreach ($bookings as $booking) {
			$user_data = get_userdata($booking->user_id);
			$rsvps[$booking->status][] = $user_data->user_login;
		}
		return $rsvps;
	}
	
	public function get_user_booking_id ($user_id=false) {
		$user_id = (int)$this->_to_user_id($user_id);
		if (!$user_id) return false;
		
		global $wpdb;
		return (int)$wpdb->get_var($wpdb->prepare("SELECT id FROM " . Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE) . " WHERE event_id = %d AND user_id = %d;", $this->get_id(), $user_id));
	}
	
	public static function get_booking ($booking_id) {
		$booking_id = (int)$booking_id;
		if (!$booking_id) return false;
		
		global $wpdb;   
		return $wpdb->get_row($wpdb->prepare("SELECT * FROM " . Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE) . " WHERE id = %d;", $booking_id));
	}

	public function get_user_booking ($user_id=false) {
		$user_id = (int)$this->_to_user_id($user_id);
		if (!$user_id) return false;
		
		global $wpdb;
		return $wpdb->get_row($wpdb->prepare("SELECT * FROM " . Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE) . " WHERE event_id = %d AND user_id = %d;", $this->get_id(), $user_id));
	}
	
	public static function get_booking_status ($booking_id) {
		$booking_id = (int)$booking_id;
		if (!$booking_id) return false;
		
		global $wpdb;   
		return $wpdb->get_var($wpdb->prepare("SELECT status FROM " . Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE) . " WHERE id = %d;", $booking_id));
	}
	
	public function get_user_booking_status ($user_id=false) {
		$user_id = (int)$this->_to_user_id($user_id);
		if (!$user_id) return false;
		
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare("SELECT status FROM " . Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE) . " WHERE event_id = %d AND user_id = %d;", $this->get_id(), $user_id));
	}
	
	public function user_is_coming ($strict=false, $user_id=false) {
		$user_id = $this->_to_user_id($user_id);
		$checks = array(self::BOOKING_YES);
		if (!$strict) $checks[] = self::BOOKING_MAYBE;
		return in_array($this->get_user_booking_status($user_id), $checks);
	}
	
	public function get_booking_paid ($booking_id) {
		return $this->get_booking_meta($booking_id, 'booking_transaction_key');
	}
	
	public static function get_booking_meta ($booking_id, $meta_key, $default=false) {
		$booking_id = (int)$booking_id;
		if (!$booking_id) return $default;
		
		global $wpdb;
		$meta_value = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM " . Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_META_TABLE) . " WHERE booking_id = %d AND meta_key = %s;", $booking_id, $meta_key));
		return $meta_value ? $meta_value : $default;
	}
	
	public static function update_booking_meta ($booking_id, $meta_key, $meta_value) {
		$booking_id = (int)$booking_id;
		if (!$booking_id) return false;
		
		global $wpdb;
		$meta_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM " . Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_META_TABLE) . " WHERE booking_id = %d AND meta_key = %s;", $booking_id, $meta_key));
		if (!$meta_id) {
			return $wpdb->query($wpdb->prepare("INSERT INTO " . Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_META_TABLE) . " VALUES (null, %d, %s, %s);", $booking_id, $meta_key, $meta_value));
		} else {
			return $wpdb->query($wpdb->prepare("UPDATE " . Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_META_TABLE) . " SET meta_value = %s WHERE id = %d;", $meta_value, $meta_id));
		}
	}
	
	public function cancel_attendance ($user_id=false) {
		$user_id = (int)$this->_to_user_id($user_id);
		if (!$user_id) return false;
		if ($this->is_premium() && $this->user_paid()) return false; // Can't edit attendance for paid premium events
		
		global $wpdb;
		return $wpdb->query($wpdb->prepare("UPDATE " . Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE) . " SET status='no' WHERE event_id = %d AND user_id = %d LIMIT 1;", $this->get_id(), $user_id));
	}
	
	public function delete_attendance ($user_id=false) {
		$user_id = (int)$this->_to_user_id($user_id);
		if (!$user_id) return false;
		if ($this->is_premium() && $this->user_paid()) return false; // Can't edit attendance for paid premium events
		
		global $wpdb;
		return $wpdb->query($wpdb->prepare("DELETE FROM " . Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE) . " WHERE event_id = %d AND user_id = %d LIMIT 1;", $this->get_id(), $user_id));
	}
	
/* ----- Meta operations ----- */

	public function set_meta ($key, $value) {
		return update_post_meta($this->get_id(), $key, $value);
	}

	public function get_meta ($key) {
		return get_post_meta($this->get_id(), $key, true);
	}
	
	
	private function _to_user_id ($user_id) {
		$user_id = (int)$user_id;
		if (!$user_id) {
			global $current_user;
			$user_id = $current_user->id;
		}
		return (int)$user_id;
	}
}
