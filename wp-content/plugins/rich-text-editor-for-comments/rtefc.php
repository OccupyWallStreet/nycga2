<?php
/*
Plugin Name: Rich Text Editor For Comments
Plugin URI: http://www.planetbd.net/rich-text-editor-for-comments/
Description: Replaces the regular dry textarea in comments with a lightweight fully customizable WYSIWYG Rich text editor to make your commentators life even better! Works with WordPress, bbPress & BuddyPress! It uses open source jWYSIWYG and JQuery.
Author: Arafat Zahan
Version: 0.8.5
Author URI: http://www.planetbd.net
License: GPL2
*/

/*  Copyright 2012 - PlanetBD.net (http://www.planetbd.net/)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation. This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    
*/

if ( ! CUSTOM_TAGS ) {
	$allowedtags = array(
		'a' => array(
			'href' => array (),
			'title' => array ()),
		'abbr' => array(
			'title' => array ()),
		'acronym' => array(
			'title' => array ()),
		'b' => array(),
		'blockquote' => array(
			'cite' => array ()),
			'br' => array(),
		'cite' => array (),
		'code' => array(),
		'del' => array(
			'datetime' => array ()),
			'dd' => array(),
			'dl' => array(),
			'dt' => array(),
		'em' => array (), 'i' => array (),
		'img' => array(
			'alt' => array (),
			'align' => array (),
			'border' => array (),
			'class' => array (),
			'height' => array (),
			'hspace' => array (),
			'longdesc' => array (),
			'vspace' => array (),
			'src' => array (),
			'style' => array (),
			'width' => array ()),
			'ins' => array('datetime' => array(), 'cite' => array()),
			'li' => array(),
			'ol' => array(),
			'p' => array(),
		'q' => array(
			'cite' => array ()),
		'strike' => array(),
		'strong' => array(),
			'sub' => array(),
			'sup' => array(),
			'u' => array(),
			'ul' => array(),
	);
}

define('COMMENT_EDITOR_URL', WP_PLUGIN_URL.'/rich-text-editor-for-comments/');

add_action('wp_print_scripts', 'comment_ediror_print_scripts');
add_action('wp_head', 'comment_ediror_inline');

function comment_ediror_print_scripts() 
{
    wp_register_script('rtefc_js', COMMENT_EDITOR_URL.'rtefc.js', array('jquery'));
	wp_enqueue_script('rtefc_js');
}

function comment_ediror_inline()
{
?>
<script tipe="text/javascript">
(function(a){a(document).ready(function(){a("#comment,#topic_text,#reply_text,#message_content,#bbp_topic_content,#bbp_reply_content,.wpcf7-textarea").wysiwyg({controls:{bold:{visible:!0},italic:{visible:!0},underline:{visible:!0},strikeThrough:{visible:!0},justifyLeft:{visible:!1},justifyCenter:{visible:!0},justifyRight:{visible:!1},justifyFull:{visible:!0},createLink:{visible:!0},insertImage:{visible:!0},indent:{visible:!0},outdent:{visible:!0},subscript:{visible:!0},superscript:{visible:!0},undo:{visible:!0},redo:{visible:!1},paragraph:{visible:!1},code:{visible:!0},highlight:{visible:!0}, h1:{visible:!1},h2:{visible:!1},h3:{visible:!0},insertOrderedList:{visible:!0},insertUnorderedList:{visible:!0},insertHorizontalRule:{visible:!1},cut:{visible:!1},copy:{visible:!1},paste:{visible:!1},html:{visible:!0},removeFormat:{visible:!0},increaseFontSize:{visible:!0},decreaseFontSize:{visible:!0}}})})})(jQuery);
</script>
<?php

    echo '<link rel="stylesheet" type="text/css" href="'.COMMENT_EDITOR_URL.'rtefc.css" />';
}