<?php
/**
 * BuddyPress - Activity Stream
 *
 * @package BuddyPress
 * @subpackage Theme
 */

get_header(); // Loads the header.php template. ?>

<?php do_atomic( 'before_content' ); // fanwood_before_content ?>

<div id="content">

	<?php do_atomic( 'open_content' ); // fanwood_open_content ?>
	
	<?php do_action( 'bp_before_directory_activity_page' ); ?>
	
	<div class="hfeed">
	
		<?php if ( current_theme_supports( 'breadcrumb-trail' ) ) breadcrumb_trail( array( 'separator' => '&raquo;' ) ); ?>
		
		<?php get_sidebar( 'before-content' ); // Loads the sidebar-before-content.php template. ?>

			<?php do_action( 'bp_before_directory_activity' ); ?>

			<?php if ( !is_user_logged_in() ) : ?>
			
				<div class="loop-meta">

					<h1 class="loop-title"><?php _e( 'Activity', 'buddypress' ); ?></h1>
					
				</div><!-- .loop-meta -->

			<?php endif; ?>

			<?php do_action( 'bp_before_directory_activity_content' ); ?>

			<?php if ( is_user_logged_in() ) : ?>

				<?php locate_template( array( 'activity/post-form.php'), true ); ?>

			<?php endif; ?>

			<?php do_action( 'template_notices' ); ?>

			<div class="item-list-tabs activity-type-tabs bp-tabs" role="navigation">
			
				<ul>
				
					<?php do_action( 'bp_before_activity_type_tab_all' ); ?>

					<li class="selected" id="activity-all"><a href="<?php echo bp_loggedin_user_domain() . bp_get_activity_slug() . '/'; ?>" title="<?php _e( 'The public activity for everyone on this site.', 'buddypress' ); ?>"><?php printf( __( 'All Members <span>%s</span>', 'buddypress' ), bp_get_total_site_member_count() ); ?></a></li>

					<?php if ( is_user_logged_in() ) : ?>

						<?php do_action( 'bp_before_activity_type_tab_friends' ) ?>

						<?php if ( bp_is_active( 'friends' ) ) : ?>

							<?php if ( bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>

								<li id="activity-friends"><a href="<?php echo bp_loggedin_user_domain() . bp_get_activity_slug() . '/' . bp_get_friends_slug() . '/'; ?>" title="<?php _e( 'The activity of my friends only.', 'buddypress' ); ?>"><?php printf( __( 'My Friends <span>%s</span>', 'buddypress' ), bp_get_total_friend_count( bp_loggedin_user_id() ) ); ?></a></li>

							<?php endif; ?>

						<?php endif; ?>

						<?php do_action( 'bp_before_activity_type_tab_groups' ) ?>

						<?php if ( bp_is_active( 'groups' ) ) : ?>

							<?php if ( bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ) : ?>

								<li id="activity-groups"><a href="<?php echo bp_loggedin_user_domain() . bp_get_activity_slug() . '/' . bp_get_groups_slug() . '/'; ?>" title="<?php _e( 'The activity of groups I am a member of.', 'buddypress' ); ?>"><?php printf( __( 'My Groups <span>%s</span>', 'buddypress' ), bp_get_total_group_count_for_user( bp_loggedin_user_id() ) ); ?></a></li>

							<?php endif; ?>

						<?php endif; ?>

						<?php do_action( 'bp_before_activity_type_tab_favorites' ); ?>

						<?php if ( bp_get_total_favorite_count_for_user( bp_loggedin_user_id() ) ) : ?>

							<li id="activity-favorites"><a href="<?php echo bp_loggedin_user_domain() . bp_get_activity_slug() . '/favorites/'; ?>" title="<?php _e( "The activity I've marked as a favorite.", 'buddypress' ); ?>"><?php printf( __( 'My Favorites <span>%s</span>', 'buddypress' ), bp_get_total_favorite_count_for_user( bp_loggedin_user_id() ) ); ?></a></li>

						<?php endif; ?>

						<?php do_action( 'bp_before_activity_type_tab_mentions' ); ?>

						<li id="activity-mentions"><a href="<?php echo bp_loggedin_user_domain() . bp_get_activity_slug() . '/mentions/'; ?>" title="<?php _e( 'Activity that I have been mentioned in.', 'buddypress' ); ?>"><?php _e( 'Mentions', 'buddypress' ); ?><?php if ( bp_get_total_mention_count_for_user( bp_loggedin_user_id() ) ) : ?> <strong><?php printf( __( '<span>%s new</span>', 'buddypress' ), bp_get_total_mention_count_for_user( bp_loggedin_user_id() ) ); ?></strong><?php endif; ?></a></li>

					<?php endif; ?>

					<?php do_action( 'bp_activity_type_tabs' ); ?>
					
				</ul>
				
			</div><!-- .item-list-tabs -->

			<div class="item-list-tabs bp-sub-tabs no-ajax" id="subnav" role="navigation">
			
				<ul>

					<li class="feed"><a href="<?php bp_sitewide_activity_feed_link() ?>" title="<?php _e( 'RSS Feed', 'buddypress' ); ?>"><?php _e( 'RSS', 'buddypress' ); ?></a></li>

					<?php do_action( 'bp_activity_syndication_options' ); ?>

					<li id="activity-filter-select" class="last">
						<label for="activity-filter-by"><?php _e( 'Show:', 'buddypress' ); ?></label> 
						<select id="activity-filter-by">
							<option value="-1"><?php _e( 'Everything', 'buddypress' ); ?></option>
							<option value="activity_update"><?php _e( 'Updates', 'buddypress' ); ?></option>

							<?php if ( bp_is_active( 'blogs' ) ) : ?>

								<option value="new_blog_post"><?php _e( 'Posts', 'buddypress' ); ?></option>
								<option value="new_blog_comment"><?php _e( 'Comments', 'buddypress' ); ?></option>

							<?php endif; ?>

							<?php if ( bp_is_active( 'forums' ) ) : ?>

								<option value="new_forum_topic"><?php _e( 'Forum Topics', 'buddypress' ); ?></option>
								<option value="new_forum_post"><?php _e( 'Forum Replies', 'buddypress' ); ?></option>

							<?php endif; ?>

							<?php if ( bp_is_active( 'groups' ) ) : ?>

								<option value="created_group"><?php _e( 'New Groups', 'buddypress' ); ?></option>
								<option value="joined_group"><?php _e( 'Group Memberships', 'buddypress' ); ?></option>

							<?php endif; ?>

							<?php if ( bp_is_active( 'friends' ) ) : ?>

								<option value="friendship_accepted,friendship_created"><?php _e( 'Friendships', 'buddypress' ); ?></option>

							<?php endif; ?>

							<option value="new_member"><?php _e( 'New Members', 'buddypress' ); ?></option>

							<?php do_action( 'bp_activity_filter_options' ); ?>

					</select>
				</li>
				
			</ul>
			
		</div><!-- .item-list-tabs -->

		<?php do_action( 'bp_before_directory_activity_list' ); ?>

		<div class="activity" role="main">

			<?php locate_template( array( 'activity/activity-loop.php' ), true ); ?>

		</div><!-- .activity -->
			
		<?php do_action( 'bp_after_directory_activity_list' ); ?>

		<?php do_action( 'bp_directory_activity_content' ); ?>

		<?php do_action( 'bp_after_directory_activity_content' ); ?>

		<?php do_action( 'bp_after_directory_activity' ); ?>

		<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>
		
	</div><!-- .hfeed -->
	
	<?php do_action( 'bp_after_directory_activity_page' ); ?>
	
	<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

</div><!-- #content -->

<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the header.php template. ?>