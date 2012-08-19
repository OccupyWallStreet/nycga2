<div id="side-entry">
<?php include( TEMPLATEPATH . '/options-var.php' ); ?>
<div id="center-column">
         
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__('center-column', TEMPLATE_DOMAIN )) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Center Column Widget', TEMPLATE_DOMAIN) ?></h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this column.', TEMPLATE_DOMAIN) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-2"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN) ?></a>
</div>
</div>
<?php endif; ?>
</div>

<?php if($bp_existed == 'true') { //check if bp existed ?>
<?php if ( $bp_front_is_activity == "true" )  { ?>
<?php } else { ?>
<div id="right-column">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__('right-column', TEMPLATE_DOMAIN )) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Right Column Widget', TEMPLATE_DOMAIN) ?> </h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this column.', TEMPLATE_DOMAIN) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-3"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN) ?></a>
</div>
</div>
<?php endif; ?>
</div>
<?php } ?>
<?php } else { ?>
<div id="right-column">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(__('right-column', TEMPLATE_DOMAIN )) ) : ?>
<div id="text" class="widget widget_text">
<h2 class="widgettitle"><?php _e( 'Right Column Widget', TEMPLATE_DOMAIN) ?> </h2>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this column.', TEMPLATE_DOMAIN) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-3"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN) ?></a>
</div>
</div>
<?php endif; ?>
</div>
<?php } ?>



</div>