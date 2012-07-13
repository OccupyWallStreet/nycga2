<?php
/**
 * File that holds all the author plugins functions
 *
 * @package WordPress_Plugins
 * @subpackage RevealIDsForWPAdmin
 */


/**
 * Writes CSS and JS to the plugin page's header for displaying my other plugins
 *
 * @since 1.0.1
 * @author scripts@schloebe.de
 */
function ridwpa_authorplugins_head() {
	wp_enqueue_script( 'os_authorplugins_script', RIDWPA_PLUGINFULLURL . "js/os_authorplugins_script.js", array('jquery'), RIDWPA_VERSION );
	$ridwpa_authorplugins_style  = "\n<link rel='stylesheet' href='" . RIDWPA_PLUGINFULLURL . "css/os_authorplugins_style.css' type='text/css' media='all' />\n";
	print( $ridwpa_authorplugins_style );
}

/**
 * Plugin credits in WP footer
 *
 * @since 1.0.1
 * @author scripts@schloebe.de
 */
function ridwpa_plugin_footer() {
	$plugin_data = get_plugin_data( RIDWPA_PLUGINFULLDIR . 'reveal-ids-for-wp-admin-25.php' );
	$plugin_data['Title'] = $plugin_data['Name'];
	if ( !empty($plugin_data['Plugin URI']) && !empty($plugin_data['Name']) )
		$plugin_data['Title'] = '<a href="' . $plugin_data['Plugin URI'] . '" title="'.__( 'Visit plugin homepage' ).'">' . $plugin_data['Name'] . '</a>';
	
	if ( basename($_SERVER['REQUEST_URI']) == 'reveal-ids-for-wp-admin-25.php' ) {
		printf('%1$s ' . __('plugin') . ' | ' . __('Version') . ' <a href="http://www.schloebe.de/wordpress/reveal-ids-for-wp-admin-25-plugin/" title="">%2$s</a> | ' . __('Author') . ' %3$s<br />', $plugin_data['Title'], $plugin_data['Version'], $plugin_data['Author']);
	}
}

/**
 * Initialization of author plugins stuff
 *
 * @since 1.0.1
 * @author scripts@schloebe.de
 */
function ridwpa_authorplugins_init() {
	global $wp_version;
	if( version_compare($wp_version, '2.5', '>=') ) {
		add_action('in_admin_footer', 'ridwpa_plugin_footer');
	}
}

if ( basename($_SERVER['REQUEST_URI']) == 'reveal-ids-for-wp-admin-25.php' ) {
	add_action( "admin_print_scripts", 'ridwpa_authorplugins_head' );
}
add_action( 'admin_init', 'ridwpa_authorplugins_init', 1 );
?>