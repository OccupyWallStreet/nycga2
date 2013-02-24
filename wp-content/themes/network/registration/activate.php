<?php get_header( 'network' ); ?>

	<div id="content">
		<div class="padder">

		<?php do_action( 'bp_before_activation_page' ) ?>

		<div class="page" id="activate-page">

			<?php if ( bp_account_was_activated() ) : ?>

				<h2 class="widgettitle"><?php _e( 'Account Activated', 'network' ) ?></h2>

				<?php do_action( 'bp_before_activate_content' ) ?>

				<?php if ( isset( $_GET['e'] ) ) : ?>
					<p><?php _e( 'Your account was activated successfully! Your account details have been sent to you in a separate email.', 'network' ) ?></p>
				<?php else : ?>
					<p><?php _e( 'Your account was activated successfully! You can now log in with the username and password you provided when you signed up.', 'network' ) ?></p>
				<?php endif; ?>

			<?php else : ?>

				<h3><?php _e( 'Activate your Account', 'network' ) ?></h3>

				<?php do_action( 'bp_before_activate_content' ) ?>

				<p><?php _e( 'Please provide a valid activation key.', 'network' ) ?></p>

				<form action="" method="get" class="standard-form" id="activation-form">

					<label for="key"><?php _e( 'Activation Key:', 'network' ) ?></label>
					<input type="text" name="key" id="key" value="" />

					<p class="submit">
						<input type="submit" name="submit" value="<?php _e( 'Activate', 'network' ) ?>" />
					</p>

				</form>

			<?php endif; ?>

			<?php do_action( 'bp_after_activate_content' ) ?>

		</div><!-- .page -->

		<?php do_action( 'bp_after_activation_page' ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php get_sidebar( 'network' ) ?>

<?php get_footer( 'network' ); ?>
