</div><!-- end content -->
<?php do_action( 'bp_after_content' ) ?>

<?php do_action( 'bp_before_footer' ) ?>

<div id="footer">

<div class="fleft">
&copy;<?php echo gmdate('Y'); ?> <a title="<?php bloginfo('description'); ?>" href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a>
<br /><?php echo wp_network_footer(); ?><?php do_action( 'bp_footer' ) ?>
</div>

<div class="fright">
<?php _e('Provided by', TEMPLATE_DOMAIN); ?> <a href="http://premium.wpmudev.org" title="WordPress plugins, themes and support"><?php _e("WPMU DEV - The WordPress Experts",TEMPLATE_DOMAIN); ?></a><span id="wblk">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#top-header"><?php _e('Go back to top &uarr;', TEMPLATE_DOMAIN); ?></a></span>
</div>
</div>

<?php do_action( 'bp_after_footer' ) ?>

<div id="footer-end"><?php do_action( 'bp_footer' ) ?><br /><?php wp_footer(); ?></div>

</div><!-- end container -->
<?php do_action( 'bp_after_container' ) ?>

</div><!-- end wrapper -->

</body>
</html>