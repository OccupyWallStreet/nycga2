	<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>
	<?php echo apply_atomic_shortcode( 'byline', '<div class="byline">' . __('[entry-published] [entry-comments-link zero="Respond" one="%1$s" more="%1$s"]', 'fanwood' ) . '</div>'); ?>

	<div class="entry-summary"><?php the_content(); ?></div><!-- .entry-summary -->