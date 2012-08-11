<?php

/**
 * BuddyPress - Single Member Notification Settings
 *
 * @package BuddyPress
 * @subpackage bp-default
 */
 
get_header(); // Loads the header.php template. ?>

<?php do_atomic( 'before_content' ); // fanwood_before_content ?>

<div id="content">

	<?php do_atomic( 'open_content' ); // fanwood_open_content ?>

	<div class="hfeed">

		<?php get_sidebar( 'before-content' ); // Loads the sidebar-before-content.php template. ?>

		<?php do_action( 'bp_before_member_settings_template' ); ?>

			<div id="item-header">

				<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs bp-tabs no-ajax" id="object-nav" role="navigation">
					<ul>

						<?php bp_get_displayed_user_nav(); ?>

						<?php do_action( 'bp_member_options_nav' ); ?>

					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body" role="main">

				<?php do_action( 'bp_before_member_body' ); ?>

				<div class="item-list-tabs bp-sub-tabs no-ajax" id="subnav">
					<ul>

						<?php bp_get_options_nav(); ?>

						<?php do_action( 'bp_member_plugin_options_nav' ); ?>

					</ul>
				</div><!-- .item-list-tabs -->

				
				<div class="entry-content">
				
					<h2><?php _e( 'Email Notification', 'buddypress' ); ?></h2>

					<?php do_action( 'bp_template_content' ) ?>

					<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/notifications'; ?>" method="post" class="standard-form" id="settings-form">
						<p><?php _e( 'Send a notification by email when:', 'buddypress' ); ?></p>

						<?php do_action( 'bp_notification_settings' ); ?>

						<?php do_action( 'bp_members_notification_settings_before_submit' ); ?>

						<p class="submit">
							<input type="submit" name="submit" value="<?php _e( 'Save Changes', 'buddypress' ); ?>" id="submit" class="auto" />
						</p>

						<?php do_action( 'bp_members_notification_settings_after_submit' ); ?>

						<?php wp_nonce_field('bp_settings_notifications'); ?>

					</form>

					<?php do_action( 'bp_after_member_body' ); ?>
				
				</div><!-- .entry-content -->

			</div><!-- #item-body -->

			<?php do_action( 'bp_after_member_settings_template' ); ?>

		<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>

	</div><!--. hfeed -->
	
	<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

</div><!-- #content -->

<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the header.php template. ?>
