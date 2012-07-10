<?php
/**
 * BuddyPress - Activity Permalink
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

		<div class="activity no-ajax" role="main">
			<?php if ( bp_has_activities( 'display_comments=threaded&show_hidden=true&include=' . bp_current_action() ) ) : ?>

				<ul id="activity-stream" class="activity-list item-list">
				<?php while ( bp_activities() ) : bp_the_activity(); ?>

					<?php locate_template( array( 'activity/entry.php' ), true ) ?>

				<?php endwhile; ?>
				</ul>

			<?php endif; ?>
		</div><!-- .activity -->
		
		<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>
		
	</div><!-- .hfeed -->
	
	<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

</div><!-- #content -->

<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the header.php template. ?>