<?php

/**
 * Attempt to find featured image.
 * If this fails, return filter hook output.
 */
function wdsb_get_image ($post_id=false, $size='medium') {
	// If we don't have post id, no reason to even try.
	$post_id = (int)$post_id;
	if (!$post_id) return apply_filters(
		'wdsb-media-image', '', $size
	);
	
	// Try to find featured image
	$thumb_id = function_exists('get_post_thumbnail_id') ? get_post_thumbnail_id($post_id) : false;
	if ($thumb_id) {
		$image = wp_get_attachment_image_src($thumb_id, $size);
		if ($image) return apply_filters(
			'wdsb-media-image',
			apply_filters('wdsb-media-image-featured_image', $image[0], $size), $size
		);
	}
	
	// Aw shucks, we're still here.
	return apply_filters(
		'wdsb-media-image', '', $size
	);
}

/**
 * Attempt to create link description.
 */
function wdsb_get_description ($post_id=false) {
	// If we don't have post id, no reason to even try.
	$post_id = (int)$post_id;
	if (!$post_id) return apply_filters(
		'wdsb-media-title', get_bloginfo('name')
	);
	
	return apply_filters(
		'wdsb-media-title', 
		apply_filters('wdsb-media-title-post_title', get_the_title($post_id))
	);
}

/**
 * Attempt to get fully qualified URL.
 */
function wdsb_get_url ($post_id=false) {
	$url = (@$_SERVER["HTTPS"] == 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	// If we don't have post id, no reason to even try.
	$post_id = (int)$post_id;
	if (!$post_id) return apply_filters(
		'wdsb-media-url', $url
	);
	
	return apply_filters(
		'wdsb-media-url',
		apply_filters('wdsb-media-url-post_url', get_permalink($post_id))
	);
}
