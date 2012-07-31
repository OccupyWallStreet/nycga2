<?php /* Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_activity_loop() */ ?>

<?php do_action( 'bp_before_activity_loop' ) ?>

<?php if ( bp_has_activities( bp_ajax_querystring( 'activity' ) ) ) : ?>

	<?php /* Show pagination if JS is not enabled, since the "Load More" link will do nothing */ ?>
	<noscript>
		<div class="pagination">
			<div class="pag-count"><?php bp_activity_pagination_count() ?></div>
			<div class="pagination-links"><?php bp_activity_pagination_links() ?></div>
		</div>
	</noscript>

	<?php if ( empty( $_POST['page'] ) ) : ?>
		<ul id="activity-stream" class="activity-list item-list">
	<?php endif; ?>

	<?php while ( bp_activities() ) : bp_the_activity(); ?>

		<?php include( locate_template( array( 'activity/entry.php' ), false ) ) ?>

	<?php endwhile; ?>

	<?php if ( bp_get_activity_count() == bp_get_activity_per_page() ) : ?>
		<li class="load-more">
			<a href="#more"><?php _e( 'Load More', 'buddypress' ) ?></a> &nbsp; <span class="ajax-loader"></span>
		</li>
	<?php endif; ?>

	<?php if ( empty( $_POST['page'] ) ) : ?>
		</ul>
	<?php endif; ?>

<?php else : ?>
	<div id="message" class="info">
		<p><?php _e( 'Sorry, there was no activity found. Please try a different filter.', 'buddypress' ) ?></p>
	</div>
<?php endif; ?>

<?php do_action( 'bp_after_activity_loop' ) ?>

<form action="" name="activity-loop-form" id="activity-loop-form" method="post">
	<?php wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ) ?>
</form>