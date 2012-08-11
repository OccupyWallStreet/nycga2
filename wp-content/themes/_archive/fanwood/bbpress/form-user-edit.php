<?php

/**
 * bbPress User Profile Edit Part
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<form id="bbp-your-profile" action="<?php bbp_user_profile_edit_url( bbp_get_displayed_user_id() ); ?>" method="post">

	<?php do_action( 'bbp_user_edit_before' ); ?>

	<div class="bbp-form">
		<h3><?php _e( 'Name', 'bbpress' ) ?></h3>

		<?php do_action( 'bbp_user_edit_before_name' ); ?>

		<p>
			<label for="first_name"><?php _e( 'First Name', 'bbpress' ) ?></label><br />
			<input type="text" name="first_name" id="first_name" value="<?php echo esc_attr( bbp_get_displayed_user_field( 'first_name' ) ); ?>" class="regular-text" tabindex="<?php bbp_tab_index(); ?>" />
		</p>

		<p>
			<label for="last_name"><?php _e( 'Last Name', 'bbpress' ) ?></label><br />
			<input type="text" name="last_name" id="last_name" value="<?php echo esc_attr( bbp_get_displayed_user_field( 'last_name' ) ); ?>" class="regular-text" tabindex="<?php bbp_tab_index(); ?>" />
		</p>

		<p>
			<label for="nickname"><?php _e( 'Nickname', 'bbpress' ); ?></label><br />
			<input type="text" name="nickname" id="nickname" value="<?php echo esc_attr( bbp_get_displayed_user_field( 'nickname' ) ); ?>" class="regular-text" tabindex="<?php bbp_tab_index(); ?>" />
		</p>

		<p>
			<label for="display_name"><?php _e( 'Display name publicly as', 'bbpress' ) ?></label>

			<?php bbp_edit_user_display_name(); ?>

		</p>

		<?php do_action( 'bbp_user_edit_after_name' ); ?>

		<h3><?php _e( 'Contact Info', 'bbpress' ) ?></h3>

		<?php do_action( 'bbp_user_edit_before_contact' ); ?>

		<p>
			<label for="url"><?php _e( 'Website', 'bbpress' ) ?></label><br />
			<input type="text" name="url" id="url" value="<?php echo esc_attr( bbp_get_displayed_user_field( 'user_url' ) ); ?>" class="regular-text code" tabindex="<?php bbp_tab_index(); ?>" />
		</p>

		<?php foreach ( bbp_edit_user_contact_methods() as $name => $desc ) : ?>

			<p>
				<label for="<?php echo $name; ?>"><?php echo apply_filters( 'user_'.$name.'_label', $desc ); ?></label><br />
				<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo esc_attr( bbp_get_displayed_user_field( 'name' ) ); ?>" class="regular-text" tabindex="<?php bbp_tab_index(); ?>" />
			</p>

		<?php endforeach; ?>

		<?php do_action( 'bbp_user_edit_after_contact' ); ?>

		<h3><?php bbp_is_user_home() ? _e( 'About Yourself', 'bbpress' ) : _e( 'About the user', 'bbpress' ); ?></h3>

		<?php do_action( 'bbp_user_edit_before_about' ); ?>

		<p>
			<label for="description"><?php _e( 'Biographical Info', 'bbpress' ); ?></label><br />
			<textarea name="description" id="description" rows="5" cols="30" tabindex="<?php bbp_tab_index(); ?>"><?php echo esc_attr( bbp_get_displayed_user_field( 'description' ) ); ?></textarea>
			<span class="description"><?php _e( 'Share a little biographical information to fill out your profile. This may be shown publicly.', 'bbpress' ); ?></span>
		</p>

		<?php do_action( 'bbp_user_edit_after_about' ); ?>
	
		<h3><?php _e( 'Account', 'bbpress' ) ?></h3>

		<?php do_action( 'bbp_user_edit_before_account' ); ?>

		<p>
			<label for="user_login"><?php _e( 'Username', 'bbpress' ); ?></label><br />
			<input type="text" name="user_login" id="user_login" value="<?php echo esc_attr( bbp_get_displayed_user_field( 'user_login' ) ); ?>" disabled="disabled" class="regular-text" tabindex="<?php bbp_tab_index(); ?>" />
			<span class="description"><?php _e( 'Usernames cannot be changed.', 'bbpress' ); ?></span>
		</p>

		<p>
			<label for="email"><?php _e( 'Email', 'bbpress' ); ?></label><br />

			<input type="text" name="email" id="email" value="<?php echo esc_attr( bbp_get_displayed_user_field( 'user_email' ) ); ?>" class="regular-text" tabindex="<?php bbp_tab_index(); ?>" />

			<?php

			// Handle address change requests
			$new_email = get_option( bbp_get_displayed_user_id() . '_new_email' );
			if ( $new_email && $new_email != bbp_get_displayed_user_field( 'user_email' ) ) : ?>

			<span class="description updated">
				<?php printf( __( 'There is a pending email address change to <code>%1$s</code>. <a href="%2$s">Cancel</a>', 'bbpress' ), $new_email['newemail'], esc_url( self_admin_url( 'user.php?dismiss=' . bbp_get_current_user_id()  . '_new_email' ) ) ); ?>
			</span>

			<?php endif; ?>

		</p>

		<p>
			<label for="pass1"><?php _e( 'New Password', 'bbpress' ); ?></label><br />
			<input type="password" name="pass1" id="pass1" size="16" value="" autocomplete="off" tabindex="<?php bbp_tab_index(); ?>" />
			<span class="description"><?php _e( 'If you would like to change the password, type a new one. Otherwise leave this blank.', 'bbpress' ); ?></span>
		</p>
				
		<p>
			<label for="pass1"><?php _e( 'Enter Password Again', 'bbpress' ); ?></label><br />
			<input type="password" name="pass2" id="pass2" size="16" value="" autocomplete="off" tabindex="<?php bbp_tab_index(); ?>" />
		</p>

		<div id="pass-strength-result"></div>
				
		<p class="description indicator-hint"><?php _e( 'Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).', 'bbpress' ); ?></p>

		<?php if ( !bbp_is_user_home() ) : ?>

		<p>
			<label for="role"><?php _e( 'Role:', 'bbpress' ) ?></label><br />
			<?php bbp_edit_user_role(); ?>
		</p>

		<?php endif; ?>

		<?php if ( is_multisite() && is_super_admin() && current_user_can( 'manage_network_options' ) ) : ?>

		<p>
			<label for="role"><?php _e( 'Super Admin', 'bbpress' ); ?></label><br />
			<input type="checkbox" id="super_admin" name="super_admin"<?php checked( is_super_admin( bbp_get_displayed_user_id() ) ); ?> tabindex="<?php bbp_tab_index(); ?>" />
			<span class="description"><?php _e( 'Grant this user super admin privileges for the Network.', 'bbpress' ); ?></span>
		</p>

		<?php endif; ?>

		<?php do_action( 'bbp_user_edit_after_account' ); ?>

		<?php do_action( 'bbp_user_edit_after' ); ?>

		<div class="bbp-submit-wrapper">
			<?php bbp_edit_user_form_fields(); ?>
			<button type="submit" tabindex="<?php bbp_tab_index(); ?>" id="bbp_user_edit_submit" name="bbp_user_edit_submit" class="button submit user-submit"><?php bbp_is_user_home() ? _e( 'Update Profile', 'bbpress' ) : _e( 'Update User', 'bbpress' ); ?></button>
		</div>
		
	</div>

</form>