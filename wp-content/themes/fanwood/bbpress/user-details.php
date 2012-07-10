<?php

/**
 * User Details
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<div class="loop-meta vcard">

	<h1 class="loop-title">
		<?php printf( __( 'About %s', 'bbpress' ), bbp_get_displayed_user_field( 'display_name' ) ); ?>
	
		<?php if ( bbp_is_user_home() || current_user_can( 'edit_users' ) ) : ?>

			<a href="<?php bbp_user_profile_edit_url(); ?>" title="<?php printf( __( 'Edit Profile of %s', 'bbpress' ), esc_attr( bbp_get_displayed_user_field( 'display_name' ) ) ); ?>"><?php _e( '(Edit)', 'bbpress' ); ?></a>
			
		<?php endif; ?>
	</h1>

	<div class="loop-description">
		<?php echo bbp_current_user_avatar( 50 ); ?>
		<p><?php echo bbp_get_displayed_user_field( 'description' ); ?></p>
	</div><!-- .loop-description -->
	
</div><!-- .loop-meta -->