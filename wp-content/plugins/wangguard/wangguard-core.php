<?php
$wangguard_db_version = 1.4;

/********************************************************************/
/*** INIT & INSTALL BEGINS ***/
/********************************************************************/
//Plugin init
function wangguard_init() {
	global $wangguard_api_key , $wangguard_api_port , $wangguard_api_host , $wangguard_rest_path;

	$wangguard_api_key = wangguard_get_option('wangguard_api_key');

	$wangguard_api_host = 'rest.wangguard.com';
	$wangguard_rest_path = '/';

	$wangguard_api_port = 80;

	if (function_exists('load_plugin_textdomain')) {
		$plugin_dir = basename(dirname(__FILE__));
		load_plugin_textdomain('wangguard', false, $plugin_dir . "/languages/" );
	}

	wp_register_style( 'wangguardCSS', "/" . PLUGINDIR . '/wangguard/wangguard.css' );

	wp_enqueue_style('wangguard', "/" . PLUGINDIR . '/wangguard/wangguard.css');

	wp_enqueue_script("jquery");

	wangguard_admin_warnings();
}
add_action('init', 'wangguard_init');



//Admin init function
function wangguard_admin_init() {
	global $wangguard_db_version;
	
	wp_enqueue_style( 'wangguardCSS' );

	wp_enqueue_script("jquery-ui-widget");
	wp_enqueue_script("raphael" , "/" . PLUGINDIR . '/wangguard/js/raphael.js' , array('jquery-ui-widget'));
	wp_enqueue_script("wijmo-wijchartcore" , "/" . PLUGINDIR . '/wangguard/js/jquery.wijmo.wijchartcore.min.js' , array('raphael'));
	wp_enqueue_script("wijmo.wijbarchart" , "/" . PLUGINDIR . '/wangguard/js/jquery.wijmo.wijbarchart.min.js' , array('wijmo-wijchartcore'));
	
	$version = wangguard_get_option("wangguard_db_version");
	if (false === $version)
		$version = get_option("wangguard_db_version");
	
	if (false === $version)
		$version = 0;
	
	//Upgrade DB
	if ($version < $wangguard_db_version)
		wangguard_install ($version);
}
add_action('admin_init', 'wangguard_admin_init');




function wangguard_install($current_version) {
	global $wpdb;
	global $wangguard_db_version;

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$table_name = $wpdb->base_prefix . "wangguardquestions";
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

		$sql = "CREATE TABLE " . $table_name . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			Question VARCHAR(255) NOT NULL,
			Answer VARCHAR(50) NOT NULL,
			RepliedOK INT(11) DEFAULT 0 NOT NULL,
			RepliedWRONG INT(11) DEFAULT 0 NOT NULL,
			UNIQUE KEY id (id)
		);";

		dbDelta($sql);
	}

	$table_name = $wpdb->base_prefix . "wangguarduserstatus";
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

		$sql = "CREATE TABLE " . $table_name . " (
			ID BIGINT(20) NOT NULL,
			user_status VARCHAR(20) NOT NULL,
			user_ip VARCHAR(15) NOT NULL,
			user_proxy_ip VARCHAR(15) NOT NULL,
			UNIQUE KEY ID (ID)
		);";

		dbDelta($sql);
	}



	$table_name = $wpdb->base_prefix . "wangguardreportqueue";
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

		$sql = "CREATE TABLE " . $table_name . " (
			ID BIGINT(20) NULL,
			blog_id BIGINT(20) NULL,
			reported_on TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			reported_by_ID BIGINT(20) NOT NULL,
			KEY reported_by_ID (reported_by_ID),
			KEY ID (ID),
			KEY blog_id (blog_id),
			UNIQUE KEY ID_blog (ID , blog_id)
		);";

		dbDelta($sql);
	}




	$table_name = $wpdb->base_prefix . "wangguardsignupsstatus";
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

		$sql = "CREATE TABLE " . $table_name . " (
			signup_username VARCHAR(60) NOT NULL,
			user_status VARCHAR(20) NOT NULL,
			user_ip VARCHAR(15) NOT NULL,
			user_proxy_ip VARCHAR(15) NOT NULL,
			UNIQUE KEY signup_username (signup_username)
		);";

		dbDelta($sql);
	}



	$table_name = $wpdb->base_prefix . "wangguardoptions";
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
			option_name varchar(64) NOT NULL,
			option_value longtext NOT NULL,			
			UNIQUE KEY option_name (option_name)
		);";

		dbDelta($sql);
	}

	
	if ($current_version < 1.2) {
		//move old options to the new wangguard options table and delete them
		wangguard_update_option("wangguard_stats", get_option("wangguard_stats") );
		wangguard_update_option("wangguard-enable-bp-report-btn", get_option("wangguard-enable-bp-report-btn") );
		wangguard_update_option("wangguard_api_key", get_option("wangguard_api_key") );
		wangguard_update_option("wangguard-report-posts", get_option("wangguard-report-posts") );
		wangguard_update_option("wangguard-expertmode", get_option("wangguard-expertmode") );
		wangguard_update_option("wangguard-enable-bp-report-btn", get_option("wangguard-enable-bp-report-btn") );
		wangguard_update_option("wangguard-enable-bp-report-blog", get_option("wangguard-enable-bp-report-blog") );
		
		delete_option("wangguard_db_version");
		delete_option("wangguard_stats");
		delete_option("wangguard_connectivity_time");
		delete_option("wangguard_available_servers");
		delete_option("wangguard_api_key");
		delete_option("wangguard-report-posts");
		delete_option("wangguard-expertmode");
		delete_option("wangguard-enable-bp-report-btn");
		delete_option("wangguard-enable-bp-report-blog");
	}
	
	if ($current_version < 1.4) {
		$table_name = $wpdb->base_prefix . "wangguarduserstatus";
		$sql = "ALTER TABLE " . $table_name . " ADD user_proxy_ip VARCHAR(15) NOT NULL;";
		@$wpdb->query($sql);


		$table_name = $wpdb->base_prefix . "wangguardsignupsstatus";
		$sql = "ALTER TABLE " . $table_name . " ADD user_proxy_ip VARCHAR(15) NOT NULL;";
		@$wpdb->query($sql);
	}
	
	
	//stats array
	$stats = wangguard_get_option("wangguard_stats");
	if (!is_array($stats)) {
		$stats = array("check"=>0 , "detected"=>0);
		wangguard_update_option("wangguard_stats", $stats);
	}

	
	//Enable BP report button by default
	$tmp = wangguard_get_option("wangguard-enable-bp-report-btn");
	if (empty ($tmp))
	 wangguard_update_option ("wangguard-enable-bp-report-btn", 1);

	//Don't delete users when reporting by default
	$tmp = wangguard_get_option("wangguard-delete-users-on-report");
	if (empty ($tmp))
	 wangguard_update_option ("wangguard-delete-users-on-report", -1);

	//Don't delete users when reporting by default
	$tmp = wangguard_get_option("wangguard-verify-gmail");
	if ($tmp === false)
	 wangguard_update_option ("wangguard-verify-gmail", 1);

	//db version
	wangguard_update_option("wangguard_db_version", $wangguard_db_version);
}
register_activation_hook(__FILE__,'wangguard_install');


//Add the Settings link on the plugins page
function wangguard_action_links( $links, $file ) {
	
	global $wangguard_is_network_admin;
	$urlFunc = "admin_url";
	if ($wangguard_is_network_admin && function_exists("network_admin_url"))
		$urlFunc = "network_admin_url";
	
    if ( $file == plugin_basename(__FILE__) )
		$newlink = array('<a href="' . $urlFunc( 'admin.php?page=wangguard_conf' ) . '">'.esc_html(__('Settings', 'wangguard')).'</a>');
	else
		$newlink = array();

    return array_merge($newlink , $links);
}
add_filter('plugin_action_links', 'wangguard_action_links', 10, 2);
/********************************************************************/
/*** INIT & INSTALL ENDS ***/
/********************************************************************/







/********************************************************************/
/*** HELPER FUNCS BEGINS ***/
/********************************************************************/
/**
 * Returns the client IP
 */
function wangguard_getRemoteIP() {
	return $_SERVER['REMOTE_ADDR'];
}

/**
 * Returns the HTTP_X_FORWARDED_FOR header if present
 */
function wangguard_getRemoteProxyIP() {
	$ipAddress = '';
	if ($_SERVER['HTTP_X_FORWARDED_FOR'] != "" ) {
		$ipAddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
		if (strpos($ipAddress, ',') !== false) {
			$ipAddress = explode(',', $ipAddress);
			$ipAddress = $ipAddress[0];
		}
		
		$test = ip2long($ipAddress);
		if (($test === FALSE) || ($test === -1))
			$ipAddress = '';
	}
	
	return $ipAddress;
}

//Is multisite?
function wangguard_is_multisite() {
	if (function_exists('is_multisite')) {
		return is_multisite();
	}
	else {
		global $wpmu;
		if ($wpmu == 1)
			return true;
		else
			return false;
	}
}

if ( !function_exists('wp_nonce_field') ) {
	function wangguard_nonce_field($action = -1) { return; }
	$wangguard_nonce = -1;
} else {
	function wangguard_nonce_field($action = -1) { return wp_nonce_field($action); }
	$wangguard_nonce = 'wangguard-update-key';
}

//Extracts the domain part from an email address
function wangguard_extract_domain($email) {
	$emailArr = explode("@" , $email);
	if (!is_array($emailArr)) {
		return "";
	}
	else {
		return $emailArr[1];
	}
}


//update the stats
function wangguard_stats_update($action) {
	$stats = wangguard_get_option("wangguard_stats");
	if (!is_array($stats)) {
		$stats = array("check"=>0 , "detected"=>0);
	}
	$stats[$action] = $stats[$action] + 1;
	wangguard_update_option("wangguard_stats", $stats);
}


/**
 * Reports a single email, the function doesn't look into the accounts, do not delete users nor blogs
 * Used in functions which detect (for sure) sploggers that are attempting to create an account
 * @param string $email 
 * @param string $clientIP 
 * @param boolean $isSplogger - send true if the email is a confirmed splogger
 */
function wangguard_report_email($email , $clientIP , $ProxyIP , $isSplogger = false) {
	global $wangguard_api_key;

	//update local stats disregarding the key
	wangguard_stats_update("detected");

	$valid = wangguard_verify_key($wangguard_api_key);
	if ($valid == 'failed') {
		echo "-2";
		return;
	}
	else if ($valid == 'invalid') {
		echo "-1";
		return;
	}
	
	$isSploggerParam = $isSplogger ? "1" : "0";
	
	wangguard_http_post("wg=<in><apikey>$wangguard_api_key</apikey><email>".$email."</email><ip>".$clientIP."</ip><proxyip>".$ProxyIP."</proxyip><issplogger>".$isSploggerParam."</issplogger></in>", 'add-email.php');
}



function wangguard_report_users($wpusersRs , $scope="email" , $deleteUser = true) {
	global $wangguard_api_key;
	global $wpdb;

	$valid = wangguard_verify_key($wangguard_api_key);
	if ($valid == 'failed') {
		echo "-2";
		die();
	}
	else if ($valid == 'invalid') {
		echo "-1";
		die();
	}

	if (!$wpusersRs) {
		return "0";
	}
	
	$deleteUser = wangguard_get_option ("wangguard-delete-users-on-report")=='1';

	$usersFlagged = array();
	foreach ($wpusersRs as $spuserID) {
		$user_object = new WP_User($spuserID);


		if ( !wangguard_is_admin($user_object) ) {
			if (!empty ($user_object->user_email)) {
				//Get the user's client IP from which he signed up
				$table_name = $wpdb->base_prefix . "wangguarduserstatus";
				$clientIP = $wpdb->get_var( $wpdb->prepare("select user_ip from $table_name where ID = %d" , $user_object->ID) );
				$ProxyIP = $wpdb->get_var( $wpdb->prepare("select user_proxy_ip from $table_name where ID = %d" , $user_object->ID) );

				if ($scope == 'domain')
					$response = wangguard_http_post("wg=<in><apikey>$wangguard_api_key</apikey><domain>".wangguard_extract_domain($user_object->user_email)."</domain><ip>".$clientIP."</ip><proxyip>".$ProxyIP."</proxyip></in>", 'add-domain.php');
				elseif ($scope == 'email')
					$response = wangguard_http_post("wg=<in><apikey>$wangguard_api_key</apikey><email>".$user_object->user_email."</email><ip>".$clientIP."</ip><proxyip>".$ProxyIP."</proxyip></in>", 'add-email.php');
			}


			if ($deleteUser && current_user_can( 'delete_users' )) {

				if (function_exists("get_blogs_of_user") && function_exists("update_blog_status")) {

					$blogs = @get_blogs_of_user( $spuserID, true );
					if (is_array($blogs))
						foreach ( (array) $blogs as $key => $details ) {

							$isMainBlog = false;
							if (isset ($current_site)) {
								$isMainBlog = ($details->userblog_id != $current_site->blog_id); // main blog not a spam !
							}
							elseif (defined("BP_ROOT_BLOG")) {
								$isMainBlog = ( 1 == $details->userblog_id || BP_ROOT_BLOG == $details->userblog_id );
							}
							else
								$isMainBlog = ($details->userblog_id == 1);
							
							$userIsAuthor = false;
							if (!$isMainBlog) {
								//Only works on WP 3+
								if (method_exists ($wpdb , 'get_blog_prefix')) {
									$blog_prefix = $wpdb->get_blog_prefix( $details->userblog_id );
									$authorcaps = $wpdb->get_var( sprintf("SELECT meta_value as caps FROM $wpdb->users u, $wpdb->usermeta um WHERE u.ID = %d and u.ID = um.user_id AND meta_key = '{$blog_prefix}capabilities'" , $spuserID ));
									
									$caps = maybe_unserialize( $authorcaps );
									$userIsAuthor = ( !isset( $caps['subscriber'] ) && !isset( $caps['contributor'] ) );
								}
							}
							
							//Update blog to spam if the user is the author and its not the main blog
							if ((!$isMainBlog) && $userIsAuthor) {
								@update_blog_status( $details->userblog_id, 'spam', '1' );
								
								//remove blog from queue
								$table_name = $wpdb->base_prefix . "wangguardreportqueue";
								$wpdb->query( $wpdb->prepare("delete from $table_name where blog_id = '%d'" , $details->userblog_id ) );
							}
						}
				}

				if (wangguard_is_multisite () && function_exists("wpmu_delete_user"))
					wpmu_delete_user($spuserID);
				else
					wp_delete_user($spuserID);
			}
			else {
				global $wpdb;

				//Update the new status
				$table_name = $wpdb->base_prefix . "wangguarduserstatus";
				$wpdb->query( $wpdb->prepare("update $table_name set user_status = 'reported' where ID = '%d'" , $spuserID ) );
			}
			$usersFlagged[] = $spuserID;
		}
	}

	if (count($usersFlagged))
		return implode (",", $usersFlagged);
	else
		return "0";
}



function wangguard_rollback_report($wpusersRs) {
	global $wangguard_api_key;
	global $wpdb;

	$valid = wangguard_verify_key($wangguard_api_key);
	if ($valid == 'failed') {
		echo "-2";
		die();
	}
	else if ($valid == 'invalid') {
		echo "-1";
		die();
	}

	if (!$wpusersRs) {
		return "0";
	}
	
	$usersRolledBack = array();
	foreach ($wpusersRs as $spuserID) {
		$user_object = new WP_User($spuserID);


		if ( !wangguard_is_admin($user_object) ) {
			if (!empty ($user_object->user_email)) {
				//Get the user's client IP from which he signed up
				$response = wangguard_http_post("wg=<in><apikey>$wangguard_api_key</apikey><email>".$user_object->user_email."</email></in>", 'remove-email.php');
			}

			global $wpdb;

			//Update the new status
			$table_name = $wpdb->base_prefix . "wangguarduserstatus";
			$wpdb->query( $wpdb->prepare("update $table_name set user_status = 'force-checked' where ID = '%d'" , $spuserID ) );
			
			$usersRolledBack[] = $spuserID;
		}
	}

	if (count($usersRolledBack))
		return implode (",", $usersRolledBack);
	else
		return "0";
}

function wangguard_is_admin($user_object) {
	return $user_object->has_cap('administrator');
}


/*
 * wangguard_verify_user: takes a WP_User object and checks its status against WangGuard service, possible responses are:
 * 
 * not-checked : user was not checked, admins aren't checked, also replied when a WangGuard server error occurs
 * reported : user is reported on WangGuard
 * checked : user isn't reported on WangGuard
 * error:XXX : WangGuard server replied with an error code (mostly protocol issues)
 * 
 */
function wangguard_verify_user($user_object) {
	global $wpdb;
	global $wangguard_api_key;
	
	$user_check_status = "not-checked";

	//admins doesn't gets checked
	if (wangguard_is_admin($user_object)) return $user_check_status;
	
	wangguard_stats_update("check");

	//Get the user's client IP from which he signed up
	$table_name = $wpdb->base_prefix . "wangguarduserstatus";
	$clientIP = $wpdb->get_var( $wpdb->prepare("select user_ip from $table_name where ID = %d" , $user_object->ID) );
	$ProxyIP = $wpdb->get_var( $wpdb->prepare("select user_proxy_ip from $table_name where ID = %d" , $user_object->ID) );

	//Rechecks the user agains WangGuard service
	$response = wangguard_http_post("wg=<in><apikey>$wangguard_api_key</apikey><email>".$user_object->user_email."</email><ip>".$clientIP."</ip><proxyip>".$ProxyIP."</proxyip></in>", 'query-email.php');
	$responseArr = XML_unserialize($response);
	if ( is_array($responseArr)) {
		if (($responseArr['out']['cod'] == '10') || ($responseArr['out']['cod'] == '11')) {
			$user_check_status = 'reported';
			wangguard_stats_update("detected");
		}
		else {
			if ($responseArr['out']['cod'] == '20') {
				$user_check_status = 'checked';
			}
			else {
				$user_check_status = 'error:'.$responseArr['out']['cod'];
			}
		}
	}

	$table_name = $wpdb->base_prefix . "wangguarduserstatus";
	$tmpIP = $wpdb->get_var( $wpdb->prepare("select user_ip from $table_name where ID = %d" , $user_object->ID) );
	$tmpProxyIP = $wpdb->get_var( $wpdb->prepare("select user_proxy_ip from $table_name where ID = %d" , $user_object->ID) );

	//There may be cases where OUR record for the user isn't there (DB migrations for example or manual inserts) so we just delete and re-insert the user
	$wpdb->query( $wpdb->prepare("delete from $table_name where ID = %d" , $user_object->ID ) );
	$wpdb->query( $wpdb->prepare("insert into $table_name(ID , user_status , user_ip , user_proxy_ip) values (%d , '%s' , '%s' , '%s')" , $user_object->ID , $user_check_status , $tmpIP , $tmpProxyIP ) );
	
	return $user_check_status;
}

//get option from the main blog's options table
function wangguard_get_option($option) {
	global $wpdb;
	
	$table_name = $wpdb->base_prefix . "wangguardoptions";
	
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM {$table_name} WHERE option_name = %s LIMIT 1", $option ) );
	
	if ( is_object( $row ) )
		return maybe_unserialize($row->option_value);
	else
		return false;
}
//update option from the main blog's options table
function wangguard_update_option($option , $newvalue) {
	global $wpdb;

	$table_name = $wpdb->base_prefix . "wangguardoptions";

	$oldvalue = wangguard_get_option( $option );
	
	$newvalue = sanitize_option( $option, $newvalue );
	$newvalue = maybe_serialize( $newvalue );
	if ( false === $oldvalue ) {
		//option wasn't present on the options table, add it
		$result = $wpdb->query( $wpdb->prepare( "INSERT INTO `{$table_name}` (`option_name`, `option_value`) VALUES ( %s  ,  %s )", $option, $newvalue ) );
	}
	else {
		$result = $wpdb->update( $table_name, array( 'option_value' => $newvalue ), array( 'option_name' => $option ) );
	
	}
	
	
	
	if ( $result ) 
		return true;
	else
		return false;
}



// getmxrr() support for Windows by HM2K <php [spat] hm2k.org>
function win_getmxrr($hostname, &$mxhosts, &$mxweight=false) {
	//clean the array
    $mxhosts = array();
    if (empty($hostname)) return;
    $exec='nslookup -type=MX '.escapeshellarg($hostname);
    @exec($exec, $output);
    if (empty($output)) return;
    $i=-1;
    foreach ($output as $line) {
        $i++;
        if (preg_match("/^$hostname\tMX preference = ([0-9]+), mail exchanger = (.+)$/i", $line, $parts)) {
          $mxweight[$i] = trim($parts[1]);
          $mxhosts[$i] = trim($parts[2]);
        }
        if (preg_match('/responsible mail addr = (.+)$/i', $line, $parts)) {
          $mxweight[$i] = $i;
          $mxhosts[$i] = trim($parts[1]);
        }
    }
    return ($i!=-1);
}

if (  (!function_exists('getmxrr')) && (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')  ) {
	//define alt getmxrr on windows server
    function getmxrr($hostname, &$mxhosts, &$mxweight=false) {
        return win_getmxrr($hostname, $mxhosts, $mxweight);
    }
}
/********************************************************************/
/*** HELPER FUNCS ENDS ***/
/********************************************************************/






/********************************************************************/
/*** KEY FUNCS BEGINS ***/
/********************************************************************/
//Return WangGuard stored API KEY
function wangguard_get_key() {
	global $wangguard_api_key;
	if ( !empty($wangguard_api_key) )
		return $wangguard_api_key;
	return wangguard_get_option('wangguard_api_key');
}

//Checks the API KEY against wangguard service
function wangguard_verify_key( $key, $ip = null ) {
	global $wangguard_api_key;
	if ( empty($key) && $wangguard_api_key )
		$key = $wangguard_api_key;

	$response = wangguard_http_post("wg=<in><apikey>$key</apikey></in>", 'verify-key.php' , $ip);


	$responseArr = XML_unserialize($response);

	if ( !is_array($responseArr))
		return 'failed';
	elseif ($responseArr['out']['cod'] != '0')
		return 'invalid';
	else
		return "valid";
}
/********************************************************************/
/*** KEY FUNCS ENDS ***/
/********************************************************************/














/********************************************************************/
/*** NETWORKING FUNCTIONS BEGINS ***/
/********************************************************************/

// Check connectivity between the WordPress blog and wangguard's servers.
// Returns an associative array of server IP addresses, where the key is the IP address, and value is true (available) or false (unable to connect).
function wangguard_check_server_connectivity() {
	global $wangguard_api_host;

	// Some web hosts may disable one or both functions
	if ( !function_exists('fsockopen') || !function_exists('gethostbynamel') )
		return array();

	$ips = gethostbynamel($wangguard_api_host);
	if ( !$ips || !is_array($ips) || !count($ips) )
		return array();

	$servers = array();
	foreach ( $ips as $ip ) {
		$response = wangguard_verify_key( wangguard_get_key(), $ip );
		// even if the key is invalid, at least we know we have connectivity
		if ( $response == 'valid' || $response == 'invalid' )
			$servers[$ip] = true;
		else
			$servers[$ip] = false;
	}

	return $servers;
}


// Check the server connectivity and store the results in an option.
// Cached results will be used if not older than the specified timeout in seconds; use $cache_timeout = 0 to force an update.
// Returns the same associative array as wangguard_check_server_connectivity()
function wangguard_get_server_connectivity( $cache_timeout = 86400 ) {
	$servers = wangguard_get_option('wangguard_available_servers');
	if (empty ($servers)) 
		$servers = false;
	if ( (time() - wangguard_get_option('wangguard_connectivity_time') < $cache_timeout) && $servers !== false )
		return $servers;

	// There's a race condition here but the effect is harmless.
	$servers = wangguard_check_server_connectivity();
	wangguard_update_option('wangguard_available_servers', $servers);
	wangguard_update_option('wangguard_connectivity_time', time());
	return $servers;
}

// Returns true if server connectivity was OK at the last check, false if there was a problem that needs to be fixed.
function wangguard_server_connectivity_ok() {
	// skip the check on WPMU because the status page is hidden
	global $wangguard_api_key;
	if ( $wangguard_api_key )
		return true;
	$servers = wangguard_get_server_connectivity();
	return !( empty($servers) || !count($servers) || count( array_filter($servers) ) < count($servers) );
}


function wangguard_get_host($host) {
	// if all servers are accessible, just return the host name.
	// if not, return an IP that was known to be accessible at the last check.
	if ( wangguard_server_connectivity_ok() ) {
		return $host;
	} else {
		$ips = wangguard_get_server_connectivity();
		// a firewall may be blocking access to some wangguard IPs
		if ( count($ips) > 0 && count(array_filter($ips)) < count($ips) ) {
			// use DNS to get current IPs, but exclude any known to be unreachable
			$dns = (array)gethostbynamel( rtrim($host, '.') . '.' );
			$dns = array_filter($dns);
			foreach ( $dns as $ip ) {
				if ( array_key_exists( $ip, $ips ) && empty( $ips[$ip] ) )
					unset($dns[$ip]);
			}
			// return a random IP from those available
			if ( count($dns) )
				return $dns[ array_rand($dns) ];

		}
	}
	// if all else fails try the host name
	return $host;
}


// Returns the server's response body
function wangguard_http_post($request, $op , $ip=null) {
	global $wp_version;
	global $wangguard_api_port , $wangguard_api_host , $wangguard_rest_path;

	$wangguard_version = constant('WANGGUARD_VERSION');

	$http_request  = "POST {$wangguard_rest_path}{$op} HTTP/1.0\r\n";
	$http_request .= "Host: $wangguard_api_host\r\n";
	$http_request .= "Content-Type: application/x-www-form-urlencoded; charset=" . get_option('blog_charset') . "\r\n";
	$http_request .= "Content-Length: " . strlen($request) . "\r\n";
	$http_request .= "User-Agent: WordPress/$wp_version | WangGuard/$wangguard_version\r\n";
	$http_request .= "\r\n";
	$http_request .= $request;

	if (!empty ($ip))
		$http_host = $ip;
	else
		$http_host = wangguard_get_host($wangguard_api_host);

	//Init response buffer
	$response = '';


	/*fsock connection*/
	if( false != ( $fs = @fsockopen($http_host, $wangguard_api_port, $errno, $errstr, 5) ) ) {
		fwrite($fs, $http_request);

		while ( !feof($fs) )
			$response .= fgets($fs, 1100);
		fclose($fs);
	}
	/*fsock connection*/

	$response = str_replace("\r", "", $response);
	$response = substr($response, strpos($response, "\n\n")+2);

	return $response;
}
/********************************************************************/
/*** NETWORKING FUNCTIONS END ***/
/********************************************************************/









/********************************************************************/
/*** NOTICES & RIGHT NOW BEGINS ***/
/********************************************************************/
//Shows admin warnings if any
function wangguard_admin_warnings() {
	global $wangguard_api_key , $wangguard_is_network_admin;
	
	
	if ( !$wangguard_api_key && !isset($_POST['submit']) ) {
		function wangguard_warning() {

			global $wangguard_is_network_admin;
			
			$urlFunc = "admin_url";
			if ($wangguard_is_network_admin && function_exists("network_admin_url"))
				$urlFunc = "network_admin_url";

			$confURL = $urlFunc("admin.php?page=wangguard_conf");
			
			echo "
			<div id='wangguard-warning' class='updated fade'><p><strong>".__('WangGuard is almost ready.', 'wangguard')."</strong> ".sprintf(__('You must <a href="%1$s">enter your WangGuard API key</a> for it to work.', 'wangguard'), $confURL)."</p></div>
			";
		}
		add_action('admin_notices', 'wangguard_warning');
		return;
	} elseif ( wangguard_get_option('wangguard_connectivity_time') && empty($_POST) && is_admin() && !wangguard_server_connectivity_ok() ) {
		function wangguard_warning() {
			
			global $wangguard_is_network_admin;
			
			$urlFunc = "admin_url";
			if ($wangguard_is_network_admin && function_exists("network_admin_url"))
				$urlFunc = "network_admin_url";

			$confURL = $urlFunc("admin.php?page=wangguard_conf");

			echo "
			<div id='wangguard-warning' class='updated fade'><p><strong>".__('WangGuard has detected a problem.', 'wangguard')."</strong> ".sprintf(__('A server or network problem is preventing WangGuard from working correctly.  <a href="%1$s">Click here for more information</a> about how to fix the problem.', 'wangguard'), $confURL)."</p></div>
			";
		}
		add_action('admin_notices', 'wangguard_warning');
		return;
	}
}





/**
 * Show plugin changes
 *
 * @return void
 */

function wangguard_plugin_update_message() {

	
	$args = array(
		'method' => 'GET'
	);
	$response = wp_remote_request(WANGGUARD_README_URL, $args);;
	
	
	if (!is_wp_error($response) && $response['response']['code'] == 200) {
		$matches = null;
		$regexp = '~==\s*Changelog\s*==\s*=\s*[^\n]+\s*=(.*)(=\s*' . preg_quote(WANGGUARD_VERSION) . ')~Uis';
		
		if (preg_match($regexp, $response['body'], $matches)) {
			
			$changelog = (array) preg_split('~[\r\n]+~', trim($matches[1]));
			
			
			$path = dirname( __FILE__ );
			$path = ltrim( str_replace( '\\', '/', str_replace( rtrim( ABSPATH, '\\\/' ), '', $path ) ), '\\\/' );
			$path = site_url() . '/' . $path;
			
			echo '<div style="margin-top:5px">';
			echo '<span style="color: #a00000; font-weight:bold;"><img src="'.$path.'/newver.jpg" style="vertical-align:middle;margin-right:3px"/> '.__('These are the improvements of the new version', 'wangguard').':</span>';
			$ul = false;

			foreach ($changelog as $index => $line) {
				if (preg_match('~^\s*\*\s*~', $line)) {
					if (!$ul) {
						echo '<ul style="list-style: disc; margin-left: 20px; font-weight:normal; margin-top:5px">';
						$ul = true;
					}
					$line = preg_replace('~^\s*\*\s*~', '', htmlspecialchars($line));
					echo '<li>' . $line . '</li>';
				} else {
					if ($ul) {
						echo '</ul><div style="clear: left;"></div>';
						$ul = false;
					}
					echo '<p style="margin: 5px 0;">' . htmlspecialchars($line) . '</p>';
				}
			}

			if ($ul) {
				echo '</ul>';
			}

			echo '</div>';
		}
	}
}
add_action('in_plugin_update_message-' . WANGGUARD_PLUGIN_FILE, 'wangguard_plugin_update_message');


//dashboard right now activity
function wangguard_rightnow() {

	if (function_exists('is_multisite')) {
		if ((is_multisite() && !is_super_admin()) || (!current_user_can('level_10')))
			return;
	}
	else {
		if (!current_user_can('level_10'))
			return;
	}
	
	$stats = wangguard_get_option("wangguard_stats");
	if (!is_array($stats)) {
		$stats = array("check"=>0 , "detected"=>0);
	}

	$rightnow = sprintf(__('WangGuard has checked %d users, and detected %d Sploggers.' , 'wangguard') , $stats['check'] , $stats['detected']);

	echo "<p class='wangguard-right-now'>$rightnow</p>\n";
}
add_action('rightnow_end', 'wangguard_rightnow');
/********************************************************************/
/*** NOTICES & RIGHT NOW ENDS ***/
/********************************************************************/











/********************************************************************/
/*** QUEUE COLUMNS DEF BEGINS ***/
/********************************************************************/
function wangguard_page_wangguard_queue_headers($v) {
	return array(
			'cb'			=> '<input type="checkbox" />',
			'username'		=> __( 'Username' ),
			'wgtype'		=> __( 'Type' , "wangguard" ),
			'email'			=> __( 'E-mail' ),
			'wgreported_by' => __( 'Reported by' , 'wangguard' ),
			'wgreported_on' => __( 'Reported on' , 'wangguard' ),
			'wgstatus' => __( 'WangGuard Status' , 'wangguard' )
		);
}
add_filter("manage_wangguard_page_wangguard_queue_columns", "wangguard_page_wangguard_queue_headers" );
add_filter("manage_wangguard_page_wangguard_queue-network_columns", "wangguard_page_wangguard_queue_headers" );
/********************************************************************/
/*** QUEUE COLUMNS DEF ENDS ***/
/********************************************************************/




/********************************************************************/
/*** USER SCREEN ACTION & COLS BEGINS ***/
/********************************************************************/
//Add the WangGuard status column
function wangguard_add_status_column($columns) {
	$columns['wangguardstatus'] = __("WangGuard Status", 'wangguard');
	return $columns;
}
function wangguard_wpmu_custom_columns($column_name , $userid) {
	wangguard_user_custom_columns('' , $column_name , $userid , true);
}
function wangguard_user_custom_columns($dummy , $column_name , $userid , $echo = false ) {
	global $wpdb;

	$html = "";

	if ($column_name == 'wangguardstatus' ) {
		$table_name = $wpdb->base_prefix . "wangguarduserstatus";
		$status = $wpdb->get_var( $wpdb->prepare("select user_status from $table_name where ID = %d" , $userid) );

		if (empty ($status)) {
			$html = '<span class="wangguard-status-no-status wangguardstatus-'.$userid.'">'. __('No status', 'wangguard') .'</span>';
		}
		elseif ($status == 'not-checked') {
			$html = '<span class="wangguard-status-not-checked wangguardstatus-'.$userid.'">'. __('Not checked', 'wangguard') .'</span>';
		}
		elseif ($status == 'reported') {
			$html = '<span class="wangguard-status-splogguer wangguardstatus-'.$userid.'">'. __('Reported as Splogger', 'wangguard') .'</span>';
		}
		elseif ($status == 'autorep') {
			$html = '<span class="wangguard-status-splogguer wangguardstatus-'.$userid.'">'. __('Automatically reported as Splogger', 'wangguard') .'</span>';
		}
		elseif ($status == 'checked') {
			$html = '<span class="wangguard-status-checked wangguardstatus-'.$userid.'">'. __('Checked', 'wangguard') .'</span>';
		}
		elseif ($status == 'force-checked') {
			$html = '<span class="wangguard-status-checked wangguardstatus-'.$userid.'">'. __('Checked (forced)', 'wangguard') .'</span>';
		}
		elseif (substr($status , 0 , 5) == 'error') {
			$html = '<span class="wangguard-status-error wangguardstatus-'.$userid.'">'. __('Error', 'wangguard') . " - " . substr($status , 6) . '</span>';
		}
		else {
			$html = '<span class="wangguardstatus-'.$userid.'">'. $status . '</span>';
		}

		$user_object = new WP_User($userid);

		$Domain = explode("@",$user_object->user_email);
		$Domain = $Domain[1];

		$deleteUser = wangguard_get_option ("wangguard-delete-users-on-report")=='1';

		$html .= "<br/><div class=\"row-actions\">";
		if ( !wangguard_is_admin($user_object) ) {
			
			$rollbackStyle = (($status == 'reported') || ($status == 'autorep')) ? "" : "style='display:none'";
			$reportStyle = (($status == 'reported') || ($status == 'autorep')) ? "style='display:none'" : "";

			$html .= '<a href="javascript:void(0)" '.$rollbackStyle.' rel="'.$user_object->ID.'" class="wangguard-rollback">'.esc_html(__('Not a Splogger', 'wangguard')).'</a>';

			if (($deleteUser && current_user_can( 'delete_users' )) || !$deleteUser)
				$html .= '<a href="javascript:void(0)" '.$reportStyle.' rel="'.$user_object->ID.'" class="wangguard-splogger">'.esc_html(__('Splogger', 'wangguard')).'</a>';
			
			$html .= " | ";
			
			//$html .= '<a href="javascript:void(0)" rel="'.$user_object->ID.'" class="wangguard-domain">'.esc_html(__('Report Domain', 'wangguard')).'</a> | ';
			$html .= '<a href="javascript:void(0)" rel="'.$user_object->ID.'" class="wangguard-recheck">'.esc_html(__('Recheck', 'wangguard')).'</a> | ';
			$html .= '<a href="http://'.$Domain.'" target="_new">'.esc_html(__('Open Web', 'wangguard')).'</a>';
		}
		$html .= "</div>";
		
		if ($echo)
			echo $html;
		else
			return $html;
		
   	}
	else {
		return $dummy;
	}

}
add_filter('manage_users_columns', 'wangguard_add_status_column');
add_filter('wpmu_users_columns', 'wangguard_add_status_column');

//If called from ms-admin, call wpmu handler (2 params), else the 3 params func
if (($wuangguard_parent == 'ms-users.php') || ($wuangguard_parent == 'wpmu-users.php'))
	add_action('manage_users_custom_column', 'wangguard_wpmu_custom_columns', 10, 2);
else
	add_action('manage_users_custom_column', 'wangguard_user_custom_columns', 10, 3);
/********************************************************************/
/*** USER SCREEN ACTION & COLS ENDS ***/
/********************************************************************/




/********************************************************************/
/*** POSTS SCREEN ACTION & COLS BEGINS ***/
/********************************************************************/
if (wangguard_get_option ("wangguard-report-posts")==1)
	add_filter('post_row_actions','wangguard_post_row_actions',10,2);
function wangguard_post_row_actions($actions , $post) {
	$user_object = new WP_User($post->post_author);
	
	$deleteUser = wangguard_get_option ("wangguard-delete-users-on-report")=='1';
	
	if ( ((current_user_can( 'delete_users' ) && $deleteUser) || !$deleteUser) && !wangguard_is_admin($user_object) )
		$actions[] = '<a href="javascript:void(0)" rel="'.$post->post_author.'" class="wangguard-splogger">'.esc_html(__('Splogger', 'wangguard')).'</a>';
	return $actions;
}
/********************************************************************/
/*** POSTS SCREEN ACTION & COLS ENDS ***/
/********************************************************************/


?>