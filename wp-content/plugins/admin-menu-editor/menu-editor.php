<?php
/*
Plugin Name: Admin Menu Editor
Plugin URI: http://w-shadow.com/blog/2008/12/20/admin-menu-editor-for-wordpress/
Description: Lets you directly edit the WordPress admin menu. You can re-order, hide or rename existing menus, add custom menus and more. 
Version: 1.1.7
Author: Janis Elsts
Author URI: http://w-shadow.com/blog/
*/

//Are we running in the Dashboard?
if ( is_admin() ) {

    //Load the plugin
    require dirname(__FILE__) . '/includes/menu-editor-core.php';
    $wp_menu_editor = new WPMenuEditor(__FILE__, 'ws_menu_editor');

}//is_admin()
