<div class="item-list-tabs no-ajax" id="subnav">
	<ul>
		<li class="feed"><a href="<?php bp_link_activity_feed_link() ?>" title="RSS Feed"><?php _e( 'RSS', 'buddypress' ) ?></a></li>

		<?php do_action( 'bp_link_activity_syndication_options' ) ?>

		<li id="activity-filter-select" class="last">
			<select>
				<option value="-1"><?php _e( 'No Filter', 'buddypress') ?></option>
				<?php do_action( 'bp_link_activity_filter_options' ) ?>
			</select>
		</li>
	</ul>
</div><!-- .item-list-tabs -->

<?php do_action( 'bp_before_link_activity_post_form' ) ?>

<?php if ( is_user_logged_in() ) : ?>
	<?php bp_links_locate_template( array( 'activity/post-form.php' ), true ) ?>
<?php endif; ?>

<?php do_action( 'bp_after_link_activity_post_form' ) ?>
<?php do_action( 'bp_before_link_activity_content' ) ?>

<div class="activity single-link">
	<?php locate_template( array( 'activity/activity-loop.php' ), true ) ?>
</div><!-- .activity -->

<?php do_action( 'bp_after_link_activity_content' ) ?>
