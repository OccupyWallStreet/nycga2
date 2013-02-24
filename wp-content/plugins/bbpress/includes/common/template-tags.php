<?php

/**
 * bbPress Common Template Tags
 *
 * Common template tags are ones that are used by more than one component, like
 * forums, topics, replies, users, topic tags, etc...
 *
 * @package bbPress
 * @subpackage TemplateTags
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** URLs **********************************************************************/

/**
 * Ouput the forum URL
 * 
 * @since bbPress (r3979)
 *
 * @uses bbp_get_forums_url() To get the forums URL
 * @param string $path Additional path with leading slash
 */
function bbp_forums_url( $path = '/' ) {
	echo bbp_get_forums_url( $path );
}
	/**
	 * Return the forum URL
	 * 
	 * @since bbPress (r3979)
	 *
	 * @uses home_url() To get the home URL
	 * @uses bbp_get_root_slug() To get the forum root location
	 * @param string $path Additional path with leading slash
	 */
	function bbp_get_forums_url( $path = '/' ) {
		return home_url( bbp_get_root_slug() . $path );
	}

/**
 * Ouput the forum URL
 *
 * @since bbPress (r3979)
 *
 * @uses bbp_get_topics_url() To get the topics URL
 * @param string $path Additional path with leading slash
 */
function bbp_topics_url( $path = '/' ) {
	echo bbp_get_topics_url( $path );
}
	/**
	 * Return the forum URL
	 *
	 * @since bbPress (r3979)
	 *
	 * @uses home_url() To get the home URL
	 * @uses bbp_get_topic_archive_slug() To get the topics archive location
	 * @param string $path Additional path with leading slash
	 * @return The URL to the topics archive
	 */
	function bbp_get_topics_url( $path = '/' ) {
		return home_url( bbp_get_topic_archive_slug() . $path );
	}

/** Add-on Actions ************************************************************/

/**
 * Add our custom head action to wp_head
 *
 * @since bbPress (r2464)
 *
 * @uses do_action() Calls 'bbp_head'
*/
function bbp_head() {
	do_action( 'bbp_head' );
}

/**
 * Add our custom head action to wp_head
 *
 * @since bbPress (r2464)
 *
 * @uses do_action() Calls 'bbp_footer'
 */
function bbp_footer() {
	do_action( 'bbp_footer' );
}

/** is_ ***********************************************************************/

/**
 * Check if current site is public
 *
 * @since bbPress (r3398)
 *
 * @param int $site_id
 * @uses get_current_blog_id()
 * @uses get_blog_option()
 * @uses apply_filters()
 * @return bool True if site is public, false if private
 */
function bbp_is_site_public( $site_id = 0 ) {

	// Get the current site ID
	if ( empty( $site_id ) )
		$site_id = get_current_blog_id();

	// Get the site visibility setting
	$public = get_blog_option( $site_id, 'blog_public', 1 );

	return (bool) apply_filters( 'bbp_is_site_public', $public, $site_id );
}

/**
 * Check if current page is a bbPress forum
 *
 * @since bbPress (r2549)
 *
 * @param int $post_id Possible post_id to check
 * @uses bbp_get_forum_post_type() To get the forum post type
 * @return bool True if it's a forum page, false if not
 */
function bbp_is_forum( $post_id = 0 ) {

	// Assume false
	$retval = false;

	// Supplied ID is a forum
	if ( !empty( $post_id ) && ( bbp_get_forum_post_type() == get_post_type( $post_id ) ))
		$retval = true;

	return (bool) apply_filters( 'bbp_is_forum', $retval, $post_id );
}

/**
 * Check if we are viewing a forum archive.
 *
 * @since bbPress (r3251)
 *
 * @uses is_post_type_archive() To check if we are looking at the forum archive
 * @uses bbp_get_forum_post_type() To get the forum post type ID
 *
 * @return bool
 */
function bbp_is_forum_archive() {

	// Default to false
	$retval = false;

	// In forum archive
	if ( is_post_type_archive( bbp_get_forum_post_type() ) || bbp_is_query_name( 'bbp_forum_archive' ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_forum_archive', $retval );
}

/**
 * Viewing a single forum
 *
 * @since bbPress (r3338)
 *
 * @uses is_single()
 * @uses bbp_get_forum_post_type()
 * @uses get_post_type()
 * @uses apply_filters()
 *
 * @return bool
 */
function bbp_is_single_forum() {

	// Assume false
	$retval = false;

	// Edit is not a single forum
	if ( bbp_is_forum_edit() )
		return false;

	// Single and a match
	if ( is_singular( bbp_get_forum_post_type() ) || bbp_is_query_name( 'bbp_single_forum' ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_single_forum', $retval );
}

/**
 * Check if current page is a forum edit page
 *
 * @since bbPress (r3553)
 *
 * @uses WP_Query Checks if WP_Query::bbp_is_forum_edit is true
 * @return bool True if it's the forum edit page, false if not
 */
function bbp_is_forum_edit() {
	global $wp_query, $pagenow;

	// Assume false
	$retval = false;

	// Check query
	if ( !empty( $wp_query->bbp_is_forum_edit ) && ( $wp_query->bbp_is_forum_edit == true ) )
		$retval = true;

	// Editing in admin
	elseif ( is_admin() && ( 'post.php' == $pagenow ) && ( get_post_type() == bbp_get_forum_post_type() ) && ( !empty( $_GET['action'] ) && ( 'edit' == $_GET['action'] ) ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_forum_edit', $retval );
}

/**
 * Check if current page is a bbPress topic
 *
 * @since bbPress (r2549)
 *
 * @param int $post_id Possible post_id to check
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses get_post_type() To get the post type of the post id
 * @return bool True if it's a topic page, false if not
 */
function bbp_is_topic( $post_id = 0 ) {

	// Assume false
	$retval = false;

	// Supplied ID is a topic
	if ( !empty( $post_id ) && ( bbp_get_topic_post_type() == get_post_type( $post_id ) ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_topic', $retval, $post_id );
}

/**
 * Viewing a single topic
 *
 * @since bbPress (r3338)
 *
 * @uses is_single()
 * @uses bbp_get_topic_post_type()
 * @uses get_post_type()
 * @uses apply_filters()
 *
 * @return bool
 */
function bbp_is_single_topic() {

	// Assume false
	$retval = false;

	// Edit is not a single topic
	if ( bbp_is_topic_edit() )
		return false;

	// Single and a match
	if ( is_singular( bbp_get_topic_post_type() ) || bbp_is_query_name( 'bbp_single_topic' ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_single_topic', $retval );
}

/**
 * Check if we are viewing a topic archive.
 *
 * @since bbPress (r3251)
 *
 * @uses is_post_type_archive() To check if we are looking at the topic archive
 * @uses bbp_get_topic_post_type() To get the topic post type ID
 *
 * @return bool
 */
function bbp_is_topic_archive() {

	// Default to false
	$retval = false;

	// In topic archive
	if ( is_post_type_archive( bbp_get_topic_post_type() ) || bbp_is_query_name( 'bbp_topic_archive' ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_topic_archive', $retval );
}

/**
 * Check if current page is a topic edit page
 *
 * @since bbPress (r2753)
 *
 * @uses WP_Query Checks if WP_Query::bbp_is_topic_edit is true
 * @return bool True if it's the topic edit page, false if not
 */
function bbp_is_topic_edit() {
	global $wp_query, $pagenow;

	// Assume false
	$retval = false;

	// Check query
	if ( !empty( $wp_query->bbp_is_topic_edit ) && ( $wp_query->bbp_is_topic_edit == true ) )
		$retval = true;

	// Editing in admin
	elseif ( is_admin() && ( 'post.php' == $pagenow ) && ( get_post_type() == bbp_get_topic_post_type() ) && ( !empty( $_GET['action'] ) && ( 'edit' == $_GET['action'] ) ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_topic_edit', $retval );
}

/**
 * Check if current page is a topic merge page
 *
 * @since bbPress (r2756)
 *
 * @uses bbp_is_topic_edit() To check if it's a topic edit page
 * @return bool True if it's the topic merge page, false if not
 */
function bbp_is_topic_merge() {

	// Assume false
	$retval = false;

	// Check topic edit and GET params
	if ( bbp_is_topic_edit() && !empty( $_GET['action'] ) && ( 'merge' == $_GET['action'] ) )
		return true;

	return (bool) apply_filters( 'bbp_is_topic_merge', $retval );
}

/**
 * Check if current page is a topic split page
 *
 * @since bbPress (r2756)
 *
 * @uses bbp_is_topic_edit() To check if it's a topic edit page
 * @return bool True if it's the topic split page, false if not
 */
function bbp_is_topic_split() {

	// Assume false
	$retval = false;

	// Check topic edit and GET params
	if ( bbp_is_topic_edit() && !empty( $_GET['action'] ) && ( 'split' == $_GET['action'] ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_topic_split', $retval );
}

/**
 * Check if the current page is a topic tag
 *
 * @since bbPress (r3311)
 *
 * @return bool True if it's a topic tag, false if not
 */
function bbp_is_topic_tag() {

	// Bail if topic-tags are off
	if ( ! bbp_allow_topic_tags() )
		return false;

	// Return false if editing a topic tag
	if ( bbp_is_topic_tag_edit() )
		return false;

	// Assume false
	$retval = false;

	// Check tax and query vars
	if ( is_tax( bbp_get_topic_tag_tax_id() ) || !empty( bbpress()->topic_query->is_tax ) || get_query_var( 'bbp_topic_tag' ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_topic_tag', $retval );
}

/**
 * Check if the current page is editing a topic tag
 *
 * @since bbPress (r3346)
 *
 * @uses WP_Query Checks if WP_Query::bbp_is_topic_tag_edit is true
 * @return bool True if editing a topic tag, false if not
 */
function bbp_is_topic_tag_edit() {
	global $wp_query, $pagenow, $taxnow;

	// Bail if topic-tags are off
	if ( ! bbp_allow_topic_tags() )
		return false;

	// Assume false
	$retval = false;

	// Check query
	if ( !empty( $wp_query->bbp_is_topic_tag_edit ) && ( true == $wp_query->bbp_is_topic_tag_edit ) )
		$retval = true;

	// Editing in admin
	elseif ( is_admin() && ( 'edit-tags.php' == $pagenow ) && ( bbp_get_topic_tag_tax_id() == $taxnow ) && ( !empty( $_GET['action'] ) && ( 'edit' == $_GET['action'] ) ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_topic_tag_edit', $retval );
}

/**
 * Check if the current post type is one of bbPress's
 *
 * @since bbPress (r3311)
 *
 * @param mixed $the_post Optional. Post object or post ID.
 * @uses get_post_type()
 * @uses bbp_get_forum_post_type()
 * @uses bbp_get_topic_post_type()
 * @uses bbp_get_reply_post_type()
 *
 * @return bool
 */
function bbp_is_custom_post_type( $the_post = false ) {

	// Assume false
	$retval = false;

	// Viewing one of the bbPress post types
	if ( in_array( get_post_type( $the_post ), array(
		bbp_get_forum_post_type(),
		bbp_get_topic_post_type(),
		bbp_get_reply_post_type()
	) ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_custom_post_type', $retval, $the_post );
}

/**
 * Check if current page is a bbPress reply
 *
 * @since bbPress (r2549)
 *
 * @param int $post_id Possible post_id to check
 * @uses bbp_get_reply_post_type() To get the reply post type
 * @uses get_post_type() To get the post type of the post id
 * @return bool True if it's a reply page, false if not
 */
function bbp_is_reply( $post_id = 0 ) {

	// Assume false
	$retval = false;

	// Supplied ID is a reply
	if ( !empty( $post_id ) && ( bbp_get_reply_post_type() == get_post_type( $post_id ) ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_reply', $retval, $post_id );
}

/**
 * Check if current page is a reply edit page
 *
 * @since bbPress (r2753)
 *
 * @uses WP_Query Checks if WP_Query::bbp_is_reply_edit is true
 * @return bool True if it's the reply edit page, false if not
 */
function bbp_is_reply_edit() {
	global $wp_query, $pagenow;

	// Assume false
	$retval = false;

	// Check query
	if ( !empty( $wp_query->bbp_is_reply_edit ) && ( true == $wp_query->bbp_is_reply_edit ) )
		$retval = true;

	// Editing in admin
	elseif ( is_admin() && ( 'post.php' == $pagenow ) && ( get_post_type() == bbp_get_reply_post_type() ) && ( !empty( $_GET['action'] ) && ( 'edit' == $_GET['action'] ) ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_reply_edit', $retval );
}

/**
 * Viewing a single reply
 *
 * @since bbPress (r3344)
 *
 * @uses is_single()
 * @uses bbp_get_reply_post_type()
 * @uses get_post_type()
 * @uses apply_filters()
 *
 * @return bool
 */
function bbp_is_single_reply() {

	// Assume false
	$retval = false;

	// Edit is not a single reply
	if ( bbp_is_reply_edit() )
		return false;

	// Single and a match
	if ( is_singular( bbp_get_reply_post_type() ) || ( bbp_is_query_name( 'bbp_single_reply' ) ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_single_reply', $retval );
}

/**
 * Check if current page is a bbPress user's favorites page (profile page)
 *
 * @since bbPress (r2652)
 *
 * @return bool True if it's the favorites page, false if not
 */
function bbp_is_favorites() {
	global $wp_query;

	// Assume false
	$retval = false;

	// Check query
	if ( !empty( $wp_query->bbp_is_single_user_favs ) && ( true == $wp_query->bbp_is_single_user_favs ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_favorites', $retval );
}

/**
 * Check if current page is a bbPress user's subscriptions page (profile page)
 *
 * @since bbPress (r2652)
 *
 * @return bool True if it's the subscriptions page, false if not
 */
function bbp_is_subscriptions() {
	global $wp_query;

	// Assume false
	$retval = false;

	// Check query
	if ( !empty( $wp_query->bbp_is_single_user_subs ) && ( true == $wp_query->bbp_is_single_user_subs ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_subscriptions', $retval );
}

/**
 * Check if current page shows the topics created by a bbPress user (profile
 * page)
 *
 * @since bbPress (r2688)
 *
 * @uses bbp_is_query_name() To get the query name
 * @return bool True if it's the topics created page, false if not
 */
function bbp_is_topics_created() {
	global $wp_query;

	// Assume false
	$retval = false;

	// Check query
	if ( !empty( $wp_query->bbp_is_single_user_topics ) && ( true == $wp_query->bbp_is_single_user_topics ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_topics_created', $retval );
}

/**
 * Check if current page shows the topics created by a bbPress user (profile
 * page)
 *
 * @since bbPress (r4225)
 *
 * @uses bbp_is_query_name() To get the query name
 * @return bool True if it's the topics created page, false if not
 */
function bbp_is_replies_created() {
	global $wp_query;

	// Assume false
	$retval = false;

	// Check query
	if ( !empty( $wp_query->bbp_is_single_user_replies ) && ( true == $wp_query->bbp_is_single_user_replies ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_replies_created', $retval );
}

/**
 * Check if current page is the currently logged in users author page
 *
 * @since bbPress (r2688)
 * @uses bbp_is_single_user() Check query variable
 * @uses is_user_logged_in() Must be logged in to be home
 * @uses bbp_get_displayed_user_id()
 * @uses bbp_get_current_user_id()
 * @return bool True if it's the user's home, false if not
 */
function bbp_is_user_home() {
	global $wp_query;

	// Assume false
	$retval = false;

	// Check query
	if ( !empty( $wp_query->bbp_is_single_user_home ) && ( true == $wp_query->bbp_is_single_user_home ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_user_home', $retval );
}

/**
 * Check if current page is the currently logged in users author edit page
 *
 * @since bbPress (r3918)
 * @uses bbp_is_single_user_edit() Check query variable
 * @uses is_user_logged_in() Must be logged in to be home
 * @uses bbp_get_displayed_user_id()
 * @uses bbp_get_current_user_id()
 * @return bool True if it's the user's home, false if not
 */
function bbp_is_user_home_edit() {

	// Assume false
	$retval = false;

	if ( bbp_is_user_home() && bbp_is_single_user_edit() )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_user_home_edit', $retval );
}

/**
 * Check if current page is a user profile page
 *
 * @since bbPress (r2688)
 *
 * @uses WP_Query Checks if WP_Query::bbp_is_single_user is set to true
 * @return bool True if it's a user's profile page, false if not
 */
function bbp_is_single_user() {
	global $wp_query;

	// Assume false
	$retval = false;

	// Check query
	if ( !empty( $wp_query->bbp_is_single_user ) && ( true == $wp_query->bbp_is_single_user ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_single_user', $retval );
}

/**
 * Check if current page is a user profile edit page
 *
 * @since bbPress (r2688)
 *
 * @uses WP_Query Checks if WP_Query::bbp_is_single_user_edit is set to true
 * @return bool True if it's a user's profile edit page, false if not
 */
function bbp_is_single_user_edit() {
	global $wp_query;

	// Assume false
	$retval = false;

	// Check query
	if ( !empty( $wp_query->bbp_is_single_user_edit ) && ( true == $wp_query->bbp_is_single_user_edit ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_single_user_edit', $retval );
}

/**
 * Check if current page is a user profile page
 *
 * @since bbPress (r4225)
 *
 * @uses WP_Query Checks if WP_Query::bbp_is_single_user_profile is set to true
 * @return bool True if it's a user's profile page, false if not
 */
function bbp_is_single_user_profile() {
	global $wp_query;

	// Assume false
	$retval = false;

	// Check query
	if ( !empty( $wp_query->bbp_is_single_user_profile ) && ( true == $wp_query->bbp_is_single_user_profile ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_single_user_profile', $retval );
}

/**
 * Check if current page is a user topics created page
 *
 * @since bbPress (r4225)
 *
 * @uses WP_Query Checks if WP_Query::bbp_is_single_user_topics is set to true
 * @return bool True if it's a user's topics page, false if not
 */
function bbp_is_single_user_topics() {
	global $wp_query;

	// Assume false
	$retval = false;

	// Check query
	if ( !empty( $wp_query->bbp_is_single_user_topics ) && ( true == $wp_query->bbp_is_single_user_topics ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_single_user_topics', $retval );
}

/**
 * Check if current page is a user replies created page
 *
 * @since bbPress (r4225)
 *
 * @uses WP_Query Checks if WP_Query::bbp_is_single_user_replies is set to true
 * @return bool True if it's a user's replies page, false if not
 */
function bbp_is_single_user_replies() {
	global $wp_query;

	// Assume false
	$retval = false;

	// Check query
	if ( !empty( $wp_query->bbp_is_single_user_replies ) && ( true == $wp_query->bbp_is_single_user_replies ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_single_user_replies', $retval );
}

/**
 * Check if current page is a view page
 *
 * @since bbPress (r2789)
 *
 * @global WP_Query $wp_query To check if WP_Query::bbp_is_view is true 
 * @uses bbp_is_query_name() To get the query name
 * @return bool Is it a view page?
 */
function bbp_is_single_view() {
	global $wp_query;

	// Assume false
	$retval = false;

	// Check query
	if ( !empty( $wp_query->bbp_is_view ) && ( true == $wp_query->bbp_is_view ) )
		$retval = true;

	// Check query name
	if ( empty( $retval ) && bbp_is_query_name( 'bbp_single_view' ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_single_view', $retval );
}

/**
 * Check if current page is an edit page
 *
 * @since bbPress (r3585)
 *
 * @uses WP_Query Checks if WP_Query::bbp_is_edit is true
 * @return bool True if it's the edit page, false if not
 */
function bbp_is_edit() {
	global $wp_query;

	// Assume false
	$retval = false;

	// Check query
	if ( !empty( $wp_query->bbp_is_edit ) && ( $wp_query->bbp_is_edit == true ) )
		$retval = true;

	return (bool) apply_filters( 'bbp_is_edit', $retval );
}

/**
 * Use the above is_() functions to output a body class for each scenario
 *
 * @since bbPress (r2926)
 *
 * @param array $wp_classes
 * @param array $custom_classes
 * @uses bbp_is_single_forum()
 * @uses bbp_is_single_topic()
 * @uses bbp_is_topic_edit()
 * @uses bbp_is_topic_merge()
 * @uses bbp_is_topic_split()
 * @uses bbp_is_single_reply()
 * @uses bbp_is_reply_edit()
 * @uses bbp_is_reply_edit()
 * @uses bbp_is_single_view()
 * @uses bbp_is_single_user_edit()
 * @uses bbp_is_single_user()
 * @uses bbp_is_user_home()
 * @uses bbp_is_subscriptions()
 * @uses bbp_is_favorites()
 * @uses bbp_is_topics_created()
 * @uses bbp_is_forum_archive()
 * @uses bbp_is_topic_archive()
 * @uses bbp_is_topic_tag()
 * @uses bbp_is_topic_tag_edit()
 * @uses bbp_get_topic_tag_tax_id()
 * @uses bbp_get_topic_tag_slug()
 * @uses bbp_get_topic_tag_id()
 * @return array Body Classes
 */
function bbp_body_class( $wp_classes, $custom_classes = false ) {

	$bbp_classes = array();

	/** Archives **************************************************************/

	if ( bbp_is_forum_archive() )
		$bbp_classes[] = bbp_get_forum_post_type() . '-archive';

	if ( bbp_is_topic_archive() )
		$bbp_classes[] = bbp_get_topic_post_type() . '-archive';

	/** Topic Tags ************************************************************/

	if ( bbp_is_topic_tag() ) {
		$bbp_classes[] = bbp_get_topic_tag_tax_id();
		$bbp_classes[] = bbp_get_topic_tag_tax_id() . '-' . bbp_get_topic_tag_slug();
		$bbp_classes[] = bbp_get_topic_tag_tax_id() . '-' . bbp_get_topic_tag_id();
	}

	if ( bbp_is_topic_tag_edit() ) {
		$bbp_classes[] = bbp_get_topic_tag_tax_id() . '-edit';
		$bbp_classes[] = bbp_get_topic_tag_tax_id() . '-' . bbp_get_topic_tag_slug() . '-edit';
		$bbp_classes[] = bbp_get_topic_tag_tax_id() . '-' . bbp_get_topic_tag_id()   . '-edit';
	}

	/** Components ************************************************************/

	if ( bbp_is_single_forum() )
		$bbp_classes[] = bbp_get_forum_post_type();

	if ( bbp_is_single_topic() )
		$bbp_classes[] = bbp_get_topic_post_type();

	if ( bbp_is_single_reply() )
		$bbp_classes[] = bbp_get_reply_post_type();

	if ( bbp_is_topic_edit() )
		$bbp_classes[] = bbp_get_topic_post_type() . '-edit';

	if ( bbp_is_topic_merge() )
		$bbp_classes[] = bbp_get_topic_post_type() . '-merge';

	if ( bbp_is_topic_split() )
		$bbp_classes[] = bbp_get_topic_post_type() . '-split';

	if ( bbp_is_reply_edit() )
		$bbp_classes[] = bbp_get_reply_post_type() . '-edit';

	if ( bbp_is_single_view() )
		$bbp_classes[] = 'bbp-view';

	/** User ******************************************************************/

	if ( bbp_is_single_user_edit() ) {
		$bbp_classes[] = 'bbp-user-edit';
		$bbp_classes[] = 'single';
		$bbp_classes[] = 'singular';
	}

	if ( bbp_is_single_user() ) {
		$bbp_classes[] = 'bbp-user-page';
		$bbp_classes[] = 'single';
		$bbp_classes[] = 'singular';
	}

	if ( bbp_is_user_home() ) {
		$bbp_classes[] = 'bbp-user-home';
		$bbp_classes[] = 'single';
		$bbp_classes[] = 'singular';
	}

	if ( bbp_is_user_home_edit() ) {
		$bbp_classes[] = 'bbp-user-home-edit';
		$bbp_classes[] = 'single';
		$bbp_classes[] = 'singular';
	}

	if ( bbp_is_topics_created() ) {
		$bbp_classes[] = 'bbp-topics-created';
		$bbp_classes[] = 'single';
		$bbp_classes[] = 'singular';
	}

	if ( bbp_is_favorites() ) {
		$bbp_classes[] = 'bbp-favorites';
		$bbp_classes[] = 'single';
		$bbp_classes[] = 'singular';
	}

	if ( bbp_is_subscriptions() ) {
		$bbp_classes[] = 'bbp-subscriptions';
		$bbp_classes[] = 'single';
		$bbp_classes[] = 'singular';
	}

	/** Clean up **************************************************************/

	// Add bbPress class if we are within a bbPress page
	if ( !empty( $bbp_classes ) )
		$bbp_classes[] = 'bbPress';

	// Merge WP classes with bbPress classes and remove any duplicates
	$classes = array_unique( array_merge( (array) $bbp_classes, (array) $wp_classes ) );

	return apply_filters( 'bbp_get_the_body_class', $classes, $bbp_classes, $wp_classes, $custom_classes );
}

/**
 * Use the above is_() functions to return if in any bbPress page
 *
 * @since bbPress (r3344)
 *
 * @uses bbp_is_single_forum()
 * @uses bbp_is_single_topic()
 * @uses bbp_is_topic_edit()
 * @uses bbp_is_topic_merge()
 * @uses bbp_is_topic_split()
 * @uses bbp_is_single_reply()
 * @uses bbp_is_reply_edit()
 * @uses bbp_is_reply_edit()
 * @uses bbp_is_single_view()
 * @uses bbp_is_single_user_edit()
 * @uses bbp_is_single_user()
 * @uses bbp_is_user_home()
 * @uses bbp_is_subscriptions()
 * @uses bbp_is_favorites()
 * @uses bbp_is_topics_created()
 * @return bool In a bbPress page
 */
function is_bbpress() {

	// Defalt to false
	$retval = false;

	/** Archives **************************************************************/

	if ( bbp_is_forum_archive() )
		$retval = true;

	elseif ( bbp_is_topic_archive() )
		$retval = true;

	/** Topic Tags ************************************************************/

	elseif ( bbp_is_topic_tag() )
		$retval = true;

	elseif ( bbp_is_topic_tag_edit() )
		$retval = true;

	/** Components ************************************************************/

	elseif ( bbp_is_single_forum() )
		$retval = true;

	elseif ( bbp_is_single_topic() )
		$retval = true;

	elseif ( bbp_is_single_reply() )
		$retval = true;

	elseif ( bbp_is_topic_edit() )
		$retval = true;

	elseif ( bbp_is_topic_merge() )
		$retval = true;

	elseif ( bbp_is_topic_split() )
		$retval = true;

	elseif ( bbp_is_reply_edit() )
		$retval = true;

	elseif ( bbp_is_single_view() )
		$retval = true;

	/** User ******************************************************************/

	elseif ( bbp_is_single_user_edit() )
		$retval = true;

	elseif ( bbp_is_single_user() )
		$retval = true;

	elseif ( bbp_is_user_home() )
		$retval = true;

	elseif ( bbp_is_user_home_edit() )
		$retval = true;

	elseif ( bbp_is_topics_created() )
		$retval = true;

	elseif ( bbp_is_favorites() )
		$retval = true;

	elseif ( bbp_is_subscriptions() )
		$retval = true;

	/** Done ******************************************************************/

	return (bool) apply_filters( 'is_bbpress', $retval );
}

/** Forms *********************************************************************/

/**
 * Output the login form action url
 *
 * @since bbPress (r2815)
 *
 * @param string $url Pass a URL to redirect to
 * @uses add_query_arg() To add a arg to the url
 * @uses site_url() Toget the site url
 * @uses apply_filters() Calls 'bbp_wp_login_action' with the url and args
 */
function bbp_wp_login_action( $args = '' ) {
	$defaults = array (
		'action'  => '',
		'context' => ''
	);
	$r = bbp_parse_args( $args, $defaults, 'login_action' );
	extract( $r );

	if ( !empty( $action ) )
		$login_url = add_query_arg( array( 'action' => $action ), 'wp-login.php' );
	else
		$login_url = 'wp-login.php';

	$login_url = site_url( $login_url, $context );

	echo apply_filters( 'bbp_wp_login_action', $login_url, $args );
}

/**
 * Output hidden request URI field for user forms.
 *
 * The referer link is the current Request URI from the server super global. The
 * input name is '_wp_http_referer', in case you wanted to check manually.
 *
 * @since bbPress (r2815)
 *
 * @param string $url Pass a URL to redirect to
 * @uses wp_get_referer() To get the referer
 * @uses esc_attr() To escape the url
 * @uses apply_filters() Calls 'bbp_redirect_to_field' with the referer field
 *                        and url
 */
function bbp_redirect_to_field( $redirect_to = '' ) {

	// Rejig the $redirect_to
	if ( !isset( $_SERVER['REDIRECT_URL'] ) || ( !$redirect_to == home_url( $_SERVER['REDIRECT_URL'] ) ) )
		$redirect_to = wp_get_referer();

	// Make sure we are directing somewhere
	if ( empty( $redirect_to ) )
		$redirect_to = home_url( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '' );

	// Remove loggedout query arg if it's there
	$redirect_to    = (string) esc_attr( remove_query_arg( 'loggedout', $redirect_to ) );
	$redirect_field = '<input type="hidden" id="bbp_redirect_to" name="redirect_to" value="' . $redirect_to . '" />';

	echo apply_filters( 'bbp_redirect_to_field', $redirect_field, $redirect_to );
}

/**
 * Echo sanitized $_REQUEST value.
 *
 * Use the $input_type parameter to properly process the value. This
 * ensures correct sanitization of the value for the receiving input.
 *
 * @since bbPress (r2815)
 *
 * @param string $request Name of $_REQUEST to look for
 * @param string $input_type Type of input. Default: text. Accepts:
 *                            textarea|password|select|radio|checkbox
 * @uses bbp_get_sanitize_val() To sanitize the value.
 */
function bbp_sanitize_val( $request = '', $input_type = 'text' ) {
	echo bbp_get_sanitize_val( $request, $input_type );
}
	/**
	 * Return sanitized $_REQUEST value.
	 *
	 * Use the $input_type parameter to properly process the value. This
	 * ensures correct sanitization of the value for the receiving input.
	 *
	 * @since bbPress (r2815)
	 *
	 * @param string $request Name of $_REQUEST to look for
	 * @param string $input_type Type of input. Default: text. Accepts:
	 *                            textarea|password|select|radio|checkbox
	 * @uses esc_attr() To escape the string
	 * @uses apply_filters() Calls 'bbp_get_sanitize_val' with the sanitized
	 *                        value, request and input type
	 * @return string Sanitized value ready for screen display
	 */
	function bbp_get_sanitize_val( $request = '', $input_type = 'text' ) {

		// Check that requested
		if ( empty( $_REQUEST[$request] ) )
			return false;

		// Set request varaible
		$pre_ret_val = $_REQUEST[$request];

		// Treat different kinds of fields in different ways
		switch ( $input_type ) {
			case 'text'     :
			case 'textarea' :
				$retval = esc_attr( stripslashes( $pre_ret_val ) );
				break;

			case 'password' :
			case 'select'   :
			case 'radio'    :
			case 'checkbox' :
			default :
				$retval = esc_attr( $pre_ret_val );
				break;
		}

		return apply_filters( 'bbp_get_sanitize_val', $retval, $request, $input_type );
	}

/**
 * Output the current tab index of a given form
 *
 * Use this function to handle the tab indexing of user facing forms within a
 * template file. Calling this function will automatically increment the global
 * tab index by default.
 *
 * @since bbPress (r2810)
 *
 * @param int $auto_increment Optional. Default true. Set to false to prevent
 *                             increment
 */
function bbp_tab_index( $auto_increment = true ) {
	echo bbp_get_tab_index( $auto_increment );
}

	/**
	 * Output the current tab index of a given form
	 *
	 * Use this function to handle the tab indexing of user facing forms
	 * within a template file. Calling this function will automatically
	 * increment the global tab index by default.
	 *
	 * @since bbPress (r2810)
	 *
	 * @uses apply_filters Allows return value to be filtered
	 * @param int $auto_increment Optional. Default true. Set to false to
	 *                             prevent the increment
	 * @return int $bbp->tab_index The global tab index
	 */
	function bbp_get_tab_index( $auto_increment = true ) {
		$bbp = bbpress();

		if ( true === $auto_increment )
			++$bbp->tab_index;

		return apply_filters( 'bbp_get_tab_index', (int) $bbp->tab_index );
	}

/**
 * Output a select box allowing to pick which forum/topic a new topic/reply
 * belongs in.
 *
 * Can be used for any post type, but is mostly used for topics and forums.
 *
 * @since bbPress (r2746)
 *
 * @param mixed $args See {@link bbp_get_dropdown()} for arguments
 */
function bbp_dropdown( $args = '' ) {
	echo bbp_get_dropdown( $args );
}
	/**
	 * Output a select box allowing to pick which forum/topic a new
	 * topic/reply belongs in.
	 *
	 * @since bbPress (r2746)
	 *
	 * @param mixed $args The function supports these args:
	 *  - post_type: Post type, defaults to bbp_get_forum_post_type() (bbp_forum)
	 *  - selected: Selected ID, to not have any value as selected, pass
	 *               anything smaller than 0 (due to the nature of select
	 *               box, the first value would of course be selected -
	 *               though you can have that as none (pass 'show_none' arg))
	 *  - sort_column: Sort by? Defaults to 'menu_order, post_title'
	 *  - child_of: Child of. Defaults to 0
	 *  - post_status: Which all post_statuses to find in? Can be an array
	 *                  or CSV of publish, category, closed, private, spam,
	 *                  trash (based on post type) - if not set, these are
	 *                  automatically determined based on the post_type
	 *  - posts_per_page: Retrieve all forums/topics. Defaults to -1 to get
	 *                     all posts
	 *  - walker: Which walker to use? Defaults to
	 *             {@link BBP_Walker_Dropdown}
	 *  - select_id: ID of the select box. Defaults to 'bbp_forum_id'
	 *  - tab: Tabindex value. False or integer
	 *  - options_only: Show only <options>? No <select>?
	 *  - show_none: False or something like __( '(No Forum)', 'bbpress' ),
	 *                will have value=""
	 *  - none_found: False or something like
	 *                 __( 'No forums to post to!', 'bbpress' )
	 *  - disable_categories: Disable forum categories and closed forums?
	 *                         Defaults to true. Only for forums and when
	 *                         the category option is displayed.
	 * @uses BBP_Walker_Dropdown() As the default walker to generate the
	 *                              dropdown
	 * @uses current_user_can() To check if the current user can read
	 *                           private forums
	 * @uses bbp_get_forum_post_type() To get the forum post type
	 * @uses bbp_get_topic_post_type() To get the topic post type
	 * @uses walk_page_dropdown_tree() To generate the dropdown using the
	 *                                  walker
	 * @uses apply_filters() Calls 'bbp_get_dropdown' with the dropdown
	 *                        and args
	 * @return string The dropdown
	 */
	function bbp_get_dropdown( $args = '' ) {

		/** Arguments *********************************************************/

		$defaults = array (
			'post_type'          => bbp_get_forum_post_type(),
			'selected'           => 0,
			'sort_column'        => 'menu_order',
			'child_of'           => '0',
			'numberposts'        => -1,
			'orderby'            => 'menu_order',
			'order'              => 'ASC',
			'walker'             => '',

			// Output-related
			'select_id'          => 'bbp_forum_id',
			'tab'                => bbp_get_tab_index(),
			'options_only'       => false,
			'show_none'          => false,
			'none_found'         => false,
			'disable_categories' => true,
			'disabled'           => ''
		);
		$r = bbp_parse_args( $args, $defaults, 'get_dropdown' );

		if ( empty( $r['walker'] ) ) {
			$r['walker']            = new BBP_Walker_Dropdown();
			$r['walker']->tree_type = $r['post_type'];
		}

		// Force 0
		if ( is_numeric( $r['selected'] ) && $r['selected'] < 0 )
			$r['selected'] = 0;

		extract( $r );

		// Unset the args not needed for WP_Query to avoid any possible conflicts.
		// Note: walker and disable_categories are not unset
		unset( $r['select_id'], $r['tab'], $r['options_only'], $r['show_none'], $r['none_found'] );

		/** Post Status *******************************************************/

		// Define local variable(s)
		$post_stati = array();

		// Public
		$post_stati[] = bbp_get_public_status_id();

		// Forums
		if ( bbp_get_forum_post_type() == $post_type ) {

			// Private forums
			if ( current_user_can( 'read_private_forums' ) ) {
				$post_stati[] = bbp_get_private_status_id();
			}

			// Hidden forums
			if ( current_user_can( 'read_hidden_forums' ) ) {
				$post_stati[] = bbp_get_hidden_status_id();
			}
		}

		// Setup the post statuses
		$r['post_status'] = implode( ',', $post_stati );

		/** Setup variables ***************************************************/

		$name      = esc_attr( $select_id );
		$select_id = $name;
		$tab       = (int) $tab;
		$retval    = '';
		$posts     = get_posts( $r );
		$disabled  = disabled( isset( bbpress()->options[$disabled] ), true, false );

		/** Drop Down *********************************************************/

		// Items found
		if ( !empty( $posts ) ) {
			if ( empty( $options_only ) ) {
				$tab     = !empty( $tab ) ? ' tabindex="' . $tab . '"' : '';
				$retval .= '<select name="' . $name . '" id="' . $select_id . '"' . $tab  . $disabled . '>' . "\n";
			}

			$retval .= !empty( $show_none ) ? "\t<option value=\"\" class=\"level-0\">" . $show_none . '</option>' : '';
			$retval .= walk_page_dropdown_tree( $posts, 0, $r );

			if ( empty( $options_only ) )
				$retval .= '</select>';

		// No items found - Display feedback if no custom message was passed
		} elseif ( empty( $none_found ) ) {

			// Switch the response based on post type
			switch ( $post_type ) {

				// Topics
				case bbp_get_topic_post_type() :
					$retval = __( 'No topics available', 'bbpress' );
					break;

				// Forums
				case bbp_get_forum_post_type() :
					$retval = __( 'No forums available', 'bbpress' );
					break;

				// Any other
				default :
					$retval = __( 'None available', 'bbpress' );
					break;
			}
		}

		return apply_filters( 'bbp_get_dropdown', $retval, $args );
	}

/**
 * Output the required hidden fields when creating/editing a forum
 *
 * @since bbPress (r3553)
 *
 * @uses bbp_is_forum_edit() To check if it's the forum edit page
 * @uses wp_nonce_field() To generate hidden nonce fields
 * @uses bbp_forum_id() To output the forum id
 * @uses bbp_is_single_forum() To check if it's a forum page
 * @uses bbp_forum_id() To output the forum id
 */
function bbp_forum_form_fields() {

	if ( bbp_is_forum_edit() ) : ?>

		<input type="hidden" name="action"       id="bbp_post_action" value="bbp-edit-forum" />
		<input type="hidden" name="bbp_forum_id" id="bbp_forum_id"    value="<?php bbp_forum_id(); ?>" />

		<?php

		if ( current_user_can( 'unfiltered_html' ) )
			wp_nonce_field( 'bbp-unfiltered-html-forum_' . bbp_get_forum_id(), '_bbp_unfiltered_html_forum', false );

		?>

		<?php wp_nonce_field( 'bbp-edit-forum_' . bbp_get_forum_id() );

	else :

		if ( bbp_is_single_forum() ) : ?>

			<input type="hidden" name="bbp_forum_parent_id" id="bbp_forum_parent_id" value="<?php bbp_forum_parent_id(); ?>" />

		<?php endif; ?>

		<input type="hidden" name="action" id="bbp_post_action" value="bbp-new-forum" />

		<?php

		if ( current_user_can( 'unfiltered_html' ) )
			wp_nonce_field( 'bbp-unfiltered-html-forum_new', '_bbp_unfiltered_html_forum', false );

		?>

		<?php wp_nonce_field( 'bbp-new-forum' );

	endif;
}

/**
 * Output the required hidden fields when creating/editing a topic
 *
 * @since bbPress (r2753)
 *
 * @uses bbp_is_topic_edit() To check if it's the topic edit page
 * @uses wp_nonce_field() To generate hidden nonce fields
 * @uses bbp_topic_id() To output the topic id
 * @uses bbp_is_single_forum() To check if it's a forum page
 * @uses bbp_forum_id() To output the forum id
 */
function bbp_topic_form_fields() {

	if ( bbp_is_topic_edit() ) : ?>

		<input type="hidden" name="action"       id="bbp_post_action" value="bbp-edit-topic" />
		<input type="hidden" name="bbp_topic_id" id="bbp_topic_id"    value="<?php bbp_topic_id(); ?>" />

		<?php

		if ( current_user_can( 'unfiltered_html' ) )
			wp_nonce_field( 'bbp-unfiltered-html-topic_' . bbp_get_topic_id(), '_bbp_unfiltered_html_topic', false );

		?>

		<?php wp_nonce_field( 'bbp-edit-topic_' . bbp_get_topic_id() );

	else :

		if ( bbp_is_single_forum() ) : ?>

			<input type="hidden" name="bbp_forum_id" id="bbp_forum_id" value="<?php bbp_forum_id(); ?>" />

		<?php endif; ?>

		<input type="hidden" name="action" id="bbp_post_action" value="bbp-new-topic" />

		<?php if ( current_user_can( 'unfiltered_html' ) )
			wp_nonce_field( 'bbp-unfiltered-html-topic_new', '_bbp_unfiltered_html_topic', false ); ?>

		<?php wp_nonce_field( 'bbp-new-topic' );

	endif;
}

/**
 * Output the required hidden fields when creating/editing a reply
 *
 * @since bbPress (r2753)
 *
 * @uses bbp_is_reply_edit() To check if it's the reply edit page
 * @uses wp_nonce_field() To generate hidden nonce fields
 * @uses bbp_reply_id() To output the reply id
 * @uses bbp_topic_id() To output the topic id
 * @uses bbp_forum_id() To output the forum id
 */
function bbp_reply_form_fields() {

	if ( bbp_is_reply_edit() ) : ?>

		<input type="hidden" name="bbp_reply_title" id="bbp_reply_title" value="<?php bbp_reply_title(); ?>" />
		<input type="hidden" name="bbp_reply_id"    id="bbp_reply_id"    value="<?php bbp_reply_id(); ?>" />
		<input type="hidden" name="action"          id="bbp_post_action" value="bbp-edit-reply" />

		<?php if ( current_user_can( 'unfiltered_html' ) )
			wp_nonce_field( 'bbp-unfiltered-html-reply_' . bbp_get_reply_id(), '_bbp_unfiltered_html_reply', false ); ?>

		<?php wp_nonce_field( 'bbp-edit-reply_' . bbp_get_reply_id() );

	else : ?>

		<input type="hidden" name="bbp_reply_title" id="bbp_reply_title" value="<?php printf( __( 'Reply To: %s', 'bbpress' ), bbp_get_topic_title() ); ?>" />
		<input type="hidden" name="bbp_topic_id"    id="bbp_topic_id"    value="<?php bbp_topic_id(); ?>" />
		<input type="hidden" name="action"          id="bbp_post_action" value="bbp-new-reply" />

		<?php if ( current_user_can( 'unfiltered_html' ) )
			wp_nonce_field( 'bbp-unfiltered-html-reply_' . bbp_get_topic_id(), '_bbp_unfiltered_html_reply', false ); ?>

		<?php wp_nonce_field( 'bbp-new-reply' );

		// Show redirect field if not viewing a specific topic
		if ( bbp_is_query_name( 'bbp_single_topic' ) ) :
			bbp_redirect_to_field( get_permalink() );

		endif;
	endif;
}

/**
 * Output the required hidden fields when editing a user
 *
 * @since bbPress (r2690)
 *
 * @uses bbp_displayed_user_id() To output the displayed user id
 * @uses wp_nonce_field() To generate a hidden nonce field
 * @uses wp_referer_field() To generate a hidden referer field
 */
function bbp_edit_user_form_fields() {
?>

	<input type="hidden" name="action"  id="bbp_post_action" value="bbp-update-user" />
	<input type="hidden" name="user_id" id="user_id"         value="<?php bbp_displayed_user_id(); ?>" />

	<?php wp_nonce_field( 'update-user_' . bbp_get_displayed_user_id() );
}

/**
 * Merge topic form fields
 *
 * Output the required hidden fields when merging a topic
 *
 * @since bbPress (r2756)
 *
 * @uses wp_nonce_field() To generate a hidden nonce field
 * @uses bbp_topic_id() To output the topic id
 */
function bbp_merge_topic_form_fields() {
?>

	<input type="hidden" name="action"       id="bbp_post_action" value="bbp-merge-topic" />
	<input type="hidden" name="bbp_topic_id" id="bbp_topic_id"    value="<?php bbp_topic_id(); ?>" />

	<?php wp_nonce_field( 'bbp-merge-topic_' . bbp_get_topic_id() );
}

/**
 * Split topic form fields
 *
 * Output the required hidden fields when splitting a topic
 *
 * @since bbPress (r2756)
 *
 * @uses wp_nonce_field() To generete a hidden nonce field
 */
function bbp_split_topic_form_fields() {
?>

	<input type="hidden" name="action"       id="bbp_post_action" value="bbp-split-topic" />
	<input type="hidden" name="bbp_reply_id" id="bbp_reply_id"    value="<?php echo absint( $_GET['reply_id'] ); ?>" />

	<?php wp_nonce_field( 'bbp-split-topic_' . bbp_get_topic_id() );
}

/**
 * Output a textarea or TinyMCE if enabled
 *
 * @since bbPress (r3586)
 *
 * @param array $args
 * @uses bbp_get_the_content() To return the content to output
 */
function bbp_the_content( $args = array() ) {
	echo bbp_get_the_content( $args );
}
	/**
	 * Return a textarea or TinyMCE if enabled
	 *
	 * @since bbPress (r3586)
	 *
	 * @param array $args
	 *
	 * @uses apply_filter() To filter args and output
	 * @uses wp_parse_pargs() To compare args
	 * @uses bbp_use_wp_editor() To see if WP editor is in use
	 * @uses bbp_is_edit() To see if we are editing something
	 * @uses wp_editor() To output the WordPress editor
	 *
	 * @return string HTML from output buffer 
	 */
	function bbp_get_the_content( $args = array() ) {

		// Default arguments
		$defaults = array(
			'context'       => 'topic',
			'before'        => '<div class="bbp-the-content-wrapper">',
			'after'         => '</div>',
			'wpautop'       => true,
			'media_buttons' => false,
			'textarea_rows' => '12',
			'tabindex'      => bbp_get_tab_index(),
			'editor_class'  => 'bbp-the-content',
			'tinymce'       => true,
			'teeny'         => true,
			'quicktags'     => true
		);
		$r = bbp_parse_args( $args, $defaults, 'get_the_content' );
		extract( $r );

		// Assume we are not editing
		$post_content = '';

		// Start an output buffor
		ob_start();

		// Output something before the editor
		if ( !empty( $before ) )
			echo $before;

		// Get sanitized content
		if ( bbp_is_edit() )
			$post_content = call_user_func( 'bbp_get_form_' . $context . '_content' );

		// Use TinyMCE if available
		if ( bbp_use_wp_editor() ) :
			wp_editor( htmlspecialchars_decode( $post_content, ENT_QUOTES ), 'bbp_' . $context . '_content', array(
				'wpautop'       => $wpautop,
				'media_buttons' => $media_buttons,
				'textarea_rows' => $textarea_rows,
				'tabindex'      => $tabindex,
				'editor_class'  => $editor_class,
				'tinymce'       => $tinymce,
				'teeny'         => $teeny,
				'quicktags'     => $quicktags
			) );

		/**
		 * Fallback to normal textarea.
		 *
		 * Note that we do not use esc_textarea() here to prevent double
		 * escaping the editable output, mucking up existing content.
		 */
		else : ?>

			<textarea id="bbp_<?php echo esc_attr( $context ); ?>_content" class="<?php echo esc_attr( $editor_class ); ?>" name="bbp_<?php echo esc_attr( $context ); ?>_content" cols="60" rows="<?php echo esc_attr( $textarea_rows ); ?>" tabindex="<?php echo esc_attr( $tabindex ); ?>"><?php echo $post_content; ?></textarea>

		<?php endif;

		// Output something after the editor
		if ( !empty( $after ) )
			echo $after;

		// Put the output into a usable variable
		$output = ob_get_contents();

		// Flush the output buffer
		ob_end_clean();

		return apply_filters( 'bbp_get_the_content', $output, $args, $post_content );
	}

/** Views *********************************************************************/

/**
 * Output the view id
 *
 * @since bbPress (r2789)
 *
 * @param string $view Optional. View id
 * @uses bbp_get_view_id() To get the view id
 */
function bbp_view_id( $view = '' ) {
	echo bbp_get_view_id( $view );
}

	/**
	 * Get the view id
	 *
	 * If a view id is supplied, that is used. Otherwise the 'bbp_view'
	 * query var is checked for.
	 *
	 * @since bbPress (r2789)
	 *
	 * @param string $view Optional. View id.
	 * @uses sanitize_title() To sanitize the view id
	 * @uses get_query_var() To get the view id from query var 'bbp_view'
	 * @return bool|string ID on success, false on failure
	 */
	function bbp_get_view_id( $view = '' ) {
		$bbp = bbpress();

		$view = !empty( $view ) ? sanitize_title( $view ) : get_query_var( 'bbp_view' );

		if ( array_key_exists( $view, $bbp->views ) )
			return $view;

		return false;
	}

/**
 * Output the view name aka title
 *
 * @since bbPress (r2789)
 *
 * @param string $view Optional. View id
 * @uses bbp_get_view_title() To get the view title
 */
function bbp_view_title( $view = '' ) {
	echo bbp_get_view_title( $view );
}

	/**
	 * Get the view name aka title
	 *
	 * If a view id is supplied, that is used. Otherwise the bbp_view
	 * query var is checked for.
	 *
	 * @since bbPress (r2789)
	 *
	 * @param string $view Optional. View id
	 * @uses bbp_get_view_id() To get the view id
	 * @return bool|string Title on success, false on failure
	 */
	function bbp_get_view_title( $view = '' ) {
		$bbp = bbpress();

		$view = bbp_get_view_id( $view );
		if ( empty( $view ) )
			return false;

		return $bbp->views[$view]['title'];
	}

/**
 * Output the view url
 *
 * @since bbPress (r2789)
 *
 * @param string $view Optional. View id
 * @uses bbp_get_view_url() To get the view url
 */
function bbp_view_url( $view = false ) {
	echo bbp_get_view_url( $view );
}
	/**
	 * Return the view url
	 *
	 * @since bbPress (r2789)
	 *
	 * @param string $view Optional. View id
	 * @uses sanitize_title() To sanitize the view id
	 * @uses home_url() To get blog home url
	 * @uses add_query_arg() To add custom args to the url
	 * @uses apply_filters() Calls 'bbp_get_view_url' with the view url,
	 *                        used view id
	 * @return string View url (or home url if the view was not found)
	 */
	function bbp_get_view_url( $view = false ) {
		global $wp_rewrite;

		$view = bbp_get_view_id( $view );
		if ( empty( $view ) )
			return home_url();

		// Pretty permalinks
		if ( $wp_rewrite->using_permalinks() ) {
			$url = $wp_rewrite->root . bbp_get_view_slug() . '/' . $view;
			$url = home_url( user_trailingslashit( $url ) );

		// Unpretty permalinks
		} else {
			$url = add_query_arg( array( 'bbp_view' => $view ), home_url( '/' ) );
		}

		return apply_filters( 'bbp_get_view_link', $url, $view );
	}

/** Query *********************************************************************/

/**
 * Check the passed parameter against the current _bbp_query_name
 *
 * @since bbPress (r2980)
 *
 * @uses bbp_get_query_name() Get the query var '_bbp_query_name'
 * @return bool True if match, false if not
 */
function bbp_is_query_name( $name = '' )  {
	return (bool) ( bbp_get_query_name() == $name );
}

/**
 * Get the '_bbp_query_name' setting
 *
 * @since bbPress (r2695)
 *
 * @uses get_query_var() To get the query var '_bbp_query_name'
 * @return string To return the query var value
 */
function bbp_get_query_name()  {
	return get_query_var( '_bbp_query_name' );
}

/**
 * Set the '_bbp_query_name' setting to $name
 *
 * @since bbPress (r2692)
 *
 * @param string $name What to set the query var to
 * @uses set_query_var() To set the query var '_bbp_query_name'
 */
function bbp_set_query_name( $name = '' )  {
	set_query_var( '_bbp_query_name', $name );
}

/**
 * Used to clear the '_bbp_query_name' setting
 *
 * @since bbPress (r2692)
 *
 * @uses bbp_set_query_name() To set the query var '_bbp_query_name' value to ''
 */
function bbp_reset_query_name() {
	bbp_set_query_name();
}

/** Breadcrumbs ***************************************************************/

/**
 * Output the page title as a breadcrumb
 *
 * @since bbPress (r2589)
 *
 * @param string $sep Separator. Defaults to '&larr;'
 * @param bool $current_page Include the current item
 * @param bool $root Include the root page if one exists
 * @uses bbp_get_breadcrumb() To get the breadcrumb
 */
function bbp_title_breadcrumb( $args = array() ) {
	echo bbp_get_breadcrumb( $args );
}

/**
 * Output a breadcrumb
 *
 * @since bbPress (r2589)
 *
 * @param string $sep Separator. Defaults to '&larr;'
 * @param bool $current_page Include the current item
 * @param bool $root Include the root page if one exists
 * @uses bbp_get_breadcrumb() To get the breadcrumb
 */
function bbp_breadcrumb( $args = array() ) {
	echo bbp_get_breadcrumb( $args );
}
	/**
	 * Return a breadcrumb ( forum -> topic -> reply )
	 *
	 * @since bbPress (r2589)
	 *
	 * @param string $sep Separator. Defaults to '&larr;'
	 * @param bool $current_page Include the current item
	 * @param bool $root Include the root page if one exists
	 *
	 * @uses get_post() To get the post
	 * @uses bbp_get_forum_permalink() To get the forum link
	 * @uses bbp_get_topic_permalink() To get the topic link
	 * @uses bbp_get_reply_permalink() To get the reply link
	 * @uses get_permalink() To get the permalink
	 * @uses bbp_get_forum_post_type() To get the forum post type
	 * @uses bbp_get_topic_post_type() To get the topic post type
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 * @uses bbp_get_forum_title() To get the forum title
	 * @uses bbp_get_topic_title() To get the topic title
	 * @uses bbp_get_reply_title() To get the reply title
	 * @uses get_the_title() To get the title
	 * @uses apply_filters() Calls 'bbp_get_breadcrumb' with the crumbs
	 * @return string Breadcrumbs
	 */
	function bbp_get_breadcrumb( $args = array() ) {

		// Turn off breadcrumbs
		if ( apply_filters( 'bbp_no_breadcrumb', is_front_page() ) )
			return;

		// Define variables
		$front_id         = $root_id                                 = 0;
		$ancestors        = $crumbs           = $tag_data            = array();
		$pre_root_text    = $pre_front_text   = $pre_current_text    = '';
		$pre_include_root = $pre_include_home = $pre_include_current = true;

		/** Home Text *********************************************************/

		// No custom home text
		if ( empty( $args['home_text'] ) ) {

			// Set home text to page title
			$front_id = get_option( 'page_on_front' );
			if ( !empty( $front_id ) ) {
				$pre_front_text = get_the_title( $front_id );

			// Default to 'Home'
			} else {
				$pre_front_text = __( 'Home', 'bbpress' );
			}
		}

		/** Root Text *********************************************************/

		// No custom root text
		if ( empty( $args['root_text'] ) ) {
			$page = bbp_get_page_by_path( bbp_get_root_slug() );
			if ( !empty( $page ) ) {
				$root_id = $page->ID;
			}
			$pre_root_text = bbp_get_forum_archive_title();
		}

		/** Includes **********************************************************/

		// Root slug is also the front page
		if ( !empty( $front_id ) && ( $front_id == $root_id ) )
			$pre_include_root = false;

		// Don't show root if viewing forum archive
		if ( bbp_is_forum_archive() )
			$pre_include_root = false;

		// Don't show root if viewing page in place of forum archive
		if ( !empty( $root_id ) && ( ( is_single() || is_page() ) && ( $root_id == get_the_ID() ) ) )
			$pre_include_root = false;

		/** Current Text ******************************************************/

		// Forum archive
		if ( bbp_is_forum_archive() ) {
			$pre_current_text = bbp_get_forum_archive_title();

		// Topic archive
		} elseif ( bbp_is_topic_archive() ) {
			$pre_current_text = bbp_get_topic_archive_title();

		// View
		} elseif ( bbp_is_single_view() ) {
			$pre_current_text = bbp_get_view_title();

		// Single Forum
		} elseif ( bbp_is_single_forum() ) {
			$pre_current_text = bbp_get_forum_title();

		// Single Topic
		} elseif ( bbp_is_single_topic() ) {
			$pre_current_text = bbp_get_topic_title();

		// Single Topic
		} elseif ( bbp_is_single_reply() ) {
			$pre_current_text = bbp_get_reply_title();

		// Topic Tag (or theme compat topic tag)
		} elseif ( bbp_is_topic_tag() || ( get_query_var( 'bbp_topic_tag' ) && !bbp_is_topic_tag_edit() ) ) {

			// Always include the tag name
			$tag_data[] = bbp_get_topic_tag_name();

			// If capable, include a link to edit the tag
			if ( current_user_can( 'manage_topic_tags' ) ) {
				$tag_data[] = '<a href="' . bbp_get_topic_tag_edit_link() . '" class="bbp-edit-topic-tag-link">' . __( '(Edit)', 'bbpress' ) . '</a>';
			}

			// Implode the results of the tag data
			$pre_current_text = sprintf( __( 'Topic Tag: %s', 'bbpress' ), implode( ' ', $tag_data ) );

		// Edit Topic Tag
		} elseif ( bbp_is_topic_tag_edit() ) {
			$pre_current_text = __( 'Edit', 'bbpress' );

		// Single
		} else {
			$pre_current_text = get_the_title();
		}

		/** Parse Args ********************************************************/

		// Parse args
		$defaults = array(

			// HTML
			'before'          => '<div class="bbp-breadcrumb"><p>',
			'after'           => '</p></div>',
			
			// Separator
			'sep'             => __( '&rsaquo;', 'bbpress' ),
			'pad_sep'         => 1,
			'sep_before'      => '<span class="bbp-breadcrumb-sep">',
			'sep_after'       => '</span>',
			
			// Crumbs
			'crumb_before'    => '',
			'crumb_after'     => '',

			// Home
			'include_home'    => $pre_include_home,
			'home_text'       => $pre_front_text,

			// Forum root
			'include_root'    => $pre_include_root,
			'root_text'       => $pre_root_text,

			// Current
			'include_current' => $pre_include_current,
			'current_text'    => $pre_current_text,
			'current_before'  => '<span class="bbp-breadcrumb-current">',
			'current_after'   => '</span>',
		);
		$r = bbp_parse_args( $args, $defaults, 'get_breadcrumb' );
		extract( $r );

		/** Ancestors *********************************************************/

		// Get post ancestors
		if ( is_singular() || bbp_is_forum_edit() || bbp_is_topic_edit() || bbp_is_reply_edit() )
			$ancestors = array_reverse( (array) get_post_ancestors( get_the_ID() ) );

		// Do we want to include a link to home?
		if ( !empty( $include_home ) || empty( $home_text ) )
			$crumbs[] = '<a href="' . trailingslashit( home_url() ) . '" class="bbp-breadcrumb-home">' . $home_text . '</a>';

		// Do we want to include a link to the forum root?
		if ( !empty( $include_root ) || empty( $root_text ) ) {

			// Page exists at root slug path, so use its permalink
			$page = bbp_get_page_by_path( bbp_get_root_slug() );
			if ( !empty( $page ) ) {
				$root_url = get_permalink( $page->ID );

			// Use the root slug
			} else {
				$root_url = get_post_type_archive_link( bbp_get_forum_post_type() );
			}

			// Add the breadcrumb
			$crumbs[] = '<a href="' . $root_url . '" class="bbp-breadcrumb-root">' . $root_text . '</a>';
		}

		// Ancestors exist
		if ( !empty( $ancestors ) ) {

			// Loop through parents
			foreach( (array) $ancestors as $parent_id ) {

				// Parents
				$parent = get_post( $parent_id );

				// Switch through post_type to ensure correct filters are applied
				switch ( $parent->post_type ) {

					// Forum
					case bbp_get_forum_post_type() :
						$crumbs[] = '<a href="' . bbp_get_forum_permalink( $parent->ID ) . '" class="bbp-breadcrumb-forum">' . bbp_get_forum_title( $parent->ID ) . '</a>';
						break;

					// Topic
					case bbp_get_topic_post_type() :
						$crumbs[] = '<a href="' . bbp_get_topic_permalink( $parent->ID ) . '" class="bbp-breadcrumb-topic">' . bbp_get_topic_title( $parent->ID ) . '</a>';
						break;

					// Reply (Note: not in most themes)
					case bbp_get_reply_post_type() :
						$crumbs[] = '<a href="' . bbp_get_reply_permalink( $parent->ID ) . '" class="bbp-breadcrumb-reply">' . bbp_get_reply_title( $parent->ID ) . '</a>';
						break;

					// WordPress Post/Page/Other
					default :
						$crumbs[] = '<a href="' . get_permalink( $parent->ID ) . '" class="bbp-breadcrumb-item">' . get_the_title( $parent->ID ) . '</a>';
						break;
				}
			}

		// Edit topic tag
		} elseif ( bbp_is_topic_tag_edit() ) {
			$crumbs[] = '<a href="' . get_term_link( bbp_get_topic_tag_id(), bbp_get_topic_tag_tax_id() ) . '" class="bbp-breadcrumb-topic-tag">' . sprintf( __( 'Topic Tag: %s', 'bbpress' ), bbp_get_topic_tag_name() ) . '</a>';
		}

		/** Current ***********************************************************/

		// Add current page to breadcrumb
		if ( !empty( $include_current ) || empty( $pre_current_text ) )
			$crumbs[] = $current_before . $current_text . $current_after;

		/** Separator *********************************************************/

		// Wrap the separator in before/after before padding and filter
		if ( ! empty( $sep ) )
			$sep = $sep_before . $sep . $sep_after;

		// Pad the separator
		if ( !empty( $pad_sep ) )
			$sep = str_pad( $sep, strlen( $sep ) + ( (int) $pad_sep * 2 ), ' ', STR_PAD_BOTH );

		/** Finish Up *********************************************************/

		// Filter the separator and breadcrumb
		$sep    = apply_filters( 'bbp_breadcrumb_separator', $sep    );
		$crumbs = apply_filters( 'bbp_breadcrumbs',          $crumbs );

		// Build the trail
		$trail = !empty( $crumbs ) ? ( $before . $crumb_before . implode( $sep . $crumb_after . $crumb_before , $crumbs ) . $crumb_after . $after ) : '';

		return apply_filters( 'bbp_get_breadcrumb', $trail, $crumbs, $r );
	}

/** Topic Tags ***************************************************************/

/**
 * Output all of the allowed tags in HTML format with attributes.
 *
 * This is useful for displaying in the post area, which elements and
 * attributes are supported. As well as any plugins which want to display it.
 *
 * @since bbPress (r2780)
 *
 * @uses bbp_get_allowed_tags()
 */
function bbp_allowed_tags() {
	echo bbp_get_allowed_tags();
}
	/**
	 * Display all of the allowed tags in HTML format with attributes.
	 *
	 * This is useful for displaying in the post area, which elements and
	 * attributes are supported. As well as any plugins which want to display it.
	 *
	 * @since bbPress (r2780)
	 *
	 * @uses allowed_tags() To get the allowed tags
	 * @uses apply_filters() Calls 'bbp_allowed_tags' with the tags
	 * @return string HTML allowed tags entity encoded.
	 */
	function bbp_get_allowed_tags() {
		return apply_filters( 'bbp_get_allowed_tags', allowed_tags() );
	}

/** Errors & Messages *********************************************************/

/**
 * Display possible errors & messages inside a template file
 *
 * @since bbPress (r2688)
 *
 * @uses WP_Error bbPress::errors::get_error_codes() To get the error codes
 * @uses WP_Error bbPress::errors::get_error_data() To get the error data
 * @uses WP_Error bbPress::errors::get_error_messages() To get the error
 *                                                       messages
 * @uses is_wp_error() To check if it's a {@link WP_Error}
 */
function bbp_template_notices() {

	// Bail if no notices or errors
	if ( !bbp_has_errors() )
		return;

	// Define local variable(s)
	$errors = $messages = array();

	// Get bbPress
	$bbp = bbpress();

	// Loop through notices
	foreach ( $bbp->errors->get_error_codes() as $code ) {

		// Get notice severity
		$severity = $bbp->errors->get_error_data( $code );

		// Loop through notices and separate errors from messages
		foreach ( $bbp->errors->get_error_messages( $code ) as $error ) {
			if ( 'message' == $severity ) {
				$messages[] = $error;
			} else {
				$errors[]   = $error;
			}
		}
	}

	// Display errors first...
	if ( !empty( $errors ) ) : ?>

		<div class="bbp-template-notice error">
			<p>
				<?php echo implode( "</p>\n<p>", $errors ); ?>
			</p>
		</div>

	<?php endif;

	// ...and messages last
	if ( !empty( $messages ) ) : ?>

		<div class="bbp-template-notice">
			<p>
				<?php echo implode( "</p>\n<p>", $messages ); ?>
			</p>
		</div>

	<?php endif;
}

/** Login/logout/register/lost pass *******************************************/

/**
 * Output the logout link
 *
 * @since bbPress (r2827)
 *
 * @param string $redirect_to Redirect to url
 * @uses bbp_get_logout_link() To get the logout link
 */
function bbp_logout_link( $redirect_to = '' ) {
	echo bbp_get_logout_link( $redirect_to );
}
	/**
	 * Return the logout link
	 *
	 * @since bbPress (r2827)
	 *
	 * @param string $redirect_to Redirect to url
	 * @uses wp_logout_url() To get the logout url
	 * @uses apply_filters() Calls 'bbp_get_logout_link' with the logout link and
	 *                        redirect to url
	 * @return string The logout link
	 */
	function bbp_get_logout_link( $redirect_to = '' ) {
		return apply_filters( 'bbp_get_logout_link', '<a href="' . wp_logout_url( $redirect_to ) . '" class="button logout-link">' . __( 'Log Out', 'bbpress' ) . '</a>', $redirect_to );
	}

/** Title *********************************************************************/

/**
 * Custom page title for bbPress pages
 *
 * @since bbPress (r2788)
 *
 * @param string $title Optional. The title (not used).
 * @param string $sep Optional, default is '&raquo;'. How to separate the
 *                     various items within the page title.
 * @param string $seplocation Optional. Direction to display title, 'right'.
 * @uses bbp_is_single_user() To check if it's a user profile page
 * @uses bbp_is_single_user_edit() To check if it's a user profile edit page
 * @uses bbp_is_user_home() To check if the profile page is of the current user
 * @uses get_query_var() To get the user id
 * @uses get_userdata() To get the user data
 * @uses bbp_is_single_forum() To check if it's a forum
 * @uses bbp_get_forum_title() To get the forum title
 * @uses bbp_is_single_topic() To check if it's a topic
 * @uses bbp_get_topic_title() To get the topic title
 * @uses bbp_is_single_reply() To check if it's a reply
 * @uses bbp_get_reply_title() To get the reply title
 * @uses is_tax() To check if it's the tag page
 * @uses get_queried_object() To get the queried object
 * @uses bbp_is_single_view() To check if it's a view
 * @uses bbp_get_view_title() To get the view title
 * @uses apply_filters() Calls 'bbp_raw_title' with the title
 * @uses apply_filters() Calls 'bbp_profile_page_wp_title' with the title,
 *                        separator and separator location
 * @return string The tite
 */
function bbp_title( $title = '', $sep = '&raquo;', $seplocation = '' ) {

	// Store original title to compare
	$_title = $title;

	/** Archives **************************************************************/

	// Forum Archive
	if ( bbp_is_forum_archive() ) {
		$title = bbp_get_forum_archive_title();

	// Topic Archive
	} elseif ( bbp_is_topic_archive() ) {
		$title = bbp_get_topic_archive_title();

	/** Singles ***************************************************************/

	// Forum page
	} elseif ( bbp_is_single_forum() ) {
		$title = sprintf( __( 'Forum: %s', 'bbpress' ), bbp_get_forum_title() );

	// Topic page
	} elseif ( bbp_is_single_topic() ) {
		$title = sprintf( __( 'Topic: %s', 'bbpress' ), bbp_get_topic_title() );

	// Replies
	} elseif ( bbp_is_single_reply() ) {
		$title = bbp_get_reply_title();

	// Topic tag page (or edit)
	} elseif ( bbp_is_topic_tag() || bbp_is_topic_tag_edit() || get_query_var( 'bbp_topic_tag' ) ) {
		$term  = get_queried_object();
		$title = sprintf( __( 'Topic Tag: %s', 'bbpress' ), $term->name );

	/** Users *****************************************************************/

	// Profile page
	} elseif ( bbp_is_single_user() ) {

		// Current users profile
		if ( bbp_is_user_home() ) {
			$title = __( 'Your Profile', 'bbpress' );

		// Other users profile
		} else {
			$userdata = get_userdata( bbp_get_user_id() );
			$title    = sprintf( __( '%s\'s Profile', 'bbpress' ), $userdata->display_name );
		}

	// Profile edit page
	} elseif ( bbp_is_single_user_edit() ) {

		// Current users profile
		if ( bbp_is_user_home_edit() ) {
			$title = __( 'Edit Your Profile', 'bbpress' );

		// Other users profile
		} else {
			$userdata = get_userdata( bbp_get_user_id() );
			$title    = sprintf( __( 'Edit %s\'s Profile', 'bbpress' ), $userdata->display_name );
		}

	/** Views *****************************************************************/

	// Views
	} elseif ( bbp_is_single_view() ) {
		$title = sprintf( __( 'View: %s', 'bbpress' ), bbp_get_view_title() );
	}

	// Filter the raw title
	$title = apply_filters( 'bbp_raw_title', $title, $sep, $seplocation );

	// Compare new title with original title
	if ( $title == $_title )
		return $title;

	// Temporary separator, for accurate flipping, if necessary
	$t_sep  = '%WP_TITILE_SEP%';
	$prefix = '';

	if ( !empty( $title ) )
		$prefix = " $sep ";

	// sep on right, so reverse the order
	if ( 'right' == $seplocation ) {
		$title_array = array_reverse( explode( $t_sep, $title ) );
		$title       = implode( " $sep ", $title_array ) . $prefix;

	// sep on left, do not reverse
	} else {
		$title_array = explode( $t_sep, $title );
		$title       = $prefix . implode( " $sep ", $title_array );
	}

	// Filter and return
	return apply_filters( 'bbp_title', $title, $sep, $seplocation );
}
