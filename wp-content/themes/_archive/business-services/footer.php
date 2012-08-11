	<?php include (get_template_directory() . '/library/options/options.php'); ?>
</div>
</div>
</div>
	<?php locate_template( array( '/library/components/footer-adverts.php' ), true ); ?>
	<?php locate_template( array( '/library/components/footer-links.php' ), true ); ?>
<div class="ubox">
	<?php if($bp_existed == 'true') : ?>
			<h3><?php _e( 'Community', 'business-services') ?></h3>
	<?php locate_template( array( '/library/components/buddypress/buddypress-panel.php' ), true ); ?>
		<?php endif; ?>
	<?php if($bp_existed != 'true') : ?>	
				<?php if ( is_active_sidebar( 'home-sidebar' ) ) : ?>
						<?php dynamic_sidebar( 'home-sidebar' ); ?>
				<?php endif; ?>
		<?php endif; ?>
</div>
</div>
</div>
</div>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_container' ) ?>
			<?php do_action( 'bp_before_footer' ) ?>
			<?php endif; ?>
		<div id="footer-bottom">
		<div id="footer-bottom-inner">
		<div id="footer-bottom-content">
		<div class="fleft">
				<a href="http://premium.wpmudev.org/themes/" title="<?php _e( 'WordPress Plugins, Themes and Support', 'business-services' )?>" ><?php _e( 'WPMU DEV - The WordPress Experts', 'business-services' ) ?></a><a href="<?php echo get_option('home'); ?>"><?php _e( 'Copyright', 'business-services' ) ?> &copy;<?php echo gmdate(__('Y')); ?> <?php bloginfo('name'); ?></a><a href="#site-wrapper"><?php _e('Go back to top &uarr;', 'business-services'); ?></a>
				<?php if($bp_existed == 'true') : ?>
					<?php do_action( 'bp_footer' ) ?>
					<?php endif; ?>
					<?php if($bp_existed == 'true') : ?>
						<?php do_action( 'bp_after_footer' ) ?>
						<?php endif; ?>
					<?php wp_footer(); ?>
				
			</div>

			<div class="fright"></div>


			</div>
			</div>
			</div>
	</body>
</html>