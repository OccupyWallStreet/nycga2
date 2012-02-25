<?php get_header() ?>

	<div id="content">
		<div class="padder" id="jes-padder">
			<?php if ( bp_jes_has_events() ) : while ( jes_bp_events() ) : bp_jes_the_event(); ?>

			<?php do_action( 'bp_before_event_home_content' ) ?>

			<div id="item-header">
				<?php locate_template( array( 'events/single/event-header.php' ), true ) ?>
			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_options_nav() ?>

						<?php do_action( 'bp_event_options_nav' ) ?>
					</ul>
				</div>
			</div><!-- #item-nav -->

	<div style="clear:left;"></div>

			<div id="item-body">
				<?php do_action( 'bp_before_event_body' ) ?>

				<?php if ( bp_is_event_admin_page() && jes_bp_event_is_visible() ) : ?>
					<?php locate_template( array( 'events/single/admin.php' ), true ) ?>

				<?php elseif ( bp_is_event_members() && jes_bp_event_is_visible() ) : ?>
					<?php locate_template( array( 'events/single/members.php' ), true ) ?>

				<?php elseif ( bp_is_jes_event_google_map_jes() && jes_bp_event_is_visible() ) : ?>
					<?php locate_template( array( 'events/single/google-map.php' ), true ) ?>

				<?php elseif ( bp_is_jes_event_flyer_jes() && jes_bp_event_is_visible() ) : ?>
					<?php locate_template( array( 'events/single/flyer.php' ), true ) ?>

				<?php elseif ( bp_is_jes_event_invite_jes() && jes_bp_event_is_visible() ) : ?>
					<?php locate_template( array( 'events/single/send-invites.php' ), true ) ?>

				<?php elseif ( bp_is_event_membership_request() ) : ?>
					<?php locate_template( array( 'events/single/request-join-to-event.php' ), true ) ?>

				<?php elseif ( jes_bp_event_is_visible() && bp_is_active( 'activity' ) ) : ?>
					<?php locate_template( array( 'events/single/details.php' ), true ) ?>

				<?php elseif ( !jes_bp_event_is_visible() ) : ?>
					<?php /* The event is not visible, show the status message */ ?>

					<?php do_action( 'bp_before_event_status_message' ) ?>

					<div id="message" class="info">
						<p><?php jes_bp_event_status_message() ?></p>
					</div>

					<?php do_action( 'bp_after_event_status_message' ) ?>

				<?php else : ?>
					<?php
						/* If nothing sticks, just load a event front template if one exists. */
						locate_template( array( 'events/single/front.php' ), true );
					?>
				<?php endif; ?>

				<?php do_action( 'bp_after_event_body' ) ?>
			</div><!-- #item-body -->

			<?php do_action( 'bp_after_event_home_content' ) ?>

			<?php endwhile; endif; ?>
		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>
