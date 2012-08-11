	<?php include (get_template_directory() . '/library/options/options.php'); ?>
		<div class="clear"></div>
	</div><!--end container -->
</div><!-- end #site-wrapper or #site-wrapper-home -->
<?php if($bp_existed == 'true') : ?>
	<?php do_action( 'bp_after_container' ) ?>
	<?php do_action( 'bp_before_footer' ) ?>
<?php endif ?>
<?php
        $footfeat_on = get_option(
'dev_studio_footfeat_show');
        if ($footfeat_on == "yes"){
            ?>
                <?php locate_template( array(
'/library/components/feature-section.php' ), true ); ?>
    <?php } ?>
	<?php locate_template( array( '/library/components/widget-section.php' ), true ); ?>
	<?php if ( !is_user_logged_in() ) : ?>
			<?php locate_template( array( '/library/components/signup-box.php' ), true ); ?>
	<?php endif; ?>
	<div id="footer-wrapper"><!-- start #footer-wrapper -->
		<div id="footer"><!-- start #footer -->
			<div id="footer-links"><!-- start #footer-links-->
					<?php $footerlinks = get_option('dev_studio_footer_links'); ?>
					<?php echo stripslashes($footerlinks); ?>						
			<a href="http://premium.wpmudev.org/themes/" title="<?php _e( 'WordPress Plugins, Themes
				and Support', 'studio' )?>" ><?php _e( 'WPMU DEV - The WordPress Experts', 'studio' ) ?></a><a href="<?php echo get_option('home'); ?>"><?php _e( 'Copyright', 'studio' ) ?> &copy;<?php echo gmdate(__('Y')); ?> <?php bloginfo('name'); ?></a><a href="#site-wrapper"><?php _e('Go back to top &uarr;', 'studio'); ?></a>
					<?php if($bp_existed == 'true') : ?>
						<?php do_action( 'bp_footer' ) ?>
					<?php endif; ?>
			</div><!-- end #footer-links-->
		</div><!-- end #footer -->
	</div><!-- end #footer-wrapper -->
			<?php if($bp_existed == 'true') : ?>
				<?php do_action( 'bp_after_footer' ) ?>
			<?php endif; ?>
			<?php wp_footer(); ?>
			<!-- start google code-->
			<?php $googlecode = get_option('dev_studio_google');
			echo stripslashes($googlecode);
			?>
			<!-- end google code -->
</body>
</html>