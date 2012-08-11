		</div> <!-- #container -->

		<?php do_action( 'bp_after_container' ) ?>
		<?php do_action( 'bp_before_footer' ) ?>

		<div id="footer">
	    	<p>	    <a href="http://premium.wpmudev.org/" title="<?php _e( 'Colours child theme by WPMU Dev', 'buddypress' )?>" ><?php _e( 'WPMU DEV', 'buddypress' ) ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="http://buddydress.com" title="<?php _e( 'Colours child theme by BuddyDress', 'buddypress' )?>" ><?php _e( 'BuddyDress', 'buddypress' ) ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo get_option('home'); ?>"><?php _e( 'Copyright', 'buddypress' ) ?> &copy;<?php echo gmdate(__('Y')); ?> <?php bloginfo('name'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#header"><?php _e('Go back to top &uarr;', 'buddypress'); ?></a></p>

			<?php do_action( 'bp_footer' ) ?>
		</div><!-- #footer -->

		<?php do_action( 'bp_after_footer' ) ?>

		<?php wp_footer(); ?>

	</body>

</html>