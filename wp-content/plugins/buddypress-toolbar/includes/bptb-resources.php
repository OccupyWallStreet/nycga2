<?php
/**
 * Display the resource links for the "BuddyPress Group".
 *
 * @package    BuddyPress Toolbar
 * @subpackage Resources
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
 * Resource links collection
 *
 * @since 1.0
 */
$bpgroup_menu_items = array(

	/** Support menu items */
	'bpsupport' => array(
		'parent' => $bpgroup,
		'title'  => __( 'BuddyPress Support', 'buddypress-toolbar' ),
		'href'   => 'http://buddypress.org/support/',
		'meta'   => array( 'title' => __( 'BuddyPress Support', 'buddypress-toolbar' ) )
	),
	'bpsupportwporg' => array(
		'parent' => $bpsupport,
		'title'  => __( 'WordPress.org Support Forum', 'buddypress-toolbar' ),
		'href'   => 'http://wordpress.org/support/plugin/buddypress',
		'meta'   => array( 'title' => __( 'WordPress.org Support Forum', 'buddypress-toolbar' ) )
	),
	'bpsupportticket' => array(
		'parent' => $bpsupport,
		'title'  => __( 'BP Trac: New Ticket', 'buddypress-toolbar' ),
		'href'   => 'http://buddypress.trac.wordpress.org/newticket',
		'meta'   => array( 'title' => __( 'BP Trac: New Ticket', 'buddypress-toolbar' ) )
	),

	/** Codex menu items */
	'bpcodex' => array(
		'parent' => $bpgroup,
		'title'  => __( 'Codex & Documentation', 'buddypress-toolbar' ),
		'href'   => 'http://codex.buddypress.org/',
		'meta'   => array( 'title' => __( 'Codex & Documentation', 'buddypress-toolbar' ) )
	),
	'bpcodex-releases' => array(
		'parent' => $bpcodex,
		'title'  => __( 'Releases', 'buddypress-toolbar' ),
		'href'   => 'http://codex.buddypress.org/releases/',
		'meta'   => array( 'title' => __( 'Releases - see in the sidebar there!', 'buddypress-toolbar' ) )
	),
	'bpcodex-themes' => array(
		'parent' => $bpcodex,
		'title'  => __( 'Theme Development', 'buddypress-toolbar' ),
		'href'   => 'http://codex.buddypress.org/theme-development/',
		'meta'   => array( 'title' => __( 'Theme Development - see in the sidebar there!', 'buddypress-toolbar' ) )
	),
	'bpcodex-developer' => array(
		'parent' => $bpcodex,
		'title'  => __( 'Developer Docs', 'buddypress-toolbar' ),
		'href'   => 'http://codex.buddypress.org/developer-docs/',
		'meta'   => array( 'title' => __( 'Developer Docs - see in the sidebar there!', 'buddypress-toolbar' ) )
	),

	/** Codex search form */
	'bpcodex-searchform' => array(
		'parent' => $bpgroup,
		'title' => '<form method="get" action="http://codex.buddypress.org/" class=" " target="_blank">
		<input type="text" placeholder="' . $bptb_search_codex . '" onblur="this.value=(this.value==\'\') ? \'' . $bptb_search_codex . '\' : this.value;" onfocus="this.value=(this.value==\'' . $bptb_search_codex . '\') ? \'\' : this.value;" value="' . $bptb_search_codex . '" name="s" value="' . esc_attr( 'Search Codex', 'buddypress-toolbar' ) . '" class="text bptb-search-input" />' . $bptb_go_button,
		'href'   => false,
		'meta'   => array( 'target' => '', 'title' => _x( 'Search Codex', 'Translators: For the tooltip', 'buddypress-toolbar' ) )
	),

	/** BuddyPress HQ menu items */
	'bpsites' => array(
		'parent' => $bpgroup,
		'title'  => __( 'BuddyPress HQ', 'buddypress-toolbar' ),
		'href'   => 'http://buddypress.org/',
		'meta'   => array( 'title' => __( 'BuddyPress HQ', 'buddypress-toolbar' ) )
	),
	'bpblog' => array(
		'parent' => $bpsites,
		'title'  => __( 'Official Blog', 'buddypress-toolbar' ),
		'href'   => 'http://buddypress.org/blog/',
		'meta'   => array( 'title' => __( 'Official Blog', 'buddypress-toolbar' ) )
	),
	'bpabout' => array(
		'parent' => $bpsites,
		'title'  => __( 'About BuddyPress', 'buddypress-toolbar' ),
		'href'   => 'http://buddypress.org/about/',
		'meta'   => array( 'title' => __( 'About BuddyPress', 'buddypress-toolbar' ) )
	),
	'bpdevel' => array(
		'parent' => $bpsites,
		'title'  => __( 'Development Updates', 'buddypress-toolbar' ),
		'href'   => 'http://bpdevel.wordpress.com/',
		'meta'   => array( 'title' => __( 'Development Updates', 'buddypress-toolbar' ) )
	),
	'bptrac' => array(
		'parent' => $bpsites,
		'title'  => __( 'Trac: Tickets &amp; Bug Reports', 'buddypress-toolbar' ),
		'href'   => 'http://buddypress.trac.wordpress.org/roadmap',
		'meta'   => array( 'title' => __( 'Trac: Tickets &amp; Bug Reports', 'buddypress-toolbar' ) )
	),
	'bpextendplugins' => array(
		'parent' => $bpsites,
		'title'  => __( 'Extend BuddyPress: Plugins', 'buddypress-toolbar' ),
		'href'   => 'http://buddypress.org/extend/plugins/',
		'meta'   => array( 'title' => __( 'Extend BuddyPress: Plugins', 'buddypress-toolbar' ) )
	),
	'bpextendrecplug' => array(
		'parent' => $bpsites,
		'title'  => __( 'Extend BuddyPress: Recommended Plugins', 'buddypress-toolbar' ),
		'href'   => 'http://buddypress.org/extend/recommended-plugins/',
		'meta'   => array( 'title' => __( 'Extend BuddyPress: Recommended Plugins', 'buddypress-toolbar' ) )
	),
	'bpextendthemes' => array(
		'parent' => $bpsites,
		'title'  => __( 'Extend BuddyPress: Themes', 'buddypress-toolbar' ),
		'href'   => 'http://buddypress.org/extend/themes/',
		'meta'   => array( 'title' => __( 'Extend BuddyPress: Themes', 'buddypress-toolbar' ) )
	),
	'bpluginswporg' => array(
		'parent' => $bpsites,
		'title'  => __( 'More free plugins/extensions at WP.org', 'buddypress-toolbar' ),
		'href'   => 'http://wordpress.org/extend/plugins/tags/buddypress/',
		'meta'   => array( 'title' => __( 'More free plugins/extensions at WP.org', 'buddypress-toolbar' ) )
	),
	'bpthemeswporg' => array(
		'parent' => $bpsites,
		'title'  => __( 'More free Themes at WP.org', 'buddypress-toolbar' ),
		'href'   => 'http://wordpress.org/extend/themes/tags/buddypress',
		'meta'   => array( 'title' => __( 'More free Themes at WP.org', 'buddypress-toolbar' ) )
	),
	'bptrickscom' => array(
		'parent' => $bpsites,
		'title'  => __( 'BP-Tricks.com (Community Site)', 'buddypress-toolbar' ),
		'href'   => 'http://bp-tricks.com/',
		'meta'   => array( 'title' => __( 'BP-Tricks.com - A BuddyPress Community Site', 'buddypress-toolbar' ) )
	),
	'bpffnews' => array(
		'parent' => $bpsites,
		'title'  => __( 'BuddyPress News Planet', 'buddypress-toolbar' ),
		'href'   => 'http://friendfeed.com/buddypress-news',
		'meta'   => array( 'title' => __( 'BuddyPress News Planet (official and community news via FriendFeed service)', 'buddypress-toolbar' ) )
	),
);
