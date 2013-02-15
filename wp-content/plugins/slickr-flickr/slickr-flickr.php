<?php
/*
Plugin Name: Slickr Flickr
Plugin URI: http://www.slickrflickr.com
Description: Displays photos from Flickr in slideshows and galleries
Version: 1.43
Author: Russell Jamieson
Author URI: http://www.russelljamieson.com

Copyright 2011-2012 Russell Jamieson (russell.jamieson@gmail.com)

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
define('SLICKR_FLICKR_VERSION','1.43');
define('SLICKR_FLICKR', 'slickr-flickr');
define('SLICKR_FLICKR_PLUGIN_URL', plugins_url(SLICKR_FLICKR));
define('SLICKR_FLICKR_HOME', 'http://www.slickrflickr.com');

require_once(dirname(__FILE__).'/slickr-flickr-utils.php');
require_once(dirname(__FILE__).'/slickr-flickr-'.(is_admin()?'admin':'public').'.php');
?>