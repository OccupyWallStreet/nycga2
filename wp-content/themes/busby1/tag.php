<?php get_header(); ?>
<div id="left">
		<section id="primary">
			<div id="content" role="main">

				<?php the_post(); ?>

				<header class="page-header">
					<h1 class="page-title"><?php
						printf( __( 'Tag Archives: %s', 'toolbox' ), '<span>' . single_tag_title( '', false ) . '</span>' );
					?></h1>
				</header>

				<?php rewind_posts(); ?>

				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
					
					<?php get_template_part( 'content', get_post_format() ); ?>

				<?php endwhile; ?>

			</div><!-- #content -->
		</section><!-- #primary -->

<?php kriesi_pagination(); ?>

</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>