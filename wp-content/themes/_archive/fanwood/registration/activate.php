<?php
/**
 * BuddyPress - Activate
 *
 * Account activation template for BuddyPress.
 *
 * @package BuddyPress
 * @subpackage Theme
 */
 
get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // fanwood_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // fanwood_open_content ?>

		<div class="hfeed">
		
			<?php if ( current_theme_supports( 'breadcrumb-trail' ) ) breadcrumb_trail( array( 'separator' => '&raquo;' ) ); ?>
		
			<?php get_sidebar( 'before-content' ); // Loads the sidebar-before-content.php template. ?>

			<?php do_action( 'bp_before_activation_page' ) ?>

			<div class="page" id="activate-page">

				<div class="hentry page">
			
				<?php if ( bp_account_was_activated() ) : ?>

					<h1 class="entry-title"><?php _e( 'Account Activated', 'buddypress' ) ?></h1>

					<div class="entry-content">
					
						<?php do_action( 'bp_before_activate_content' ) ?>

						<?php if ( isset( $_GET['e'] ) ) : ?>
							<p><?php _e( 'Your account was activated successfully! Your account details have been sent to you in a separate email.', 'buddypress' ) ?></p>
						<?php else : ?>
							<p><?php _e( 'Your account was activated successfully! You can now log in with the username and password you provided when you signed up.', 'buddypress' ) ?></p>
						<?php endif; ?>
					
					</div><!-- .entry-content -->

				<?php else : ?>

					<h1 class="entry-title"><?php _e( 'Activate Your Account', 'buddypress' ) ?></h1>

					<div class="entry-content">
					
						<?php do_action( 'bp_before_activate_content' ) ?>

						<p><?php _e( 'Please provide a valid activation key.', 'buddypress' ) ?></p>

						<form action="" method="get" class="standard-form" id="activation-form">

							<p>
								<label for="key"><?php _e( 'Activation Key:', 'buddypress' ) ?></label><br />
								<input type="text" name="key" id="key" value="" />
							</p>

							<p class="submit">
								<input type="submit" name="submit" value="<?php _e( 'Activate', 'buddypress' ) ?>" />
							</p>

						</form>
						
						<?php echo apply_atomic_shortcode( 'entry_edit_link', '[entry-edit-link before="<p>" after="</p>"]' ); ?>
					
					</div><!-- .entry-content -->

				<?php endif; ?>

				<?php do_action( 'bp_after_activate_content' ) ?>
				
				</div><!-- .hentry -->

			</div><!-- .page -->

			<?php do_action( 'bp_after_activation_page' ) ?>
			
			<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>
		
		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>