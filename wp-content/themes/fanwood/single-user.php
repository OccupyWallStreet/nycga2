<?php

/**
 * bbPress User Profile
 *
 * @package bbPress
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // fanwood_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // fanwood_open_content ?>

		<div class="hfeed">
		
			<?php get_sidebar( 'before-content' ); // Loads the sidebar-before-content.php template. ?>

			<div id="bbp-user-<?php bbp_current_user_id(); ?>" class="bbp-single-user">

				<?php bbp_get_template_part( 'bbpress/content', 'single-user' ); ?>

			</div><!-- #bbp-user-<?php bbp_current_user_id(); ?> -->
			
			<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>
