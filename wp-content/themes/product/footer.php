	<?php include (get_template_directory() . '/library/options/options.php'); ?>
		</div> <!-- #container -->
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_container' ) ?>
			<?php do_action( 'bp_before_footer' ) ?>
			<?php 
					$navigationlocation = get_option('dev_product_nav_location');
					if ($navigationlocation == "bottom"){
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
		<?php endif; ?>
		<div id="footer">	
						<div id="footer-navigation">	<a href="http://premium.wpmudev.org/themes/" title="<?php _e( 'WordPress Plugins, Themes and Support', 'product' )?>" ><?php _e( 'WPMU DEV - The WordPress Experts', 'product' ) ?></a></a><a href="<?php echo get_option('home'); ?>"><?php _e( 'Copyright', 'product' ) ?> &copy;<?php echo gmdate(__('Y')); ?> <?php get_bloginfo( 'name' ); ?></a><a href="#site-wrapper"><?php _e('Go back to top &uarr;', 'product'); ?></a></div>
			<?php if($bp_existed == 'true') : ?>
				<?php do_action( 'bp_footer' ) ?>
		<?php endif; ?>
				<?php if($bp_existed == 'true') : ?>
					<?php do_action( 'bp_after_footer' ) ?>
		<?php endif; ?>
				<?php wp_footer(); ?>
				</div>	
								<div class="shadow-spacer"></div>
		</div>
	</body>
</html>