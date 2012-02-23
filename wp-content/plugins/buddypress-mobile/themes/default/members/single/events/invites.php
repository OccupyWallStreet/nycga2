<?php do_action( 'bp_before_event_invites_content' ) ?>

<?php if ( bp_jes_has_events( 'type=invites&user_id=' . bp_loggedin_user_id() ) ) : ?>

	<ul id="event-list" class="invites item-list">

		<?php while ( jes_bp_events() ) : bp_jes_the_event(); ?>

			<li>
				<?php jes_bp_event_avatar_thumb() ?>
				<h4><a href="<?php jes_bp_event_permalink() ?>"><?php jes_bp_event_name() ?></a><span class="small"> - <?php printf( __( '%s members', 'buddypress' ), jes_bp_event_total_members( false ) ) ?></span></h4>

				<p class="desc">
					<?php jes_bp_event_description_excerpt() ?>
				</p>

				<?php do_action( 'jes_event_invites_item' ) ?>

				<div class="action">
					<a class="button accept" href="<?php bp_event_jes_accept_invite_link() ?>"><?php _e( 'Accept', 'buddypress' ) ?></a> &nbsp;
					<a class="button reject confirm" href="<?php bp_event_reject_invite_link() ?>"><?php _e( 'Reject', 'buddypress' ) ?></a>

					<?php do_action( 'jes_event_invites_item_action' ) ?>

				</div>
			</li>

		<?php endwhile; ?>
	</ul>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'You have no outstanding event invites.', 'buddypress' ) ?></p>
	</div>

<?php endif;?>

<?php do_action( 'bp_after_event_invites_content' ) ?>