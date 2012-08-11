<?php do_action( 'bp_after_content' ) ?>
</div>
<?php do_action( 'bp_after_container' ) ?>
</div>

</div><!-- end wrapper -->

<?php do_action( 'bp_before_footer' ) ?>

<div class="footer" id="footer">
<div id="footer-wrap">
<div id="footer-container"> 
<div class="myedu">&copy;<?php echo gmdate('Y'); ?> <a title="<?php bloginfo('description'); ?>" href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a><br /><?php echo wp_network_footer(); ?><br /><?php wp_footer(); ?>
</div>
<div id="incsubfooter">
<?php _e('Provided by', TEMPLATE_DOMAIN); ?> <a href="http://premium.wpmudev.org" title="WordPress plugins, themes and support"><?php _e("WPMU DEV - The WordPress Experts",TEMPLATE_DOMAIN); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#top-bar"><?php _e('Go back to top &uarr;', TEMPLATE_DOMAIN); ?></a>
</div>
</div>
</div>

</div>

<?php do_action( 'bp_footer' ) ?>

<?php do_action( 'bp_after_footer' ) ?>



</body>
</html>