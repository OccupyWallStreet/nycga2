<?php
/**
 * Library Data Handling
 *
 * Handles user-submitted data formatting (input and output)
 *
 * @since 2.0.b2
 */


/**
 *
 * @TODO document
 *
 */
function pl_urlencode($text, $allowed = false) {
	$whitelist_tags = '<span><em><strong><i><b><u><code><br><strike><sub><sup>';
	return urlencode( trim( strip_tags( stripslashes($text), ($allowed) ? $whitelist_tags : false ) ) );
}


/**
 *
 * @TODO document
 *
 */
function pl_strip($text, $allowed = true) {
	$whitelist_tags = '<span><em><strong><i><b><u><code><br><strike><sub><sup>';
	return trim( strip_tags($text, ($allowed) ? $whitelist_tags : false) );
}


/**
 *
 * @TODO document
 *
 */
function pl_ehtml($text) {
	echo pl_html($text);
}


/**
 *
 * @TODO document
 *
 */
function pl_html($text) {
	return trim( htmlentities( stripslashes( $text ), ENT_QUOTES, 'UTF-8' ) );
}


/**
 *
 * @TODO document
 *
 */
function pl_texturize($text, $stripslashes = false, $decode = false) {
	return trim( wptexturize(($decode) ? urldecode($text) : (($stripslashes) ? stripslashes($text) : $text ) ) );
}


/**
 *
 * @TODO document
 *
 */
function pl_htmlspecialchars($text, $stripslashes = false, $decode = false) {
	return trim( htmlspecialchars(($decode) ? urldecode($text) : (($stripslashes) ? stripslashes($text) : $text ) ) );
}


/**
 *
 * @TODO document
 *
 */
function pl_noscripts($text) {
	return trim( pl_strip_only(stripslashes($text), '<script>', true ) );
}


/**
 *
 * @TODO document
 *
 */
function pl_strip_js($text) {
	return trim( pl_strip_only($text, '<script>', true) );
}


/**
 *
 * @TODO document
 *
 */
function pl_strip_only($str, $tags, $stripContent = false) {
	
	$content = '';
	if (!is_array($tags)) {
		$tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
		if (end($tags) == '') array_pop($tags);
	}
	foreach ($tags as $tag) {
		if ($stripContent) $content = '(.+</'.$tag.'[^>]*>|)';
		$str = preg_replace('#</?'.$tag.'[^>]*>'.$content.'#is', '', $str);
	}
	return $str;
}
