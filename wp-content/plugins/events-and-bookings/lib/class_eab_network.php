<?php

/**
 * Handles network integration,
 * through Post Indexer.
 */
class Eab_Network {
	
	private function __construct () {}
	
	/**
	 * Available only on multisite.
	 */
	public static function serve () {
		if (!is_multisite()) return false;
		$me = new Eab_Network;
		$me->_add_hooks();
	}
	
	private function _add_hooks () {
		add_action('plugins_loaded', array($this, 'load_dependencies'));
	}
	
	/**
	 * Check if PI is available, and proceed if it is.
	 */
	function load_dependencies () {
		if (!function_exists('post_indexer_make_current')) return false;
		add_action('widgets_init', array($this, 'load_widgets'), 20);
	}
	
	/**
	 * We have all we need, let's register widgets.
	 */
	function load_widgets () {
		require_once EAB_PLUGIN_DIR . 'lib/widgets/NetworkUpcoming_Widget.class.php';
		register_widget('Eab_NetworkUpcoming_Widget');
	}
	
/* ----- Model procedures ----- */

	/**
	 * Gets a list of upcoming events.
	 * Only the events that are not yet over will be returned.
	 */
	public static function get_upcoming_events ($limit=5) {
		if (!function_exists('post_indexer_make_current')) return false;
		$limit = (int)$limit ? (int)$limit : 5;
		
		global $wpdb;
		$result = array();
		$count = 0;
		$raw_network_events = $wpdb->get_results("SELECT * FROM {$wpdb->base_prefix}site_posts WHERE post_type='incsub_event' ORDER BY post_published_stamp DESC");
		if (!$raw_network_events) return $result;
		
		foreach ($raw_network_events as $event) {
			if ($count == $limit) break;
			switch_to_blog($event->blog_id);
			$post = get_post($event->post_id);
			$event = new Eab_EventModel($post);
			if ($event->is_expired()) continue;
			$post->blog_id = $event->blog_id;
			$result[] = $post;
			$count++;
			restore_current_blog();
		}
		return $result;
	}
}
