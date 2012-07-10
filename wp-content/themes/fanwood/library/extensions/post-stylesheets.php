<?php
/**
 * Post Stylesheets - A WordPress script for post-specific stylesheets.
 *
 * Post Stylesheets allows users and developers to add unique, per-post stylesheets.  This script was 
 * created so that custom stylesheet files could be dropped into a theme's '/css' folder and loaded for 
 * individual posts using the 'Stylesheet' post meta key and the stylesheet name as the post meta value.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package PostStylesheets
 * @version 0.3.0
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2010 - 2012, Justin Tadlock
 * @link http://justintadlock.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * @todo register_meta()
 */

/* Register metadata with WordPress. */
add_action( 'init', 'post_stylesheets_register_meta' );

/* Add post type support for the 'post-stylesheets' feature. */
add_action( 'init', 'post_stylesheets_add_post_type_support' );
add_action( 'init', 'post_stylesheets_remove_post_type_support' );

/* Filters stylesheet_uri with a function for adding a new style. */
add_filter( 'stylesheet_uri', 'post_stylesheets_stylesheet_uri', 10, 2 );

/* Admin setup for the 'post-stylesheets' feature. */
add_action( 'admin_menu', 'post_stylesheets_admin_setup' );

/**
 * Registers the post stylesheets meta key ('Stylesheet') for posts and provides a function to sanitize
 * the metadata on update.
 *
 * @since 0.3.0
 * @return void
 */
function post_stylesheets_register_meta() {
	register_meta( 'post', post_stylesheets_get_meta_key(), 'post_stylesheets_sanitize_meta' );
}

/**
 * Callback function for sanitizing meta when add_metadata() or update_metadata() is called by WordPress. 
 * If a developer wants to set up a custom method for sanitizing the data, they should use the 
 * "sanitize_{$meta_type}_meta_{$meta_key}" filter hook to do so.
 *
 * @since 0.3.0
 * @param mixed $meta_value The value of the data to sanitize.
 * @param string $meta_key The meta key name.
 * @param string $meta_type The type of metadata (post, comment, user, etc.)
 * @return mixed $meta_value
 */
function post_styleheets_sanitize_meta( $meta_value, $meta_key, $meta_type ) {
	return esc_attr( strip_tags( $meta_value ) );
}

/**
 * Adds post type support for the 'post-stylesheets' feature to all 'public' post types.
 *
 * @since 0.3.0
 * @access private
 * @return void
 */
function post_stylesheets_add_post_type_support() {

	/* Get all available 'public' post types. */
	$post_types = get_post_types( array( 'public' => true ) );

	/* Loop through each of the public post types and add support for post stylesheets. */
	foreach ( $post_types as $type )
		add_post_type_support( $type, 'post-stylesheets' );
}

/**
 * Removes post stylesheets support for certain post types created by plugins.
 *
 * @since 0.3.0
 * @access private
 * @return void
 */
function post_stylesheets_remove_post_type_support() {

	/* Removes post stylesheets support of the bbPress 'topic' post type. */
	if ( function_exists( 'bbp_get_topic_post_type' ) )
		remove_post_type_support( bbp_get_topic_post_type(), 'post-stylesheets' );

	/* Removes post stylesheets support of the bbPress 'reply' post type. */
	if ( function_exists( 'bbp_get_reply_post_type' ) )
		remove_post_type_support( bbp_get_reply_post_type(), 'post-stylesheets' );
}

/**
 * Checks if a post (or any post type) has the given meta key of 'Stylesheet' when on the singular view of 
 * the post on the front of the site.  If found, the function checks within the '/css' folder of the stylesheet 
 * directory (child theme) and the template directory (parent theme).  If the file exists, it is used rather 
 * than the typical style.css file.
 *
 * @since 0.1.0
 * @todo Use features from Ticket #18302 when available. http://core.trac.wordpress.org/ticket/18302
 * @access private
 * @param string $stylesheet_uri The URI of the active theme's stylesheet.
 * @param string $stylesheet_dir_uri The directory URI of the active theme's stylesheet.
 * @return string $stylesheet_uri
 */
function post_stylesheets_stylesheet_uri( $stylesheet_uri, $stylesheet_dir_uri ) {

	/* Check if viewing a singular post. */
	if ( is_singular() ) {

		/* If viewing a bbPress topic, use its forum object. */
		if ( function_exists( 'bbp_is_single_topic' ) && bbp_is_single_topic() )
			$post = get_post( bbp_get_topic_forum_id( get_queried_object_id() ) );

		/* If viewing a bbPress reply, use its forum object. */
		elseif ( function_exists( 'bbp_is_single_reply' ) && bbp_is_single_reply() )
			$post = get_post( bbp_get_reply_forum_id( get_queried_object_id() ) );

		/* Get the queried object (post). */
		else
			$post = get_queried_object();

		/* Check if the post type supports 'post-stylesheets' before proceeding. */
		if ( post_type_supports( $post->post_type, 'post-stylesheets' ) ) {

			/* Check if the user has set a value for the post stylesheet. */
			$stylesheet = get_post_stylesheet( $post->ID );

			/* If a meta value was given and the file exists, set $stylesheet_uri to the new file. */
			if ( !empty( $stylesheet ) ) {

				/* If the stylesheet is found in the child theme '/css' folder, use it. */
				if ( file_exists( trailingslashit( get_stylesheet_directory() ) . "css/{$stylesheet}" ) )
					$stylesheet_uri = trailingslashit( $stylesheet_dir_uri ) . "css/{$stylesheet}";

				/* Else, if the stylesheet is found in the parent theme '/css' folder, use it. */
				elseif ( file_exists( trailingslashit( get_template_directory() ) . "css/{$stylesheet}" ) )
					$stylesheet_uri = trailingslashit( get_template_directory_uri() ) . "css/{$stylesheet}";
			}
		}
	}

	/* Return the stylesheet URI. */
	return $stylesheet_uri;
}

/**
 * Returns the post stylesheet if one is saved as post metadata.
 *
 * @since 0.3.0
 * @access public
 * @param int $post_id The ID of the post to get the stylesheet for.
 * @return string Stylesheet name if given.  Empty string for no stylesheet.
 */
function get_post_stylesheet( $post_id ) {
	return get_post_meta( $post_id, post_stylesheets_get_meta_key(), true );
}

/**
 * Adds/updates the post stylesheet for a specific post.
 *
 * @since 0.3.0
 * @access public
 * @param int $post_id The ID of the post to set the stylesheet for.
 * @param string $stylesheet The filename of the stylesheet.
 * @return bool True on successful update, false on failure.
 */
function set_post_stylesheet( $post_id, $stylesheet ) {
	return update_post_meta( $post_id, post_stylesheets_get_meta_key(), $stylesheet );
}

/**
 * Deletes a post stylesheet.
 *
 * @since 0.3.0
 * @access public
 * @param int $post_id The ID of the post to delete the stylesheet for.
 * @return bool True on successful delete, false on failure.
 */
function delete_post_stylesheet( $post_id ) {
	return delete_post_meta( $post_id, post_stylesheets_get_meta_key() );
}

/**
 * Checks if a post has a specific post stylesheet.
 *
 * @since 0.3.0
 * @access public
 * @param string $stylesheet The filename of the stylesheet.
 * @param int $post_id The ID of the post to check.
 * @return bool True|False depending on whether the post has the stylesheet.
 */
function has_post_stylesheet( $stylesheet, $post_id = '' ) {

	/* If no post ID is given, use WP's get_the_ID() to get it and assume we're in the post loop. */
	if ( empty( $post_id ) )
		$post_id = get_the_ID();

	/* Return true/false based on whether the stylesheet matches. */
	return ( $stylesheet == get_post_stylesheet( $post_id ) ? true : false );
}

/**
 * Admin setup for the post stylesheets script.
 *
 * @since 0.3.0
 * @access private
 * @return void
 */
function post_stylesheets_admin_setup() {

	/* Load the post meta boxes on the new post and edit post screens. */
	add_action( 'load-post.php', 'post_stylesheets_load_meta_boxes' );
	add_action( 'load-post-new.php', 'post_stylesheets_load_meta_boxes' );
}

/**
 * Hooks into the 'add_meta_boxes' hook to add the post stylesheets meta box and the 'save_post' hook 
 * to save the metadata.
 *
 * @since 0.3.0
 * @access private
 * @return void
 */
function post_stylesheets_load_meta_boxes() {

	/* Add the post stylesheets meta box on the 'add_meta_boxes' hook. */
	add_action( 'add_meta_boxes', 'post_stylesheets_create_meta_box', 10, 2 );

	/* Saves the post meta box data. */
	add_action( 'save_post', 'post_stylesheets_meta_box_save', 10, 2 );
}

/**
 * Adds the post stylesheets meta box if the post type supports 'post-stylesheets' and the current user has 
 * permission to edit post meta.
 *
 * @since 0.2.0
 * @access private
 * @param string $post_type The post type of the current post being edited.
 * @param object $post The current post object.
 * @return void
 */
function post_stylesheets_create_meta_box( $post_type, $post ) {

	/* Add the meta box if the post type supports 'post-stylesheets'. */
	if ( ( post_type_supports( $post_type, 'post-stylesheets' ) ) && ( current_user_can( 'edit_post_meta', $post->ID ) || current_user_can( 'add_post_meta', $post->ID ) || current_user_can( 'delete_post_meta', $post->ID ) ) )
		add_meta_box( "post-stylesheets", __( 'Stylesheet', 'post-stylesheets' ), 'post_stylesheets_meta_box', $post_type, 'side', 'default' );
}

/**
 * Displays the input field for entering a custom stylesheet.
 *
 * @since 0.2.0
 * @access private
 * @param object $object The post object currently being edited.
 * @param array $box Specific information about the meta box being loaded.
 * @return void
 */
function post_stylesheets_meta_box( $object, $box ) { ?>

	<p>
		<?php wp_nonce_field( basename( __FILE__ ), 'post-stylesheets-nonce' ); ?>
		<input type="text" class="widefat" name="post-stylesheets" id="post-stylesheets" value="<?php echo esc_attr( get_post_stylesheet( $object->ID ) ); ?>" />
	</p>
<?php
}

/**
 * Saves the user-selected post stylesheet on the 'save_post' hook.
 *
 * @since 0.2.0
 * @access private
 * @param int $post_id The ID of the current post being saved.
 * @param object $post The post object currently being saved.
 */
function post_stylesheets_meta_box_save( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['post-stylesheets-nonce'] ) || !wp_verify_nonce( $_POST['post-stylesheets-nonce'], basename( __FILE__ ) ) )
		return;

	/* Check if the post type supports 'post-stylesheets'. */
	if ( !post_type_supports( $post->post_type, 'post-stylesheets' ) )
		return;

	/* Get the meta key. */
	$meta_key = post_stylesheets_get_meta_key();

	/* Get the previous post stylesheet. */
	$meta_value = get_post_stylesheet( $post_id );

	/* Get the submitted post stylesheet. */
	$new_meta_value = $_POST['post-stylesheets'];

	/* If there is no new meta value but an old value exists, delete it. */
	if ( current_user_can( 'delete_post_meta', $post_id, $meta_key ) && '' == $new_meta_value && $meta_value )
		delete_post_stylesheet( $post_id );

	/* If a new meta value was added and there was no previous value, add it. */
	elseif ( current_user_can( 'add_post_meta', $post_id, $meta_key ) && $new_meta_value && '' == $meta_value )
		set_post_stylesheet( $post_id, $new_meta_value );

	/* If the old layout doesn't match the new layout, update the post layout meta. */
	elseif ( current_user_can( 'edit_post_meta', $post_id, $meta_key ) && $meta_value !== $new_meta_value )
		set_post_stylesheet( $post_id, $new_meta_value );
}

/**
 * Returns the meta key used by the script for post metadata.
 *
 * @since 0.3.0
 * @access public
 * @return string
 */
function post_stylesheets_get_meta_key() {
	return apply_filters( 'post_stylesheets_meta_key', 'Stylesheet' );
}

?>