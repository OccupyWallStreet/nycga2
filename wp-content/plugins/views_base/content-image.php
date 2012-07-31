<?php
/**
 * The template for displaying posts in the Image post format
 *
 * @package views_base
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="entry-content">
			<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'views_base' ) ); ?>
		</div><!-- .entry-content -->

		<footer class="entry-meta">
			<h1><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'views_base' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
			<h2><?php the_date(); ?></h2>
			<!--<?php edit_post_link( __( 'Edit', 'views_base' ), '<div class="edit-link">', '</div>' ); ?>-->
		</footer><!-- #entry-meta -->
	</article><!-- #post -->
