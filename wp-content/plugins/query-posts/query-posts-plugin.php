<?php
/**
 * Plugin Name: Query Posts
 * Plugin URI: http://justintadlock.com/archives/2009/03/15/query-posts-widget-wordpress-plugin
 * Description: A widget that allows you to show posts (or any post type) in any way you'd like on your site.
 * Version: 0.3.2
 * Author: Justin Tadlock
 * Author URI: http://justintadlock.com
 *
 * This plugin was created to allow users to show posts anywhere on their site.  Of course, the 
 * ability to show the widget anywhere rests on the idea that the theme has plenty of widget-ready 
 * areas.  This can be used to make simple lists in the sidebar, but it is so much more than that.  
 * Essentially, one could run a completely widgetized site with this plugin.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package QueryPosts
 * @version 0.3.2
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2008 - 2010, Justin Tadlock
 * @link http://justintadlock.com/archives/2009/03/15/query-posts-widget-wordpress-plugin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Launch the plugin. */
add_action( 'plugins_loaded', 'query_posts_setup' );

/**
 * Initialize the plugin.  This function loads the required files needed for the plugin
 * to run in the proper order.
 *
 * @since 0.3.0
 */
function query_posts_setup() {

	/* Set constant path to the members plugin directory. */
	define( 'QUERY_POSTS_DIR', plugin_dir_path( __FILE__ ) );

	/* Load the translation of the plugin. */
	load_plugin_textdomain( 'query-posts', false, '/query-posts/languages' );

	/* Load the plugin's widgets. */
	add_action( 'widgets_init', 'query_posts_load_widgets' );

	/* Create shortcodes. */
	add_action( 'init', 'query_posts_shortcodes', 11 );
}

/**
* Loads all the widget files at appropriate time. Calls the register function for each widget.
*
* @since 0.1.0
*/
function query_posts_load_widgets() {
	require_once( QUERY_POSTS_DIR . 'widget-query-posts.php' );
	register_widget( 'Query_Posts_Widget' );
}

/**
 * Check if specific shortcodes exist. If not, create them.
 *
 * @since 0.1.0
 */
function query_posts_shortcodes() {
	global $shortcode_tags;

	if ( !is_array( $shortcode_tags ) )
		return;

	if ( !array_key_exists( 'entry-author', $shortcode_tags ) )
		add_shortcode( 'entry-author', 'query_posts_entry_author_shortcode' );

	if ( !array_key_exists( 'entry-terms', $shortcode_tags ) )
		add_shortcode( 'entry-terms', 'query_posts_entry_terms_shortcode' );

	if ( !array_key_exists( 'entry-comments-link', $shortcode_tags ) )
		add_shortcode( 'entry-comments-link', 'query_posts_entry_comments_link_shortcode' );

	if ( !array_key_exists( 'entry-published', $shortcode_tags ) )
		add_shortcode( 'entry-published', 'query_posts_entry_published_shortcode' );

	if ( !array_key_exists( 'entry-edit-link', $shortcode_tags ) )
		add_shortcode( 'entry-edit-link', 'query_posts_entry_edit_link_shortcode' );
}

/**
 * Displays the edit link for an individual post.
 *
 * @since 0.3.0
 * @param array $attr
 */
function query_posts_entry_edit_link_shortcode( $attr ) {
	global $post;

	$domain = 'query-posts';
	$post_type = get_post_type_object( $post->post_type );

	if ( !current_user_can( "edit_{$post_type->capability_type}", $post->ID ) )
		return '';

	$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr );

	return $attr['before'] . '<span class="edit"><a class="post-edit-link" href="' . get_edit_post_link( $post->ID ) . '" title="' . sprintf( __( 'Edit %1$s', $domain ), $post->post_type ) . '">' . __( 'Edit', $domain ) . '</a></span>' . $attr['after'];
}

/**
 * Displays the published date of an individual post.
 *
 * @since 0.3.0
 * @param array $attr
 */
function query_posts_entry_published_shortcode( $attr ) {
	$domain = 'query-posts';
	$attr = shortcode_atts( array( 'before' => '', 'after' => '', 'format' => get_option( 'date_format' ) ), $attr );

	$published = '<abbr class="published" title="' . sprintf( get_the_time( __( 'l, F jS, Y, g:i a', $domain ) ) ) . '">' . sprintf( get_the_time( $attr['format'] ) ) . '</abbr>';
	return $attr['before'] . $published . $attr['after'];
}

/**
 * Displays a post's number of comments wrapped in a link to the comments area.
 *
 * @since 0.3.0
 * @param array $attr
 */
function query_posts_entry_comments_link_shortcode( $attr ) {

	$domain = 'query-posts';
	$number = get_comments_number();
	$attr = shortcode_atts( array( 'zero' => __( 'Leave a response', $domain ), 'one' => __( '1 Response', $domain ), 'more' => __( '%1$s Responses', $domain ), 'css_class' => 'comments-link', 'none' => '', 'before' => '', 'after' => '' ), $attr );

	if ( 0 == $number && !comments_open() && !pings_open() ) {
		if ( $attr['none'] )
			$comments_link = '<span class="' . esc_attr( $attr['css_class'] ) . '">' . $attr['none'] . '</span>';
	}
	elseif ( $number == 0 )
		$comments_link = '<a class="' . esc_attr( $attr['css_class'] ) . '" href="' . get_permalink() . '#respond" title="' . sprintf( __( 'Comment on %1$s', $domain ), the_title_attribute( 'echo=0' ) ) . '">' . $attr['zero'] . '</a>';
	elseif ( $number == 1 )
		$comments_link = '<a class="' . esc_attr( $attr['css_class'] ) . '" href="' . get_comments_link() . '" title="' . sprintf( __( 'Comment on %1$s', $domain ), the_title_attribute( 'echo=0' ) ) . '">' . $attr['one'] . '</a>';
	elseif ( $number > 1 )
		$comments_link = '<a class="' . esc_attr( $attr['css_class'] ) . '" href="' . get_comments_link() . '" title="' . sprintf( __( 'Comment on %1$s', $domain ), the_title_attribute( 'echo=0' ) ) . '">' . sprintf( $attr['more'], $number ) . '</a>';

	if ( $comments_link )
		$comments_link = $attr['before'] . $comments_link . $attr['after'];

	return $comments_link;
}

/**
 * Displays an individual post's author with a link to his or her archive.
 *
 * @since 0.3.0
 * @param array $attr
 */
function query_posts_entry_author_shortcode( $attr ) {
	$attr = shortcode_atts( array( 'before' => '', 'after' => '' ), $attr );
	$author = '<span class="author vcard"><a class="url fn n" href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" title="' . get_the_author_meta( 'display_name' ) . '">' . get_the_author_meta( 'display_name' ) . '</a></span>';
	return $attr['before'] . $author . $attr['after'];
}

/**
 * Displays a list of terms for a specific taxonomy.
 *
 * @since 0.3.0
 * @param array $attr
 */
function query_posts_entry_terms_shortcode( $attr ) {
	global $post;

	$attr = shortcode_atts( array( 'id' => $post->ID, 'taxonomy' => 'post_tag', 'separator' => ', ', 'before' => '', 'after' => '' ), $attr );

	$attr['before'] = '<span class="' . $attr['taxonomy'] . '">' . $attr['before'];
	$attr['after'] .= '</span>';

	return get_the_term_list( $attr['id'], $attr['taxonomy'], $attr['before'], $attr['separator'], $attr['after'] );
}

/**
 * Returns taxonomies that have $query_var set for the various post types of the current
 * WordPress installation.
 *
 * @since 0.3.0
 * @return array $out Array of available taxonomy names.
 */
function query_posts_get_taxonomies() {

	$post_types = get_post_types( array( 'exclude_from_search' => false ), 'names' );
	$post_type_taxonomies = array();
	$all_taxonomies = array();

	foreach ( $post_types as $post_type ) {
		$post_type_taxonomies = get_object_taxonomies( $post_type );
		if ( is_array( $post_type_taxonomies ) ) {
			foreach ( $post_type_taxonomies as $taxonomy ) {
				$tax = get_taxonomy( $taxonomy );
				if ( $tax->query_var || 'category' == $taxonomy || 'post_tag' == $taxonomy )
					$all_taxonomies[] = $taxonomy;
			}
		}
	}

	$out = array_unique( $all_taxonomies );

	return $out;
}

/**
 * Creates a form label with the given parameters for use with the widget.
 *
 * @since 0.3.0
 * @param string $label
 * @param string|int $id
 */
function query_posts_label( $label, $id ) {
	echo '<label for="' . esc_attr( $id ) . '"><code>' . $label . '</code></label>';
}

/**
 * Creates a form checkbox for use with the widget.
 *
 * @since 0.3.0
 * @param string $label
 * @param string|int $id
 * @param string $name
 * @param bool $checked
 */
function query_posts_input_checkbox( $label, $id, $name, $checked ) {
	echo "\n\t\t\t<p>";
	echo '<label for="' . esc_attr( $id ) . '" style="font-size:9px;">';
	echo '<input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" ' . $checked . ' />';
	echo "{$label}</label>";
	echo '</p>';
}

/**
 * Creates a textarea for use with the widget
 *
 * @since 0.3.0
 * @param string $label
 * @param string|int $id
 * @param string $name
 * @param string $value
 */
function query_posts_textarea( $label, $id, $name, $value ) {
	echo "\n\t\t\t<p>";
	query_posts_label( $label, $id );
	echo "<textarea id='" . esc_attr( $id ) . "' name='" . esc_attr( $name ) . "' rows='2' cols='10' class='widefat code' style='width:100%;height:3.5em;'>" . esc_html( $value ) . "</textarea>";
	echo '</p>';
}

/**
 * Creates a form text input for use with the widget.
 *
 * @since 0.3.0
 * @param string $label
 * @param string|int $id
 * @param string $name
 * @param string $value
 */
function query_posts_input_text( $label, $id, $name, $value ) {
	echo "\n\t\t\t<p>";
	query_posts_label( $label, $id );
	echo "<input type='text' id='" . esc_attr( $id ) . "' name='" . esc_attr( $name ) . "' value='" . esc_attr( $value ) . "' class='code widefat' />";
	echo '</p>';
}

/**
 * Creates a small text input for use with the widget.
 *
 * @since 0.3.0
 * @param string $label
 * @param string|int $id
 * @param string $name
 * @param string $value
 */
function query_posts_input_text_small( $label, $id, $name, $value ) {
	echo "\n\t\t\t<p>";
	query_posts_label( $label, $id );
	echo "<input type='text' id='" . esc_attr( $id ) . "' name='" . esc_attr( $name ) . "' value='" . esc_attr( $value ) . "' size='6' style='float: right; width: 50px;' class='code' />";
	echo '</p>';
}

/**
 * Creates a multiple slect box for use with the widget.
 *
 * @since 0.3.0
 * @param string $label
 * @param string|int $id
 * @param string $name
 * @param string $value
 * @param array $options
 * @param book $blank_option
 */
function query_posts_select_multiple( $label, $id, $name, $value, $options, $blank_option ) {

	$value = (array) $value;

	if ( $blank_option && is_array( $options ) )
		$options = array_merge( array( '' ), $options );

	echo "\n\t\t\t<p>";
	query_posts_label( $label, $id );
	echo "<select id='" . esc_attr( $id ) . "' name='" . esc_attr( $name ) . "[]' multiple='multiple' size='4' style='width:100%;height:5.0em;'>";

	foreach ( $options as $option_value => $option_label )
		echo "<option value='" . esc_attr( ( ( $option_value ) ? $option_value : $option_label ) ) . "'" . ( ( in_array( $option_value, $value ) || in_array( $option_label, $value ) ) ? " selected='selected'" : '' ) . ">" . esc_html( $option_label ) . "</option>";

	echo '</select>';
	echo '</p>';
}

/**
 * Creates a single slect box for use with the widget.
 *
 * @since 0.3.0
 * @param string $label
 * @param string|int $id
 * @param string $name
 * @param string $value
 * @param array $options
 * @param book $blank_option
 * @param string $class Optional.
 * @param string $style Optional.
 */
function query_posts_select_single( $label, $id, $name, $value, $options, $blank_option, $class = '', $style = '' ) {

	$style = ( ( $style ) ? $style . ' min-width: 50px;' : 'width:100%;' );
	$class = ( ( $class ) ? $class : 'widefat;' );

	if ( $blank_option )
		$options = array_merge( array( '' ), $options );

	echo "\n\t\t\t<p>";
	query_posts_label( $label, $id );
	echo "<select id='" . esc_attr( $id ) . "' name='" . esc_attr( $name ) . "' class='{$class}' style='{$style}'>";

	foreach ( $options as $option_value => $option_label ) {
		$option_value = (string) $option_value;
		$option_label = (string) $option_label;
		echo "<option value='" . esc_attr( ( ( $option_value ) ? $option_value : $option_label ) ) . "'" . ( ( $value == $option_value || $value == $option_label ) ? " selected='selected'" : '' ) . ">" . esc_html( $option_label ) . "</option>";
	}

	echo '</select>';
	echo '</p>';
}

?>