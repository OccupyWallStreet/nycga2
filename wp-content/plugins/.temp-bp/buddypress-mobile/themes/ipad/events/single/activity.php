<div class="item-list-tabs no-ajax" id="subnav">
	<ul>
		<li class="feed"><a href="<?php bp_event_activity_feed_link() ?>" title="RSS Feed"><?php _e( 'RSS', 'jet-event-system' ) ?></a></li>

		<?php do_action( 'bp_event_activity_syndication_options' ) ?>

		<li id="activity-filter-select" class="last">
			<select>
				<option value="-1"><?php _e( 'No Filter', 'jet-event-system' ) ?></option>
				<option value="activity_update"><?php _e( 'Show Updates', 'jet-event-system' ) ?></option>
				<option value="joined_event"><?php _e( 'Show New Event Memberships', 'jet-event-system' ) ?></option>

				<?php do_action( 'bp_event_activity_filter_options' ) ?>
			</select>
		</li>
	</ul>
</div><!-- .item-list-tabs -->

<?php do_action( 'bp_before_event_activity_post_form' ) ?>

<?php if ( is_user_logged_in() && bp_event_is_member() ) : ?>
	<?php locate_template( array( 'activity/post-form.php'), true ) ?>
<?php endif; ?>

<?php do_action( 'bp_after_event_activity_post_form' ) ?>
<?php do_action( 'bp_before_event_activity_content' ) ?>

<div class="activity single-event">
	<?php locate_template( array( 'activity/activity-loop.php' ), true ) ?>
</div><!-- .activity.single-event -->

<?php do_action( 'bp_after_event_activity_content' ) ?>
