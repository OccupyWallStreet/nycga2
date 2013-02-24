<?php

/**
 * bbPress Core Theme Compatibility
 *
 * @package bbPress
 * @subpackage ThemeCompatibility
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** Theme Compat **************************************************************/

/**
 * What follows is an attempt at intercepting the natural page load process
 * to replace the_content() with the appropriate bbPress content.
 *
 * To do this, bbPress does several direct manipulations of global variables
 * and forces them to do what they are not supposed to be doing.
 *
 * Don't try anything you're about to witness here, at home. Ever.
 */

/** Base Class ****************************************************************/

/**
 * Theme Compatibility base class
 *
 * This is only intended to be extended, and is included here as a basic guide
 * for future Theme Packs to use. @link BBP_Twenty_Ten is a good example of
 * extending this class, as is @link bbp_setup_theme_compat()
 *
 * @since bbPress (r3506)
 */
class BBP_Theme_Compat {

	/**
	 * Should be like:
	 *
	 * array(
	 *     'id'      => ID of the theme (should be unique)
	 *     'name'    => Name of the theme (should match style.css)
	 *     'version' => Theme version for cache busting scripts and styling
	 *     'dir'     => Path to theme
	 *     'url'     => URL to theme
	 * );
	 * @var array 
	 */
	private $_data = array();

	/**
	 * Pass the $properties to the object on creation.
	 *
	 * @since bbPress (r3926)
	 * @param array $properties
	 */
    public function __construct( Array $properties = array() ) {
		$this->_data = $properties;
	}

	/**
	 * Set a theme's property.
	 *
	 * @since bbPress (r3926)
	 * @param string $property
	 * @param mixed $value
	 * @return mixed
	 */
	public function __set( $property, $value ) {
		return $this->_data[$property] = $value;
	}

	/**
	 * Get a theme's property.
	 *
	 * @since bbPress (r3926)
	 * @param string $property
	 * @param mixed $value
	 * @return mixed
	 */
	public function __get( $property ) {
		return array_key_exists( $property, $this->_data ) ? $this->_data[$property] : '';
	}
}

/** Functions *****************************************************************/

/**
 * Setup the default theme compat theme
 *
 * @since bbPress (r3311)
 * @param BBP_Theme_Compat $theme
 */
function bbp_setup_theme_compat( $theme = '' ) {
	$bbp = bbpress();

	// Make sure theme package is available, set to default if not
	if ( ! isset( $bbp->theme_compat->packages[$theme] ) || ! is_a( $bbp->theme_compat->packages[$theme], 'BBP_Theme_Compat' ) ) {
		$theme = 'default';
	}

	// Set the active theme compat theme
	$bbp->theme_compat->theme = $bbp->theme_compat->packages[$theme];
}

/**
 * Gets the name of the bbPress compatable theme used, in the event the
 * currently active WordPress theme does not explicitly support bbPress.
 * This can be filtered or set manually. Tricky theme authors can override the
 * default and include their own bbPress compatability layers for their themes.
 *
 * @since bbPress (r3506)
 * @uses apply_filters()
 * @return string
 */
function bbp_get_theme_compat_id() {
	return apply_filters( 'bbp_get_theme_compat_id', bbpress()->theme_compat->theme->id );
}

/**
 * Gets the name of the bbPress compatable theme used, in the event the
 * currently active WordPress theme does not explicitly support bbPress.
 * This can be filtered or set manually. Tricky theme authors can override the
 * default and include their own bbPress compatability layers for their themes.
 *
 * @since bbPress (r3506)
 * @uses apply_filters()
 * @return string
 */
function bbp_get_theme_compat_name() {
	return apply_filters( 'bbp_get_theme_compat_name', bbpress()->theme_compat->theme->name );
}

/**
 * Gets the version of the bbPress compatable theme used, in the event the
 * currently active WordPress theme does not explicitly support bbPress.
 * This can be filtered or set manually. Tricky theme authors can override the
 * default and include their own bbPress compatability layers for their themes.
 *
 * @since bbPress (r3506)
 * @uses apply_filters()
 * @return string
 */
function bbp_get_theme_compat_version() {
	return apply_filters( 'bbp_get_theme_compat_version', bbpress()->theme_compat->theme->version );
}

/**
 * Gets the bbPress compatable theme used in the event the currently active
 * WordPress theme does not explicitly support bbPress. This can be filtered,
 * or set manually. Tricky theme authors can override the default and include
 * their own bbPress compatability layers for their themes.
 *
 * @since bbPress (r3032)
 * @uses apply_filters()
 * @return string
 */
function bbp_get_theme_compat_dir() {
	return apply_filters( 'bbp_get_theme_compat_dir', bbpress()->theme_compat->theme->dir );
}

/**
 * Gets the bbPress compatable theme used in the event the currently active
 * WordPress theme does not explicitly support bbPress. This can be filtered,
 * or set manually. Tricky theme authors can override the default and include
 * their own bbPress compatability layers for their themes.
 *
 * @since bbPress (r3032)
 * @uses apply_filters()
 * @return string
 */
function bbp_get_theme_compat_url() {
	return apply_filters( 'bbp_get_theme_compat_url', bbpress()->theme_compat->theme->url );
}

/**
 * Gets true/false if page is currently inside theme compatibility
 *
 * @since bbPress (r3265)
 * @return bool
 */
function bbp_is_theme_compat_active() {
	$bbp = bbpress();

	if ( empty( $bbp->theme_compat->active ) )
		return false;

	return $bbp->theme_compat->active;
}

/**
 * Sets true/false if page is currently inside theme compatibility
 *
 * @since bbPress (r3265)
 * @param bool $set
 * @return bool
 */
function bbp_set_theme_compat_active( $set = true ) {
	bbpress()->theme_compat->active = $set;

	return (bool) bbpress()->theme_compat->active;
}

/**
 * Set the theme compat templates global
 *
 * Stash possible template files for the current query. Useful if plugins want
 * to override them, or see what files are being scanned for inclusion.
 *
 * @since bbPress (r3311)
 */
function bbp_set_theme_compat_templates( $templates = array() ) {
	bbpress()->theme_compat->templates = $templates;

	return bbpress()->theme_compat->templates;
}

/**
 * Set the theme compat template global
 *
 * Stash the template file for the current query. Useful if plugins want
 * to override it, or see what file is being included.
 *
 * @since bbPress (r3311)
 */
function bbp_set_theme_compat_template( $template = '' ) {
	bbpress()->theme_compat->template = $template;

	return bbpress()->theme_compat->template;
}

/**
 * Set the theme compat original_template global
 *
 * Stash the original template file for the current query. Useful for checking
 * if bbPress was able to find a more appropriate template.
 *
 * @since bbPress (r3926)
 */
function bbp_set_theme_compat_original_template( $template = '' ) {
	bbpress()->theme_compat->original_template = $template;

	return bbpress()->theme_compat->original_template;
}

/**
 * Set the theme compat original_template global
 *
 * Stash the original template file for the current query. Useful for checking
 * if bbPress was able to find a more appropriate template.
 *
 * @since bbPress (r3926)
 */
function bbp_is_theme_compat_original_template( $template = '' ) {
	$bbp = bbpress();

	if ( empty( $bbp->theme_compat->original_template ) )
		return false;

	return (bool) ( $bbp->theme_compat->original_template == $template );
}

/**
 * Register a new bbPress theme package to the active theme packages array
 *
 * @since bbPress (r3829)
 * @param array $theme
 */
function bbp_register_theme_package( $theme = array(), $override = true ) {

	// Create new BBP_Theme_Compat object from the $theme array
	if ( is_array( $theme ) )
		$theme = new BBP_Theme_Compat( $theme );

	// Bail if $theme isn't a proper object
	if ( ! is_a( $theme, 'BBP_Theme_Compat' ) )
		return;

	// Load up bbPress
	$bbp = bbpress();

	// Only override if the flag is set and not previously registered
	if ( empty( $bbp->theme_compat->packages[$theme->id] ) || ( true === $override ) ) {
		$bbp->theme_compat->packages[$theme->id] = $theme;
	}
}
/**
 * This fun little function fills up some WordPress globals with dummy data to
 * stop your average page template from complaining about it missing.
 *
 * @since bbPress (r3108)
 * @global WP_Query $wp_query
 * @global object $post
 * @param array $args
 */
function bbp_theme_compat_reset_post( $args = array() ) {
	global $wp_query, $post;

	// Default arguments
	$defaults = array(
		'ID'                    => -9999,
		'post_status'           => bbp_get_public_status_id(),
		'post_author'           => 0,
		'post_parent'           => 0,
		'post_type'             => 'page',
		'post_date'             => 0,
		'post_date_gmt'         => 0,
		'post_modified'         => 0,
		'post_modified_gmt'     => 0,
		'post_content'          => '',
		'post_title'            => '',
		'post_excerpt'          => '',
		'post_content_filtered' => '',
		'post_mime_type'        => '',
		'post_password'         => '',
		'post_name'             => '',
		'guid'                  => '',
		'menu_order'            => 0,
		'pinged'                => '',
		'to_ping'               => '',
		'ping_status'           => '',
		'comment_status'        => 'closed',
		'comment_count'         => 0,

		'is_404'          => false,
		'is_page'         => false,
		'is_single'       => false,
		'is_archive'      => false,
		'is_tax'          => false,
	);

	// Switch defaults if post is set
	if ( isset( $wp_query->post ) ) {		  
		$defaults = array(
			'ID'                    => $wp_query->post->ID,
			'post_status'           => $wp_query->post->post_status,
			'post_author'           => $wp_query->post->post_author,
			'post_parent'           => $wp_query->post->post_parent,
			'post_type'             => $wp_query->post->post_type,
			'post_date'             => $wp_query->post->post_date,
			'post_date_gmt'         => $wp_query->post->post_date_gmt,
			'post_modified'         => $wp_query->post->post_modified,
			'post_modified_gmt'     => $wp_query->post->post_modified_gmt,
			'post_content'          => $wp_query->post->post_content,
			'post_title'            => $wp_query->post->post_title,
			'post_excerpt'          => $wp_query->post->post_excerpt,
			'post_content_filtered' => $wp_query->post->post_content_filtered,
			'post_mime_type'        => $wp_query->post->post_mime_type,
			'post_password'         => $wp_query->post->post_password,
			'post_name'             => $wp_query->post->post_name,
			'guid'                  => $wp_query->post->guid,
			'menu_order'            => $wp_query->post->menu_order,
			'pinged'                => $wp_query->post->pinged,
			'to_ping'               => $wp_query->post->to_ping,
			'ping_status'           => $wp_query->post->ping_status,
			'comment_status'        => $wp_query->post->comment_status,
			'comment_count'         => $wp_query->post->comment_count,

			'is_404'          => false,
			'is_page'         => false,
			'is_single'       => false,
			'is_archive'      => false,
			'is_tax'          => false,
		);
	}
	$dummy = bbp_parse_args( $args, $defaults, 'theme_compat_reset_post' );

	// Clear out the post related globals
	unset( $wp_query->posts );
	unset( $wp_query->post  );
	unset( $post            );

	// Setup the dummy post object
	$wp_query->post                        = new stdClass; 
	$wp_query->post->ID                    = $dummy['ID'];
	$wp_query->post->post_status           = $dummy['post_status'];
	$wp_query->post->post_author           = $dummy['post_author'];
	$wp_query->post->post_parent           = $dummy['post_parent'];
	$wp_query->post->post_type             = $dummy['post_type'];
	$wp_query->post->post_date             = $dummy['post_date'];
	$wp_query->post->post_date_gmt         = $dummy['post_date_gmt'];
	$wp_query->post->post_modified         = $dummy['post_modified'];
	$wp_query->post->post_modified_gmt     = $dummy['post_modified_gmt'];
	$wp_query->post->post_content          = $dummy['post_content'];
	$wp_query->post->post_title            = $dummy['post_title'];
	$wp_query->post->post_excerpt          = $dummy['post_excerpt'];
	$wp_query->post->post_content_filtered = $dummy['post_content_filtered'];
	$wp_query->post->post_mime_type        = $dummy['post_mime_type'];
	$wp_query->post->post_password         = $dummy['post_password'];
	$wp_query->post->post_name             = $dummy['post_name'];
	$wp_query->post->guid                  = $dummy['guid'];
	$wp_query->post->menu_order            = $dummy['menu_order'];
	$wp_query->post->pinged                = $dummy['pinged'];
	$wp_query->post->to_ping               = $dummy['to_ping'];
	$wp_query->post->ping_status           = $dummy['ping_status'];
	$wp_query->post->comment_status        = $dummy['comment_status'];
	$wp_query->post->comment_count         = $dummy['comment_count'];

	// Set the $post global
	$post = $wp_query->post;

	// Setup the dummy post loop
	$wp_query->posts[0] = $wp_query->post;

	// Prevent comments form from appearing
	$wp_query->post_count = 1;
	$wp_query->is_404     = $dummy['is_404'];
	$wp_query->is_page    = $dummy['is_page'];
	$wp_query->is_single  = $dummy['is_single'];
	$wp_query->is_archive = $dummy['is_archive'];
	$wp_query->is_tax     = $dummy['is_tax'];

	/**
	 * Force the header back to 200 status if not a deliberate 404
	 *
	 * @see http://bbpress.trac.wordpress.org/ticket/1973
	 */
	if ( ! $wp_query->is_404() )
		status_header( 200 );

	// If we are resetting a post, we are in theme compat
	bbp_set_theme_compat_active();
}

/**
 * Reset main query vars and filter 'the_content' to output a bbPress
 * template part as needed.
 *
 * @since bbPress (r3032)
 * @param string $template
 * @uses bbp_is_single_user() To check if page is single user
 * @uses bbp_get_single_user_template() To get user template
 * @uses bbp_is_single_user_edit() To check if page is single user edit
 * @uses bbp_get_single_user_edit_template() To get user edit template
 * @uses bbp_is_single_view() To check if page is single view
 * @uses bbp_get_single_view_template() To get view template
 * @uses bbp_is_forum_edit() To check if page is forum edit
 * @uses bbp_get_forum_edit_template() To get forum edit template
 * @uses bbp_is_topic_merge() To check if page is topic merge
 * @uses bbp_get_topic_merge_template() To get topic merge template
 * @uses bbp_is_topic_split() To check if page is topic split
 * @uses bbp_get_topic_split_template() To get topic split template
 * @uses bbp_is_topic_edit() To check if page is topic edit
 * @uses bbp_get_topic_edit_template() To get topic edit template
 * @uses bbp_is_reply_edit() To check if page is reply edit
 * @uses bbp_get_reply_edit_template() To get reply edit template
 * @uses bbp_set_theme_compat_template() To set the global theme compat template
 */
function bbp_template_include_theme_compat( $template = '' ) {

	// Bail if the template already matches a bbPress template. This includes
	// archive-* and single-* WordPress post_type matches (allowing
	// themes to use the expected format) as well as all bbPress-specific
	// template files for users, topics, forums, etc...
	if ( !empty( bbpress()->theme_compat->bbpress_template ) )
		return $template;

	/** Users *************************************************************/

	if ( bbp_is_single_user_edit() || bbp_is_single_user() ) {

		// Reset post
		bbp_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => '',
			'post_title'     => esc_attr( bbp_get_displayed_user_field( 'display_name' ) ),
			'post_status'    => bbp_get_public_status_id(),
			'is_archive'     => false,
			'comment_status' => 'closed'
		) );

	/** Forums ************************************************************/

	// Forum archive
	} elseif ( bbp_is_forum_archive() ) {

		// Reset post
		bbp_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_title'     => bbp_get_forum_archive_title(),
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => bbp_get_forum_post_type(),
			'post_status'    => bbp_get_public_status_id(),
			'is_archive'     => true,
			'comment_status' => 'closed'
		) );

	// Single Forum
	} elseif ( bbp_is_forum_edit() || bbp_is_single_forum() ) {

		// Reset post
		bbp_theme_compat_reset_post( array(
			'ID'             => bbp_get_forum_id(),
			'post_title'     => bbp_get_forum_title(),
			'post_author'    => bbp_get_forum_author_id(),
			'post_date'      => 0,
			'post_content'   => get_post_field( 'post_content', bbp_get_forum_id() ),
			'post_type'      => bbp_get_forum_post_type(),
			'post_status'    => bbp_get_forum_visibility(),
			'is_single'      => true,
			'comment_status' => 'closed'
		) );

	/** Topics ************************************************************/

	// Topic archive
	} elseif ( bbp_is_topic_archive() ) {

		// Reset post
		bbp_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_title'     => bbp_get_topic_archive_title(),
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => bbp_get_topic_post_type(),
			'post_status'    => bbp_get_public_status_id(),
			'is_archive'     => true,
			'comment_status' => 'closed'
		) );

	// Single Topic
	} elseif ( bbp_is_topic_edit() || bbp_is_single_topic() ) {

		// Reset post
		bbp_theme_compat_reset_post( array(
			'ID'             => bbp_get_topic_id(),
			'post_title'     => bbp_get_topic_title(),
			'post_author'    => bbp_get_topic_author_id(),
			'post_date'      => 0,
			'post_content'   => get_post_field( 'post_content', bbp_get_topic_id() ),
			'post_type'      => bbp_get_topic_post_type(),
			'post_status'    => bbp_get_topic_status(),
			'is_single'      => true,
			'comment_status' => 'closed'
		) );

	/** Replies ***********************************************************/

	// Reply archive
	} elseif ( is_post_type_archive( bbp_get_reply_post_type() ) ) {

		// Reset post
		bbp_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_title'     => __( 'Replies', 'bbpress' ),
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => bbp_get_reply_post_type(),
			'post_status'    => bbp_get_public_status_id(),
			'comment_status' => 'closed'
		) );

	// Single Reply
	} elseif ( bbp_is_reply_edit() || bbp_is_single_reply() ) {

		// Reset post
		bbp_theme_compat_reset_post( array(
			'ID'             => bbp_get_reply_id(),
			'post_title'     => bbp_get_reply_title(),
			'post_author'    => bbp_get_reply_author_id(),
			'post_date'      => 0,
			'post_content'   => get_post_field( 'post_content', bbp_get_reply_id() ),
			'post_type'      => bbp_get_reply_post_type(),
			'post_status'    => bbp_get_reply_status(),
			'comment_status' => 'closed'
		) );

	/** Views *************************************************************/

	} elseif ( bbp_is_single_view() ) {

		// Reset post
		bbp_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_title'     => bbp_get_view_title(),
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => '',
			'post_status'    => bbp_get_public_status_id(),
			'comment_status' => 'closed'
		) );

	/** Topic Tags ********************************************************/

	// Topic Tag Edit
	} elseif ( bbp_is_topic_tag_edit() || bbp_is_topic_tag() ) {

		// Stash the current term in a new var
		set_query_var( 'bbp_topic_tag', get_query_var( 'term' ) );

		// Reset the post with our new title
		bbp_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => '',
			'post_title'     => sprintf( __( 'Topic Tag: %s', 'bbpress' ), '<span>' . bbp_get_topic_tag_name() . '</span>' ),
			'post_status'    => bbp_get_public_status_id(),
			'comment_status' => 'closed'
		) );
	}

	/**
	 * If we are relying on bbPress's built in theme compatibility to load
	 * the proper content, we need to intercept the_content, replace the
	 * output, and display ours instead.
	 *
	 * To do this, we first remove all filters from 'the_content' and hook
	 * our own function into it, which runs a series of checks to determine
	 * the context, and then uses the built in shortcodes to output the
	 * correct results from inside an output buffer.
	 *
	 * Uses bbp_get_theme_compat_templates() to provide fall-backs that
	 * should be coded without superfluous mark-up and logic (prev/next
	 * navigation, comments, date/time, etc...)
	 * 
	 * Hook into the 'bbp_get_bbpress_template' to override the array of
	 * possible templates, or 'bbp_bbpress_template' to override the result.
	 */
	if ( bbp_is_theme_compat_active() ) {

		// Remove all filters from the_content
		bbp_remove_all_filters( 'the_content' );

		// Add a filter on the_content late, which we will later remove
		add_filter( 'the_content', 'bbp_replace_the_content' );

		// Find the appropriate template file
		$template = bbp_get_theme_compat_templates();
	}

	return apply_filters( 'bbp_template_include_theme_compat', $template );
}

/**
 * Replaces the_content() if the post_type being displayed is one that would
 * normally be handled by bbPress, but proper single page templates do not
 * exist in the currently active theme.
 *
 * Note that we do *not* currently use is_main_query() here. This is because so
 * many existing themes either use query_posts() or fail to use wp_reset_query()
 * when running queries before the main loop, causing theme compat to fail.
 *
 * @since bbPress (r3034)
 * @param string $content
 * @return type
 */
function bbp_replace_the_content( $content = '' ) {

	// Bail if not inside the query loop
	if ( ! in_the_loop() )
		return $content;

	$bbp = bbpress();

	// Define local variable(s)
	$new_content = '';

	// Bail if shortcodes are unset somehow
	if ( !is_a( $bbp->shortcodes, 'BBP_Shortcodes' ) )
		return $content;

	// Use shortcode API to display forums/topics/replies because they are
	// already output buffered and ready to fit inside the_content

	/** Users *************************************************************/

	// Profile View
	if ( bbp_is_single_user_edit() || bbp_is_single_user() ) {
		ob_start();

		bbp_get_template_part( 'content', 'single-user' );

		$new_content = ob_get_contents();

		ob_end_clean();

	/** Forums ************************************************************/

	// Forum archive
	} elseif ( bbp_is_forum_archive() ) {

		// Page exists where this archive should be
		$page = bbp_get_page_by_path( bbp_get_root_slug() );
		if ( !empty( $page ) ) {

			// Restore previously unset filters
			bbp_restore_all_filters( 'the_content' );

			// Remove 'bbp_replace_the_content' filter to prevent infinite loops
			remove_filter( 'the_content', 'bbp_replace_the_content' );

			// Start output buffer
			ob_start();

			// Grab the content of this page
			$new_content = apply_filters( 'the_content', $page->post_content );

			// Clean up the buffer
			ob_end_clean();

			// Add 'bbp_replace_the_content' filter back (@see $this::start())
			add_filter( 'the_content', 'bbp_replace_the_content' );

		// No page so show the archive
		} else {
			$new_content = $bbp->shortcodes->display_forum_index();
		}

	// Forum Edit
	} elseif ( bbp_is_forum_edit() ) {
		$new_content = $bbp->shortcodes->display_forum_form();

	// Single Forum
	} elseif ( bbp_is_single_forum() ) {
		$new_content = $bbp->shortcodes->display_forum( array( 'id' => get_the_ID() ) );

	/** Topics ************************************************************/

	// Topic archive
	} elseif ( bbp_is_topic_archive() ) {

		// Page exists where this archive should be
		$page = bbp_get_page_by_path( bbp_get_topic_archive_slug() );
		if ( !empty( $page ) ) {

			// Restore previously unset filters
			bbp_restore_all_filters( 'the_content' );

			// Remove 'bbp_replace_the_content' filter to prevent infinite loops
			remove_filter( 'the_content', 'bbp_replace_the_content' );

			// Start output buffer
			ob_start();

			// Grab the content of this page
			$new_content = apply_filters( 'the_content', $page->post_content );

			// Clean up the buffer
			ob_end_clean();

			// Add 'bbp_replace_the_content' filter back (@see $this::start())
			add_filter( 'the_content', 'bbp_replace_the_content' );

		// No page so show the archive
		} else {
			$new_content = $bbp->shortcodes->display_topic_index();
		}

	// Topic Edit
	} elseif ( bbp_is_topic_edit() ) {

		// Split
		if ( bbp_is_topic_split() ) {
			ob_start();

			bbp_get_template_part( 'form', 'topic-split' );

			$new_content = ob_get_contents();

			ob_end_clean();

		// Merge
		} elseif ( bbp_is_topic_merge() ) {
			ob_start();

			bbp_get_template_part( 'form', 'topic-merge' );

			$new_content = ob_get_contents();

			ob_end_clean();

		// Edit
		} else {
			$new_content = $bbp->shortcodes->display_topic_form();
		}

	// Single Topic
	} elseif ( bbp_is_single_topic() ) {
		$new_content = $bbp->shortcodes->display_topic( array( 'id' => get_the_ID() ) );

	/** Replies ***********************************************************/

	// Reply archive
	} elseif ( is_post_type_archive( bbp_get_reply_post_type() ) ) {
		//$new_content = $bbp->shortcodes->display_reply_index();

	// Reply Edit
	} elseif ( bbp_is_reply_edit() ) {
		$new_content = $bbp->shortcodes->display_reply_form();

	// Single Reply
	} elseif ( bbp_is_single_reply() ) {
		$new_content = $bbp->shortcodes->display_reply( array( 'id' => get_the_ID() ) );

	/** Views *************************************************************/

	} elseif ( bbp_is_single_view() ) {
		$new_content = $bbp->shortcodes->display_view( array( 'id' => get_query_var( 'bbp_view' ) ) );

	/** Topic Tags ********************************************************/

	// Show topics of tag
	} elseif ( bbp_is_topic_tag() ) {
		$new_content = $bbp->shortcodes->display_topics_of_tag( array( 'id' => bbp_get_topic_tag_id() ) );

	// Edit topic tag
	} elseif ( bbp_is_topic_tag_edit() ) {
		$new_content = $bbp->shortcodes->display_topic_tag_form();
	}

	// Juggle the content around and try to prevent unsightly comments
	if ( !empty( $new_content ) && ( $new_content != $content ) ) {

		// Set the content to be the new content
		$content = apply_filters( 'bbp_replace_the_content', $new_content, $content );

		// Clean up after ourselves
		unset( $new_content );

		// Reset the $post global
		wp_reset_postdata();
	}

	// Return possibly hi-jacked content
	return $content;
}

/** Helpers *******************************************************************/

/**
 * Remove the canonical redirect to allow pretty pagination
 *
 * @since bbPress (r2628)
 * @param string $redirect_url Redirect url
 * @uses WP_Rewrite::using_permalinks() To check if the blog is using permalinks
 * @uses bbp_get_paged() To get the current page number
 * @uses bbp_is_single_topic() To check if it's a topic page
 * @uses bbp_is_single_forum() To check if it's a forum page
 * @return bool|string False if it's a topic/forum and their first page,
 *                      otherwise the redirect url
 */
function bbp_redirect_canonical( $redirect_url ) {
	global $wp_rewrite;

	// Canonical is for the beautiful
	if ( $wp_rewrite->using_permalinks() ) {

		// If viewing beyond page 1 of several
		if ( 1 < bbp_get_paged() ) {

			// Only on single topics...
			if ( bbp_is_single_topic() ) {
				$redirect_url = false;

			// ...and single forums...
			} elseif ( bbp_is_single_forum() ) {
				$redirect_url = false;

			// ...and single replies...
			} elseif ( bbp_is_single_reply() ) {
				$redirect_url = false;

			// ...and any single anything else...
			//
			// @todo - Find a more accurate way to disable paged canonicals for
			//          paged shortcode usage within other posts.
			} elseif ( is_page() || is_singular() ) {
				$redirect_url = false;
			}

		// If editing a topic
		} elseif ( bbp_is_topic_edit() ) {
			$redirect_url = false;

		// If editing a reply
		} elseif ( bbp_is_reply_edit() ) {
			$redirect_url = false;
		}
	}

	return $redirect_url;
}

/** Filters *******************************************************************/

/**
 * Removes all filters from a WordPress filter, and stashes them in the $bbp
 * global in the event they need to be restored later.
 *
 * @since bbPress (r3251)
 * @global WP_filter $wp_filter
 * @global array $merged_filters
 * @param string $tag
 * @param int $priority
 * @return bool
 */
function bbp_remove_all_filters( $tag, $priority = false ) {
	global $wp_filter, $merged_filters;

	$bbp = bbpress();

	// Filters exist
	if ( isset( $wp_filter[$tag] ) ) {

		// Filters exist in this priority
		if ( !empty( $priority ) && isset( $wp_filter[$tag][$priority] ) ) {

			// Store filters in a backup
			$bbp->filters->wp_filter[$tag][$priority] = $wp_filter[$tag][$priority];

			// Unset the filters
			unset( $wp_filter[$tag][$priority] );

		// Priority is empty
		} else {

			// Store filters in a backup
			$bbp->filters->wp_filter[$tag] = $wp_filter[$tag];

			// Unset the filters
			unset( $wp_filter[$tag] );
		}
	}

	// Check merged filters
	if ( isset( $merged_filters[$tag] ) ) {

		// Store filters in a backup
		$bbp->filters->merged_filters[$tag] = $merged_filters[$tag];

		// Unset the filters
		unset( $merged_filters[$tag] );
	}

	return true;
}

/**
 * Restores filters from the $bbp global that were removed using
 * bbp_remove_all_filters()
 *
 * @since bbPress (r3251)
 * @global WP_filter $wp_filter
 * @global array $merged_filters
 * @param string $tag
 * @param int $priority
 * @return bool
 */
function bbp_restore_all_filters( $tag, $priority = false ) {
	global $wp_filter, $merged_filters;

	$bbp = bbpress();

	// Filters exist
	if ( isset( $bbp->filters->wp_filter[$tag] ) ) {

		// Filters exist in this priority
		if ( !empty( $priority ) && isset( $bbp->filters->wp_filter[$tag][$priority] ) ) {

			// Store filters in a backup
			$wp_filter[$tag][$priority] = $bbp->filters->wp_filter[$tag][$priority];

			// Unset the filters
			unset( $bbp->filters->wp_filter[$tag][$priority] );

		// Priority is empty
		} else {

			// Store filters in a backup
			$wp_filter[$tag] = $bbp->filters->wp_filter[$tag];

			// Unset the filters
			unset( $bbp->filters->wp_filter[$tag] );
		}
	}

	// Check merged filters
	if ( isset( $bbp->filters->merged_filters[$tag] ) ) {

		// Store filters in a backup
		$merged_filters[$tag] = $bbp->filters->merged_filters[$tag];

		// Unset the filters
		unset( $bbp->filters->merged_filters[$tag] );
	}

	return true;
}

/**
 * Force comments_status to 'closed' for bbPress post types
 *
 * @since bbPress (r3589)
 * @param bool $open True if open, false if closed
 * @param int $post_id ID of the post to check
 * @return bool True if open, false if closed
 */
function bbp_force_comment_status( $open, $post_id = 0 ) {

	// Get the post type of the post ID
	$post_type = get_post_type( $post_id );

	// Default return value is what is passed in $open
	$retval = $open;

	// Only force for bbPress post types
	switch ( $post_type ) {
		case bbp_get_forum_post_type() :
		case bbp_get_topic_post_type() :
		case bbp_get_reply_post_type() :
			$retval = false;
			break;
	}

	// Allow override of the override
	return apply_filters( 'bbp_force_comment_status', $retval, $open, $post_id, $post_type );
}
