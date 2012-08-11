<?php
/*
Plugin Name: Featured Posts Slideshow
Plugin URI: http://www.iwebix.de/featured-posts-slideshow-wordpress-plugin/
Description: This Plugin shows up to 5 Posts with a short description and a title at the right, and a image for every post on the left.
Version: 1.0
Author: Dennis Nissle, IWEBIX
Author URI: http://www.iwebix.de/
*/
/* options page */
$options_page = get_option('siteurl') . '/wp-admin/admin.php?page=featured-posts-slideshow/options.php';
function featured_options_page() {
	add_options_page('Featured Posts Slideshow Options', 'Featured Posts Slideshow', 10, 'featured-posts-slideshow/options.php');
}

add_action('admin_menu', 'featured_options_page');
?>
