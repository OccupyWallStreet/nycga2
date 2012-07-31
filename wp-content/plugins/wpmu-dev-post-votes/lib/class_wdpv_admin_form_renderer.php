<?php
/**
 * Renders form elements for admin settings pages.
 */
class Wdpv_AdminFormRenderer {
	function _get_option () {
		return WP_NETWORK_ADMIN ? get_site_option('wdpv') : get_option('wdpv');
	}

	function _create_checkbox ($name) {
		$opt = $this->_get_option();
		$value = @$opt[$name];
		$disabled = (@$opt['disable_siteadmin_changes'] && !current_user_can('manage_network_options')) ? 'disabled="disabled"' : '';
		return
			"<input {$disabled} type='radio' name='wdpv[{$name}]' id='{$name}-yes' value='1' " . ((int)$value ? 'checked="checked" ' : '') . " /> " .
				"<label for='{$name}-yes'>" . __('Yes', 'wdpv') . "</label>" .
			'&nbsp;' .
			"<input {$disabled} type='radio' name='wdpv[{$name}]' id='{$name}-no' value='0' " . (!(int)$value ? 'checked="checked" ' : '') . " /> " .
				"<label for='{$name}-no'>" . __('No', 'wdpv') . "</label>" .
		"";
	}

	function _create_radiobox ($name, $value) {
		$opt = $this->_get_option();
		$checked = (@$opt[$name] == $value) ? true : false;
		return "<input type='radio' name='wdpv[{$name}]' id='{$name}-{$value}' value='{$value}' " . ($checked ? 'checked="checked" ' : '') . " /> ";
	}

	function create_allow_voting_box () {
		echo $this->_create_checkbox ('allow_voting');
	}
	function create_allow_visitor_voting_box () {
		echo $this->_create_checkbox ('allow_visitor_voting');
	}
	function create_use_ip_check_box () {
		echo $this->_create_checkbox ('use_ip_check');
		_e(
			'<p>By default, visitors are tracked by IP too in order to prevent multiple voting. However, this can be problematic in certain cases (e.g. multiple users behind a single router).</p>' .
			'<p>Set this to "No" if you don\'t want to use this measure.</p>',
			'wdpv'
		);
	}
	function create_show_login_link_box () {
		echo $this->_create_checkbox ('show_login_link');
		_e(
			'<p>By default, if visitor voting is not allowed, voting will not be shown at all.</p>' .
			'<p>Set this to "Yes" if you wish to have the login link instead.</p>',
			'wdpv'
		);
	}
	function create_voting_position_box () {
		$positions = array (
			'top' => __('Before the post', 'wdpv'),
			'bottom' => __('After the post', 'wdpv'),
			'both' => __('Both before and after the post', 'wdpv'),
			'manual' => __('Manually position the box using shortcode or widget', 'wdpv'),
		);
		foreach ($positions as $pos => $label) {
			echo $this->_create_radiobox ('voting_position', $pos);
			echo "<label for='voting_position-{$pos}'>$label</label><br />";
		}
	}
	function create_front_page_voting_box () {
		echo $this->_create_checkbox ('front_page_voting');
		_e(
			'<p>By default, voting will be shown only on singular pages.</p>' .
			'<p>Set this option to "Yes" to add voting to all posts on the front page.</p>',
			'wdpv'
		);
	}
	function create_voting_appearance_box () {
		$skins = array (
			'' => __('Default', 'wdpv'),
			'arrows' => __('Arrows', 'wdpv'),
			'plusminus' => __('Plus/Minus', 'wdpv'),
			'whitearrow' => __('White arrows', 'wdpv'),
			'qa' => __('Q&amp;A arrows', 'wdpv'),
		);
		foreach ($skins as $skin => $label) {
			echo $this->_create_radiobox ('voting_appearance', $skin);
			echo "<label for='voting_appearance-{$skin}'>$label</label><br />";
			$path_fragment = $skin ? "{$skin}/" : '';
			echo '<div class="wdpv_preview">' . __('Preview:', 'wdpv') .
				' <img src="' . WDPV_PLUGIN_URL . '/img/' . $path_fragment . 'up.png" />' .
				' <img src="' . WDPV_PLUGIN_URL . '/img/' . $path_fragment . 'down.png" />' .
			'</div>';
		}
	}
	function create_voting_positive_box () {
		echo $this->_create_checkbox ('voting_positive');
		_e(
			'<p>If checked, this option will prevent negative votes by showing only positive voting link.</p>',
			'wdpv'
		);
	}
	function create_disable_siteadmin_changes_box () {
		echo $this->_create_checkbox ('disable_siteadmin_changes');
		_e(
			'<p>By default, Site Admins are allowed to access plugin settings and make changes.</p>' .
			'<p>Set this option to "Yes" to prevent them from making changes to plugin settings.</p>',
			'wdpv'
		);
	}

	function create_skip_post_types_box () {
		$post_types = get_post_types(array('public'=>true), 'names');
		$opt = $this->_get_option();
		$skip_types = is_array(@$opt['skip_post_types']) ? @$opt['skip_post_types'] : array();

		foreach ($post_types as $tid=>$type) {
			$checked = in_array($type, $skip_types) ? 'checked="checked"' : '';
			echo
				"<input type='hidden' name='wdpv[skip_post_types][{$type}]' value='0' />" . // Override for checkbox
				"<input {$checked} type='checkbox' name='wdpv[skip_post_types][{$type}]' id='skip_post_types-{$tid}' value='{$type}' /> " .
				"<label for='skip_post_types-{$tid}'>" . ucfirst($type) . "</label>" .
			"<br />";
		}
		_e(
			'<p>Voting will <strong><em>not</em></strong> be shown for selected types.</p>',
			'wdpv'
		);
	}

// BuddyPress

	function create_bp_publish_activity_box () {
		echo $this->_create_checkbox ('bp_publish_activity');
		echo '<div><small>' . __('Activities will be recorded only for your logged in users', 'wdpv') . '</small></div>';
		echo __("Hide from sitewide activity stream:", 'wdpv') . ' ';
		echo $this->_create_checkbox ('bp_publish_activity_local');
		echo '<div><small>' . __('Recorded activities will be hidden from your sitewide activity stream', 'wdpv') . '</small></div>';
	}
	function create_bp_profile_votes_box () {
		$opt = $this->_get_option();
		echo $this->_create_checkbox ('bp_profile_votes');
		echo "<br />";

		// Set defaults
		$opt['bp_profile_votes_limit'] = @$opt['bp_profile_votes_limit'] ? $opt['bp_profile_votes_limit'] : 0;
		$opt['bp_profile_votes_period'] = @$opt['bp_profile_votes_period'] ? $opt['bp_profile_votes_period'] : 1;
		$opt['bp_profile_votes_unit'] = @$opt['bp_profile_votes_unit'] ? $opt['bp_profile_votes_unit'] : 'month';

		echo __("Show", 'wdpv') . ' ';
		echo '<select name="wdpv[bp_profile_votes_limit]">';
		for ($i=0; $i<=20; $i++) {
			$title = $i ? $i : __('all', 'wdpv');
			$selected = ($i == @$opt['bp_profile_votes_limit']) ? 'selected="selected"' : '';
			echo "<option value='{$i}' {$selected}>{$title}</option>";
		}
		echo '</select> ';

		echo __('vote(s) within last', 'wdpv') . ' ';
		echo '<select name="wdpv[bp_profile_votes_period]">';
		for ($i=1; $i<=24; $i++) {
			$selected = ($i == @$opt['bp_profile_votes_period']) ? 'selected="selected"' : '';
			echo "<option value='{$i}' {$selected}>{$i}</option>";
		}
		echo '</select> ';
		echo '<select name="wdpv[bp_profile_votes_unit]">';
		foreach (array('hour', 'day', 'week', 'month', 'year') as $unit) {
			$selected = ($unit == @$opt['bp_profile_votes_unit']) ? 'selected="selected"' : '';
			$title = ucfirst($unit) . '(s)';
			echo "<option value='{$unit}' {$selected}>{$title}</option>";
		}
		echo '</select> ';

	}

	function create_plugins_box () {
		$all = Wdpv_PluginsHandler::get_all_plugins();
		$active = Wdpv_PluginsHandler::get_active_plugins();
		$sections = array('thead', 'tfoot');

		echo "<table class='widefat'>";
		foreach ($sections as $section) {
			echo "<{$section}>";
			echo '<tr>';
			echo '<th width="30%">' . __('Add-on name', 'wdpv') . '</th>';
			echo '<th>' . __('Add-on description', 'wdpv') . '</th>';
			echo '</tr>';
			echo "</{$section}>";
		}
		echo "<tbody>";
		foreach ($all as $plugin) {
			$plugin_data = Wdpv_PluginsHandler::get_plugin_info($plugin);
			if (!@$plugin_data['Name']) continue; // Require the name
			$is_active = in_array($plugin, $active);
			echo "<tr>";
			echo "<td width='30%'>";
			echo '<b>' . $plugin_data['Name'] . '</b>';
			echo "<br />";
			echo ($is_active
				?
				'<a href="#deactivate" class="wdpv_deactivate_plugin" wdpv:plugin_id="' . esc_attr($plugin) . '">' . __('Deactivate', 'wdpv') . '</a>'
				:
				'<a href="#activate" class="wdpv_activate_plugin" wdpv:plugin_id="' . esc_attr($plugin) . '">' . __('Activate', 'wdpv') . '</a>'
			);
			echo "</td>";
			echo '<td>' .
				$plugin_data['Description'] .
				'<br />' .
				sprintf(__('Version %s', 'wdpv'), $plugin_data['Version']) .
				'&nbsp;|&nbsp;' .
				sprintf(__('by %s', 'wdpv'), '<a href="' . $plugin_data['Plugin URI'] . '">' . $plugin_data['Author'] . '</a>') .
			'</td>';
			echo "</tr>";
		}
		echo "</tbody>";
		echo "</table>";

		echo <<<EOWdpvPluginJs
<script type="text/javascript">
(function ($) {
$(function () {
	$(".wdpv_activate_plugin").click(function () {
		var me = $(this);
		var plugin_id = me.attr("wdpv:plugin_id");
		$.post(ajaxurl, {"action": "wdpv_activate_plugin", "plugin": plugin_id}, function (data) {
			window.location = window.location;
		});
		return false;
	});
	$(".wdpv_deactivate_plugin").click(function () {
		var me = $(this);
		var plugin_id = me.attr("wdpv:plugin_id");
		$.post(ajaxurl, {"action": "wdpv_deactivate_plugin", "plugin": plugin_id}, function (data) {
			window.location = window.location;
		});
		return false;
	});
});
})(jQuery);
</script>
EOWdpvPluginJs;
	}
}