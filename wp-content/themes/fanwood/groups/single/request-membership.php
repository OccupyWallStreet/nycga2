<?php

/**
 * BuddyPress - Single Group Request Membership
 *
 * This template is currently NOT active. It should be when BuddyPress 1.6 is released.
 *
 * @package BuddyPress
 * @subpackage Theme
 */
 
?>

<div class="entry-content">

	<?php do_action( 'bp_before_group_request_membership_content' ) ?>

	<?php if ( !bp_group_has_requested_membership() ) : ?>
		<p><?php printf( __( "You are requesting to become a member of the group '%s'.", "buddypress" ), bp_get_group_name( false ) ); ?></p>

		<form action="<?php bp_group_form_action('request-membership') ?>" method="post" name="request-membership-form" id="request-membership-form" class="standard-form">
			<p>
				<label for="group-request-membership-comments"><?php _e( 'Comments (optional)', 'buddypress' ); ?></label><br />
				<textarea name="group-request-membership-comments" id="group-request-membership-comments"></textarea>
			</p>

			<?php do_action( 'bp_group_request_membership_content' ) ?>

			<p class="submit">
				<input type="submit" name="group-request-send" id="group-request-send" value="<?php _e( 'Send Request', 'buddypress' ) ?>" />
			</p>

			<?php wp_nonce_field( 'groups_request_membership' ) ?>
		</form><!-- #request-membership-form -->
	<?php endif; ?>

	<?php do_action( 'bp_after_group_request_membership_content' ) ?>

</div><!-- .entry-content -->
