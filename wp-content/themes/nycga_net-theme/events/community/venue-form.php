<?php

// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
	die('-1');
}

/**
 * The Venue Form
 *
 * You can customize this view by putting a replacement file of the same name (delete.php) in the events/community/ directory of your theme.
 *
 * @package TribeCommunityEvents
 * @since  1.0
 * @author Modern Tribe Inc.
 */

?>

<form method="post" action="">
   <?php wp_nonce_field( 'ecp_venue_submission' ); ?>
   <div class="events-community-post-title"><label for='post_title' <?php if ( $_POST && empty( $venue->post_title ) ) echo 'class="error"'; ?>><?php _e( 'Venue Name:', 'tribe-events-community' ); ?></label> <small class="req">(required)</small><input type="text" name="post_title" value="<?php if ( isset( $venue->post_title ) ) echo esc_html( $venue->post_title ); ?>"/></div>
   <div class="events-community-post-content">
      <label for='post_content'><?php _e( 'Venue Description:', 'tribe-events-community' ); ?></label> <small class="req"></small>

		<?php
		// if the admin wants the rich editor, and they are using WP 3.3+, show the WYSIWYG, otherwise default to just a textarea
		if ( $this->useVisualEditor && function_exists( 'wp_editor' ) ) {
			$settings = array(
			    'wpautop' => true,
			    'media_buttons' => false,
			    'editor_class' => 'frontend',
			    'textarea_rows' => 5,
			    'tabindex' => 2,
			);

			if ( isset( $venue->post_content ) ) {
				wp_editor( $venue->post_content, 'tcepostcontent', $settings );
			} else {
				wp_editor( '', 'tcepostcontent', $settings );
			}
		} else {

			?> <textarea name="tcepostcontent"><?php if ( isset( $venue->post_content ) ) echo esc_textarea( $venue->post_content ); ?></textarea> <?php
		}
		?>
   </div>

	<?php

	$tec = TribeEvents::instance();

	//filter include paths to redirect to local view files
	add_filter( 'tribe_events_venue_meta_box_template' , array( $this, 'tribe_community_events_venue_meta_box_template' ) );

	if ( $tribe_venue_id ) {
		global $post;
		$post = $venue;
		$tec->VenueMetaBox();
	}

   ?>

   <div class="events-community-footer">
   <input type='submit' class="button submit events-community-submit" value="<?php
   if ( $tribe_venue_id ) {
    _e( 'Update Venue', 'tribe-events-community' );
  } else {
  	_e( 'Submit Venue', 'tribe-events-community' );
  }
   ?>" name='community-event' />
   </div>

</form>