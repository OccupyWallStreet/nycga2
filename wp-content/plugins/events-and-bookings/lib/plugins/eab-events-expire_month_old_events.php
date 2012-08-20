<?php
/*
Plugin Name: Expire last month events
Description: By default, your past events will be archived. Activating this add-on will immediately expire your month-old archived events.
Plugin URI: http://premium.wpmudev.org/project/events-and-booking
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

/*
Detail: Your <em>archived</em> events will be shown in archives, but visitors won't be able to RSVP. <br /> <em>Expired</em> events are removed from your archives.
*/

class Eab_Events_ExpireMonthOldEvents {
	
	private function __construct () {}
	
	public static function serve () {
		$me = new Eab_Events_ExpireMonthOldEvents;
		$me->_add_hooks();
	}
	
	private function _add_hooks () {
		add_action('admin_notices', array($this, 'show_nags'));
		add_action('eab_scheduled_jobs', array($this, 'expire_archived_events'), 99);
	}
	
	function show_nags () {
		if (!class_exists('Eab_Events_ExpirePastEvents')) return false;
		if (defined('EAB_EXPIRY_CLASS_NAG_RENDERED')) return false;
		echo '<div class="error"><p>' .
			__("<b>Conflict warning:</b> You'll need to turn off one of the past events expiry add-ons.", Eab_EventsHub::TEXT_DOMAIN) .
		'</p></div>';
		define('EAB_EXPIRY_CLASS_NAG_RENDERED', true);
	}
	
	function expire_archived_events () {
		if (class_exists('Eab_Events_ExpirePastEvents')) return false;
		$args = array();
		$collection = new Eab_LastMonthArchivedCollection(time(), $args);
		$events = $collection->to_collection();
		foreach ($events as $event) {
			$event->set_status(Eab_EventModel::STATUS_EXPIRED);
		}
	}
}



/**
 * Month-old archived events
 */
class Eab_LastMonthArchivedCollection extends Eab_TimedCollection {
	
	public function build_query_args ($args) {
		$time = $this->get_timestamp();
		
		$args = array_merge(
			$args,
			array(
			 	'post_type' => 'incsub_event',
				'suppress_filters' => false, 
				'posts_per_page' => -1,
				'meta_query' => array(
					array(
		    			'key' => 'incsub_event_status',
		    			'value' => Eab_EventModel::STATUS_ARCHIVED,
					),
					array(
		    			'key' => 'incsub_event_end',
		    			'value' => date("Y-m-01 00:00:01", $time),
		    			'compare' => '<',
		    			'type' => 'DATETIME'
					),
				)
			)
		);
		return $args;
	}
}

Eab_Events_ExpireMonthOldEvents::serve();
