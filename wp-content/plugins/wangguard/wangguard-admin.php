<?php
/*
Plugin Name: WangGuard
Plugin URI: http://www.wangguard.com
Description: <strong>Stop Sploggers</strong>. It is very important to use <a href="http://www.wangguard.com" target="_new">WangGuard</a> at least for a week, reporting your site's unwanted users as sploggers from the Users panel. WangGuard will learn at that time to protect your site from sploggers in a much more effective way. WangGuard protects each web site in a personalized way using information provided by Administrators who report sploggers world-wide, that's why it's very important that you report your sploggers to WangGuard. The longer you use WangGuard, the more effective it will become.
Version: 1.3.2
Author: WangGuard
Author URI: http://www.wangguard.com
License: GPL2
*/

/*  Copyright 2010  WangGuard (email : info@wangguard.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('WANGGUARD_VERSION', '1.3.2');
define('WANGGUARD_PLUGIN_FILE', 'wangguard/wangguard-admin.php');
define('WANGGUARD_README_URL', 'http://plugins.trac.wordpress.org/browser/wangguard/trunk/readme.txt?format=txt');

//error_reporting(E_ALL);
//ini_set("display_errors", 1);

//Which file are we are getting called from?
$wuangguard_parent = basename($_SERVER['SCRIPT_NAME']);


$wangguard_is_network_admin = function_exists("is_multisite") && function_exists( 'is_network_admin' );
if ($wangguard_is_network_admin)
	$wangguard_is_network_admin = is_multisite();


include_once 'wangguard-xml.php';
include_once 'wangguard-core.php';

$wangguard_api_key = wangguard_get_option('wangguard_api_key');





/********************************************************************/
/*** CONFIG BEGINS ***/
/********************************************************************/
include_once 'wangguard-conf.php';
include_once 'wangguard-queue.php';
include_once 'wangguard-wizard.php';
include_once 'wangguard-stats.php';
/********************************************************************/
/*** CONFIG ENDS ***/
/********************************************************************/

















/********************************************************************/
/*** ADD & VALIDATE SECURITY QUESTIONS ON REGISTER BEGINS ***/
/********************************************************************/

// for wp regular
add_action('register_form','wangguard_add_hfield_1' , rand(1,10));
add_action('register_form','wangguard_add_hfield_2' , rand(1,10));
add_action('register_form','wangguard_add_hfield_3' , rand(1,10));
add_action('register_form','wangguard_add_hfield_4' , rand(1,10));
add_action('register_form','wangguard_register_add_question');
add_action('register_post','wangguard_signup_validate',10,3);


$wangguard_add_mu_filter_actions = true;
if (defined('BP_VERSION')) {
	if (version_compare(BP_VERSION, '1.1') >= 0) {
		$wangguard_add_mu_filter_actions = false;
		$wangguard_bp_hook = "bp_after_account_details_fields";

		// for buddypress 1.1 only
		add_action($wangguard_bp_hook,'wangguard_add_hfield_1' , rand(1,10));
		add_action($wangguard_bp_hook,'wangguard_add_hfield_2' , rand(1,10));
		add_action($wangguard_bp_hook,'wangguard_add_hfield_3' , rand(1,10));
		add_action($wangguard_bp_hook,'wangguard_add_hfield_4' , rand(1,10));
		add_action('bp_before_registration_submit_buttons', 'wangguard_register_add_question_bp11');
		add_action('bp_signup_validate', 'wangguard_signup_validate_bp11' );
	}
}

if ($wangguard_add_mu_filter_actions) {
	// for wpmu and (buddypress versions before 1.1)
	add_action('signup_extra_fields','wangguard_add_hfield_1' , rand(1,10));
	add_action('signup_extra_fields','wangguard_add_hfield_2' , rand(1,10));
	add_action('signup_extra_fields','wangguard_add_hfield_3' , rand(1,10));
	add_action('signup_extra_fields','wangguard_add_hfield_4' , rand(1,10));
	add_action('signup_extra_fields', 'wangguard_register_add_question_mu' );
	add_filter('wpmu_validate_user_signup', 'wangguard_wpmu_signup_validate_mu');
}




/**
 * Checks MX record for an email domain's
 * 
 * @param type $email
 * @return boolean 
 */
function wangguard_mx_record_is_ok($email) {
	//checks if an associated MX record is found on the server's DNS for the email domain
	
	//option is activated and getmxrr() function exists?
	$wangguard_mx_ok = function_exists('getmxrr');
	if ( !$wangguard_mx_ok || wangguard_get_option("wangguard-verify-dns-mx")!='1')
		return true;
	

	$email = explode("@" , $email);

	if( count($email) != 2 )
		return true;
	
	
	$mxr = array();
	$ret = getmxrr($email[1] , $mxr);
	
	return $ret && count($mxr);
}

/**
 * Cleans username from an email address
 */
function wangguard_get_clean_gmail_username($email) {
	//Cleans dots and + from gmail.com and googlemail.com addresses, lowercases the username and returns it. Returns false otherwise.
	
	$email = explode("@" , $email);

	if( count($email) != 2 )
		return false;
	
	$email[1] = strtolower($email[1]);

	if ( ($email[1]  ==  "gmail.com") || ($email[1]  ==  "googlemail.com") ) {
		$email[0] = str_replace(".", "" , $email[0]);

		//if the gmail address has a plus sign, remove from it to the end as gmail ignores that
		if ( strpos(  $email[0]  ,  "+") !== false) {
			$email[0] = substr($email[0] , 0 , strpos(  $email[0]  ,  "+"));
		}

		return strtolower($email[0]);
		
	}
	else
		return false;
}

/**
 * Checks wheter an alias of an email already exists
 * 
 * @global type $wpdb
 * @param type $email
 * @return boolean 
 */
function wangguard_email_aliases_exists($email) {
	global $wpdb;
	
	//option is activated?
	if ( wangguard_get_option("wangguard-verify-gmail")!='1')  
		return false;
	
	
	//cleans the email
	$guser = wangguard_get_clean_gmail_username($email);

	if ($guser !== false) {
		
		//if the email already exists, WP catches it, there's no need for WangGuard to check for aliases
		if (email_exists($email))
			return false;
		
		//get gmail.com and googlemail.com registered users
		$gmailaddresses = $wpdb->get_results("select user_email from {$wpdb->users} where LOWER(user_email) LIKE '%@gmail.com' OR LOWER(user_email) LIKE '%@googlemail.com'");

		if (!empty ($gmailaddresses)) {
			foreach ($gmailaddresses as $r) {
				$existing = wangguard_get_clean_gmail_username($r->user_email);
				if ($existing == $guser)
					return true;
			}
		}
	}

	return false;
}


$wangguard_NonceHName = 'wangguard-hidden-field-check';
$wangguard_NonceFName = 'wangguard-hidden-display-check';
$wangguard_NoncePName = 'wangguard-hidden-position-check';
$wangguard_NonceCName = 'wangguard-hidden-check-check';
$wangguard_HPrefix = 'user_';
$wangguard_FPrefix = 'newuser_';

/**
 * Get a random string
 */
function wangguard_randomstring($rndLen) {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$str = '';
	$strlen = strlen($chars);
	for ($i=0; $i < $rndLen; $i++)
	{
		$str .= substr($chars, mt_rand(0, $strlen - 1), 1);
	}
	return $str;
}
function wangguard_add_hfield_1() {
	global $wangguard_NonceHName , $wangguard_HPrefix;
	
	$nonceAct = $wangguard_NonceHName;
	$nonceValue = wp_create_nonce( $nonceAct );
	$fieldID = wangguard_randomstring(mt_rand(6,10));
	$nonce_field = '<input  type="hidden" id="' . $fieldID . '" name="' . $wangguard_HPrefix . $nonceValue . '" value="" />';
	echo $nonce_field;
}
function wangguard_add_hfield_2() {
	global $wangguard_NonceFName , $wangguard_FPrefix;
	
	$style = wangguard_randomstring(mt_rand(6,10));
	$fieldID = wangguard_randomstring(mt_rand(6,10));
	echo '<style type="text/css">.'.$style.' {display:none; visibility:hidden}</style>';
	
	$nonceAct = $wangguard_NonceFName;
	$nonceValue = wp_create_nonce( $nonceAct );
	$nonce_field = '<div class="'.$style.'"><input type="text" id="' . $fieldID . '" name="' . $wangguard_FPrefix . $nonceValue . '" value="" /></div>';
	echo $nonce_field;
}
function wangguard_add_hfield_3() {
	global $wangguard_NoncePName;
	
	$style = wangguard_randomstring(mt_rand(6,10));
	$fieldID = wangguard_randomstring(mt_rand(6,10));
	echo '<style type="text/css">.'.$style.' {position:absolute; top:-'.mt_rand(1000 , 2000).'px}</style>';
	
	$nonceAct = $wangguard_NoncePName;
	$nonceValue = wp_create_nonce( $nonceAct );
	$nonce_field = '<div class="'.$style.'"><label for="'.$nonceValue.'">Write down whats your favorite hobby is (required)</label><br/><input tabindex="'.mt_rand(9999,99999).'" type="text" id="' . $fieldID . '" name="' . $nonceValue . '" value="" /></div>';
	echo $nonce_field;
}
function wangguard_add_hfield_4() {
	global $wangguard_NonceCName;
	
	$style = wangguard_randomstring(mt_rand(6,10));
	$fieldID = wangguard_randomstring(mt_rand(6,10));
	echo '<style type="text/css">.'.$style.' {display:none; visibility:hidden}</style>';
	
	$nonceAct = $wangguard_NonceCName;
	$nonceValue = wp_create_nonce( $nonceAct );
	$nonce_field = '<div class="'.$style.'"><input type="checkbox" value="1" id="' . $fieldID . '" name="' . $nonceValue . '" /></div>';
	echo $nonce_field;
}

/**
 * WangGuard nonce
 */
function wangguard_get_nonce_value($action) {
	$user = wp_get_current_user();
	$uid = (int) $user->id;

	$i = wp_nonce_tick();

	return substr(wp_hash($i . $action . $uid, 'nonce'), -12, 10);
}

/**
 * Validates if there is suspicius activity on signup
 * 
 * @global string $wangguard_NonceHName
 * @global string $wangguard_HPrefix
 * @global string $wangguard_NonceFName
 * @global string $wangguard_FPrefix
 * @global string $wangguard_NoncePName
 * @global string $wangguard_NonceCName
 * @param type $userEmail
 * @return boolean 
 */
function wangguard_validate_hfields($userEmail) {
	global $wangguard_NonceHName , $wangguard_HPrefix;
	global $wangguard_NonceFName , $wangguard_FPrefix;
	global $wangguard_NoncePName;
	global $wangguard_NonceCName;

	$hNonce = wangguard_get_nonce_value($wangguard_NonceHName);
	$fNonce = wangguard_get_nonce_value($wangguard_NonceFName);
	$pNonce = wangguard_get_nonce_value($wangguard_NoncePName);
	$cNonce = wangguard_get_nonce_value($wangguard_NonceCName);
	
	$validated =  
		empty ($_POST[$wangguard_HPrefix.$hNonce]) &&
		empty ($_POST[$wangguard_FPrefix.$fNonce]) &&
		empty ($_POST[$pNonce]) &&
		empty ($_POST[$cNonce]);
	
	if (!$validated) {
		wangguard_report_email($userEmail , wangguard_getRemoteIP() , wangguard_getRemoteProxyIP() , true);
	}
	
	return $validated;
}

//*********** WPMU ***********
/**
 * Adds a security question if any exists
 * 
 * @global type $wpdb
 * @param type $errors 
 */
function wangguard_register_add_question_mu($errors) {
	global $wpdb;

	$table_name = $wpdb->base_prefix . "wangguardquestions";

	//Get one random question from the question table
	$qrs = $wpdb->get_row("select * from $table_name order by RAND() LIMIT 1");

	if (!is_null($qrs)) {
		$question = $qrs->Question;
		$questionID = $qrs->id;

		$html = '
			<label for="wangguardquestansw">' . $question . '</label>';
		echo $html;

		if ( $errmsg = $errors->get_error_message('wangguardquestansw') ) {
			echo '<p class="error">'.$errmsg.'</p>';
		}

		$html = '
			<input type="text" name="wangguardquestansw" id="wangguardquestansw" class="wangguard-mu-register-field" value="" maxlength="50" />
			<input type="hidden" name="wangguardquest" value="'.$questionID.'" />
		';
		echo $html;
	}
}

/**
 * Validates security question
 * 
 * @global type $wangguard_bp_validated
 * @param type $param
 * @return array
 */
function wangguard_wpmu_signup_validate_mu($param) {
	global $wangguard_bp_validated;

	if ( strpos($_SERVER['PHP_SELF'], 'wp-admin') !== false ) {
		return $param;
	}

	//BP1.1+ calls the new BP filter first (wangguard_signup_validate_bp11) and then the legacy MU filters (this one), if the BP new 1.1+ filter has been already called, silently return
	if ($wangguard_bp_validated)
		return $param;


	$errors = $param['errors'];
	
	if (!wangguard_validate_hfields($_POST['user_email'])) {
	    $errors->add('user_name',  __('<strong>ERROR</strong>: Banned by WangGuard <a href="http://www.wangguard.com/faq" target="_new">Is a mistake?</a>.', 'wangguard'));
		return $param;
	}
	
	$answerOK = wangguard_question_repliedOK();

	//If at least a question exists on the questions table, then check the provided answer
	if (!$answerOK)
	    $errors->add('wangguardquestansw', addslashes( __('<strong>ERROR</strong>: The answer to the security question is invalid.', 'wangguard')));
	else {

		//check domain against the list of selected blocked domains
		$blocked = wangguard_is_domain_blocked($param['user_email']);
		if ($blocked) {
			$errors->add('user_email',   __('<strong>ERROR</strong>: Domain not allowed.', 'wangguard'));
		}
		else {
			$reported = wangguard_is_email_reported_as_sp($param['user_email'] , wangguard_getRemoteIP() , wangguard_getRemoteProxyIP());

			if ($reported) 
				$errors->add('user_email',   __('<strong>ERROR</strong>: Banned by WangGuard <a href="http://www.wangguard.com/faq" target="_new">Is a mistake?</a>.', 'wangguard'));
			else if (wangguard_email_aliases_exists($param['user_email']))
				$errors->add('user_email',  addslashes( __('<strong>ERROR</strong>: Duplicate alias email found by WangGuard.', 'wangguard')));
			else if (!wangguard_mx_record_is_ok($param['user_email']))
				$errors->add('user_email',  addslashes( __("<strong>ERROR</strong>: WangGuard couldn't find an MX record associated with your email domain.", 'wangguard')));
		}
	}
	return $param;
}
//*********** WPMU ***********




//*********** BP1.1+ ***********
/**
 * Adds a security question if any exists
 * 
 * @global type $wpdb
 * @return array 
 */
function wangguard_register_add_question_bp11(){
	global $wpdb;

	if ( strpos($_SERVER['PHP_SELF'], 'wp-admin') !== false ) {
		return $param;
	}

	$table_name = $wpdb->base_prefix . "wangguardquestions";

	//Get one random question from the question table
	$qrs = $wpdb->get_row("select * from $table_name order by RAND() LIMIT 1");

	if (!is_null($qrs)) {
		$question = $qrs->Question;
		$questionID = $qrs->id;

		$html = '
		<div class="register-section" style=" width: 200px; clear:left; margin-top:-10px;">
			<label for="wangguardquestansw">' . $question . '</label>';
		echo $html;

		do_action( 'bp_wangguardquestansw_errors' );
		
		$html = '
			<input type="text" name="wangguardquestansw" id="wangguardquestansw" value="" maxlength="50" />
			<input type="hidden" name="wangguardquest" value="'.$questionID.'" />
			</div>
		';
		echo $html;
	}
}

/**
 * Validates security question
 * 
 * @global type $bp
 * @global boolean $wangguard_bp_validated
 */
function wangguard_signup_validate_bp11() {
	global $bp;
	global $wangguard_bp_validated;

	$wangguard_bp_validated = true;

	
	if (!wangguard_validate_hfields($_POST['signup_email'])) {
		$bp->signup->errors['signup_email'] = __('<strong>ERROR</strong>: Banned by WangGuard <a href="http://www.wangguard.com/faq" target="_new">Is a mistake?</a>.', 'wangguard');
		return;
	}
	
	
	$answerOK = wangguard_question_repliedOK();

	//If at least a question exists on the questions table, then check the provided answer
	if (!$answerOK)
		$bp->signup->errors['wangguardquestansw'] = addslashes (__('<strong>ERROR</strong>: The answer to the security question is invalid.', 'wangguard'));
	else {

		//check domain against the list of selected blocked domains
		$blocked = wangguard_is_domain_blocked($_REQUEST['signup_email']);
		if ($blocked) {
			$bp->signup->errors['signup_email'] = addslashes( __("<strong>ERROR</strong>: Domain not allowed.", 'wangguard'));
		}
		else {
			$reported = wangguard_is_email_reported_as_sp($_REQUEST['signup_email'] , wangguard_getRemoteIP() , wangguard_getRemoteProxyIP());

			if ($reported)
				$bp->signup->errors['signup_email'] = __('<strong>ERROR</strong>: Banned by WangGuard <a href="http://www.wangguard.com/faq" target="_new">Is a mistake?</a>.', 'wangguard');
			else if (wangguard_email_aliases_exists($_REQUEST['signup_email']))
				$bp->signup->errors['signup_email'] = addslashes (__('<strong>ERROR</strong>: Duplicate alias email found by WangGuard.', 'wangguard'));
			else if (!wangguard_mx_record_is_ok($_REQUEST['signup_email']))
				$bp->signup->errors['signup_email'] = addslashes( __("<strong>ERROR</strong>: WangGuard couldn't find an MX record associated with your email domain.", 'wangguard'));
		}
	}
	
	if (isset ($bp->signup->errors['signup_email']))
		$bp->signup->errors['signup_email'] = addslashes($bp->signup->errors['signup_email']);
}
//*********** BP1.1+ ***********





//*********** WP REGULAR ***********
/**
 * Adds a security question if any exists
 * 
 * @global type $wpdb 
 */
function wangguard_register_add_question(){
	global $wpdb;

	$table_name = $wpdb->base_prefix . "wangguardquestions";

	//Get one random question from the question table
	$qrs = $wpdb->get_row("select * from $table_name order by RAND() LIMIT 1");

	if (!is_null($qrs)) {
		$question = $qrs->Question;
		$questionID = $qrs->id;

		$html = '
			<div width="100%">
			<p>
			<label style="display: block; margin-bottom: 5px;">' . $question . '
			<input type="text" name="wangguardquestansw" id="wangguardquestansw" class="input wpreg-wangguardquestansw" value="" size="20" maxlength="50" tabindex="26" />
			</label>
			<input type="hidden" name="wangguardquest" value="'.$questionID.'" />
			</p>
			</div>
		';
		echo $html;
	}
}


/**
 * Validates security question
 * 
 * @param type $user_name
 * @param type $user_email
 * @param type $errors
 */
function wangguard_signup_validate($user_name , $user_email,$errors){
	if (!wangguard_validate_hfields($_POST['user_email'])) {
		$errors->add('user_login',__('<strong>ERROR</strong>: Banned by WangGuard <a href="http://www.wangguard.com/faq" target="_new">Is a mistake?</a>.', 'wangguard'));
		return;
	}
	
	$answerOK = wangguard_question_repliedOK();

	//If at least a question exists on the questions table, then check the provided answer
	if (!$answerOK)
		$errors->add('wangguard_error',__('<strong>ERROR</strong>: The answer to the security question is invalid.', 'wangguard'));
	else {

		//check domain against the list of selected blocked domains
		$blocked = wangguard_is_domain_blocked($_REQUEST['user_email']);
		if ($blocked) {
			$errors->add('wangguard_error',__('<strong>ERROR</strong>: Domain not allowed.', 'wangguard'));
		}
		else {
			$reported = wangguard_is_email_reported_as_sp($_REQUEST['user_email'] , wangguard_getRemoteIP() , wangguard_getRemoteProxyIP() , true);

			if ($reported)
				$errors->add('wangguard_error',__('<strong>ERROR</strong>: Banned by WangGuard <a href="http://www.wangguard.com/faq" target="_new">Is a mistake?</a>.', 'wangguard'));
			else if (wangguard_email_aliases_exists($_REQUEST['user_email']))
				$errors->add('wangguard_error',  addslashes( __('<strong>ERROR</strong>: Duplicate alias email found by WangGuard.', 'wangguard')));
			else if (!wangguard_mx_record_is_ok($_REQUEST['user_email']))
				$errors->add('wangguard_error',  addslashes( __("<strong>ERROR</strong>: WangGuard couldn't find an MX record associated with your email domain.", 'wangguard')));
		}
	}
}
//*********** WP REGULAR ***********


/**
 * Checks if a domain for an email address is selected to be blocked on the "Blocked domains" configuration screen
 * 
 * @param type $email 
 */
function wangguard_is_domain_blocked($email) {
	$parts = explode("@", $email);
	
	//if email is not well formed, return TRUE, this should never happens as WP already checks for a valid email format
	if (count($parts) != 2)
		return true;
	
	$domain = strtolower($parts[1]);
	$selectedDomains = maybe_unserialize( wangguard_get_option('blocked-list-domains') );
	if (!is_array($selectedDomains)) $selectedDomains = array();
	
	//matches exact domain?
	if (isset($selectedDomains[$domain]))
		return true;
	
	$domainParts = explode(".", $domain);
	if (count($domainParts) > 1) {
		$subdomcheck = $domainParts[count($domainParts)-1];
		
		//check for the top level domain
		if (isset($selectedDomains["*." . $subdomcheck]))
			return true;

		//n-level domains
		$from = count($domainParts)-2;
		for ($i = $from ; $i>=0 ; $i-- ) {
			$subdomcheck = $domainParts[$i] . "." . $subdomcheck;
			if (isset($selectedDomains["*." . $subdomcheck]))
				return true;
		}
	}
	else
		//malformed domain
		return true;
	
	return false;
}


/**
 * Verifies the email against WangGuard service
 * 
 * @global type $wpdb
 * @global type $wangguard_api_key
 * @global type $wangguard_user_check_status
 * @param type $email
 * @param type $clientIP
 * @param type $callingFromRegularWPHook regular WP hook sends true on this param
 * @return boolean 
 */
function wangguard_is_email_reported_as_sp($email , $clientIP , $ProxyIP , $callingFromRegularWPHook = false) {
	global $wpdb;
	global $wangguard_api_key;
	global $wangguard_user_check_status;

	if (empty ($wangguard_api_key))
		return false;

	$wangguard_user_check_status = "not-checked";

	$response = wangguard_http_post("wg=<in><apikey>$wangguard_api_key</apikey><email>".$email."</email><ip>".$clientIP."</ip><proxyip>".$ProxyIP."</proxyip></in>", 'query-email.php');
	$responseArr = XML_unserialize($response);

	wangguard_stats_update("check");

	if ( is_array($responseArr)) {
		if (($responseArr['out']['cod'] == '10') || ($responseArr['out']['cod'] == '11')) {
			wangguard_stats_update("detected");
			return true;
		}
		else {
			if ($responseArr['out']['cod'] == '20')
				$wangguard_user_check_status = 'checked';
			elseif ($responseArr['out']['cod'] == '100')
				$wangguard_user_check_status = 'error:' . __('Your WangGuard API KEY is invalid.', 'wangguard');
			else
				$wangguard_user_check_status = 'error:'.$responseArr['out']['cod'];
		}
	}

	return false;
}



/**
 *	Verifies the security question, used from the WP, WPMU and BP validation functions
 * @global type $wpdb
 * @return boolean 
 */
function wangguard_question_repliedOK() {
	
	
	//WP 3.2.1 multisite introduces a new two step registration, on step 2 we don't have to check the security question as it was checked in the step 1
	if ($_POST['stage'] == 'validate-blog-signup') {
		if (!wp_verify_nonce($_POST['_signup_form'] , 'signup_form_' . $_POST['signup_form_id']))
			return false;
		else
			return true;
	}
	
	
	global $wpdb;

	$table_name = $wpdb->base_prefix . "wangguardquestions";

	//How many questions are created?
	$questionCount = $wpdb->get_col("select count(*) as q from $table_name");

	$answerOK = true;

	//If at least a question exists on the questions table, then check the provided answer
	if ($questionCount[0]) {
		$questionID = intval($_REQUEST['wangguardquest']);
		$answer = $_REQUEST['wangguardquestansw'];

		$qrs = $wpdb->get_row( $wpdb->prepare("select * from $table_name where id = %d" , $questionID));
		if (!is_null($qrs)) {
			if (mb_strtolower( $_REQUEST['wangguardquestansw'] ) == mb_strtolower( $qrs->Answer ) ) {
				$wpdb->query( $wpdb->prepare("update $table_name set RepliedOK = RepliedOK + 1 where id = %d" , $questionID ) );
			}
			else {
				$answerOK = false;
				$wpdb->query( $wpdb->prepare("update $table_name set RepliedWRONG = RepliedWRONG + 1 where id = %d" , $questionID ) );
			}
		}
		else {
			$answerOK = false;
			$wpdb->query( $wpdb->prepare("update $table_name set RepliedWRONG = RepliedWRONG + 1 where id = %d" , $questionID ) );
		}
	}

	return $answerOK;
}

/********************************************************************/
/*** ADD & VALIDATE SECURITY QUESTIONS ON REGISTER ENDS ***/
/********************************************************************/








/********************************************************************/
/*** USER REGISTATION & DELETE FILTERS BEGINS ***/
/********************************************************************/
// user register and delete actions
add_action('user_register','wangguard_plugin_user_register');
add_action('bp_complete_signup','wangguard_plugin_bp_complete_signup');
add_action('bp_core_activated_user','wangguard_bp_core_activated_user' , 10 , 3);
add_action('wpmu_activate_user','wangguard_wpmu_activate_user' , 10 , 3);

add_action('delete_user','wangguard_plugin_user_delete');
add_action('wpmu_delete_user','wangguard_plugin_user_delete');
add_action('make_spam_user','wangguard_make_spam_user');
add_action('make_ham_user','wangguard_make_ham_user');
add_action('bp_core_action_set_spammer_status','wangguard_bp_core_action_set_spammer_status' , 10 , 2);


/**
 * Save the status of the verification upon BP signups
 * 
 * @global type $wpdb
 * @global type $wangguard_user_check_status 
 */
function wangguard_plugin_bp_complete_signup() {
	global $wpdb;
	global $wangguard_user_check_status;
	
	$table_name = $wpdb->base_prefix . "wangguardsignupsstatus";

	//delete just in case a previous record from a user which didn't activate the account is there
	$wpdb->query( $wpdb->prepare("delete from $table_name where signup_username = '%s'" , $_POST['signup_username']));

	//Insert the new signup record
	$wpdb->query( $wpdb->prepare("insert into $table_name(signup_username , user_status , user_ip , user_proxy_ip) values ('%s' , '%s' , '%s' , '%s')" , $_POST['signup_username'] , $wangguard_user_check_status , wangguard_getRemoteIP() , wangguard_getRemoteProxyIP() ) );
}


/**
 * Account activated on BP hook
 * 
 * @global type $wpdb
 * @global type $wangguard_api_key
 * @global type $wangguard_user_check_status
 * @param type $userid
 * @param type $key
 * @param type $user 
 */
function wangguard_bp_core_activated_user($userid, $key, $user) {
	global $wpdb;
	global $wangguard_api_key;
	global $wangguard_user_check_status;

	wangguard_plugin_user_register($userid);
}

/**
 * Account activated on WPMU hook
 * 
 * @global type $wpdb
 * @global type $wangguard_api_key
 * @global type $wangguard_user_check_status
 * @param type $userid
 * @param type $password
 * @param type $meta 
 */
function wangguard_wpmu_activate_user($userid, $password, $meta) {
	global $wpdb;
	global $wangguard_api_key;
	global $wangguard_user_check_status;

	wangguard_plugin_user_register($userid);
}

/**
 * Saves the status of the verification against WangGuard service upon user registration
 * 
 * @global type $wpdb
 * @global type $wangguard_user_check_status
 * @param type $userid 
 */
function wangguard_plugin_user_register($userid) {
	global $wpdb;
	global $wangguard_user_check_status;


	if (empty ($wangguard_user_check_status)) {
		$user = new WP_User($userid);
		$table_name = $wpdb->base_prefix . "wangguardsignupsstatus";

		//if there a status on the signups table?
		$user_status = $wpdb->get_var( $wpdb->prepare("select user_status from $table_name where signup_username = '%s'" , $user->user_login));

		//delete the signup status
		$wpdb->query( $wpdb->prepare("delete from $table_name where signup_username = '%s'" , $user->user_login));

		//If not empty, overrides the status with the signup status
		if (!empty ($user_status))
			$wangguard_user_check_status = $user_status;
	}


	$table_name = $wpdb->base_prefix . "wangguarduserstatus";

	$user_status = $wpdb->get_var( $wpdb->prepare("select ID from $table_name where ID = %d" , $userid));
	if ($user_status == null)
		//insert the new status
		$wpdb->query( $wpdb->prepare("insert into $table_name(ID , user_status , user_ip , user_proxy_ip) values (%d , '%s' , '%s' , '%s')" , $userid , $wangguard_user_check_status , wangguard_getRemoteIP() , wangguard_getRemoteProxyIP() ) );
	else
		//update the new status
		$wpdb->query( $wpdb->prepare("update $table_name set user_status = '%s' where ID = %d" , $wangguard_user_check_status , $userid  ) );
}


/**
 * Deletes the status of a user from the WangGuard status tracking table
 * 
 * @global type $wpdb
 * @param type $userid 
 */
function wangguard_plugin_user_delete($userid) {
	global $wpdb;

	$user = new WP_User($userid);
	
	//delete the signup status
	$table_name = $wpdb->base_prefix . "wangguardsignupsstatus";
	$wpdb->query( $wpdb->prepare("delete from $table_name where signup_username = '%s'" , $user->user_login));
	
	//delete the user status
	$table_name = $wpdb->base_prefix . "wangguarduserstatus";
	$wpdb->query( $wpdb->prepare("delete from $table_name where ID = %d" , $userid ) );
	
	//delete the user from the moderation queue
	$table_name = $wpdb->base_prefix . "wangguardreportqueue";
	$wpdb->query( $wpdb->prepare("delete from $table_name where ID = %d" , $userid ) );
	
	//delete the user reports from the moderation queue
	$table_name = $wpdb->base_prefix . "wangguardreportqueue";
	$wpdb->query( $wpdb->prepare("delete from $table_name where reported_by_ID = %d" , $userid ) );
}


/**
 * User has been reported as spam, send to WangGuard
 * @global type $wpdb
 * @param type $userid 
 */
function wangguard_make_spam_user($userid) {
	global $wpdb;

	//flag a user
	//get the recordset of the user to flag
	$wpusersRs = $wpdb->get_col( $wpdb->prepare("select ID from $wpdb->users where ID = %d" , $userid ) );

	wangguard_report_users($wpusersRs , "email" , false);
}

/**
 * User has been reported as safe, rollback on WangGuard
 * @global type $wpdb
 * @param type $userid 
 */
function wangguard_make_ham_user($userid) {
	global $wpdb;

	//flag a user
	//get the recordset of the user to make as safe
	$wpusersRs = $wpdb->get_col( $wpdb->prepare("select ID from $wpdb->users where ID = %d" , $userid ) );

	wangguard_rollback_report($wpusersRs);
}

/**
 * Updates WangGuard user staus when a user is flagged as spam or ham 
 * @param type $userid
 * @param type $is_spam 
 */
function wangguard_bp_core_action_set_spammer_status($userid , $is_spam) {
	if ($is_spam)
		wangguard_make_spam_user ($userid);
	else
		wangguard_make_ham_user ($userid);
}
/********************************************************************/
/*** USER REGISTATION & DELETE FILTERS ENDS ***/
/********************************************************************/







/********************************************************************/
/*** AJAX FRONT HANDLERS BEGINS ***/
/********************************************************************/
add_action('wp_head', 'wangguard_ajax_front_setup');
add_action('wp_ajax_wangguard_ajax_front_handler', 'wangguard_ajax_front_callback');

/**
 * Front end ajax functions
 * 
 * @global type $wuangguard_parent
 */
function wangguard_ajax_front_setup() {
	global $wuangguard_parent;
	
	if (!is_user_logged_in()) return;?>
<script type="text/javascript" >
	
	
function wangguard_isjQuery17()	 {
	var jQueryVersion = jQuery.fn.jquery.split('.');
	var ret = ( (parseInt(jQueryVersion[0])==1) && (parseInt(jQueryVersion[1])>=7) ) || ( parseInt(jQueryVersion[0])>1 );
	return ret;
}
	

if (typeof ajaxurl == 'undefined')
	ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
else if (ajaxurl == undefined)
	ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
	
jQuery(document).ready(function() {
	
	if (wangguard_isjQuery17() == true) {
		jQuery(document).on("click", ".wangguard-user-report", function(){
			wangguardUserReport_handler(this);
		});  
	}
	else {
		jQuery('.wangguard-user-report').live('click' , function () {
			wangguardUserReport_handler(this);
		});
	}
	
	function wangguardUserReport_handler(sender) {
		if (!confirm('<?php echo addslashes(__("Do you confirm to report the user?" , "wangguard"))?>')) 
			return;
		
		var userID = jQuery(sender).attr("rel");
		
		if ((userID == undefined) || (userID == '')) {
			userID = 0;
			
			//BP profile button doesn't allow to add a rel attr to the button so we store it in tne class field
			var tmpClass = jQuery(sender).attr("class");
			var matches = tmpClass.match(/wangguard-user-report-id-(\d+)/);
			if (matches != null)
				userID = matches[1];
		}
		
		data = {
			action	: 'wangguard_ajax_front_handler',
			object	: 'user',
			wpnonce	: '<?php echo wp_create_nonce("wangguardreport")?>',
			userid	: userID
		};
		jQuery.post(ajaxurl, data, function(response) {
			if (response=='0') {
				alert('<?php echo addslashes(__('The user was reported.', 'wangguard'))?>');
				jQuery(".wangguard-user-report[rel='"+userID+"']").fadeOut();
				jQuery(".wangguard-user-report-id-"+userID).fadeOut();
			}
		});
	};
	
	
	if (wangguard_isjQuery17() == true) {
		jQuery(document).on("click", ".wangguard-blog-report", function(){
			wangguardBlogReport_handler(this);
		});  
	}
	else {
		jQuery('.wangguard-blog-report').live('click' , function () {
			wangguardBlogReport_handler(this);
		});
	}
	
	function wangguardBlogReport_handler(sender) {
		if (!confirm('<?php echo addslashes(__("Do you confirm to report the blog and authors?" , "wangguard"))?>')) 
			return;
		
		var blogID = jQuery(sender).attr("rel");
		
		if ((blogID == undefined) || (blogID == '')) {
			blogID = 0;
			
			//BP profile button doesn't allow to add a rel attr to the button so we store it in tne class field
			var tmpClass = jQuery(sender).attr("class");
			var matches = tmpClass.match(/wangguard-blog-report-id-(\d+)/);
			if (matches != null)
				blogID = matches[1];
		}
		
		data = {
			action	: 'wangguard_ajax_front_handler',
			object	: 'blog',
			wpnonce	: '<?php echo wp_create_nonce("wangguardreport")?>',
			blogid	: blogID
		};
		jQuery.post(ajaxurl, data, function(response) {
			if (response=='0') {
				alert('<?php echo addslashes(__('The blog was reported.', 'wangguard'))?>');
				jQuery(".wangguard-blog-report").fadeOut();
			}
		});
	};
});</script>
<?php
}


/**
 * Checks whether a user is reported on queue
 * 
 * @global type $wpdb
 * @param type $userid
 * @return boolean 
 */
function wangguard_is_user_reported($userid) {
	global $wpdb;
	$table_name = $wpdb->base_prefix . "wangguardreportqueue";
	$Count = $wpdb->get_col( $wpdb->prepare("select count(*) as q from $table_name where ID = %d" , $userid) );
	return $Count[0] > 0;
}

/**
 * Checks whether a blog is reported on queue
 * 
 * @global type $wpdb
 * @param type $blogid
 * @return boolean
 */
function wangguard_is_blog_reported($blogid) {
	global $wpdb;
	$table_name = $wpdb->base_prefix . "wangguardreportqueue";
	$Count = $wpdb->get_col( $wpdb->prepare("select count(*) as q from $table_name where blog_id = %d" , $blogid) );
	return $Count[0] > 0;
}

/**
 * Front end AJAX handler
 * 
 * @global type $wpdb
 */
function wangguard_ajax_front_callback() {
	global $wpdb;
	if (!is_user_logged_in()) return;

	//add user ID or blog ID to the 
	$object = $_REQUEST['object'];
	$nonce = $_REQUEST['wpnonce'];
	if ( !wp_verify_nonce( $nonce, 'wangguardreport' ) )
		die();

	$thisUserID = get_current_user_id();
	
	if ($object == "user") {
		$userid = (int)$_REQUEST['userid'];
		if (empty ($userid)) die();
		if (wangguard_is_user_reported($userid)) die("0");

		$user_object = new WP_User($userid);

		//do not add admins as reported
		if ( wangguard_is_admin($user_object) ) die("0");
		
		$table_name = $wpdb->base_prefix . "wangguardreportqueue";
		$wpdb->query( $wpdb->prepare("insert into $table_name(ID , blog_id , reported_by_ID) values (%d , NULL , %d)" , $userid , $thisUserID ) );
		echo "0";
	}
	elseif ($object == "blog") {
		$blogid = (int)$_REQUEST['blogid'];
		if (empty ($blogid)) die();
		if (wangguard_is_blog_reported($blogid)) die("0");

		$isMainBlog = false;
		if (isset ($current_site)) {
			$isMainBlog = ($blogid != $current_site->blog_id); // main blog not a spam !
		}
		elseif (defined("BP_ROOT_BLOG")) {
			$isMainBlog = ( 1 == $blogid || BP_ROOT_BLOG == $blogid );
		}
		else
			$isMainBlog = ($blogid == 1);

		
		//do not report main blog
		if ($isMainBlog) die("0");

		
		$table_name = $wpdb->base_prefix . "wangguardreportqueue";
		$wpdb->query( $wpdb->prepare("insert into $table_name(ID , blog_id , reported_by_ID) values (NULL , %d , %d)" , $blogid , $thisUserID ) );
		echo "0";
	}
	
	die();
}
/********************************************************************/
/*** AJAX FRONT HANDLERS ENDS ***/
/********************************************************************/






/********************************************************************/
/*** AJAX ADMIN HANDLERS BEGINS ***/
/********************************************************************/
add_action('admin_head', 'wangguard_ajax_setup');
add_action('wp_ajax_wangguard_ajax_handler', 'wangguard_ajax_callback');
add_action('wp_ajax_wangguard_ajax_recheck', 'wangguard_ajax_recheck_callback');
add_action('wp_ajax_wangguard_ajax_questionadd', 'wangguard_ajax_questionadd');
add_action('wp_ajax_wangguard_ajax_questiondelete', 'wangguard_ajax_questiondelete');

/**
 * Admin side AJAX functions
 * 
 * @global type $wuangguard_parent
 * @global type $wuangguard_parent
 * @global type $wuangguard_parent
 */
function wangguard_ajax_setup() {
	global $wuangguard_parent;
	
	if (!current_user_can('level_10')) return;
?>

<script type="text/javascript" >
var wangguardBulkOpError = false;

function wangguard_isjQuery17()	 {
	var jQueryVersion = jQuery.fn.jquery.split('.');
	var ret = ( (parseInt(jQueryVersion[0])==1) && (parseInt(jQueryVersion[1])>=7) ) || ( parseInt(jQueryVersion[0])>1 );
	return ret;
}


jQuery(document).ready(function($) {
	jQuery("a.wangguard-splogger").click(function() {
		var userid = jQuery(this).attr("rel");
		wangguard_report(userid , false);
	});

	jQuery("a.wangguard-rollback").click(function() {
		var userid = jQuery(this).attr("rel");
		wangguard_rollback(userid);
	});

	jQuery("a.wangguard-splogger-blog").click(function() {
		var blogid = jQuery(this).attr("rel");
		wangguard_report_blog(blogid , false);
	});

	function wangguard_report(userid , frombulk) {
		var confirmed = true;
		<?php if (wangguard_get_option ("wangguard-expertmode")!='1') {?>
			if (!frombulk) {
			<?php if (wangguard_get_option ("wangguard-delete-users-on-report")=='1') {?>
				confirmed = confirm('<?php echo addslashes(__('Do you confirm to flag this user as Splogger? This operation is IRREVERSIBLE and will DELETE the user.', 'wangguard'))?>');
			<?php }
			else {?>
				confirmed = confirm('<?php echo addslashes(__('Do you confirm to flag this user as Splogger?', 'wangguard'))?>');
			<?php }?>
			}
		<?php }?>

		if (confirmed) {
			data = {
				action	: 'wangguard_ajax_handler',
				scope	: 'email',
				userid	: userid
			};
			jQuery.post(ajaxurl, data, function(response) {
				if (response=='0') {
					alert('<?php echo addslashes(__('The selected user couldn\'t be found on the users table.', 'wangguard'))?>');
				}
				else if (response=='-1') {
					wangguardBulkOpError = true;
					alert('<?php echo addslashes(__('Your WangGuard API KEY is invalid.', 'wangguard'))?>');
				}
				else if (response=='-2') {
					wangguardBulkOpError = true;
					alert('<?php echo addslashes(__('There was a problem connecting to the WangGuard server. Please check your server configuration.', 'wangguard'))?>');
				}
				else {
					<?php if ($wuangguard_parent == 'edit.php') {?>
					document.location = document.location;
					<?php }
					else {?>
						<?php if (wangguard_get_option ("wangguard-delete-users-on-report")=='1') {?>
							jQuery('td span.wangguardstatus-'+response).parent().parent().fadeOut();
						<?php }
						else {?>
							jQuery('td span.wangguardstatus-'+response).removeClass('wangguard-status-checked');
							jQuery('td span.wangguardstatus-'+response).addClass('wangguard-status-splogguer');
							jQuery('td span.wangguardstatus-'+response).html('<?php echo __('Reported as Splogger' , 'wangguard')?>');
							jQuery('a.wangguard-splogger[rel=\''+response+'\']').hide();
							jQuery('a.wangguard-rollback[rel=\''+response+'\']').show();
						<?php }?>
					<?php }?>
				}
			});
		}
	}




	function wangguard_rollback(userid) {
		var confirmed = true;
		<?php if (wangguard_get_option ("wangguard-expertmode")!='1') {?>
			confirmed = confirm('<?php echo addslashes(__('Do you confirm to flag this user as safe?', 'wangguard'))?>');
		<?php }?>

		if (confirmed) {
			data = {
				action	: 'wangguard_ajax_handler',
				scope	: 'rollback-email',
				userid	: userid
			};
			jQuery.post(ajaxurl, data, function(response) {
				if (response=='0') {
					alert('<?php echo addslashes(__('The selected user couldn\'t be found on the users table.', 'wangguard'))?>');
				}
				else if (response=='-1') {
					wangguardBulkOpError = true;
					alert('<?php echo addslashes(__('Your WangGuard API KEY is invalid.', 'wangguard'))?>');
				}
				else if (response=='-2') {
					wangguardBulkOpError = true;
					alert('<?php echo addslashes(__('There was a problem connecting to the WangGuard server. Please check your server configuration.', 'wangguard'))?>');
				}
				else {
					<?php if ($wuangguard_parent == 'edit.php') {?>
					document.location = document.location;
					<?php }
					else {?>
						jQuery('td span.wangguardstatus-'+response).removeClass('wangguard-status-splogguer');
						jQuery('td span.wangguardstatus-'+response).addClass('wangguard-status-checked');
						jQuery('td span.wangguardstatus-'+response).html('<?php echo __('Checked (forced)' , 'wangguard')?>');
						jQuery('a.wangguard-rollback[rel=\''+response+'\']').hide();
						jQuery('a.wangguard-splogger[rel=\''+response+'\']').show();
					<?php }?>
				}
			});
		}
	}





	function wangguard_report_blog(blogid) {
		var confirmed = true;
		<?php if (wangguard_get_option ("wangguard-expertmode")!='1') {?>
			<?php if (wangguard_get_option ("wangguard-delete-users-on-report")=='1') {?>
				confirmed = confirm('<?php echo addslashes(__('Do you confirm to flag this blog\'s author(s) as Splogger(s)? This operation is IRREVERSIBLE and will DELETE the user(s).', 'wangguard'))?>');
			<?php }
			else {?>
				confirmed = confirm('<?php echo addslashes(__('Do you confirm to flag this blog\'s author(s) as Splogger(s)?', 'wangguard'))?>');
			<?php }?>
		<?php }?>

		if (confirmed) {
			data = {
				action	: 'wangguard_ajax_handler',
				scope	: 'blog',
				blogid	: blogid
			};
			jQuery.post(ajaxurl, data, function(response) {
				if (response=='0') {
					alert('<?php echo addslashes(__('The selected blog couldn\'t be found.', 'wangguard'))?>');
				}
				else if (response=='-1') {
					wangguardBulkOpError = true;
					alert('<?php echo addslashes(__('Your WangGuard API KEY is invalid.', 'wangguard'))?>');
				}
				else if (response=='-2') {
					wangguardBulkOpError = true;
					alert('<?php echo addslashes(__('There was a problem connecting to the WangGuard server. Please check your server configuration.', 'wangguard'))?>');
				}
				else {
					jQuery('tr#blog-'+blogid).fadeOut();
					
					var users = response.split(",");
					for (i=0;i<=users.length;i++)
						jQuery('td span.wangguardstatus-'+users[i]).parent().parent().fadeOut();
				}
			});
		}
	}

	
	
	jQuery(".wangguard-queue-remove-blog").click(function() {
		if (!confirm('<?php echo addslashes(__("Do you confirm to remove the blog from the Moderation Queue?" , "wangguard"))?>')) 
			return;
		
		var blogID = jQuery(this).attr("rel");
		
		data = {
			action	: 'wangguard_ajax_handler',
			scope	: 'queue_blog_remove',
			wpnonce	: '<?php echo wp_create_nonce("wangguardreport")?>',
			blogid	: blogID
		};
		jQuery.post(ajaxurl, data, function(response) {
			if (response=='0') {
				jQuery("tr#blog-"+blogID).fadeOut();
			}
		});
	});


	
	jQuery(".wangguard-queue-remove-user").click(function() {
		if (!confirm('<?php echo addslashes(__("Do you confirm to remove the user from the Moderation Queue?" , "wangguard"))?>')) 
			return;
		
		var userID = jQuery(this).attr("rel");
		
		data = {
			action	: 'wangguard_ajax_handler',
			scope	: 'queue_user_remove',
			wpnonce	: '<?php echo wp_create_nonce("wangguardreport")?>',
			userid	: userID
		};
		jQuery.post(ajaxurl, data, function(response) {
			if (response=='0') {
				jQuery("tr#user-"+userID).fadeOut();
			}
		});
	});






	jQuery("a.wangguard-domain").click(function() {

		var confirmed = true;
		<?php if (wangguard_get_option ("wangguard-expertmode")!='1') {?>
			<?php if (wangguard_get_option ("wangguard-delete-users-on-report")=='1') {?>
				confirmed = confirm('<?php echo addslashes(__('Do you confirm to flag this user domain as Splogger? This operation is IRREVERSIBLE and will DELETE the users that shares this domain.', 'wangguard'))?>');
			<?php }
			else {?>
				confirmed = confirm('<?php echo addslashes(__('Do you confirm to flag this user domain as Splogger?', 'wangguard'))?>');
			<?php }?>
		<?php }?>

		if (confirmed) {
			data = {
				action	: 'wangguard_ajax_handler',
				scope	: 'domain',
				userid	: jQuery(this).attr("rel")
			};
			jQuery.post(ajaxurl, data, function(response) {
				if (response=='0') {
					alert('<?php echo addslashes(__('The selected user couldn\'t be found on the users table.', 'wangguard'))?>');
				}
				else if (response=='-1') {
					alert('<?php echo addslashes(__('Your WangGuard API KEY is invalid.', 'wangguard'))?>');
				}
				else if (response=='-2') {
					alert('<?php echo addslashes(__('There was a problem connecting to the WangGuard server. Please check your server configuration.', 'wangguard'))?>');
				}
				else {
					var users = response.split(",");
					for (i=0;i<=users.length;i++)
						jQuery('td span.wangguardstatus-'+users[i]).parent().parent().fadeOut();
				}
			});
		}
	});

	<?php 
	global $wuangguard_parent;
	if (($wuangguard_parent == 'ms-users.php') || ($wuangguard_parent == 'wpmu-users.php') || ($wuangguard_parent == 'users.php')) {?>
	jQuery(document).ajaxError(function(e, xhr, settings, exception) {
		alert('<?php echo addslashes(__('There was a problem connecting to your WordPress server.', 'wangguard'))?>');
	});
	<?php }?>


	jQuery("a.wangguard-recheck").click(function() {
		var userid = jQuery(this).attr("rel");
		wangguard_recheck(userid);
	});

	function wangguard_recheck(userid) {
		data = {
			action	: 'wangguard_ajax_recheck',
			userid	: userid
		};

		jQuery.post(ajaxurl, data, function(response) {
			if (response=='0') {
				alert('<?php echo addslashes(__('The selected user couldn\'t be found on the users table.', 'wangguard'))?>');
			}
			else if (response=='-1') {
				wangguardBulkOpError = true;
				alert('<?php echo addslashes(__('Your WangGuard API KEY is invalid.', 'wangguard'))?>');
			}
			else if (response=='-2') {
				wangguardBulkOpError = true;
				alert('<?php echo addslashes(__('There was a problem connecting to the WangGuard server. Please check your server configuration.', 'wangguard'))?>');
			}
			else {
				jQuery('td span.wangguardstatus-'+userid).fadeOut(500, function() {
					jQuery(this).html(response);
					jQuery(this).fadeIn(500);
				})
			}
		});
	}


	if (wangguard_isjQuery17() == true) {
		jQuery(document).on("click", "a.wangguard-delete-question", function(){
			wangguardDeleteQuestion(this);
		});  
	}
	else {
		jQuery('a.wangguard-delete-question').live('click' , function () {
			wangguardDeleteQuestion_handler(this);
		});
	}


	function wangguardDeleteQuestion(sender) {

		<?php if (wangguard_get_option ("wangguard-expertmode")=='1') {?>
			var confirmed = true;
		<?php }
		else {?>
			var confirmed = confirm('<?php echo addslashes(__('Do you confirm to delete this question?.', 'wangguard'))?>');
		<?php }?>

		if (confirmed) {
			var questid	= jQuery(sender).attr("rel");
			data = {
				action	: 'wangguard_ajax_questiondelete',
				questid	: questid
			};
			jQuery.post(ajaxurl, data, function(response) {
				if (response!='0') {
					jQuery("#wangguard-question-"+questid).slideUp("fast");
				}
			});
		}
	};


	
	jQuery("#wangguardnewquestionbutton").click(function() {
		jQuery("#wangguardnewquestionerror").hide();

		var wgq = jQuery("#wangguardnewquestion").val();
		var wga = jQuery("#wangguardnewquestionanswer").val();
		if ((wgq=='') || wga=='') {
			jQuery("#wangguardnewquestionerror").slideDown();
			return;
		}
		
		data = {
			action	: 'wangguard_ajax_questionadd',
			q		: wgq,
			a		: wga
		};
		jQuery.post(ajaxurl, data, function(response) {
			if (response!='0') {
				var newquest = '<div class="wangguard-question" id="wangguard-question-'+response+'">';
				newquest += '<?php echo addslashes(__("Question", 'wangguard'))?>: <strong>'+wgq+'</strong><br/>';
				newquest += '<?php echo addslashes(__("Answer", 'wangguard'))?>: <strong>'+wga+'</strong><br/>';
				newquest += '<a href="javascript:void(0)" rel="'+response+'" class="wangguard-delete-question"><?php echo addslashes(__('delete question', 'wangguard'))?></a></div>';
				
				jQuery("#wangguard-new-question-container").append(newquest);

				jQuery("#wangguardnewquestion").val("");
				jQuery("#wangguardnewquestionanswer").val("");
			}
			else if (response=='0') {
				jQuery("#wangguardnewquestionerror").slideDown();
			}
		});
	});



	<?php
	global $wuangguard_parent;
	if (($wuangguard_parent == 'ms-users.php') || ($wuangguard_parent == 'wpmu-users.php') || ($wuangguard_parent == 'users.php')) {?>
		var wangguard_bulk = '';
		wangguard_bulk += '<input style="margin-right:15px" type="button" class="button-secondary action wangguardbulkcheckbutton" name="wangguardbulkcheckbutton" value="<?php echo addslashes(__('Bulk check Sploggers' , 'wangguard')) ?>">';
		wangguard_bulk += '<input type="button" class="button-secondary action wangguardbulkreportbutton" name="wangguardbulkreportbutton" value="<?php echo addslashes(__('Bulk report Sploggers' , 'wangguard')) ?>">';
		jQuery("div.tablenav div.alignleft:first").append(wangguard_bulk);
		jQuery("div.tablenav div.alignleft:last").append(wangguard_bulk);



		if (wangguard_isjQuery17() == true) {
			jQuery(document).on("click", "input.wangguardbulkcheckbutton", function(){
				wangguardbulkcheck_handler();
			});  
		}
		else {
			jQuery('input.wangguardbulkcheckbutton').live('click' , function () {
				wangguardbulkcheck_handler();
			});
		}


		function wangguardbulkcheck_handler() {
			var userscheck;
			userscheck = jQuery('input[name="users[]"]:checked');

			//Checkboxes name varies thru WP screens (users.php / ms-users.php / wpmu-users.php) and versions
			if (userscheck.length == 0)
				userscheck = jQuery('input[name="allusers[]"]:checked');

			//Checkboxes name varies thru WP screens (users.php / ms-users.php / wpmu-users.php) and versions
			if (userscheck.length == 0)
				userscheck = jQuery('th.check-column input[type="checkbox"]:checked');

			wangguardBulkOpError = false;

			userscheck.each(function() {

					if (wangguardBulkOpError) {
						return;
					}

					wangguard_recheck(jQuery(this).val());
			});

		};


		if (wangguard_isjQuery17() == true) {
			jQuery(document).on("click", "input.wangguardbulkreportbutton", function(){
				wangguardbulkreportbutton_handler();
			});  
		}
		else {
			jQuery('input.wangguardbulkreportbutton').live('click' , function () {
				wangguardbulkreportbutton_handler();
			});
		}


		function wangguardbulkreportbutton_handler() {
			<?php if (wangguard_get_option ("wangguard-delete-users-on-report")=='1') {?>
				if (!confirm('<?php _e('Do you confirm to flag the selected users as Sploggers? This operation is IRREVERSIBLE and will DELETE the users.' , 'wangguard')?>'))
					return;
			<?php }
			else {?>
				if (!confirm('<?php _e('Do you confirm to flag the selected users as Sploggers?' , 'wangguard')?>'))
					return;
			<?php }?>

			var userscheck;
			userscheck = jQuery('input[name="users[]"]:checked');

			//Checkboxes name varies thru WP screens (users.php / ms-users.php / wpmu-users.php) and versions
			if (userscheck.length == 0)
				userscheck = jQuery('input[name="allusers[]"]:checked');

			//Checkboxes name varies thru WP screens (users.php / ms-users.php / wpmu-users.php) and versions
			if (userscheck.length == 0)
				userscheck = jQuery('th.check-column input[type="checkbox"]:checked');


			wangguardBulkOpError = false;

			userscheck.each(function() {

					if (wangguardBulkOpError) {
						return;
					}

					wangguard_report(jQuery(this).val() , true);
			});

			//document.location = document.location;
		};

	<?php }?>
});


</script>
<?php
}


/**
 * Admin side AJAX handler
 * 
 * @global type $wpdb 
 */
function wangguard_ajax_callback() {
	global $wpdb;

	if (!current_user_can('level_10')) die();
	
	$userid = intval($_POST['userid']);
	$scope = $_POST['scope'];
	
	
	switch ($scope) {
		case "queue_blog_remove":
			//remove blog from queue
			$blogid = intval($_POST['blogid']);
			$table_name = $wpdb->base_prefix . "wangguardreportqueue";
			$wpdb->query( $wpdb->prepare("delete from $table_name where blog_id = '%d'" , $blogid ) );
			echo "0";
			break;
		
		
		case "queue_user_remove":
			//remove user from queue
			$table_name = $wpdb->base_prefix . "wangguardreportqueue";
			$wpdb->query( $wpdb->prepare("delete from $table_name where ID = '%d'" , $userid ) );
			echo "0";
			break;
		
		
		case "domain":
			//flag domain
			$userDomain = new WP_User($userid);
			$domain = wangguard_extract_domain($userDomain->user_email);
			$domain = '%@' . str_replace(array("%" , "_"), array("\\%" , "\\_"), $domain);

			//get the recordset of the users to flag
			$wpusersRs = $wpdb->get_col( $wpdb->prepare("select ID from $wpdb->users where user_email LIKE '%s'" , $domain ) );
			echo wangguard_report_users($wpusersRs , $scope);
			break;
		
		
		case "blog":
			//flag domain
			$blogid = intval($_POST['blogid']);
			$blog_prefix = $wpdb->get_blog_prefix( $blogid );
			$authors = $wpdb->get_results( "SELECT user_id, meta_value as caps FROM $wpdb->users u, $wpdb->usermeta um WHERE u.ID = um.user_id AND meta_key = '{$blog_prefix}capabilities'" );
			$authorsArray = array();
			foreach( (array)$authors as $author ) {
				$caps = maybe_unserialize( $author->caps );
				if ( isset( $caps['subscriber'] ) || isset( $caps['contributor'] ) ) continue;
				
				$authorsArray[] = $author->user_id;
			}
			
			echo wangguard_report_users($authorsArray , "email");
			
			break;
		
		case "rollback-email":
			$wpusersRs = $wpdb->get_col( $wpdb->prepare("select ID from $wpdb->users where ID = %d" , $userid ) );
			echo wangguard_rollback_report($wpusersRs);
			break;
		
		default:
			//flag a user
			//get the recordset of the user to flag
			$wpusersRs = $wpdb->get_col( $wpdb->prepare("select ID from $wpdb->users where ID = %d" , $userid ) );
			echo wangguard_report_users($wpusersRs , $scope);
			break;
	}

	die();
}

/**
 * Add question handler
 * 
 * @global type $wpdb 
 */
function wangguard_ajax_questionadd() {
	global $wpdb;

	if (!current_user_can('level_10')) die();

	$q = trim($_POST['q']);
	$a = trim($_POST['a']);


	if (get_magic_quotes_gpc()) {
		$q = stripslashes($q);
		$a = stripslashes($a);
	}

	if (empty ($q) || empty ($a)) {
		echo "0";
		die();
	}

	$table_name = $wpdb->base_prefix . "wangguardquestions";
	$wpdb->insert( $table_name , array( 'Question'=>$q  , "Answer"=>$a) , array('%s','%s') );

	echo $wpdb->insert_id;
	die();
}

/**
 * Delete question handler
 * 
 * @global type $wpdb 
 */
function wangguard_ajax_questiondelete() {
	global $wpdb;

	if (!current_user_can('level_10')) die();

	$questid = intval($_POST['questid']);

	$table_name = $wpdb->base_prefix . "wangguardquestions";
	$wpdb->query( $wpdb->prepare("delete from $table_name where id = %d" , $questid) );

	echo $questid;
	die();
}

/**
 * Recheck user on WangGuard handler
 * 
 * @global type $wpdb
 * @global type $wangguard_api_key
 */
function wangguard_ajax_recheck_callback() {
	global $wpdb;
	global $wangguard_api_key;

	if (!current_user_can('level_10')) die();

	$userid = intval($_POST['userid']);

	$valid = wangguard_verify_key($wangguard_api_key);
	if ($valid == 'failed') {
		echo "-2";
		die();
	} 
	else if ($valid == 'invalid') {
		echo "-1";
		die();
	}

	$user_object = new WP_User($userid);
	if (empty ($user_object->user_email)) {
		echo "0";
		die();
	}

	if ( wangguard_is_admin($user_object) ) {
		echo '<span class="wangguard-status-no-status wangguardstatus-'.$userid.'">'. __('No status', 'wangguard') .'</span>';
		die();
	}

	$user_check_status = wangguard_verify_user($user_object);

	if ($user_check_status == "reported") {
		echo '<span class="wangguard-status-splogguer">'. __('Reported as Splogger', 'wangguard') .'</span>';
	}
	elseif ($user_check_status == "checked") {
		echo '<span class="wangguard-status-checked">'. __('Checked', 'wangguard') .'</span>';
	}
	elseif (substr($user_check_status,0,5) == "error") {
		echo '<span class="wangguard-status-error">'. __('Error', 'wangguard') . " - " . substr($user_check_status,6) . '</span>';
	}
	else
		return '<span class="wangguard-status-not-checked">'. __('Not checked', 'wangguard') .'</span>';

	die();
}
/********************************************************************/
/*** AJAX ADMIN HANDLERS ENDS ***/
/********************************************************************/




/********************************************************************/
/*** BP FRONTEND REPORT BUTTONS BEGINS ***/
/********************************************************************/
/**
 * Hook to insert the report user on BP comment
 * 
 * @global type $bp
 * @global type $user_ID
 * @param string $link
 * @param type $args
 * @param type $comment
 * @param type $post
 * @return string 
 */
function wangguard_bp_comment_reply_link($link , $args, $comment, $post='') {
	global $bp , $user_ID;
	$userid = $comment->user_id;

	if (!$bp) return $link;
	$user_object = new WP_User($userid);
	if (empty ($user_object->ID)) return $link;
	if ($user_ID == $user_object->ID) return $link;
	if (wangguard_is_admin($user_object)) return $link;
	

	$link .= '<a href="javascript:void(0)" style="margin-left:10px" class="comment-reply-link wangguard-user-report" rel="'.$userid.'" title="'.__('Report user', 'wangguard').'">'.__('Report user', 'wangguard').'</a>';
	return $link;
}

/**
 * Hook to insert the report user on BP blog post and activity
 * 
 * @global type $l10n
 * @global type $post
 * @param type $id
 * @param type $type
 */
function wangguard_bp_report_button($id = '', $type = '') {

	if (!is_user_logged_in())
		return;
	
	if ( !$type && !is_single() )
		$type = 'activity';
	elseif ( !$type && is_single() )
		$type = 'blogpost';


	if (function_exists("is_textdomain_loaded")) {
		if (!is_textdomain_loaded("wangguard"))
			load_textdomain ("wangguard", PLUGINDIR . "/wangguard/languages/wangguard-".WPLANG.".mo");
	}
	else {
		global $l10n;
		if (!isset( $l10n['wangguard']))
			load_textdomain ("wangguard", PLUGINDIR . " /wangguard/languages/wangguard-".WPLANG.".mo");
	}
	
	if ( $type == 'activity' ) :

		$activity = bp_activity_get_specific( array( 'activity_ids' => bp_get_activity_id() ) );

		if ( $activity_type !== 'activity_liked' ) :
			$user_id = $activity['activities'][0]->user_id;
			$user_object = new WP_User($user_id);
			if (empty ($user_object->ID)) return;
			if (!wangguard_is_admin($user_object)) :

				if ( true || !bp_like_is_liked( bp_get_activity_id(), 'activity' ) ) : ?>
				<a href="javascript:void(0)" class="button wangguard-user-report" rel="<?php echo $user_object->ID;?>" title="<?php echo __('Report user', 'wangguard'); ?>"><?php echo  __('Report user', 'wangguard');?></a>
				<?php endif;
			endif;
		endif;

	elseif ( $type == 'blogpost' ) :
		global $post;
		if (empty ($post->post_author)) return;

		$user_id = $post->post_author;
		$user_object = new WP_User($user_id);
		if (empty ($user_object->ID)) return;
		if (!wangguard_is_admin($user_object)) :
			if (true || !bp_like_is_liked( $id, 'blogpost' ) ) : ?>

				<div class="activity-list"><div class="activity-meta"><a href="javascript:void(0)" class="button wangguard-user-report" rel="<?php echo $user_object->ID;?>" title="<?php echo __('Report user', 'wangguard'); ?>"><?php echo  __('Report user', 'wangguard');?></a></div></div>

			<?php endif;
		endif;
		
	endif;
}
if (wangguard_get_option ("wangguard-enable-bp-report-btn")==1) {
	add_filter( 'bp_activity_entry_meta', 'wangguard_bp_report_button' );
	add_action( 'bp_before_blog_single_post', 'wangguard_bp_report_button' );
	add_filter( 'comment_reply_link', 'wangguard_bp_comment_reply_link' , 10 , 4);
}

/**
 * Hook to insert the report user on user's profile
 * 
 * @global type $bp
 */
function wangguard_bp_report_button_header() {
	global $bp;
	if (!$bp) return;
	$user_object = new WP_User($bp->displayed_user->id);
	if (empty ($user_object->ID)) return;
	if (wangguard_is_admin($user_object)) return;
	
	echo bp_get_button( array(
		'id'                => 'wangguard_report_user',
		'component'         => 'members',
		'must_be_logged_in' => true,
		'block_self'        => true,
		'wrapper_id'        => 'wangguard_report_user-button',
		'link_href'         => "javascript:void(0)",
		'link_class'        => 'wangguard-user-report wangguard-user-report-id-' . $user_object->ID,
		'link_title'        => __('Report user', 'wangguard'),
		'link_text'         => __('Report user', 'wangguard')
	) );
}
if (wangguard_get_option ("wangguard-enable-bp-report-btn")==1) {
	add_action( 'bp_member_header_actions',    'wangguard_bp_report_button_header' , 20 );
}
/********************************************************************/
/*** BP FRONTEND REPORT BUTTONS ENDS ***/
/********************************************************************/



/********************************************************************/
/*** ADMIN BAR REPORT BEGIN ***/
/********************************************************************/
/**
 * Add WangGuard to BP admin bar
 * 
 * @global type $current_blog
 * @global type $wangguard_is_network_admin
 * @global type $wp_version
 */
function wangguard_add_bp_admin_bar_menus() {
	global $current_blog , $wangguard_is_network_admin;

	if (!is_user_logged_in())
		return;
	

	$urlFunc = "admin_url";
	if ($wangguard_is_network_admin && function_exists("network_admin_url"))
		$urlFunc = "network_admin_url";

	
	if (function_exists("is_super_admin"))
		$showAdmin = is_super_admin();
	else
		$showAdmin = current_user_can('level_10');

	
	global $wp_version;
	$cur_wp_version = preg_replace('/-.*$/', '', $wp_version);
	$WP_List_TableClassSupported = version_compare($cur_wp_version , '3.1.0' , ">=");
	
	$queueEnabled = ((wangguard_get_option("wangguard-enable-bp-report-blog") == 1) || (wangguard_get_option ("wangguard-enable-bp-report-btn")==1))  &&   $WP_List_TableClassSupported;
	
	// This is a blog, render a menu with links to all authors
	if ($showAdmin) {
		echo '<li id="wangguard-report-menu"><a href="'. $urlFunc( "admin.php?page=" . ($queueEnabled ? "wangguard_queue" : "wangguard_conf") ).'">';
		_e('WangGuard', 'wangguard');
		echo '</a>';
		echo '<ul class="wangguard-report-menu-list">';

		if ( $current_blog && (wangguard_get_option("wangguard-enable-bp-report-blog") == 1) ) {
			if (BP_ROOT_BLOG != $current_blog->blog_id) {
			echo '<li>';
			echo '<a href="javascript:void(0)" class="wangguard-blog-report" rel="'.$current_blog->blog_id.'">';
			echo __('Report blog and author', 'wangguard') . '</a>';
			echo '</li>';
			}
		}

		
		if ($queueEnabled) {
			echo '<li>';
			echo '<a href="'.$urlFunc( "admin.php?page=wangguard_queue" ).'">';
			echo __('Moderation Queue', 'wangguard') . '</a>';
			echo '<div class="admin-bar-clear"></div>';
			echo '</li>';
		}
		echo '<li>';
		echo '<a href="'.$urlFunc( "admin.php?page=wangguard_wizard" ).'">';
		echo __('Wizard', 'wangguard') . '</a>';
		echo '<div class="admin-bar-clear"></div>';
		echo '</li>';
		echo '<li>';
		echo '<a href="'.$urlFunc( "admin.php?page=wangguard_conf" ).'">';
		echo __('Configuration', 'wangguard') . '</a>';
		echo '<div class="admin-bar-clear"></div>';
		echo '</li>';
		echo '<li>';
		echo '<a href="'.$urlFunc( "admin.php?page=wangguard_stats" ).'">';
		echo __('Stats', 'wangguard') . '</a>';
		echo '<div class="admin-bar-clear"></div>';
		echo '</li>';

		echo '</ul>';
		echo '</li>';
	}
	else {
		if ( $current_blog && (wangguard_get_option("wangguard-enable-bp-report-blog") == 1) ) {
			if (BP_ROOT_BLOG != $current_blog->blog_id) {
				echo '<li id="wangguard-report-menu-noop">';
				echo '<a href="javascript:void(0)" class="wangguard-blog-report" rel="'.$current_blog->blog_id.'">';
				echo __('Report blog and author', 'wangguard') . '</a>';
				echo '</a>';
				echo '</li>';
			}
		}
	}

}
add_action('bp_adminbar_menus', 'wangguard_add_bp_admin_bar_menus' , 10 );


/**
 * Add WangGuard to WP admin bar
 * @global type $wp_admin_bar
 * @global type $current_blog
 * @global type $current_site
 * @global type $wangguard_is_network_admin
 * @global type $wp_version
 */
function wangguard_add_wp_admin_bar_menus() {
	global $wp_admin_bar , $current_blog , $current_site , $wangguard_is_network_admin;

	if (!is_user_logged_in())
		return;
	
	$urlFunc = "admin_url";
	if ($wangguard_is_network_admin && function_exists("network_admin_url"))
		$urlFunc = "network_admin_url";

	$isMainBlog = false;
	if (defined("BP_ROOT_BLOG")) {
		$isMainBlog = ( 1 == $current_blog->blog_id || BP_ROOT_BLOG == $current_blog->blog_id );
	}
	else
		$isMainBlog = ($current_blog->blog_id == 1);
	
	$showReport = !$isMainBlog && (wangguard_get_option ("wangguard-enable-bp-report-blog")==1);
	
	
	global $wp_version;
	$cur_wp_version = preg_replace('/-.*$/', '', $wp_version);
	$WP_List_TableClassSupported = version_compare($cur_wp_version , '3.1.0' , ">=");
	

	$queueEnabled = ((wangguard_get_option("wangguard-enable-bp-report-blog") == 1) || (wangguard_get_option ("wangguard-enable-bp-report-btn")==1))  &&   $WP_List_TableClassSupported;
	
	if (function_exists("is_super_admin"))
		$showAdmin = is_super_admin();
	else
		$showAdmin = current_user_can('level_10');
	
	if ($showAdmin) {
		$wp_admin_bar->add_menu( array( 'id' => 'wangguard-admbar-splog', 'title' => __( 'WangGuard', 'wangguard' ), 'href' => $urlFunc( "admin.php?page=" . ($queueEnabled ? "wangguard_queue" : "wangguard_conf") ) ) );

		if ($showReport)
			$wp_admin_bar->add_menu( array( 'parent' => 'wangguard-admbar-splog', 'id' => "wangguard-admbar-report-blog", 'meta'=>array("class"=>"wangguard-blog-report wangguard-blog-report-id-".$current_blog->blog_id ), 'title' => __('Report blog and author', 'wangguard'), 'href' => '#' ) );

		if ($queueEnabled)
			$wp_admin_bar->add_menu( array( 'parent' => 'wangguard-admbar-splog', 'id' => "wangguard-admbar-queue", 'title' => __('Moderation Queue', 'wangguard'), 'href' => $urlFunc( "admin.php?page=wangguard_queue" ) ) );
		
		$wp_admin_bar->add_menu( array( 'parent' => 'wangguard-admbar-splog', 'id' => "wangguard-admbar-wizard", 'title' => __('Wizard', 'wangguard'), 'href' => $urlFunc( "admin.php?page=wangguard_wizard" ) ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'wangguard-admbar-splog', 'id' => "wangguard-admbar-stats", 'title' => __('Stats', 'wangguard'), 'href' => $urlFunc( "admin.php?page=wangguard_stats" ) ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'wangguard-admbar-splog', 'id' => "wangguard-admbar-settings", 'title' => __('Configuration', 'wangguard'), 'href' => $urlFunc( "admin.php?page=wangguard_conf" ) ) );
	}
	elseif ($showReport) {
		$wp_admin_bar->add_menu( array( 'id' => "wangguard-admbar-report-blog", 'meta'=>array("class"=>"wangguard-blog-report wangguard-blog-report-id-".$current_blog->blog_id ), 'title' => __('Report blog and author', 'wangguard'), 'href' => '#' ) );
	}

}

add_action('admin_bar_menu', 'wangguard_add_wp_admin_bar_menus', 100 );
/********************************************************************/
/*** ADMIN BAR REPORT BEGIN ***/
/********************************************************************/





/********************************************************************/
/*** ADMIN GROUP MENU BEGINS ***/
/********************************************************************/
/**
 * Add WangGuard to WP menu
 * 
 * @global type $menu
 * @global array $admin_page_hooks
 * @global array $_registered_pages
 * @global type $wpdb
 * @return boolean 
 */
function wangguard_add_admin_menu() {
	if ( !is_super_admin() )
		return false;

	global $menu, $admin_page_hooks, $_registered_pages , $wpdb;

	$params = array(
		'page_title' => __( 'WangGuard', 'wangguard' ),
		'menu_title' => __( 'WangGuard', 'wangguard' ),
		'access_level' => 10,
		'file' => 'wangguard_conf',
		'function' => 'wangguard_conf',
		'position' => 20
	);

	extract( $params, EXTR_SKIP );

	$file = plugin_basename( $file );

	$admin_page_hooks[$file] = sanitize_title( $menu_title );

	$hookname = get_plugin_page_hookname( $file, '' );
	if (!empty ( $function ) && !empty ( $hookname ))
		add_action( $hookname, $function );

	$position = 25;
	do {
		$position++;
	} while ( !empty( $menu[$position] ) );

	if ( empty( $icon_url ) )
		$icon_url = '';
	
	$menu[$position] = array ( $menu_title, "level_10", "wangguard_conf", $page_title, 'menu-top ' . $hookname, $hookname, $icon_url );

	$_registered_pages[$hookname] = true;

	$countSpan = "";
	$table_name = $wpdb->base_prefix . "wangguardreportqueue";
	$Count = $wpdb->get_col( "select count(*) as q from $table_name" );
	if ($Count[0] > 0)
		$countSpan = '<span class="update-plugins" ><span class="pending-count">'.$Count[0].'</span></span>';
	
	
	@include_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	
	$queueEnabled = ((wangguard_get_option("wangguard-enable-bp-report-blog") == 1) || (wangguard_get_option ("wangguard-enable-bp-report-btn")==1))  &&   class_exists('WP_List_Table');
	
	add_submenu_page( 'wangguard_conf', __( 'Configuration', 'wangguard'), __( 'Configuration', 'wangguard' ), 'manage_options', 'wangguard_conf', 'wangguard_conf' );
	
	if ($queueEnabled) 
		add_submenu_page( 'wangguard_conf', __( 'Moderation Queue', 'wangguard'), __( 'Moderation Queue', 'wangguard' ) . $countSpan, 'manage_options', 'wangguard_queue', 'wangguard_queue' );
	
	add_submenu_page( 'wangguard_conf', __( 'Wizard', 'wangguard'), __( 'Wizard', 'wangguard' ), 'manage_options', 'wangguard_wizard', 'wangguard_wizard' );
	add_submenu_page( 'wangguard_conf', __( 'Stats', 'wangguard'), __( 'Stats', 'wangguard' ), 'manage_options', 'wangguard_stats', 'wangguard_stats' );
}


if (!$wangguard_is_network_admin)
	add_action( 'admin_menu', 'wangguard_add_admin_menu' );
else
	add_action( 'network_admin_menu', 'wangguard_add_admin_menu' );
/********************************************************************/
/*** ADMIN GROUP MENU ENDS ***/
/********************************************************************/




/********************************************************************/
/*** DASHBOARD BEGINS ***/
/********************************************************************/
/**
 * Show stats box on dashboard
 */
function wangguard_dashboard_stats() {
	if ( !is_super_admin() )
		return false;

	wp_add_dashboard_widget("wangguard_dashboard_stats", __( 'WangGuard Stats' , 'wangguard' ) . " - " . __( 'Last 7 days' , 'wangguard' ) , "wangguard_dashboard_stats_render");
	
	
	global $wp_meta_boxes;
	

	if (is_array($wp_meta_boxes['dashboard']['normal']['core'])) {
		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
		$wangguard_stats_backup = $normal_dashboard['wangguard_dashboard_stats'];

		unset($wp_meta_boxes['dashboard']['normal']['core']['wangguard_dashboard_stats']);
		$wp_meta_boxes['dashboard']['side']['core']['wangguard_dashboard_stats'] = $wangguard_stats_backup;		
	}
	else if (is_array($wp_meta_boxes['dashboard-network']['normal']['core'])) {
		$normal_dashboard = $wp_meta_boxes['dashboard-network']['normal']['core'];
		$wangguard_stats_backup = $normal_dashboard['wangguard_dashboard_stats'];

		unset($wp_meta_boxes['dashboard-network']['normal']['core']['wangguard_dashboard_stats']);
		$wp_meta_boxes['dashboard-network']['side']['core']['wangguard_dashboard_stats'] = $wangguard_stats_backup;		
	}
	
}

/**
 * Renders the stats box content on dashboard
 * 
 * @global type $wangguard_api_key
 * @global type $wangguard_is_network_admin
 * @global type $wangguard_api_host
 * @global type $wangguard_rest_path
 */
function wangguard_dashboard_stats_render() {
	global $wangguard_api_key , $wangguard_is_network_admin,$wangguard_api_host,$wangguard_rest_path;

	if ( !current_user_can('level_10') )
		return;
	
	$lang = substr(WPLANG, 0,2);
	?>
	<script type="text/javascript">
		var wangguardResizeTimer;

		jQuery(document).ready(function () {
           var WGstatsURL = "http://<?php echo $wangguard_api_host . $wangguard_rest_path?>get-stat.php?wg="+ encodeURIComponent('<in><apikey><?php echo $wangguard_api_key?></apikey><last7>1</last7><lang><?php echo $lang?></lang></in>');
		   
           jQuery.ajax({
                dataType: "jsonp",
                url: WGstatsURL,
                jsonpCallback: "callback",
                success: createChart
            });

			function createChart(data) {
				jQuery("#wangguard-stats-container").wijbarchart(data);
			}
		});
	</script>

	<div id="wangguard-stats-container" class="ui-widget ui-widget-content ui-corner-all" style="width: 98%; height: 300px; margin:0 auto;"></div>
	<?php
	$urlFunc = "admin_url";
	if ($wangguard_is_network_admin && function_exists("network_admin_url"))
		$urlFunc = "network_admin_url";
		echo '<div style="text-align:center"><a href="'.$urlFunc( "admin.php?page=wangguard_stats" ).'">'.__( 'Click here to access the WangGuard stats' , 'wangguard' ).'</a></div>';
}

if ( $wangguard_is_network_admin )
	add_action( 'wp_network_dashboard_setup', 'wangguard_dashboard_stats' );
else
	add_action( 'wp_dashboard_setup', 'wangguard_dashboard_stats' );
/********************************************************************/
/*** DASHBOARD ENDS ***/
/********************************************************************/

?>