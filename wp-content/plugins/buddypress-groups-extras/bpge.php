<?php
/*
Plugin Name: BuddyPress Groups Extras
Plugin URI: http://ovirium.com/
Description: Adding extra group fields and some other missing functionality to groups
Version: 1.0
Author: slaFFik
Author URI: http://cosydale.com/
*/
define ('BPGE_VERSION', '1.0');

register_activation_hook( __FILE__, 'bpge_activation');
//register_deactivation_hook( __FILE__, 'bpge_deactivation');
function bpge_activation() {
    $bpge['groups'] = 'all';
    add_option('bpge', $bpge, '', 'yes');
}
function bpge_deactivation() { delete_option('bpge'); }

/* LOAD LANGUAGES */
add_action ('plugins_loaded', 'bpge_load_textdomain', 7 );
function bpge_load_textdomain() {
    $locale = apply_filters('buddypress_locale', get_locale() );
    $mofile = dirname( __File__ )   . "/langs/bpge-$locale.mo";

    if ( file_exists( $mofile ) )
        load_textdomain('bpge', $mofile);
}


add_action( 'bp_loaded', 'bpge_load' );
function bpge_load(){
	global $bp;
	require ( dirname(__File__) . '/bpge-cssjs.php');
	if ( is_admin()){
		require ( dirname(__File__) . '/bpge-admin.php');
	}
	$bpge = get_option('bpge');
	if ( (is_string($bpge['groups']) && $bpge['groups'] == 'all' ) || (is_array($bpge['groups']) && in_array($bp->groups->current_group->id, $bpge['groups'])) ){
		require ( dirname(__File__) . '/bpge-loader.php');
	}
}

function bpge_names($name = 'name'){
	switch ($name){
		case 'title_general':
			return __('Group Extras &rarr; General Settings', 'bpge');
			break;
		case 'title_fields':
			return __('Group Extras &rarr; Fields Management', 'bpge');
			break;
		case 'title_fields_add':
			return __('Group Extras &rarr; Add Fields', 'bpge');
			break;
		case 'title_fields_edit':
			return __('Group Extras &rarr; Edit Field', 'bpge');
			break;
		case 'name':
			return __('Description', 'bpge');
			break;
		case 'nav':
			return __('Extras', 'bpge');
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