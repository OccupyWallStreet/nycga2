<?php do_action( 'bp_before_links_loop' ) ?>

<?php if ( bp_has_links( bp_ajax_querystring( 'links' ) ) ) : ?>

	<div class="pagination">

		<div class="pag-count" id="link-dir-count">
			<?php bp_links_pagination_count() ?>
		</div>

		<div class="pagination-links" id="link-dir-pag">
			<?php bp_links_pagination_links() ?>
		</div>

	</div>

	<?php do_action( 'bp_before_directory_links_list' ) ?>

	<ul id="link-list" class="item-list">
		<?php do_action( 'bp_before_directory_links_list_content' ) ?>
		
		<?php
			while ( bp_links() ) {
				bp_the_link();
				include( bp_links_locate_template( array( 'links-loop-item.php' ) ) );
			}
		?>

		<?php do_action( 'bp_after_directory_links_list_content' ) ?>
	</ul>

	<?php bp_link_vote_panel_form() ?>

	<?php do_action( 'bp_after_directory_links_list' ) ?>

	<?php do_action( 'bp_after_links_loop' ) ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no links found.', 'buddypress-links' ) ?></p>
	</div>

<?php endif; ?>