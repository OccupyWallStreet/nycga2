<?php /* Template Name: Homepage */
get_header();
?>
<div class="content">
<?php do_action( 'bp_before_home' ) ?>

<div id="home-left"><!-- home-left -->

<?php
$featured_status = get_option('tn_buddysocial_blog_featured_style_status');
if($featured_status == "enable") { ?>
<?php locate_template ( array('lib/templates/wp-template/gallery.php'), true ); ?>
<?php } ?>

<div id="box-left" class="box">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( __('home-left', TEMPLATE_DOMAIN) ) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Home Left Widget', TEMPLATE_DOMAIN ) ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this column.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-1"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN ) ?></a>
</div>
</div>
<?php endif; ?>
</div>

<?php
$layout_style = get_option('tn_buddysocial_blog_home_layout_style');
if($layout_style == "" || $layout_style == '3-column') { ?>
<div id="box-center" class="box">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( __('home-center', TEMPLATE_DOMAIN) ) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Home Center Widget', TEMPLATE_DOMAIN ) ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this column.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-2"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN ) ?></a>
</div>
</div>
<?php endif; ?>
</div>
<?php } ?>

</div><!-- close homeleft -->


<div id="box-right" class="box">

<?php
include (TEMPLATEPATH . '/options-var.php');
if($bp_existed == 'true') { ?>
<?php locate_template ( array('lib/templates/bp-template/bp-searchform.php'), true ); ?>
<?php } else { ?>
<?php get_search_form(); ?>
<?php } ?>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( __('home-right', TEMPLATE_DOMAIN) ) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Home Right Widget', TEMPLATE_DOMAIN ) ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this column.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-3"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN ) ?></a>
</div>
</div>
<?php endif; ?>
</div>

<?php //endif; ?>

<?php do_action( 'bp_after_home' ) ?>

</div>

<?php get_footer(); ?>