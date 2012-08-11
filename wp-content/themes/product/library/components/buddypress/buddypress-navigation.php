	<div id="page-navigation">
		<?php wp_nav_menu( array('theme_location' => 'primary', 'menu_class' => 'sf-menu', 'container' => '', )); ?>
		<div class="clear"></div>
	</div>
	<div class="shadow-spacer"></div>
	<?php 
			$navigationlocation = get_option('dev_product_nav_location');
			if (($navigationlocation == "top") || ($navigationlocation == "")){
	?>
	<div id="buddypress-navigation">
				<ul class="sf-menu">
					<?php if ( 'activity' != bp_dtheme_page_on_front() && bp_is_active( 'activity' ) ) : ?>
						<li<?php if ( bp_is_page( BP_ACTIVITY_SLUG ) ) : ?> class="selected"<?php endif; ?>>
							<a href="<?php echo site_url() ?>/<?php echo BP_ACTIVITY_SLUG ?>/" title="<?php _e( 'Activity', 'product' ) ?>"><?php _e( 'Activity', 'product' ) ?></a>
						</li>
					<?php endif; ?>
					<li<?php if ( bp_is_page( BP_MEMBERS_SLUG ) || bp_is_member() ) : ?> class="selected"<?php endif; ?>>
						<a href="<?php echo site_url() ?>/<?php echo BP_MEMBERS_SLUG ?>/" title="<?php _e( 'Members', 'product' ) ?>"><?php _e( 'Members', 'product' ) ?></a>
					</li>
					<?php if ( bp_is_active( 'groups' ) ) : ?>
						<li<?php if ( bp_is_page( BP_GROUPS_SLUG ) || bp_is_group() ) : ?> class="selected"<?php endif; ?>>
							<a href="<?php echo site_url() ?>/<?php echo BP_GROUPS_SLUG ?>/" title="<?php _e( 'Groups', 'product' ) ?>"><?php _e( 'Groups', 'product' ) ?></a>
						</li>
						<?php if ( bp_is_active( 'forums' ) && bp_is_active( 'groups' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && !(int) bp_get_option( 'bp-disable-forum-directory' ) ) && bp_forums_is_installed_correctly() ) : ?>
							<li<?php if ( bp_is_page( BP_FORUMS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
								<a href="<?php echo site_url() ?>/<?php echo BP_FORUMS_SLUG ?>/" title="<?php _e( 'Forums', 'product' ) ?>"><?php _e( 'Forums', 'product' ) ?></a>
							</li>
						<?php endif; ?>
					<?php endif; ?>
					<?php if ( bp_is_active( 'blogs' ) && bp_core_is_multisite() ) : ?>
						<li<?php if ( bp_is_page( BP_BLOGS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
							<a href="<?php echo site_url() ?>/<?php echo BP_BLOGS_SLUG ?>/" title="<?php _e( 'Blogs', 'product' ) ?>"><?php _e( 'Blogs', 'product' ) ?></a>
						</li>
					<?php endif; ?>
					<?php do_action( 'bp_nav_items' ); ?>
			</ul>
			<div class="clear"></div>
	</div>
	<div class="shadow-spacer"></div>
	<?php
	}
	?>