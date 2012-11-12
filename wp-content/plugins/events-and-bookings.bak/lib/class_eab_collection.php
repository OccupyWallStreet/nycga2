<?php

/**
 * Abstract collection root class.
 */
abstract class WpmuDev_Collection {
	
	/**
	 * Holds a WP_Query instance.
	 */
	private $_query;
	
	/**
	 * Constructs WP_Query object with overriden arugment set.
	 * NEVER NEVER EVER call this directly. Use Factory instead.
	 * @param array WP_Query arguments.
	 */
	public function __construct ($args) {
		$query = $this->build_query_args($args);
		$this->_query = new WP_Query($query);
//echo '<pre>';die(var_Export($this->_query));
	}
	
	/**
	 * Returns a WP_Query instance.
	 */
	public function to_query () {
		return apply_filters('wpmudev-query', $this->_query);
	}
	
	abstract public function build_query_args ($args);
	abstract public function to_collection ();
}

/**
 * Abstract Event collection root class.
 */
abstract class Eab_Collection extends WpmuDev_Collection {
	
	/**
	 * Converts WP_Query result set into an array of Eab_EventModel objects.
	 * @return array
	 */
	public function to_collection () {
		$events = array();
		$query = $this->to_query();
		if (!$query->posts) return $events;
		foreach ($query->posts as $post) {
			$events[] = new Eab_EventModel($post);
		}
		return apply_filters('eab-collection', $events);
	}

}


/**
 * General purpose time-restricted collection.
 */
abstract class Eab_TimedCollection extends Eab_Collection {
	
	protected $_timestamp;
	
	/**
	 * NEVER NEVER EVER call this directly. Use Factory instead.
	 */
	public function __construct ($timestamp=false, $args=array()) {
		$this->_timestamp = $timestamp ? $timestamp : time();
		$query = $this->build_query_args($args);
		parent::__construct($query);
	}
	
	public function get_timestamp () {
		return $this->_timestamp;
	}
	
}


/**
 * Upcoming events time-restricted collection implementation.
 */
class Eab_UpcomingCollection extends Eab_TimedCollection {

	public function __construct ($timestamp=false, $args=array()) {
		add_filter('posts_where', array($this, 'posts_where'));
		add_filter('posts_join', array($this, 'join_postmeta'));
		add_filter('posts_orderby', array($this, 'order_by_date'));
		parent::__construct($timestamp, $args);
		remove_filter('posts_where', array($this, 'posts_where'));
		remove_filter('posts_join', array($this, 'join_postmeta'));
		remove_filter('posts_orderby', array($this, 'order_by_date'));
	}

	public function order_by_date ($q) {
		global $wpdb;
		return "eab_meta.meta_value ASC"; // @TODO: SET UP EVENT ORDERING DIRECTION!!
	}

	public function join_postmeta ($q) {
		global $wpdb;
		return "{$q} JOIN {$wpdb->postmeta} AS eab_meta ON ({$wpdb->posts}.ID = eab_meta.post_id)";
	}

	public function posts_where ($q) {
		return "{$q} AND eab_meta.meta_key='incsub_event_start'";
	}
	
	public function build_query_args ($args) {
		$time = $this->get_timestamp();
		$year = (int)date('Y', $time);
		$month = date('m', $time);
		$time = strtotime("{$year}-{$month}-01");
		
		$forbidden_statuses = array(Eab_EventModel::STATUS_CLOSED);
		if (!isset($args['incsub_event'])) { // If not single
			$forbidden_statuses[] = Eab_EventModel::STATUS_EXPIRED;
		}
		
		$start_month = $month ? sprintf("%02d", $month) : date('m');
		if ($start_month < 12) {
			$end_month = sprintf("%02d", (int)$month+1);
			$end_year = $year;
		} else {
			$end_month = '01';
			$end_year = $year+1;
		}
		
		if (!isset($args['posts_per_page'])) $args['posts_per_page'] = -1;		
		
		$args = array_merge(
			$args,
			array(
			 	'post_type' => 'incsub_event', 
			 	'post_status' => array('publish', Eab_EventModel::RECURRENCE_STATUS),
				'suppress_filters' => false, 
				'meta_query' => array(
					array(
		    			'key' => 'incsub_event_start',
		    			'value' => apply_filters('eab-collection-upcoming-end_timestamp', "{$end_year}-{$end_month}-01 00:00"),
		    			'compare' => '<',
		    			'type' => 'DATETIME'
					),
					array(
		    			'key' => 'incsub_event_end',
		    			'value' => apply_filters('eab-collection-upcoming-start_timestamp', "{$year}-{$start_month}-01 00:00"),
		    			'compare' => '>=',
		    			'type' => 'DATETIME'
					),
					array(
						'key' => 'incsub_event_status',
						'value' => $forbidden_statuses,
						'compare' => 'NOT IN',
					),
				)
			)
		);
		return $args;
	}
}


/**
 * Upcoming events time-restricted collection (5 weeks period) implementation.
 * @author: Hakan Evin
 */
class Eab_UpcomingWeeksCollection extends Eab_TimedCollection {

	public function __construct ($timestamp=false, $args=array()) {
		add_filter('posts_where', array($this, 'posts_where'));
		add_filter('posts_join', array($this, 'join_postmeta'));
		add_filter('posts_orderby', array($this, 'order_by_date'));
		parent::__construct($timestamp, $args);
		remove_filter('posts_where', array($this, 'posts_where'));
		remove_filter('posts_join', array($this, 'join_postmeta'));
		remove_filter('posts_orderby', array($this, 'order_by_date'));
	}

	public function order_by_date ($q) {
		global $wpdb;
		return "eab_meta.meta_value ASC"; // @TODO: SET UP EVENT ORDERING DIRECTION!!
	}

	public function join_postmeta ($q) {
		global $wpdb;
		return "{$q} JOIN {$wpdb->postmeta} AS eab_meta ON ({$wpdb->posts}.ID = eab_meta.post_id)";
	}

	public function posts_where ($q) {
		return "{$q} AND eab_meta.meta_key='incsub_event_start'";
	}
	
	public function build_query_args ($args) {
		// Changes by Hakan
		// Commented lines were not removed intentionally.
		$time = $this->get_timestamp();
		
		$forbidden_statuses = array(Eab_EventModel::STATUS_CLOSED);
		if (!isset($args['incsub_event'])) { // If not single
			$forbidden_statuses[] = Eab_EventModel::STATUS_EXPIRED;
		}
		
		if (!isset($args['posts_per_page'])) $args['posts_per_page'] = -1;		
		
		$args = array_merge(
			$args,
			array(
			 	'post_type' => 'incsub_event', 
			 	'post_status' => array('publish', Eab_EventModel::RECURRENCE_STATUS),
				'suppress_filters' => false, 
				'meta_query' => array(
					array(
		    			'key' => 'incsub_event_start',
						'value' => date( "Y-m-d H:i", $time + 5 * 7 * 86400 ), // Events whose starting dates are 5 weeks from now
		    			'compare' => '<',
		    			'type' => 'DATETIME'
					),
					array(
		    			'key' => 'incsub_event_end',
						'value' => date( "Y-m-d H:i", $time ), // Events those already started now
		    			'compare' => '>=',
		    			'type' => 'DATETIME'
					),
					array(
						'key' => 'incsub_event_status',
						'value' => $forbidden_statuses,
						'compare' => 'NOT IN',
					),
				)
			)
		);
		return $args;
	}
}


/**
 * Popular (most RSVPd) events collection implementation.
 */
class Eab_PopularCollection extends Eab_Collection {
	
	public function build_query_args ($args) {
		global $wpdb;
		$result = $wpdb->get_col("SELECT event_id, COUNT(event_id) as cnt FROM " . Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE) . " WHERE status IN ('yes', 'maybe') GROUP BY event_id ORDER BY cnt DESC");
		$args = array_merge(
			$args,
			array(
				'post__in' => array_values($result),
				'post_type' => 'incsub_event',
				'post_status' => array('publish', Eab_EventModel::RECURRENCE_STATUS),
				'posts_per_page' => -1,
			)
		);
		return $args;
	}	
}


/**
 * Events organized by the user
 */
class Eab_OrganizerCollection extends Eab_Collection {
	
	public function build_query_args ($arg) {
		$arg = (int)$arg;
		$args = array(
			'author' => $arg,
			'post_type' => 'incsub_event',
			'post_status' => array('publish', Eab_EventModel::RECURRENCE_STATUS),
			'posts_per_page' => -1,
		);
		return $args;
	}	
}

/**
 * Old events time-restricted collection implementation.
 * Old events are events with last end time in the past,
 * but not yet expired.
 */
class Eab_OldCollection extends Eab_TimedCollection {
	
	public function build_query_args ($args) {
		
		$args = array_merge(
			$args,
			array(
			 	'post_type' => 'incsub_event',
				'suppress_filters' => false, 
				'posts_per_page' => -1,
				'meta_query' => array(
					array(
		    			'key' => 'incsub_event_status',
		    			'value' => Eab_EventModel::STATUS_OPEN,
					),
					array(
		    			'key' => 'incsub_event_end',
		    			'value' => date("Y-m-d H:i:s", $this->get_timestamp()),
		    			'compare' => '<',
		    			'type' => 'DATETIME'
					),
				)
			)
		);
		return $args;
	}
}

/**
 * All archived events
 */
class Eab_ArchivedCollection extends Eab_Collection {
	
	public function build_query_args ($args) {
		
		$args = array_merge(
			$args,
			array(
			 	'post_type' => 'incsub_event',
				'posts_per_page' => -1,
				'meta_query' => array(
					array(
		    			'key' => 'incsub_event_status',
		    			'value' => Eab_EventModel::STATUS_ARCHIVED,
					),
				)
			)
		);
		return $args;
	}
}

class Eab_AllRecurringChildrenCollection extends Eab_Collection {
	
	public function build_query_args ($arg) {
		if (!$arg instanceof WpmuDev_DatedVenuePremiumModel) return $arg;
		$status = $arg->is_trashed() 
			? WpmuDev_RecurringDatedItem::RECURRENCE_TRASH_STATUS
			: WpmuDev_RecurringDatedItem::RECURRENCE_STATUS
		;
		$args = array (
			'post_type' => 'incsub_event',
			'post_status' => $status,
			'post_parent' => $arg->get_id(),
			'posts_per_page' => -1,
		);
		return $args;
	}
}


/**
 * Factory class for spawning collections.
 * Pure static class.
 */
class Eab_CollectionFactory {
	
	private function __construct () {}
	
	/**
	 * Upcoming events query factory method
	 * @return object Eab_UpcomingCollection instance
	 */
	public static function get_upcoming ($timestamp=false, $args=array()) {
		$me = new Eab_UpcomingCollection($timestamp, $args);
		return $me->to_query();
	}
	
	/**
	 * Upcoming events factory method
	 * @return array Upcoming events list
	 */
	public static function get_upcoming_events ($timestamp=false, $args=array()) {
		$me = new Eab_UpcomingCollection($timestamp, $args);
		return $me->to_collection();
	}

	/**
	 * Upcoming events weeks factory method
	 * @return array Upcoming events list
	 */
	public static function get_upcoming_weeks_events ($timestamp=false, $args=array()) {
		$me = new Eab_UpcomingWeeksCollection($timestamp, $args);
		return $me->to_collection();
	}
	
	/**
	 * Old events query factory method.
	 * @return object Eab_OldCollection instance
	 */
	public static function get_old ($timestamp=false, $args=array()) {
		$me = new Eab_OldCollection($timestamp, $args);
		return $me->to_query();
	}
	
	/**
	 * Old events factory method
	 * @return array Old events list
	 */
	public static function get_old_events ($timestamp=false, $args=array()) {
		$me = new Eab_OldCollection($timestamp, $args);
		return $me->to_collection();
	}

	/**
	 * Popular events query factory method.
	 * @return object Eab_PopularCollection instance
	 */
	public static function get_popular ($args=array()) {
		$me = new Eab_PopularCollection($args);
		return $me->to_query();
	}
	
	/**
	 * Popular events factory method
	 * @return array Popular events list
	 */
	public static function get_popular_events ($args=array()) {
		$me = new Eab_PopularCollection($args);
		return $me->to_collection();
	}
	
	public static function get_all_recurring_children_events ($event) {
		$me = new Eab_AllRecurringChildrenCollection($event);
		return $me->to_collection();
	} 
	
	public static function get_user_organized_events ($user_id) {
		$me = new Eab_OrganizerCollection($user_id);
		return $me->to_collection();
	} 
}
