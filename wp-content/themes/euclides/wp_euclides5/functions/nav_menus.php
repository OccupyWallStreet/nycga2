<?php

register_nav_menus(
	array(
	  'ci_main_menu' => __('Main Menu', CI_DOMAIN)
	)
);

// Add ID and Class attributes to the first <ul> occurence in wp_page_menu
function mainmenu_add_ul_atributes($ul_attributes) {
	return preg_replace('/<ul>/', '<ul class="main-nav">', $ul_attributes, 1);
}
add_filter('wp_page_menu','mainmenu_add_ul_atributes');


?>