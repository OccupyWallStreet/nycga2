<?php
/**
 * Home Template
 *
 * This is the home template.  Technically, it is the "posts page" template.  It is used when a visitor is on the 
 * page assigned to show a site's latest blog posts.
 *
 * @package Fanwood
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

<?php do_atomic( 'before_content' ); // fanwood_before_content ?>

<div id="content">

	<?php do_atomic( 'open_content' ); // fanwood_open_content ?>
	
	<div class="hfeed">
	
		<?php if ( current_theme_supports( 'breadcrumb-trail' ) ) breadcrumb_trail( array( 'separator' => '&raquo;' ) ); ?>
	
		<?php get_template_part( 'loop-meta' ); // Loads the loop-meta.php template. ?>
	
		<?php get_sidebar( 'before-content' ); // Loads the sidebar-before-content.php template. ?>
	
		<ul class="loop-entries">
		
		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>
			
			<?php do_atomic( 'before_entry' ); // fanwood_before_entry ?>

			<li id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">
			
				<?php do_atomic( 'open_entry' ); // fanwood_open_entry ?>
				
				<?php get_template_part( 'content', get_post_format() ); ?>
					
				<?php do_atomic( 'close_entry' ); // fanwood_close_entry ?>

			</li><!-- .hentry -->
			
			<?php do_atomic( 'after_entry' ); // fanwood_after_entry ?>
			
				<?php endwhile; ?>

			<?php else : ?>

				<?php get_template_part( 'loop-error' ); // Loads the loop-error.php template. ?>

		<?php endif; ?>

		</ul><!-- .loop-content -->
		
		<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>
		
	</div><!-- .hfeed -->
	
	<?php do_atomic( 'close_content' ); // fanwood_close_content ?>
	
	<?php get_template_part( 'loop-nav' ); // Loads the loop-nav.php template. ?>

</div><!-- #content -->

<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>