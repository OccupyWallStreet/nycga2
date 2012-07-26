<?php
/*
 Plugin Name: The Events Calendar: Community Events
 Description: Community Events is an add-on providing additional functionality to the open source plugin The Events Calendar. Empower users to submit and manage their events on your website. <a href="http://tri.be/shop/wordpress-community-events/?ref=tec-community-plugin">Check out the full feature list</a>. Need more features? Peruse our selection of <a href="http://tri.be/shop/" target="_blank">plugins</a>.
 Version: 1.0.1.1
 Author: Modern Tribe, Inc.
 Author URI: http://tri.be?ref=tec-plugin
 Text Domain: tribe-events-community
 License: GPLv2 or later
*/

/*
Copyright 2011-2012 by Modern Tribe Inc and the contributors

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

require_once( dirname( __FILE__ ) . '/lib/tribe-community-events.class.php' );
require_once( dirname( __FILE__ ) . '/vendor/tribe-common-libraries/tribe-common-libraries.class.php' );
require_once( dirname( __FILE__ ) . '/lib/tribe-community-events-template-tags.php' );

/**
 * Instantiate class and set up WordPress actions.
 */
function Tribe_CE_Load() {
	if ( class_exists( 'TribeEvents' ) && defined( 'TribeEvents::VERSION' ) && version_compare( TribeEvents::VERSION, TribeCommunityEvents::REQUIRED_TEC_VERSION, '>=' ) ) {
		TribeCommunityEvents::instance();
	} else {
		add_action( 'admin_notices', 'tribe_ce_show_fail_message' );
	}
}

/**
 * Shows message if the plugin can't load due to TEC not being installed.
 */
function tribe_ce_show_fail_message() {
	if ( current_user_can( 'activate_plugins' ) ) {
		$url = 'plugin-install.php?tab=plugin-information&plugin=the-events-calendar&TB_iframe=true';
		$title = __( 'The Events Calendar', 'tribe-events-community' );
		echo '<div class="error"><p>'.sprintf( __( 'To begin using <b>The Events Calendar: Community Events</b>, please install the latest version of %s.', 'tribe-events-community' ), '<a href="%s" class="thickbox" title="%s">' . __( 'The Events Calendar', 'tribe-events-community' ) . '</a>', $title ) . '</p></div>';
	}
}



function tribe_ce_uninstall() {
	delete_option( 'pue_install_key_events_community' );
}

register_uninstall_hook( __FILE__ , 'tribe_ce_uninstall' );
register_activation_hook( __FILE__ , array( 'TribeCommunityEvents', 'activateFlushRewrite' ) );

add_action( 'plugins_loaded', 'Tribe_CE_Load', 1 ); // high priority so that it's not too late for tribe_register-helpers class