<?php

if(!function_exists('em_paginate')){ //overridable e.g. in you mu-plugins folder.
/**
 * Takes a few params and determins a pagination link structure
 * @param string $link
 * @param int $total
 * @param int $limit
 * @param int $page
 * @param int $pagesToShow
 * @return string
 */
function em_paginate($link, $total, $limit, $page=1, $pagesToShow=10){
	if($limit > 0){
		$url_parts = explode('?', $link);
		$base_link = $url_parts[0];
    	//Get querystring for first page without page
    	$query_arr = array();
    	parse_str($url_parts[1], $query_arr);
    	unset($query_arr['page']); unset($query_arr['pno']);
    	$base_querystring = build_query($query_arr);
    	if( !empty($base_querystring) ) $base_querystring = '?'.$base_querystring;
    	//calculate
		$maxPages = ceil($total/$limit); //Total number of pages
		$startPage = ($page <= $pagesToShow) ? 1 : $pagesToShow * (floor($page/$pagesToShow)) ; //Which page to start the pagination links from (in case we're on say page 12 and $pagesToShow is 10 pages)
		$placeholder = urlencode('%PAGE%');
		$link = str_replace('%PAGE%', $placeholder, $link); //To avoid url encoded/non encoded placeholders
	    //Add the back and first buttons
		    $string = ($page>1 && $startPage != 1) ? '<a class="prev page-numbers" href="'.str_replace($placeholder,1,$link).'">&lt;&lt;</a> ' : '';
		    if($page == 2){
		    	$string .= ' <a class="prev page-numbers" href="'.$base_link.$base_querystring.'">&lt;</a> ';
		    }elseif($page > 2){
		    	$string .= ' <a class="prev page-numbers" href="'.str_replace($placeholder,$page-1,$link).'">&lt;</a> ';
		    }
		//Loop each page and create a link or just a bold number if its the current page
		    for ($i = $startPage ; $i < $startPage+$pagesToShow && $i <= $maxPages ; $i++){
	            if($i == $page){
	                $string .= ' <strong><span class="page-numbers current">'.$i.'</span></strong>';
	            }elseif($i=='1'){
	                $string .= ' <a class="page-numbers" href="'.$base_link.$base_querystring.'">'.$i.'</a> ';
	            }else{
	                $string .= ' <a class="page-numbers" href="'.str_replace($placeholder,$i,$link).'">'.$i.'</a> ';
	            }
		    }
		//Add the forward and last buttons
		    $string .= ($page < $maxPages) ? ' <a class="next page-numbers" href="'.str_replace($placeholder,$page+1,$link).'">&gt;</a> ' :' ' ;
		    $string .= ($i-1 < $maxPages) ? ' <a class="next page-numbers" href="'.str_replace($placeholder,$maxPages,$link).'">&gt;&gt;</a> ' : ' ';
		//Return the string
		    return apply_filters('em_paginate', $string);
	}
}
}

/**
 * Creates a wp-admin style navigation.
 * @param string $link
 * @param int $total
 * @param int $limit
 * @param int $page
 * @param int $pagesToShow
 * @return string
 * @uses paginate_links()
 * @uses add_query_arg()
 */
function em_admin_paginate($total, $limit, $page=1, $vars=false, $base = false, $format = ''){
	$return = '<div class="tablenav-pages">';
	$base = !empty($base) ? $base:add_query_arg( 'pno', '%#%' );
	$events_nav = paginate_links( array(
		'base' => $base,
		'format' => $format,
		'total' => ceil($total / $limit),
		'current' => $page,
		'add_args' => $vars
	));
	$return .= sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s', 'dbem') . ' </span>%s',
		number_format_i18n( ( $page - 1 ) * $limit + 1 ),
		number_format_i18n( min( $page * $limit, $total ) ),
		number_format_i18n( $total ),
		$events_nav
	);
	$return .= '</div>';
	return apply_filters('em_admin_paginate',$return,$total,$limit,$page,$vars);
}

/**
 * Takes a url and appends GET params (supplied as an assoc array), it automatically detects if you already have a querystring there
 * @param string $url
 * @param array $params
 * @param bool $html
 * @param bool $encode
 * @return string
 */
function em_add_get_params($url, $params=array(), $html=true, $encode=true){
	//Splig the url up to get the params and the page location
	$url_parts = explode('?', $url);
	$url = $url_parts[0];
	$url_params_dirty = array();
	if(count($url_parts) > 1){
		$url_params_dirty = $url_parts[1];
		//get the get params as an array
		if( !is_array($url_params_dirty) ){
			if( strstr($url_params_dirty, '&amp;') !== false ){
				$url_params_dirty = explode('&amp;', $url_params_dirty);
			}else{
				$url_params_dirty = explode('&', $url_params_dirty);
			}
		}
		//split further into associative array
		$url_params = array();
		foreach($url_params_dirty as $url_param){
			if( !empty($url_param) ){
				$url_param = explode('=', $url_param);
				if(count($url_param) > 1){
					$url_params[$url_param[0]] = $url_param[1];
				}
			}
		}
		//Merge it together
		$params = array_merge($url_params, $params);
	}
	//Now build the array back up.
	$count = 0;
	foreach($params as $key=>$value){
		if( $value !== null ){
			$value = ($encode) ? urlencode($value):$value;
			if( $count == 0 ){
				$url .= "?{$key}=".$value;
			}else{
				$url .= ($html) ? "&amp;{$key}=".$value:"&{$key}=".$value;
			}
			$count++;
		}
	}
	return $url;
}

/**
 * Get a array of countries, translated. Keys are 2 character country iso codes. If you supply a string or array that will be the first value in the array (if array, the array key is the first key in the returned array)
 * @param mixed $add_blank
 * @return array
 */
function em_get_countries($add_blank = false){
	global $em_countries_array;
	if( !is_array($em_countries_array) ){
		$em_countries_array = array ('AF' => 'Afghanistan', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AQ' => 'Antarctica', 'AG' => 'Antigua and Barbuda', 'AR' => 'Argentina', 'AM' => 'Armenia', 'AW' => 'Aruba', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia and Herzegovina', 'BW' => 'Botswana', 'BR' => 'Brazil', 'VG' => 'British Virgin Islands', 'BN' => 'Brunei', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'CI' => 'C&ocirc;te D\'Ivoire', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'KY'=>'Cayman Islands', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CR' => 'Costa Rica', 'HR' => 'Croatia', 'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'KP' => 'Democratic People\'s Republic of Korea', 'CD' => 'Democratic Republic of the Congo', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 'SV' => 'El Salvador', 'XE' => 'England', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'PF' => 'French Polynesia', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GN' => 'Guinea', 'GW' => 'Guinea Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IL' => 'Israel', 'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KV' => 'Kosovo', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Laos', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyan Arab Jamahiriya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macedonia', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Mauritania', 'MU' => 'Mauritius', 'MR' => 'Mauritius', 'MX' => 'Mexico', 'FM' => 'Micronesia', 'MD' => 'Moldova', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar(Burma)', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'AN' => 'Netherlands Antilles', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'XI' => 'Northern Ireland', 'MP' => 'Northern Mariana Islands', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'CG' => 'Republic of the Congo', 'RO' => 'Romania', 'RU' => 'Russia', 'RW' => 'Rwanda', 'ST' => 'S&agrave;o Tom&eacute; And Pr&iacute;ncipe', 'KN' => 'Saint Kitts and Nevis', 'LC' => 'Saint Lucia', 'VC' => 'Saint Vincent and the Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'SA' => 'Saudi Arabia', 'XS' => 'Scotland', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'KR' => 'South Korea', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syria', 'TW' => 'Taiwan', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TO' => 'Tonga', 'TT' => 'Trinidad and Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TV' => 'Tuvalu', 'VI' => 'US Virgin Islands', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 'US' => 'United States', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VA' => 'Vatican', 'VE' => 'Venezuela', 'VN' => 'Vietnam', 'XW' => 'Wales', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe' );
		array_walk($em_countries_array, '__');
	}
	if($add_blank !== false){
		if(is_array($add_blank)){
			$em_countries_array = $add_blank + $em_countries_array;
		}else{
			array_unshift($em_countries_array, $add_blank);
		}
	}
	return apply_filters('em_get_countries', $em_countries_array);
}

/**
 * Returns an array of scopes available to events manager. Hooking into this function's em_get_scopes filter will allow you to add scope options to the event pages.
 */
function em_get_scopes(){
	$scopes = array(
		'all' => __('All events','dbem'),
		'future' => __('Future events','dbem'),
		'past' => __('Past events','dbem'),
		'today' => __('Today\'s events','dbem'),
		'tomorrow' => __('Tomorrow\'s events','dbem'),
		'month' => __('Events this month','dbem'),
		'next-month' => __('Events next month','dbem'),
		'1-months'  => __('Events current and next month','dbem'),
		'2-months'  => __('Events within 2 months','dbem'),
		'3-months'  => __('Events within 3 months','dbem'),
		'6-months'  => __('Events within 6 months','dbem'),
		'12-months' => __('Events within 12 months','dbem')
	);
	return apply_filters('em_get_scopes',$scopes);
}

function em_get_currencies(){
	$currencies = new stdClass();
	$currencies->names = array('EUR' => 'EUR - Euros','USD' => 'USD - U.S. Dollars','GBP' => 'GBP - British Pounds','CAD' => 'CAD - Canadian Dollars','AUD' => 'AUD - Australian Dollars','BRL' => 'BRL - Brazilian Reais','CZK' => 'CZK - Czech Koruny','DKK' => 'DKK - Danish Kroner','HKD' => 'HKD - Hong Kong Dollars','HUF' => 'HUF - Hungarian Forints','ILS' => 'ILS - Israeli New Shekels','JPY' => 'JPY - Japanese Yen','MYR' => 'MYR - Malaysian Ringgit','MXN' => 'MXN - Mexican Pesos','TWD' => 'TWD - New Taiwan Dollars','NZD' => 'NZD - New Zealand Dollars','NOK' => 'NOK - Norwegian Kroner','PHP' => 'PHP - Philippine Pesos','PLN' => 'PLN - Polish Zlotys','SGD' => 'SGD - Singapore Dollars','SEK' => 'SEK - Swedish Kronor','CHF' => 'CHF - Swiss Francs','THB' => 'THB - Thai Baht','TRY' => 'TRY - Turkish Liras');
	$currencies->symbols = array( 'EUR' => '&euro;','USD' => '$','GBP' => '&pound;','CAD' => '$','AUD' => '$','BRL' => 'R$','DKK' => 'kr','HKD' => '$','HUF' => 'Ft','JPY' => '&#165;','MYR' => 'RM','MXN' => '$','TWD' => '$','NZD' => '$','NOK' => 'kr','PHP' => 'Php','SGD' => '$','SEK' => 'kr','CHF' => 'CHF','TRY' => 'TL');
	$currencies->true_symbols = array( 'EUR' => '€','USD' => '$','GBP' => '£','CAD' => '$','AUD' => '$','BRL' => 'R$','DKK' => 'kr','HKD' => '$','HUF' => 'Ft','JPY' => '¥','MYR' => 'RM','MXN' => '$','TWD' => '$','NZD' => '$','NOK' => 'kr','PHP' => 'Php','SGD' => '$','SEK' => 'kr','CHF' => 'CHF','TRY' => 'TL');
	return apply_filters('em_get_currencies',$currencies);
}

function em_get_currency_formatted($price, $currency=false, $format=false){
	$formatted_price = '';
	if(!$format) $format = get_option('dbem_bookings_currency_format','@#');
	if(!$currency) $currency = get_option('dbem_bookings_currency');
	$formatted_price = str_replace('@', em_get_currency_symbol(true,$currency), $format);
	$formatted_price = str_replace('#', number_format( $price, 2, get_option('dbem_bookings_currency_decimal_point','.'), get_option('dbem_bookings_currency_thousands_sep',',') ), $formatted_price);
	return $formatted_price;
}

function em_get_currency_symbol($true_symbol = false, $currency = false){
	if( !$currency ) $currency = get_option('dbem_bookings_currency');
	if($true_symbol){
		return em_get_currencies()->true_symbols[$currency];
	}
	return apply_filters('em_get_currency_symbol', em_get_currencies()->symbols[$currency]);
}

function em_get_currency_name($currency = false){
	if( !$currency ) $currency = get_option('dbem_bookings_currency');
	return apply_filters('em_get_currency_name', em_get_currencies()->names[$currency]);
}

function em_get_hour_format(){
	$locale_code = substr ( get_locale (), 0, 2 );
	$hours_locale_regexp = "H:i";
	// Setting 12 hours format for those countries using it
	if (preg_match ( "/en|sk|zh|us|uk/", $locale_code )){
		$hours_locale_regexp = "h:i A";
	}
	return $hours_locale_regexp;
}

function em_get_date_format(){
	global $localised_date_formats;
	$locale_code = substr ( get_locale (), 0, 2 );
	$localised_date_format = $localised_date_formats[$locale_code];
	return $localised_date_format;
}

function em_get_days_names(){
	return array (1 => __ ( 'Mon' ), 2 => __ ( 'Tue' ), 3 => __ ( 'Wed' ), 4 => __ ( 'Thu' ), 5 => __ ( 'Fri' ), 6 => __ ( 'Sat' ), 0 => __ ( 'Sun' ) );
}

/**
 * Works like check_admin_referrer(), but also in public mode. If in admin mode, it triggers an error like in check_admin_referrer(), if outside admin it just exits with an error.
 * @param string $action
 */
function em_verify_nonce($action, $nonce_name='_wpnonce'){
	if( is_admin() ){
		if( !wp_verify_nonce($_REQUEST[$nonce_name] && $action) ) check_admin_referer('trigger_error');
	}else{
		if( !wp_verify_nonce($_REQUEST[$nonce_name] && $action) ) exit( __('Trying to perform an illegal action.','dbem') );
	}
}

/**
 * Gets all WP users
 * @return array
 */
function em_get_wp_users( $args = array(), $extra_users = array() ) {
	global $wpdb;
	$users = get_users($args);
	$indexed_users = array();
	foreach($users as $user){
		$indexed_users[$user->ID] = $user->display_name;
	}
 	return $extra_users + $indexed_users;
}

function em_get_attributes($lattributes = false){
	//We also get a list of attribute names and create a ddm list (since placeholders are fixed)
	$formats =
		get_option ( 'dbem_placeholders_custom' ).
		get_option ( 'dbem_location_placeholders_custom' ).
		get_option ( 'dbem_event_list_item_format' ).
		get_option ( 'dbem_event_page_title_format' ).
		get_option ( 'dbem_full_calendar_event_format' ).
		get_option ( 'dbem_location_baloon_format' ).
		get_option ( 'dbem_location_event_list_item_format' ).
		get_option ( 'dbem_location_page_title_format' ).
		get_option ( 'dbem_map_text_format' ).
		get_option ( 'dbem_rss_description_format' ).
		get_option ( 'dbem_rss_title_format' ).
		get_option ( 'dbem_single_event_format' ).
		get_option ( 'dbem_single_location_format' );
	//We now have one long string of formats, get all the attribute placeholders
	if( $lattributes ){
		preg_match_all('/#_LATT\{([^}]+)\}(\{([^}]+)\})?/', $formats, $matches);
	}else{
		preg_match_all('/#_ATT\{([^}]+)\}(\{([^}]+)\})?/', $formats, $matches);
	}
	//Now grab all the unique attributes we can use in our event.
	$attributes = array('names'=>array(), 'values'=>array());
	foreach($matches[1] as $key => $attribute) {
		if( !in_array($attribute, $attributes['names']) ){
			$attributes['names'][] = $attribute ;
			//check if there's ddm values
			$attribute_values = array();
			if(strstr($matches[3][$key], '|') !== false){
				$attribute_values = explode('|',$matches[3][$key]);
			}
			$attributes['values'][$attribute] = apply_filters('em_get_attributes_'.$attribute,$attribute_values, $attribute, $matches);
		}
	}
	return apply_filters('em_get_attributes', $attributes, $matches);
}

/**
 * Copied straight from wp-login.php, only change atm is a function renaming.
 * Handles registering a new user.
 *
 * @param array associative array of user values to insert
 * @return int|WP_Error Either user's ID or error on failure.
 */
function em_register_new_user( $user_data ) {
	$user_data = apply_filters('em_register_new_user_pre',$user_data);
	$errors = new WP_Error();
	if( !empty($user_data['user_name']) ){
		$name = explode(' ', $user_data['user_name']);
		$user_data['first_name'] = array_shift($name);
		$user_data['last_name'] = implode(' ',$name);
	}
	$sanitized_user_login = sanitize_user( $user_data['user_login'] );
	$user_data['user_login'] = $sanitized_user_login;
	$user_email = apply_filters( 'user_registration_email', $user_data['user_email'] );

	// Check the username
	if ( $sanitized_user_login == '' ) {
		$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.', 'dbem') );
	} elseif ( ! validate_username( $user_data['user_login'] ) ) {
		$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.', 'dbem') );
		$sanitized_user_login = '';
	} elseif ( username_exists( $sanitized_user_login ) ) {
		$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered, please choose another one.' ) );
	}

	// Check the e-mail address
	if ( $user_email == '' ) {
		$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.', 'dbem') );
	} elseif ( ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.', 'dbem') );
		$user_email = '';
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.' ) );
	}

	do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

	ob_start(); //prevent any errors going out here, e.g. with RPR
	$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );
	ob_clean();

	if ( $errors->get_error_code() )
		return $errors;

	$user_data['user_pass'] = wp_generate_password( 12, false);

	$user_id = wp_insert_user( $user_data );
	if( is_numeric($user_id) && !empty($user_data['dbem_phone']) ){
		update_user_meta($user_id, 'dbem_phone', $user_data['dbem_phone']);
	}

	if ( ! $user_id ) {
		$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !', 'dbem'), get_option( 'admin_email' ) ) );
		return $errors;
	}

	update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.

	em_new_user_notification( $user_id, $user_data['user_pass'] );

	return apply_filters('em_register_new_user',$user_id);
}

/**
 * Notify the blog admin of a new user, normally via email.
 *
 * @since 2.0
 *
 * @param int $user_id User ID
 * @param string $plaintext_pass Optional. The user's plaintext password
 */
function em_new_user_notification($user_id, $plaintext_pass = '') {
	global $LoginWithAjax;

	//if you want you can disable this email from going out, and will still consider registration as successful.
	if( get_option('dbem_email_disable_registration') ){ return true;  }

	//Copied out of /wp-includes/pluggable.php
	$user = new WP_User($user_id);

	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$message  = sprintf(__('New user registration on your blog %s:', 'dbem'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s', 'dbem'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s', 'dbem'), $user_email) . "\r\n";
	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration', 'dbem'), $blogname), $message);

	if ( empty($plaintext_pass) )
		return;

	//
	ob_start();
	em_locate_template('emails/new-user.php', true);
	$message = ob_get_clean();
	$message  = str_replace(array('%password%','%username%'), array($plaintext_pass, $user_login), $message);

	return wp_mail($user_email, sprintf(__('[%s] Your username and password', 'dbem'), $blogname), $message);
}

/*
 * UI Helpers
 * previously dbem_UI_helpers.php functions
 */

function em_option_items($array, $saved_value) {
	$output = "";
	foreach($array as $key => $item) {
		$selected ='';
		if ($key == $saved_value)
			$selected = "selected='selected'";
		$output .= "<option value='".esc_attr($key)."' $selected >".esc_html($item)."</option>\n";

	}
	echo $output;
}

function em_checkbox_items($name, $array, $saved_values, $horizontal = true) {
	$output = "";
	foreach($array as $key => $item) {
		$checked = "";
		if (in_array($key, $saved_values))
			$checked = "checked='checked'";
		$output .=  "<input type='checkbox' name='".esc_attr($name)."' value='".esc_attr($key)."' $checked /> ".esc_html($item);
		if(!$horizontal)
			$output .= "<br/>\n";
	}
	echo $output;

}

function em_options_input_text($title, $name, $description, $default='') {
	?>
	<tr valign="top" id='<?php echo esc_attr($name);?>_row'>
		<th scope="row"><?php echo esc_html($title); ?></th>
	    <td>
			<input name="<?php echo esc_attr($name) ?>" type="text" id="<?php echo esc_attr($title) ?>" style="width: 95%" value="<?php echo esc_attr(get_option($name, $default), ENT_QUOTES); ?>" size="45" /><br />
			<em><?php echo $description; ?></em>
		</td>
	</tr>
	<?php
}
function em_options_input_password($title, $name, $description) {
	?>
	<tr valign="top" id='<?php echo esc_attr($name);?>_row'>
		<th scope="row"><?php echo esc_html($title); ?></th>
	    <td>
			<input name="<?php echo esc_attr($name) ?>" type="password" id="<?php echo esc_attr($title) ?>" style="width: 95%" value="<?php echo esc_attr(get_option($name)); ?>" size="45" /><br />
			<em><?php echo $description; ?></em>
		</td>
	</tr>
	<?php
}

function em_options_textarea($title, $name, $description) {
	?>
	<tr valign="top" id='<?php echo esc_attr($name);?>_row'>
		<th scope="row"><?php echo esc_html($title); ?></th>
			<td>
				<textarea name="<?php echo esc_attr($name) ?>" id="<?php echo esc_attr($name) ?>" rows="6" cols="60"><?php echo esc_attr(get_option($name), ENT_QUOTES);?></textarea><br/>
				<em><?php echo $description; ?></em>
			</td>
		</tr>
	<?php
}

function em_options_radio($name, $options, $title='') {
		$option = get_option($name);
		?>
	   	<tr valign="top" id='<?php echo esc_attr($name);?>_row'>
	   		<?php if( !empty($title) ): ?>
	   		<th scope="row"><?php  echo esc_html($title); ?></th>
	   		<td>
	   		<?php else: ?>
	   		<td colspan="2">
	   		<?php endif; ?>
	   			<table>
	   			<?php foreach($options as $value => $text): ?>
	   				<tr>
	   					<td><input id="<?php echo esc_attr($name) ?>_<?php echo esc_attr($value); ?>" name="<?php echo esc_attr($name) ?>" type="radio" value="<?php echo esc_attr($value); ?>" <?php if($option == $value) echo "checked='checked'"; ?> /></td>
	   					<td><?php echo $text ?></td>
	   				</tr>
				<?php endforeach; ?>
				</table>
			</td>
	   	</tr>
<?php
}

function em_options_radio_binary($title, $name, $description, $option_names = '') {
	if( empty($option_names) ) $option_names = array(0 => __('No','dbem'), 1 => __('Yes','dbem'));
	if( substr($name, 0, 7) == 'dbem_ms' ){
		$list_events_page = get_site_option($name);
	}else{
		$list_events_page = get_option($name);
	}
	?>
   	<tr valign="top" id='<?php echo $name;?>_row'>
   		<th scope="row"><?php echo esc_html($title); ?></th>
   		<td>
   			<?php echo $option_names[1]; ?> <input id="<?php echo esc_attr($name) ?>_yes" name="<?php echo esc_attr($name) ?>" type="radio" value="1" <?php if($list_events_page) echo "checked='checked'"; ?> />&nbsp;&nbsp;&nbsp;
			<?php echo $option_names[0]; ?> <input  id="<?php echo esc_attr($name) ?>_no" name="<?php echo esc_attr($name) ?>" type="radio" value="0" <?php if(!$list_events_page) echo "checked='checked'"; ?> />
			<br/><em><?php echo $description; ?></em>
		</td>
   	</tr>
	<?php
}

function em_options_select($title, $name, $list, $description) {
	$option_value = get_option($name);
	if( $name == 'dbem_events_page' && !is_object(get_page($option_value)) ){
		$option_value = 0; //Special value
	}
	?>
   	<tr valign="top" id='<?php echo esc_attr($name);?>_row'>
   		<th scope="row"><?php echo esc_html($title); ?></th>
   		<td>
			<select name="<?php echo esc_attr($name); ?>" >
				<?php foreach($list as $key => $value) : ?>
 				<option value='<?php echo esc_attr($key) ?>' <?php echo ("$key" == $option_value) ? "selected='selected' " : ''; ?>>
 					<?php echo esc_html($value); ?>
 				</option>
				<?php endforeach; ?>
			</select> <br/>
			<em><?php echo $description; ?></em>
		</td>
   	</tr>
	<?php
}
// got from http://davidwalsh.name/php-email-encode-prevent-spam
function em_ascii_encode($e){
	$output = '';
    for ($i = 0; $i < strlen($e); $i++) { $output .= '&#'.ord($e[$i]).';'; }
    return $output;
}


if( !function_exists('get_current_blog_id') ){
	/**
	 * Substitutes the original function in 3.1 onwards, for backwards compatability (only created if not previously defined)
	 * @return int
	 */
	function get_current_blog_id(){ return 1; } //for < 3.1
}

function em_get_thumbnail_url($image_url, $width, $height){
	return plugins_url('includes/thumbnails/timthumb.php', __FILE__).'?src='.$image_url.'&amp;h='. $height .'&amp;w='. $width;
}
?>