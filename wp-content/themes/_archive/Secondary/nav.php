<div id="pagenav">

<?php
if(function_exists('wp_nav_menu')) {
wp_nav_menu(array(
'theme_location' => 'top-nav',
'container' => '',
'container_id' => 'logo-inner',
'menu_id' => 'top-nav',
'fallback_cb' => 'topnav_fallback',
));
} else {
?>
<?php
}
?>
</div>

<div id="categories" class="fix"> 
<ul class="fix">
<li class="<?php if ( is_category() or is_archive() or is_single() or is_paged() or is_search() or (function_exists('is_tag') and is_tag()) ) { ?>current_page_item<?php } else { ?>page_item<?php } ?>"></li>
<?php wp_list_categories('orderby=id&show_count=0&use_desc_for_title=1&depth=1&title_li='); ?>
</ul>
</div>