	<?php include (get_template_directory() . '/library/options/options.php'); ?>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_container' ) ?>
			<?php do_action( 'bp_before_footer' ) ?>
		<?php endif; ?>

	</div>
	</div>
	</div>
	<div id="footer">
	<div id="footer-inner">
	<div id="footer-content">
	<div class="fleft">
				<a href="http://premium.wpmudev.org/themes/" title="<?php _e( 'WordPress Plugins, Themes and Support', 'business-portfolio' )?>" ><?php _e( 'WPMU DEV - The WordPress Experts', 'business-portfolio' ) ?></a><a href="<?php echo get_option('home'); ?>"><?php _e( 'Copyright', 'business-portfolio' ) ?> &copy;<?php echo gmdate(__('Y')); ?> <?php bloginfo('name'); ?></a><a href="#top-wrap"><?php _e('Go back to top &uarr;', 'business-portfolio'); ?></a>
			
			</div>


			<div class="fright">
					<?php if($bp_existed == 'true') : ?>
				<?php locate_template( array( '/library/components/buddypress/buddypress-navigation.php' ), true ); ?>
					<?php endif; ?>
			</div>
				<?php wp_footer(); ?>
			</div>
			</div>
		
			<?php if($bp_existed == 'true') : ?>
				<?php do_action( 'bp_footer' ) ?>
			<?php endif; ?>
				<?php if($bp_existed == 'true') : ?>
					<?php do_action( 'bp_after_footer' ) ?>
				<?php endif; ?>
	</body>
</html>