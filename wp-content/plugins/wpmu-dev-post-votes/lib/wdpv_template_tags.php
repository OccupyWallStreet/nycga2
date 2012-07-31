<?php

function wdpv_get_vote_up_ms ($standalone=true, $blog_id=false, $post_id=false) {
	if (!class_exists('Wdpv_Codec')) return false;

	$codec = new Wdpv_Codec;
	$standalone = $standalone ? 'yes' : 'no';
	return $codec->process_vote_up_code(array('standalone'=>$standalone, 'blog_id'=>$blog_id, 'post_id'=>$post_id));
}

function wdpv_get_vote_up ($standalone=true, $post_id=false) {
	return wdpv_get_vote_up_ms($standalone, false, $post_id);
}

function wdpv_vote_up ($standalone=true) {
	echo wdpv_get_vote_up ($standalone);
}

// Vote down

function wdpv_get_vote_down_ms ($standalone=true, $blog_id=false, $post_id=false) {
	if (!class_exists('Wdpv_Codec')) return false;

	$codec = new Wdpv_Codec;
	$standalone = $standalone ? 'yes' : 'no';
	return $codec->process_vote_down_code(array('standalone'=>$standalone, 'blog_id'=>$blog_id, 'post_id'=>$post_id));
}

function wdpv_get_vote_down ($standalone=true, $post_id=false) {
	return wdpv_get_vote_down_ms($standalone, false, $post_id);
}

function wdpv_vote_down ($standalone=true) {
	echo wdpv_get_vote_down ($standalone);
}

// Full voting widgets

function wdpv_get_vote_ms ($standalone=true, $blog_id=false, $post_id=false) {
	if (!class_exists('Wdpv_Codec')) return false;

	$codec = new Wdpv_Codec;
	$standalone = $standalone ? 'yes' : 'no';
	return $codec->process_vote_widget_code(array('standalone'=>$standalone, 'blog_id'=>$blog_id, 'post_id'=>$post_id));
}

function wdpv_get_vote ($standalone=true, $post_id=false) {
	return wdpv_get_vote_ms($standalone, false, $post_id);
}

function wdpv_vote ($standalone=true) {
	echo wdpv_get_vote ($standalone);
}

// Vote results

function wdpv_get_vote_result_ms ($standalone=true, $blog_id=false, $post_id=false) {
	if (!class_exists('Wdpv_Codec')) return false;

	$codec = new Wdpv_Codec;
	$standalone = $standalone ? 'yes' : 'no';
	return $codec->process_vote_result_code(array('standalone'=>$standalone, 'blog_id'=>$blog_id, 'post_id'=>$post_id));
}

function wdpv_get_vote_result ($standalone=true, $post_id=false) {
	return wdpv_get_vote_result_ms($standalone, false, $post_id);
}

function wdpv_vote_result ($standalone=true) {
	echo wdpv_get_vote_result ($standalone);
}

function wdpv_get_popular ($limit=5, $network=false) {
	if (!class_exists('Wdpv_Codec')) return false;

	$codec = new Wdpv_Codec;
	return $codec->process_popular_code(array('limit'=>$limit, 'network'=>$network));
}

function wdpv_popular ($standalone=true) {
	echo wdpv_get_popular ($standalone);
}

/**
 * Compatibility layer.
 */
if (!is_multisite()) {
	if (!function_exists('get_blog_permalink')) {
		function get_blog_permalink ($blog_id, $post_id) {
			return get_permalink($post_id);
		}
	}
	if (!function_exists('get_blog_post')) {
		function get_blog_post ($blog_id, $post_id) {
			return get_post($post_id);
		}
	}
}
