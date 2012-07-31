<?php

/*
  Modify the query to include filtering by custom_field.
  
*/

add_filter('wpv_filter_query', 'wpv_filter_post_custom_field', 10, 2);  
function wpv_filter_post_custom_field($query, $view_settings) {

	global $WP_Views, $no_parameter_found;

	$meta_keys = $WP_Views->get_meta_keys();

	foreach (array_keys($view_settings) as $key) {
		if (strpos($key, 'custom-field-') === 0 && strpos($key, '_compare') === strlen($key) - strlen('_compare')) {
			$name = substr($key, 0, strlen($key) - strlen('_compare'));
			$name = substr($name, strlen('custom-field-'));
			
			$meta_name = $name;
			if (!in_array($meta_name, $meta_keys)) {
				$meta_name = str_replace('_', ' ', $meta_name);
			}

			$value = $view_settings['custom-field-' . $name . '_value'];
			$value = wpv_apply_user_functions($value);
			
			if ($value != $no_parameter_found) { // Only add if we have found a parameter
				
				$compare_mode = $view_settings['custom-field-' . $name . '_compare'];
				
				if ($compare_mode == 'BETWEEN' || $compare_mode == 'NOT BETWEEN') {
					// we need to make sure we have values for min and max.
					
					$values = explode(',', $value);
					if (count($values) == 0) {
						continue;
					}
					if (count($values) == 1) {
						
						if ($values[0] == $no_parameter_found) {
							// nothing to compare to so ignore
							continue;
						}
						
						// assume this is the smaller value
						
						if ($compare_mode == 'BETWEEN') {
							$compare_mode =  '>=';
						} else {
							$compare_mode =  '<=';
						}
						$value = $values[0];
					} else {
						if ($values[0] == $no_parameter_found && $values[1] == $no_parameter_found) {
							// nothing to compare so ignore
							continue;
						}
						if ($values[0] == $no_parameter_found) {
							// minimum value is missing so use less than compare.
							if ($compare_mode == 'BETWEEN') {
								$compare_mode = '<=';
							} else {
								$compare_mode = '>=';
							}
							$value = $values[1];
						} elseif ($values[1] == $no_parameter_found) {
							// maximum value is missing so use greater than compare.
							if ($compare_mode == 'BETWEEN') {
								$compare_mode = '>=';
							} else {
								$compare_mode = '<=';
							}
							$value = $values[0];
						}  
						
						
					}
					
				}
				
				$value = str_replace($no_parameter_found, '', $value); // just in case we have more than on parameter

				if (!isset($query['meta_query']) && isset($view_settings['custom_fields_relationship'])) {
					$query['meta_query'] = array('relation' => $view_settings['custom_fields_relationship']);
				}
	
				
				$query['meta_query'][] = array('key' => $meta_name,
											  'value' => $value,
											  'type' => $view_settings['custom-field-' . $name . '_type'],
											  'compare' => $compare_mode);
			}			
			
		}
	}

    return $query;
}

function wpv_get_custom_field_view_params($view_settings) {
    $pattern = '/VIEW_PARAM\(([^(]*?)\)/siU';

	$results = array();
	
	foreach (array_keys($view_settings) as $key) {
		if (strpos($key, 'custom-field-') === 0 && strpos($key, '_compare') === strlen($key) - strlen('_compare')) {
			$name = substr($key, 0, strlen($key) - strlen('_compare'));
			$name = substr($name, strlen('custom-field-'));
			
			$value = $view_settings['custom-field-' . $name . '_value'];
			
    
		    if(preg_match_all($pattern, $value, $matches, PREG_SET_ORDER)) {
		        foreach($matches as $match) {
					$results[] = $match[1];
				}
			}
			
		}
	}
	
	return $results;
}

