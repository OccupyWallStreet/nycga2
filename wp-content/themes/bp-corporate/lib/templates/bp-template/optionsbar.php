<div id="profile-right">

<div class="sidebar_list">

<?php if ( BP_FORUMS_SLUG == bp_current_component() && bp_is_directory() ) : ?>
<div id="forum-directory-tags" class="widget tags">
<h2 class="widgettitle"><?php _e( 'Forum Topic Tags', TEMPLATE_DOMAIN ) ?></h2>
<?php if ( function_exists('bp_forums_tag_heat_map') ) : ?>
<div class="textwidget" id="tag-text"><?php bp_forums_tag_heat_map(); ?></div>
<?php endif; ?>
</div>
<?php endif; ?>


<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__('buddypress-right', TEMPLATE_DOMAIN )) ) : ?>
<div id="text1" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'BuddyPress Right Widget', TEMPLATE_DOMAIN ) ?>  </h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this sidebar.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo site_url(); ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-9"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN) ?></a>
</div>
</div>
<?php endif; ?>

</div>


</div>