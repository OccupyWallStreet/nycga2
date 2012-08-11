<?php include (TEMPLATEPATH . '/options.php'); ?>

<div id="sidebar">

<div id="right-column" class="bpside">

<?php if($bp_existed == 'true') { ?>

<?php if( bp_is_blog_page() || is_front_page() || !bp_current_component()) {  ?>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__('blog-sidebar', TEMPLATE_DOMAIN)) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Sidebar Widget', TEMPLATE_DOMAIN ) ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this sidebar.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-4"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN ) ?></a>
</div>
</div>
<?php endif; ?>

<?php } else { ?>

<?php if ( BP_FORUMS_SLUG == bp_current_component() && bp_is_directory() ) : ?>
<div id="forum-directory-tags" class="widget tags">
<h2 class="widgettitle"><?php _e( 'Forum Topic Tags', TEMPLATE_DOMAIN ) ?></h2>
<?php if ( function_exists('bp_forums_tag_heat_map') ) : ?>
<div class="textwidget" style="line-height: 200%;" id="tag-text"><?php bp_forums_tag_heat_map(); ?></div>
<?php endif; ?>
</div>
<?php endif; ?>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__('buddypress-sidebar', TEMPLATE_DOMAIN)) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'BuddyPress Sidebar Widget', TEMPLATE_DOMAIN ) ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this sidebar.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-5"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN ) ?></a>
</div>
</div>
<?php endif; ?>

<?php } ?>

<?php } else { ?>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__('blog-sidebar', TEMPLATE_DOMAIN)) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Sidebar Widget', TEMPLATE_DOMAIN ) ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this sidebar.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-4"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN ) ?></a>
</div>
</div>
<?php endif; ?>

<?php } ?>

</div>


</div>





<?php if ( is_active_sidebar( __('bbpress-sidebar', TEMPLATE_DOMAIN) ) ) : ?>
<div class="bb-sidebar" id="sidebar">
<div id="right-column" class="bpside">
<?php dynamic_sidebar( __('bbpress-sidebar', TEMPLATE_DOMAIN) ); ?>
</div>
</div>
<?php endif; ?>
