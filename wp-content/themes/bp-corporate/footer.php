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

<div>
<a rel="license" href="http://creativecommons.org/licenses/by/3.0/deed.en_US"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/80x15.png" /></a><br />
						<span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">nycga.net</span> Created in <a href="'http://wordpress.org" target="_blank">Wordpress</a> and <a href="http://buddypress.org" target="_blank">BuddyPress</a> by <a xmlns:cc="http://creativecommons.org/ns#" href="http://nycga.net" property="cc:attributionName" rel="cc:attributionURL">OWS Tech Ops</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by/3.0/deed.en_US">Creative Commons Attribution 3.0 Unported License</a>.<br />Source code is available at <a xmlns:dct="http://purl.org/dc/terms/" href="https://github.com/OccupyWallStreet/nycga2" rel="dct:source">https://github.com/OccupyWallStreet/nycga2</a>.
</div>

<?php do_action( 'bp_after_footer' ) ?>

</div>

</div>

<?php wp_footer(); ?>
<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->
</body>
</html>
