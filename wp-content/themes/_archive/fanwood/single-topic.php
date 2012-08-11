<?php
/**
 * Single Topic
 *
 * @package bbPress
 * @subpackage Theme
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // fanwood_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // fanwood_open_content ?>

		<div class="hfeed">
			
			<?php bbp_breadcrumb( array( 'before' => '<div class="breadcrumb">', 'after' => '</div>', 'sep' => '<span class="sep">&raquo</span>' ) ); ?>
			
			<?php get_sidebar( 'before-content' ); // Loads the sidebar-before-content.php template. ?>
			
			<?php do_action( 'bbp_template_notices' ); ?>

			<?php if ( bbp_user_can_view_forum( array( 'forum_id' => bbp_get_topic_forum_id() ) ) ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">
						<h1 class="entry-title"><?php bbp_topic_title(); ?></h1>
					</div><!-- .hentry -->
					
					<?php bbp_get_template_part( 'bbpress/content', 'single-topic' ); ?>

				<?php endwhile; ?>

			<?php elseif ( bbp_is_forum_private( bbp_get_topic_forum_id(), false ) ) : ?>

				<?php bbp_get_template_part( 'bbpress/feedback', 'no-access' ); ?>

			<?php endif; ?>
			
			<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>