<?php

/**
 * BuddyPress - Single Member General Settings
 *
 * @package BuddyPress
 * @subpackage Theme
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
				
				<h2><?php _e( 'General Settings', 'buddypress' ); ?></h2>

				<?php do_action( 'bp_template_content' ) ?>

				<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/general'; ?>" method="post" class="standard-form" id="settings-form">

					<p>
						<label for="pwd"><?php _e( 'Current Password <span>(required to update email or change current password)</span>', 'buddypress' ); ?></label><br />
							
						<input type="password" name="pwd" id="pwd" size="16" value="" class="settings-input small" /><br />
							
						<label><a href="<?php echo site_url( add_query_arg( array( 'action' => 'lostpassword' ), 'wp-login.php' ), 'login' ); ?>" title="<?php _e( 'Password Lost and Found', 'buddypress' ); ?>"><?php _e( 'Lost your password?', 'buddypress' ); ?></a></label>
					</p>

					<p>
						<label for="email"><?php _e( 'Account Email', 'buddypress' ); ?></label><br />
						<input type="text" name="email" id="email" value="<?php echo bp_get_displayed_user_email(); ?>" class="settings-input" />
					</p>

					<p>
						<label for="pass1"><?php _e( 'Change Password <span>(leave blank for no change)</span>', 'buddypress' ); ?></label><br />
							
						<input type="password" name="pass1" id="pass1" size="16" value="" class="settings-input small" /><br />
							
						<label for="pass1"><?php _e( 'New Password', 'buddypress' ); ?></label><br />
							
						<input type="password" name="pass2" id="pass2" size="16" value="" class="settings-input small" />
							
						<label for="pass2"><?php _e( 'Repeat New Password', 'buddypress' ); ?></label>
					</p>

					<?php do_action( 'bp_core_general_settings_before_submit' ); ?>

					<div class="submit">
						<input type="submit" name="submit" value="<?php _e( 'Save Changes', 'buddypress' ); ?>" id="submit" class="auto" />
					</div>

					<?php do_action( 'bp_core_general_settings_after_submit' ); ?>

					<?php wp_nonce_field( 'bp_settings_general' ); ?>

				</form>
					
			</div><!-- .entry-content -->

			<?php do_action( 'bp_after_member_body' ); ?>

		</div><!-- #item-body -->

		<?php do_action( 'bp_after_member_settings_template' ); ?>
			
		<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>

	</div><!--. hfeed -->
	
	<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

</div><!-- #content -->

<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the header.php template. ?>


