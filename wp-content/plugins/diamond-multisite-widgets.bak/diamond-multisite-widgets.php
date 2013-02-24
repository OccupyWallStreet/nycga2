<?php
/*
 Plugin Name: Diamond MultiSite Widgets
 Plugin URI: http://wordpress.org/extend/plugins/diamond-multisite-widgets/
 Description: Multisite bloglist widget, Multisite recent posts widget, Multisite recent comments widget. Content from the whole network. An administration widget on the post-writing window. You can copy your post to the network's sub blogs. Post/Page shortcodes support. RSS Feed support.
 Author: Daniel Bozo
 Version: 1.8
 Author URI: http://www.amegrant.com
 */
 
/*  Copyright 2010  Daniel Bozo  (email : daniel.bozo@amegrant.hu)

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
	$diamond_multisite_widget_version = "1.8";
	
	require_once 'functions.php';
	require_once 'diamond-cache.php';
	require_once 'diamond-post-feed.php';
	require_once 'diamond-admin.php';
	require_once 'diamond-recent-posts.php';
	require_once 'diamond-recent-comments.php';
	require_once 'diamond-broadcast-posts.php';
	require_once 'diamond-bloglist.php';
 
 		
	
	?>