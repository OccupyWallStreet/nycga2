<?php
  

add_filter('post_updated_messages', 'wpv_post_updated_messages_filter', 9999);

function wpv_post_updated_messages_filter($messages) {
	global $post;
	
    $post_type = get_post_type();
	if ($post_type == 'view') {
	  $messages['view'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __('View updated.', 'wpv-views'),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __('View updated.', 'wpv-views'),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __('View restored to revision from %s', 'wpv-views'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __('View published.', 'wpv-views'),
			7 => __('View saved.', 'wpv-views'),
			8 => __('View submitted.', 'wpv-views'),
			9 => sprintf( __('View scheduled for: <strong>%1$s</strong>.', 'wpv-views'),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) ),
			10 => __('View draft updated', 'wpv-views'),
			);
    }
	if ($post_type == 'view-template') {
	  $messages['view-template'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => __('View template updated.', 'wpv-views'),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __('View template updated.', 'wpv-views'),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __('View template restored to revision from %s', 'wpv-views'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => __('View template published.', 'wpv-views'),
			7 => __('View template saved.', 'wpv-views'),
			8 => __('View template submitted.', 'wpv-views'),
			9 => sprintf( __('View template scheduled for: <strong>%1$s</strong>.', 'wpv-views'),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) ),
			10 => __('View template draft updated', 'wpv-views'),
			);
    }
    return $messages;
}

function wpv_render_checkboxes($values, $selected, $name) {
	$checkboxes = '<ul>';
	foreach($values as $value) {

		if (in_array($value, $selected)) {
			$checked = ' checked="checked"';
		} else {
			$checked = '';
		}
		$checkboxes .= '<li><label><input type="checkbox" name="_wpv_settings[' . $name . '][]" value="' . $value . '"' . $checked . ' />&nbsp;' . $value . '</label></li>';
		
	}
	$checkboxes .= '</ul>';
	
	return $checkboxes;
}

function wpv_render_filter_td($row, $id, $name, $summary_function, $selected, $data) {

	$td = '<td><img src="' . WPV_URL . '/res/img/delete.png" onclick="on_delete_wpv_filter(\'' . $row . '\')" style="cursor: pointer">';
	$td .= '<td class="wpv_td_filter">';
	$td .= "<div id=\"wpv-filter-" . $id . "-show\">\n";
	$td .= call_user_func($summary_function, $selected);
	$td .= "</div>\n";
	$td .= "<div id=\"wpv-filter-" . $id . "-edit\" style='background:" . WPV_EDIT_BACKGROUND . ";display:none'>\n";

	$td .= '<fieldset>';
	$td .= '<legend><strong>' . $name . ':</strong></legend>';
	$td .= '<div>' . $data . '</div>';
	$td .= '</fieldset>';
	ob_start();
	?>
		<input class="button-primary" type="button" value="<?php echo __('OK', 'wpv-views'); ?>" name="<?php echo __('OK', 'wpv-views'); ?>" onclick="wpv_show_filter_<?php echo $id; ?>_edit_ok()"/>
		<input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="<?php echo __('Cancel', 'wpv-views'); ?>" onclick="wpv_show_filter_<?php echo $id; ?>_edit_cancel()"/>
	<?php
	$td .= ob_get_clean();
	$td .= '</div></td>';

	return $td;
}