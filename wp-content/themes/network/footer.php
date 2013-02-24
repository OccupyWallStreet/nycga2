<?php global $bp, $bp_existed; ?>

<div class="clear"></div>
</div>

<div class="footer-wrapper">
<?php get_sidebar('footer'); ?>
<div class="clear"></div>
</div>
<div class="footer-wrapper">
<div id="footer" class="copyright"><p><a href="http://premium.wpmudev.org/themes/" title="<?php _e( 'WordPress Plugins, Themes and Support', 'network' )?>" ><?php _e( 'WPMU DEV - The WordPress Experts', 'network' ) ?></a><a href="<?php echo get_option('home'); ?>"><?php _e( 'Copyright', 'network' ) ?> &copy;<?php echo gmdate(__('Y')); ?> <?php bloginfo('name'); ?></a><a href="#site-wrapper"><?php _e('Go back to top &uarr;', 'network'); ?></a></p></div>
	
<?php if($bp_existed == 'true') : ?><?php do_action( 'bp_footer' ) ?><?php endif; ?>

<div class="clear"></div>
</div>
<div class="clear"></div>
</div>
<!-- start google code-->
<?php $googlecode = get_option('dev_network_google');
echo stripslashes($googlecode);
?>
<!-- end google code -->

<?php wp_footer(); ?>
</body>
</html>