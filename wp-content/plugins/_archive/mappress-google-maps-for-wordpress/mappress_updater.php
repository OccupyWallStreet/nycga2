<?php
class Mappress_Updater {
	var $basename,
		$pro_url = 'http://wphostreviews.com/mappress',
		$plugin_name = 'mappress';
	
	function Mappress_Updater($basename) {
		$this->basename = $basename;
		
		// Plugin actions
		add_filter("plugin_action_links_{$this->basename}", array(&$this, 'plugin_action_links'), 10, 2);         

		// Updates for Pro
		if (class_exists('Mappress_Pro')) 
			add_filter("site_transient_update_plugins", array(&$this, 'site_transient_update_plugins'));
	}
		
	function plugin_action_links($links, $file) { 
		$settings_link = "<a href='" . admin_url("options-general.php?page={$this->plugin_name}") . "'>" . __('Settings') . "</a>";
		array_unshift( $links, $settings_link ); 
		return $links;
	}

	/**
	* Intercept repository updates for Pro, minor releases, and beta
	*
	* @param mixed $value
	*/
	function site_transient_update_plugins($value) {
		if (isset($value->response[$this->basename])) {
			// Remove the proposed update 
			unset($value->response[$this->basename]);

			// Suggest Pro update if not a minor versions
			$dots = substr_count($value->response[$this->basename]->new_version, '.');			
			if ($dots < 2) {
				if (!has_filter( "after_plugin_row_$this->basename" ))
					add_filter("after_plugin_row_$this->basename", array(&$this, 'after_plugin_row_pro'), 20);
			}
		}
		return $value;
	}

	function after_plugin_row_pro($value) {
		$pro_link = "<a href='$this->pro_url'>" . __('download it now', 'mappress') . "</a>";
		
		echo '<tr class="plugin-update-tr">'
			. '<td colspan="3" class="plugin-update">'
			. '<div class="update-message">'
			. sprintf(__("A PRO Version update is available for manual installation: %s", 'mappress'), $pro_link)
			. '</div>'
			. '</td></tr>';        
	}
} // End class Mappress_Updater
?>