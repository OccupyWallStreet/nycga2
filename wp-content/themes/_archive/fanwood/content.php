	<?php if ( current_theme_supports( 'get-the-image' ) ) : ?>
		<?php $image = get_the_image( array( 'echo' => false ) );
			if ( $image ) : ?>
				<a href="<?php echo get_permalink(); ?>" title="<?php the_title_attribute( 'echo=1' ); ?>" rel="bookmark" class="featured-image-link"><?php get_the_image( array( 'size' => 'fanwood-thumbnail', 'link_to_post' => false, 'width' => '240' ) ); ?></a>
		<?php endif; ?>
	<?php endif; ?>

	<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>
	<?php echo apply_atomic_shortcode( 'byline', '<div class="byline">' . __('[entry-published] [entry-comments-link zero="Respond" one="%1$s" more="%1$s"]', 'fanwood' ) . '</div>'); ?>

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->