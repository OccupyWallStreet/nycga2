<?php

/**
 * BuddyPress - Single Member WordPress Profile
 *
 * @package BuddyPress
 * @subpackage Theme
 */
 
?>

<?php do_action( 'bp_before_profile_loop_content' ) ?>

<?php $ud = get_userdata( bp_displayed_user_id() ); ?>

<?php do_action( 'bp_before_profile_field_content' ) ?>

	<div class="bp-widget wp-profile">
	
		<div class="entry-content">
	
			<h2><?php bp_is_my_profile() ? _e( 'My Profile', 'buddypress' ) : printf( __( "%s's Profile", 'buddypress' ), bp_get_displayed_user_fullname() ); ?></h2>

			<table class="wp-profile-fields">

				<?php if ( $ud->display_name ) : ?>

					<tr id="wp_displayname">
						<td class="label"><p><?php _e( 'Name', 'buddypress' ); ?></p></td>
						<td class="data"><?php echo $ud->display_name; ?></td>
					</tr>

				<?php endif; ?>

				<?php if ( $ud->user_description ) : ?>

					<tr id="wp_desc">
						<td class="label"><p><?php _e( 'About Me', 'buddypress' ); ?></p></td>
						<td class="data"><?php echo $ud->user_description; ?></td>
					</tr>

				<?php endif; ?>

				<?php if ( $ud->user_url ) : ?>

					<tr id="wp_website">
						<td class="label"><p><?php _e( 'Website', 'buddypress' ); ?></p></td>
						<td class="data"><?php echo make_clickable( $ud->user_url ); ?></td>
					</tr>

				<?php endif; ?>

				<?php if ( $ud->jabber ) : ?>

					<tr id="wp_jabber">
						<td class="label"><p><?php _e( 'Jabber', 'buddypress' ); ?></p></td>
						<td class="data"><?php echo $ud->jabber; ?></td>
					</tr>

				<?php endif; ?>

				<?php if ( $ud->aim ) : ?>

					<tr id="wp_aim">
						<td class="label"><p><?php _e( 'AOL Messenger', 'buddypress' ); ?></p></td>
						<td class="data"><?php echo $ud->aim; ?></td>
					</tr>

				<?php endif; ?>

				<?php if ( $ud->yim ) : ?>

					<tr id="wp_yim">
						<td class="label"><p><?php _e( 'Yahoo Messenger', 'buddypress' ); ?></p></td>
						<td class="data"><?php echo $ud->yim; ?></td>
					</tr>

				<?php endif; ?>

			</table>
		
		</div><!-- .entry-content -->
	</div>

<?php do_action( 'bp_after_profile_field_content' ) ?>

<?php do_action( 'bp_profile_field_buttons' ) ?>

<?php do_action( 'bp_after_profile_loop_content' ) ?>
