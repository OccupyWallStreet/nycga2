<?php
/*
Plugin Name: More Fields
Version: 2.1
Author URI: http://more-plugins.se/
Plugin URI: http://more-plugins.se/plugins/more-fields/
Description:  Add more input boxes to use on the write/edit page.
Author: Henrik Melin, Kal Ström
License: GPL2

	USAGE:

	See http://more-plugins.se/plugins/more-fields/

	Copyright (C) 2010  Henrik Melin, Kal Ström
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
    
*/
// Reset More Fields
if (0) update_option('more_fields', array());

// Plugin settings
$settings = array(
	'name' => 'More Fields', 
	'option_key' => 'more_fields',
	'fields' => array(),
	'default' => array(),
	'file' => __FILE__,
);

// Always on components
if (!defined('MORE_PLUGINS_DEV')) include('more-plugins/more-plugins.php');
else include(ABSPATH . '/wp-content/plugins/more-plugins.php');
include('more-fields-object.php');
include('more-fields-field-types.php');
include('more-fields-rewrite-object.php');
include('more-fields-template-functions.php');

$more_fields = new more_fields_object($settings);

// Load admin components
if (is_admin()) {
	if (!defined('MORE_PLUGINS_DEV')) include('more-plugins/more-plugins-admin.php');
	else include(ABSPATH . '/wp-content/plugins/more-plugins-admin.php');
	
	include('more-fields-settings-object.php');
	$more_fields_settings = new more_fields_admin($settings);
}

?>
