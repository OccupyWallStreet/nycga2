<div class="navigation">
	<ul class="nav">
		<?php if ( 'activity' != bp_dtheme_page_on_front() && bp_is_active( 'activity' ) ) : ?>
			<li<?php if ( bp_is_page( BP_ACTIVITY_SLUG ) ) : ?> class="selected"<?php endif; ?>>
				<a href="<?php echo site_url() ?>/<?php echo BP_ACTIVITY_SLUG ?>/" title="<?php _e( 'Activity', 'business-blog' ) ?>"><?php _e( 'Activity', 'business-blog' ) ?></a>
			</li>
		<?php endif; ?>
		<li<?php if ( bp_is_page( BP_MEMBERS_SLUG ) || bp_is_member() ) : ?> class="selected"<?php endif; ?>>
			<a href="<?php echo site_url() ?>/<?php echo BP_MEMBERS_SLUG ?>/" title="<?php _e( 'Members', 'business-blog' ) ?>"><?php _e( 'Members', 'business-blog' ) ?></a>
		</li>
		<?php if ( bp_is_active( 'groups' ) ) : ?>
			<li<?php if ( bp_is_page( BP_GROUPS_SLUG ) || bp_is_group() ) : ?> class="selected"<?php endif; ?>>
				<a href="<?php echo site_url() ?>/<?php echo BP_GROUPS_SLUG ?>/" title="<?php _e( 'Groups', 'business-blog' ) ?>"><?php _e( 'Groups', 'business-blog' ) ?></a>
			</li>
			<?php if ( bp_is_active( 'forums' ) && bp_is_active( 'groups' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && !(int) bp_get_option( 'bp-disable-forum-directory' ) ) && bp_forums_is_installed_correctly() ) : ?>
				<li<?php if ( bp_is_page( BP_FORUMS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
					<a href="<?php echo site_url() ?>/<?php echo BP_FORUMS_SLUG ?>/" title="<?php _e( 'Forums', 'business-blog' ) ?>"><?php _e( 'Forums', 'business-blog' ) ?></a>
				</li>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ( bp_is_active( 'blogs' ) && bp_core_is_multisite() ) : ?>
			<li<?php if ( bp_is_page( BP_BLOGS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
				<a href="<?php echo site_url() ?>/<?php echo BP_BLOGS_SLUG ?>/" title="<?php _e( 'Blogs', 'business-blog' ) ?>"><?php _e( 'Blogs', 'business-blog' ) ?></a>
			</li>
		<?php endif; ?>
		<?php do_action( 'bp_nav_items' ); ?>
</ul>
</div>


