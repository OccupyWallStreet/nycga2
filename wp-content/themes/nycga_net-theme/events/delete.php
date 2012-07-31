<?php

// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
	die('-1');
}


/**
 * Delete event screen
 * You can customize this view by putting a replacement file of the same name (delete.php) in the events/community/ directory of your theme.
 *
 * @package TribeCommunityEvents
 * @since  1.0
 * @author Modern Tribe Inc.
 */

	echo '<div id="add-new"><a href="' . $this->getUrl( 'add' ).'" class="button">' . __( 'Add New', 'tribe-events-community' ) . '</a></div>';
	echo '<div id="my-events"><a href="' . $this->getUrl( 'list' ).'" class="button">' . __( 'My Events', 'tribe-events-community' ) . '</a></div>';
	echo '<div id="not-user">' . __( 'Not', 'tribe-events-community' ) . ' <i>' . $current_user->display_name . '</i> ? <a href="' .wp_logout_url( get_permalink() ) . '">' . __( 'Log Out', 'tribe-events-community' ).'</a></div>';
	echo '<div style="clear:both"></div>';

	$this->outputMessage();