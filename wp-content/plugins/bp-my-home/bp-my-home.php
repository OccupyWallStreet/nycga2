<?php
/*
Plugin Name: BP My Home
Plugin URI: http://imath.owni.fr/tag/bp-my-home/
Description: BP My Home makes it possible to add moveable and collapsible widgets to BuddyPress Members area.
Version: 1.2.2
Requires at least: 3.2
Tested up to: 3.2.1
License: GNU/GPL 2
Author: imath
Author URI: http://imath.owni.fr/
Network: true
*/

define( 'BP_MYHOME_SLUG', 'my-home' );
define ( 'BP_MYHOME_PLUGIN_NAME', 'bp-my-home' );
define ( 'BP_MYHOME_PLUGIN_URL', WP_PLUGIN_URL . '/' . BP_MYHOME_PLUGIN_NAME );
define ( 'BP_MYHOME_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . BP_MYHOME_PLUGIN_NAME );
define ( 'BP_MYHOME_VERSION', '1.2.2' );
//comment next line if you dont want BP My Home to be the default component of member's profile
define ( 'BP_DEFAULT_COMPONENT',BP_MYHOME_SLUG);

/*defining widgets dir*/
$upload_dir = wp_upload_dir();
define ('BP_MYHOME_WIDGETS_DIR', $upload_dir['basedir'].'/bpmh-widgets');
define ('BP_MYHOME_WIDGETS_URL', $upload_dir['baseurl'].'/bpmh-widgets');


/* Only load the component if BuddyPress is loaded and initialized. */
function bp_my_home_init() {
	global $bp;
	if ( $bp->current_component == BP_MYHOME_SLUG ){
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'jquery-ui-1814', BP_MYHOME_PLUGIN_URL . '/js/jquery-ui-1.8.14.custom.min.js' );
		wp_enqueue_script( 'bp-my-home', BP_MYHOME_PLUGIN_URL . '/js/bp-my-home.js' );
		wp_enqueue_style('my-home-style', BP_MYHOME_PLUGIN_URL . '/styles.css');
		
		//Loading active widgets
		
		$active_widgets = get_option('_bpmh_activated_widgets');
		if($active_widgets!="") {
			foreach($active_widgets as $widget_key=>$widget_values){
				if(file_exists(BP_MYHOME_WIDGETS_DIR.'/'.$widget_key)) include_once(BP_MYHOME_WIDGETS_DIR.'/'.$widget_key);
			}
			do_action('load_widget_language_files');
		}
	}
	if ( !bp_my_home_is_still_bp_1_2() ) {
		require( dirname( __FILE__ ) . '/includes/bp-my-home-class.php' );
	}
	require( dirname( __FILE__ ) . '/includes/bp-my-home-core.php' );
}
add_action( 'bp_init', 'bp_my_home_init' );


/**
* bp_my_home_load_textdomain
* translation!
* 
*/
function bp_my_home_load_textdomain() {

	// try to get locale
	$locale = apply_filters( 'bp_my_home_load_textdomain_get_locale', get_locale() );

	// if we found a locale, try to load .mo file
	if ( !empty( $locale ) ) {
		// default .mo file path
		$mofile_default = sprintf( '%s/languages/%s-%s.mo', BP_MYHOME_PLUGIN_DIR, BP_MYHOME_PLUGIN_NAME, $locale );
		// final filtered file path
		$mofile = apply_filters( 'bp_my_home_load_textdomain_mofile', $mofile_default );
		// make sure file exists, and load it
		if ( file_exists( $mofile ) ) {
			load_textdomain( BP_MYHOME_PLUGIN_NAME, $mofile );
		}
	}
}
add_action ( 'bp_init', 'bp_my_home_load_textdomain', 2 );

/**
* bp_my_home_is_still_bp_1_2
* many thanks to Boone B. Gorges for this snippet
* http://bpdevel.wordpress.com/2011/08/09/maintain-backward-compatibility-with-an-abstraction-file/
* check if BuddyPress is not 1.5
*/
function bp_my_home_is_still_bp_1_2(){
	if( defined( 'BP_VERSION' ) && version_compare( BP_VERSION, '1.5-beta-1', '<' ) ){
		return true;
	}
	else return false;
}


/**
* bp_my_home_add_admin_menu
* Add a submenu to buddypress to manage bpmh widgets.
* 
*/
function bp_my_home_add_admin_menu() {
	global $bp;

	if ( !$bp->loggedin_user->is_site_admin )
		return false;

	require ( dirname( __FILE__ ) . '/includes/bp-my-home-admin.php' );
	
	add_submenu_page( 'bp-general-settings', __( 'BPMH Manager', 'bp-my-home' ), __( 'BPMH Manager', 'bp-my-home' ), 'manage_options', 'bp-mh-admin', 'bp_my_home_manager_admin' );
}

add_action( is_multisite() ? 'network_admin_menu' : 'admin_menu', 'bp_my_home_add_admin_menu', 14 );


/**
* functions to automatically add website rss feeds or bookmark post/page
*/

if(function_exists('get_blog_option')){
	global $the_active_widgets;
	$the_active_widgets = get_blog_option('1','_bpmh_activated_widgets');
	//RSS
	if("yes" == get_blog_option('1','bp-my-home-auto-rss')) require ( dirname( __FILE__ ) . '/includes/bp-my-home-auto-rss.php' );
	//Bookmarks
	if("yes" == get_blog_option('1','bp-my-home-auto-bkmk')) require ( dirname( __FILE__ ) . '/includes/bp-my-home-auto-bkmk.php' );
}
else{
	global $the_active_widgets;
	$the_active_widgets = get_option('_bpmh_activated_widgets');
	//RSS
	if("yes" == get_option('bp-my-home-auto-rss')) require ( dirname( __FILE__ ) . '/includes/bp-my-home-auto-rss.php' );
	//Bookmarks
	if("yes" == get_option('bp-my-home-auto-bkmk')) require ( dirname( __FILE__ ) . '/includes/bp-my-home-auto-bkmk.php' );
}

//function to check the activated widgets or if a user allready saved a bkmk or a feed
function bpmh_in_array($search, $list, $type="activated"){
	$found=0;
	if($type=="activated"){
		$searched_widget = $search."/".$search.".php";
		if($list!=""){
			foreach($list as $k=>$v){
				if($k==$searched_widget){
					$found=1;
				}
			}
		}
	}
	elseif($type=="user-saved"){
		if($list!=""){
			foreach($list as $bkmk){
				if($bkmk['url']==$search){
					$found=1;
				}
			}
		}
	}
	return $found;
}



/**
* thanks to Jatinder Pal Singh (bp_profile_as_homepage plugin)
*/
function bp_my_home_profile_homepage()
{
	global $bp;
	
	if(is_user_logged_in() && $_SERVER['REQUEST_URI']=='/')
	{
			$home_page = get_user_meta($bp->loggedin_user->id, 'bpmh_user_home_page',true);
			if($home_page=="yes") wp_redirect( $bp->loggedin_user->domain .'my-home');
	}
}
function bp_my_home_logout_redirection()
{
	global $bp;
	$redirect = $bp->root_domain;
	wp_logout_url( $redirect );	
}
add_filter('get_header','bp_my_home_profile_homepage',1);
add_action('wp_logout','bp_my_home_logout_redirection');


/**
* bp_my_home_widget_dir_is_empty
* Check if BP My Home widgets are in their new directory
*/
function bp_my_home_widget_dir_is_empty(){
	$directory = dir(BP_MYHOME_WIDGETS_DIR);
	$directory_not_empty = false;
	// Loop while the read method goes through each and
	// every file
	while ((FALSE !== ($item = $directory->read())) && ( $directory_not_empty==false))
	{
		// If an item is not "." and "..", then something
		// exists in the directory and it is not empty
		if ($item != '.' && $item != '..')
		{
			$directory_not_empty = true;
		}
	}

	// Close the directory
	$directory->close();
	return $directory_not_empty;
}


/**
* bp_my_home_activate
* store plugin's version
* 
*/
function bp_my_home_activate() {	
	//if first install
	if(!get_option('bp-my-home-version')){
		if(!file_exists(BP_MYHOME_WIDGETS_DIR)){
			mkdir(BP_MYHOME_WIDGETS_DIR);
		}
		if(!file_exists(BP_MYHOME_WIDGETS_DIR.'-temp')){
			mkdir(BP_MYHOME_WIDGETS_DIR.'-temp');
		}
		update_option( 'bp-my-home-version', BP_MYHOME_VERSION );
	}
	else{
		if(!file_exists(BP_MYHOME_WIDGETS_DIR)){
			mkdir(BP_MYHOME_WIDGETS_DIR);
		}
		if(!file_exists(BP_MYHOME_WIDGETS_DIR.'-temp')){
			mkdir(BP_MYHOME_WIDGETS_DIR.'-temp');
		}
		update_option( 'bp-my-home-version', BP_MYHOME_VERSION );
	}
}
register_activation_hook( __FILE__, 'bp_my_home_activate' );
add_action( is_multisite() ? 'network_admin_menu' : 'admin_menu', 'bp_my_home_activate');
?>