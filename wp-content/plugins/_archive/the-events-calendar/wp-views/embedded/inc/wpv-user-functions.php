<?php

include_once WPV_PATH_EMBEDDED . '/common/wpv-filter-date-embedded.php';

$no_parameter_found = 'WPV_NO_PARAM_FOUND';

function wpv_apply_user_functions($value) {
    $value = wpv_apply_user_function_url_param($value);
    $value = wpv_apply_user_function_view_param($value);
    $value = wpv_apply_user_function_date_compare($value);
    
    return $value;
}

function wpv_apply_user_function_url_param($value) {
    global $no_parameter_found;
    
    $pattern = '/URL_PARAM\(([^(]*?)\)/siU';
    
    if(preg_match_all($pattern, $value, $matches, PREG_SET_ORDER)) {
        foreach($matches as $match) {
            if (isset($_GET[$match[1]])) {
				$url_param = $_GET[$match[1]];
				if (is_array($url_param)) {
					$url_param = implode(',', $url_param);
				}
				if ($url_param == '') {
					// an empty parameter should be ignored.
					// eg. my-site.com/price_search/?beds=2&price=
	                $url_param = $no_parameter_found;
				}
            } else {
                $url_param = $no_parameter_found;
            }
            $search = $match[0];
            $value = str_replace($search, $url_param, $value);
        }
        
    }
    
    return $value;
}

function wpv_apply_user_function_view_param($value) {
    global $WP_Views;
    global $no_parameter_found;
    
    $pattern = '/VIEW_PARAM\(([^(]*?)\)/siU';
    
    if(preg_match_all($pattern, $value, $matches, PREG_SET_ORDER)) {
        foreach($matches as $match) {
            $view_attr = $WP_Views->get_view_shortcodes_attributes();
            
            if (isset($view_attr[$match[1]])) {
				if ($view_attr[$match[1]] != '') {
					$view_param = $view_attr[$match[1]];
				} else {
					// an empty parameter should be ignored.
					// eg. [wpv-view name="my-view" beds="2" price=""]
	                $view_param = $no_parameter_found;
				}
            } else {
                $view_param = $no_parameter_found;
            }
            $search = $match[0];
            $value = str_replace($search, $view_param, $value);
        }
        
    }
    
    return $value;
}

function wpv_apply_user_function_date_compare($value) {
	$parsed = wpv_filter_parse_date($value);
	if($parsed) {
		$value = $parsed;
	}
	
	return $value;
}
