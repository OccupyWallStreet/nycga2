<?php include (TEMPLATEPATH . '/options-var.php'); if($bp_existed == 'true') { ?>
<?php do_action( 'bp_after_content' ) ?>
</div><!-- end content class -->
<?php } else { ?>
<?php do_action( 'bp_after_content' ) ?>
</div><!-- end content class -->
<?php } ?>

<?php do_action( 'bp_before_footer' ) ?>

<div class="footer" id="footer">
<div class="alignleft">
&copy;<?php echo gmdate('Y'); ?> <a title="<?php bloginfo('description'); ?>" href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a>
<br /><?php echo wp_network_footer(); ?><?php wp_footer(); ?>
</div>
<div class="alignright">
<?php _e('Provided by', TEMPLATE_DOMAIN); ?> <a href="http://premium.wpmudev.org" title="WordPress plugins, themes and support"><?php _e("WPMU DEV - The WordPress Experts",TEMPLATE_DOMAIN); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#top-bar"><?php _e('Go back to top &uarr;', TEMPLATE_DOMAIN); ?></a>
</div>
</div>


</div><!-- end container -->
<?php do_action( 'bp_after_container' ) ?>
</div><!-- end wrapper -->


<?php do_action( 'bp_after_footer' ) ?>

</body>

</html>