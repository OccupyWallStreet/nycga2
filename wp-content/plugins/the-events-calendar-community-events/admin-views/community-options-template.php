<?php

// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
	die('-1');
}


/**
 * Tribe Community Events settings
 *
 * @package TribeCommunityEvents
 * @since  1.0
 * @author Modern Tribe Inc.
 */

$tce = TribeCommunityEvents::instance();

$blockRolesList = $tce->getOption( 'blockRolesList' );

if ( empty($blockRolesList) ) $blockRolesList = array();

$redirectRoles = array();

foreach ( get_editable_roles() as $role => $atts ) {
	//don't let them lock admins out
	if($role == 'administrator')
		continue;

	$redirectRoles[$role] = $atts['name'];
}

$statuses = array( 'draft' => 'Draft', 'pending' => 'Pending Review', 'publish' => 'Published' );
$statuses = apply_filters( 'tribe_community_events_default_status_options', $statuses );

$trash_vs_delete_options = array( 
	'1' => 'Placed in the Trash',
	'0' => 'Permanently Deleted'
);

if ( TribeEvents::ecpActive() ) {
	$organizers = TribeEvents::instance()->get_organizer_info();
	$organizer_options = array();
	if ( is_array( $organizers ) && !empty( $organizers ) ) {
		$organizer_options[0] = __( 'No Default', 'tribe-events-community-pro' );
		foreach ( $organizers as $organizer ) {
			$organizer_options[$organizer->ID] = $organizer->post_title;
		}
	}

	$venues = TribeEvents::instance()->get_venue_info();
	$venue_options = array();
	if ( is_array( $venues ) && !empty( $venues ) ) {
		$venue_options[0] = __( 'Use New Venue/No Default', 'tribe-events-community-pro' );
		foreach ( $venues as $venue ) {
			$venue_options[$venue->ID] = $venue->post_title;
		}
	}
}

$communityTab = array(
	'priority' => 36,
	'fields' => array(
		'info-start' => array(
			'type' => 'html',
			'html' => '<div id="modern-tribe-info">'
		),
		'info-box-title' => array(
			'type' => 'html',
			'html' => '<h2>' . __('Community Events Settings', 'tribe-events-comunity') . '</h2>',
		),
		'info-box-description' => array(
			'type' => 'html',
			'html' => '<p>' . __('Community Events enables frontend event-submission on your site. Whether soliciting contributions from anonymous users or registered members of the site, you as the admin have complete editorial control over what makes it on the calendar.</p><p>Before jumping into it, make sure you\'ve checked out our <a href="http://tri.be/support/documentation/community-events-new-user-primer/">Community Events new user primer</a>: it features both videos and written steps covering how the plugin works and what the different options available on this settings page can do to enhance your site.', 'tribe-events-community') . '</p>',
		),
		'info-end' => array(
			'type' => 'html',
			'html' => '</div>',
		),
		'general-heading' => array(
		'type' => 'heading',
		'label' => __( 'General', 'tribe-events-community' ),
		'parent_option' => TribeCommunityEvents::OPTIONNAME,
	),

		'allowAnonymousSubmissions' => array(
			'type' => 'checkbox_bool',
			'label' => __( 'Allow anonymous submissions', 'tribe-events-community' ),
			'default' => false,
			'validation_type' => 'boolean',
			'parent_option' => TribeCommunityEvents::OPTIONNAME,
		),

		'useVisualEditor' => array(
			'type' => 'checkbox_bool',
			'label' => __( 'Use visual editor for event descriptions', 'tribe-events-community' ),
			'tooltip' => __( 'Requires WordPress 3.3 +', 'tribe-events-community', 'tribe-events-community' ),
			'default' => false,
			'validation_type' => 'boolean',
			'parent_option' => TribeCommunityEvents::OPTIONNAME,
		),

		'defaultStatus' => array(
			'type' => 'dropdown',
		 	'label' => __( 'Default status for submitted events', 'tribe-events-community' ),
			'validation_type' => 'options',
			'size' => 'medium',
			'default' => 'draft',
			'options' => $statuses,
			'parent_option' => TribeCommunityEvents::OPTIONNAME,
		),

		'communityRewriteSlug' => array(
			'type' => 'text',
		 	'label' => __( 'Community rewrite slug', 'tribe-events-community' ),
			'validation_type' => 'slug',
			'size' => 'medium',
			'default' => 'community',
			'tooltip' => __( 'The slug used for building the community events URL -- it uses the main events slug as well.', 'tribe-events-community' ),
			'parent_option' => TribeCommunityEvents::OPTIONNAME,
		),

		'current-community-events-slug' => array(
			'type' => 'html',
			'display_callback' => 'tribe_display_current_community_events_slug',
			'conditional' => ( '' != get_option( 'permalink_structure' ) ),
		),


	'alerts-heading' => array(
		'type' => 'heading',
		'label' => __( 'Alerts', 'tribe-events-community' ),
	),

		'emailAlertsEnabled' => array(
			'type' => 'checkbox_bool',
			'label' => __( 'Send an email alert when a new event is submitted', 'tribe-events-community' ),
			'default' => false,
			'validation_type' => 'boolean',
			'parent_option' => TribeCommunityEvents::OPTIONNAME,
		),

		'emailAlertsList' => array(
			'type' => 'textarea',
			'label' => __( 'Email addresses to be notified', 'tribe-events-community' ),
			'default' => get_option( 'admin_email' ),
			'validation_type' => 'html',
			'tooltip' => __( 'One address per line', 'tribe-events-community' ),
			'parent_option' => TribeCommunityEvents::OPTIONNAME,
		),



	'member-heading' => array(
		'type' => 'heading',
		'label' => __( 'Members', 'tribe-events-community' ),
	),

		'member-info' => array(
			'type' => 'html',
			'html' => '<p>'. __( 'Options to grant members (logged in users) access to their submitted events', 'tribe-events-community' ) .'</p>',
		),

		'allowUsersToEditSubmissions' => array(
			'type' => 'checkbox_bool',
			'label' => __( 'Allow users to edit their submissions', 'tribe-events-community' ),
			'default' => false,
			'validation_type' => 'boolean',
			'parent_option' => TribeCommunityEvents::OPTIONNAME,
		),

		'allowUsersToDeleteSubmissions' => array(
			'type' => 'checkbox_bool',
			'label' => __( 'Allow users to remove their submissions', 'tribe-events-community' ),
			'default' => false,
			'validation_type' => 'boolean',
			'parent_option' => TribeCommunityEvents::OPTIONNAME,
		),
		
		'trashItemsVsDelete' => array(
			'type' => 'radio',
			'label' => __( 'Deleted events should be:', 'tribe-events-community' ),
			'options' => $trash_vs_delete_options,
			'default' => '1',
			'validation_type' => 'options',
			'parent_option' => TribeCommunityEvents::OPTIONNAME,
		),

		'myevents-heading' => array(
			'type' => 'heading',
			'label' => __( 'My Events', 'tribe-events-community' ),
		),

		'eventsPerPage' => array(
			'type' => 'text',
			'label' => __( 'Events per page', 'tribe-events-community' ),
			'tooltip' => __( 'This is the number of events displayed per page', 'tribe-events-community' ),
			'default' => 10,
			'validation_type' => 'positive_int',
			'parent_option' => TribeCommunityEvents::OPTIONNAME,
		 ),

		'eventListDateFormat' => array(
			'type' => 'text',
			'label' => __( 'Date/time format', 'tribe-events-community' ),
			'tooltip' => '<a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">' . __( 'Help with Date &amp; Time Formatting', 'tribe-events-community' ) . '</a>',
			'default' => 'M j, Y',
			'validation_type' => 'html',
			'parent_option' => TribeCommunityEvents::OPTIONNAME,
		 ),

		'access-heading' => array(
			'type' => 'heading',
			'label' => __( 'Access Control', 'tribe-events-community' ),
		),

		'blockRolesFromAdmin' => array(
			'type' => 'checkbox_bool',
			'label' => __( 'Block access to WordPress Admin', 'tribe-events-community' ),
			'tooltip' => __( 'Also disables the admin bar', 'tribe-events-community' ),
			'default' => false,
			'validation_type' => 'boolean',
			'parent_option' => TribeCommunityEvents::OPTIONNAME,
		),

		'blockRolesList' => array(
			'type' => 'checkbox_list',
			'label' => __( 'Roles to block', 'tribe-events-community' ),
			'default' => array(),
			'options' => $redirectRoles,
			'validation_type' => 'options_multi',
			'can_be_empty' => true,
			'parent_option' => TribeCommunityEvents::OPTIONNAME,
		),

		'blockRolesRedirect' => array(
			'type' => 'text',
			'label' => __( 'Redirect URL', 'tribe-events-community' ),
			'tooltip' => __( 'Where users will be sent if they attempt to access the admin<br>Should be a full url e.g. http://domain.com/path/ <br> Leave blank for homepage', 'tribe-events-community' ),
			'default' => '',
			'validation_type' => 'url',
			'can_be_empty' => true,
			'parent_option' => TribeCommunityEvents::OPTIONNAME,
		 ),


		'ecp-heading' => array(
				'type' => 'heading',
				'label' => __( 'Pro Options', 'tribe-events-community' ),
				'conditional' => ( TribeEvents::ecpActive() && ( $venue_options || $organizer_options ) ),
			),

		'defaultCommunityVenueID' => array(
			'type' => 'dropdown_chosen',
		 	'label' => __( 'Default venue for submitted events', 'tribe-events-community' ),
			'validation_type' => 'options',
			'default' => 0,
			'options' => $venue_options,
			'parent_option' => TribeCommunityEvents::OPTIONNAME,
			'can_be_empty' => true,
			'conditional' => ( TribeEvents::ecpActive() && $venue_options ),
		),

		'defaultCommunityOrganizerID' => array(
			'type' => 'dropdown_chosen',
		 	'label' => __( 'Default organizer for submitted events', 'tribe-events-community' ),
			'validation_type' => 'options',
			'default' => 0,
			'options' => $organizer_options,
			'parent_option' => TribeCommunityEvents::OPTIONNAME,
			'can_be_empty' => true,
			'conditional' => ( TribeEvents::ecpActive() && $organizer_options ),
		),


	),
);

function tribe_display_current_community_events_slug() {
	echo '<p class="tribe-field-indent tribe-field-description description">' . __( 'Your current community events URLs are', 'tribe-events-community' ) . ' <br /><code>' . tribe_get_events_link() . TribeCommunityEvents::getOption( 'communityRewriteSlug', 'community', true ) . '/add</code><br /><code>' . tribe_get_events_link() . TribeCommunityEvents::getOption( 'communityRewriteSlug', 'community', true ) . '/list</code></p>';
}