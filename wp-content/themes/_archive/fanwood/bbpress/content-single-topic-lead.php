<?php

/**
 * Single Topic Part
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<div class="byline">

	<span class="bbp-topic-date"><?php printf( __( '%1$s at %2$s', 'bbpress' ), get_the_date(), esc_attr( get_the_time() ) ); ?></span>
	
	<a href="<?php echo bbp_get_topic_author_url(); ?>" class="bbp-author-name"><?php bbp_topic_author(); ?></a>

	<a href="#bbp-topic-<?php bbp_topic_id(); ?>" title="<?php bbp_topic_title(); ?>" class="bbp-topic-permalink">#<?php bbp_topic_id(); ?></a>

	<?php if ( is_super_admin() ) : ?>
		<?php bbp_author_ip( bbp_get_topic_id() ); ?>
	<?php endif; ?>

</div>

<?php if( is_user_logged_in() ) { ?>
	<div class="bbp-subscribe-links">
		<?php bbp_user_subscribe_link( array( 'before' => '' ) ); ?>
		<?php bbp_user_favorites_link(); ?>
	</div>
<?php } ?>

<div class="entry-content">
	<?php bbp_topic_content(); ?>
	<?php bbp_topic_admin_links( array( 'sep' => ' &#160; ' ) ); ?>
</div><!-- .entry-content -->

<div class="entry-author-meta">

	<a href="<?php echo bbp_get_topic_author_url(); ?>" title="<?php the_author_meta('display_name'); ?>" class="avatar-frame"><?php echo get_avatar(get_the_author_meta('ID'), '50', '', ''); ?></a>

	<p class="author-name"><?php _e( 'Started by:', 'fanwood'); ?> <a href="<?php echo bbp_get_topic_author_url(); ?>"><?php bbp_topic_author(); ?></a></p>
	<p class="author-description"><?php the_author_meta('description'); ?></p>

</div><!-- .entry-author -->