<?php
function bp_em_screen_settings_menu() {
	global $bp, $current_user, $bp_settings_updated, $pass_error;

	if ( isset( $_POST['submit'] ) ) {
		/* Check the nonce */
		check_admin_referer('bp-em-admin');

		$bp_settings_updated = true;

		/**
		 * This is when the user has hit the save button on their settings.
		 * The best place to store these settings is in wp_usermeta.
		 */
		update_usermeta( $bp->loggedin_user->id, 'bp-em-option-one', attribute_escape( $_POST['bp-em-option-one'] ) );
	}

	add_action( 'bp_template_content_header', 'bp_em_screen_settings_menu_header' );
	add_action( 'bp_template_title', 'bp_em_screen_settings_menu_title' );
	add_action( 'bp_template_content', 'bp_em_screen_settings_menu_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

	function bp_em_screen_settings_menu_header() {
		_e( 'Events Settings Header', 'dbem' );
	}

	function bp_em_screen_settings_menu_title() {
		_e( 'Events Settings', 'dbem' );
	}

	function bp_em_screen_settings_menu_content() {
		global $bp, $bp_settings_updated; ?>

		<?php if ( $bp_settings_updated ) { ?>
			<div id="message" class="updated fade">
				<p><?php _e( 'Changes Saved.', 'dbem' ) ?></p>
			</div>
		<?php } ?>

		<form action="<?php echo $bp->loggedin_user->domain . 'settings/events-settings/'; ?>" name="bp-em-admin-form" id="account-delete-form" class="bp-em-admin-form" method="post">

			<input type="checkbox" name="bp-em-option-one" id="bp-em-option-one" value="1"<?php if ( '1' == get_usermeta( $bp->loggedin_user->id, 'bp-em-option-one' ) ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Do you love clicking checkboxes?', 'dbem' ); ?>
			<p class="submit">
				<input type="submit" value="<?php _e( 'Save Settings', 'dbem' ) ?> &raquo;" id="submit" name="submit" />
			</p>

			<?php
			/* This is very important, don't leave it out. */
			wp_nonce_field( 'bp-em-admin' );
			?>

		</form>
	<?php
	}
