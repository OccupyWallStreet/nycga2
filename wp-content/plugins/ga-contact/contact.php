<?php
/*
Plugin Name: GA Contact Tab 
Plugin URI: http://ovirium.com/
Description: Adding extra group fields and some other missing functionality to groups
Version: 1.1
Author: slaFFik
Author URI: http://cosydale.com/
*/
define ('CONTACT_VERSION', '1.0');

register_activation_hook( __FILE__, 'contact_activation');
//register_deactivation_hook( __FILE__, 'contact_deactivation');
function contact_activation() {
    $contact['groups'] = 'all';
    add_option('contact', $contact, '', 'yes');
}
function contact_deactivation() { delete_option('contact'); }

/* LOAD LANGUAGES */
add_action ('plugins_loaded', 'contact_load_textdomain', 7 );
function contact_load_textdomain() {
    $locale = apply_filters('buddypress_locale', get_locale() );
    $mofile = dirname( __File__ )   . "/langs/contact-$locale.mo";

    if ( file_exists( $mofile ) )
        load_textdomain('contact', $mofile);
}


add_action( 'bp_loaded', 'contact_load' );
function contact_load(){
	global $bp;
	require ( dirname(__File__) . '/contact-cssjs.php');
	if ( is_admin()){
		require ( dirname(__File__) . '/contact-admin.php');
	}
	$contact = get_option('contact');
	if ( (is_string($contact['groups']) && $contact['groups'] == 'all' ) || (is_array($contact['groups']) && in_array($bp->groups->current_group->id, $contact['groups'])) ){
		require ( dirname(__File__) . '/contact-loader.php');
	}
}

function contact_names($name = 'name'){
	switch ($name){
		case 'title_general':
			return __('Contact Tab &rarr; General Settings', 'contact');
			break;
		case 'title_fields':
			return __('Contact Tab &rarr; Fields Management', 'contact');
			break;
                case 'title_fields_reset':
			return __('Status Tab &rarr; Restore Default Fields', 'status');
			break;     		    
		case 'title_fields_add':
			return __('Contact Tab &rarr; Add Fields', 'contact');
			break;
		case 'title_fields_edit':
			return __('Contact Tab &rarr; Edit Field', 'contact');
			break;
		case 'name':
			return __('Contact', 'contact');
			break;
		case 'nav':
			return __('Contact', 'contact');
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
