<?php
/*
Plugin Name: Events Manager
Version: 5.1.8.5
Plugin URI: http://wp-events-plugin.com
Description: Event registration and booking management for WordPress. Recurring events, locations, google maps, rss, ical, booking registration and more!
Author: Marcus Sykes
Author URI: http://wp-events-plugin.com
*/

/*
Copyright (c) 2012, Marcus Sykes

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Setting constants
define('EM_VERSION', 5.1841); //self expanatory
define('EM_PRO_MIN_VERSION', 2.144); //self expanatory
define('EM_DIR', dirname( __FILE__ )); //an absolute path to this directory
define('EM_SLUG', plugin_basename( __FILE__ )); //for updates

//EM_MS_GLOBAL
if( get_site_option('dbem_ms_global_table') && is_multisite() ){
	define('EM_MS_GLOBAL', true);
}else{
	define('EM_MS_GLOBAL',false);
}

//DEBUG MODE - currently not public, not fully tested
if( !defined('WP_DEBUG') && get_option('dbem_wp_debug') ){
	define('WP_DEBUG',true);
}
function dbem_debug_mode(){
	if( !empty($_REQUEST['dbem_debug_off']) ){
		update_option('dbem_debug',0);
		wp_redirect($_SERVER['HTTP_REFERER']);
	}
	if( current_user_can('activate_plugins') ){
		include_once('em-debug.php');
	}
}
//add_action('plugins_loaded', 'dbem_debug_mode');

// INCLUDES
include('classes/em-object.php'); //Base object, any files below may depend on this
include("em-posts.php"); //set up events as posts
//Template Tags & Template Logic
include("em-actions.php");
include("em-events.php");
include("em-emails.php");
include("em-functions.php");
include("em-ical.php");
include("em-shortcode.php");
include("em-template-tags.php");
//Widgets
include("widgets/em-events.php");
if( get_option('dbem_locations_enabled') ){
	include("widgets/em-locations.php");
}
include("widgets/em-calendar.php");
//Classes
include('classes/em-booking.php');
include('classes/em-bookings.php');
include("classes/em-bookings-table.php") ;
include('classes/em-calendar.php');
include('classes/em-category.php');
include('classes/em-category-taxonomy.php');
include('classes/em-categories.php');
include('classes/em-event.php');
include('classes/em-event-post.php');
include('classes/em-events.php');
include('classes/em-location.php');
include('classes/em-location-post.php');
include('classes/em-locations.php');
include("classes/em-mailer.php") ;
include('classes/em-notices.php');
include('classes/em-people.php');
include('classes/em-person.php');
include('classes/em-permalinks.php');
include('classes/em-tag.php');
include('classes/em-tag-taxonomy.php');
include('classes/em-ticket-booking.php');
include('classes/em-ticket.php');
include('classes/em-tickets-bookings.php');
include('classes/em-tickets.php');
//Admin Files
if( is_admin() ){
	include('admin/em-admin.php');
	include('admin/em-bookings.php');
	include('admin/em-docs.php');
	include('admin/em-help.php');
	include('admin/em-options.php');
	if( is_multisite() ){
		include('admin/em-ms-options.php');
	}
	//post/taxonomy controllers
	include('classes/em-event-post-admin.php');
	include('classes/em-event-posts-admin.php');
	include('classes/em-location-post-admin.php');
	include('classes/em-location-posts-admin.php');
	include('classes/em-categories-taxonomy.php');
	//bookings folder
		include('admin/bookings/em-cancelled.php');
		include('admin/bookings/em-confirmed.php');
		include('admin/bookings/em-events.php');
		include('admin/bookings/em-rejected.php');
		include('admin/bookings/em-pending.php');
		include('admin/bookings/em-person.php');
}

/* Only load the component if BuddyPress is loaded and initialized. */
function bp_em_init() {
	if ( version_compare( BP_VERSION, '1.3', '>' ) ){
		require( dirname( __FILE__ ) . '/buddypress/bp-em-core.php' );
	}
}
add_action( 'bp_include', 'bp_em_init' );

//Table names
global $wpdb;
if( EM_MS_GLOBAL ){
	$prefix = $wpdb->base_prefix;
}else{
	$prefix = $wpdb->prefix;
}
	define('EM_CATEGORIES_TABLE', $prefix.'em_categories'); //TABLE NAME
	define('EM_EVENTS_TABLE',$prefix.'em_events'); //TABLE NAME
	define('EM_TICKETS_TABLE', $prefix.'em_tickets'); //TABLE NAME
	define('EM_TICKETS_BOOKINGS_TABLE', $prefix.'em_tickets_bookings'); //TABLE NAME
	define('EM_META_TABLE',$prefix.'em_meta'); //TABLE NAME
	define('EM_RECURRENCE_TABLE',$prefix.'dbem_recurrence'); //TABLE NAME
	define('EM_LOCATIONS_TABLE',$prefix.'em_locations'); //TABLE NAME
	define('EM_BOOKINGS_TABLE',$prefix.'em_bookings'); //TABLE NAME

//Backward compatability for old images stored in < EM 5
if( EM_MS_GLOBAL ){
	//If in ms recurrence mode, we are getting the default wp-content/uploads folder
	$upload_dir = array(
		'basedir' => WP_CONTENT_DIR.'/uploads/',
		'baseurl' => WP_CONTENT_URL.'/uploads/'
	);
}else{
	$upload_dir = wp_upload_dir();
}
if( file_exists($upload_dir['basedir'].'/locations-pics' ) ){
	define("EM_IMAGE_UPLOAD_DIR", $upload_dir['basedir']."/locations-pics/");
	define("EM_IMAGE_UPLOAD_URI", $upload_dir['baseurl']."/locations-pics/");
	define("EM_IMAGE_DS",'-');
}else{
	define("EM_IMAGE_UPLOAD_DIR", $upload_dir['basedir']."/events-manager/");
	define("EM_IMAGE_UPLOAD_URI", $upload_dir['baseurl']."/events-manager/");
	define("EM_IMAGE_DS",'/');
}

/**
 * @author marcus
 * Contains functions for loading styles on both admin and public sides.
 */
class EM_Scripts_and_Styles {
	function init(){
		if( is_admin() ){
			//Scripts and Styles
			add_action('admin_print_styles-post.php', array('EM_Scripts_and_Styles','admin_styles'));
			add_action('admin_print_styles-post-new.php', array('EM_Scripts_and_Styles','admin_styles'));
			add_action('admin_print_styles-edit.php', array('EM_Scripts_and_Styles','admin_styles'));
			if( (!empty($_GET['page']) && substr($_GET['page'],0,14) == 'events-manager') || (!empty($_GET['post_type']) && $_GET['post_type'] == EM_POST_TYPE_EVENT) ){
				add_action('admin_print_styles', array('EM_Scripts_and_Styles','admin_styles'));
			}
			add_action('admin_print_scripts-post.php', array('EM_Scripts_and_Styles','admin_scripts'));
			add_action('admin_print_scripts-post-new.php', array('EM_Scripts_and_Styles','admin_scripts'));
			add_action('admin_print_scripts-edit.php', array('EM_Scripts_and_Styles','admin_scripts'));
			if( (!empty($_GET['page']) && substr($_GET['page'],0,14) == 'events-manager') || (!empty($_GET['post_type']) && $_GET['post_type'] == EM_POST_TYPE_EVENT) ){
				add_action('admin_print_scripts', array('EM_Scripts_and_Styles','admin_scripts'));
			}
		}else{
			add_action('init', array('EM_Scripts_and_Styles','public_enqueue'));
		}
		add_action('init', array('EM_Scripts_and_Styles','localize_script'));
	}

	/**
	 * Enqueing public scripts and styles
	 */
	function public_enqueue() {
		//Scripts
		wp_enqueue_script('events-manager', plugins_url('includes/js/events-manager.js',__FILE__), array('jquery', 'jquery-ui-core','jquery-ui-widget','jquery-ui-position','jquery-ui-sortable','jquery-ui-datepicker','jquery-ui-autocomplete','jquery-ui-dialog')); //jQuery will load as dependency
		//Styles
		wp_enqueue_style('events-manager', plugins_url('includes/css/events_manager.css',__FILE__)); //main css
	}

	/**
	 * Localize the script vars that require PHP intervention, removing the need for inline JS.
	 */
	function localize_script(){
		global $em_localized_js;
		$locale_code = substr ( get_locale(), 0, 2 );
		//Localize
		$em_localized_js = array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'bookingajaxurl' => admin_url('admin-ajax.php'),
			'locationajaxurl' => admin_url('admin-ajax.php?action=locations_search'),
			'firstDay' => get_option('start_of_week'),
			'locale' => $locale_code,
			'dateFormat' => get_option('dbem_date_format_js'),
			'bookingInProgress' => __('Please wait while the booking is being submitted.','dbem'),
			'ui_css' => plugins_url('includes/css/ui-lightness.css', __FILE__),
			'show24hours' => get_option('dbem_time_24h'),
			'is_ssl' => is_ssl(),
			'tickets_save' => __('Save Ticket','dbem'),
			'bookings_export_save' => __('Export Bookings','dbem'),
			'bookings_settings_save' => __('Save Settings','dbem'),
			'booking_delete' => __("Are you sure you want to delete?",'dbem')
		);
		$em_localized_js['txt_search'] = get_option('dbem_search_form_text_label',__('Search','dbem'));
		$em_localized_js['txt_searching'] = __('Searching...','dbem');
		$em_localized_js['txt_loading'] = __('Loading...','dbem');
		//logged in messages that visitors shouldn't need to see
		if( is_user_logged_in() ){
			$em_localized_js['event_reschedule_warning'] = __('Are you sure you want to reschedule this recurring event? If you do this, you will lose all booking information and the old recurring events will be deleted.', 'dbem');
			$em_localized_js['disable_bookings_warning'] = __('Are you sure you want to disable bookings? If you do this and save, you will lose all previous bookings. If you wish to prevent further bookings, reduce the number of spaces available to the amount of bookings you currently have', 'dbem');
			$em_localized_js['event_detach_warning'] = __('Are you sure you want to detach this event? By doing so, this event will be independent of the recurring set of events.', 'dbem');
			$delete_text = ( !EMPTY_TRASH_DAYS ) ? __('This cannot be undone.','dbem'):__('All events will be moved to trash.','dbem');
			$em_localized_js['delete_recurrence_warning'] = __('Are you sure you want to delete all recurrences of this event?', 'dbem').' '.$delete_text;
			$em_localized_js['booking_warning_cancel'] = get_option('dbem_booking_warning_cancel');
		}
		//load admin/public only vars
		if( is_admin() ){
			$em_localized_js['event_post_type'] = EM_POST_TYPE_EVENT;
			$em_localized_js['location_post_type'] = EM_POST_TYPE_LOCATION;
		}
		wp_localize_script('events-manager','EM', apply_filters('em_wp_localize_script', $em_localized_js));
	}

	function admin_styles(){
		global $post;
		wp_enqueue_style('events-manager-admin', WP_PLUGIN_URL.'/events-manager/includes/css/events_manager_admin.css');
	}
	function admin_scripts(){
		global $post;
		wp_enqueue_script('events-manager', WP_PLUGIN_URL.'/events-manager/includes/js/events-manager.js', array('jquery', 'jquery-ui-core','jquery-ui-widget','jquery-ui-position','jquery-ui-sortable','jquery-ui-datepicker','jquery-ui-autocomplete','jquery-ui-dialog'));
		self::localize_script();
	}
}
EM_Scripts_and_Styles::init();
function em_enqueue_public(){ EM_Scripts_and_Styles::public_enqueue(); } //In case ppl used this somewhere

/**
 * Perform plugins_loaded actions
 */
function em_plugins_loaded(){
	//Capabilities
	global $em_capabilities_array;
	$em_capabilities_array = apply_filters('em_capabilities_array', array(
		/* Booking Capabilities */
		'manage_others_bookings' => sprintf(__('You do not have permission to manage others %s','dbem'),__('bookings','dbem')),
		'manage_bookings' => sprintf(__('You do not have permission to manage %s','dbem'),__('bookings','dbem')),
		/* Event Capabilities */
		'publish_events' => sprintf(__('You do not have permission to publish %s','dbem'),__('events','dbem')),
		'delete_others_events' => sprintf(__('You do not have permission to delete others %s','dbem'),__('events','dbem')),
		'delete_events' => sprintf(__('You do not have permission to delete %s','dbem'),__('events','dbem')),
		'edit_others_events' => sprintf(__('You do not have permission to edit others %s','dbem'),__('events','dbem')),
		'edit_events' => sprintf(__('You do not have permission to edit %s','dbem'),__('events','dbem')),
		'read_private_events' => sprintf(__('You cannot read private %s','dbem'),__('events','dbem')),
		/*'read_events' => sprintf(__('You cannot view %s','dbem'),__('events','dbem')),*/
		/* Recurring Event Capabilties */
		'publish_recurring_events' => sprintf(__('You do not have permission to publish %s','dbem'),__('recurring events','dbem')),
		'delete_others_recurring_events' => sprintf(__('You do not have permission to delete others %s','dbem'),__('recurring events','dbem')),
		'delete_recurring_events' => sprintf(__('You do not have permission to delete %s','dbem'),__('recurring events','dbem')),
		'edit_others_recurring_events' => sprintf(__('You do not have permission to edit others %s','dbem'),__('recurring events','dbem')),
		'edit_recurring_events' => sprintf(__('You do not have permission to edit %s','dbem'),__('recurring events','dbem')),
		/* Location Capabilities */
		'publish_locations' => sprintf(__('You do not have permission to publish %s','dbem'),__('locations','dbem')),
		'delete_others_locations' => sprintf(__('You do not have permission to delete others %s','dbem'),__('locations','dbem')),
		'delete_locations' => sprintf(__('You do not have permission to delete %s','dbem'),__('locations','dbem')),
		'edit_others_locations' => sprintf(__('You do not have permission to edit others %s','dbem'),__('locations','dbem')),
		'edit_locations' => sprintf(__('You do not have permission to edit %s','dbem'),__('locations','dbem')),
		'read_private_locations' => sprintf(__('You cannot read private %s','dbem'),__('locations','dbem')),
		'read_others_locations' => sprintf(__('You cannot view others %s','dbem'),__('locations','dbem')),
		/*'read_locations' => sprintf(__('You cannot view %s','dbem'),__('locations','dbem')),*/
		/* Category Capabilities */
		'delete_event_categories' => sprintf(__('You do not have permission to delete %s','dbem'),__('categories','dbem')),
		'edit_event_categories' => sprintf(__('You do not have permission to edit %s','dbem'),__('categories','dbem')),
		/* Upload Capabilities */
		'upload_event_images' => __('You do not have permission to upload images','dbem')
	));
	// LOCALIZATION
	load_plugin_textdomain('dbem', false, dirname( plugin_basename( __FILE__ ) ).'/includes/langs');
}
add_filter('plugins_loaded','em_plugins_loaded');

/**
 * Perform init actions
 */
function em_init(){
	//Hard Links
	global $EM_Mailer, $wp_rewrite;
	if( get_option("dbem_events_page") > 0 ){
		define('EM_URI', get_permalink(get_option("dbem_events_page"))); //PAGE URI OF EM
	}else{
		if( $wp_rewrite->using_permalinks() ){
			define('EM_URI', trailingslashit(home_url()).'/'.EM_POST_TYPE_EVENT_SLUG.'/'); //PAGE URI OF EM
		}else{
			define('EM_URI', trailingslashit(home_url()).'?post_type='.EM_POST_TYPE_EVENT); //PAGE URI OF EM
		}
	}
	if( $wp_rewrite->using_permalinks() ){
		define('EM_RSS_URI', trailingslashit(EM_URI)."rss/"); //RSS PAGE URI
	}else{
		if( get_option("dbem_events_page") > 0 ){
			define('EM_RSS_URI', EM_URI."&rss=1"); //RSS PAGE URI
		}else{
			define('EM_RSS_URI', EM_URI."&feed=rss2"); //RSS PAGE URI
		}
	}
	$EM_Mailer = new EM_Mailer();
	//Upgrade/Install Routine
	if( is_admin() && current_user_can('activate_plugins') ){
		if( EM_VERSION > get_option('dbem_version', 0) ){
			require_once( dirname(__FILE__).'/em-install.php');
			em_install();
		}
	}
	//add custom functions.php file
	locate_template('plugins/events-manager/functions.php', true);
}
add_filter('init','em_init',1);

/**
 * This function will load an event into the global $EM_Event variable during page initialization, provided an event_id is given in the url via GET or POST.
 * global $EM_Recurrences also holds global array of recurrence objects when loaded in this instance for performance
 * All functions (admin and public) can now work off this object rather than it around via arguments.
 * @return null
 */
function em_load_event(){
	global $EM_Event, $EM_Recurrences, $EM_Location, $EM_Person, $EM_Booking, $EM_Category, $EM_Ticket, $current_user;
	if( !defined('EM_LOADED') ){
		$EM_Recurrences = array();
		if( isset( $_REQUEST['event_id'] ) && is_numeric($_REQUEST['event_id']) && !is_object($EM_Event) ){
			$EM_Event = new EM_Event($_REQUEST['event_id']);
		}elseif( isset($_REQUEST['post']) && (get_post_type($_REQUEST['post']) == 'event' || get_post_type($_REQUEST['post']) == 'event-recurring') ){
			$EM_Event = em_get_event($_REQUEST['post'], 'post_id');
		}elseif ( !empty($_REQUEST['event_slug']) && EM_MS_GLOBAL && is_main_blog() && !get_site_option('dbem_ms_global_events_links')) {
			// single event page for a subsite event being shown on the main blog
			global $wpdb;
			$matches = array();
			if( preg_match('/\-([0-9]+)$/', $_REQUEST['event_slug'], $matches) ){
				$event_id = $matches[1];
			}else{
				$event_id = $wpdb->get_var('SELECT event_id FROM '.EM_EVENTS_TABLE." WHERE event_slug='{$_REQUEST['event_slug']}' AND blog_id!=".get_current_blog_id());
			}
			$EM_Event = em_get_event($event_id);
		}
		if( isset($_REQUEST['location_id']) && is_numeric($_REQUEST['location_id']) && !is_object($EM_Location) ){
			$EM_Location = new EM_Location($_REQUEST['location_id']);
		}elseif( isset($_REQUEST['post']) && get_post_type($_REQUEST['post']) == 'location' ){
			$EM_Location = em_get_location($_REQUEST['post'], 'post_id');
		}elseif ( !empty($_REQUEST['location_slug']) && EM_MS_GLOBAL && is_main_blog() && !get_site_option('dbem_ms_global_locations_links')) {
			// single event page for a subsite event being shown on the main blog
			global $wpdb;
			$matches = array();
			if( preg_match('/\-([0-9]+)$/', $_REQUEST['location_slug'], $matches) ){
				$location_id = $matches[1];
			}else{
				$location_id = $wpdb->get_var('SELECT location_id FROM '.EM_LOCATIONS_TABLE." WHERE location_slug='{$_REQUEST['location_slug']}' AND blog_id!=".get_current_blog_id());
			}
			$EM_Location = em_get_location($location_id);
		}
		if( is_user_logged_in() || (!empty($_REQUEST['person_id']) && is_numeric($_REQUEST['person_id'])) ){
			//make the request id take priority, this shouldn't make it into unwanted objects if they use theobj::get_person().
			if( !empty($_REQUEST['person_id']) ){
				$EM_Person = new EM_Person( $_REQUEST['person_id'] );
			}else{
				$EM_Person = new EM_Person( get_current_user_id() );
			}
		}
		if( isset($_REQUEST['booking_id']) && is_numeric($_REQUEST['booking_id']) && !is_object($_REQUEST['booking_id']) ){
			$EM_Booking = new EM_Booking($_REQUEST['booking_id']);
		}
		if( isset($_REQUEST['category_id']) && is_numeric($_REQUEST['category_id']) && !is_object($_REQUEST['category_id']) ){
			$EM_Category = new EM_Category($_REQUEST['category_id']);
		}elseif( isset($_REQUEST['category_slug']) && !is_object($EM_Category) ){
			$EM_Category = new EM_Category( $_REQUEST['category_slug'] );
		}
		if( isset($_REQUEST['ticket_id']) && is_numeric($_REQUEST['ticket_id']) && !is_object($_REQUEST['ticket_id']) ){
			$EM_Ticket = new EM_Ticket($_REQUEST['ticket_id']);
		}
		define('EM_LOADED',true);
	}
}
add_action('template_redirect', 'em_load_event', 1);
if(is_admin()){ add_action('init', 'em_load_event', 2); }

/**
 * Catches various option names and returns a network-wide option value instead of the individual blog option. Uses the magc __call function to catch unprecedented names.
 * @author marcus
 *
 */
class EM_MS_Globals {
	function __construct(){ add_action( 'init', array(&$this, 'add_filters'), 1); }
	function add_filters(){
		foreach( $this->get_globals() as $global_option_name ){
			add_filter('pre_option_'.$global_option_name, array(&$this, 'pre_option_'.$global_option_name), 1,1);
			add_filter('pre_update_option_'.$global_option_name, array(&$this, 'pre_update_option_'.$global_option_name), 1,2);
			add_action('add_option_'.$global_option_name, array(&$this, 'add_option_'.$global_option_name), 1,1);
		}
	}
	function get_globals(){
		$globals = array(
			//multisite settings
			'dbem_ms_global_table', 'dbem_ms_global_caps',
			'dbem_ms_global_events', 'dbem_ms_global_events_links','dbem_ms_events_slug',
			'dbem_ms_global_locations','dbem_ms_global_locations_links','dbem_ms_locations_slug','dbem_ms_mainblog_locations',
			//mail
			'dbem_rsvp_mail_port', 'dbem_mail_sender_address', 'dbem_smtp_password', 'dbem_smtp_username','dbem_smtp_host', 'dbem_mail_sender_name','dbem_smtp_host','dbem_rsvp_mail_send_method','dbem_rsvp_mail_SMTPAuth',
			//images
			'dbem_image_max_width','dbem_image_max_height','dbem_image_max_size'
		);
		if( EM_MS_GLOBAL ){
			$globals[] = 'dbem_taxonomy_category_slug';
		}
		return apply_filters('em_ms_globals', $globals);
	}
	function __call($filter_name, $value){
		if( strstr($filter_name, 'pre_option_') !== false ){
			$return = get_site_option(str_replace('pre_option_','',$filter_name));
			return $return;
		}elseif( strstr($filter_name, 'pre_update_option_') !== false ){
			if( is_super_admin() ){
				update_site_option(str_replace('pre_update_option_','',$filter_name), $value[0]);
			}
			return $value[1];
		}elseif( strstr($filter_name, 'add_option_') !== false ){
			if( is_super_admin() ){
				update_site_option(str_replace('add_option_','',$filter_name),$value[0]);
			}
			delete_option(str_replace('pre_option_','',$filter_name));
			return;
		}
		return $value[0];
	}
}
if( defined('MULTISITE') && MULTISITE ){
	global $EM_MS_Globals;
	$EM_MS_Globals = new EM_MS_Globals();
}

/**
 * Works much like <a href="http://codex.wordpress.org/Function_Reference/locate_template" target="_blank">locate_template</a>, except it takes a string instead of an array of templates, we only need to load one.
 * @param string $template_name
 * @param boolean $load
 * @uses locate_template()
 * @return string
 */
function em_locate_template( $template_name, $load=false, $args = array() ) {
	//First we check if there are overriding tempates in the child or parent theme
	$located = locate_template(array('plugins/events-manager/'.$template_name));
	if( !$located ){
		if ( file_exists(EM_DIR.'/templates/'.$template_name) ) {
			$located = EM_DIR.'/templates/'.$template_name;
		}
	}
	$located = apply_filters('em_locate_template', $located, $template_name, $load, $args);
	if( $located && $load ){
		if( is_array($args) ) extract($args);
		include($located);
	}
	return $located;
}

/**
 * Quick class to dynamically catch wp_options that are EM formats and need replacing with template files.
 * Since the options filter doesn't have a catchall filter, we send all filters to the __call function and figure out the option that way.
 */
class EM_Formats {
	function __construct(){ add_action( 'template_redirect', array(&$this, 'add_filters')); }
	function add_filters(){
		//you can hook into this filter and activate the format options you want to override by supplying the wp option names in an array, just like in the database.
		$formats = apply_filters('em_formats_filter', array());
		foreach( $formats as $format_name ){
			add_filter('pre_option_'.$format_name, array(&$this, $format_name), 1,1);
		}
	}
	function __call( $name, $value ){
		$format = em_locate_template( 'formats/'.substr($name, 5).'.php' );
		if( $format ){
			ob_start();
			include($format);
			$value[0] = ob_get_clean();
		}
		return $value[0];
	}
}
global $EM_Formats;
$EM_Formats = new EM_Formats();

/**
 * Catches the event rss feed requests
 */
function em_rss() {
	global $post, $wp_query;
	if ( is_object($post) && $post->ID == get_option('dbem_events_page') && $wp_query->get('rss') ) {
		ob_start();
		em_locate_template('templates/rss.php', true);
		echo apply_filters('em_rss', ob_get_clean());
		die ();
	}
}
add_action ( 'template_redirect', 'em_rss' );

/**
 * Monitors event saves and changes the rss pubdate so it's current
 * @param boolean $result
 * @return boolean
 */
function em_rss_pubdate_change($result){
	if($result) update_option('em_rss_pubdate', date('D, d M Y H:i:s ', current_time('timestamp', true)).'GMT');
	return $result;
}
add_filter('em_event_save', 'em_rss_pubdate_change', 10,1);

function em_admin_bar_mod($wp_admin_bar){
	$wp_admin_bar->add_menu( array(
		'parent' => 'network-admin',
		'id'     => 'network-admin-em',
		'title'  => __( 'Events Manager','dbem' ),
		'href'   => network_admin_url('admin.php?page=events-manager-options'),
	) );
}
add_action( 'admin_bar_menu', 'em_admin_bar_mod', 21 );


function em_activate() {
	update_option('dbem_flush_needed',1);
}
register_activation_hook( __FILE__,'em_activate');

/* Creating the wp_events table to store event data*/
function em_deactivate() {
	global $wp_rewrite;
   	$wp_rewrite->flush_rules();
}
register_deactivation_hook( __FILE__,'em_deactivate');
?>