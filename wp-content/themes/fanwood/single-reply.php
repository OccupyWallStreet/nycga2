<?php
/**
 * Single Reply
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

			<?php while ( have_posts() ) : the_post(); ?>

			<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">
			
				<?php do_atomic( 'open_entry' ); // fanwood_open_entry ?>
			
				<h1 class="entry-title"><?php bbp_reply_title(); ?></h1>
				
				<div class="byline">
					<span class="bbp-reply-date"><?php printf( __( '%1$s at %2$s', 'bbpress' ), get_the_date(), esc_attr( get_the_time() ) ); ?></span>
					<?php bbp_reply_author_link( array( 'type' => 'name' ) ); ?>
					<span class="bbp-reply-permalink"><a href="<?php bbp_reply_url(); ?>" title="<?php bbp_reply_title(); ?>">#</a></span>
				</div><!-- .byline -->

				<div class="entry-content">

					<?php bbp_reply_content(); ?>
					
					<?php if( is_user_logged_in() ) { ?>
						<?php bbp_reply_admin_links( array( 'sep' => ' &#160; ' ) ); ?>
					<?php } ?>
					
				</div>
				
				<?php do_atomic( 'close_entry' ); // fanwood_close_entry ?>
				
			</div><!-- .hentry -->

			<?php endwhile; ?>
			
			<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>
