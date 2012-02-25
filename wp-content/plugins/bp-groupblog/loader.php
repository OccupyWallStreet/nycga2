<?php

/*
Plugin Name: BP Groupblog
Plugin URI: http://wordpress.org/extend/plugins/search.php?q=buddypress+groupblog
Description: Automates and links WPMU blogs groups controlled by the group creator.
Author: Rodney Blevins & Marius Ooms
Version: 1.7.1
License: (Groupblog: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html)
Network: true
*/

/**
 * Loads BuddyPress Groupblog only when BP is active
 *
 * @package BP Groupblog
 * @since 1.6
 */
function bp_groupblog_init() {
	// BP Groupblog requires multisite
	if ( !is_multisite() )
		return;

	if ( !bp_is_active( 'groups' ) )
		return;

	require_once( dirname( __FILE__ ) . '/bp-groupblog.php' );
}
add_action( 'bp_include', 'bp_groupblog_init' );

?>
