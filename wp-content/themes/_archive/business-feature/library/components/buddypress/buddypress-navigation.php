<div class="nav">
<div class="content-wrap">
<div class="content-content">
<ul>
	<?php wp_nav_menu( array('theme_location' => 'primary', 'menu_class' => 'sf-menu', 'container' => '', )); ?>
</ul>
</div>
</div>
</div>
<div class="clear"></div>
<div class="nav">
<div class="content-wrap">
<div class="content-content">
<ul class="sf-menu">
		<?php if ( 'activity' != bp_dtheme_page_on_front() && bp_is_active( 'activity' ) ) : ?>
			<li<?php if ( bp_is_page( BP_ACTIVITY_SLUG ) ) : ?> class="selected"<?php endif; ?>>
				<a href="<?php echo site_url() ?>/<?php echo BP_ACTIVITY_SLUG ?>/" title="<?php _e( 'Activity', 'business-feature' ) ?>"><?php _e( 'Activity', 'business-feature' ) ?></a>
			</li>
		<?php endif; ?>
		<li<?php if ( bp_is_page( BP_MEMBERS_SLUG ) || bp_is_member() ) : ?> class="selected"<?php endif; ?>>
			<a href="<?php echo site_url() ?>/<?php echo BP_MEMBERS_SLUG ?>/" title="<?php _e( 'Members', 'business-feature' ) ?>"><?php _e( 'Members', 'business-feature' ) ?></a>
		</li>
		<?php if ( bp_is_active( 'groups' ) ) : ?>
			<li<?php if ( bp_is_page( BP_GROUPS_SLUG ) || bp_is_group() ) : ?> class="selected"<?php endif; ?>>
				<a href="<?php echo site_url() ?>/<?php echo BP_GROUPS_SLUG ?>/" title="<?php _e( 'Groups', 'business-feature' ) ?>"><?php _e( 'Groups', 'business-feature' ) ?></a>
			</li>
			<?php if ( bp_is_active( 'forums' ) && bp_is_active( 'groups' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && !(int) bp_get_option( 'bp-disable-forum-directory' ) ) && bp_forums_is_installed_correctly() ) : ?>
				<li<?php if ( bp_is_page( BP_FORUMS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
					<a href="<?php echo site_url() ?>/<?php echo BP_FORUMS_SLUG ?>/" title="<?php _e( 'Forums', 'business-feature' ) ?>"><?php _e( 'Forums', 'business-feature' ) ?></a>
				</li>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ( bp_is_active( 'blogs' ) && bp_core_is_multisite() ) : ?>
			<li<?php if ( bp_is_page( BP_BLOGS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
				<a href="<?php echo site_url() ?>/<?php echo BP_BLOGS_SLUG ?>/" title="<?php _e( 'Blogs', 'business-feature' ) ?>"><?php _e( 'Blogs', 'business-feature' ) ?></a>
			</li>
		<?php endif; ?>
		<?php do_action( 'bp_nav_items' ); ?>
</ul>
</div>
</div>
</div>