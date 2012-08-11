<?php

/**
 * BuddyPress - Groups Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter()
 *
 * @package BuddyPress
 * @subpackage Theme
 */

?>

<?php do_action( 'bp_before_groups_loop' ); ?>

<?php if ( bp_has_groups( bp_ajax_querystring( 'groups' ) ) ) : ?>

	<div id="pag-top" class="pagination bp-pagination">

		<div class="pag-count" id="group-dir-count-top">
			<?php bp_groups_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="group-dir-pag-top">
			<?php bp_groups_pagination_links(); ?>
		</div>

	</div><!-- .bp-pagination -->

	<?php do_action( 'bp_before_directory_groups_list' ); ?>

	<ul id="groups-list" class="item-list dir-list" role="main">

	<?php while ( bp_groups() ) : bp_the_group(); ?>

		<li id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">
			<div class="item-header">
				<div class="item-avatar">
					<a href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar( 'type=thumb&width=50&height=50' ); ?></a>
				</div>
				
				<div class="item-title"><a href="<?php bp_group_permalink(); ?>"><?php bp_group_name(); ?></a></div>
				
				<span class="activity"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ); ?></span>
				
			</div><!-- .item-header -->

			<div class="item-content">
				<div class="item-desc"><?php bp_group_description_excerpt(); ?></div>
				<?php do_action( 'bp_directory_groups_item' ); ?>
			</div>

			<div class="item-action">

				<?php do_action( 'bp_directory_groups_actions' ); ?>

				<span class="action-meta">
					<?php bp_group_type(); ?> <span class="group-member-count"><?php bp_group_member_count(); ?></span>
				</span>

			</div><!-- .item-action -->
		</li>

	<?php endwhile; ?>

	</ul>

	<?php do_action( 'bp_after_directory_groups_list' ); ?>

	<div id="pag-bottom" class="pagination bp-pagination">

		<div class="pag-count" id="group-dir-count-bottom">
			<?php bp_groups_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="group-dir-pag-bottom">
			<?php bp_groups_pagination_links(); ?>
		</div>

	</div><!-- .bp-pagination -->

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no groups found.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_groups_loop' ); ?>
