<?php
/*
Plugin Name: CustomPress
Plugin URI: http://premium.wpmudev.org/project/custompress
Description: CustomPress - Custom Post, Taxonomy and Field Manager.
Version: 1.2.4.1
Author: Ivan Shaovchev, Andrey Shipilov (Incsub), Arnold Bailey (Incsub)
Author URI: http://premium.wpmudev.org
Text Domain: custompress
Domain Path: languages
WDP ID: 163
License: GNU General Public License (Version 2 - GPLv2)
Network: false
*/

$plugin_header_translate = array(    
__('CustomPress - Custom Post, Taxonomy and Field Manager.', 'custompress'),    
__('Ivan Shaovchev, Andrey Shipilov (Incsub), Arnold Bailey (Incsub)', 'custompress'),    
__('http://premium.wpmudev.org', 'custompress'),    
__('CustomPress', 'custompress'));

/*
Copyright 2011 Incsub, (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/* Define plugin version */
define ( 'CP_VERSION', '1.2.4.1' );
/* define the plugin folder url */
define ( 'CP_PLUGIN_URL', plugin_dir_url(__FILE__) );
/* define the plugin folder dir */
define ( 'CP_PLUGIN_DIR', plugin_dir_path(__FILE__) );
/* define the text domain for CustomPress */
define ( 'CP_TEXT_DOMAIN', 'custompress' );

/* include CustomPress files */
include_once 'core/wpmudev-dash-notification.php';

include_once 'core/core.php';
include_once 'core/content-types.php';

if ( is_admin() ) include_once 'core/admin.php';
