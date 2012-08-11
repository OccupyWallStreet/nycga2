<?php
/**
 * Loop Author Template
 *
 * Displays the author's avatar and biography.
 * This is typically shown on singular view pages only.
 *
 * @package Fanwood
 * @subpackage Template
 */
?>

<div class="entry-author-meta">

	<a href="<?php echo get_author_posts_url(get_the_author_meta( 'ID' )); ?>" title="<?php echo esc_attr( get_the_author_meta( 'display_name' ) ); ?>" class="avatar-frame"><?php echo get_avatar(get_the_author_meta('ID'), '60', '', ''); ?></a>

	<p class="author-name"><?php echo do_shortcode('[entry-author]'); ?></p>
	<p class="author-description"><?php the_author_meta('description'); ?></p>

</div><!-- .entry-author -->