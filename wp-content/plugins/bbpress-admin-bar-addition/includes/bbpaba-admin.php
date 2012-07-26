<?php
/**
 * Helper functions for the admin - plugin links.
 *
 * @package    bbPress Admin Bar Addition
 * @subpackage Admin
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright 2011-2012, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link       http://genesisthemes.de/en/wp-plugins/bbpress-admin-bar-addition/
 * @link       http://twitter.com/#!/deckerweb
 *
 * @since 1.0
 * @version 1.1
 */

add_filter( 'plugin_row_meta', 'ddw_bbpaba_plugin_links', 10, 2 );
/**
 * Add various support links to plugin page
 *
 * @since 1.0
 *
 * @param  $bbpaba_links
 * @param  $bbpaba_file
 * @return strings plugin links
 */
function ddw_bbpaba_plugin_links( $bbpaba_links, $bbpaba_file ) {

	if ( ! current_user_can( 'install_plugins' ) )
		return $bbpaba_links;

	if ( $bbpaba_file == BBPABA_PLUGIN_BASEDIR . '/bbpress-admin-bar-addition.php' ) {
		$bbpaba_links[] = '<a href="http://wordpress.org/extend/plugins/bbpress-admin-bar-addition/faq/" target="_new" title="' . __( 'FAQ', 'bbpaba' ) . '">' . __( 'FAQ', 'bbpaba' ) . '</a>';
		$bbpaba_links[] = '<a href="http://wordpress.org/support/plugin/bbpress-admin-bar-addition" target="_new" title="' . __( 'Support', 'bbpaba' ) . '">' . __( 'Support', 'bbpaba' ) . '</a>';
		$bbpaba_links[] = '<a href="' . __( 'http://genesisthemes.de/en/donate/', 'bbpaba' ) . '" target="_new" title="' . __( 'Donate', 'bbpaba' ) . '">' . __( 'Donate', 'bbpaba' ) . '</a>';
	}

	return $bbpaba_links;

}  // end of function ddw_bbpaba_plugin_links
