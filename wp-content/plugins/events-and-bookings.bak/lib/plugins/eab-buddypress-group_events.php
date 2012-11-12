<?php
/*
Plugin Name: BuddyPress: Group Events
Description: Allows you to connect your Events with your BuddyPress groups.
Plugin URI: http://premium.wpmudev.org/project/events-and-booking
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

/*
Detail: Allows deeper integration of your Events with BuddyPress groups. <br /> <b>Requires BuddyPress Groups component</b>
*/ 

class Eab_BuddyPress_GroupEvents {
	
	const SLUG = 'group-events';
	private $_data;
	
	private function __construct () {
		$this->_data = Eab_Options::get_instance();
	}
	
	public static function serve () {
		$me = new Eab_BuddyPress_GroupEvents;
		$me->_add_hooks();
	}
	
	private function _add_hooks () {
		add_action('admin_notices', array($this, 'show_nags'));
		add_action('eab-settings-after_plugin_settings', array($this, 'show_settings'));
		add_filter('eab-settings-before_save', array($this, 'save_settings'));
		
		if ($this->_data->get_option('bp-group_event-auto_join_groups')) {
			add_action('incsub_event_booking_yes', array($this, 'auto_join_group'), 10, 2);
			add_action('incsub_event_booking_maybe', array($this, 'auto_join_group'), 10, 2);
		}
		if ($this->_data->get_option('bp-group_event-private_events')) {
			add_filter('wpmudev-query', array($this, 'filter_query'));
		}
		
		add_action('bp_init', array($this, 'add_tab'));
		add_filter('eab-event_meta-event_meta_box-after', array($this, 'add_meta_box'));
		add_action('eab-event_meta-save_meta', array($this, 'save_meta'));
		add_action('eab-events-recurrent_event_child-save_meta', array($this, 'save_meta'));
		
		// Front page editor integration
		add_filter('eab-events-fpe-add_meta', array($this, 'add_fpe_meta_box'), 10, 2);
		add_action('eab-events-fpe-enqueue_dependencies', array($this, 'enqueue_fpe_dependencies'), 10, 2);
		add_action('eab-events-fpe-save_meta', array($this, 'save_fpe_meta'), 10, 2);
	}

	function filter_query ($query) {
		global $current_user;
		if (!($query instanceof WP_Query)) return $query;
		if (Eab_EventModel::POST_TYPE != @$query->query_vars['post_type']) return $query;
		if (!function_exists('groups_is_user_member')) return $query;
		
		$posts = array();
		foreach ($query->posts as $post) {
			$group = (int)get_post_meta($post->ID, 'eab_event-bp-group_event', true);
			if ($group) {
				if (!groups_is_user_member($current_user->id, $group)) continue; 
			}
			$posts[] = $post;
		}
		$query->posts = $posts;
		$query->post_count = count($posts);
		return $query;
	}
	
	function auto_join_group ($event_id, $user_id) {
		if (!function_exists('groups_get_groups')) return false;
		if (!$this->_data->get_option('bp-group_event-auto_join_groups')) return false;
		$group_id = (int)get_post_meta($event_id, 'eab_event-bp-group_event', true);
		if (!$group_id) return false;

		groups_accept_invite($user_id, $group_id);
	}
	
	function show_nags () {
		if (!defined('BP_VERSION')) {
			echo '<div class="error"><p>' .
				__("You'll need BuddyPress installed and activated for Groups Events add-on to work", Eab_EventsHub::TEXT_DOMAIN) .
			'</p></div>';
		}
		if (!function_exists('groups_get_groups')) {
			echo '<div class="error"><p>' .
				__("You'll need to enable BuddyPress Groups component for Groups Events add-on to work", Eab_EventsHub::TEXT_DOMAIN) .
			'</p></div>';
		}
	}
	
	function show_settings () {
		$tips = new WpmuDev_HelpTooltips();
		$tips->set_icon_url(plugins_url('events-and-bookings/img/information.png'));
		$checked = $this->_data->get_option('bp-group_event-auto_join_groups') ? 'checked="checked"' : '';
		$private = $this->_data->get_option('bp-group_event-private_events') ? 'checked="checked"' : '';
?>
<div id="eab-settings-group_events" class="eab-metabox postbox">
	<h3 class="eab-hndle"><?php _e('Group Events settings :', Eab_EventsHub::TEXT_DOMAIN); ?></h3>
	<div class="eab-inside">
		<div class="eab-settings-settings_item">
	    	<label for="eab_event-bp-group_event-auto_join_groups"><?php _e('Automatically join the group by RSVPing to events', Eab_EventsHub::TEXT_DOMAIN); ?>?</label>
			<input type="checkbox" id="eab_event-bp-group_event-auto_join_groups" name="event_default[bp-group_event-auto_join_groups]" value="1" <?php print $checked; ?> />
			<span><?php echo $tips->add_tip(__('When your users RSVP positively to your group event, they will also automatically join the group the event belongs to.', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
	    </div>
		<div class="eab-settings-settings_item">
	    	<label for="eab_event-bp-group_event-private_events"><?php _e('Group events are private to groups', Eab_EventsHub::TEXT_DOMAIN); ?>?</label>
			<input type="checkbox" id="eab_event-bp-group_event-private_events" name="event_default[bp-group_event-private_events]" value="1" <?php print $private; ?> />
			<span><?php echo $tips->add_tip(__('If you enable this option, users outside your groups will <b>not</b> be able to see your Group Events.', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
	    </div>
	</div>
</div>
<?php
	}

	function save_settings ($options) {
		$options['bp-group_event-auto_join_groups'] = $_POST['event_default']['bp-group_event-auto_join_groups'];
		$options['bp-group_event-private_events'] = $_POST['event_default']['bp-group_event-private_events'];
		return $options;
	}

	function add_meta_box ($box) {
		global $post;
		if (!function_exists('groups_get_groups')) return $box;
		$group_id = get_post_meta($post->ID, 'eab_event-bp-group_event', true);
		
		$groups = groups_get_groups();
		$groups = @$groups['groups'] ? $groups['groups'] : array();
		
		$ret = '';
		$ret .= '<div class="eab_meta_box">';
		$ret .= '<div class="misc-eab-section" >';
		$ret .= '<div class="eab_meta_column_box top"><label for="eab_event-bp-group_event">' .
			__('Group event', Eab_EventsHub::TEXT_DOMAIN) . 
		'</label></div>';
		
		$ret .= __('This is a group event for', Eab_EventsHub::TEXT_DOMAIN);
		$ret .= ' <select name="eab_event-bp-group_event" id="eab_event-bp-group_event">';
		$ret .= '<option value="">' . __('Not a group event', Eab_EventsHub::TEXT_DOMAIN) . '&nbsp;</option>';
		foreach ($groups as $group) {
			$selected = ($group->id == $group_id) ? 'selected="selected"' : '';
			$ret .= "<option value='{$group->id}' {$selected}>{$group->name}</option>";
		}
		$ret .= '</select> ';
		
		$ret .= '</div>';
		$ret .= '</div>';
		return $box . $ret;
	}

	function add_fpe_meta_box ($box, $event) {
		if (!function_exists('groups_get_groups')) return $box;
		$group_id = get_post_meta($event->get_id(), 'eab_event-bp-group_event', true);
		
		$groups = groups_get_groups();
		$groups = @$groups['groups'] ? $groups['groups'] : array();
		
		$ret .= '<div class="eab-events-fpe-meta_box">';
		$ret .= __('This is a group event for', Eab_EventsHub::TEXT_DOMAIN);
		$ret .= ' <select name="eab_event-bp-group_event" id="eab_event-bp-group_event">';
		$ret .= '<option value="">' . __('Not a group event', Eab_EventsHub::TEXT_DOMAIN) . '&nbsp;</option>';
		foreach ($groups as $group) {
			$selected = ($group->id == $group_id) ? 'selected="selected"' : '';
			$ret .= "<option value='{$group->id}' {$selected}>{$group->name}</option>";
		}
		$ret .= '</select> ';
		$ret .= '</div>';
		
		return $box . $ret;
	}
	
	private function _save_meta ($post_id, $request) {
		if (!function_exists('groups_get_groups')) return false;
		if (!isset($request['eab_event-bp-group_event'])) return false;
		
		$data = (int)$request['eab_event-bp-group_event'];
		//if (!$data) return false;
		
		update_post_meta($post_id, 'eab_event-bp-group_event', $data);
	}
	
	function save_meta ($post_id) {
		$this->_save_meta($post_id, $_POST);
	}

	function save_fpe_meta ($post_id, $request) {
		$this->_save_meta($post_id, $request);
	}
	
	function add_tab () {
		global $bp, $current_user;
		if (!function_exists('groups_get_groups')) return false;
		if (!$bp->is_single_item) return false;

		// Don't show groups tab for non-members if Events are private to groups
		if ($this->_data->get_option('bp-group_event-private_events')) {
			if (!groups_is_user_member($current_user->id, $bp->groups->current_group->id)) return false; 
		}
		
		$name = __('Group Events', Eab_EventsHub::TEXT_DOMAIN);
		$groups_link = bp_get_group_permalink($bp->groups->current_group);//$bp->root_domain . '/' . $bp->groups->slug . '/' . $bp->groups->current_group->slug . '/';
		
		bp_core_new_subnav_item(array(
			'name' => $name,
			'slug' => self::SLUG,
			'parent_url' => $groups_link,
			'parent_slug' => $bp->groups->current_group->slug,
			'screen_function' => array($this, 'bind_bp_groups_page'),
		));
	}
	
	function bind_bp_groups_page () {
		add_action('bp_template_content', array($this, 'show_group_events_profile_body'));
		add_action('bp_head', array($this, 'enqueue_dependencies'));
		bp_core_load_template(apply_filters('bp_core_template_plugin', 'groups/single/plugins'));
	}
	
	function enqueue_dependencies () {
		// @TODO: refactor to separate style.
		wp_enqueue_style('eab-bp-group_events', plugins_url(basename(EAB_PLUGIN_DIR) . "/default-templates/calendar/events.css"));
	}
	
	function enqueue_fpe_dependencies () {
		wp_enqueue_script('eab-buddypress-group_events-fpe', plugins_url(basename(EAB_PLUGIN_DIR) . "/js/eab-buddypress-group_events-fpe.js"), array('jquery'));
	}

	function show_group_events_profile_body () {
		global $bp;
		$timestamp = $this->_get_requested_timestamp();
		
		$collection = new Eab_BuddyPress_GroupEventsCollection($bp->groups->current_group->id, $timestamp);
		$events = $collection->to_collection();
		if (!class_exists('Eab_CalendarTable_EventArchiveCalendar')) require_once EAB_PLUGIN_DIR . 'lib/class_eab_calendar_helper.php';
		$renderer = new Eab_CalendarTable_EventArchiveCalendar($events);
		
		do_action('eab-buddypress-group_events-before_events');
		echo '<h3>' . date('F Y', $timestamp) . '</h3>'; 
		do_action('eab-buddypress-group_events-after_head');
		echo $this->_get_navigation($timestamp);
		echo $renderer->get_month_calendar($timestamp);
		echo $this->_get_navigation($timestamp);
		do_action('eab-buddypress-group_events-after_events');
	}

	private function _get_navigation ($timestamp) {
		global $bp;
		$root = $bp->root_domain . '/' . $bp->groups->slug . '/' . $bp->groups->current_group->slug . '/';
		
		$prev_url = $root . self::SLUG . date('/Y/m/', $timestamp - (28*86400));
		$next_url = $root . self::SLUG . date('/Y/m/', $timestamp + (32*86400));
		
		return '<div class="eab-bp-group_events-navigation">' .
			'<div class="eab-bp-group_events-navigation-prev" style="float:left">' .
				"<a href='{$prev_url}'>" . __('Prev', Eab_EventsHub::TEXT_DOMAIN) . '</a>' .
			'</div>' .
			'<div class="eab-bp-group_events-navigation-next" style="float:right">' .
				"<a href='{$next_url}'>" . __('Next', Eab_EventsHub::TEXT_DOMAIN) . '</a>' .
			'</div>' .
		'</div>';
	}
	
	private function _get_requested_timestamp () {
		global $bp;
		if (!$bp->action_variables) return time();
		
		$year = (int)(isset($bp->action_variables[0]) ? $bp->action_variables[0] : date('Y'));
		$year = $year ? $year : date('Y');

		$month = (int)(isset($bp->action_variables[1]) ? $bp->action_variables[1] : date('m'));
		$month = $month ? $month : date('m');
		
		return strtotime("{$year}-{$month}-01");
	}
}



class Eab_BuddyPress_GroupEventsCollection extends Eab_UpcomingCollection {
	
	private $_group_id;
		
	public function __construct ($group_id, $timestamp=false, $args=array()) {
		$this->_group_id = $group_id;
		parent::__construct($timestamp, $args);
	}
	
	public function build_query_args ($args) {
		$args = parent::build_query_args($args);
		$args['meta_query'][] = array(
			'key' => 'eab_event-bp-group_event',
			'value' => $this->_group_id,
		);
		return $args;
	}
}

Eab_BuddyPress_GroupEvents::serve();


/**
 * Group events add-on template extension.
 */
class Eab_GroupEvents_Template extends Eab_Template {

	public static function get_group ($event_id=false) {
		if (!$event_id) {
			global $post;
			$event_id = $post->ID;
		}
		if (!$event_id) return false;
		
		$group_id = get_post_meta($event_id, 'eab_event-bp-group_event', true);
		if (!$group_id) return false;
		
		$group = groups_get_group(array('group_id' => $group_id));
		if (!$group) return false;

		return $group;
	}

	public static function get_group_name ($event_id=false) {
		$group = self::get_group($event_id);
		return $group->name;
	}
}