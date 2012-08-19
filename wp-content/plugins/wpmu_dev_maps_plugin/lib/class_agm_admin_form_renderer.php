<?php

/**
 * Handles rendering of form elements for plugin Options page.
 */
class AgmAdminFormRenderer {

	function _create_small_text_box ($name, $value) {
		return "<input type='text' disabled='disabled' name='agm_google_maps[{$name}]' id='{$name}' size='3' value='{$value}' />";
	}

	function create_height_box () {
		$opt = get_option('agm_google_maps');
		echo $this->_create_small_text_box('height', @$opt['height']) . 'px';
	}

	function create_width_box () {
		$opt = get_option('agm_google_maps');
		echo $this->_create_small_text_box('width', @$opt['width']) . 'px';
	}

	function create_image_limit_box () {
		$opt = get_option('agm_google_maps');
		$limit = (isset($opt['image_limit'])) ? $opt['image_limit'] : 10;
		echo $this->_create_small_text_box('image_limit', $limit);
	}

	function  create_map_type_box () {
		$opt = get_option('agm_google_maps');
		$items = array(
			'ROADMAP',
			'SATELLITE',
			'HYBRID',
			'TERRAIN'
		);
		echo "<select id='map_type' disabled='disabled' name='agm_google_maps[map_type]'>";
		foreach($items as $item) {
			$selected = ($opt['map_type'] == $item) ? 'selected="selected"' : '';
			echo "<option value='$item' $selected>$item</option>";
		}
		echo "</select>";
	}
	function  create_map_zoom_box () {
		$opt = get_option('agm_google_maps');
		$items = array(
			'1' => 'Earth',
			'3' => 'Continent',
			'5' => 'Region',
			'7' => 'Nearby Cities',
			'12' => 'City Plan',
			'15' => 'Details',
		);
		echo "<select id='zoom' name='agm_google_maps[zoom]'>";
		foreach($items as $item=>$label) {
			$selected = ($opt['zoom'] == $item) ? 'selected="selected"' : '';
			echo "<option value='{$item}' {$selected}>{$label}</option>";
		}
		echo "</select>";
		_e('<p>Please note, these titles are only approximations, but generally fit the description.</p>', 'agm_google_maps');
	}

	function  create_map_units_box () {
		$opt = get_option('agm_google_maps');
		$items = array(
			'METRIC' => __('Metric', 'agm_google_maps'),
			'IMPERIAL' => __('Imperial', 'agm_google_maps'),
		);
		echo "<select id='zoom' name='agm_google_maps[units]'>";
		foreach($items as $item=>$label) {
			$selected = ($opt['units'] == $item) ? 'selected="selected"' : '';
			echo "<option value='{$item}' {$selected}>{$label}</option>";
		}
		echo "</select>";
		_e('<div>These units will be used to express distances for directions</div>', 'agm_google_maps');
	}

	function  create_image_size_box () {
		$opt = get_option('agm_google_maps');
		$items = array(
			'small',
			'medium',
			'thumbnail',
			'square',
			'mini_square',
		);
		echo "<select id='image_size' disabled='disabled' name='agm_google_maps[image_size]'>";
		foreach($items as $item) {
			$selected = ($opt['image_size'] == $item) ? 'selected="selected"' : '';
			echo "<option value='$item' $selected>$item</option>";
		}
		echo "</select>";
	}

	function create_alignment_box () {
		$opt = get_option('agm_google_maps');
		$pos = $opt['map_alignment'];
		echo
			'<input type="radio" id="map_alignment_left" disabled="disabled" name="agm_google_maps[map_alignment]" value="left" ' . (('left' == $pos) ? 'checked="checked"' : '') . '  />' .
			'<label for="map_alignment_left">' . '<img src="' . AGM_PLUGIN_URL . '/img/system/left.png" />' . __('Left', 'agm_google_maps') . '</label><br/>'
		;
		echo
			'<input type="radio" id="map_alignment_center" disabled="disabled" name="agm_google_maps[map_alignment]" value="center" ' . (('center' == $pos) ? 'checked="checked"' : '') . '  />' .
			'<label for="map_alignment_center">' . '<img src="' . AGM_PLUGIN_URL . '/img/system/center.png" />' . __('Center', 'agm_google_maps') . '</label><br/>'
		;
		echo
			'<input type="radio" id="map_alignment_right" disabled="disabled" name="agm_google_maps[map_alignment]" value="right" ' . (('right' == $pos) ? 'checked="checked"' : '') . '  />' .
			'<label for="map_alignment_right">' . '<img src="' . AGM_PLUGIN_URL . '/img/system/right.png" />' . __('Right', 'agm_google_maps') . '</label><br/>'
		;
	}

	function create_custom_css_box () {
		$opt = get_option('agm_google_maps');
		$css = @$opt['additional_css'];
		echo "<textarea name='agm_google_maps[additional_css]' disabled='disabled' class='widefat' rows='4' cols='32'>{$css}</textarea>";
		_e('<p>You can use this box to add some quick style changes, to better blend maps appearance with your themes.</p>', 'agm_google_maps');
		_e('<p>You may want to set styles for some of these selectors: <code>.agm_mh_info_title</code>, <code>.agm_mh_info_body</code>, <code>a.agm_mh_marker_item_directions</code>, <code>.agm_mh_marker_list</code>, <code>.agm_mh_marker_item</code>, <code>.agm_mh_marker_item_content</code></p>', 'agm_google_maps');
	}

	function _create_cfyn_box ($name, $value) {
		return '<input type="radio" name="agm_google_maps[custom_fields_options][' . $name . ']" id="agm_cfyn_' . $name . '-yes" value="1" ' . ((1 == $value) ? 'checked="checked"' : '') . ' /> <label for="agm_cfyn_' . $name . '-yes">' . __("Yes", 'agm_google_maps') . '</label>' .
			'&nbsp;' .
			'<input type="radio" name="agm_google_maps[custom_fields_options][' . $name . ']" id="agm_cfyn_' . $name . '-no" value="0" ' . ((0 == $value) ? 'checked="checked"' : '') . ' /> <label for="agm_cfyn_' . $name . '-no">' . __("No", 'agm_google_maps') . '</label>' .
		'';
	}

	function create_snapping_box () {
		$opt = apply_filters('agm_google_maps-options', get_option('agm_google_maps'));
		$use = isset($opt['snapping']) ? $opt['snapping'] : 1;
		echo '<input type="radio" name="agm_google_maps[snapping]" id="agm_snapping-yes" value="1" ' . ($use ? 'checked="checked"' : '') . ' /> <label for="agm_snapping-yes">' . __("Yes", 'agm_google_maps') . '</label>' .
			'&nbsp;' .
			'<input type="radio" name="agm_google_maps[snapping]" id="agm_snapping-no" value="0" ' . ($use ? '' : 'checked="checked"') . ' /> <label for="agm_snapping-no">' . __("No", 'agm_google_maps') . '</label>' .
		'';
	}

	function create_use_custom_fields_box () {
		$opt = get_option('agm_google_maps');
		$use = @$opt['use_custom_fields'];
		echo '<input type="radio" name="agm_google_maps[use_custom_fields]" id="agm_use_custom_fields-yes" value="1" ' . ($use ? 'checked="checked"' : '') . ' /> <label for="agm_use_custom_fields-yes">' . __("Yes", 'agm_google_maps') . '</label>' .
			'&nbsp;' .
			'<input type="radio" name="agm_google_maps[use_custom_fields]" id="agm_use_custom_fields-no" value="0" ' . ($use ? '' : 'checked="checked"') . ' /> <label for="agm_use_custom_fields-no">' . __("No", 'agm_google_maps') . '</label>' .
		'';
	}
	function create_custom_fields_map_box () {
		$opt = get_option('agm_google_maps');
		$lat_field = @$opt['custom_fields_map']['latitude_field'];
		$lon_field = @$opt['custom_fields_map']['longitude_field'];
		$add_field = @$opt['custom_fields_map']['address_field'];

		echo '<div><b>' . __('My posts have latitude/longitude fields', 'agm_google_maps') . '</b></div>';
		echo __("Latitude field name:", 'agm_google_maps') . ' <input type="text" name="agm_google_maps[custom_fields_map][latitude_field]" size="12" maxisize="32" value="' . $lat_field . '" />';
		echo '<br />';
		echo __("Logitude field name:", 'agm_google_maps') . ' <input type="text" name="agm_google_maps[custom_fields_map][longitude_field]" size="12" maxisize="32" value="' . $lon_field . '" />';

		echo '<div><b>' . __('My posts have an address field', 'agm_google_maps') . '</b></div>';
		echo __("Address field name:", 'agm_google_maps') . ' <input type="text" name="agm_google_maps[custom_fields_map][address_field]" size="12" maxisize="32" value="' . $add_field . '" />';
	}
	function create_custom_fields_options_box () {
		$opt = get_option('agm_google_maps');
		$opt = $opt['custom_fields_options'];
		echo "<div><small>" . __("(A new map will be automatically created, using the defaults you specified above)", 'agm_google_maps') . "</small></div>";
		echo __("Associate the new map to post:", 'agm_google_maps') . ' ' . $this->_create_cfyn_box('associate_map', @$opt['associate_map']) . '<br />';
		echo __("Automatically show the map:", 'agm_google_maps') . ' ' . $this->_create_cfyn_box('autoshow_map', @$opt['autoshow_map']) . '<br />';

		$positions = array (
			'top' => 'Above',
			'bottom' => 'Below',
		);
		$select = '<select name="agm_google_maps[custom_fields_options][map_position]">';
		foreach ($positions as $key=>$lbl) {
			$select .= "<option value='{$key}' " . (($key == @$opt['map_position']) ? 'selected="selected"' : '') . ">" . __($lbl, 'agm_google_maps') . '</option>';
		}
		$select .= '</select>';

		printf (
			__("If previous option is set to \"Yes\", the new map will be shown %s the post body", 'agm_google_maps'),
			$select
		);
	}

	function create_plugins_box () {
		$all = AgmPluginsHandler::get_all_plugins();
		$active = AgmPluginsHandler::get_active_plugins();
		$sections = array('thead', 'tfoot');

		echo "<table class='widefat'>";
		foreach ($sections as $section) {
			echo "<{$section}>";
			echo '<tr>';
			echo '<th width="30%">' . __('Add-on name', 'agm_google_maps') . '</th>';
			echo '<th>' . __('Add-on description', 'agm_google_maps') . '</th>';
			echo '</tr>';
			echo "</{$section}>";
		}
		echo "<tbody>";
		foreach ($all as $plugin) {
			$plugin_data = AgmPluginsHandler::get_plugin_info($plugin);
			if (!@$plugin_data['Name']) continue; // Require the name
			$is_active = in_array($plugin, $active);
			echo "<tr>";
			echo "<td width='30%'>";
			echo '<b>' . $plugin_data['Name'] . '</b>';
			echo "<br />";
			echo '<a style="color:#CC0000;" title="Upgrade Now" href="http://premium.wpmudev.org/project/wordpress-google-maps-plugin">Upgrade to Google Maps Pro to enable add-ons</a></p>';
			echo "</td>";
			echo '<td>' .
				$plugin_data['Description'] .
				'<br />' .
				sprintf(__('Version %s', 'agm_google_maps'), $plugin_data['Version']) .
				'&nbsp;|&nbsp;' .
				sprintf(__('by %s', 'agm_google_maps'), '<a href="' . $plugin_data['Plugin URI'] . '">' . $plugin_data['Author'] . '</a>') .
			'</td>';
			echo "</tr>";
		}
		echo "</tbody>";
		echo "</table>";
	}
}
