<?php if ( bp_event_jes_has_members( 'exclude_admins_mods=0' ) ) : ?>

	<?php do_action( 'bp_before_event_members_content' ) ?>

	<div class="pagination no-ajax">

		<div id="member-count" class="pag-count">
			<?php bp_event_member_pagination_count() ?>
		</div>

		<div id="member-pagination" class="pagination-links">
			<?php bp_event_member_pagination() ?>
		</div>

	</div>

	<?php do_action( 'bp_before_event_members_list' ) ?>

	<ul id="member-list" class="item-list">
		<?php while ( bp_event_members() ) : bp_event_the_member(); ?>

			<li>
				<a href="<?php bp_event_member_domain() ?>">
					<?php bp_event_member_avatar_thumb() ?>
				</a>
				<h5><?php bp_event_member_link() ?></h5>
				<span class="activity"><?php bp_event_member_joined_since() ?></span>

				<?php do_action( 'bp_event_members_list_item' ) ?>

				<?php if ( function_exists( 'friends_install' ) ) : ?>

					<div class="action">
						<?php bp_add_friend_button( bp_get_event_member_id(), bp_get_event_member_is_friend() ) ?>

						<?php do_action( 'bp_event_members_list_item_action' ) ?>
					</div>

				<?php endif; ?>
			</li>

		<?php endwhile; ?>

	</ul>

	<?php do_action( 'bp_after_event_members_content' ) ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'This event has no members.', 'jet-event-system' ); ?></p>
	</div>

<?php endif; ?>
