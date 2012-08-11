<?php include ( TEMPLATEPATH . '/options-var.php' ); ?>

<?php if($tn_blogsmu_home_service_ads == "") { ?>
<?php } else { ?>
<div id="services-banner"><?php echo stripslashes( $tn_blogsmu_home_service_ads ); ?></div>
<?php } ?>

</div><!-- end content -->
</div><!-- end container -->
</div><!-- end wrapper -->


<?php
if($tn_blogsmu_home_footer_block == 'disable') { ?>
<?php } else { ?>
<div id="footer">
<div id="footer-wrap">

<div id="footer-content">


<div id="links">


<div class="linkbox">
<ul class="list">
<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar(__('footer 1', TEMPLATE_DOMAIN))) : ?>
<li>
<h3><?php _e('Recent Blog Post', TEMPLATE_DOMAIN); ?></h3>
<ul>
<?php wp_get_archives('type=postbypost&limit=10'); ?>
</ul>
</li>
<?php endif; ?>
</ul>
</div>



<div class="linkbox">
<ul class="list">
<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar(__('footer 2', TEMPLATE_DOMAIN))) : ?>
<li>
<h3><?php _e('Archives', TEMPLATE_DOMAIN); ?></h3>
<ul>
<?php wp_get_archives('type=monthly&limit=10&show_post_count=0'); ?>
</ul>
</li>
<?php endif; ?>
</ul>
</div>



<div class="mobile-blk">
<div class="linkbox">
<ul class="list">
<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar(__('footer 3', TEMPLATE_DOMAIN))) : ?>
<li>
<h3><?php _e('Meta', TEMPLATE_DOMAIN); ?></h3>
<ul>
<?php wp_register(); ?>
<li><?php wp_loginout(); ?></li>
<?php wp_meta(); ?>
</ul>
</li>
<?php endif; ?>
</ul>
</div>



<div class="linkbox">
<ul class="list">
<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar(__('footer 4', TEMPLATE_DOMAIN))) : ?>
<li>
<h3><?php _e('Recent Comments', TEMPLATE_DOMAIN); ?></h3>
<ul>
<?php get_custom_recent_comments(); ?>
</ul>
</li>
<?php endif; ?>
</ul>
</div>

</div>

</div>






</div>
</div>
</div>
<?php } ?>


<?php do_action( 'bp_after_container' ) ?>

<?php do_action( 'bp_before_footer' ) ?>


<div id="bottom-content">
<div class="bottom-content-inner">
<div class="alignleft">
&copy;<?php echo gmdate('Y'); ?> <a title="<?php bloginfo('description'); ?>" href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a><br /><?php echo wp_network_footer(); ?>
</div>

<div class="alignright">
<?php _e('Provided by', TEMPLATE_DOMAIN); ?> <a href="http://premium.wpmudev.org" title="WordPress plugins, themes and support"><?php _e("WPMU DEV - The WordPress Experts",TEMPLATE_DOMAIN); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#top-bg"><?php _e('Go back to top &uarr;', TEMPLATE_DOMAIN); ?></a>
</div>

</div>
</div>

<?php wp_footer(); ?>
<?php do_action( 'bp_footer' ) ?>
<?php do_action( 'bp_after_footer' ) ?>

</body>
</html>
