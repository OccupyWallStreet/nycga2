<div id="sidebar">

<div id="right-column" class="bpside">

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__('right-column', TEMPLATE_DOMAIN)) ) : ?>

<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Right Column Widget', TEMPLATE_DOMAIN ) ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this column.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-3"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN ) ?></a>
</div>
</div>

<?php endif; ?>

</div>

</div>