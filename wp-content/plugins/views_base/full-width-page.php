<?php
/**
 * Template Name: Full-width page, no sidebar
 *
 * @package views_base
 */

get_header(); ?>

		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
				<?php comments_template( '', true ); ?>
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->

<?php get_footer(); ?>