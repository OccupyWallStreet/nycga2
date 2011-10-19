<?php
/*
Plugin Name: Events Manager
Version: 4.212
Plugin URI: http://wp-events-plugin.com
Description: Event registration and booking management for Wordpress. Recurring events, locations, google maps, rss, ical, booking registration and more!
Author: Marcus Sykes
Author URI: http://wp-events-plugin.com
*/

/*
Copyright (c) 2011, Marcus Sykes

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
add_action('plugins_loaded', 'dbem_debug_mode');

// INCLUDES 
include_once('classes/em-object.php'); //Base object, any files below may depend on this 
//Template Tags & Template Logic
include_once("em-actions.php");
include_once("em-events.php");
include_once("em-functions.php");
include_once("em-ical.php");
include_once("em-shortcode.php");
include_once("em-template-tags.php");
include_once("em-template-tags-depreciated.php"); //To depreciate
//Widgets
include_once("widgets/em-events.php");
include_once("widgets/em-locations.php");
include_once("widgets/em-calendar.php");
//Classes
include_once('classes/em-booking.php');
include_once('classes/em-bookings.php');
include_once('classes/em-calendar.php');
include_once('classes/em-category.php');
include_once('classes/em-categories.php');
include_once('classes/em-event.php');
include_once('classes/em-events.php');
include_once('classes/em-location.php');
include_once('classes/em-locations.php');
include_once("classes/em-mailer.php") ;
include_once('classes/em-notices.php');
include_once('classes/em-people.php');
include_once('classes/em-person.php');
include_once('classes/em-permalinks.php');
include_once('classes/em-ticket-booking.php');
include_once('classes/em-ticket.php');
include_once('classes/em-tickets-bookings.php');
include_once('classes/em-tickets.php');
//Admin Files
if( is_admin() ){
	include_once('admin/em-admin.php');
	include_once('admin/em-bookings.php');
	include_once('admin/em-categories.php');
	include_once('admin/em-docs.php');
	include_once('admin/em-event.php');
	include_once('admin/em-events.php');
	include_once('admin/em-help.php');
	include_once('admin/em-locations.php');
	include_once('admin/em-options.php');
	include_once('admin/em-people.php');
	//bookings folder
		include_once('admin/bookings/em-cancelled.php');
		include_once('admin/bookings/em-confirmed.php');
		include_once('admin/bookings/em-events.php');
		include_once('admin/bookings/em-rejected.php');
		include_once('admin/bookings/em-pending.php');
		include_once('admin/bookings/em-person.php');
}
/* Only load the component if BuddyPress is loaded and initialized. */
function bp_em_init() {
	require( dirname( __FILE__ ) . '/buddypress/bp-em-core.php' );
}
add_action( 'bp_init', 'bp_em_init' );


// Setting constants
define('EM_VERSION', 4.212); //self expanatory
define('EM_PRO_MIN_VERSION', 1.344); //self expanatory
define('EM_DIR', dirname( __FILE__ )); //an absolute path to this directory
if( get_site_option('dbem_ms_global_table') && is_multisite() ){
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

//Table names
global $wpdb;
if( get_site_option('dbem_ms_global_table') ){
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
	define('EM_PEOPLE_TABLE',$prefix.'em_people'); //TABLE NAME
	define('EM_MIN_CAPABILITY', $prefix.'edit_events');	// Minimum user level to add events
	define('EM_EDITOR_CAPABILITY',$prefix. 'publish_events');	// Minimum user level to access calendars
	define('EM_SETTING_CAPABILITY', $prefix.'activate_plugins'); // Minimum user level to edit settings in EM

// Localised date formats as in the jquery UI datepicker plugin but for php date
$localised_date_formats = array("am" => "d.m.Y","ar" => "d/m/Y", "bg" => "d.m.Y", "ca" => "m/d/Y", "cs" => "d.m.Y", "da" => "d-m-Y", "de" =>"d.m.Y", "es" => "d/m/Y", "en" => "m/d/Y", "fi" => "d.m.Y", "fr" => "d/m/Y", "he" => "d/m/Y", "hu" => "Y-m-d", "hy" => "d.m.Y", "id" => "d/m/Y", "is" => "d/m/Y", "it" => "d/m/Y", "ja" => "Y/m/d", "ko" => "Y-m-d", "lt" => "Y-m-d", "lv" => "d-m-Y", "nl" => "d.m.Y", "no" => "Y-m-d", "pl" => "Y-m-d", "pt" => "d/m/Y", "ro" => "m/d/Y", "ru" => "d.m.Y", "sk" => "d.m.Y", "sv" => "Y-m-d", "th" => "d/m/Y", "tr" => "d.m.Y", "ua" => "d.m.Y", "uk" => "d.m.Y", "us" => "m/d/Y", "CN" => "Y-m-d", "TW" => "Y/m/d");
//TODO reorganize how defaults are created, e.g. is it necessary to create false entries? They are false by default... less code, but maybe not verbose enough...
       

// FILTERS
// filters for general events field (corresponding to those of  "the _title")
add_filter('dbem_general', 'wptexturize');
add_filter('dbem_general', 'convert_chars');
add_filter('dbem_general', 'trim');
// filters for the notes field  (corresponding to those of  "the _content")   
add_filter('dbem_notes', 'wptexturize');
add_filter('dbem_notes', 'convert_smilies');
add_filter('dbem_notes', 'convert_chars');
add_filter('dbem_notes', 'wpautop');
add_filter('dbem_notes', 'prepend_attachment');
// RSS general filters
add_filter('dbem_general_rss', 'strip_tags');
add_filter('dbem_general_rss', 'ent2ncr', 8);
add_filter('dbem_general_rss', 'esc_html');
// RSS content filter
add_filter('dbem_notes_rss', 'convert_chars', 8);    
add_filter('dbem_notes_rss', 'ent2ncr', 8);
// Notes map filters
add_filter('dbem_notes_map', 'convert_chars', 8);
add_filter('dbem_notes_map', 'js_escape');

/**
 * Enqueing public scripts and styles 
 */
function em_enqueue_public() {
	//Scripts
	wp_enqueue_script('events-manager', WP_PLUGIN_URL.'/events-manager/includes/js/events-manager.js', array('jquery', 'jquery-ui-core','jquery-ui-widget','jquery-ui-position')); //jQuery will load as dependency
	//Styles
	//wp_enqueue_style('em-ui-css', WP_PLUGIN_URL.'/events-manager/includes/css/jquery-ui-1.8.13.custom.css');
	wp_enqueue_style('events-manager', WP_PLUGIN_URL.'/events-manager/includes/css/events_manager.css'); //main css
	em_js_localize_vars();
}
if(!is_admin()){ add_action ( 'init', 'em_enqueue_public' ); }

function em_js_localize_vars(){
	//Localise vars regardless
	$locale_code = substr ( WPLANG, 0, 2 );
	if( WPLANG == 'en_GB'){
		$locale_code = 'en-GB';
	}
	wp_localize_script('events-manager','EM', array(
		'ajaxurl' => admin_url('admin-ajax.php'),
		'locationajaxurl' => admin_url('admin-ajax.php?action=locations_search'),
		'firstDay' => get_option('start_of_week'),
		'locale' => $locale_code,
		'bookingInProgress' => __('Please wait while the booking is being submitted.','dbem'),
		'ui_css' => WP_PLUGIN_URL.'/events-manager/includes/css/jquery-ui-1.8.13.custom.css'
	));
}

/**
 * Perform plugins_loaded actions 
 */
function em_plugins_loaded(){
	//Capabilities
	global $em_capabilities_array;
	$em_capabilities_array = apply_filters('em_capabilities_array', array(
		'publish_events' => sprintf(__('You do not have permission to publish %s','dbem'),__('events','dbem')),
		'edit_categories' => sprintf(__('You do not have permission to edit %s','dbem'),__('categories','dbem')),
		'delete_others_events' => sprintf(__('You do not have permission to delete others %s','dbem'),__('events','dbem')),
		'delete_others_locations' => sprintf(__('You do not have permission to delete others %s','dbem'),__('locations','dbem')),
		'edit_others_locations' => sprintf(__('You do not have permission to edit others %s','dbem'),__('locations','dbem')),
		'manage_others_bookings' => sprintf(__('You do not have permission to manage others %s','dbem'),__('bookings','dbem')),
		'edit_others_events' => sprintf(__('You do not have permission to edit others %s','dbem'),__('events','dbem')),
		'delete_locations' => sprintf(__('You do not have permission to delete %s','dbem'),__('locations','dbem')),
		'delete_events' => sprintf(__('You do not have permission to delete %s','dbem'),__('events','dbem')),
		'edit_locations' => sprintf(__('You do not have permission to edit %s','dbem'),__('locations','dbem')),
		'manage_bookings' => sprintf(__('You do not have permission to manage %s','dbem'),__('bookings','dbem')),
		'read_others_locations' => sprintf(__('You cannot to view others %s','dbem'),__('locations','dbem')),
		'edit_recurrences' => sprintf(__('You do not have permission to edit %s','dbem'),__('recurrences','dbem')),
		'edit_events' => sprintf(__('You do not have permission to edit %s','dbem'),__('events','dbem'))
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
	global $EM_Mailer, $wpdb, $wp_rewrite;
	define('EM_URI', get_permalink(get_option("dbem_events_page"))); //PAGE URI OF EM 
	if( $wp_rewrite->using_permalinks() ){
		define('EM_RSS_URI', trailingslashit(EM_URI)."rss/"); //RSS PAGE URI
	}else{
		define('EM_RSS_URI', EM_URI."&rss=1"); //RSS PAGE URI
	}
	$EM_Mailer = new EM_Mailer();
	//Upgrade/Install Routine
	if( is_admin() && current_user_can('activate_plugins') ){
		if( EM_VERSION > get_option('dbem_version', 0) ){
			require_once( dirname(__FILE__).'/em-install.php');
			em_install();
		}
	}
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
		}elseif( isset($_REQUEST['event_slug']) && !is_object($EM_Event) ){
			$EM_Event = new EM_Event( $_REQUEST['event_slug'] );
		}
		if( isset($_REQUEST['location_id']) && is_numeric($_REQUEST['location_id']) && !is_object($EM_Location) ){
			$EM_Location = new EM_Location($_REQUEST['location_id']);
		}elseif( isset($_REQUEST['location_slug']) && !is_object($EM_Location) ){
			$EM_Location = new EM_Location($_REQUEST['location_slug']);
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


// Create the Manage Events and the Options submenus  
function em_create_events_submenu () {
	if(function_exists('add_submenu_page')) {
		//Count pending bookings
		$bookings_num = '';
		$bookings_pending_count = apply_filters('em_bookings_pending_count',0);
		if( get_option('dbem_bookings_approval') == 1){ 
			$bookings_pending_count += count(EM_Bookings::get(array('status'=>'0'))->bookings);
		}
		if($bookings_pending_count > 0){
			$bookings_num = '<span class="update-plugins count-'.$bookings_pending_count.'"><span class="plugin-count">'.$bookings_pending_count.'</span></span>';
		}
		//Count pending events
		$events_num = '';
		$events_pending_count = EM_Events::count(array('status'=>0, 'scope'=>'all'));
		//TODO Add flexible permissions
		if($events_pending_count > 0){
			$events_num = '<span class="update-plugins count-'.$events_pending_count.'"><span class="plugin-count">'.$events_pending_count.'</span></span>';
		}
		$both_pending_count = apply_filters('em_items_pending_count', $events_pending_count + $bookings_pending_count);
		$both_num = ($both_pending_count > 0) ? '<span class="update-plugins count-'.$both_pending_count.'"><span class="plugin-count">'.$both_pending_count.'</span></span>':'';
	  	add_object_page(__('Events', 'dbem'),__('Events', 'dbem').$both_num,'edit_events','events-manager','em_admin_events_page', plugins_url().'/events-manager/includes/images/calendar-16.png');
	   	// Add a submenu to the custom top-level menu:
	   		$plugin_pages = array(); 
			$plugin_pages[] = add_submenu_page('events-manager', __('Edit', 'dbem'),__('Edit', 'dbem').$events_num,'edit_events','events-manager','em_admin_events_page');
			$plugin_pages[] = add_submenu_page('events-manager', __('Add new', 'dbem'), __('Add new','dbem'), 'edit_events', 'events-manager-event', "em_admin_event_page");
			$plugin_pages[] = add_submenu_page('events-manager', __('Locations', 'dbem'), __('Locations', 'dbem'), 'edit_locations', 'events-manager-locations', "em_admin_locations_page");
			$plugin_pages[] = add_submenu_page('events-manager', __('Bookings', 'dbem'), __('Bookings', 'dbem').$bookings_num, 'manage_bookings', 'events-manager-bookings', "em_bookings_page");
			$plugin_pages[] = add_submenu_page('events-manager', __('Event Categories','dbem'),__('Categories','dbem'), 'edit_categories', "events-manager-categories", 'em_admin_categories_page');
			$plugin_pages[] = add_submenu_page('events-manager', __('Events Manager Settings','dbem'),__('Settings','dbem'), 'activate_plugins', "events-manager-options", 'em_admin_options_page');
			$plugin_pages[] = add_submenu_page('events-manager', __('Getting Help for Events Manager','dbem'),__('Help','dbem'), 'activate_plugins', "events-manager-help", 'em_admin_help_page');
			$plugin_pages = apply_filters('em_create_events_submenu',$plugin_pages);
			foreach($plugin_pages as $plugin_page){
				add_action( 'admin_print_scripts-'. $plugin_page, 'em_admin_load_scripts' );
				add_action( 'admin_head-'. $plugin_page, 'em_admin_general_script' );
				add_action( 'admin_print_styles-'. $plugin_page, 'em_admin_load_styles' );
			}
  	}
}
add_action('admin_menu','em_create_events_submenu');

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
		return apply_filters('em_ms_globals', array(
			//multisite settings
			'dbem_ms_global_table', 'dbem_ms_global_events', 'dbem_ms_global_events_links', 'dbem_ms_global_locations',
			//mail
			'dbem_rsvp_mail_port', 'dbem_mail_sender_address', 'dbem_smtp_password', 'dbem_smtp_username','dbem_smtp_host', 'dbem_mail_sender_name','dbem_smtp_host','dbem_rsvp_mail_send_method','dbem_rsvp_mail_SMTPAuth',
			//images	
			'dbem_image_max_width','dbem_image_max_height','dbem_image_max_size'	
		));
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
			add_filter('option_'.$format_name, array(&$this, $format_name), 1,1);
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
 * Add a link to the favourites menu
 * @param array $actions
 * @return multitype:string 
 */
function em_favorite_menu($actions) {
	// add quick link to our favorite plugin
	$actions ['admin.php?page=events-manager-event'] = array (__ ( 'Add an event', 'dbem' ), EM_MIN_CAPABILITY );
	return $actions;
}
add_filter ( 'favorite_actions', 'em_favorite_menu' );

/**
 * Settings link in the plugins page menu
 * @param array $links
 * @param string $file
 * @return array
 */
function em_set_plugin_meta($links, $file) {
	$plugin = plugin_basename(__FILE__);
	// create link
	if ($file == $plugin) {
		return array_merge(
			$links,
			array( sprintf( '<a href="admin.php?page=events-manager-options">%s</a>', __('Settings', 'dbem') ) )
		);
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'em_set_plugin_meta', 10, 2 );

/* Creating the wp_events table to store event data*/
function em_activate() {
	global $wp_rewrite;
   	$wp_rewrite->flush_rules();
}
register_activation_hook( __FILE__,'em_activate');
?>