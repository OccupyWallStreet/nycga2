<?php
//
//  class-ai1ec-updater.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2012-05-09.
//
include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

class Ai1ec_Updater extends WP_Upgrader {
	function upgrade_strings() {
		$this->strings['up_to_date'] = __('The plugin is at the latest version.');
		$this->strings['no_package'] = __('Update package not available.');
		$this->strings['downloading_package'] = __('Downloading update from <span class="code">%s</span>&#8230;');
		$this->strings['unpack_package'] = __('Unpacking the update&#8230;');
		$this->strings['deactivate_plugin'] = __('Deactivating the plugin&#8230;');
		$this->strings['remove_old'] = __('Removing the old version of the plugin&#8230;');
		$this->strings['remove_old_failed'] = __('Could not remove the old plugin.');
		$this->strings['process_failed'] = __('Plugin update failed.');
		$this->strings['process_success'] = __('Plugin updated successfully.');
	}
	function upgrade( $plugin, $url ) {

		$this->init();
		$this->upgrade_strings();

		add_filter('upgrader_pre_install', array(&$this, 'deactivate_plugin_before_upgrade'), 10, 2);
		add_filter('upgrader_clear_destination', array(&$this, 'delete_old_plugin'), 10, 4);

		$this->run( 
			array(
				'package'           => $url,
				'destination'       => WP_PLUGIN_DIR,
				'clear_destination' => true,
				'clear_working'     => true,
				'hook_extra'        => array(
					'plugin' => $plugin
				)
			)
		);

		// Cleanup our hooks, in case something else does a upgrade on this connection.
		remove_filter( 'upgrader_pre_install', array( &$this, 'deactivate_plugin_before_upgrade' ) );
		remove_filter( 'upgrader_clear_destination', array( &$this, 'delete_old_plugin') );

		if( ! $this->result || is_wp_error( $this->result ) )
			return $this->result;

		// Force refresh of plugin update information
		delete_site_transient( 'update_plugins' );
		wp_cache_delete( 'plugins', 'plugins' );

		// activate the plugin
		activate_plugin( $plugin );
		echo '<p>Plugin activated.</p>';
		echo '<a href="' . admin_url( 'index.php' ) . '">Continue Here</a>';
	}

	//Hooked to pre_install
	function deactivate_plugin_before_upgrade( $return, $plugin ) {

		if( is_wp_error( $return ) ) //Bypass.
			return $return;

		$plugin = isset( $plugin['plugin'] ) ? $plugin['plugin'] : '';
		if( empty( $plugin ) )
			return new WP_Error( 'bad_request', $this->strings['bad_request'] );

		if( is_plugin_active( $plugin ) ) {
			$this->skin->feedback( 'deactivate_plugin' );
			//Deactivate the plugin silently, Prevent deactivation hooks from running.
			deactivate_plugins( $plugin, true );
		}
	}

	//Hooked to upgrade_clear_destination
	function delete_old_plugin( $removed, $local_destination, $remote_destination, $plugin ) {
		global $wp_filesystem;

		if( is_wp_error( $removed ) )
			return $removed; //Pass errors through.

		$plugin = isset( $plugin['plugin'] ) ? $plugin['plugin'] : '';
		if( empty( $plugin ) )
			return new WP_Error( 'bad_request', $this->strings['bad_request'] );

		$plugins_dir = $wp_filesystem->wp_plugins_dir();
		$this_plugin_dir = trailingslashit( dirname( $plugins_dir . $plugin ) );

		if( ! $wp_filesystem->exists( $this_plugin_dir ) ) //If its already vanished.
			return $removed;

		// If plugin is in its own directory, recursively delete the directory.
		if( strpos( $plugin, '/') && $this_plugin_dir != $plugins_dir ) //base check on if plugin includes directory separator AND that its not the root plugin folder
			$deleted = $wp_filesystem->delete( $this_plugin_dir, true );
		else
			$deleted = $wp_filesystem->delete( $plugins_dir . $plugin );

		if( ! $deleted )
			return new WP_Error( 'remove_old_failed', $this->strings['remove_old_failed'] );

		return true;
	}
}