<?php

/**
 * Single User Edit Part
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

	<?php
		do_action( 'bbp_template_notices' );

		// Profile details
		bbp_get_template_part( 'bbpress/user', 'details' );
	?>
	
	<div class="bbp-template-notice">	
		<a href="<?php bbp_user_profile_url(); ?>" title="<?php printf( __( 'Profile of User %s', 'bbpress' ), esc_attr( bbp_get_displayed_user_field( 'display_name' ) ) ); ?>"><?php _e( 'Back to Profile', 'bbpress' ); ?></a>

	</div>

	<?php
		// User edit form
		bbp_get_template_part( 'bbpress/form', 'user-edit' );

	?>