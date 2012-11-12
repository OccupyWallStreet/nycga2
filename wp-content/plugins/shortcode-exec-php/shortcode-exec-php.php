<?php
/*
Plugin Name: Shortcode Exec PHP
Plugin URI: http://wordpress.org/extend/plugins/shortcode-exec-php/
Description: Execute arbitrary, reusable PHP code in posts, pages, comments, widgets and RSS feeds using shortcodes in a safe and easy way
Version: 1.44
Author: Marcel Bokhorst
Author URI: http://blog.bokhorst.biz/about/
*/

/*
	Copyright 2010, 2011, 2012 Marcel Bokhorst

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
	Acknowledgments

	EditArea by Christophe Dolivet
	See http://www.cdolivet.com/index.php?page=editArea for details
	This script is publised under the GNU Lesser General Public License

	jQuery JavaScript Library
	This library is published under both the GNU General Public License and MIT License

	All licenses are GPL compatible (see http://www.gnu.org/philosophy/license-list.html#GPLCompatibleLicenses)
*/

#error_reporting(E_ALL);

// Include support class
require_once('shortcode-exec-php-class.php');

// Check pre-requisites
WPShortcodeExecPHP::Check_prerequisites();

// Start plugin
global $wp_shortcode_exec_php;
$wp_shortcode_exec_php = new WPShortcodeExecPHP();

// That's it!

?>
