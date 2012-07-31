<?php
//
// CheezCap - Cheezburger Custom Administration Panel
// (c) 2008 - 2010 Cheezburger Network (Pet Holdings, Inc.)
// LOL: http://cheezburger.com
// Source: http://code.google.com/p/cheezcap/
// Authors: Kyall Barrows, Toby McKes, Stefan Rusek, Scott Porad
// License: GNU General Public License, version 2 (GPL), http://www.gnu.org/licenses/gpl-2.0.html
//

require_once( dirname(__FILE__) . '/get-pro.php' );
require_once( dirname(__FILE__) . '/post-metabox.php' );
require_once( dirname(__FILE__) . '/library.php' );
require_once( dirname(__FILE__) . '/config.php' );


add_action( 'admin_init', 'custom_community_theme_options_init' );
function custom_community_theme_options_init(){
	register_setting( 'custom_community_options', 'custom_community_theme_options', 'custom_community_theme_options_validate' );
}

add_action( 'admin_init', 'cc_update_old_version' );
function cc_update_old_version(){
	global $wpdb;
	
	if(get_option('cc_version') <= 1.8){
		$options = wp_load_alloptions();
		foreach((array) $options as $kay => $value) :
			$kay = esc_attr($kay);
	  		if(substr($kay, 0, 4)=='cap_') {
		  		
		  		$cap = get_option('custom_community_theme_options');
		  		$cap[$kay] = $value;
		  		update_option( 'custom_community_theme_options', $cap );
		  		
		    	delete_option($kay);     
	  		}
	  	endforeach;
	    update_option( 'cc_version', 1.9 );
	} else if (!get_option('cc_version')){
	cap_defaults_init();
		update_option( 'cc_version', 1.9 );
	}
}

$cap = new autoconfig();

if ( ! defined( 'LOADED_CONFIG' ) ) {
    add_action( 'admin_menu', 'cap_add_admin' );
    define( 'LOADED_CONFIG', 1 );
}

function cap_add_admin() {
	global $themename, $req_cap_to_edit;

	if ( ! current_user_can ( $req_cap_to_edit ) )
		return;

	if ( isset( $_GET['page'] ) && $_GET['page'] == 'theme_settings' ) {
		$options = cap_get_options();
		$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
		$method = false;
		$done = false;
		$data = new ImportData();
		switch ( $action ) {
			case 'Reset':			
				delete_option('custom_community_theme_options');     
				cap_defaults_init();				  
				$method = false;
				break;
			case 'Export':
				$method = 'Export';
				$done = 'cap_serialize_export';
				break;
			case 'Import':
				$method = 'Import';
				if(empty($_FILES['file']['tmp_name']))
					return ;
				
				$data = unserialize( implode ('', file ($_FILES['file']['tmp_name'])));
				break;
		}

		if ( $method ) {
			foreach ( $options as $group ) {
				foreach ( $group->options as $option ) {
					call_user_func( array( $option, $method ), $data );
				}
	    	}
			if ( $done )
				call_user_func( $done, $data );
		}
	}

	$pgName = "$themename Settings";
	$hook = add_theme_page( $pgName, $pgName, isset( $req_cap_to_edit ) ? $req_cap_to_edit : 'edit_theme_options', 'theme_settings', 'top_level_settings' );
	add_action( "admin_print_scripts-$hook", 'cap_admin_js_libs' );
	add_action( "admin_footer-$hook", 'cap_admin_js_footer' );
	add_action( "admin_print_styles-$hook", 'cap_admin_css' );	
}

function cap_defaults_init(){
	$cap_options = cap_get_options();
	
	$cap_options_default = Array();
	
//echo '<pre>';
//print_r($cap_options);
//echo '</pre>';

	foreach( $cap_options as $cap_option ) :
		$cap_option_arr = (Array) $cap_option;
		foreach ($cap_option_arr['options'] AS $option){
			switch(get_class($option)){
				case 'BooleanOption':
					$cap_options_default[$option->id] = $option->options[$option->std];
				break;
				case 'DropdownOption':
					$cap_options_default[$option->id] = $option->options[0];
				break;
				default:
					$cap_options_default[$option->id] = $option->std;
				break;
			}
		}
	endforeach;
	
	update_option( 'custom_community_theme_options', $cap_options_default );
}