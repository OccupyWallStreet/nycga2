<?php
/*
Plugin Name: GA Status Tab 
Plugin URI: http://ovirium.com/
Description: Adding extra group fields and some other missing functionality to groups
Version: 1.0
Author: slaFFik
Author URI: http://cosydale.com/
*/
define ('STATUS_VERSION', '1.0');

register_activation_hook( __FILE__, 'status_activation');
//register_deactivation_hook( __FILE__, 'status_deactivation');
function status_activation() {
    $status['groups'] = 'all';
    add_option('status', $status, '', 'yes');
}
function status_deactivation() { delete_option('status'); }

/* LOAD LANGUAGES */
add_action ('plugins_loaded', 'status_load_textdomain', 7 );
function status_load_textdomain() {
    $locale = apply_filters('buddypress_locale', get_locale() );
    $mofile = dirname( __File__ )   . "/langs/status-$locale.mo";

    if ( file_exists( $mofile ) )
        load_textdomain('status', $mofile);
}


add_action( 'bp_loaded', 'status_load' );
function status_load(){
	global $bp;
	require ( dirname(__File__) . '/status-cssjs.php');
	if ( is_admin()){
		require ( dirname(__File__) . '/status-admin.php');
	}
	$status = get_option('status');
	if ( (is_string($status['groups']) && $status['groups'] == 'all' ) || (is_array($status['groups']) && in_array($bp->groups->current_group->id, $status['groups'])) ){
		require ( dirname(__File__) . '/status-loader.php');
	}
}

function status_names($name = 'name'){
	switch ($name){
		case 'title_general':
			return __('Status Tab &rarr; General Settings', 'status');
			break;
		case 'title_fields':
			return __('Status Tab &rarr; Fields Management', 'status');
			break;
		case 'title_fields_add':
			return __('Status Tab &rarr; Add Fields', 'status');
			break;
		case 'title_fields_edit':
			return __('Status Tab &rarr; Edit Field', 'status');
			break;
		case 'name':
			return __('Status', 'status');
			break;
		case 'nav':
			return __('Status', 'status');
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
