	<?php include (get_template_directory() . '/library/options/options.php'); ?>
</div>
</div>
</div>
</div>
<?php wp_reset_query(); ?>
	<?php if(is_home()) { ?>
		<?php locate_template( array( '/library/components/featured-footer.php' ), true ); ?>
		<?php } ?>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_container' ) ?>
			<?php do_action( 'bp_before_footer' ) ?>
		<?php endif; ?>
		</div>
		</div>
		
	<div id="backtop"><div id="backtopbutton"><a href="#top-content"><?php _e( 'back to top', 'business-blog' ) ?></a></div>
	</div>
	<div id="footer">
	<div id="footer-content">
	<div id="footer-content-wrap">
	<div class="aleft">
				<a href="http://premium.wpmudev.org/themes/" title="<?php _e( 'WordPress Plugins, Themes
				and Support', 'business-blog' )?>" ><?php _e( 'WPMU DEV - The WordPress Experts', 'business-blog' ) ?></a><a href="<?php echo get_option('home'); ?>"><?php _e( 'Copyright', 'business-blog' ) ?> &copy;<?php echo gmdate(__('Y')); ?> <?php bloginfo('name'); ?></a><a href="#site-wrapper"><?php _e('Go back to top &uarr;', 'business-blog'); ?></a>
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
	</body>
</html>