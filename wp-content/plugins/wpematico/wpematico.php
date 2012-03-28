<?php
/*
Plugin Name: WPeMatico
Plugin URI: http://www.netmdp.com/wpematico/
Description: Enables administrators to create posts automatically from RSS/Atom feeds.  If you like it, please rate it.
Author: Esteban Truelsegaard
Version: 0.85Beta
Author URI: http://www.netmdp.com
Text Domain: wpematico
Domain Path: /lang/
*/

/*
	Copyright 2010 Esteban Truelsegaard  (email : esteban@netmdp.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// don't load directly
if ( !defined('ABSPATH') )
	die('-1');

//Set plugin dirname
define('WPEMATICO_PLUGIN_BASEDIR', dirname(plugin_basename(__FILE__)));
//Set plugin dirname
define('WPEMATICO_DIR', WP_CONTENT_URL . '/plugins/' . plugin_basename(dirname(__FILE__)));
//Set Plugin Version
define('WPEMATICO_VERSION', '0.85Beta');
//load Text Domain
load_plugin_textdomain('wpematico', false, WPEMATICO_PLUGIN_BASEDIR.'/lang');
//Load functions file
require_once(dirname(__FILE__).'/app/functions.php');
//Plugin activate
register_activation_hook(__FILE__, 'wpematico_plugin_activate');
//Plugin deactivate
register_deactivation_hook(__FILE__, 'wpematico_plugin_deactivate');
//Plugin init
add_action('plugins_loaded', 'wpematico_plugins_loaded');
//Admin message
add_action('admin_notices', 'wpematico_admin_notice'); 
//Filters
add_action('the_permalink','wpematico_permalink');  
?>
