		<?php if ( 'activity' != bp_dtheme_page_on_front() && bp_is_active( 'activity' ) ) : ?>			
				<a href="<?php echo site_url() ?>/<?php echo BP_ACTIVITY_SLUG ?>/" title="<?php _e( 'Activity', 'business-portfolio' ) ?>"><?php _e( 'Activity', 'business-portfolio' ) ?></a>
		<?php endif; ?>
			<a href="<?php echo site_url() ?>/<?php echo BP_MEMBERS_SLUG ?>/" title="<?php _e( 'Members', 'business-portfolio' ) ?>"><?php _e( 'Members', 'business-portfolio' ) ?></a>
		<?php if ( bp_is_active( 'groups' ) ) : ?>
				<a href="<?php echo site_url() ?>/<?php echo BP_GROUPS_SLUG ?>/" title="<?php _e( 'Groups', 'business-portfolio' ) ?>"><?php _e( 'Groups', 'business-portfolio' ) ?></a>
			<?php if ( bp_is_active( 'forums' ) && bp_is_active( 'groups' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && !(int) bp_get_option( 'bp-disable-forum-directory' ) ) && bp_forums_is_installed_correctly() ) : ?>
					<a href="<?php echo site_url() ?>/<?php echo BP_FORUMS_SLUG ?>/" title="<?php _e( 'Forums', 'business-portfolio' ) ?>"><?php _e( 'Forums', 'business-portfolio' ) ?></a>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ( bp_is_active( 'blogs' ) && bp_core_is_multisite() ) : ?>
				<a href="<?php echo site_url() ?>/<?php echo BP_BLOGS_SLUG ?>/" title="<?php _e( 'Blogs', 'business-portfolio' ) ?>"><?php _e( 'Blogs', 'business-portfolio' ) ?></a>
		<?php endif; ?>
						<?php do_action( 'bp_nav_items' ); ?>