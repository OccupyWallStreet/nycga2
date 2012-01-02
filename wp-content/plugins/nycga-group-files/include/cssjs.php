<?php

/**
 * nycga_group_files_front_cssjs()
 *
 * This function will enqueue the components css and javascript files
 * only when the front group documents page is displayed
 */
function nycga_group_files_front_cssjs() {
	global $bp;

	//if we're on a group page
	if ( $bp->current_component == $bp->groups->slug ) {

		wp_enqueue_script( 'nycga-group-files', WP_PLUGIN_URL . '/nycga-group-files/js/general.js',array('jquery') );
		wp_enqueue_style('nycga-group-files',WP_PLUGIN_URL . '/nycga-group-files/css/style.css');

		//if we're on the group forums page and the admin has enabled documents as forum attachments
		if( $bp->current_action == 'forum' && get_option('nycga_group_files_forum_attachments') ) {
			wp_enqueue_script('nycga-group-files-forums', WP_PLUGIN_URL . '/nycga-group-files/js/forum.js');
		}

		switch( nycga_group_files_THEME_VERSION ){
			case '1.1':
				wp_enqueue_style('nycga-group-files-1.1', WP_PLUGIN_URL . '/nycga-group-files/css/11.css');
			break;
			case '1.2':
			//	wp_enqueue_style('nycga-group-files-1.2', WP_PLUGIN_URL . '/nycga-group-files/css/12.css');
			break;
		}
	}
}
add_action( 'bp_setup_nav', 'nycga_group_files_front_cssjs', 1 );

/**
 * nycga_group_files_admin_cssjs()
 *
 * This function will enqueue the css and js files for the admin back-end
 */
function nycga_group_files_admin_cssjs() {
	wp_enqueue_style('nycga-group-files-admin',WP_PLUGIN_URL . '/nycga-group-files/css/admin.css');
	wp_enqueue_script( 'nycga-group-files-admin', WP_PLUGIN_URL . '/nycga-group-files/js/admin.js', array('jquery') );
}
add_action('admin_init','nycga_group_files_admin_cssjs');
		
?>
