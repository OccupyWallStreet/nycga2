<?php
/**
 * Renders form elements for admin settings pages.
 */
class Wdqs_AdminFormRenderer {
	function _get_option () {
		return WP_NETWORK_ADMIN ? get_site_option('wdqs') : get_option('wdqs');
	}

	function _create_checkbox ($name, $disabled=false) {
		$opt = $this->_get_option();
		$value = @$opt[$name];
		$disabled = $disabled ? 'disabled="disabled"' : '';
		return
			"<input {$disabled} type='radio' name='wdqs[{$name}]' id='{$name}-yes' value='1' " . ((int)$value ? 'checked="checked" ' : '') . " /> " .
				"<label for='{$name}-yes'>" . __('Yes', 'wdqs') . "</label>" .
			'&nbsp;' .
			"<input {$disabled} type='radio' name='wdqs[{$name}]' id='{$name}-no' value='0' " . (!(int)$value ? 'checked="checked" ' : '') . " /> " .
				"<label for='{$name}-no'>" . __('No', 'wdqs') . "</label>" .
		"";
	}

	function _create_radiobox ($name, $value) {
		$opt = $this->_get_option();
		$checked = (@$opt[$name] == $value) ? true : false;
		return "<input type='radio' name='wdqs[{$name}]' id='{$name}-{$value}' value='{$value}' " . ($checked ? 'checked="checked" ' : '') . " /> ";
	}


	function create_show_on_public_pages_box () {
		echo $this->_create_checkbox('__disabled1__', true);
	}
	function create_show_on_front_page_box () {
		echo $this->_create_checkbox('__disabled2__', true);
	}
	function create_show_on_dashboard_box () {
		echo $this->_create_checkbox('show_on_dashboard');
	}

	function create_title_box () {
		$title = __('My quick %s post', 'wdqs');
		echo "<input type='text' disabled='disabled' class='widefat' value='{$title}' />";
		echo "<div><small>" . __('This is what will be used as fallback title for your Status posts by default', 'wdqs') . '</small></div>';
		echo "<div><small>" . __('Use <code>%s</code> anywhere in your title to insert the post type (i.e. status, link, image, video)', 'wdqs') . '</small></div>';
	}

	function create_post_format_box () {
		$theme_formats = get_theme_support('post-formats');
		$theme_formats = is_array($theme_formats) ? $theme_formats[0] : array();
		array_unshift($theme_formats, '');
		if (!current_theme_supports('post-formats') || !$theme_formats) {
			_e('<p>Your theme does not support post formats</p>', 'wdqs');
			return;
		}
		$opts = $this->_get_option();

		echo '<label for="post_format-link">' . __('Link post format:', 'wdqs') . '</label> ';
		echo '<select disabled="disabled" id="post_format-link">';
		foreach ($theme_formats as $format) {
			$name = ((int)$format >= 0) ? esc_html(get_post_format_string($format)) : __('Use default format', 'wdqs');
			echo "<option value='{$format}' {$selected}>{$name}</option>";
		}
		echo '</select>';
		echo '<div><small>' . __('This format will be applied to all your &quot;Link&quot; posts.', 'wdqs')  . '</small></div>';

		echo '<label for="post_format-image">' . __('Image post format:', 'wdqs') . '</label> ';
		echo '<select disabled="disabled" id="post_format-image">';
		foreach ($theme_formats as $format) {
			$name = ((int)$format >= 0) ? esc_html(get_post_format_string($format)) : __('Use default format', 'wdqs');
			echo "<option value='{$format}' {$selected}>{$name}</option>";
		}
		echo '</select>';
		echo '<div><small>' . __('This format will be applied to all your &quot;Image&quot; posts.', 'wdqs')  . '</small></div>';

		echo '<label for="post_format-video">' . __('Video post format:', 'wdqs') . '</label> ';
		echo '<select disabled="disabled" id="post_format-video">';
		foreach ($theme_formats as $format) {
			$name = ((int)$format >= 0) ? esc_html(get_post_format_string($format)) : __('Use default format', 'wdqs');
			echo "<option value='{$format}' {$selected}>{$name}</option>";
		}
		echo '</select>';
		echo '<div><small>' . __('This format will be applied to all your &quot;Video&quot; posts.', 'wdqs')  . '</small></div>';

		echo '<label for="post_format-status">' . __('Generic Status post format:', 'wdqs') . '</label> ';
		echo '<select disabled="disabled" id="post_format-status">';
		foreach ($theme_formats as $format) {
			$name = ((int)$format >= 0) ? esc_html(get_post_format_string($format)) : __('Use default format', 'wdqs');
			echo "<option value='{$format}' {$selected}>{$name}</option>";
		}
		echo '</select>';
		echo '<div><small>' . __('This format will be applied to all your generic &quot;Status&quot; posts.', 'wdqs')  . '</small></div>';
	}

}