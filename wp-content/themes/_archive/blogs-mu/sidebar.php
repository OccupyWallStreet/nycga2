<?php do_action( 'bp_before_sidebar' ) ?>

<div id="sidebar">

<div class="sidebar">
<div class="sidebar-end">

<?php include (TEMPLATEPATH . '/options-var.php'); if($bp_existed == 'true') { //check if bp existed ?>

<?php do_action( 'bp_inside_before_sidebar' ) ?>

<?php if( !bp_is_blog_page() ) { ?>

<ul class="sidebar_list">

<?php if ( BP_FORUMS_SLUG == bp_current_component() && bp_is_directory() ) { ?>
<li id="text" class="widget widget_text">
<h3 class="widgettitle"><?php _e( 'Forum Tags', TEMPLATE_DOMAIN ) ?>  </h3>
<?php if ( function_exists('bp_forums_tag_heat_map') ) : ?>
<div class="textwidget" style="line-height: 26px;" id="tag-text"><?php bp_forums_tag_heat_map(); ?></div>
<?php endif; ?>
</li>
<?php } ?>


<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar( __('member-sidebar', TEMPLATE_DOMAIN) )) : ?>

<li id="text" class="widget widget_text">
<h3 class="widgettitle"><?php _e( 'Member Sidebar', TEMPLATE_DOMAIN ) ?></h3>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this column.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-5"><?php _e( 'Add Widgets', TEMPLATE_DOMAIN ) ?></a>
</div>
</li>

<?php endif; ?>

</ul>

<?php } else { // on profile activity component etc sidebar ?>


<ul class="sidebar_list">
<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar( __('left-sidebar', TEMPLATE_DOMAIN) )) : ?>


<li id="text" class="widget widget_text">
<h3 class="widgettitle"><?php _e( 'Left Sidebar', TEMPLATE_DOMAIN ) ?></h3>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this column.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar="><?php _e( 'Add Widgets', TEMPLATE_DOMAIN ) ?></a>
</div>
</li>

<?php endif; ?>
</ul>

<?php } ?>


<?php } else { //if bp not active ?>

<ul class="sidebar_list">
<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar( __('left-sidebar', TEMPLATE_DOMAIN) )) : ?>

<li>
<h3><?php _e('Recent Blog Post', TEMPLATE_DOMAIN); ?></h3>
<ul>
<?php wp_get_archives('type=postbypost&limit=5'); ?>       
</ul>
</li>

<li>
<h3><?php _e('Archives', TEMPLATE_DOMAIN); ?></h3>
<ul>
<?php wp_get_archives('type=monthly&limit=10&show_post_count=1'); ?>
</ul>
</li>

<li id="text" class="widget widget_text">
<h3 class="widgettitle"><?php _e( 'Left Sidebar', TEMPLATE_DOMAIN ) ?></h3>
<div class="textwidget">
<?php _e( 'Please log in and add widgets to this column.', TEMPLATE_DOMAIN ) ?>
&nbsp;<a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar="><?php _e( 'Add Widgets', TEMPLATE_DOMAIN ) ?></a>
</div>
</li>
<?php endif; ?>
</ul>
<?php } ?>

<?php do_action( 'bp_inside_after_sidebar' ) ?>

</div>
</div>
</div>



<?php if ( is_active_sidebar( __('bbpress-sidebar', TEMPLATE_DOMAIN) ) ) : ?>
<div class="bb-sidebar" id="sidebar">
<div class="sidebar">
<div class="sidebar-end">
<ul class="sidebar_list">
<?php dynamic_sidebar( __('bbpress-sidebar', TEMPLATE_DOMAIN) ); ?>
</ul>
</div>
</div>
</div>
<?php endif; ?>


<?php do_action( 'bp_after_sidebar' ) ?>