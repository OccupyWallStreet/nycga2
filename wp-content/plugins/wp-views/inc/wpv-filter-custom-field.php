<?php

if(is_admin()){
	add_action('init', 'wpv_filter_custom_field_init');
	
	function wpv_filter_custom_field_init() {
		global $pagenow;
		
		if($pagenow == 'post.php' || $pagenow == 'post-new.php'){
			add_action('wpv_add_filter_table_row', 'wpv_add_filter_custom_field_table_row', 2, 1);
			add_filter('wpv_add_filters', 'wpv_add_filter_custom_field', 2, 1);
		}
	}
	
    function wpv_add_filter_custom_field($filters) {
		global $WP_Views;
		
		$meta_keys = $WP_Views->get_meta_keys();

		foreach ($meta_keys as $key) {		
			
			$filters['custom-field-' . str_replace(' ', '_', $key)] = array('name' => sprintf(__('Custom field - %s', 'wpv-views'), $key),
										'type' => 'callback',
										'callback' => 'wpv_add_meta_key',
										'args' => array('name' => $key));
		}

		// add the nonce field here.
		wp_nonce_field('wpv_add_custom_field_nonce', 'wpv_add_custom_field_nonce');
		
        return $filters;
    }

    function wpv_add_filter_custom_field_table_row($view_settings) {
		global $view_settings_table_row;

		if (!isset($view_settings['custom_fields_relationship'])) {
			$view_settings['custom_fields_relationship'] = 'OR';
		}
		
		// Find any custom fields
		
		$summary = '';
		$count = 0;
		foreach (array_keys($view_settings) as $key) {
			if (strpos($key, 'custom-field-') === 0 && strpos($key, '_compare') === strlen($key) - strlen('_compare')) {
				$name = substr($key, 0, strlen($key) - strlen('_compare'));

				$td = wpv_get_table_row_ui_post_custom_field($view_settings_table_row, $name, null, null, $view_settings);
				echo '<tr class="wpv_custom_field_edit_row wpv_filter_row wpv_post_type_filter_row wpv_edit_row" id="wpv_filter_row_' . $view_settings_table_row . '" style="background:' . WPV_EDIT_BACKGROUND . '; display:none;">' . $td . '</tr>';
            
				$view_settings_table_row++;
				$count++;
				
				if ($summary != '') {
					if ($view_settings['custom_fields_relationship'] == 'OR') {
						$summary .= __(' OR', 'wpv-views');
					} else {
						$summary .= __(' AND', 'wpv-views');
					}
				}
				
				$summary .= wpv_get_custom_field_summary($name, $view_settings);
					
			}
		}
		
		if ($summary != '') {
			if ($count > 1) {
				echo '<tr class="wpv_custom_field_edit_row wpv_filter_row wpv_post_type_filter_row wpv_edit_row" id="wpv_filter_row_' . $view_settings_table_row . '" style="background:' . WPV_EDIT_BACKGROUND . '; display:none;">';
				wpv_filter_custom_field_relationship_admin($view_settings);			
				echo '</tr>';
			
				$view_settings_table_row++;
			}
			echo '<tr class="wpv_custom_field_edit_row wpv_filter_row wpv_post_type_filter_row wpv_edit_row" id="wpv_filter_row_' . $view_settings_table_row . '" style="background:' . WPV_EDIT_BACKGROUND . '; display:none;"><td></td><td>';
			?>
				<?php
					$filters = wpv_add_filter_custom_field(array());
					wpv_filter_add_filter_admin($view_settings, $filters, 'popup_add_custom_field', 'Add another custom field');
				?>
				<hr />
				<div class="wpv_custom_field_param_missing_ok"><?php echo __('A custom field parameter is missing or incorrect.', 'wpv-views'); ?></div>
				<input class="button-primary" type="button" value="<?php echo __('OK', 'wpv-views'); ?>" name="<?php echo __('OK', 'wpv-views'); ?>" onclick="wpv_show_filter_custom_field_edit_ok()"/>
				<input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="<?php echo __('Cancel', 'wpv-views'); ?>" onclick="wpv_show_filter_custom_field_edit_cancel()"/>
				<span class="wpv-custom-fields-help"><i>
					<?php echo sprintf(__('%sLearn about filtering by custom fields%s', 'wpv-views'),
								   '<a href="' . WPV_FILTER_BY_CUSTOM_FIELD_LINK . '" target="_blank">',
								   ' &raquo;</a>'
								   ); ?>
				</i></span>
				
			<?php
			
			echo '</td></tr>';
		
			$view_settings_table_row++;

			echo '<tr class="wpv_custom_field_show_row wpv_filter_row wpv_post_type_filter_row" id="wpv_filter_row_' . $view_settings_table_row . '"><td><img src="' . WPV_URL . '/res/img/delete-disabled.png" title="' . __('Edit this filters group to delete items', 'wpv-views') . '"></td><td>';
			_e('Select posts with custom fields: ', 'wpv-views');
			echo $summary;
			
			?>
			<br />
			<input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" name="<?php echo __('Edit', 'wpv-views'); ?>" onclick="wpv_show_filter_custom_field_edit()"/>
			<?php
			
			echo '</td></tr>';

			$view_settings_table_row++;
		}

		
    }

	function wpv_filter_custom_field_relationship_admin($view_settings) {
		if (!isset($view_settings['custom_fields_relationship'])) {
			$view_settings['custom_fields_relationship'] = '';
		}
		?>
		
		<td></td>
		<td>
			<fieldset>
				<legend><strong><?php _e('Custom field relationship:', 'wpv-views') ?></strong></legend>            
				<?php _e('Relationship to use when querying with multiple custom fields:', 'wpv-views'); ?>
				<select name="_wpv_settings[custom_fields_relationship]">            
					<option value="OR"><?php _e('OR', 'wpv-views'); ?>&nbsp;</option>
					<?php $selected = $view_settings['custom_fields_relationship']=='AND' ? ' selected="selected"' : ''; ?>
					<option value="AND" <?php echo $selected ?>><?php _e('AND', 'wpv-views'); ?>&nbsp;</option>
				</select>
				
			</fieldset>
		</td>

		<?php
	}
	
	function wpv_ajax_add_custom_field() {
		if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_add_custom_field_nonce')) {
			global $view_settings_table_row;
			
			if (isset($_POST['custom_fields_name'])) {
				$custom_fields = array();
				for($i = 0; $i < sizeof($_POST['custom_fields_name']); $i++) {
					$name = $_POST['custom_fields_name'][$i];
					$custom_fields['custom-field-' . $name . '_compare'] = $_POST['custom_fields_compare'][$i];
					$custom_fields['custom-field-' . $name . '_type'] = $_POST['custom_fields_type'][$i];
					$custom_fields['custom-field-' . $name . '_value'] = $_POST['custom_fields_value'][$i];
				}
				$custom_fields['custom_fields_relationship'] = $_POST['custom_fields_relationship'];
				
				$view_settings_table_row = $_POST['row'];
				
				wpv_add_filter_custom_field_table_row($custom_fields);
			}
		}
		die();
	}
    
}

function wpv_get_table_row_ui_post_custom_field($row, $type, $not_used, $custom_field, $view_settings = array()) {
	$field_name = substr($type, strlen('custom-field-'));
	$args = array('name' => $field_name);
	
	if (sizeof($view_settings) == 0) {
		$view_settings[$type . '_compare'] = $custom_field['compare'];
		$view_settings[$type . '_type'] = $custom_field['type'];
		$view_settings[$type . '_value'] = $custom_field['value'];
	}
	
	ob_start();
	
	?>
	<td>
		<img src="<?php echo WPV_URL; ?>/res/img/delete.png" onclick="on_delete_wpv_filter('<?php echo $row; ?>')" style="cursor: pointer">
	</td>
	<td class="wpv_td_filter">
		<fieldset>
			<legend><strong><?php echo __('Custom field', 'wpv_views') . ' - ' . $field_name; ?>:</strong></legend>
			<?php wpv_add_meta_key($args, $view_settings); ?>
		</fieldset>
	</td>
	
	<?php
	
	$buffer = ob_get_clean();
	
	return $buffer;
}

function wpv_get_custom_field_summary($type, $view_settings = array()) {
	$field_name = substr($type, strlen('custom-field-'));
	$args = array('name' => $field_name);
	
	ob_start();
	
	?>
	<strong><?php echo $field_name . ' ' . $view_settings[$type . '_compare'] . ' ' . $view_settings[$type . '_value']; ?></strong>
	
	<?php
	
	$buffer = ob_get_clean();
	
	return $buffer;
}

function wpv_add_meta_key($args, $view_settings = null) {
	
	$compare = array('=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN');
	$types = array('CHAR', 'NUMERIC', 'BINARY', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED');
	
	if($view_settings === null) {
		$value = '';
		$compare_selected = '';
		$type_selected = '';
		$name = 'custom-field-' . str_replace(' ', '_', $args['name']) . '%s';
		$parts = array($value);
	} else {
		$value = $view_settings['custom-field-' . str_replace(' ', '_', $args['name']) . '_value'];
		
		$value = _wpv_encode_date($value);
		
		$compare_selected = $view_settings['custom-field-' . str_replace(' ', '_', $args['name']) . '_compare'];
		$compare_count = 1;
		$parts = array($value);
		switch($compare_selected) {
			case 'BETWEEN':
			case 'NOT BETWEEN':
				$compare_count = 2;
				$parts = explode(',', $value);
				
				// Make sure we have only 2 items.
				while (count($parts) < 2) {
					$parts[] = '';
				}
				while (count($parts) > 2) {
					array_pop($parts);
				}
				break;
			
			case 'IN':
			case 'NOT IN':
				$parts = explode(',', $value);
				$compare_count = count($parts);
				if ($compare_count < 1) {
					$compare_count = 1;
					$parts = array($value);
				}
				break;
			
		}

		$value = _wpv_unencode_date($value);

		$type_selected = $view_settings['custom-field-' . str_replace(' ', '_', $args['name']) . '_type'];
		$name = '_wpv_settings[custom-field-' . str_replace(' ', '_', $args['name']) . '%s]';
	}

	
	?>


	<div class="meta_key_div" style="margin-left: 20px;">
		<br />
		<?php _e('Comparison function:', 'wpv-views'); ?>
		<select name="<?php echo sprintf($name, '_compare'); ?>" class="wpv_custom_field_compare_select">
			<?php
				foreach($compare as $com) {
					$selected = $compare_selected == $com ? ' selected="selected"' : ''; 
					echo '<option value="'. $com . '" '. $selected . '>' . $com . '&nbsp;</option>';
				}
			?>
		</select>
		<select name="<?php echo sprintf($name, '_type'); ?>">
			<?php
				foreach($types as $type) {
					$selected = $type_selected == $type ? ' selected="selected"' : ''; 
					echo '<option value="'. $type . '" '. $selected . '>' . $type . '&nbsp;</option>';
				}
			?>
		</select>
		<br />
		
		<div class="wpv_custom_field_values">

			<?php // This is where we store the actual value derived from the follow controls ?>
			<input type="hidden" name="<?php echo sprintf($name, '_value'); ?>" value="<?php echo $value; ?>" />

			<?php
			
				for ($i = 0; $i < count($parts); $i++) {
					
					echo '<div class="wpv_custom_field_value_div">';
					
					
					$options = array();
					$options[__('Constant', 'wpv-views') . '&nbsp'] = 'constant';
					$options[__('URL parameter', 'wpv-views') . '&nbsp'] = 'url';
					$options[__('Shortcode attribute', 'wpv-views') . '&nbsp'] = 'attribute';
					$options['NOW&nbsp'] = 'now';
					$options['TODAY&nbsp;'] = 'today';
					$options['FUTURE_DAY&nbsp;'] = 'future_day';
					$options['PAST_DAY&nbsp;'] = 'past_day';
					$options['THIS_MONTH&nbsp;'] = 'this_month';
					$options['FUTURE_MONTH&nbsp;'] = 'future_month';
					$options['PAST_MONTH&nbsp;'] = 'past_month';
					$options['THIS_YEAR&nbsp;'] = 'this_year';
					$options['FUTURE_YEAR&nbsp;'] = 'future_year';
					$options['PAST_YEAR&nbsp;'] = 'past_year';
					$options['SECONDS_FROM_NOW&nbsp;'] = 'seconds_from_now';
					$options['MONTHS_FROM_NOW&nbsp;'] = 'months_from_now';
					$options['YEARS_FROM_NOW&nbsp;'] = 'years_from_now';
					$options['DATE&nbsp;'] = 'date';
		
					$function_value = _wpv_get_custom_filter_function_and_value($parts[$i]);
					
					echo wpv_form_control(array('field' => array(
							'#name' => 'wpv_custom_field_compare_mode-' . $args['name'] . $i ,
							'#type' => 'select',
							'#attributes' => array('style' => '', 'class' => 'wpv_custom_field_compare_mode'),
							'#inline' => true,
							'#options' => $options,
							'#default_value' => $function_value['function'],
					 )));
					
					echo '<input type="text" class="wpv_custom_filter_value_text" value="' . $function_value['value'];
					if ($function_value['text_boxes'] == 1) {
						echo '" />';
					} else {
						echo '" style="display:none;" />';
					}
					
					?>
					<span class="wpv_custom_field_param_missing"><?php echo __('<- Please enter a value here', 'wpv-views'); ?></span>
					<?php
					
					// Add controls for entering the date.
					_wpv_add_date_controls($function_value['function'], $function_value['value']);
					
					// Add a "Remove" button
					echo '<input type="button" class="button-secondary wpv_custom_field_remove_value" value="' . __('Remove', 'wpv-views') . '" ';
					if ($i > 0 && ($compare_selected == 'IN' || $compare_selected == 'NOT IN')) {
						echo ' />';
					} else {
						echo 'style="display:none" />';
					}
					
					echo '</div>';
					
				}
			?>
			<?php $show = ($compare_selected == 'IN' || $compare_selected == 'NOT IN') ? ' ' : 'style="display:none" '; ?>
			<input type="button" class="button-secondary wpv_custom_field_add_value" value="<?php echo __('Add another value', 'wpv-views'); ?>" <?php echo $show; ?>/>
																								  
																								  
		</div>
		

	<?php if ($view_settings == null): ?>		
		<br />
		<span class="wpv-custom-fields-help"><i>
			<?php echo sprintf(__('%sLearn about filtering by custom fields%s', 'wpv-views'),
						   '<a href="' . WPV_FILTER_BY_CUSTOM_FIELD_LINK . '" target="_blank">',
						   ' &raquo;</a>'
						   ); ?>
		</i></span>
	<?php endif; ?>
	
	</div>
	

	<?php
}

function _wpv_add_date_controls($function, $value) {

	global $wp_locale;
	
	if ($function == 'date') {
		$date_parts = explode(',', $value);
		$time_adj = mktime(0, 0, 0, $date_parts[1], $date_parts[0], $date_parts[2]);
	} else {
		$time_adj = current_time('timestamp');
	}
	$jj = gmdate( 'd', $time_adj );
	$mm = gmdate( 'm', $time_adj );
	$aa = gmdate( 'Y', $time_adj );

	echo '<span class="wpv-custom-field-date">' . "\n";
	
	$month = "<select >\n";
	for ( $i = 1; $i < 13; $i = $i +1 ) {
		$monthnum = zeroise($i, 2);
		$month .= "\t\t\t" . '<option value="' . $monthnum . '"';
		if ( $i == $mm )
			$month .= ' selected="selected"';
		$month .= '>' . $monthnum . '-' . $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) . "</option>\n";
	}
	$month .= '</select>';

	$day = '<input type="text" value="' . $jj . '" size="2" maxlength="2" autocomplete="off" />';
	$year = '<input type="text" value="' . $aa . '" size="4" maxlength="4" autocomplete="off" />';
	
	printf(__('%1$s%2$s, %3$s'), $month, $day, $year);
	
	echo '<span class="wpv_custom_field_invalid_date">' . __('<- Please enter a valid date here', 'wpv-views') . '</span>' . "\n";
	
	echo "</span>\n";
}

function _wpv_encode_date($value) {
	if (preg_match_all('/DATE\(([\\d,-]*)\)/', $value, $matches)) {
        foreach($matches[0] as $match) {
			$value = str_replace($match, str_replace(',', '####coma####', $match), $value);
		}		
	}
	
	return $value;
}

function _wpv_unencode_date($value) {
	return str_replace('####coma####', ',', $value);
}

function _wpv_get_custom_filter_function_and_value($value) {
	$trim = trim($value);
	$function = 'constant';
	$return_val = $value;
	$text_boxes = 1;
	
	$singles = array('url' => '/^URL_PARAM\((.*?)\)/',
					 'attribute' => '/^VIEW_PARAM\((.*?)\)/',
					 'future_day' => '/^FUTURE_DAY\((.*?)\)/',
					 'past_day' => '/^PAST_DAY\((.*?)\)/',
					 'future_month' => '/^FUTURE_MONTH\((.*?)\)/',
					 'past_month' => '/^PAST_MONTH\((.*?)\)/',
					 'future_year' => '/^FUTURE_YEAR\((.*?)\)/',
					 'past_year' => '/^PAST_YEAR\((.*?)\)/',
					 'seconds_from_now' => '/^SECONDS_FROM_NOW\((.*?)\)/',
					 'months_from_now' => '/^MONTHS_FROM_NOW\((.*?)\)/',
					 'years_from_now' => '/^YEARS_FROM_NOW\((.*?)\)/',
					 'date' => '/^DATE\((.*?)\)/');
					 
	
	foreach($singles as $code => $pattern) {
		if (preg_match($pattern, $trim, $matches) == 1) {
			$function = $code;
			$return_val = $matches[1];
			break;
		}
	}

	$zeros = array('now' => '/^NOW\((.*?)\)/',
				   'today' => '/^TODAY\((.*?)\)/',
				   'this_month' => '/^THIS_MONTH\((.*?)\)/',
				   'this_year' => '/^THIS_YEAR\((.*?)\)/');

	foreach($zeros as $code => $pattern) {
		if (preg_match($pattern, $trim, $matches) == 1) {
			$function = $code;
			$return_val = '';
			$text_boxes = 0;
			break;
		}
	}
	
	$return_val = str_replace('####coma####', ',', $return_val);

	return array('function' => $function, 'value' => $return_val, 'text_boxes' => $text_boxes);
}

add_filter('wpv_get_table_row_ui_type', 'wpv_get_table_row_ui_type_custom_field');
function wpv_get_table_row_ui_type_custom_field($type) {

	if (strpos($type, 'custom-field-') === 0) {
		return 'post_custom_field';
	}
	
	return $type;
}

add_filter('wpv-view-get-summary', 'wpv_custom_field_summary_filter', 7, 3);

function wpv_custom_field_summary_filter($summary, $post_id, $view_settings) {
	$result = '';
	if(isset($view_settings['query_type']) && $view_settings['query_type'][0] == 'posts') {
		$count = 0;
		foreach (array_keys($view_settings) as $key) {
			if (strpos($key, 'custom-field-') === 0 && strpos($key, '_compare') === strlen($key) - strlen('_compare')) {
				$name = substr($key, 0, strlen($key) - strlen('_compare'));
	
				$count++;
					
				if ($result != '') {
					if (isset($view_settings['custom_fields_relationship']) && $view_settings['custom_fields_relationship'] == 'OR') {
						$result .= __(' OR', 'wpv-views');
					} else {
						$result .= __(' AND', 'wpv-views');
					}
				}
					
				$result .= wpv_get_custom_field_summary($name, $view_settings);
						
			}
		}
	}

	if ($result != '' && $summary != '') {
		$summary .= '<br />';
	}
	$summary .= $result;
	return $summary;
}


function wpv_custom_fields_get_url_params($view_settings) {
	global $WP_Views;

	$pattern = '/URL_PARAM\(([^(]*?)\)/siU';
	$meta_keys = $WP_Views->get_meta_keys();
	
	$results = array();

	foreach (array_keys($view_settings) as $key) {
		if (strpos($key, 'custom-field-') === 0 && strpos($key, '_compare') === strlen($key) - strlen('_compare')) {
			$name = substr($key, 0, strlen($key) - strlen('_compare'));
			$name = substr($name, strlen('custom-field-'));
			
			$meta_name = $name;
			if (!in_array($meta_name, $meta_keys)) {
				$meta_name = str_replace('_', ' ', $meta_name);
			}

			$value = $view_settings['custom-field-' . $name . '_value'];
			
			if(preg_match_all($pattern, $value, $matches, PREG_SET_ORDER)) {
				foreach($matches as $match) {
					$results[] = array('name' => $name, 'param' => $match[1], 'mode' => 'cf');
				}
			}
		}
	}
	
	return $results;
}
