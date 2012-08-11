<?php
$index_style = get_option('tn_buddysocial_blog_index_layout_style');
if($index_style == '' || $index_style == '3-column') { ?>

<div id="left-sidebar">

<div id="arrow-break">
<div id="leftside"></div>
</div>

<div class="sidebar_list">


<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__('sidebar-left', TEMPLATE_DOMAIN)) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Sidebar Left Widget', TEMPLATE_DOMAIN ) ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this column.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-4"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN ) ?></a>
</div>
</div>
<?php endif; ?>







</div>
</div>

<?php } ?>