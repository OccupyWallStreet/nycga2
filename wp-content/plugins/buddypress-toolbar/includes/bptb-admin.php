<?php
/**
 * Helper functions for the admin - plugin links and help tabs.
 *
 * @package    BuddyPress Toolbar
 * @subpackage Admin
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright 2012, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link       http://genesisthemes.de/en/wp-plugins/buddypress-toolbar/
 * @link       http://twitter.com/#!/deckerweb
 *
 * @since 1.0
 * @version 1.1
 */

/**
 * Setting internal plugin helper links constants
 *
 * @since 1.3
 */
define( 'BPTB_URL_TRANSLATE',		'http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/buddypress-toolbar' );
define( 'BPTB_URL_WPORG_FAQ',		'http://wordpress.org/extend/plugins/buddypress-toolbar/faq/' );
define( 'BPTB_URL_WPORG_FORUM',		'http://wordpress.org/support/plugin/buddypress-toolbar' );
if ( get_locale() == 'de_DE' || get_locale() == 'de_AT' || get_locale() == 'de_CH' || get_locale() == 'de_LU' ) {
	define( 'BPTB_URL_DONATE', 	'http://genesisthemes.de/spenden/' );
} else {
	define( 'BPTB_URL_DONATE', 	'http://genesisthemes.de/en/donate/' );
}


add_filter( 'plugin_row_meta', 'ddw_bptb_plugin_links', 10, 2 );
/**
 * Add various support links to plugin page
 *
 * @since 1.0
 * @version 1.1
 *
 * @param  $bptb_links
 * @param  $bptb_file
 * @return strings plugin links
 */
function ddw_bptb_plugin_links( $bptb_links, $bptb_file ) {

	if ( ! current_user_can( 'install_plugins' ) )
		return $bptb_links;

	if ( $bptb_file == BPTB_PLUGIN_BASEDIR . '/buddypress-toolbar.php' ) {
		$bptb_links[] = '<a href="' . BPTB_URL_WPORG_FAQ . '" target="_new" title="' . __( 'FAQ', 'buddypress-toolbar' ) . '">' . __( 'FAQ', 'buddypress-toolbar' ) . '</a>';
		$bptb_links[] = '<a href="' . BPTB_URL_WPORG_FORUM . '" target="_new" title="' . __( 'Support', 'buddypress-toolbar' ) . '">' . __( 'Support', 'buddypress-toolbar' ) . '</a>';
		$bptb_links[] = '<a href="' . BPTB_URL_TRANSLATE . '" target="_new" title="' . __( 'Translations', 'buddypress-toolbar' ) . '">' . __( 'Translations', 'buddypress-toolbar' ) . '</a>';
		$bptb_links[] = '<a href="' . BPTB_URL_DONATE . '" target="_new" title="' . __( 'Donate', 'buddypress-toolbar' ) . '">' . __( 'Donate', 'buddypress-toolbar' ) . '</a>';
	}

	return $bptb_links;

}  // end of function ddw_bptb_plugin_links
