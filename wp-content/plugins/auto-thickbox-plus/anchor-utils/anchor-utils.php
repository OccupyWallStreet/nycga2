<?php
/*
 * Anchor Utils
 * Author: Denis de Bernardy <http://www.mesoconcepts.com>
 * Version: 1.1
 */

if ( @ini_get('pcre.backtrack_limit') <= 750000 )
	@ini_set('pcre.backtrack_limit', 750000);
if ( @ini_get('pcre.recursion_limit') <= 250000 )
	@ini_set('pcre.recursion_limit', 250000);

/**
 * anchor_utils
 *
 * @package Anchor Utils
 **/
class anchor_utils {

	/**
	 * ob_start()
	 *
	 * @return void
	 **/
	function ob_start() {
		static $done = false;

		if ( $done )
			return;

		if ( has_filter('ob_filter_anchor') ) {
			ob_start(array('anchor_utils', 'ob_filter'));
			add_action('wp_footer', array('anchor_utils', 'ob_flush'), 10000);
			$done = true;
		}
	} # ob_start()

	/**
	 * ob_filter()
	 *
	 * @param string $text
	 * @return string $text
	 **/
	function ob_filter($text) {
		global $escape_anchor_filter;
		$escape_anchor_filter = array();

		$text = anchor_utils::escape($text);

		$text = preg_replace_callback("/
			<\s*a\s+
			([^<>]+)
			>
			((?!<\s*\/\s*a\s*>).+?)
			<\s*\/\s*a\s*>
			/isx", array('anchor_utils', 'ob_filter_callback'), $text);

		$text = anchor_utils::unescape($text);

		return $text;
	} # ob_filter()

	/**
	 * ob_flush()
	 *
	 * @return void
	 **/
	function ob_flush() {
		static $done = true;

		if ( $done )
			return;

		ob_end_flush();
		$done = true;
	} # ob_flush()

	/**
	 * ob_filter_callback()
	 *
	 * @param array $match
	 * @return string $str
	 **/
	function ob_filter_callback($match) {
		# skip empty anchors
		if ( !trim($match[2]) )
			return $match[0];

		# parse anchor
		$anchor = anchor_utils::parse_anchor($match);

		if ( !$anchor )
			return $match[0];

		# filter anchor
		$anchor = apply_filters('ob_filter_anchor', $anchor);

		# return anchor
		return anchor_utils::build_anchor($anchor);
	} # ob_filter_callback()

	/**
	 * filter()
	 *
	 * @param string $text
	 * @return string $text
	 **/
	function filter($text) {
		if ( !has_filter('filter_anchor') )
			return $text;

		global $escape_anchor_filter;
		$escape_anchor_filter = array();

		$text = anchor_utils::escape($text);

		$text = preg_replace_callback("/
			<\s*a\s+
			([^<>]+)
			>
			((?!<\s*\/\s*a\s*>).+?)
			<\s*\/\s*a\s*>
			/isx", array('anchor_utils', 'filter_callback'), $text);

		$text = anchor_utils::unescape($text);

		return $text;
	} # filter()

	/**
	 * filter_callback()
	 *
	 * @param array $match
	 * @return string $str
	 **/
	function filter_callback($match) {
		# skip empty anchors
		if ( !trim($match[2]) )
			return $match[0];

		# parse anchor
		$anchor = anchor_utils::parse_anchor($match);

		if ( !$anchor )
			return $match[0];

		# filter anchor
		$anchor = apply_filters('filter_anchor', $anchor);

		# return anchor
		return anchor_utils::build_anchor($anchor);
	} # filter_callback()

	/**
	 * parse_anchor()
	 *
	 * @param array $match
	 * @return array $anchor
	 **/
	function parse_anchor($match) {
		$anchor = array();
		$anchor['attr'] = anchor_utils::shortcode_parse_atts($match[1]);

		if ( !is_array($anchor['attr']) || empty($anchor['attr']['href']) # parser error or no link
			|| $anchor['attr']['href'] != ( function_exists('esc_url') ? esc_url($anchor['attr']['href'], null, 'db') : clean_url($anchor['attr']['href'], null, 'db') ) ) # likely a script
			return false;

		foreach ( array('class', 'rel') as $attr ) {
			if ( !isset($anchor['attr'][$attr]) ) {
				$anchor['attr'][$attr] = array();
			} else {
				$anchor['attr'][$attr] = explode(' ', $anchor['attr'][$attr]);
				$anchor['attr'][$attr] = array_map('trim', $anchor['attr'][$attr]);
			}
		}

		$anchor['body'] = $match[2];

		$anchor['attr']['href'] = @html_entity_decode($anchor['attr']['href'], ENT_COMPAT, get_option('blog_charset'));

		return $anchor;
	} # parse_anchor()

	/**
	 * @see shortcode_parse_atts() in wp-includes/shortcodes.php
	 * @note This function accepts custom data attributes (data-*)
	 */
	function shortcode_parse_atts($text) {
		$atts = array();
		$pattern = '/([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w-]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
		$text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
		if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
			foreach ($match as $m) {
				if (!empty($m[1]))
					$atts[strtolower($m[1])] = stripcslashes($m[2]);
				elseif (!empty($m[3]))
					$atts[strtolower($m[3])] = stripcslashes($m[4]);
				elseif (!empty($m[5]))
					$atts[strtolower($m[5])] = stripcslashes($m[6]);
				elseif (isset($m[7]) and strlen($m[7]))
					$atts[] = stripcslashes($m[7]);
				elseif (isset($m[8]))
					$atts[] = stripcslashes($m[8]);
			}
		} else {
			$atts = ltrim($text);
		}
		return $atts;
	}

	/**
	 * build_anchor()
	 *
	 * @param array $anchor
	 * @return string $anchor
	 **/
	function build_anchor($anchor) {
		$anchor['attr']['href'] = function_exists('esc_url') ? esc_url($anchor['attr']['href']) : clean_url($anchor['attr']['href']);

		$str = '<a ';
		foreach ( $anchor['attr'] as $k => $v ) {
			if ( is_array($v) ) {
				$v = array_unique($v);
				if ( $v )
					$str .= ' ' . $k . '="' . implode(' ', $v) . '"';
			} else {
				$str .= ' ' . $k . '="' . $v . '"';
			}
		}
		$str .= '>' . $anchor['body'] . '</a>';

		return $str;
	} # build_anchor()

	/**
	 * escape()
	 *
	 * @param string $text
	 * @return string $text
	 **/
	function escape($text) {
		global $escape_anchor_filter;

		if ( !isset($escape_anchor_filter) )
			$escape_anchor_filter = array();

		foreach ( array(
			'head' => "/
				.*?
				<\s*\/\s*head\s*>
				/isx",
			'blocks' => "/
				<\s*(script|style|object|textarea)(?:\s.*?)?>
				.*?
				<\s*\/\s*\\1\s*>
				/isx",
			) as $regex ) {
			$text = preg_replace_callback($regex, array('anchor_utils', 'escape_callback'), $text);
		}

		return $text;
	} # escape()

	/**
	 * escape_callback()
	 *
	 * @param array $match
	 * @return string $text
	 **/
	function escape_callback($match) {
		global $escape_anchor_filter;

		$tag_id = "----escape_anchor_utils:" . md5($match[0]) . "----";
		$escape_anchor_filter[$tag_id] = $match[0];

		return $tag_id;
	} # escape_callback()

	/**
	 * unescape()
	 *
	 * @param string $text
	 * @return string $text
	 **/
	function unescape($text) {
		global $escape_anchor_filter;

		if ( !$escape_anchor_filter )
			return $text;

		$unescape = array_reverse($escape_anchor_filter);

		return str_replace(array_keys($unescape), array_values($unescape), $text);
	} # unescape()
} # anchor_utils

add_filter('the_content', array('anchor_utils', 'filter'), 100);
add_filter('the_excerpt', array('anchor_utils', 'filter'), 100);
add_filter('widget_text', array('anchor_utils', 'filter'), 100);
add_filter('comment_text', array('anchor_utils', 'filter'), 100);

add_action('wp_head', array('anchor_utils', 'ob_start'), 10000);
?>