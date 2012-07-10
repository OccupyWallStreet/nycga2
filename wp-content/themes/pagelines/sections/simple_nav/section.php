<?php
/*
	Section: Simple Nav
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates footer navigation.
	Class Name: SimpleNav
	Workswith: footer
*/

/**
 * Simple Nav Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class SimpleNav extends PageLinesSection {

	/**
	* PHP that always loads no matter if section is added or not.
	*/
	function section_persistent(){
		register_nav_menus( array( 'simple_nav' => __( 'Simple Nav Section', 'pagelines' ) ) );

	}

	/**
	* Section template.
	*/
   function section_template() { 

	if(function_exists('wp_nav_menu'))
		wp_nav_menu( array('menu_class'  => 'inline-list simplenav font-sub', 'theme_location'=>'simple_nav','depth' => 1,  'fallback_cb'=>'simple_nav_fallback') );
	else
		nav_fallback();
	}

}

if(!function_exists('simple_nav_fallback')){

	/**
	*
	* @TODO document
	*
	*/
	function simple_nav_fallback() {
		printf('<ul id="simple_nav_fallback" class="inline-list simplenav font-sub">%s</ul>', wp_list_pages( 'title_li=&sort_column=menu_order&depth=1&echo=0') );
	}
}
