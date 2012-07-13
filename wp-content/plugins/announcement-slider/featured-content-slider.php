<?php
/*
Plugin Name: Announcement Slider
Plugin URI: http://nycga.net
Description: Based on the featured content slider plugin by IWEBIX, this is used to show Announcements in a nice slider.
Version: 1.0
Author: Pea, NYCGA
Author URI: http://nycga.net
*/


function insert_feat($atts, $content = null) {
    include (ABSPATH . '/wp-content/plugins/announcement-slider/content-slider.php');
}
add_shortcode("featslider", "insert_feat");

?>