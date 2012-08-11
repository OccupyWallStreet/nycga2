	<?php include (get_template_directory() . '/library/options/options.php'); ?>
		</div> <!-- #container -->
		</div>
		</div>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_container' ) ?>
			<?php do_action( 'bp_before_footer' ) ?>
		<?php endif; ?>
		<div id="footer">
			<div class="content-wrap">
			<div class="content-content">
			<div class="aleft">
			<div id="fcopyright">
				<a href="http://premium.wpmudev.org/themes/" title="<?php _e( 'WordPress Plugins, Themes and Support', 'business-feature' )?>" ><?php _e( 'WPMU DEV - The WordPress Experts', 'business-feature' ) ?></a><a href="<?php echo get_option('home'); ?>"><?php _e( 'Copyright', 'business-feature' ) ?> &copy;<?php echo gmdate(__('Y')); ?> <?php bloginfo('name'); ?></a><a href="#site-wrapper"><?php _e('Go back to top &uarr;', 'business-feature'); ?></a>
			</div>
			</div>
			<?php if($bp_existed == 'true') : ?>
				<?php do_action( 'bp_footer' ) ?>
			<?php endif; ?>
				<?php if($bp_existed == 'true') : ?>
					<?php do_action( 'bp_after_footer' ) ?>
				<?php endif; ?>
				<?php wp_footer(); ?>
			</div>
			</div>
			</div>
	</body>
</html>