<?php
/**
 * @package WordPress
 * @subpackage Yoko
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
    <div class="entry-post-format">
		<header class="entry-header">
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'yoko' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
			<p><?php echo get_the_date(); ?> <?php _e( 'by', 'yoko' ); ?> <?php the_author() ?> | <?php comments_popup_link( __( '0 comments', 'yoko' ), __( '1 Comment', 'yoko' ), __( '% Comments', 'yoko' ) ); ?></p>
		</header><!-- end entry-header -->

			<?php the_content( __( 'Continue Reading &rarr;', 'yoko' ) ); ?>		
			<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'yoko' ), 'after' => '</div>' ) ); ?>
		
		<footer class="entry-meta">
			<p><?php if ( count( get_the_category() ) ) : ?>
			<?php printf( __( 'Categories: %2$s', 'yoko' ), 'entry-utility-prep entry-utility-prep-cat-links', get_the_category_list( ', ' ) ); ?> | 
			<?php endif; ?>
			<?php $tags_list = get_the_tag_list( '', ', ' ); 
			if ( $tags_list ): ?>
			<?php printf( __( 'Tags: %2$s', 'yoko' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list ); ?> | 
			<?php endif; ?>
			<a href="<?php echo get_permalink(); ?>"><?php _e( 'Permalink ', 'yoko' ); ?></a>
			<?php edit_post_link( __( 'Edit &rarr;', 'yoko' ), '| <span class="edit-link">', '</span>' ); ?></p>
		</footer><!-- end entry-meta -->
	</div><!-- end entry-post-format -->
			
</article><!-- end post-<?php the_ID(); ?> -->