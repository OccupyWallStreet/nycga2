<?php
/*
Plugin Name: Update Services
Plugin URI: http://premium.wpmudev.org/project/update-services
Description: Multisite automatically removes the update services box from the settings 
Author: S H Mohanjith (Incsub), Andrew Billits (Incsub)
Version: 1.0.2
Author URI: http://premium.wpmudev.org
Network: true
WDP ID: 77
*/ 

/* 
Copyright 2007-2009 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//------------------------------------------------------------------------//
//---Config---------------------------------------------------------------//
//------------------------------------------------------------------------//

//------------------------------------------------------------------------//
//---Hook-----------------------------------------------------------------//
//------------------------------------------------------------------------//
add_action('init', 'update_services_init');
add_action('wpmu_options', 'update_services_site_admin_options');
add_action('update_wpmu_options', 'update_services_site_admin_options_process');
add_filter('whitelist_options', 'update_services_enable', 99);
add_action('admin_head', 'update_services_setting_section');
add_action('wpmu_new_blog', 'update_services_default', 1, 1);
//------------------------------------------------------------------------//
//---Functions------------------------------------------------------------//
//------------------------------------------------------------------------//
function update_services_init() {
	if ( !is_multisite() )
		exit( 'The Update Services plugin is only compatible with WordPress Multisite.' );
		
	load_plugin_textdomain('update_services', false, dirname(plugin_basename(__FILE__)).'/languages');
}

function update_services_default($blog_ID) {
	$default_update_services = stripslashes( get_site_option('default_update_services') );
	if ( $default_update_services == 'empty' ) {
		$default_update_services = '';
	}
	if ( !empty( $default_update_services ) ) {
		switch_to_blog( $blog_ID );
		update_option('ping_sites', $default_update_services);
		restore_current_blog();	
	}
}

function update_services_setting_section() {
	add_settings_section('updates-services', 'Update Services', 'update_services_setting_section_output', 'writing');
}

function update_services_enable($options) {
	$add = array( 'writing' => array( 'ping_sites' ) );

	$options = add_option_whitelist( $add, $options );

	return $options;
}

function update_services_site_admin_options_process() {
	$default_update_services = $_POST['default_update_services'];
	if ( empty( $default_update_services ) ) {
		$default_update_services = 'empty';
	}
	update_site_option( 'default_update_services' , $default_update_services );
}

//------------------------------------------------------------------------//
//---Output Functions-----------------------------------------------------//
//------------------------------------------------------------------------//

function update_services_setting_section_output() {
	if ( get_option('blog_public') ) {
	?>
	<p><label for="ping_sites"><?php _e('When you publish a new post, WordPress automatically notifies the following site update services. For more about this, see <a href="http://codex.wordpress.org/Update_Services">Update Services</a> on the Codex. Separate multiple service <abbr title="Universal Resource Locator">URL</abbr>s with line breaks.', 'update_services') ?></label></p>
	<textarea name="ping_sites" id="ping_sites" class="large-text" rows="3"><?php form_option('ping_sites'); ?></textarea>
	<?php
	} else {
	?>
		<p><?php printf(__('WordPress is not notifying any <a href="http://codex.wordpress.org/Update_Services">Update Services</a> because of your blog\'s <a href="%s">privacy settings</a>.', 'update_services'), 'options-privacy.php'); ?></p>
	<?php
	}
}

function update_services_site_admin_options() {
	$default_update_services = stripslashes( get_site_option('default_update_services') );
	if ( $default_update_services == 'empty' ) {
		$default_update_services = '';
	}
	if ( empty( $default_update_services ) ) {
		if ( get_site_option('default_update_services_initial') != '1' ) {
			$default_update_services = 'http://rpc.pingomatic.com/';
			update_site_option( 'default_update_services_initial' , '1' );
		}
	}
	?>
		<h3><?php _e('Update Services', 'update_services') ?></h3> 
		<table class="form-table">
			<tr valign="top"> 
				<th scope="row"><?php _e('Default Update Services', 'update_services') ?></th> 
				<td><textarea name="default_update_services" type="text" rows="3" wrap="soft" id="default_update_services" style="width: 95%"/><?php echo stripslashes( $default_update_services ); ?></textarea>
					<br />
					<?php _e('Separate multiple service URLs with line breaks. Note that the default updates services will be configured for new blogs only.', 'update_services') ?>
				</td>
			</tr>
		</table>
	<?php
}

if ( !function_exists( 'wdp_un_check' ) ) {
	add_action( 'admin_notices', 'wdp_un_check', 5 );
	add_action( 'network_admin_notices', 'wdp_un_check', 5 );

	function wdp_un_check() {
		if ( !class_exists( 'WPMUDEV_Update_Notifications' ) && current_user_can( 'edit_users' ) )
			echo '<div class="error fade"><p>' . __('Please install the latest version of <a href="http://premium.wpmudev.org/project/update-notifications/" title="Download Now &raquo;">our free Update Notifications plugin</a> which helps you stay up-to-date with the most stable, secure versions of WPMU DEV themes and plugins. <a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">More information &raquo;</a>', 'wpmudev') . '</a></p></div>';
	}
}
