<?php include(TEMPLATEPATH . '/options-var.php'); ?>

<?php if($bp_existed == 'true') { //check if bp existed ?>

<?php if( !bp_is_blog_page() && bp_current_component() || bp_is_directory() ) { ?>

<?php locate_template( array( 'lib/templates/bp-template/optionsbar.php'), true ); ?>

<?php } else { ?>

<div id="sidebar"><!-- start sidebar -->

<?php if ( BP_FORUMS_SLUG == bp_current_component() && bp_is_directory() ) { ?>
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__('buddypress-right', TEMPLATE_DOMAIN )) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'BuddyPress Right Widget', TEMPLATE_DOMAIN ) ?>  </h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this sidebar.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-9"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN) ?></a>
</div>
</div>
<?php endif; ?>
<?php } else { ?>
<div id="center-column">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__('blog-sidebar', TEMPLATE_DOMAIN )) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Blog Sidebar Widget', TEMPLATE_DOMAIN) ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this sidebar.', TEMPLATE_DOMAIN) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-4"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN) ?></a>
</div>
</div>
<?php endif; ?>
</div>
<?php } ?>

</div><!-- end sidebar -->

<?php } ?>

<?php } else { ?>

<div id="sidebar">
<div id="center-column">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__('blog-sidebar', TEMPLATE_DOMAIN )) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Blog Sidebar Widget', TEMPLATE_DOMAIN) ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this sidebar.', TEMPLATE_DOMAIN) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-4"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN) ?></a>
</div>
</div>
<?php endif; ?>
</div>
</div>

<?php } ?>

<?php if ( is_active_sidebar( __('bbpress-sidebar', TEMPLATE_DOMAIN) ) ) : ?>
<div class="bb-sidebar" id="sidebar">
<div id="center-column">
<?php dynamic_sidebar( __('bbpress-sidebar', TEMPLATE_DOMAIN) ); ?>
</div>
</div>
<?php endif; ?>