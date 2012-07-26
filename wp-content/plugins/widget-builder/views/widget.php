<?php
/**
 * Widget template. This template can be overriden using the "tribe_widget_builder_widget.php" filter.
 * See the readme.txt file for more info.
 */

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

// build html
$widget = $before_widget;
$widget .= ( !empty( $title ) ) ? $before_title . $title . $after_title : '';
if ( !empty( $image ) ) {
	$widget .= ( !empty( $link_url ) ) ? '<a href="' . $link_url . '" target="_blank"><img src="' . $image[0] . '" /></a>' : '<img src="' . $image[0] . '" />'; 
}
$widget .= $content;
$widget .= ( !empty( $link_url ) ) ? '<a href="' . $link_url . '" target="_blank">' . $link_text . '</a>' : '';
$widget .= $after_widget;

// to screen
echo $widget;
