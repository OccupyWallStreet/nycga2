<?php get_header() ?>

	<div id="content" class="link-home">
		<div class="padder">
			<?php if ( bp_has_links() ) : while ( bp_links() ) : bp_the_link(); ?>

			<?php do_action( 'bp_before_link_home_content' ) ?>

			<div id="item-header">
				<?php bp_links_locate_template( array( 'single/link-header.php' ), true ) ?>
			</div>

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_options_nav() ?>

						<?php do_action( 'bp_link_options_nav' ) ?>
					</ul>
				</div>
			</div>

			<div id="item-body">
				<?php do_action( 'bp_before_link_body' ) ?>

				<?php if ( bp_link_is_admin_page() && bp_link_is_visible() ) : ?>
					<?php bp_links_locate_template( array( 'single/admin.php' ), true ) ?>

				<?php elseif ( bp_link_is_visible() ) : ?>
					<?php
						if ( bp_links_is_activity_enabled() ):
							bp_links_locate_template( array( 'single/activity.php' ), true );
						endif;
					?>
				<?php else: ?>
					<?php /* The link is not visible, show the status message */ ?>

					<?php do_action( 'bp_before_link_status_message' ) ?>

					<div id="message" class="info">
						<p><?php bp_link_status_message() ?></p>
					</div>

					<?php do_action( 'bp_after_link_status_message' ) ?>
				<?php endif; ?>

				<?php do_action( 'bp_after_link_body' ) ?>
			</div>

			<?php do_action( 'bp_after_link_home_content' ) ?>

			<?php endwhile; endif; ?>
		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>
