<?php

// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
	die('-1');
}

/**
 * The Organizer Form (requires the organizer-meta-box.php file)
 * You can customize this view by putting a replacement file of the same name (delete.php) in the events/community/ directory of your theme.
 *
 * @package TribeCommunityEvents
 * @since  1.0
 * @author Modern Tribe Inc.
 */


	if( is_user_logged_in() && $this->allowUsersToEditSubmissions )
		echo '<div id="my-events"><a href="' . $this->getUrl( 'list' ) . '" class="button">' . __( 'My Events', 'tribe-events-community' ) . '</a></div>';

	if( is_user_logged_in() )
		echo '<div id="not-user">' . __( 'Not', 'tribe-events-community' ) . ' <i>' . $current_user->display_name . '</i>? <a href="' . wp_logout_url( get_permalink() ) . '">' . __( 'Log Out', 'tribe-events-community' ) . '</a></div>';

	if( $this->allowUsersToEditSubmissions || is_user_logged_in() )
		echo '<div style="clear:both"></div>';

	$this->outputMessage( $this->messageType );

	?>

	<form method="post" action="">
	   <?php wp_nonce_field( 'ecp_organizer_submission' ); ?>
	<div class="events-community-post-title"></div>
	<div class="events-community-post-content"></div>

	<?php
	$tec = TribeEvents::instance();
	add_filter( 'tribe_events_organizer_meta_box_template', array( $this, 'tribe_community_events_organizer_meta_box_template' ) );

	if ( $tribe_organizer_id ) {
		global $post;
		$post = $organizer;
		$tec->OrganizerMetaBox();
	}
  ?>

   <div class="events-community-footer">
   <input type='submit' class="button submit events-community-submit" value="<?php
   if ( $tribe_organizer_id ) {
   	_e( 'Update Organizer', 'tribe-events-community' );
   } else {
   	_e( 'Submit Organizer', 'tribe-events-community' );
   }
   ?>" name='community-event' />
   </div>

</form>