<?php
/*
Plugin Name: GA Info Tab Plus
Plugin URI: http://nycga.net
Description: Adding extra group fields and some other missing functionality to groups
Version: 1.0
Author: Rachel Baker
Author URI: http://rachelbaker.me
*/
define ('info_tab_plus_VERSION', '1.0');

register_activation_hook( __FILE__, 'info_tab_plus_activation');
//register_deactivation_hook( __FILE__, 'info_tab_plus_deactivation');
function info_tab_plus_activation() {
    $info_tab['groups'] = 'all';
    add_option('info', $info_tab, '', 'yes');
}
function info_tab_plus_deactivation() { delete_option('info'); }

/* LOAD LANGUAGES */
add_action ('plugins_loaded', 'info_tab_plus_load_textdomain', 7 );
function info_tab_plus_load_textdomain() {
    $locale = apply_filters('buddypress_locale', get_locale() );
    $mofile = dirname( __File__ )   . "/langs/contact-$locale.mo";

    if ( file_exists( $mofile ) )
        load_textdomain('info', $mofile);
}


add_action( 'bp_loaded', 'info_tab_plus_load' );
function info_tab_plus_load(){
	global $bp;
	require ( dirname(__File__) . '/contact-cssjs.php');
	if ( is_admin()){
		require ( dirname(__File__) . '/contact-admin.php');
	}
	$info_tab = get_option('info');
	if ( (is_string($info_tab['groups']) && $info_tab['groups'] == 'all' ) || (is_array($info_tab['groups']) && in_array($bp->groups->current_group->id, $info_tab['groups'])) ){
		require ( dirname(__File__) . '/contact-loader.php');
	}
}

function info_tab_plus_names($name = 'name'){
	switch ($name){
		case 'title_general':
			return __('Contact Tab &rarr; General Settings', 'info');
			break;
		case 'title_fields':
			return __('Contact Tab &rarr; Fields Management', 'info');
			break;
                case 'title_fields_reset':
			return __('Status Tab &rarr; Restore Default Fields', 'status');
			break;     		    
		case 'title_fields_add':
			return __('Contact Tab &rarr; Add Fields', 'info');
			break;
		case 'title_fields_edit':
			return __('Contact Tab &rarr; Edit Field', 'info');
			break;
		case 'name':
			return __('info', 'info');
			break;
		case 'nav':
			return __('info', 'info');
			break;
	}
}

// tech func
if (!function_exists('print_var')){
    function print_var($var){
        echo '<pre>';
        if(!empty($var))
            print_r($var);
        else
            var_dump($var);
        echo '</pre>';
    }
}
