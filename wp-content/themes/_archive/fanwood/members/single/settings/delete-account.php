<?php

/**
 * BuddyPress - single Member Delete Account
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

			<h2 class="entry-title"><?php _e( 'Delete Account', 'buddypress' ); ?></h2>

			<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/delete-account'; ?>" name="account-delete-form" id="account-delete-form" class="standard-form" method="post">

				<div id="message" class="info">
					<p><?php _e( 'WARNING: Deleting your account will completely remove ALL content associated with it. There is no way back, please be careful with this option.', 'buddypress' ); ?></p>
				</div>

				<div class="entry-content">
					<p>
						<input type="checkbox" name="delete-account-understand" id="delete-account-understand" value="1" onclick="if(this.checked) { document.getElementById('delete-account-button').disabled = ''; } else { document.getElementById('delete-account-button').disabled = 'disabled'; }" /> <?php _e( 'I understand the consequences of deleting my account.', 'buddypress' ); ?>
					</p>

					<?php do_action( 'bp_members_delete_account_before_submit' ); ?>

					<p class="submit">
						<input type="submit" disabled="disabled" value="<?php _e( 'Delete My Account', 'buddypress' ) ?>" id="delete-account-button" name="delete-account-button" />
					</p>

					<?php do_action( 'bp_members_delete_account_after_submit' ); ?>

					<?php wp_nonce_field( 'delete-account' ); ?>
				</div><!-- .entry-content -->
			</form>

			<?php do_action( 'bp_after_member_body' ); ?>

		</div><!-- #item-body -->

		<?php do_action( 'bp_after_member_settings_template' ); ?>

		<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>

	</div><!--. hfeed -->
	
	<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

</div><!-- #content -->

<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the header.php template. ?>
