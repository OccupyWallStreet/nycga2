<?php
/**
 * Display links to active extensions specific settings' pages: Events Manager.
 *
 * @package    BuddyPress Toolbar
 * @subpackage Plugin/Extension Support
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright 2012, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link       http://genesisthemes.de/en/wp-plugins/buddypress-toolbar/
 * @link       http://twitter.com/#!/deckerweb
 *
 * @since 1.3
 */

/**
 * Support for: Events Manager (free, by Marcus Sykes)
 *
 * @since 1.1
 */
	/** Entries at "Extensions" level submenu */
	/** Events */
	if ( current_user_can( 'edit_events' ) || current_user_can( 'edit_posts' ) ) {
		$menu_items['exteventsmanager'] = array(
			'parent' => $extensions,
			'title'  => __( 'Events Manager', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit.php?post_type=event' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Events Manager', 'buddypress-toolbar' ) )
		);
		$menu_items['exteventsmanager-add'] = array(
			'parent' => $exteventsmanager,
			'title'  => __( 'Add new Event', 'buddypress-toolbar' ),
			'href'   => admin_url( 'post-new.php?post_type=event' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Add new Event', 'buddypress-toolbar' ) )
		);
	}  // end-if cap check

	/** Event Tags */
	if ( get_option( 'dbem_tags_enabled', true ) && ( current_user_can( 'edit_event_categories' ) || current_user_can( 'manage_terms' ) ) ) {
		$menu_items['exteventsmanager-tags'] = array(
			'parent' => $exteventsmanager,
			'title'  => __( 'Event Tags', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit-tags.php?taxonomy=event-tags&post_type=event' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Event Tags', 'buddypress-toolbar' ) )
		);
	}  // end-if cap check

	/** Event Categories */
	if ( get_option( 'dbem_categories_enabled', true ) && ( current_user_can( 'edit_recurring_events' ) || current_user_can( 'manage_terms' ) ) ) {
		$menu_items['exteventsmanager-categories'] = array(
			'parent' => $exteventsmanager,
			'title'  => __( 'Event Categories', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit-tags.php?taxonomy=event-categories&post_type=event' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Event Categories', 'buddypress-toolbar' ) )
		);
	}  // end-if cap check

	/** Recurring Events */
	if ( get_option( 'dbem_recurrence_enabled' ) && ( current_user_can( 'edit_event_categories' ) || current_user_can( 'edit_posts' ) ) ) {
		$menu_items['exteventsmanager-recurring'] = array(
			'parent' => $exteventsmanager,
			'title'  => __( 'Recurring Events', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit.php?post_type=event-recurring' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Recurring Events', 'buddypress-toolbar' ) )
		);
	}  // end-if cap check

	/** Locations */
	if ( get_option( 'dbem_locations_enabled', true ) && ( current_user_can( 'edit_locations' ) || current_user_can( 'edit_posts' ) ) ) {
		$menu_items['exteventsmanager-locations'] = array(
			'parent' => $exteventsmanager,
			'title'  => __( 'Locations', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit.php?post_type=location' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Locations', 'buddypress-toolbar' ) )
		);
	}  // end-if cap check

	/** Bookings */
	if ( get_option( 'dbem_rsvp_enabled' ) && current_user_can( 'manage_bookings' ) ) {
		$menu_items['exteventsmanager-bookings'] = array(
			'parent' => $exteventsmanager,
			'title'  => __( 'Bookings', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit.php?post_type=event&page=events-manager-bookings' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Bookings', 'buddypress-toolbar' ) )
		);
	}  // end-if cap check

	/** Settings */
	if ( current_user_can( 'activate_plugins' ) ) {
		$menu_items['exteventsmanager-settings'] = array(
			'parent' => $exteventsmanager,
			'title'  => __( 'Settings', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit.php?post_type=event&page=events-manager-options' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Settings', 'buddypress-toolbar' ) )
		);
	}  // end-if cap check

	/** Multisite Stuff */
	if ( is_multisite() && current_user_can( 'activate_plugins' ) ) {
		/** Multisite: Settings */
		$menu_items['exteventsmanager-networksettings'] = array(
			'parent' => $exteventsmanager,
			'title'  => __( 'Multisite Settings', 'buddypress-toolbar' ),
			'href'   => network_admin_url( 'admin.php?page=events-manager-options' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Multisite Settings', 'buddypress-toolbar' ) )
		);
		/** Multisite: Update */
		$menu_items['exteventsmanager-networkupdate'] = array(
			'parent' => $exteventsmanager,
			'title'  => __( 'Multisite: Update Sites', 'buddypress-toolbar' ),
			'href'   => network_admin_url( 'admin.php?page=events-manager-update' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Multisite: Update Sites', 'buddypress-toolbar' ) )
		);
	}  // end-if cap check

	/** Help */
	if ( current_user_can( 'activate_plugins' ) ) {
		$menu_items['exteventsmanager-help'] = array(
			'parent' => $exteventsmanager,
			'title'  => __( 'Help', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit.php?post_type=event&page=events-manager-help' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Help', 'buddypress-toolbar' ) )
		);
	}  // end-if cap check
