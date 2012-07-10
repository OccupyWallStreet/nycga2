<?php
/**
 * bbPRess Edit Topic Template
 *
 * @package bbPress
 * @subpackage Template
 */

get_header(); // Loads the header.php template. ?>

	<?php do_atomic( 'before_content' ); // fanwood_before_content ?>

	<div id="content">

		<?php do_atomic( 'open_content' ); // fanwood_open_content ?>

		<div class="hfeed">
			
			<?php bbp_breadcrumb( array( 'before' => '<div class="breadcrumb">', 'after' => '</div>', 'sep' => '<span class="sep">&raquo</span>' ) ); ?>
			
			<?php get_sidebar( 'before-content' ); // Loads the sidebar-before-content.php template. ?>

			<?php while ( have_posts() ) : the_post(); ?>
			
				<h1 class="entry-title"><?php the_title(); ?></h1>	
			
				<?php bbp_get_template_part( 'bbpress/form', 'topic' ); ?>

			<?php endwhile; ?>
			
			<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>

		</div><!-- .hfeed -->

		<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

	</div><!-- #content -->

	<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the footer.php template. ?>
