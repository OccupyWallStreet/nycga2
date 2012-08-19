<?php do_action( 'bp_after_content' ) ?>
</div>

</div>

<?php do_action( 'bp_after_container' ) ?>

</div><!-- end wrapper -->

<?php do_action( 'bp_before_footer' ) ?>

<div id="footer">
<div class="footer-inner">

<div class="footer-inner-class">

<div class="fbox">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( __('footer-left',TEMPLATE_DOMAIN) ) ) : ?>

<div id="text1" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Footer Left Widget', TEMPLATE_DOMAIN) ?>  </h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this footer.', TEMPLATE_DOMAIN) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-5"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN) ?></a>
</div>
</div>

<?php endif; ?>
</div>


<div class="fbox">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( __('footer-center', TEMPLATE_DOMAIN) ) ) : ?>

<div id="text2" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Footer Center Widget', TEMPLATE_DOMAIN) ?>  </h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this footer.', TEMPLATE_DOMAIN) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-6"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN) ?></a>
</div>
</div>

<?php endif; ?>
</div>


<div class="fbox">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( __('footer-right', TEMPLATE_DOMAIN) ) ) : ?>

<div id="text3" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Footer Right Widget', TEMPLATE_DOMAIN) ?>  </h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this footer.', TEMPLATE_DOMAIN) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-7"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN ) ?></a>
</div>
</div>

<?php endif; ?>
</div>


</div>


<div id="footer-cb" class="footer-inner-class">

<div class="alignleft">&copy;<?php echo gmdate('Y'); ?> <a title="<?php bloginfo('description'); ?>" href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a>
<br /><?php echo wp_network_footer(); ?><?php do_action( 'bp_footer' ) ?>
</div>

<div class="alignright"><?php _e('Provided by', TEMPLATE_DOMAIN); ?> <a href="http://premium.wpmudev.org" title="WordPress plugins, themes and support"><?php _e("WPMU DEV - The WordPress Experts",TEMPLATE_DOMAIN); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#top-header"><?php _e('Go back to top &uarr;', TEMPLATE_DOMAIN); ?></a></div>

</div>

<?php do_action( 'bp_after_footer' ) ?>

</div>

</div>

<?php wp_footer(); ?>
<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->
</body>
</html>
