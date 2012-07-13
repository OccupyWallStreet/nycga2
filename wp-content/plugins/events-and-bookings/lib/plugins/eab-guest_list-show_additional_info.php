<?php
/*
Plugin Name: Guest lists options
Description: Gives you more control over user info displayed in your RSVP lists
Plugin URI: http://premium.wpmudev.org/project/events-and-booking
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

class Eab_GuestList_ShowAdditionalInfo {
	
	private function __construct () {
		$this->_data = Eab_Options::get_instance();
	}
	
	public static function serve () {
		$me = new Eab_GuestList_ShowAdditionalInfo;
		$me->_add_hooks();
	}
	
	private function _add_hooks () {
		add_action('eab-settings-after_plugin_settings', array($this, 'show_settings'));
		add_filter('eab-settings-before_save', array($this, 'save_settings'));
		
		add_action('wp_print_styles', array($this, 'add_styles'));
		add_filter('eab-guest_list-guest_avatar', array($this, 'process_avatar'), 10, 4);
	}
	
	function add_styles () {
		global $post;
		if (Eab_EventModel::POST_TYPE != $post->post_type) return false;
	}
	
	function process_avatar ($avatar, $user_id, $user_data, $event) {
		$avatar_sizes = array(
			'' => false,
			'small' => 32,
			'medium' => 48,
			'large' => 96,
		);
		$size = $this->_data->get_option('guest_lists-sai-avatar_size');
		$size = in_array($size, array_keys($avatar_sizes)) ? (int)$avatar_sizes[$size] : false; 
		$avatar = $size ? get_avatar($user_id, $size) : false;
		
		$name = $user_data->user_login;
		switch ($this->_data->get_option('guest_lists-sai-show_name')) {
			case "username":
				$tmp_name = $user_data->user_login;
				break;
			case "display_name":
				$tmp_name = $user_data->display_name;
				break;
			case "firstname":
				$tmp_name = get_user_meta($user_id, 'first_name', true);
				break;
			case "lastname":
				$tmp_name = get_user_meta($user_id, 'last_name', true);
				break;
			case "fullname_first":
				$first = get_user_meta($user_id, 'first_name', true);
				$last = get_user_meta($user_id, 'last_name', true);
				$tmp_name = "{$first} {$last}";
				break;
			case "fullname_last":
				$first = get_user_meta($user_id, 'first_name', true);
				$last = get_user_meta($user_id, 'last_name', true);
				$tmp_name = "{$last} {$first}";
				break;
			default:
				$tmp_name = false;
				break;
			
		}
		$name = sprintf("<span class='eab-guest_lists-user_name'>%s</span>", (trim($tmp_name) ? $tmp_name : $name));
		$url = defined('BP_VERSION') 
			? bp_core_get_user_domain($user_id) : 
			get_author_posts_url($user_id)
		;
		
		if ($size) {
			$width = $size+4;
			$style = "style='display:block; width:{$width}px; float:left; overflow:hidden; margin: 0 5px;'";
		}

		$avatar = '<a ' . $style . ' href="' . $url . '" title="' . esc_attr(strip_tags($name)) . '">' .
			$avatar . $name .
		'</a>';
		return $avatar;
	}
	
	function show_settings () {
		$tips = new WpmuDev_HelpTooltips();
		$tips->set_icon_url(plugins_url('events-and-bookings/img/information.png'));
		
		$no_avatar = !$this->_data->get_option('guest_lists-sai-avatar_size') ? 'checked="checked"' : '';
		$avatar_small = ('small' == $this->_data->get_option('guest_lists-sai-avatar_size')) ? 'checked="checked"' : '';
		$avatar_med = ('medium' == $this->_data->get_option('guest_lists-sai-avatar_size')) ? 'checked="checked"' : '';
		$avatar_large = ('large' == $this->_data->get_option('guest_lists-sai-avatar_size')) ? 'checked="checked"' : '';
		
		$no_name = !$this->_data->get_option('guest_lists-sai-show_name') ? 'checked="checked"' : '';
		$username = ('username' == $this->_data->get_option('guest_lists-sai-show_name')) ? 'checked="checked"' : '';
		$display_name = ('display_name' == $this->_data->get_option('guest_lists-sai-show_name')) ? 'checked="checked"' : '';
		$firstname = ('firstname' == $this->_data->get_option('guest_lists-sai-show_name')) ? 'checked="checked"' : '';
		$lastname = ('lastname' == $this->_data->get_option('guest_lists-sai-show_name')) ? 'checked="checked"' : '';
		$fullname_first = ('fullname_first' == $this->_data->get_option('guest_lists-sai-show_name')) ? 'checked="checked"' : '';
		$fullname_last = ('fullname_last' == $this->_data->get_option('guest_lists-sai-show_name')) ? 'checked="checked"' : '';
?>
<div id="eab-settings-guest_lists" class="eab-metabox postbox">
	<h3 class="eab-hndle"><?php _e('Guest Lists Options :', Eab_EventsHub::TEXT_DOMAIN); ?></h3>
	<div class="eab-inside">
		<div class="eab-settings-settings_item" style="line-height:1.8em">
			<label><?php _e('Guest avatars', Eab_EventsHub::TEXT_DOMAIN); ?></label>
			<br />
			<input type="radio" id="eab_event-guest_lists-sai-no_avatar" name="event_default[guest_lists-sai-avatar_size]" value="" <?php print $no_avatar; ?> />
	    	<label for="eab_event-guest_lists-sai-no_avatar"><?php _e('Do not show avatars', Eab_EventsHub::TEXT_DOMAIN); ?></label>
			<span><?php echo $tips->add_tip(__('Hide user avatars in RSVP listings', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
			<br />
			<input type="radio" id="eab_event-guest_lists-sai-small_avatar" name="event_default[guest_lists-sai-avatar_size]" value="small" <?php print $avatar_small; ?> />
	    	<label for="eab_event-guest_lists-sai-small_avatar"><?php _e('Show small avatars', Eab_EventsHub::TEXT_DOMAIN); ?></label>
			<span><?php echo $tips->add_tip(__('Show small avatars in RSVP listings', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
			<br />
			<input type="radio" id="eab_event-guest_lists-sai-med_avatar" name="event_default[guest_lists-sai-avatar_size]" value="medium" <?php print $avatar_med; ?> />
	    	<label for="eab_event-guest_lists-sai-med_avatar"><?php _e('Show medium avatars', Eab_EventsHub::TEXT_DOMAIN); ?></label>
			<span><?php echo $tips->add_tip(__('Show medium avatars in RSVP listings', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
			<br />
			<input type="radio" id="eab_event-guest_lists-sai-large_avatar" name="event_default[guest_lists-sai-avatar_size]" value="large" <?php print $avatar_large; ?> />
	    	<label for="eab_event-guest_lists-sai-large_avatar"><?php _e('Show large avatars', Eab_EventsHub::TEXT_DOMAIN); ?></label>
			<span><?php echo $tips->add_tip(__('Show large avatars in RSVP listings', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
			<p></p>
	    </div>
		<div class="eab-settings-settings_item" style="line-height:1.8em">
			<label><?php _e('Guest names', Eab_EventsHub::TEXT_DOMAIN); ?></label>
			<br />
			<input type="radio" id="eab_event-guest_lists-sai-show_name-no_name" name="event_default[guest_lists-sai-show_name]" value="" <?php print $no_name; ?> />
	    	<label for="eab_event-guest_lists-sai-show_name-no_name"><?php _e('Do not show name', Eab_EventsHub::TEXT_DOMAIN); ?></label>
			<span><?php echo $tips->add_tip(__('Hide names from RSVP lists', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
	    	<br />
			<input type="radio" id="eab_event-guest_lists-sai-show_name-display_name" name="event_default[guest_lists-sai-show_name]" value="display_name" <?php print $display_name; ?> />
	    	<label for="eab_event-guest_lists-sai-show_name-display_name"><?php _e('Show display name', Eab_EventsHub::TEXT_DOMAIN); ?></label>
			<span><?php echo $tips->add_tip(__('Show user display names in RSVP listings', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
	    	<br />
			<input type="radio" id="eab_event-guest_lists-sai-show_name-username" name="event_default[guest_lists-sai-show_name]" value="username" <?php print $username; ?> />
	    	<label for="eab_event-guest_lists-sai-show_name-username"><?php _e('Show username', Eab_EventsHub::TEXT_DOMAIN); ?></label>
			<span><?php echo $tips->add_tip(__('Show usernames in RSVP listings', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
	    	<br />
			<input type="radio" id="eab_event-guest_lists-sai-show_name-firstname" name="event_default[guest_lists-sai-show_name]" value="firstname" <?php print $firstname; ?> />
	    	<label for="eab_event-guest_lists-sai-show_name-firstname"><?php _e('Show first name', Eab_EventsHub::TEXT_DOMAIN); ?></label>
			<span><?php echo $tips->add_tip(__('Show user first names in RSVP listings', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
	    	<br />
			<input type="radio" id="eab_event-guest_lists-sai-show_name-lastname" name="event_default[guest_lists-sai-show_name]" value="lastname" <?php print $lastname; ?> />
	    	<label for="eab_event-guest_lists-sai-show_name-lastname"><?php _e('Show first name', Eab_EventsHub::TEXT_DOMAIN); ?></label>
			<span><?php echo $tips->add_tip(__('Show user last names in RSVP listings', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
	    	<br />
			<input type="radio" id="eab_event-guest_lists-sai-show_name-fullname_first" name="event_default[guest_lists-sai-show_name]" value="fullname_first" <?php print $fullname_first; ?> />
	    	<label for="eab_event-guest_lists-sai-show_name-fullname_first"><?php _e('Show full name, first name first', Eab_EventsHub::TEXT_DOMAIN); ?></label>
			<span><?php echo $tips->add_tip(__('Show user full names in RSVP listings as Firstname Lastname', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
	    	<br />
			<input type="radio" id="eab_event-guest_lists-sai-show_name-fullname_last" name="event_default[guest_lists-sai-show_name]" value="fullname_last" <?php print $fullname_last; ?> />
	    	<label for="eab_event-guest_lists-sai-show_name-fullname_last"><?php _e('Show full name, last name first', Eab_EventsHub::TEXT_DOMAIN); ?></label>
			<span><?php echo $tips->add_tip(__('Show user full names in RSVP listings as Lastname Firstname', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
			<p></p>
	    </div>
	</div>
</div>
<?php
	}

	function save_settings ($options) {
		$options['guest_lists-sai-avatar_size'] = $_POST['event_default']['guest_lists-sai-avatar_size'];
		$options['guest_lists-sai-show_name'] = $_POST['event_default']['guest_lists-sai-show_name'];
		return $options;
	}
	
}

Eab_GuestList_ShowAdditionalInfo::serve();
