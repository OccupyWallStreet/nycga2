	<?php include (get_template_directory() . '/library/options/options.php'); ?>
		</div> <!-- #container -->
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_container' ) ?>
			<?php do_action( 'bp_before_footer' ) ?>
				<?php endif; ?>
		<div id="footer">
			<div class="footer-block">
					<?php if ( is_active_sidebar( 'footerone-sidebar' ) ) : ?>
							<?php dynamic_sidebar( 'footerone-sidebar' ); ?>
					<?php endif; ?>
			</div>
				<div class="footer-block">
								<?php if ( is_active_sidebar( 'footertwo-sidebar' ) ) : ?>
										<?php dynamic_sidebar( 'footertwo-sidebar' ); ?>
								<?php endif; ?>
				</div>
					<div class="footer-block">
									<?php if ( is_active_sidebar( 'footerthree-sidebar' ) ) : ?>
											<?php dynamic_sidebar( 'footerthree-sidebar' ); ?>
									<?php endif; ?>
					</div>
						<div class="footer-block-end">
										<?php if ( is_active_sidebar( 'footerfour-sidebar' ) ) : ?>
												<?php dynamic_sidebar( 'footerfour-sidebar' ); ?>
										<?php endif; ?>
						</div>
						<div class="clear"></div>
						</div>
						<div id="footer-navigation">	<a href="http://premium.wpmudev.org/themes/" title="<?php _e( 'WordPress Plugins, Themes and Support', 'bp-daily' )?>" ><?php _e( 'WPMU DEV - The WordPress Experts', 'bp-daily' ) ?></a><a href="<?php echo home_url() ; ?>"><?php _e( 'Copyright', 'bp-daily' ) ?> &copy;<?php echo gmdate(__('Y')); ?> <?php get_bloginfo( 'name' ); ?></a><a href="#site-wrapper"><?php _e('Go back to top &uarr;', 'bp-daily'); ?></a></div>
			<?php if($bp_existed == 'true') : ?>
				<?php do_action( 'bp_footer' ) ?>
					<?php endif; ?>
				<?php if($bp_existed == 'true') : ?>
					<?php do_action( 'bp_after_footer' ) ?>
						<?php endif; ?>
				<?php wp_footer(); ?>
		</div>
		</div>
	</body>
</html>