<?php

// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
	die('-1');
}


/**
 * The Submit and Edit event form
 * You can customize this view by putting a replacement file of the same name (event-form.php) in the events/community/ directory of your theme.
 *
 * @package TribeCommunityEvents
 * @since  1.0
 * @author Modern Tribe Inc.
 */
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#show_hidden_categories').click(function() {
		//jQuery('#event-categories').css('overflow-y', 'scroll');
		jQuery('.hidden_category').show('medium');
		jQuery('#show_hidden_categories').hide();
		return false;

	}); 
});

</script>

<form method="post" action="" enctype="multipart/form-data">
	<?php $this->outputMessage(); ?>
	<?php wp_nonce_field( 'ecp_event_submission' ); ?>
	<div class="events-community-post-title">
		<label for='post_title' <?php if ( $_POST && empty( $event->post_title ) ) echo 'class="error"'; ?>><?php _e( 'Event Title:', 'tribe-events-community' ); ?></label> <small class="req">(required)</small>
		<?php $this->formTitle( $event ) ?>
	</div>

	<div class="events-community-post-content">
		<label for='post_content' <?php if ( $_POST && empty( $event->post_content)  ) echo 'class="error"'; ?>><?php _e( 'Event Description:', 'tribe-events-community' ); ?></label> <small class="req">(required)</small>
		<?php $this->formContentEditor( $event ); ?>
	</div>

	<div id='eventDetails' class="inside eventForm bubble">

	<table cellspacing="0" cellpadding="0" id="EventInfo">
		<tr>
			<td colspan="2" class="tribe_sectionheader"><h4 class="event-time">Event Categories</h4></td>
		</tr>
		<tr>
			<td>
				<?php
					$this->formCategoryDropdown( $event );
				?>
			</td>
		</tr>
	</table>
	</div>

	<p></p>


	<div id='eventDetails' class="inside eventForm bubble">

	<table cellspacing="0" cellpadding="0" id="EventInfo">
		<tr>
			<td colspan="2" class="tribe_sectionheader"><h4 class="event-time">Event Image!!</h4></td>
		</tr>
		<?php
		$thumb = has_post_thumbnail( $tribe_event_id );
		if( $thumb ) : ?>
		<tr>
			<td colspan="2" class="event-image"><p><?php echo get_the_post_thumbnail( $event->ID, 'medium' ); ?></p></td>
		</tr>
		<tr>
			<td colspan="2"><p><?php echo $this->getDeleteFeaturedImageButton( $event ); ?></p></td>
		</tr>
		<?php endif; ?>

		<tr>
			<td>Upload <?php if( !$thumb ) echo ' New'; ?></td>
			<td>
				<input type="file" name="event_image">
			</td>
		</tr>
			<td><p><?php echo __('Images that are not png, jpg, or gif will not be uploaded.', 'tribe-community-events' ) ?></p></td>
			<td></td>
	</table>
	</div>

	<p></p>

	<?php
	$this->formEventDetails( $event );
	$this->formSpamControl();
	?>

	<div class="events-community-footer wp-admin events-cal">
	<input type='submit' id="post" class="button submit events-community-submit" value="<?php
	if ( isset( $tribe_event_id ) && $tribe_event_id ) {
		_e( 'Update Event', 'tribe-events-community' );
	} else {
	 _e( 'Submit Event', 'tribe-events-community' );
	}
	?>" name='community-event' />
	</div>

</form>