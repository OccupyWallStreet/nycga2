<?php get_header(); ?>
<div id="left">

		
			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'single' ); ?>

				<?php comments_template( '', true ); ?>
			<?php endwhile; // end of the loop. ?>

</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>