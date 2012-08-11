<?php
/*
Plugin Name: Q&A - WordPress Questions and Answers Plugin
Plugin URI: http://premium.wpmudev.org/project/qa-wordpress-questions-and-answers-plugin
Description: Q&A allows any WordPress site to have a fully featured questions and answers section - just like StackOverflow, Yahoo Answers, Quora and more...
Author: S H Mohanjith (Incsub), scribu (Incsub), Hakan Evin (Incsub)
Version: 1.3.0
Author URI: http://premium.wpmudev.org/
WDP ID: 217
Text Domain: qa
*/

/*

Copyright 2007-2011 Incsub, (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/


///////////////////////////////////////////////////////////////////////////
/* -------------------- WPMU DEV Dashboard Notice -------------------- */
if ( !class_exists('WPMUDEV_Dashboard_Notice') ) {
	class WPMUDEV_Dashboard_Notice {
		
		var $version = '2.0';
		
		function WPMUDEV_Dashboard_Notice() {
			add_action( 'plugins_loaded', array( &$this, 'init' ) ); 
		}
		
		function init() {
			if ( !class_exists( 'WPMUDEV_Update_Notifications' ) && current_user_can( 'install_plugins' ) && is_admin() ) {
				remove_action( 'admin_notices', 'wdp_un_check', 5 );
				remove_action( 'network_admin_notices', 'wdp_un_check', 5 );
				if ( file_exists(WP_PLUGIN_DIR . '/wpmudev-updates/update-notifications.php') ) {
					add_action( 'all_admin_notices', array( &$this, 'activate_notice' ), 5 );
				} else {
					add_action( 'all_admin_notices', array( &$this, 'install_notice' ), 5 );
					add_filter( 'plugins_api', array( &$this, 'filter_plugin_info' ), 10, 3 );
				}
			}
		}
		
		function filter_plugin_info($res, $action, $args) {
			global $wp_version;
			$cur_wp_version = preg_replace('/-.*$/', '', $wp_version);
		
			if ( $action == 'plugin_information' && strpos($args->slug, 'install_wpmudev_dash') !== false ) {
				$res = new stdClass;
				$res->name = 'WPMU DEV Dashboard';
				$res->slug = 'wpmu-dev-dashboard';
				$res->version = '';
				$res->rating = 100;
				$res->homepage = 'http://premium.wpmudev.org/project/wpmu-dev-dashboard/';
				$res->download_link = "http://premium.wpmudev.org/wdp-un.php?action=install_wpmudev_dash";
				$res->tested = $cur_wp_version;
				
				return $res;
			}
	
			return false;
		}
	
		function auto_install_url() {
			$function = is_multisite() ? 'network_admin_url' : 'admin_url';
			return wp_nonce_url($function("update.php?action=install-plugin&plugin=install_wpmudev_dash"), "install-plugin_install_wpmudev_dash");
		}
		
		function activate_url() {
			$function = is_multisite() ? 'network_admin_url' : 'admin_url';
			return wp_nonce_url($function('plugins.php?action=activate&plugin=wpmudev-updates%2Fupdate-notifications.php'), 'activate-plugin_wpmudev-updates/update-notifications.php');
		}
		
		function install_notice() {
			echo '<div class="error fade"><p>' . sprintf(__('Easily get updates, support, and one-click WPMU DEV plugin/theme installations right from in your dashboard - <strong><a href="%s" title="Install Now &raquo;">install the free WPMU DEV Dashboard plugin</a></strong>. &nbsp;&nbsp;&nbsp;<small><a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">(find out more)</a></small>', 'wpmudev'), $this->auto_install_url()) . '</a></p></div>';
		}
		
		function activate_notice() {
			echo '<div class="updated fade"><p>' . sprintf(__('Updates, Support, Premium Plugins, Community - <strong><a href="%s" title="Activate Now &raquo;">activate the WPMU DEV Dashboard plugin now</a></strong>.', 'wpmudev'), $this->activate_url()) . '</a></p></div>';
		}
	
	}
	new WPMUDEV_Dashboard_Notice();
}


// Check if we already have Q&A or Q&A Lite installed and running
if ( !class_exists( 'QA_Core' ) ) {

	// The plugin version
	define( 'QA_VERSION', '1.3.0' );

	// The full url to the plugin directory
	if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || $_SERVER['SERVER_PORT'] == '443') {
		define( 'QA_PLUGIN_URL', preg_replace('/^http:/', 'https:', WP_PLUGIN_URL) . '/' . basename( dirname( __FILE__ ) ) . '/' );
	} else {
		define( 'QA_PLUGIN_URL', WP_PLUGIN_URL . '/' . basename( dirname( __FILE__ ) ) . '/' );
	}

	// The full path to the plugin directory
	define( 'QA_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) . '/' );

	// The text domain for strings localization
	define( 'QA_TEXTDOMAIN', 'qa' );

	// The key for the options array
	define( 'QA_OPTIONS_NAME', 'qa_options' );

	// The minimum number of seconds between two user posts
	if (!defined('QA_FLOOD_SECONDS')) define( 'QA_FLOOD_SECONDS', 10 );

	// Rewrite slugs
	if (!defined('QA_SLUG_ROOT')) define( 'QA_SLUG_ROOT','questions' );
	if (!defined('QA_SLUG_ASK')) define( 'QA_SLUG_ASK', 'ask' );
	if (!defined('QA_SLUG_EDIT')) define( 'QA_SLUG_EDIT', 'edit' );
	if (!defined('QA_SLUG_UNANSWERED')) define( 'QA_SLUG_UNANSWERED', 'unanswered' );
	if (!defined('QA_SLUG_TAGS')) define( 'QA_SLUG_TAGS', 'tags' );
	if (!defined('QA_SLUG_CATEGORIES')) define( 'QA_SLUG_CATEGORIES', 'categories' );
	if (!defined('QA_SLUG_USER')) define( 'QA_SLUG_USER', 'user' );

	// Reputation multipliers
	if (!defined('QA_ANSWER_ACCEPTED')) define( 'QA_ANSWER_ACCEPTED', 15 );
	if (!defined('QA_ANSWER_ACCEPTING')) define( 'QA_ANSWER_ACCEPTING', 2 );
	if (!defined('QA_ANSWER_UP_VOTE')) define( 'QA_ANSWER_UP_VOTE', 10 );
	if (!defined('QA_QUESTION_UP_VOTE')) define( 'QA_QUESTION_UP_VOTE', 5 );
	if (!defined('QA_DOWN_VOTE')) define( 'QA_DOWN_VOTE', -2 );
	if (!defined('QA_DOWN_VOTE_PENALTY')) define( 'QA_DOWN_VOTE_PENALTY', -1 );



	if (!defined('QA_DEFAULT_TEMPLATE_DIR')) define( 'QA_DEFAULT_TEMPLATE_DIR', 'default-templates' );

	global $qa_email_notification_content, $qa_email_notification_subject;

	$qa_email_notification_subject = "[SITE_NAME] New Question";  // SITE_NAME
	$qa_email_notification_content = "Dear TO_USER,

	New question was posted on SITE_NAME.

	QUESTION_TITLE

	QUESTION_DESCRIPTION

	If you wish to answer it please goto QUESTION_LINK.

	Thanks,
	SITE_NAME";

	// Load plugin files
	include_once QA_PLUGIN_DIR . 'core/core.php';
	include_once QA_PLUGIN_DIR . 'core/answers.php';
	include_once QA_PLUGIN_DIR . 'core/edit.php';
	include_once QA_PLUGIN_DIR . 'core/votes.php';
	include_once QA_PLUGIN_DIR . 'core/subscriptions.php';
	include_once QA_PLUGIN_DIR . 'core/functions.php';
	include_once QA_PLUGIN_DIR . 'core/template-tags.php';
	include_once QA_PLUGIN_DIR . 'core/widgets.php';
	include_once QA_PLUGIN_DIR . 'core/ajax.php';

	function qa_bp_integration() {
		include_once QA_PLUGIN_DIR . 'core/buddypress.php';
	}
	add_action( 'bp_loaded', 'qa_bp_integration' );

	if ( is_admin() ) {
		include_once QA_PLUGIN_DIR . 'core/admin.php';
	}
}
else {
	if ( is_multisite() ) 
		add_action( 'network_admin_notices', 'wpmudev_qa_duplicate' );
	else
		add_action( 'admin_notices', 'wpmudev_qa_duplicate' );
}

if ( !function_exists( 'wpmudev_qa_duplicate' ) ) {
	function wpmudev_qa_duplicate() {
		echo '<div class="error fade"><p>' . 
			__("<b>[Q&A]</b> There is already a running version of Q&A. Please check if you have already installed Q&A or Q&A Lite beforehand. You need to deactivate the other version to install and run this.", QA_TEXTDOMAIN) .
			'</p></div>';

	}
}

if ( !function_exists( 'wpmudev_qa_uninstall' ) ) {
	function wpmudev_qa_uninstall() {
		remove_role( 'visitor' );
		/* Uninstall options only if Q&A Lite is not installed
		In other words, uninstall when Q&A is installed alone
		*/
		if ( !file_exists( WP_PLUGIN_DIR ."/qa-lite/qa-lite.php" ) ) {
			delete_option( 'qa_no_visit' );
			delete_option( 'qa_installed_version' );
			delete_option( 'qa_capabilties_set' );
			delete_option( QA_OPTIONS_NAME );
			delete_option( 'qa_email_notification_subject' );
			delete_option( 'qa_email_notification_content' );
		}
	}
}
register_uninstall_hook(  __FILE__ , 'wpmudev_qa_uninstall' );
