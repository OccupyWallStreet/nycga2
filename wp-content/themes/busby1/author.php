<?php get_header(); ?>
<div id="left">
	
				<?php the_post(); ?>

				<header class="page-header">
					<h1 class="page-title author"><?php printf( __( 'Author Archives: <span class="vcard">%s</span>', 'toolbox' ), "<a class='url fn n' href='" . get_author_posts_url( get_the_author_meta( 'ID' ) ) . "' title='" . esc_attr( get_the_author() ) . "' rel='me'>" . get_the_author() . "</a>" ); ?></h1>
				</header>

				<?php rewind_posts(); ?>

				
				
				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
					
					<?php get_template_part( 'content', get_post_format() ); ?>

				<?php endwhile; ?>
			<?php kriesi_pagination(); ?>	
</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>