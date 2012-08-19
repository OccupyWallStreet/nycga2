<?php get_header(); ?>
<div id="left">
		<div id="primary">
			<div id="content" role="main">

				<?php the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

				<?php comments_template( '', true ); ?>

			</div><!-- #content -->
		</div><!-- #primary -->

</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>