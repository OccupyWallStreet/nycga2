<?php

/**
 * BuddyPress - Blog Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter()
 *
 * @package BuddyPress
 * @subpackage Theme
 */

?>

<?php do_action( 'bp_before_blogs_loop' ); ?>

<?php if ( bp_has_blogs( bp_ajax_querystring( 'blogs' ) ) ) : ?>

	<div id="pag-top" class="pagination bp-pagination">

		<div class="pag-count" id="blog-dir-count-top">
			<?php bp_blogs_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="blog-dir-pag-top">
			<?php bp_blogs_pagination_links(); ?>
		</div>

	</div><!-- .bp-pagination -->

	<?php do_action( 'bp_before_directory_blogs_list' ); ?>

	<ul id="blogs-list" class="item-list dir-list" role="main">

	<?php while ( bp_blogs() ) : bp_the_blog(); ?>

		<li>
			<div class="item-header">
				<div class="item-avatar">
					<a href="<?php bp_blog_permalink(); ?>"><?php bp_blog_avatar( 'type=thumb' ); ?></a>
				</div>
				<div class="item-title"><a href="<?php bp_blog_permalink(); ?>"><?php bp_blog_name(); ?></a></div>
				<span class="activity"><?php bp_blog_last_active(); ?></span>
				<?php do_action( 'bp_directory_blogs_item' ); ?>
			</div><!-- .item-header -->

			<div class="item-action">
				<?php do_action( 'bp_directory_blogs_actions' ); ?>
				<span class="action-meta">
					<?php bp_blog_latest_post(); ?>
				</span><!-- .meta -->
			</div><!--. action -->
		</li>

	<?php endwhile; ?>

	</ul><!-- .item-list -->

	<?php do_action( 'bp_after_directory_blogs_list' ); ?>

	<?php bp_blog_hidden_fields(); ?>

	<div id="pag-bottom" class="pagination bp-pagination">

		<div class="pag-count" id="blog-dir-count-bottom">
			<?php bp_blogs_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="blog-dir-pag-bottom">
			<?php bp_blogs_pagination_links(); ?>
		</div>

	</div><!-- .bp-pagination -->

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, there were no sites found.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_blogs_loop' ); ?>
