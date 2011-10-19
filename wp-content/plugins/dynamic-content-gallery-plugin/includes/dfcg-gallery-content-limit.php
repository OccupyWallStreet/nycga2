<?php
/**
* Front-end - Function to generate auto text for Slide Pane
*
* @copyright Copyright 2008-2010  Ade WALKER  (email : info@studiograsshopper.ch)
* @package dynamic_content_gallery
* @version 3.3.5
*
* @info Based on Limit Post plugin ( the_content_limit() ) function by Alfonso Sanchez-Paus Diaz y Julian Simon de Castro,
* @info further enhanced by Charles Clarkson and Nathan Rice, to deal with caption shortcodes etc,
* @info modified by me for use with DCG.
*
* @info Used to generate a custom excerpt from Post/Page content for display in the Slide Pane.
*
* @since 3.1
*/

/* Prevent direct access to this file */
if (!defined('ABSPATH')) {
	exit( __('Sorry, you are not allowed to access this file directly.', DFCG_DOMAIN) );
}



/**
* Function for creating custom excerpt from Post/Page content
*
* Strips caption shortcodes, html, script tags etc
* Used in gallery constructor functions
*
* @param	string	$max_char, Number of characters to display
* @param	string	$more_link_text, Text to display as link to post
* @param	string	$content, Post/Page content, only used if function is called outside the Loop/Wp_Query, ie when in Pages Method
* @param	int		$page_id, ID of Page, only used if function is called outside the Loop/Wp_Query, ie when in Pages Method
* @param	string	$stripteaser
* @return	Text excerpt
*
* Note that when dealing with non-Loop/WP_Query, ie when using the custom db query in the Pages Method, we don't have access
* to $post object, so get_the_content() and get_permalink() will not work as expected. Hence the extra $content and $page_id
* arguments for passing the Page content and ID to this function.
*
* @since 3.1
* @updated 3.3
*/
function dfcg_get_the_content_limit($max_char, $more_link_text = '(more...)', $content = NULL, $page_id = NULL, $stripteaser = 0) {
    
	// $max_char can be 0 if user only wants the more link to be displayed when using Auto Text
	if( $max_char == '0' ) {
		$content = '';
		$sep = '';
	
	} else {
		
		// If used in wp_query loop, $content arg must be empty
		// This will be true in Multi Option and One Category Methods
		if( empty($content) ) {
			// We're in a normal wp_query loop, so get the content
			$content = get_the_content('', $stripteaser);
		}

    	$content = apply_filters('get_the_content_limit', $content);

    	// Strip tags and shortcodes
    	$content = strip_tags(strip_shortcodes($content), apply_filters('get_the_content_limit_allowedtags', ''));

    	// Inline styles/scripts
    	$content = trim(preg_replace('#<(s(cript|tyle)).*?</\1>#si', '', $content));

    	// Truncate $content to $max_char
    	if((strlen($content) > $max_char) && ($espacio = strpos($content, ' ', $max_char ))) {
        	$content = substr($content, 0, $espacio);
    	}
	
		$sep = '&hellip;';
	}

	if( empty($more_link_text) ) {
        $link = '';
    
	// We have More link but no page id, ie we're in Multi Option or One Cat Methods
	} elseif( !empty($more_link_text) && empty($page_id) ) {
        $link = sprintf('%s <a href="%s" rel="nofollow">%s</a>', $sep, get_permalink(), $more_link_text);
    
	} else {
		// We have More link and page id, ie we're in Pages Method
		$link = sprintf('%s <a href="%s" rel="nofollow">%s</a>', $sep, get_permalink($page_id), $more_link_text);
	}

    return sprintf('<p>%s%s</p>', $content, $link);
}


/**
* Function for displaying custom excerpt
*
* Used in gallery constructor functions
*
* @since 3.1
*/
function dfcg_the_content_limit($max_char, $more_link_text = '(more...)', $content = NULL, $page_id = NULL, $stripteaser = 0) {
	$auto_text = dfcg_get_the_content_limit($max_char, $more_link_text, $content, $page_id, $stripteaser);
	return $auto_text;
}