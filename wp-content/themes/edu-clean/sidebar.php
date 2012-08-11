<div id="sidebar">

<?php include (TEMPLATEPATH . '/options.php'); if($bp_existed == 'true') { //check if bp existed ?>

<?php if( !bp_is_blog_page()) { ?>

<?php if ( is_active_sidebar( __('member-sidebar', TEMPLATE_DOMAIN ) ) ) : ?>
<ul class="sidebar_list">
<?php dynamic_sidebar( __('member-sidebar', TEMPLATE_DOMAIN ) ); ?>
</ul>
<?php else: ?>
<ul class="sidebar_list">
<?php wp_list_categories('orderby=id&show_count=1&use_desc_for_title=0&title_li=<h3>' . __('Categories', TEMPLATE_DOMAIN) . '</h3>' ); ?>
</ul>
<?php endif; ?>

<?php } else { // on profile activity component etc sidebar ?>

<?php if ( is_active_sidebar( __('sidebar', TEMPLATE_DOMAIN ) ) ) : ?>
<ul class="sidebar_list">
<?php dynamic_sidebar( __('sidebar', TEMPLATE_DOMAIN ) ); ?>
</ul>
<?php else: ?>
<ul class="sidebar_list">
<?php wp_list_categories('orderby=id&show_count=1&use_desc_for_title=0&title_li=<h3>' . __('Categories', TEMPLATE_DOMAIN) . '</h3>' ); ?>
</ul>
<?php endif; ?>

<?php } ?>

<?php } else { // if no bp detected..lets back to normal ?>

<?php if ( is_active_sidebar( __('sidebar', TEMPLATE_DOMAIN ) ) ) : ?>
<ul class="sidebar_list">
<?php dynamic_sidebar( __('sidebar', TEMPLATE_DOMAIN ) ); ?>
</ul>
<?php else: ?>
<ul class="sidebar_list">
<?php wp_list_categories('orderby=id&show_count=1&use_desc_for_title=0&title_li=<h3>' . __('Categories', TEMPLATE_DOMAIN) . '</h3>' ); ?>
</ul>
<?php endif; ?>

<?php } ?>

</div>


<?php if ( is_active_sidebar( __('bbpress-sidebar', TEMPLATE_DOMAIN) ) ) : ?>
<div class="bb-sidebar" id="sidebar">
<ul class="sidebar_list">
<?php dynamic_sidebar( __('bbpress-sidebar', TEMPLATE_DOMAIN) ); ?>
</ul>
</div>
<?php endif; ?>