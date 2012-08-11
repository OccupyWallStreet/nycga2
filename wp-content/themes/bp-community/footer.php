<?php do_action( 'bp_after_content' ) ?>
</div> <!-- end content -->
</div><!-- end container main -->
</div><!-- end container -->
</div><!-- end wrapper -->

<?php do_action( 'bp_before_footer' ) ?>

<div id="footer">
<div id="footer-wrap">
<div id="footer-content">
<div class="aleft">
&copy;<?php echo gmdate('Y'); ?> <a title="<?php bloginfo('description'); ?>" href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a>
<br /><?php echo wp_network_footer(); ?><br /><?php do_action( 'bp_footer' ) ?>
</div>

<div class="aright">
<?php _e('Provided by', TEMPLATE_DOMAIN); ?> <a href="http://premium.wpmudev.org" title="WordPress plugins, themes and support"><?php _e("WPMU DEV - The WordPress Experts",TEMPLATE_DOMAIN); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#top-header"><?php _e('Go back to top &uarr;', TEMPLATE_DOMAIN); ?></a>
</div>

</div>
</div>

<div class="wp-bp-footer"></div>

</div>
<?php wp_footer(); ?>
<?php do_action( 'bp_after_footer' ) ?>

</body>
</html>
