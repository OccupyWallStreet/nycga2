<?php
/*
	11/17/2010 2:08:08 PM -pk
	This code is run if the plugin is uninstalled using the Admin > Plugin page.
	Before a plugin can be uninstalled, it has to be deactivated.
*/
	// Uninstall plugin options
	if ( !defined('WP_UNINSTALL_PLUGIN') )
	{
		exit();
	}
	// Some security precautions
	// You have to be logged in to run this file (duh)
	if ( !is_user_logged_in() )
		wp_die( __('You must be logged in to run the uninstaller.', 'contentscheduler') );
	// You have to have permissions to mess with plugins to run this file (duh*2)
	if ( !current_user_can( 'install_plugins' ) )
		wp_die( __('You do not have permission to run the uninstaller.', 'contentscheduler') );
	// Another check to make sure we're all legit here
	if ( defined( 'UNINSTALL_CONTENTSCHEDULER' ) )
		wp_die( 'UNINSTALL_CONTENTSCHEDULER is, oddly, set! It should only be set in this uninstaller.' );
	define( 'UNINSTALL_CONTENTSCHEDULER', 'go' );
	if ( !defined( 'UNINSTALL_CONTENTSCHEDULER' ) || constant( 'UNINSTALL_CONTENTSCHEDULER' ) == '' ) 
		wp_die( 'UNINSTALL_CONTENTSCHEDULER must be set to a non-blank value in uninstall.php' );
	// We need to handle multisite network deletion
	global $wpdb;
	// See if this is a multisite deletion
	if ( function_exists( 'is_multisite' ) && is_multisite() )
	{
		// See if the plugin is being deleted for the entire network of blogs
		// IS that even possible... a 'networkwide' deletion? We'll find out.
		if ( isset( $_GET['networkwide'] ) && ( $_GET['networkwide'] == 1 ) )
		{
			// Save the current blog id
			$orig_blog = $wpdb->blogid;
			// Loop through all existing blogs (by id)
			$all_blogs = $wpdb->get_col( $wpdb->prepare("SELECT blog_id FROM $wpdb->blogs") );
			foreach ($all_blogs as $blog_id)
			{
				switch_to_blog( $blog_id );
				delete_function();
			} // end foreach
			// switch back to the original blog
			switch_to_blog( $orig_blog );
			return;
		} // end if
	} // end if
	// Seems like it is not a multisite deletion OR it is a single blog of a multisite
	delete_function();
	function delete_function()
	{
		// I don't know that we actually need to use $current_blog_id for deletion
		//
		// If we want to check for an option to keep all options and database tables,
		// here is our chance.
		//
		// CHECK TO SEE IF WE WANT TO DELETE OPTIONS
		// get this plugin's options from the database
		$options = get_option('ContentScheduler_Options');
		if( $options['remove-cs-data'] == "Remove all data" )
		{
			// Proceed with removing data
			// Now delete options array
			delete_option('ContentScheduler_Options');
			// Delete any post_metadata for our plugin
			$allposts = get_posts('posts_per_page=-1&post_type=any&post_status=any');
			foreach( $allposts as $postinfo) {
				// older versions of the plugin had different name but might have some postmeta still living
				delete_post_meta($postinfo->ID, 'cs-enable-schedule');
				delete_post_meta($postinfo->ID, 'cs-expire-date');
				// version 0.9.7 and above use these names
				delete_post_meta($postinfo->ID, '_cs-enable-schedule');
				delete_post_meta($postinfo->ID, '_cs-expire-date');
			}
		}	
	} // end delete_function()
?>