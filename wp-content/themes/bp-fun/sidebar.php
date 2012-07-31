<div id="sidebar-column" class="bpside">

<?php
include (TEMPLATEPATH . '/options.php');
if($bp_existed == 'true') { ?>

<?php do_action('bp_sidebar_dir'); ?>

<?php if( bp_is_blog_page() || is_front_page() || !bp_current_component()) {  ?>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( __('blog-sidebar', TEMPLATE_DOMAIN) ) ) : ?>

<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e('Sidebar Widget', TEMPLATE_DOMAIN); ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this sidebar.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-4"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN) ?></a>
</div>
</div>

<?php endif; ?>

<?php } else { ?>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( __('buddypress-right', TEMPLATE_DOMAIN) ) ) : ?>

<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e('BuddyPress Right Widget', TEMPLATE_DOMAIN); ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this sidebar.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-5"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN) ?></a>
</div>
</div>

<?php endif; ?>

<?php } ?>


<?php } else { ?>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( __('blog-sidebar', TEMPLATE_DOMAIN) ) ) : ?>

<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e('Sidebar Widget', TEMPLATE_DOMAIN); ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this sidebar.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-4"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN) ?></a>
</div>
</div>

<?php endif; ?>

<?php } ?>

</div>


<?php if ( is_active_sidebar( __('bbpress-sidebar', TEMPLATE_DOMAIN) ) ) : ?>
<div class="bb-sidebar" id="sidebar-column">
<?php dynamic_sidebar( __('bbpress-sidebar', TEMPLATE_DOMAIN) ); ?>
</div>
<?php endif; ?>