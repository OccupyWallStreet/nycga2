<?php
/**
 * The template for displaying posts in the Aside post format
 *
 * @package views_base
 * 
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'views_base' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
			<div class="entry-meta">
				<?php views_base_posted_by() ?>
			</div><!-- .entry-meta -->
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'views_base' ) ); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'views_base' ), 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->

		<footer class="entry-meta">
			<?php __( '<span class="sep">Posted on </span>', 'views_base' ); ?> <?php views_base_posted_on(); ?>

			<?php if ( comments_open() ) : ?>
			<span class="comments-link">
				<?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a reply', 'views_base' ) . '</span>', __( '1 Reply', 'views_base' ), __( '% Replies', 'views_base' ) ); ?>
			</span>
			<?php endif; ?>

			<?php edit_post_link( __( 'Edit', 'views_base' ), '<span class="edit-link"><span class="sep"> | </span>', '</span>' ); ?>
		</footer><!-- #entry-meta -->
	</article><!-- #post -->
