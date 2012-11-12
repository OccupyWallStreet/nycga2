<?php
/*
Plugin Name: LiveTV Team - 0 - Main Fork
Plugin URI: http://kwark.allwebtuts.net
Description: liveTV Team - General Main fork - REQUIRED for all sup-parts - Activate this part First.
Author: Laurent (KwarK) Bertrand
Version: 1.3.1.1
Author URI: http://kwark.allwebtuts.net
*/

/*  
	Copyright 2012  Laurent (KwarK) Bertrand  (email : kwark@allwebtuts.net)
	
	Please consider a small donation for my work. Behind each code, there is a geek who has to eat.
	Thank you for my futur bundle...pizza-cola. Bundle vs bundle, it's a good deal, no ? 
	Small pizza donation @ http://kwark.allwebtuts.net
	
	You can not remove these comments such as my informations.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// disallow direct access to file
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	wp_die(__('Sorry, but you cannot access this page directly.', 'livetv'));
}

// General plugins path or url
$GLOBALS['livetv_plugin_path'] = $livetv_plugin_path = PLUGIN_DIR_PATH(__FILE__);
$GLOBALS['livetv_plugin_url'] = $livetv_plugin_url = WP_PLUGIN_URL . '/livetv-bundle/';
$GLOBALS['livetv_url'] = $livetv_url = home_url;

//langages
load_plugin_textdomain( 'livetv', true, dirname( plugin_basename( __FILE__ ) ) . '/wp-languages/' );


//Basics for admin page
add_action('admin_menu', 'livetv_options_page');

function livetv_options_page()
{
	add_menu_page('LiveTV bundle', __('LiveTV bundle', 'livetv'), 'manage_options', 'plugin-livetv-fork.php');
	add_submenu_page('plugin-livetv-fork.php', 'General config', __('General configuration', 'livetv'), 'manage_options', 'plugin-livetv-fork.php', 'livetv_do_admin_page_level_shortcode');
	
	do_action( 'livetv_add_submenu_page'); //plugin api
}

// Enqueue admin css
if(is_admin()){
	// Respects SSL, Style.css is relative to the current file
    wp_register_style( 'admincss', plugins_url('css/admin.css', __FILE__) );
    wp_enqueue_style( 'admincss' );
}

//Get role default roles and new roles
function get_roles()
{

	$wp_roles = new WP_Roles();
   	$roles = $wp_roles->get_names();
	
	return $roles;
}

function trunc($str, $limit=60)
{
	if (strlen($str) < $limit)
	{
		return $str;
	}
	$str = strrev(substr($str, 0, $limit));
	return strrev(substr($str, strpos($str, ' '))) . '';
}

//Api "extends" folder
$dirname = $livetv_plugin_path . 'extends/';

if($dirname)
{
	$dir = opendir($dirname);
	
	while($file = readdir($dir))
	{
		if($file != '.' && $file != '..' && !is_dir($dirname.$file))
		{
			include_once(''.$dirname.$file.'');
		}
	}
	
	closedir($dir);
}
do_action('livetv_extends');
?>