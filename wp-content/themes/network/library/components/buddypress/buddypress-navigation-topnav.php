<ul id="buddypress-top-menu" class="sf-menu"> 
	<?php
	if (bp_is_page( BP_ACTIVITY_SLUG ) || bp_is_page( BP_MEMBERS_SLUG ) || bp_is_page( BP_GROUPS_SLUG ) || bp_is_page( BP_FORUMS_SLUG )) {
	?>
	<li class="buddypress-menu-dropdown current"> 
	<?php } else { ?>
	<li class="buddypress-menu-dropdown"> 
	<?php } ?>
		<a href="javascript:void(null)"><?php _e( 'Community', 'network' ) ?></a> 
		<ul> 
		<?php if ( ! bp_is_component_front_page('activity') && bp_is_active( 'activity' ) ) : ?>
			<li<?php if ( bp_is_page( BP_ACTIVITY_SLUG ) ) : ?> class="selected"<?php endif; ?>>
				<a href="<?php echo site_url() ?>/<?php echo BP_ACTIVITY_SLUG ?>/" title="<?php _e( 'Activity', 'network' ) ?>"><?php _e( 'Activity', 'network' ) ?></a>
			</li>
		<?php endif; ?>
		<li<?php if ( bp_is_page( BP_MEMBERS_SLUG ) || bp_is_user() ) : ?> class="selected"<?php endif; ?>>
			<a href="<?php echo site_url() ?>/<?php echo BP_MEMBERS_SLUG ?>/" title="<?php _e( 'Members', 'network' ) ?>"><?php _e( 'Members', 'network' ) ?></a>
		</li>
		<?php if ( bp_is_active( 'groups' ) ) : ?>
			<li<?php if ( bp_is_page( BP_GROUPS_SLUG ) || bp_is_group() ) : ?> class="selected"<?php endif; ?>>
				<a href="<?php echo site_url() ?>/<?php echo BP_GROUPS_SLUG ?>/" title="<?php _e( 'Groups', 'network' ) ?>"><?php _e( 'Groups', 'network' ) ?></a>
			</li>
			<?php if ( bp_is_active( 'forums' ) && bp_is_active( 'groups' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && !(int) bp_get_option( 'bp-disable-forum-directory' ) ) && bp_forums_is_installed_correctly() ) : ?>
				<li<?php if ( bp_is_page( BP_FORUMS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
					<a href="<?php echo site_url() ?>/<?php echo BP_FORUMS_SLUG ?>/" title="<?php _e( 'Forums', 'network' ) ?>"><?php _e( 'Forums', 'network' ) ?></a>
				</li>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ( bp_is_active( 'blogs' ) && is_multisite() ) : ?>
			<li<?php if ( bp_is_page( BP_BLOGS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
				<a href="<?php echo site_url() ?>/<?php echo BP_BLOGS_SLUG ?>/" title="<?php _e( 'Blogs', 'network' ) ?>"><?php _e( 'Blogs', 'network' ) ?></a>
			</li>
		<?php endif; ?>
		<?php do_action( 'bp_nav_items' ); ?>
		</ul> 
	</li> 
</ul> 