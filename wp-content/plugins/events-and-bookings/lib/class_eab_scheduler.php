<?php

/**
 * Performs scheduled operations against events.
 */
class Eab_Scheduler {
	
	private function __construct () {
		
	}
	
	public static function serve () {
		$me = new Eab_Scheduler;
		$me->_add_hooks();
	}
	
	private function _add_hooks () {
		add_action('eab_scheduled_jobs', array($this, 'archive_old_events'));
		
		if (!wp_next_scheduled('eab_scheduled_jobs')) {
			wp_schedule_event(time(), 'hourly', 'eab_scheduled_jobs');
		}
	}
	
	/**
	 * Sets status of old events to STATUS_ARCHIVED
	 */
	function archive_old_events () {
		$time = time();
		$events = Eab_CollectionFactory::get_old_events($time);
		foreach ($events as $event) {
			if ($event->get_last_end_timestamp() < $time) $event->set_status(Eab_EventModel::STATUS_ARCHIVED);
		}
	}
}
