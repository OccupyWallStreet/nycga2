<?php
/**
 * Renders form elements for admin settings pages.
 */
class Wdsb_AdminFormRenderer {
	function _get_option ($key=false) {
		$opts = WP_NETWORK_ADMIN ? get_site_option('wdsb') : get_option('wdsb');
		return $key ? @$opts[$key] : $opts;
	}

	function _create_checkbox ($name) {
		$opt = $this->_get_option();
		$value = @$opt[$name];
		return
			"<input type='radio' name='wdsb[{$name}]' id='{$name}-yes' value='1' " . ((int)$value ? 'checked="checked" ' : '') . " /> " .
				"<label for='{$name}-yes'>" . __('Yes', 'wdsb') . "</label>" .
			'&nbsp;' .
			"<input type='radio' name='wdsb[{$name}]' id='{$name}-no' value='0' " . (!(int)$value ? 'checked="checked" ' : '') . " /> " .
				"<label for='{$name}-no'>" . __('No', 'wdsb') . "</label>" .
		"";
	}

	function _create_radiobox ($name, $value) {
		$opt = $this->_get_option();
		$checked = (@$opt[$name] == $value) ? true : false;
		return "<input type='radio' name='wdsb[{$name}]' id='{$name}-{$value}' value='{$value}' " . ($checked ? 'checked="checked" ' : '') . " /> ";
	}


	function create_services_box () {
		$services = array (
			'google' => 'Google +1',
			'facebook' => 'Facebook Like',
			'twitter' => 'Tweet this',
			'stumble_upon' => 'Stumble upon',
			'delicious' => 'Del.icio.us',
			'reddit' => 'Reddit',
			'linkedin' => 'LinkedIn',
			'pinterest' => 'Pinterest',
		);
		if (function_exists('wdpv_get_vote_up_ms')) $services['post_voting'] = 'Post Voting'; 
		$externals = array (
			'google',
			'twitter',
			'linkedin',
		);

		$load = $this->_get_option('services');
		$load = is_array($load) ? $load : array();

		$services = array_merge($load, $services);

		$skip = $this->_get_option('skip_script');
		$skip = is_array($skip) ? $skip : array();

		echo "<ul id='wdsb-services'>";
		foreach ($services as $key => $name) {
			$disabled = isset($load[$key]) ? '' : 'wdsb-disabled';
			if ('post_voting' === $key && !function_exists('wdpv_get_vote_up_ms')) continue;
			echo "<li class='wdsb-service-item {$disabled}'>";
			if (is_array($name)) {
				echo $name['name'] .
					"<br/><a href='#' class='wdsb_remove_service'>" . __('Remove this service', 'wdsb') . '</a>' .
					'<input type="hidden" name="wdsb[services][' . $key . '][name]" value="' . esc_attr($name['name']) . '" />' .
					'<input type="hidden" name="wdsb[services][' . $key . '][code]" value="' . esc_attr($name['code']) . '" />' .
				'</div>';
			} else {
				echo "<img src='" . WDSB_PLUGIN_URL . "/img/{$key}.png' width='50px' />" .
					"<input type='checkbox' name='wdsb[services][{$key}]' value='{$key}' " .
						"id='wdsb-services-{$key}' " .
						(in_array($key, $load) ? "checked='checked'" : "") .
					"/> " .
						"<label for='wdsb-services-{$key}'>{$name}</label>" .
					'<br />';
				if (in_array($key, $externals)) echo
					"<input type='checkbox' name='wdsb[skip_script][{$key}]' value='{$key}' " .
						"id='wdsb-skip_script-{$key}' " .
						(in_array($key, $skip) ? "checked='checked'" : "") .
					"/> " .
						"<label for='wdsb-skip_script-{$key}'>" .
							'<small>' . __('My page already uses scripts from this service', 'wdsb') . '</small>' .
						"</label>" .
					"";
			}

			echo "<div class='clear'></div></li>";
		}
		echo "</ul>";
	}

	function create_custom_service_box () {
		echo '<p>' .
			'<label for="wdsb_new_custom_service-name">' . __('Name', 'wdsb') . '</label>' .
			'<input type="text" name="wdsb[new_service][name]" id="wdsb_new_custom_service-name" class="widefat" />' .
		'</p>';
		echo '<p>' .
			'<label for="wdsb_new_custom_service-code">' . __('Code', 'wdsb') . '</label>' .
			'<textarea rows="1" name="wdsb[new_service][code]" id="wdsb_new_custom_service-code" class="widefat"></textarea>' .
		'</p>';
		echo '<p>' .
			'<input type="submit" class="button" value="' . __('Add', 'wdsb') . '" />' .
		'</p>';
		'';
	}

	function create_appearance_box () {
		$background = $this->_get_option('background');
		$border = $this->_get_option('border');

		echo '<label for="wdsb-background">' .
			__('Background', 'wdsb') . '</label> ' .
			"<input type='text' class='widefat' name='wdsb[background]' id='wdsb-background' value='{$background}' />" .
			'<div><small>' . __('e.g. <code>#C6C6C6</code>') . '</small></div>' .
		'<br />';
		echo '<label for="wdsb-border">' .
			__('Border', 'wdsb') . '</label> ' .
			"<input type='text' class='widefat' name='wdsb[border]' id='wdsb-border' value='{$border}' />" .
			'<div><small>' . __('e.g. <code>2px solid #AAA</code>') . '</small></div>' .
		'<br />';
	}

	function create_min_width_box () {
		$width = $this->_get_option('min_width');

		echo "<input type='text' size='4' name='wdsb[min_width]' id='wdsb-min_width' value='{$width}' /> px" .
			'<div><small>' . __('The box will be shown inline in windows narrower than this width <br />This is dependent on your theme layout', 'wdsb') . '</small></div>' .
		'<br />';
	}

	function create_top_offset_box () {
		$top_offset = (int)$this->_get_option('top_offset');
		$top_relative = $this->_get_option('top_relative');
		$top_selector = $this->_get_option('top_selector');

		$tops = array(
			'text' => __('Text', 'wdsb'),
			'page-top' => __('Page top', 'wdsb'),
			'page-bottom' => __('Page bottom', 'wdsb'),
			'selector' => __('Selector', 'wdsb'),
		);

		echo
			"<label for='wdsb-top_relative'>" . __('My box will be vertically positioned with respect to:', 'wdsb') . '</label> ' .
			'<select name="wdsb[top_relative]" id="wdsb-top_relative">'
		;
		foreach ($tops as $pos => $label) {
			$selected = ($pos == $top_relative) ? 'selected="selected"' : '';
			echo "<option value='{$pos}' {$selected}>{$label}</option>";
		}
		echo '</select><br />';
		echo
			"<label for='wdsb-top_offset'>" . __('Offset:', 'wdsb') . '</label> ' .
				"<input type='text' size='4' name='wdsb[top_offset]' id='wdsb-top_offset' value='{$top_offset}' /> px" .
				'<div><small>' . __('The box will be shown this far from the top or bottom edge, text, or from your selector below', 'wdsb') . '</small></div>'
		;
		echo
			'<div id="wdsb-top_selector-root">' .
			'<label for="wdsb-top_selector">' . __('Stick to element with this selector', 'wdsb') . '</label>' .
			"<input type='text' class='widefat' name='wdsb[top_selector]' id='wdsb-top_selector' value='{$top_selector}' />" .
			'<div><small>' . __('e.g. <code>#primary</code>') . '</small></div>' .
		'</div>';
	}

	function create_vertical_limits_box () {
		$top_selector = $this->_get_option('top_limit_selector');
		$top_offset = (int)$this->_get_option('top_limit_offset');
		$bottom_selector = $this->_get_option('bottom_limit_selector');
		$bottom_offset = (int)$this->_get_option('bottom_limit_offset');

		echo "<div>" .
			sprintf(
				__('My box will never go higher than %s px, relative to the bottom this element: %s <small>(use a CSS selector, or leave empty to base calculations on page top)</small>', 'wdsb'),
				'<input type="text" name="wdsb[top_limit_offset]" size="3" value="' . $top_offset . '" />',
				'<input type="text" name="wdsb[top_limit_selector]" size="8" value="' . $top_selector . '" />'
			) .
		"</div>";
		echo "<div>" .
			sprintf(
				__('My box will never go lower than %s px, relative to the top of this element: %s <small>(use a CSS selector, or leave empty to base calculations on page bottom)</small>', 'wdsb'),
				'<input type="text" name="wdsb[bottom_limit_offset]" size="3" value="' . $bottom_offset . '" />',
				'<input type="text" name="wdsb[bottom_limit_selector]" size="8" value="' . $bottom_selector . '" />'
			) .
		"</div>";
	}

	function create_horizontal_offset_box () {
		$offset = (int)$this->_get_option('horizontal_offset');
		$relative = $this->_get_option('horizontal_relative');
		$selector = $this->_get_option('horizontal_selector');
		$direction = $this->_get_option('horizontal_direction');

		$lefts = array(
			'text' => __('Text', 'wdsb'),
			'page' => __('Page', 'wdsb'),
			'selector' => __('Selector', 'wdsb'),
		);
		$dirs = array(
			'left' => __('left', 'wdsb'),
			'right' => __('right', 'wdsb'),
		);

		echo
			"<label for='wdsb-left_relative'>" . __('Horizontal position of my box will be calculated with respect to', 'wdsb') . '</label> ' .
				'<select name="wdsb[horizontal_direction]">'
			;
		foreach ($dirs as $dir => $label) {
			$selected = ($dir == $direction) ? 'selected="selected"' : '';
			echo "<option value='{$dir}' {$selected}>{$label}</option>";
		}
		echo '</select>';
		_e('side of my', 'wdsb');
		echo '<select name="wdsb[horizontal_relative]" id="wdsb-left_relative">';
		foreach ($lefts as $pos => $label) {
			$selected = ($pos == $relative) ? 'selected="selected"' : '';
			echo "<option value='{$pos}' {$selected}>{$label}</option>";
		}
		echo '</select><br />';
		echo
			"<label for='wdsb-left_offset'>" . __('Offset:', 'wdsb') . '</label> ' .
				"<input type='text' size='4' name='wdsb[horizontal_offset]' id='wdsb-left_offset' value='{$offset}' /> px" .
				'<div><small>' . __('The box will be shown this far from the left edge, text, or from your selector below', 'wdsb') . '</small></div>'
		;
		echo
			'<div id="wdsb-left_selector-root">' .
			'<label for="wdsb-left_selector">' . __('Stick to element with this selector', 'wdsb') . '</label>' .
			"<input type='text' class='widefat' name='wdsb[horizontal_selector]' id='wdsb-left_selector' value='{$selector}' />" .
			'<div><small>' . __('e.g. <code>#primary</code>') . '</small></div>' .
		'</div>';
	}

	function create_advanced_box () {
		$zidx = $this->_get_option('z-index');
		$zidx = $zidx ? $zidx : 10000000;
		echo "<label for='wdsb-z-index'>" . __('Z index:', 'wdsb') . '</label> ';
		echo "<input type='text' size='8' id='wdsb-z-index' name='wdsb[z-index]' value='{$zidx}' />";
		echo '<div><small>' . __("This value will be applied to the entire floating box", 'wdsb') . '</small></div>';

		echo '<p>';
		echo "<label for='wdsb-allow_fixed'>" . __('Allow fixed positioning in IE:', 'wdsb') . '</label> ';
		echo $this->_create_checkbox('allow_fixed');
		echo '</p>';

		if (!WP_NETWORK_ADMIN) {
			echo "<p>";
			echo "<label for'wdsb-show_on_front_page'>" . __('Show on front page:', 'wdsb') . '</label> ';
			echo $this->_create_checkbox('show_on_front_page');
			echo "<br />";
			echo "<label for'wdsb-show_on_archive_pages'>" . __('Show on archive pages:', 'wdsb') . '</label> ';
			echo $this->_create_checkbox('show_on_archive_pages');
			if (defined('BP_VERSION')) {
				echo '<br />';
				echo "<label for'wdsb-show_on_buddypress_pages'>" . __('Show on BuddyPress pages:', 'wdsb') . '</label> ';
				echo $this->_create_checkbox('show_on_buddypress_pages');
			}
			echo '</p>';				
		}
	}

	function create_css_box () {
		$css = $this->_get_option('css');
		echo "<textarea rows='8' name='wdsb[css]' class='widefat'>$css</textarea>";
		echo '<div><small>' . __("These are some of the selectors you may want to use: <code>#wdsb-share-box</code>, <code>.wdsb-item</code>", 'wdsb') . '</small></div>';
	}

	function create_display_box () {
		$prevent_types = $this->_get_option('prevent_types');
		$prevent_types = is_array($prevent_types) ? $prevent_types : array();

		$types = get_post_types(array('public'=>true), 'objects');
		$types = is_array($types) ? $types : array();

		echo '<label>' . __('Do <b>NOT</b> show Floating Social for these types:', 'wdsb') . '</label>';
		echo "<ul id='wdsb-prevent-box'>";
		foreach ($types as $type) {
			if (!is_object($type)) continue;
			$label = @$type->labels->name ? $type->labels->name : $type->name;
			$selected = (in_array($type->name, $prevent_types)) ? "checked='checked'" : '';
			$individual = WP_NETWORK_ADMIN ? '' :
				'<a href="#" class="wdsb_prevent_individual">' . __('Choose individual entries', 'wdsb') . '</a>';
			echo '<li>' .
				"<input type='checkbox' id='wdsb-prevent-{$type->name}' name='wdsb[prevent_types][]' value='{$type->name}' {$selected} />" .
				'&nbsp;' .
				"<label for='wdsb-prevent-{$type->name}'>{$label}</label>" .
				'&nbsp;' .
				$individual .
				'<ul class="wdsb_entries"></ul>' .
			'</li>';
		}
		echo "</ul>";

		echo "" .
			'<label for="show_metabox-yes">' . __('Show Floating Social metabox in editor:', 'wdsb') . '</label> ' .
			$this->_create_checkbox('show_metabox') .
		"";
	}

	function create_front_footer_box () {
		echo $this->_create_checkbox('front_footer');
		echo '<div><small>' . __('Some themes may have problems with rendering the social box on non-post pages, such as front page or archive pages. Checking this option may help in such situations.', 'wdsb') . '</small></div>';

		$hook = $this->_get_option('front_hook');
		$hook = $hook ? $hook : 'get_footer';
		echo '<div id="wdsb_hook_root">' .
			'<label for="wdsb-front_hook">' . __('Use this hook:', 'wdsb') . '</label> ' .
			"<input type='text' class='widefat' name='wdsb[front_hook]' id='wdsb-front_hook' value='{$hook}' />" .
			'<div><small>' . __('<b>Advanced:</b> use this hook for rendering plugin output on non-post pages', 'wdsb') . '</small></div>' .
		'</div>';
	}

	function create_manual_box () {
		echo $this->_create_checkbox('manual_placement');
		echo '<div><small>' . __('If you enable this option, the box will <b>NOT</b> be auto-added to any of your pages.', 'wdsb') . '</small></div>';
		echo '<div><small>' . 
			__('Instead, you can edit your template files to include the box by adding this template tag:', 'wdsb') . 
			' <code>&lt;?php echo wdsb_get_sharebox(); ?&gt;</code>' .
		'</small></div>';
	}

}