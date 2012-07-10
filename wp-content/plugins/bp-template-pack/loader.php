<?php
/*
Plugin Name: BuddyPress Template Pack
Plugin URI: http://wordpress.org/extend/plugins/bp-template-pack/
Description: Add support for BuddyPress to your existing WordPress theme. This plugin will guide you through the process step by step.
Author: apeatling, boonebgorges, r-a-y
Version: 1.2.1
Author URI: http://buddypress.org
*/

/**
 * BP Template Pack
 *
 * @package BP_TPack
 * @subpackage Loader
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Initialize the plugin once BuddyPress has initialized.
 */
function bp_tpack_loader() {
	if ( is_admin() )
		include( dirname( __FILE__ ) . '/bpt-admin.php' );

	include( dirname( __FILE__ ) . '/bpt-functions.php' );
}
add_action( 'bp_include', 'bp_tpack_loader' );

?>
