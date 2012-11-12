<?php
/*
Plugin Name: BuddyPress: My Events
Description: Adds an Events tab to your user profiles.
Plugin URI: http://premium.wpmudev.org/project/events-and-booking
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

/*
Detail: Displays lists of user RSVPs on your users member pages.
*/ 

class Eab_BuddyPress_MyEvents {
	
	private function __construct () {}
	
	public static function serve () {
		$me = new Eab_BuddyPress_MyEvents;
		$me->_add_hooks();
	}
	
	private function _add_hooks () {
		add_action('admin_notices', array($this, 'show_nags'));
		
		add_action('bp_init', array($this, 'add_bp_profile_entry'));
	}
	
	function show_nags () {
		if (!defined('BP_VERSION')) {
			echo '<div class="error"><p>' .
				__("You'll need BuddyPress installed and activated for My Events add-on to work", Eab_EventsHub::TEXT_DOMAIN) .
			'</p></div>';
		}
	}
	
	function add_bp_profile_entry () {
		global $bp;
		/*
		bp_core_new_subnav_item(array(
			'name' => __('My Events', Eab_EventsHub::TEXT_DOMAIN),
			'slug' => 'my-events',
			'parent_url' => $bp->loggedin_user->domain . $bp->profile->slug . '/',
			'parent_slug' => $bp->profile->slug,
			'screen_function' => array($this, 'bind_bp_profile_page'),
			'position' => 40 
		));
		 */
		bp_core_new_nav_item(array(
			'name' => __('Events', Eab_EventsHub::TEXT_DOMAIN),
			'slug' => 'my-events',
			'show_for_displayed_user' => true,
			'default_subnav_slug' => 'organized',
			'screen_function' => '__return_false',//array($this, 'bind_bp_profile_page'),
		));
		bp_core_new_subnav_item(array(
			'name' => __('Organized', Eab_EventsHub::TEXT_DOMAIN),
			'slug' => 'organized',
			'parent_url' => $bp->displayed_user->domain . 'my-events' . '/',
			'parent_slug' => 'my-events',
			'screen_function' => array($this, 'bind_bp_organized_page'),
		));
		bp_core_new_subnav_item(array(
			'name' => __('Attending', Eab_EventsHub::TEXT_DOMAIN),
			'slug' => 'attending',
			'parent_url' => $bp->displayed_user->domain . 'my-events' . '/',
			'parent_slug' => 'my-events',
			'screen_function' => array($this, 'bind_bp_attending_page'),
		));
		bp_core_new_subnav_item(array(
			'name' => __('Maybe', Eab_EventsHub::TEXT_DOMAIN),
			'slug' => 'mabe',
			'parent_url' => $bp->displayed_user->domain . 'my-events' . '/',
			'parent_slug' => 'my-events',
			'screen_function' => array($this, 'bind_bp_maybe_page'),
		));
		bp_core_new_subnav_item(array(
			'name' => __('Not Attending', Eab_EventsHub::TEXT_DOMAIN),
			'slug' => 'not-attending',
			'parent_url' => $bp->displayed_user->domain . 'my-events' . '/',
			'parent_slug' => 'my-events',
			'screen_function' => array($this, 'bind_bp_not_attending_page'),
		));
		do_action('eab-events-my_events-set_up_navigation');
	}
	
	function bind_bp_organized_page () {
		add_action('bp_template_title', array($this, 'show_organized_title'));
		add_action('bp_template_content', array($this, 'show_organized_body'));
		add_action('bp_head', array($this, 'enqueue_dependencies'));
		bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
	}
	function bind_bp_attending_page () {
		add_action('bp_template_title', array($this, 'show_attending_title'));
		add_action('bp_template_content', array($this, 'show_attending_body'));
		add_action('bp_head', array($this, 'enqueue_dependencies'));
		bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
	}
	function bind_bp_maybe_page () {
		add_action('bp_template_title', array($this, 'show_maybe_title'));
		add_action('bp_template_content', array($this, 'show_maybe_body'));
		add_action('bp_head', array($this, 'enqueue_dependencies'));
		bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
	}
	function bind_bp_not_attending_page () {
		add_action('bp_template_title', array($this, 'show_not_attending_title'));
		add_action('bp_template_content', array($this, 'show_not_attending_body'));
		add_action('bp_head', array($this, 'enqueue_dependencies'));
		bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
	}
	
	function enqueue_dependencies () {
		global $bp;
		if ('my-events' != $bp->current_component) return false;
		wp_enqueue_style('eab-bp-my_events', plugins_url('events-and-bookings/css/eab-buddypress-my_events.css'));
	}
	
	function show_organized_title () {
		echo __('Organized Events', Eab_EventsHub::TEXT_DOMAIN);
	}
	function show_attending_title () {
		echo __('Attending Events', Eab_EventsHub::TEXT_DOMAIN);
	}
	function show_maybe_title () {
		echo __('Maybe attending Events', Eab_EventsHub::TEXT_DOMAIN);
	}
	function show_not_attending_title () {
		echo __('Not attending Events', Eab_EventsHub::TEXT_DOMAIN);
	}

	function show_organized_body () {
		global $bp;
		echo '<div id="eab-bp-my_events-wrapper">';
		echo '<div class="eab-bp-my_events eab-bp-organized">' . 
			Eab_Template::get_user_organized_events($bp->displayed_user->id) .
		'</div>';
		echo '</div>';
	}
	function show_attending_body () {
		global $bp;
		echo '<div id="eab-bp-my_events-wrapper">';
		echo '<div class="eab-bp-my_events eab-bp-rsvp_yes">' . 
			Eab_Template::get_user_events(Eab_EventModel::BOOKING_YES, $bp->displayed_user->id) .
		'</div>';
		echo '</div>';
	}
	function show_maybe_body () {
		global $bp;
		echo '<div id="eab-bp-my_events-wrapper">';
		echo '<div class="eab-bp-my_events eab-bp-rsvp_maybe">' . 
			Eab_Template::get_user_events(Eab_EventModel::BOOKING_MAYBE, $bp->displayed_user->id) .
		'</div>';
		echo '</div>';
	}
	function show_not_attending_body () {
		global $bp;
		echo '<div id="eab-bp-my_events-wrapper">';
		echo '<div class="eab-bp-my_events eab-bp-rsvp_no">' . 
			Eab_Template::get_user_events(Eab_EventModel::BOOKING_NO, $bp->displayed_user->id) .
		'</div>';
		echo '</div>';
	}
}

Eab_BuddyPress_MyEvents::serve();
