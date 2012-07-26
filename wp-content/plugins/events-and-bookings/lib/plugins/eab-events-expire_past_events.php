<?php
/*
Plugin Name: Immediately expire past events
Description: By default, your past events will be archived. Activating this add-on will immediately expire all your archived events.
Plugin URI: http://premium.wpmudev.org/project/events-and-booking
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

/*
Detail: Your <em>archived</em> events will be shown in archives, but visitors won't be able to RSVP. <br /> <em>Expired</em> events are removed from your archives.
*/

class Eab_Events_ExpirePastEvents {
	
	private function __construct () {}
	
	public static function serve () {
		$me = new Eab_Events_ExpirePastEvents;
		$me->_add_hooks();
	}
	
	private function _add_hooks () {
		add_action('admin_notices', array($this, 'show_nags'));
		add_action('eab_scheduled_jobs', array($this, 'expire_archived_events'), 99);
	}
	
	function show_nags () {
		if (!class_exists('Eab_Events_ExpireMonthOldEvents')) return false;
		if (defined('EAB_EXPIRY_CLASS_NAG_RENDERED')) return false;
		echo '<div class="error"><p>' .
			__("<b>Conflict warning:</b> You'll need to turn off one of the past events expiry add-ons.", Eab_EventsHub::TEXT_DOMAIN) .
		'</p></div>';
		define('EAB_EXPIRY_CLASS_NAG_RENDERED', true);
	}
	
	function expire_archived_events () {
		if (class_exists('Eab_Events_ExpireMonthOldEvents')) return false;
		$args = array();
		$collection = new Eab_ArchivedCollection($args);
		$events = $collection->to_collection();
		foreach ($events as $event) {
			$event->set_status(Eab_EventModel::STATUS_EXPIRED);
		}
	}
}

Eab_Events_ExpirePastEvents::serve();
