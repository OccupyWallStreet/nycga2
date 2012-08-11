	<div id="category-navigation">
		<ul class="sf-menu">
					<?php 
					wp_list_categories('orderby=id&show_count=0&title_li=');
					?>
				
		</ul>
		<div class="clear"></div>
	</div>
	
	<div class="page-navigation">
					<?php wp_nav_menu( array('theme_location' => 'primary', 'menu_class' => 'sf-menu', 'container' => '', )); ?>
	<div class="clear"></div>
</div>
	
	<div class="page-navigation">
				<ul class="sf-menu">
						<?php if ( 'activity' != bp_dtheme_page_on_front() && bp_is_active( 'activity' ) ) : ?>
							<li<?php if ( bp_is_page( BP_ACTIVITY_SLUG ) ) : ?> class="selected"<?php endif; ?>>
								<a href="<?php echo site_url() ?>/<?php echo BP_ACTIVITY_SLUG ?>/" title="<?php _e( 'Activity', 'bp-daily' ) ?>"><?php _e( 'Activity', 'bp-daily' ) ?></a>
							</li>
						<?php endif; ?>
						<li<?php if ( bp_is_page( BP_MEMBERS_SLUG ) || bp_is_member() ) : ?> class="selected"<?php endif; ?>>
							<a href="<?php echo site_url() ?>/<?php echo BP_MEMBERS_SLUG ?>/" title="<?php _e( 'Members', 'bp-daily' ) ?>"><?php _e( 'Members', 'bp-daily' ) ?></a>
						</li>
						<?php if ( bp_is_active( 'groups' ) ) : ?>
							<li<?php if ( bp_is_page( BP_GROUPS_SLUG ) || bp_is_group() ) : ?> class="selected"<?php endif; ?>>
								<a href="<?php echo site_url() ?>/<?php echo BP_GROUPS_SLUG ?>/" title="<?php _e( 'Groups', 'bp-daily' ) ?>"><?php _e( 'Groups', 'bp-daily' ) ?></a>
							</li>
							<?php if ( bp_is_active( 'forums' ) && bp_is_active( 'groups' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && !(int) bp_get_option( 'bp-disable-forum-directory' ) ) && bp_forums_is_installed_correctly() ) : ?>
								<li<?php if ( bp_is_page( BP_FORUMS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
									<a href="<?php echo site_url() ?>/<?php echo BP_FORUMS_SLUG ?>/" title="<?php _e( 'Forums', 'bp-daily' ) ?>"><?php _e( 'Forums', 'bp-daily' ) ?></a>
								</li>
							<?php endif; ?>
						<?php endif; ?>
						<?php if ( bp_is_active( 'blogs' ) && bp_core_is_multisite() ) : ?>
							<li<?php if ( bp_is_page( BP_BLOGS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
								<a href="<?php echo site_url() ?>/<?php echo BP_BLOGS_SLUG ?>/" title="<?php _e( 'Blogs', 'bp-daily' ) ?>"><?php _e( 'Blogs', 'bp-daily' ) ?></a>
							</li>
						<?php endif; ?>
						
						<?php do_action( 'bp_nav_items' ); ?>
					</ul>
			<div class="clear"></div>
	</div>