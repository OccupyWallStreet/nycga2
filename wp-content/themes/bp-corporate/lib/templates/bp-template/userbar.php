<div id="userbar">
<?php do_action( 'bp_user_bar_before' ) ?>

<?php do_action( 'bp_inside_after_user_bar' ) ?>

<div class="sidebar_list">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__('buddypress-left', TEMPLATE_DOMAIN )) ) : ?>
<div id="text1" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'BuddyPress Left Widget', TEMPLATE_DOMAIN ) ?>  </h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this sidebar.', TEMPLATE_DOMAIN) ?>
&nbsp;<a href="<?php echo site_url(); ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-8"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN) ?></a>
</div>
</div>
<?php endif ?>
</div>


</div>