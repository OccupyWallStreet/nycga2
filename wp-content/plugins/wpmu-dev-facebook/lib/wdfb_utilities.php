<?php
/**
 * Misc utilities, helpers and handlers.
 */



/**
 * Helper function for generating the registration fields array.
 */
function wdfb_get_registration_fields_array () {
	global $current_site;
	$data = Wdfb_OptionsRegistry::get_instance();
	$wp_grant_blog = false;
	if (is_multisite()) {
		$reg = get_site_option('registration');
		if ('all' == $reg) $wp_grant_blog = true;
		else if ('user' != $reg) return array();
	} else {
		if (!(int)get_option('users_can_register')) return array();
	}
	$fields = array(
		array("name" => "name"),
		array("name" => "email"),
		array("name" => "first_name"),
		array("name" => "last_name"),
		array("name" => "gender"),
		array("name" => "location"),
		array("name" => "birthday"),
	);
	if ($wp_grant_blog) {
		$fields[] = array(
			'name' => 'blog_title',
			'description' => __('Your blog title', 'wdfb'),
			'type' => 'text',
		);
		$newdomain = is_subdomain_install() 
			? 'youraddress.' . preg_replace('|^www\.|', '', $current_site->domain) 
			: $current_site->domain . $current_site->path . 'youraddress'
		;
		$fields[] = array(
			'name' => 'blog_domain',
			'description' => sprintf(__('Your blog address (%s)', 'wdfb'), $newdomain),
			'type' => 'text',
		);
	}
	if (!$data->get_option('wdfb_connect', 'no_captcha')) {
		$fields[] = array("name" => "captcha");
	}
	return apply_filters('wdfb-registration_fields_array', $fields);
}

/**
 * Helper function for processing registration fields array into a string.
 */
function wdfb_get_registration_fields () {
	$ret = array();
	$fields = wdfb_get_registration_fields_array();
	foreach ($fields as $field) {
		$tmp = array();
		foreach ($field as $key => $value) {
			$tmp[] = "'{$key}':'{$value}'";
		}
		$ret[] = '{' . join(',', $tmp) . '}';
	}
	$ret = '[' . join(',', $ret) . ']';
	return apply_filters('wdfb-registration_fields_string', $ret);
}

/**
 * Helper function for finding out the proper locale.
 */
function wdfb_get_locale () {
	$data = Wdfb_OptionsRegistry::get_instance();
	$locale = $data->get_option('wdfb_api', 'locale');
	return $locale ? $locale : preg_replace('/-/', '_', get_locale());
}

/**
 * Helper function for getting the login redirect URL.
 */
function wdfb_get_login_redirect ($force_admin_redirect=false) {
	$redirect_url = false;
	$data = Wdfb_OptionsRegistry::get_instance();
	$url = $data->get_option('wdfb_connect', 'login_redirect_url');
	if ($url) {
		$base = $data->get_option('wdfb_connect', 'login_redirect_base');
		$base = ('admin_url' == $base) ? 'admin_url' : 'site_url';
		$redirect_url = $base($url);
	} else $redirect_url = defined('BP_VERSION') ? home_url() : $force_admin_redirect ? admin_url() : home_url();

	return apply_filters('wdfb-login-redirect_url', $redirect_url);
}

/**
 * Expands some basic supported user macros.
 */
function wdfb_expand_user_macros ($str) {
	$user = wp_get_current_user();
	$str = preg_replace('/\bUSER_ID\b/', $user->ID, $str);
	$str = preg_replace('/\bUSER_LOGIN\b/', $user->user_login, $str);
	return $str;
}
add_filter('wdfb-login-redirect_url', 'wdfb_expand_user_macros', 1);

/**
 * Expands some basic supported BuddyPress macros.
 */
function wdfb_expand_buddypress_macros ($str) {
	if (!defined('BP_VERSION')) return $str;

	if (function_exists('bp_get_activity_root_slug')) $str = preg_replace('/\bBP_ACTIVITY_SLUG\b/', bp_get_activity_root_slug(), $str);
	if (function_exists('bp_get_groups_slug')) $str = preg_replace('/\bBP_GROUPS_SLUG\b/', bp_get_groups_slug(), $str);
	if (function_exists('bp_get_members_slug')) $str = preg_replace('/\bBP_MEMBERS_SLUG\b/', bp_get_members_slug(), $str);

	return $str;
}
add_filter('wdfb-login-redirect_url', 'wdfb_expand_buddypress_macros', 1);



/**
 * Helper function for fetching the image for OpenGraph info.
 */
function wdfb_get_og_image ($id=false) {
	$data = Wdfb_OptionsRegistry::get_instance();
	$use = $data->get_option('wdfb_opengraph', 'always_use_image');
	if ($use) return apply_filters(
		'wdfb-opengraph-image',
		apply_filters('wdfb-opengraph-image-always_used_image', $use)
	);

	// Try to find featured image
	if (function_exists('get_post_thumbnail_id')) { // BuddyPress :/
		$thumb_id = get_post_thumbnail_id($id);
	} else {
		$thumb_id = false;
	}
	if ($thumb_id) {
		$image = wp_get_attachment_image_src($thumb_id, 'thumbnail');
		if ($image) return apply_filters(
			'wdfb-opengraph-image',
			apply_filters('wdfb-opengraph-image-featured_image', $image[0])
		);
	}

	// If we're still here, post has no featured image.
	// Fetch the first one.
	// Thank you for this fix, grola!
	if ($id) {
		$post = get_post($id);
		$html = $post->post_content;
		if (!function_exists('load_membership_plugins') && !defined('GRUNION_PLUGIN_DIR')) $html = apply_filters('the_content', $html);
	} else if (is_home() && $data->get_option('wdfb_opengraph', 'fallback_image')) {
		return apply_filters(
			'wdfb-opengraph-image',
			apply_filters('wdfb-opengraph-image-fallback_image', $data->get_option('wdfb_opengraph', 'fallback_image'))
		);
	} else {
		$html = get_the_content();
		if (!function_exists('load_membership_plugins')) $html = apply_filters('the_content', $html);
	}
	preg_match_all('/<img .*src=["\']([^ ^"^\']*)["\']/', $html, $matches);
	if (@$matches[1][0]) return apply_filters(
		'wdfb-opengraph-image',
		apply_filters('wdfb-opengraph-image-post_image', $matches[1][0])
	);

	// Post with no images? Pffft.
	// Return whatever we have as fallback.
	return apply_filters(
		'wdfb-opengraph-image',
		apply_filters('wdfb-opengraph-image-fallback_image', $data->get_option('wdfb_opengraph', 'fallback_image'))
	);
}



/**
 * Applying the proper message for registration email notification.
 */
function wdfb_add_registration_filter () {
	add_filter('wdfb-registration_message', 'wdfb_add_email_message');
}
add_action('wdfb-registration_email_sent', 'wdfb_add_registration_filter');

/**
 * Creates a proper registration email notification message.
 */
function wdfb_add_email_message ($msg) {
	return
		apply_filters(
			'wdfb-registration_message-user',
			__('<p>An email with your login credentails has been sent to your email address.</p>', 'wdfb')
		) .
		$msg
	;
}

/**
 * Error registry class for exception transport.
 */
class Wdfb_ErrorRegistry {
	private static $_errors = array();
	
	private function __construct () {}
	
	public static function store ($exception) {
		self::$_errors[] = $exception;
	}
	
	public static function clear () {
		self::$_errors = array();
	}
	
	public static function get_errors () {
		return self::$_errors;
	}
	
	public static function get_last_error () {
		return end(self::$_errors);
	}
	
	public static function get_last_error_message () {
		$e = self::get_last_error();
		return ($e && is_object($e) && $e instanceof Exception) 
			? $e->getMessage()
			: false
		;
	}
}
