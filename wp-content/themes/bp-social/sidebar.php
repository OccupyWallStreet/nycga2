<div class="main-sidebar" id="right-sidebar">
<div class="sidebar_list">

<?php include (TEMPLATEPATH . '/options-var.php'); ?>


<?php if($bp_existed == 'true') { ?>


<?php if ( function_exists( 'bp_message_get_notices' ) && is_user_logged_in() ) : ?>
	<?php bp_message_get_notices(); /* Site wide notices to all users */ ?>
<?php endif; ?>

<?php
if ( defined('BP_FORUMS_SLUG') && BP_FORUMS_SLUG == bp_current_component() && bp_is_directory() ) : ?>
<div id="forum-directory-tags" class="widget tags">
<h3><?php _e( 'Forum Topic Tags', TEMPLATE_DOMAIN ) ?></h3>
<?php if ( function_exists('bp_forums_tag_heat_map') ) : ?>
<div class="textwidget" id="tag-text" style="line-height: 24px;"><?php bp_forums_tag_heat_map( 11, 20, 'px', 55 ); ?></div>
<?php endif; ?>
</div>
<?php endif; ?>

<?php
global $bp;
if( defined('BP_GROUPS_SLUG') && $bp->current_component == BP_GROUPS_SLUG ) { ?>
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__('group-right', TEMPLATE_DOMAIN )) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Group Right Widget', TEMPLATE_DOMAIN ) ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this column.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-9"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN ) ?></a>
</div>
</div>
<?php endif; ?>

<?php } else if(!bp_is_blog_page()) { ?>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__('member-right', TEMPLATE_DOMAIN )) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Member Right Widget', TEMPLATE_DOMAIN ) ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this column.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-7"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN  ) ?></a>
</div>
</div>
<?php endif; ?>

<?php } else { ?>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__( 'sidebar-right',TEMPLATE_DOMAIN )) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Right Sidebar Widget', TEMPLATE_DOMAIN ) ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this column.',TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-5"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN ) ?></a>
</div>
</div>
<?php endif; ?>

<?php } ?>

<?php } else { // no bp installed ?>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__( 'sidebar-right',TEMPLATE_DOMAIN )) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Right Sidebar Widget', TEMPLATE_DOMAIN ) ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this column.',TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-5"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN ) ?></a>
</div>
</div>
<?php endif; ?>

<?php } ?>

</div><!-- end sidebar list -->
</div><!-- end sidebar -->



<?php if ( is_active_sidebar( __('bbpress-sidebar', TEMPLATE_DOMAIN) ) ) : ?>
<div class="main-sidebar bb-sidebar" id="right-sidebar">
<div class="sidebar_list">
<?php dynamic_sidebar( __('bbpress-sidebar', TEMPLATE_DOMAIN) ); ?>
</div>
</div>
<?php endif; ?>
