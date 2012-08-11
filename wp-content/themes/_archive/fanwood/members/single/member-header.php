<?php

/**
 * BuddyPress - Single Member Header Content Part
 *
 * @package BuddyPress
 * @subpackage Theme
 */

?>

<?php do_action( 'template_notices' ); ?>

<?php do_action( 'bp_before_member_header' ); ?>

<div class="loop-meta">

	<div id="item-header-avatar">
		<a href="<?php bp_user_link(); ?>">
			<?php bp_displayed_user_avatar( 'type=thumb' ); ?>
		</a>
	</div><!-- #item-header-avatar -->

	<div id="item-header-content">

		<h1 class="loop-title">
			<a href="<?php bp_displayed_user_link(); ?>"><?php bp_displayed_user_fullname(); ?></a>
		</h1>

		<span class="user-nicename">@<?php bp_displayed_user_username(); ?></span>
		<span class="activity"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>

		<?php do_action( 'bp_before_member_header_meta' ); ?>

		<div id="item-meta">

			<?php if ( bp_is_active( 'activity' ) ) : ?>

				<p id="latest-update">

					<?php bp_activity_latest_update( bp_displayed_user_id() ); ?>

				</p>

			<?php endif; ?>

			<div id="item-buttons">

				<?php do_action( 'bp_member_header_actions' ); ?>

			</div><!-- #item-buttons -->

			<?php
			/***
			 * If you'd like to show specific profile fields here use:
			 * bp_profile_field_data( 'field=About Me' ); -- Pass the name of the field
			 */
			 do_action( 'bp_profile_header_meta' );

			 ?>

		</div><!-- #item-meta -->

	</div><!-- #item-header-content -->
	
</div><!-- .loop-meta -->

<?php do_action( 'bp_after_member_header' ); ?>