<?php

class Eab_Shortcodes {
	
	private $_shortcodes = array (
		'calendar' => 'eab_calendar',
		'archive' => 'eab_archive',
		'single' => 'eab_single',
	);
	
	public static function serve () {
		$me = new Eab_Shortcodes;
		$me->_register();
	}
	
	/**
	 * Calendar shortcode handler.
	 */
	function process_calendar_shortcode ($args, $content=false) {
		$args = wp_parse_args($args, array(
			'date' => false,
			'footer' => false,
			'class' => false,
			'network' => false,
		));
		
		$args['footer'] = $this->_str_to_bool($args['footer']);
		$args['network'] = $this->_str_to_bool($args['network']);
		$time = $args['date'] ? strtotime($args['date']) : time();
		
		$events = ($args['network'] && is_multisite()) ? Eab_Network::get_upcoming_events(30) : Eab_CollectionFactory::get_upcoming_events($time);
		
		if (!class_exists('Eab_CalendarTable_EventShortcodeCalendar')) require_once EAB_PLUGIN_DIR . 'lib/class_eab_calendar_helper.php';
		$renderer = new Eab_CalendarTable_EventShortcodeCalendar($events);
		$renderer->set_class($args['class']);
		$renderer->set_footer($args['footer']);
		return $renderer->get_month_calendar($time);
	}
	
	/**
	 * Archive shortcode handler.
	 */
	function process_archive_shortcode ($args, $content=false) {
		$args = wp_parse_args($args, array(
			'date' => false,
			'class' => false,
			'network' => false,
		));
		$args['network'] = $this->_str_to_bool($args['network']);
		
		$time = $args['date'] ? strtotime($args['date']) : time();
		$events = ($args['network'] && is_multisite()) ? Eab_Network::get_upcoming_events(30) : Eab_CollectionFactory::get_upcoming_events($time);

		$ret = '';
		foreach ($events as $event) {
			$ret .= '<h4>' . $event->get_title() . '</h4>' . Eab_Template::get_archive_content($event);
		}
		wp_enqueue_style('eab_front');
		wp_enqueue_script('eab_event_js');
		return $ret;
	}
	
	/**
	 * Single event shortcode handler.
	 */
	function process_single_shortcode ($args, $content=false) {
		$args = wp_parse_args($args, array(
			'id' => false,
			'date' => false,
			'class' => false,
		));
		
		$time = $args['date'] ? strtotime($args['date']) : time();
		$event = get_post($args['id']);
	
		$ret = "<h4>{$event->post_title}</h4>" . Eab_Template::get_single_content($event);
		wp_enqueue_style('eab_front');
		wp_enqueue_script('eab_event_js');
		return $ret;
	}
	
	private function _str_to_bool ($val) {
		$_trues = array('yes', 'true', '1');
		return in_array($val, $_trues);
	}
	
	/**
	 * Registers shortcode handlers.
	 */
	private function _register () {
		$shortcodes = $this->_shortcodes;
		foreach ($shortcodes as $key => $shortcode) {
			add_shortcode($shortcode, array($this, "process_{$key}_shortcode"));
		}
	}
}
