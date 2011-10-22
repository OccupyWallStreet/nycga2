<div class="item-list-tabs no-ajax" id="subnav">
	<ul>
		<li class="feed"><a href="<?php bpcp_activity_feed_link() ?>" title="RSS Feed"><?php _e( 'RSS', 'buddypress' ) ?></a></li>

		<?php do_action( 'bp_group_activity_syndication_options' ) ?>

		<li id="activity-filter-select" class="last">
			<select>
				<option value="-1"><?php _e( 'No Filter', 'buddypress' ) ?></option>
				<option value="activity_update"><?php _e( 'Show Updates', 'buddypress' ) ?></option>

				<?php if ( bp_is_active( 'forums' ) ) : ?>
					<option value="new_forum_topic"><?php _e( 'Show New Forum Topics', 'buddypress' ) ?></option>
					<option value="new_forum_post"><?php _e( 'Show Forum Replies', 'buddypress' ) ?></option>
				<?php endif; ?>
			</select>
		</li>
	</ul>
</div><!-- .item-list-tabs -->

<?php if ( is_user_logged_in() && bp_group_is_member() ) : ?>
	<?php locate_template( array( 'activity/post-form.php'), true ) ?>
<?php endif; ?>

<div class="activity single-group">
	<?php locate_template( array( 'activity/activity-loop.php' ), true ) ?>
</div><!-- .activity.single-group -->
