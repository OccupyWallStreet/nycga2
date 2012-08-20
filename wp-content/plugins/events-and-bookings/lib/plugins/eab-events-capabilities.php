<?php
/*
Plugin Name: Capabilities
Description: Tweak and edit access privileges for your Events.
Plugin URI: http://premium.wpmudev.org/project/events-and-booking
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

class Eab_Events_Capabilities {
	
	private $_data;
	private $_capabilities = array();
	
	private function __construct () {
		$this->_data = Eab_Options::get_instance();
		$this->_capabilities = array (
			'edit_events' => __('Edit Events', Eab_EventsHub::TEXT_DOMAIN),
			'edit_others_events' => __('Edit Others Events', Eab_EventsHub::TEXT_DOMAIN),
			'publish_events' => __('Publish Events', Eab_EventsHub::TEXT_DOMAIN),
			'edit_published_events' => __('Edit Published Events', Eab_EventsHub::TEXT_DOMAIN),
			'delete_events' => __('Delete Events', Eab_EventsHub::TEXT_DOMAIN),
			'delete_published_events' => __('Delete Published Events', Eab_EventsHub::TEXT_DOMAIN),
		);
	}
	
	public static function serve () {
		$me = new Eab_Events_Capabilities;
		$me->_add_hooks();
	}
	
	private function _add_hooks () {
		add_action('eab-settings-after_plugin_settings', array($this, 'show_settings'));
		add_filter('eab-settings-before_save', array($this, 'save_settings'));
		add_action('admin_head-incsub_event_page_eab_settings', array($this, 'enqueue_dependencies'));
		
		add_filter('eab-capabilities-user_can', array($this, 'check_capability_for'), 10, 4);
	}
	
	function check_capability_for ($capability, $user, $capable, $args) {
		if (!in_array($capability, array_keys($this->_capabilities))) return $capable;

		$roles_map = $this->_data->get_option('eab-capabilities_map');
		$capabilities = $tmp = array();
		foreach ($user->roles as $role) {
			$tmp = isset($roles_map[$role]) ? $roles_map[$role] : array();
			$capabilities = array_merge($capabilities, $tmp);
		}
		
		if (!$capabilities) {
			$capabilities = $user->allcaps;
			$capability = preg_replace('/_event/', '_post', $capability);
		}
		return (int)@$capabilities[$capability];
	}
	
	function enqueue_dependencies () {
		wp_enqueue_style('eab-event-capabilities', plugins_url(basename(EAB_PLUGIN_DIR) . '/css/eab-event-capabilities.css'));
		wp_enqueue_script('eab-event-capabilities', plugins_url(basename(EAB_PLUGIN_DIR) . '/js/eab-event-capabilities.js'), array('jquery'));
	}
	
	function show_settings () {
		global $wp_roles;
		$_roles = $wp_roles->get_names();
?>
<div id="eab-settings-capabilities" class="eab-metabox postbox">
	<h3 class="eab-hndle"><?php _e('Event Capabilities :', Eab_EventsHub::TEXT_DOMAIN); ?></h3>
	<div class="eab-inside">
		<div class="eab-settings-settings_item">
			<select id="eab-event-capabilities-switch_hub">
				<option selected="selected"><?php echo __('Please, select a role to edit', Eab_EventsHub::TEXT_DOMAIN);?>&nbsp;</option>
			<?php foreach ($_roles as $role => $label) { ?>
				<option value="<?php esc_attr_e($role);?>"><?php echo $label;?>&nbsp;</option>
			<?php } ?>
			</select>
<?php
		foreach ($_roles as $role => $label) {
			echo $this->_create_role_box($role, $label);
		}
?>
			<p><input type="button" class="button" id="eab-event-capabilities-reset" value="<?php esc_attr_e(__('Reset to defaults', Eab_EventsHub::TEXT_DOMAIN));?>"</p>
		</div>
	</div>
</div>
<?php		
	}
	
	function save_settings ($options) {
		$options['eab-capabilities_map'] = @$_POST['eab-capabilities_map'];
		return $options;
	}
	
	private function _create_role_box ($role, $role_label) {
		$box = '<div class="eab-events-capabilities-per_role" id="eab-events-capabilities-editor-' . esc_attr($role) . '">';
		$box .= '<h4>' . sprintf(__('Your <b>%s</b> users can...', Eab_EventsHub::TEXT_DOMAIN), $role_label) . '</h4>';
		foreach ($this->_capabilities as $capability => $cap_label) {
			$box .= $this->_get_capability_box($role, $capability, $cap_label);
		}
		
		$box .= '</div>';
		return $box;
	}
	
	private function _get_capability_box ($role, $capability, $cap_label) {
		global $wp_roles;
		$wp_roles_map = $wp_roles->get_role($role);
		$roles_map = $this->_data->get_option('eab-capabilities_map');
		$roles_map = $roles_map ? $roles_map : array();

		$capabilities = isset($roles_map[$role]) ? $roles_map[$role] : array();
		
		if (!$capabilities) {
			$capabilities = $wp_roles_map->capabilities;
			$capability_check = preg_replace('/_event/', '_post', $capability);
		} else {
			$capability_check = $capability;
		}
		
		if ((int)@$capabilities[$capability_check]) {
			$checked = 'checked="checked"';
		} else {
			$checked = '';
		} 
		
		$box = '<div class="eab-events-capabilities-capability_box">';
		
		$box .= "<label for='eab-capabilities_map-{$role}-{$capability}'>";
		$box .= "<input type='hidden' name='eab-capabilities_map[{$role}][{$capability}]' value='' /> ";
		$box .= "<input type='checkbox' id='eab-capabilities_map-{$role}-{$capability}' name='eab-capabilities_map[{$role}][{$capability}]' value='1' {$checked} /> ";
		$box .= '&hellip;' . $cap_label . '</label>';
		
		$box .= '</div>';
		return $box;
	}
}

Eab_Events_Capabilities::serve();
