<?php
/**
 * The template for displaying posts in the Link post format
 *
 * @package views_base
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header><?php _e( 'Link', 'views_base' ); ?></header>
		<div class="entry-content">
			<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'views_base' ) ); ?>
		</div><!-- .entry-content -->

		<footer class="entry-meta">
			<?php edit_post_link( __( 'Edit', 'views_base' ), '<div class="edit-link">', '</div>' ); ?>
			<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'views_base' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_date(); ?></a>
		</footer><!-- #entry-meta -->
	</article><!-- #post -->
