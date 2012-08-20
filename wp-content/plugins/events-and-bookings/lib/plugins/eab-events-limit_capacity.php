<?php
/*
Plugin Name: Limited capacity Events
Description: Allows you to limit the number of attendees for each of your events.
Plugin URI: http://premium.wpmudev.org/project/events-and-booking
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

class Eab_Addon_LimitCapacity {
	
	private function __construct () {}
	
	public static function serve () {
		$me = new Eab_Addon_LimitCapacity;
		$me->_add_hooks();
	}
	
	private function _add_hooks () {
		add_filter('eab-event_meta-event_meta_box-after', array($this, 'add_capacity_meta_box'));
		add_action('eab-event_meta-save_meta', array($this, 'save_capacity_meta'));
		add_action('eab-events-recurrent_event_child-save_meta', array($this, 'save_capacity_meta'));
		
		add_action('admin_print_scripts-post.php', array($this, 'enqueue_admin_dependencies'));
		add_action('admin_print_scripts-post-new.php', array($this, 'enqueue_admin_dependencies'));
		add_action('eab-javascript-enqueue_scripts', array($this, 'enqueue_public_dependencies'));
		
		add_filter('eab-rsvps-rsvp_form', array($this, 'handle_rsvp_form'));
		add_filter('eab-event-payment_forms', array($this, 'show_remaining_tickets'), 10, 2);
		add_filter('eab-payment-paypal_tickets-extra_attributes', array($this, 'handle_paypal_tickets'), 10, 3);
		
		// Front page editor integration
		add_filter('eab-events-fpe-add_meta', array($this, 'add_fpe_meta_box'), 10, 2);
		add_action('eab-events-fpe-enqueue_dependencies', array($this, 'enqueue_fpe_dependencies'), 10, 2);
		add_action('eab-events-fpe-save_meta', array($this, 'save_fpe_meta'), 10, 2);
	}
	
	private function _get_event_total_attendance ($event_id) {
		global $wpdb;
		$event_id = (int)$event_id;
		$meta = $wpdb->get_col("SELECT id FROM " . Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE) . " WHERE event_id={$event_id} AND status='" . Eab_EventModel::BOOKING_YES . "'");
		if (!$meta) return 0;
		
		$booked = join(',', $meta);
		$multiples_this_far = $wpdb->get_col("SELECT booking_ticket_count FROM " . Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_META_TABLE) . " where booking_id IN({$booked})");
		if (!$multiples_this_far) return count($booked);
		
		$this_far = count($booked) - count($multiples_this_far);
		foreach ($multiples_this_far as $count) $this_far += $count;
		
		return $this_far;
	}
	
	function handle_paypal_tickets ($atts, $event_id, $booking_id) {
		$capacity = (int)get_post_meta($event_id, 'eab_capacity', true);
		if (!$capacity) return $atts; // No capacity set, we're good to show
		
		$total = $this->_get_event_total_attendance($event_id);
		$max = $capacity - $total;
		return "{$atts} max='{$max}'";
		
	}
	
	function handle_rsvp_form ($content) {
		global $post;
		$post_id = (int)@$post->ID;
		
		$capacity = (int)get_post_meta($post_id, 'eab_capacity', true);
		if (!$capacity) return $content; // No capacity set, we're good to show
		
		$total = $this->_get_event_total_attendance($event_id);
		/*
		global $wpdb, $current_user;
		$users = $wpdb->get_col("SELECT user_id FROM " . Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE) . " WHERE event_id={$post_id} AND status='yes';");
		
		if ($capacity > count($users)) return $content;
		return (in_array($current_user->id, $users)) ? $content : $this->_get_overbooked_message();
		 */ 
		if ($capacity > $total) return $content;
		return (in_array($current_user->id, $users)) ? $content : $this->_get_overbooked_message();
	}
	
	function show_remaining_tickets ($content, $event_id) {
		$capacity = (int)get_post_meta($event_id, 'eab_capacity', true);
		if (!$capacity) return $content; // No capacity set
		
		$total = $this->_get_event_total_attendance($event_id);
		$max = $capacity - $total;
		return $max
			? '<div class="eab-max_capacity">' . sprintf(__('%s tickets left', Eab_EventsHub::TEXT_DOMAIN), $max) . '</div>' . $content
			: $content . '<div class="eab-max_capacity">' . __('No tickets left', Eab_EventsHub::TEXT_DOMAIN) . '</div>'
		;
	}
	
	function add_capacity_meta_box ($box) {
		global $post;
		
		$capacity = (int)get_post_meta($post->ID, 'eab_capacity', true);
		$capacity_str = $capacity ? $capacity : "";
		$unlimited_capacity = $capacity ? '' : 'checked="checked"';
		
		$ret = '';
		$ret .= '<div class="eab_meta_box">';
		$ret .= '<div class="misc-eab-section" >';
		$ret .= '<div class="eab_meta_column_box top"><label for="eab_event_capacity">' .
			__('Event capacity', Eab_EventsHub::TEXT_DOMAIN) . 
		'</label></div>';
		
		$ret .= '<label for="eab_event_capacity">' . __('Enter the maximum attendees for this event', Eab_EventsHub::TEXT_DOMAIN) . '</label>';
		$ret .= ' <input type="text" name="eab-elc_capacity" id="eab_event_capacity" size="3" value="' . $capacity_str . '" /> ';
		$ret .= '<label for="eab_event_capacity-unlimited">' . __('or check for unlimited', Eab_EventsHub) . '</label>';
		$ret .= ' <input type="checkbox" name="eab-elc_capacity" id="eab_event_capacity-unlimited" ' . $unlimited_capacity . ' value="0" /> ';
		
		$ret .= '</div>';
		$ret .= '</div>';

		return $box . $ret;
	}
	
	function add_fpe_meta_box ($box, $event) {
		$capacity = (int)get_post_meta($event->get_id(), 'eab_capacity', true);
		$capacity_str = $capacity ? $capacity : "";
		$unlimited_capacity = $capacity ? '' : 'checked="checked"';
		
		$ret .= '<div class="eab-events-fpe-meta_box">';
		
		$ret .= __('Enter the maximum attendees for this event', Eab_EventsHub::TEXT_DOMAIN);
		$ret .= ' <input type="text" name="eab-elc_capacity" id="eab_event_capacity" size="3" value="' . $capacity_str . '" /> ';
		$ret .= __('or check for unlimited', Eab_EventsHub);
		$ret .= ' <input type="checkbox" name="eab-elc_capacity" id="eab_event_capacity-unlimited" ' . $unlimited_capacity . ' value="0" /> ';
		
		$ret .= '</div>';
		
		return $box . $ret;
	}
	
	private function _save_meta ($post_id, $request) {
		if (!isset($request['eab-elc_capacity'])) return false;
		
		$capacity = (int)$request['eab-elc_capacity'];
		//if (!$capacity) return false;
		
		update_post_meta($post_id, 'eab_capacity', $capacity);
	}
	
	function save_capacity_meta ($post_id) {
		$this->_save_meta($post_id, $_POST);	
	}

	function save_fpe_meta ($post_id, $request) {
		$this->_save_meta($post_id, $request);	
	}
	
	
	function enqueue_fpe_dependencies () {
		wp_enqueue_script('eab-buddypress-limit_capacity-fpe', plugins_url(basename(EAB_PLUGIN_DIR) . "/js/eab-buddypress-limit_capacity-fpe.js"), array('jquery'));
	}

	function enqueue_admin_dependencies () {
		wp_enqueue_script('eab-buddypress-limit_capacity-admin', plugins_url(basename(EAB_PLUGIN_DIR) . "/js/eab-buddypress-limit_capacity-admin.js"), array('jquery'));
	}
	
	function enqueue_public_dependencies () {
		wp_enqueue_script('eab-buddypress-limit_capacity-public', plugins_url(basename(EAB_PLUGIN_DIR) . "/js/eab-buddypress-limit_capacity-public.js"), array('jquery'));		
	}
	
	private function _get_overbooked_message () {
		return '<div class="wpmudevevents-event_reached_capacity">' .
			apply_filters('eab-rsvps-event_capacity_reached-message', __('Sorry, the event sold out.', Eab_EventsHub::TEXT_DOMAIN)) .
		'</div>';
	}
}

Eab_Addon_LimitCapacity::serve();
